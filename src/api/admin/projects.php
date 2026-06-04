<?php
header('Content-Type: application/json');
require_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // List all projects with client info
        $stmt = $pdo->query("SELECT p.id, p.client_id, c.name AS client_name, c.email AS client_email, p.title, p.status, p.progress, p.start_date, p.deadline, p.description FROM projects p JOIN clients c ON c.id = p.client_id ORDER BY p.id DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $required = ['client_id','title'];
        foreach ($required as $f) { if (!isset($data[$f]) || $data[$f] === '') { echo json_encode(['success'=>false,'error'=>"$f is required"]); exit; } }
        $client_id = (int)$data['client_id'];
        $title = trim($data['title']);
        $status = $data['status'] ?? 'pending';
        $progress = (int)($data['progress'] ?? 0);
        $start_date = $data['start_date'] ?? null;
        $deadline = $data['deadline'] ?? null;
        $description = $data['description'] ?? null;
        $stmt = $pdo->prepare('INSERT INTO projects (client_id, title, status, progress, start_date, deadline, description) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$client_id, $title, $status, $progress, $start_date, $deadline, $description]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) { echo json_encode(['success'=>false,'error'=>'Project ID required']); exit; }
        $fields = [];
        $params = [];
        foreach (['client_id','title','status','progress','start_date','deadline','description'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (!$fields) { echo json_encode(['success'=>false,'error'=>'No fields to update']); exit; }
        $params[] = $data['id'];
        $sql = 'UPDATE projects SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) { echo json_encode(['success'=>false,'error'=>'Project ID required']); exit; }
        $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


