<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$client_id = $data['client_id'] ?? null;

if (!$client_id) {
    echo json_encode(['success' => false, 'error' => 'Client ID required.']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE client_id = ?');
    $stmt->execute([$client_id]);
    $projects = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $projects]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 