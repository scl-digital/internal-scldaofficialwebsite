<?php
// Setup script to create comments database and table for local development

$host = '127.0.0.1';
$user = 'root';
$pass = '';

// First, connect without specifying a database to create one
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS internal_scl;");
    echo "✓ Database created or already exists.\n";

    // Now connect to the database and create table
    $pdo = new PDO("mysql:host=$host;dbname=internal_scl;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Create comments table
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
    echo "✓ Comments table created or already exists.\n";

    // Insert sample comment
    $stmt = $pdo->prepare("INSERT INTO comments (post, name, email, website, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        'single-standard.html',
        'Test User',
        'test@example.com',
        'https://example.com',
        'This is a test comment to verify the system is working.'
    ]);
    echo "✓ Sample comment inserted.\n";

    echo "\n✓✓✓ Setup complete! Comments system is ready.\n";
    echo "Database: internal_scl\n";
    echo "Table: comments\n";

} catch (PDOException $e) {
    http_response_code(500);
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
