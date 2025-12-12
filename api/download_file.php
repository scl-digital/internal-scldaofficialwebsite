<?php
require_once 'db.php';
$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo 'File ID required.';
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM files WHERE id = ?');
$stmt->execute([$id]);
$file = $stmt->fetch();
if (!$file) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}
$filepath = __DIR__ . '/../uploads/' . $file['filename'];
if (!file_exists($filepath)) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}
header('Content-Description: File Transfer');
header('Content-Type: ' . $file['type']);
header('Content-Disposition: attachment; filename="' . $file['original_name'] . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit; 