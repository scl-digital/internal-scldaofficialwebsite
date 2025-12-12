<?php
/**
 * Project Management API - CRUD Operations
 * Handles all project-related database operations
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

$method = $_SERVER['REQUEST_METHOD'];
$response = [
    'success' => false,
    'message' => '',
    'data' => null,
    'id' => null
];

try {
    // GET: List all projects or get specific project
    if ($method === 'GET') {
        $project_id = $_GET['id'] ?? null;
        
        if ($project_id) {
            // Get single project with details
            $stmt = $pdo->prepare("
                SELECT 
                    p.*,
                    c.name as client_name,
                    c.email as client_email,
                    COUNT(DISTINCT m.id) as milestone_count,
                    COUNT(DISTINCT t.id) as task_count
                FROM projects p
                LEFT JOIN clients c ON p.client_id = c.id
                LEFT JOIN milestones m ON p.id = m.project_id
                LEFT JOIN tasks t ON p.id = t.project_id
                WHERE p.id = ?
                GROUP BY p.id
            ");
            $stmt->execute([$project_id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$project) {
                throw new Exception("Project not found");
            }
            
            // Parse JSON fields
            if (isset($project['attachments']) && $project['attachments']) {
                $project['attachments'] = json_decode($project['attachments'], true);
            } else {
                $project['attachments'] = [];
            }
            
            $response['success'] = true;
            $response['data'] = $project;
        } else {
            // List all projects with pagination
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = min(100, max(1, (int)($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;
            
            $filter_status = $_GET['status'] ?? null;
            $filter_client = $_GET['client_id'] ?? null;
            
            $where = [];
            $params = [];
            
            if ($filter_status) {
                $where[] = "p.status = ?";
                $params[] = $filter_status;
            }
            
            if ($filter_client) {
                $where[] = "p.client_id = ?";
                $params[] = (int)$filter_client;
            }
            
            $where_clause = $where ? " WHERE " . implode(" AND ", $where) : "";
            
            // Get total count
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM projects p" . $where_clause);
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get paginated results
            $stmt = $pdo->prepare("
                SELECT 
                    p.*,
                    c.name as client_name,
                    c.email as client_email,
                    COUNT(DISTINCT m.id) as milestone_count,
                    COUNT(DISTINCT t.id) as task_count
                FROM projects p
                LEFT JOIN clients c ON p.client_id = c.id
                LEFT JOIN milestones m ON p.id = m.project_id
                LEFT JOIN tasks t ON p.id = t.project_id
                " . $where_clause . "
                GROUP BY p.id
                ORDER BY p.deadline ASC, p.created_at DESC
                LIMIT ? OFFSET ?
            ");
            
            $params[] = $limit;
            $params[] = $offset;
            $stmt->execute($params);
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Parse JSON fields
            foreach ($projects as &$project) {
                if (isset($project['attachments']) && $project['attachments']) {
                    $project['attachments'] = json_decode($project['attachments'], true);
                } else {
                    $project['attachments'] = [];
                }
            }
            
            $response['success'] = true;
            $response['data'] = $projects;
            $response['pagination'] = [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ];
        }
        
        http_response_code(200);
        echo json_encode($response);
        exit;
    }

    // POST: Create new project
    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        validateInput($data, ['client_id', 'title']);
        
        // Sanitize and prepare data
        $client_id = (int)$data['client_id'];
        $title = trim($data['title']);
        $description = trim($data['description'] ?? '');
        $status = $data['status'] ?? 'pending';
        $progress = max(0, min(100, (int)($data['progress'] ?? 0)));
        $amount = (float)($data['amount'] ?? 0);
        $start_date = $data['start_date'] ?? null;
        $deadline = $data['deadline'] ?? null;
        $notes = trim($data['notes'] ?? '');
        $attachments = isset($data['attachments']) ? json_encode($data['attachments']) : json_encode([]);
        $created_by = $_SESSION['admin_id'] ?? 0;
        
        // Validate client exists
        $stmt = $pdo->prepare("SELECT id FROM clients WHERE id = ?");
        $stmt->execute([$client_id]);
        if (!$stmt->fetch()) {
            throw new Exception("Client not found");
        }
        
        // Validate dates
        if ($start_date && $deadline && strtotime($deadline) < strtotime($start_date)) {
            throw new Exception("Deadline cannot be before start date");
        }
        
        // Insert project
        $stmt = $pdo->prepare("
            INSERT INTO projects 
            (client_id, title, description, status, progress, amount, start_date, deadline, notes, attachments, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $client_id,
            $title,
            $description,
            $status,
            $progress,
            $amount,
            $start_date,
            $deadline,
            $notes,
            $attachments,
            $created_by
        ]);
        
        $project_id = $pdo->lastInsertId();
        
        // Log activity
        logActivity($pdo, $project_id, 'project', 'created', "Project '$title' created", null, $data);
        
        $response['success'] = true;
        $response['message'] = 'Project created successfully';
        $response['id'] = $project_id;
        
        http_response_code(201);
        echo json_encode($response);
        exit;
    }

    // PUT: Update project
    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !$data['id']) {
            throw new Exception("Project ID is required");
        }
        
        $project_id = (int)$data['id'];
        
        // Get current project
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            throw new Exception("Project not found");
        }
        
        // Prepare update fields
        $updateFields = [];
        $params = [];
        $updates = [];
        
        $allowedFields = ['client_id', 'title', 'description', 'status', 'progress', 'amount', 'start_date', 'deadline', 'completed_date', 'notes'];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $value = $data[$field];
                
                // Type conversion
                if ($field === 'client_id' || $field === 'progress') {
                    $value = (int)$value;
                } else if ($field === 'amount') {
                    $value = (float)$value;
                } else if ($field !== 'client_id') {
                    $value = trim($value);
                }
                
                // Validation
                if ($field === 'client_id' && $value > 0) {
                    $stmt = $pdo->prepare("SELECT id FROM clients WHERE id = ?");
                    $stmt->execute([$value]);
                    if (!$stmt->fetch()) {
                        throw new Exception("Client not found");
                    }
                } else if ($field === 'progress') {
                    if ($value < 0 || $value > 100) {
                        throw new Exception("Progress must be between 0 and 100");
                    }
                }
                
                // Track changes
                if ($current[$field] != $value) {
                    $updates[] = [
                        'field' => $field,
                        'old' => $current[$field],
                        'new' => $value
                    ];
                }
                
                $updateFields[] = "$field = ?";
                $params[] = $value;
            }
        }
        
        if (!$updateFields) {
            throw new Exception("No fields to update");
        }
        
        // Add updated_at
        $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $project_id;
        
        // Execute update
        $sql = "UPDATE projects SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Log activity for each change
        foreach ($updates as $update) {
            logActivity($pdo, $project_id, 'project', 'updated', 
                "Field '{$update['field']}' updated", 
                [$update['field'] => $update['old']], 
                [$update['field'] => $update['new']]);
        }
        
        $response['success'] = true;
        $response['message'] = 'Project updated successfully';
        
        http_response_code(200);
        echo json_encode($response);
        exit;
    }

    // DELETE: Delete project
    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !$data['id']) {
            throw new Exception("Project ID is required");
        }
        
        $project_id = (int)$data['id'];
        
        // Get project details before deletion
        $stmt = $pdo->prepare("SELECT title FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$project) {
            throw new Exception("Project not found");
        }
        
        // Log deletion
        logActivity($pdo, $project_id, 'project', 'deleted', 
            "Project '{$project['title']}' deleted");
        
        // Delete project (cascade will handle related records)
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        
        $response['success'] = true;
        $response['message'] = 'Project deleted successfully';
        
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
