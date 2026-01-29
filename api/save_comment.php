<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$post = isset($data['post']) ? trim($data['post']) : null;
$name = isset($data['name']) ? trim($data['name']) : null;
$email = isset($data['email']) ? trim($data['email']) : null;
$website = isset($data['website']) ? trim($data['website']) : null;
$message = isset($data['message']) ? trim($data['message']) : null;

if (!$post || !$name || !$message) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

try {
    $createTableSql = "CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post VARCHAR(255) NOT NULL,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(255) DEFAULT NULL,
        website VARCHAR(255) DEFAULT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        approved TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($createTableSql);

    $stmt = $pdo->prepare('INSERT INTO comments (post, name, email, website, message) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$post, $name, $email, $website, $message]);

    $id = $pdo->lastInsertId();
    $select = $pdo->prepare('SELECT id, post, name, email, website, message, created_at FROM comments WHERE id = ?');
    $select->execute([$id]);
    $comment = $select->fetch();

    echo json_encode(['success' => true, 'comment' => $comment]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
