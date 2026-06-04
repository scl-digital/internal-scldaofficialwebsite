<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$from_id = $data['from_id'] ?? null;
$to_id = $data['to_id'] ?? null;
$from_type = $data['from_type'] ?? null;
$to_type = $data['to_type'] ?? null;
$message = $data['message'] ?? '';

if (!$from_id || !$to_id || !$from_type || !$to_type || !$message) {
    echo json_encode(['success' => false, 'error' => 'All fields required.']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO messages (from_id, to_id, from_type, to_type, message) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$from_id, $to_id, $from_type, $to_type, $message]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} 