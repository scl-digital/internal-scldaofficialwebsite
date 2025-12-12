<?php
/**
 * Activity Log API
 * Retrieves activity logs for projects, milestones, and tasks
 * Includes filtering, pagination, and export capabilities
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

$response = [
    'success' => false,
    'message' => '',
    'data' => null,
    'pagination' => null
];

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        $project_id = $_GET['project_id'] ?? null;
        $milestone_id = $_GET['milestone_id'] ?? null;
        $task_id = $_GET['task_id'] ?? null;
        $entity_type = $_GET['entity_type'] ?? null;
        $action = $_GET['action'] ?? null;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(100, max(1, (int)($_GET['limit'] ?? 50)));
        $offset = ($page - 1) * $limit;
        $sort = $_GET['sort'] ?? 'desc'; // 'asc' or 'desc'
        
        // Build where clause
        $where = [];
        $params = [];
        
        if ($project_id) {
            $where[] = "al.project_id = ?";
            $params[] = (int)$project_id;
        }
        
        if ($milestone_id) {
            $where[] = "al.milestone_id = ?";
            $params[] = (int)$milestone_id;
        }
        
        if ($task_id) {
            $where[] = "al.task_id = ?";
            $params[] = (int)$task_id;
        }
        
        if ($entity_type) {
            $where[] = "al.entity_type = ?";
            $params[] = $entity_type;
        }
        
        if ($action) {
            $where[] = "al.action = ?";
            $params[] = $action;
        }
        
        $where_clause = $where ? " WHERE " . implode(" AND ", $where) : "";
        $sort_order = strtoupper($sort) === 'ASC' ? 'ASC' : 'DESC';
        
        // Get total count
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM activity_log al" . $where_clause);
        $stmt->execute($params);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get paginated results
        $params_with_pagination = $params;
        $params_with_pagination[] = $limit;
        $params_with_pagination[] = $offset;
        
        $stmt = $pdo->prepare("
            SELECT 
                al.*,
                p.title as project_title,
                m.title as milestone_title,
                t.title as task_title
            FROM activity_log al
            LEFT JOIN projects p ON al.project_id = p.id
            LEFT JOIN milestones m ON al.milestone_id = m.id
            LEFT JOIN tasks t ON al.task_id = t.id
            " . $where_clause . "
            ORDER BY al.created_at " . $sort_order . "
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute($params_with_pagination);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Parse JSON fields
        foreach ($logs as &$log) {
            if ($log['old_value']) {
                $log['old_value'] = json_decode($log['old_value'], true);
            }
            if ($log['new_value']) {
                $log['new_value'] = json_decode($log['new_value'], true);
            }
        }
        
        $response['success'] = true;
        $response['data'] = $logs;
        $response['pagination'] = [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => ceil($total / $limit)
        ];
        
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
    
    // POST: Manual log entry (for system events)
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['entity_type']) || !isset($data['action'])) {
            throw new Exception("entity_type and action are required");
        }
        
        $project_id = $data['project_id'] ?? null;
        $milestone_id = $data['milestone_id'] ?? null;
        $task_id = $data['task_id'] ?? null;
        $entity_type = $data['entity_type'];
        $action = $data['action'];
        $description = $data['description'] ?? '';
        $old_value = $data['old_value'] ?? null;
        $new_value = $data['new_value'] ?? null;
        $user_type = $data['user_type'] ?? 'admin';
        $user_id = $data['user_id'] ?? ($_SESSION['admin_id'] ?? 0);
        $user_name = $data['user_name'] ?? ($_SESSION['admin_name'] ?? 'System');
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        $stmt = $pdo->prepare("
            INSERT INTO activity_log 
            (project_id, milestone_id, task_id, entity_type, action, description, old_value, new_value, user_type, user_id, user_name, ip_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $project_id,
            $milestone_id,
            $task_id,
            $entity_type,
            $action,
            $description,
            $old_value ? json_encode($old_value) : null,
            $new_value ? json_encode($new_value) : null,
            $user_type,
            $user_id,
            $user_name,
            $ip_address
        ]);
        
        $response['success'] = true;
        $response['message'] = 'Activity logged successfully';
        $response['id'] = $pdo->lastInsertId();
        
        http_response_code(201);
        echo json_encode($response);
        exit;
    }
    
    // DELETE: Clear old logs (optional cleanup)
    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Only allow deletion of logs older than 90 days by default
        $days = max(1, (int)($data['days_old'] ?? 90));
        $project_id = $data['project_id'] ?? null;
        
        $where = ["al.created_at < DATE_SUB(NOW(), INTERVAL ? DAY)"];
        $params = [$days];
        
        if ($project_id) {
            $where[] = "al.project_id = ?";
            $params[] = (int)$project_id;
        }
        
        $where_clause = " WHERE " . implode(" AND ", $where);
        
        $stmt = $pdo->prepare("DELETE FROM activity_log al" . $where_clause);
        $stmt->execute($params);
        
        $response['success'] = true;
        $response['message'] = "Old activity logs deleted";
        $response['rows_deleted'] = $stmt->rowCount();
        
        http_response_code(200);
        echo json_encode($response);
        exit;
    }
    
    http_response_code(405);
    throw new Exception("Method not allowed");

} catch (Exception $e) {
    http_response_code(400);
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    echo json_encode($response);
}
?>
