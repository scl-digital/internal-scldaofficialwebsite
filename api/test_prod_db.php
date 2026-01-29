<?php
// Test connection to production database
try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;dbname=u261025466_Website;charset=utf8mb4',
        'u261025466_SCLDAWebsite',
        'SCLDAWebsite@25',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Try to create comments table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post VARCHAR(255) NOT NULL,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(255) DEFAULT NULL,
        website VARCHAR(255) DEFAULT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        approved TINYINT(1) DEFAULT 1,
        INDEX(post),
        INDEX(created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sql);
    
    echo json_encode([
        'success' => true,
        'message' => 'Connected to u261025466_Website successfully',
        'database' => 'u261025466_Website',
        'user' => 'u261025466_SCLDAWebsite',
        'comments_table' => 'Created or already exists'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'database' => 'u261025466_Website',
        'user' => 'u261025466_SCLDAWebsite'
    ]);
}
?>
