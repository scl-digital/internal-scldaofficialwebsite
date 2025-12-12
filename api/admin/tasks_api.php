<?php
/**
 * Tasks Management API - CRUD Operations
 * Handles all task-related database operations including milestones
 * Includes input validation, error handling, and activity logging
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../db.php';

// Helper function to validate input
function validateInput($data, $required = []) {
    foreach ($required as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            throw new Exception("$field is required");
        }
    }
    return true;
}

// Helper function to log activity
function logActivity($pdo, $project_id, $entity_type, $action, $description, $old_value = null, $new_value = null, $milestone_id = null, $task_id = null) {
    try {
        $user_id = $_SESSION['admin_id'] ?? 0;
        $user_name = $_SESSION['admin_name'] ?? 'System';
        $user_type = 'admin';
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
    } catch (Exception $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}

// Helper to calculate project progress based on tasks
function updateProjectProgress($pdo, $project_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT AVG(progress) as avg_progress 
            FROM tasks 
            WHERE project_id = ?
        ");
        $stmt->execute([$project_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $avg_progress = (int)($result['avg_progress'] ?? 0);
        
        $stmt = $pdo->prepare("UPDATE projects SET progress = ? WHERE id = ?");
        $stmt->execute([$avg_progress, $project_id]);
    } catch (Exception $e) {
        error_log("Progress update error: " . $e->getMessage());
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$response = [
    'success' => false,
    'message' => '',
    'data' => null,
    'id' => null
];

try {
    $action = $_GET['action'] ?? null;
    
    // MILESTONES
    if ($action === 'milestones') {
        // GET milestones
        if ($method === 'GET') {
            $project_id = $_GET['project_id'] ?? null;
            $milestone_id = $_GET['id'] ?? null;
            
            if (!$project_id) {
                throw new Exception("Project ID is required");
            }
            
            if ($milestone_id) {
                // Get single milestone
                $stmt = $pdo->prepare("
                    SELECT m.*, 
                           COUNT(t.id) as task_count,
                           SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks
                    FROM milestones m
                    LEFT JOIN tasks t ON m.id = t.milestone_id
                    WHERE m.id = ? AND m.project_id = ?
                    GROUP BY m.id
                ");
                $stmt->execute([$milestone_id, $project_id]);
            } else {
                // Get all milestones for project
                $stmt = $pdo->prepare("
                    SELECT m.*, 
                           COUNT(t.id) as task_count,
                           SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed_tasks
                    FROM milestones m
                    LEFT JOIN tasks t ON m.id = t.milestone_id
                    WHERE m.project_id = ?
                    GROUP BY m.id
                    ORDER BY m.order_index ASC, m.due_date ASC
                ");
                $stmt->execute([$project_id]);
            }
            
            $data = $milestone_id ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($milestone_id && !$data) {
                throw new Exception("Milestone not found");
            }
            
            $response['success'] = true;
            $response['data'] = $data;
            http_response_code(200);
            echo json_encode($response);
            exit;
        }
        
        // POST: Create milestone
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            validateInput($data, ['project_id', 'title']);
            
            $project_id = (int)$data['project_id'];
            $title = trim($data['title']);
            $description = trim($data['description'] ?? '');
            $due_date = $data['due_date'] ?? null;
            $status = $data['status'] ?? 'pending';
            $progress = max(0, min(100, (int)($data['progress'] ?? 0)));
            
            // Verify project exists
            $stmt = $pdo->prepare("SELECT id FROM projects WHERE id = ?");
            $stmt->execute([$project_id]);
            if (!$stmt->fetch()) {
                throw new Exception("Project not found");
            }
            
            // Get next order index
            $stmt = $pdo->prepare("SELECT COALESCE(MAX(order_index), -1) + 1 as next_order FROM milestones WHERE project_id = ?");
            $stmt->execute([$project_id]);
            $order_index = $stmt->fetch(PDO::FETCH_ASSOC)['next_order'];
            
            $stmt = $pdo->prepare("
                INSERT INTO milestones 
                (project_id, title, description, status, progress, due_date, order_index)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $project_id,
                $title,
                $description,
                $status,
                $progress,
                $due_date,
                $order_index
            ]);
            
            $milestone_id = $pdo->lastInsertId();
            logActivity($pdo, $project_id, 'milestone', 'created', "Milestone '$title' created", null, $data, $milestone_id);
            
            $response['success'] = true;
            $response['message'] = 'Milestone created successfully';
            $response['id'] = $milestone_id;
            
            http_response_code(201);
            echo json_encode($response);
            exit;
        }
        
        // PUT: Update milestone
        if ($method === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                throw new Exception("Milestone ID is required");
            }
            
            $milestone_id = (int)$data['id'];
            
            // Get current milestone
            $stmt = $pdo->prepare("SELECT * FROM milestones WHERE id = ?");
            $stmt->execute([$milestone_id]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$current) {
                throw new Exception("Milestone not found");
            }
            
            $updateFields = [];
            $params = [];
            $updates = [];
            
            $allowedFields = ['title', 'description', 'status', 'progress', 'due_date', 'completed_date'];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $value = trim($data[$field]);
                    
                    if ($field === 'progress') {
                        $value = max(0, min(100, (int)$value));
                    }
                    
                    if ($current[$field] != $value) {
                        $updates[] = ['field' => $field, 'old' => $current[$field], 'new' => $value];
                    }
                    
                    $updateFields[] = "$field = ?";
                    $params[] = $value;
                }
            }
            
            if (!$updateFields) {
                throw new Exception("No fields to update");
            }
            
            $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
            $params[] = $milestone_id;
            
            $sql = "UPDATE milestones SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            foreach ($updates as $update) {
                logActivity($pdo, $current['project_id'], 'milestone', 'updated', 
                    "Field '{$update['field']}' updated", 
                    [$update['field'] => $update['old']], 
                    [$update['field'] => $update['new']], 
                    $milestone_id);
            }
            
            $response['success'] = true;
            $response['message'] = 'Milestone updated successfully';
            
            http_response_code(200);
            echo json_encode($response);
            exit;
        }
        
        // DELETE: Delete milestone
        if ($method === 'DELETE') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                throw new Exception("Milestone ID is required");
            }
            
            $milestone_id = (int)$data['id'];
            
            $stmt = $pdo->prepare("SELECT * FROM milestones WHERE id = ?");
            $stmt->execute([$milestone_id]);
            $milestone = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$milestone) {
                throw new Exception("Milestone not found");
            }
            
            logActivity($pdo, $milestone['project_id'], 'milestone', 'deleted', 
                "Milestone '{$milestone['title']}' deleted", null, null, $milestone_id);
            
            $stmt = $pdo->prepare("DELETE FROM milestones WHERE id = ?");
            $stmt->execute([$milestone_id]);
            
            $response['success'] = true;
            $response['message'] = 'Milestone deleted successfully';
            
            http_response_code(200);
            echo json_encode($response);
            exit;
        }
    }
    
    // TASKS
    else if ($action === 'tasks') {
        // GET tasks
        if ($method === 'GET') {
            $project_id = $_GET['project_id'] ?? null;
            $milestone_id = $_GET['milestone_id'] ?? null;
            $task_id = $_GET['id'] ?? null;
            
            if ($task_id) {
                // Get single task
                $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
                $stmt->execute([$task_id]);
            } else {
                // Get tasks with filters
                $where = [];
                $params = [];
                
                if ($project_id) {
                    $where[] = "t.project_id = ?";
                    $params[] = (int)$project_id;
                }
                
                if ($milestone_id) {
                    $where[] = "t.milestone_id = ?";
                    $params[] = (int)$milestone_id;
                }
                
                $where_clause = $where ? " WHERE " . implode(" AND ", $where) : "";
                
                $stmt = $pdo->prepare("
                    SELECT t.* 
                    FROM tasks t
                    " . $where_clause . "
                    ORDER BY t.order_index ASC, t.due_date ASC
                ");
                $stmt->execute($params);
            }
            
            $data = $task_id ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($task_id && !$data) {
                throw new Exception("Task not found");
            }
            
            $response['success'] = true;
            $response['data'] = $data;
            http_response_code(200);
            echo json_encode($response);
            exit;
        }
        
        // POST: Create task
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            validateInput($data, ['milestone_id', 'project_id', 'title']);
            
            $milestone_id = (int)$data['milestone_id'];
            $project_id = (int)$data['project_id'];
            $title = trim($data['title']);
            $description = trim($data['description'] ?? '');
            $status = $data['status'] ?? 'pending';
            $priority = $data['priority'] ?? 'medium';
            $progress = max(0, min(100, (int)($data['progress'] ?? 0)));
            $due_date = $data['due_date'] ?? null;
            $assigned_to = $data['assigned_to'] ?? null;
            $created_by = $_SESSION['admin_id'] ?? 0;
            
            // Verify milestone exists
            $stmt = $pdo->prepare("SELECT id FROM milestones WHERE id = ? AND project_id = ?");
            $stmt->execute([$milestone_id, $project_id]);
            if (!$stmt->fetch()) {
                throw new Exception("Milestone not found");
            }
            
            // Get next order index
            $stmt = $pdo->prepare("SELECT COALESCE(MAX(order_index), -1) + 1 as next_order FROM tasks WHERE milestone_id = ?");
            $stmt->execute([$milestone_id]);
            $order_index = $stmt->fetch(PDO::FETCH_ASSOC)['next_order'];
            
            $stmt = $pdo->prepare("
                INSERT INTO tasks 
                (milestone_id, project_id, title, description, status, priority, progress, due_date, assigned_to, created_by, order_index)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $milestone_id,
                $project_id,
                $title,
                $description,
                $status,
                $priority,
                $progress,
                $due_date,
                $assigned_to,
                $created_by,
                $order_index
            ]);
            
            $task_id = $pdo->lastInsertId();
            logActivity($pdo, $project_id, 'task', 'created', "Task '$title' created", null, $data, $milestone_id, $task_id);
            updateProjectProgress($pdo, $project_id);
            
            $response['success'] = true;
            $response['message'] = 'Task created successfully';
            $response['id'] = $task_id;
            
            http_response_code(201);
            echo json_encode($response);
            exit;
        }
        
        // PUT: Update task
        if ($method === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                throw new Exception("Task ID is required");
            }
            
            $task_id = (int)$data['id'];
            
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$task_id]);
            $current = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$current) {
                throw new Exception("Task not found");
            }
            
            $updateFields = [];
            $params = [];
            $updates = [];
            
            $allowedFields = ['title', 'description', 'status', 'priority', 'progress', 'due_date', 'assigned_to', 'completed_date'];
            
            foreach ($allowedFields as $field) {
                if (array_key_exists($field, $data)) {
                    $value = is_string($data[$field]) ? trim($data[$field]) : $data[$field];
                    
                    if ($field === 'progress') {
                        $value = max(0, min(100, (int)$value));
                    }
                    
                    if ($current[$field] != $value) {
                        $updates[] = ['field' => $field, 'old' => $current[$field], 'new' => $value];
                    }
                    
                    $updateFields[] = "$field = ?";
                    $params[] = $value;
                }
            }
            
            if (!$updateFields) {
                throw new Exception("No fields to update");
            }
            
            $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
            $params[] = $task_id;
            
            $sql = "UPDATE tasks SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            foreach ($updates as $update) {
                logActivity($pdo, $current['project_id'], 'task', 'updated', 
                    "Field '{$update['field']}' updated", 
                    [$update['field'] => $update['old']], 
                    [$update['field'] => $update['new']], 
                    $current['milestone_id'], 
                    $task_id);
            }
            
            updateProjectProgress($pdo, $current['project_id']);
            
            $response['success'] = true;
            $response['message'] = 'Task updated successfully';
            
            http_response_code(200);
            echo json_encode($response);
            exit;
        }
        
        // DELETE: Delete task
        if ($method === 'DELETE') {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id'])) {
                throw new Exception("Task ID is required");
            }
            
            $task_id = (int)$data['id'];
            
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$task_id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$task) {
                throw new Exception("Task not found");
            }
            
            logActivity($pdo, $task['project_id'], 'task', 'deleted', 
                "Task '{$task['title']}' deleted", null, null, 
                $task['milestone_id'], $task_id);
            
            $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
            $stmt->execute([$task_id]);
            
            updateProjectProgress($pdo, $task['project_id']);
            
            $response['success'] = true;
            $response['message'] = 'Task deleted successfully';
            
            http_response_code(200);
            echo json_encode($response);
            exit;
        }
    }
    
    throw new Exception("Invalid action");

} catch (Exception $e) {
    http_response_code(400);
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    echo json_encode($response);
}
?>
