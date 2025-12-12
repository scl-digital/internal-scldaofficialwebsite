<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$client_id = $_GET['client_id'] ?? $_POST['client_id'] ?? null;
$project_id = $_GET['project_id'] ?? $_POST['project_id'] ?? null;
if (!$client_id) {
    echo json_encode(['success' => false, 'error' => 'Client ID required.']);
    exit;
}
$sql = 'SELECT * FROM files WHERE client_id = ?';
$params = [$client_id];
if ($project_id) {
    $sql .= ' AND project_id = ?';
    $params[] = $project_id;
}
$sql .= ' ORDER BY uploaded_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$files = $stmt->fetchAll();
echo json_encode(['success' => true, 'data' => $files]); 