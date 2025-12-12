<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'GET') {
        // Get tasks for a milestone or project
        $milestone_id = $_GET['milestone_id'] ?? null;
        $project_id = $_GET['project_id'] ?? null;
        
        if (!$milestone_id && !$project_id) {
            echo json_encode(['success' => false, 'error' => 'Milestone ID or Project ID required.']);
            exit;
        }
        
        if ($milestone_id) {
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE milestone_id = ? ORDER BY due_date ASC");
            $stmt->execute([$milestone_id]);
        } else {
            $stmt = $pdo->prepare("SELECT t.* FROM tasks t JOIN milestones m ON t.milestone_id = m.id WHERE m.project_id = ? ORDER BY t.due_date ASC");
            $stmt->execute([$project_id]);
        }
        
        $tasks = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $tasks]);
        exit;
    }
    
    if ($method === 'POST') {
        // Create new task
        $required = ['milestone_id', 'title'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                echo json_encode(['success' => false, 'error' => "$field is required."]);
                exit;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO tasks (milestone_id, title, description, due_date, status, priority) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['milestone_id'],
            trim($data['title']),
            $data['description'] ?? null,
            $data['due_date'] ?? null,
            $data['status'] ?? 'pending',
            $data['priority'] ?? 'medium'
        ]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    if ($method === 'PUT') {
        // Update task
        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'error' => 'Task ID required.']);
            exit;
        }
        
        $fields = [];
        $params = [];
        foreach (['title', 'description', 'due_date', 'status', 'priority'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (!$fields) {
            echo json_encode(['success' => false, 'error' => 'No fields to update.']);
            exit;
        }
        
        $params[] = $data['id'];
        $sql = "UPDATE tasks SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
