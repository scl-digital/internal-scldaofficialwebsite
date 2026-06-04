<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;
$user_type = $data['user_type'] ?? null;

if (!$user_id || !$user_type) {
    echo json_encode(['success' => false, 'error' => 'User ID and type required.']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM messages WHERE (from_id = ? AND from_type = ?) OR (to_id = ? AND to_type = ?) ORDER BY timestamp ASC');
    $stmt->execute([$user_id, $user_type, $user_id, $user_type]);
    $messages = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $messages]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 