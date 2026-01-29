<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$post = isset($_GET['post']) ? trim($_GET['post']) : null;
if (!$post) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing post parameter.']);
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

    $stmt = $pdo->prepare('SELECT id, post, name, email, website, message, created_at FROM comments WHERE post = ? AND approved = 1 ORDER BY created_at ASC');
    $stmt->execute([$post]);
    $comments = $stmt->fetchAll();

    echo json_encode(['success' => true, 'comments' => $comments]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
