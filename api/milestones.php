<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

try {
    if ($method === 'GET') {
        // Get milestones for a project
        $project_id = $_GET['project_id'] ?? null;
        if (!$project_id) {
            echo json_encode(['success' => false, 'error' => 'Project ID required.']);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT * FROM milestones WHERE project_id = ? ORDER BY due_date ASC");
        $stmt->execute([$project_id]);
        $milestones = $stmt->fetchAll();
        
        // Get tasks for each milestone
        foreach ($milestones as &$milestone) {
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE milestone_id = ? ORDER BY due_date ASC");
            $stmt->execute([$milestone['id']]);
            $milestone['tasks'] = $stmt->fetchAll();
        }
        
        echo json_encode(['success' => true, 'data' => $milestones]);
        exit;
    }
    
    if ($method === 'POST') {
        // Create new milestone
        $required = ['project_id', 'title'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                echo json_encode(['success' => false, 'error' => "$field is required."]);
                exit;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO milestones (project_id, title, description, due_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['project_id'],
            trim($data['title']),
            $data['description'] ?? null,
            $data['due_date'] ?? null,
            $data['status'] ?? 'pending'
        ]);
        
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }
    
    if ($method === 'PUT') {
        // Update milestone
        if (!isset($data['id'])) {
            echo json_encode(['success' => false, 'error' => 'Milestone ID required.']);
            exit;
        }
        
        $fields = [];
        $params = [];
        foreach (['title', 'description', 'due_date', 'status'] as $field) {
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
        $sql = "UPDATE milestones SET " . implode(', ', $fields) . " WHERE id = ?";
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
