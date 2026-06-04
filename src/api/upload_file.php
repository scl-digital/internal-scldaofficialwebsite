<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

// Ensure uploads directory exists
$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Create files table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    type VARCHAR(50),
    project_id INT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
)");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded.']);
    exit;
}

$client_id = $_POST['client_id'] ?? null;
$project_id = $_POST['project_id'] ?? null;
if (!$client_id) {
    echo json_encode(['success' => false, 'error' => 'Client ID required.']);
    exit;
}

$file = $_FILES['file'];
$originalName = basename($file['name']);
$ext = pathinfo($originalName, PATHINFO_EXTENSION);
$filename = uniqid('file_', true) . '.' . $ext;
$targetPath = $uploadDir . $filename;
$type = $file['type'];

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    $stmt = $pdo->prepare('INSERT INTO files (client_id, filename, original_name, type, project_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$client_id, $filename, $originalName, $type, $project_id]);
    echo json_encode(['success' => true, 'file' => [
        'id' => $pdo->lastInsertId(),
        'filename' => $filename,
        'original_name' => $originalName,
        'type' => $type
    ]]);
} else {
    echo json_encode(['success' => false, 'error' => 'Upload failed.']);
} 