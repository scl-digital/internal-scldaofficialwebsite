<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once './db.php';

try {
    // Ensure table exists (same definition as admin)
    $pdo->exec("CREATE TABLE IF NOT EXISTS services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        slug VARCHAR(200) UNIQUE NULL,
        category VARCHAR(100) NULL,
        description TEXT NULL,
        price_amount DECIMAL(10,2) DEFAULT 0,
        price_period VARCHAR(50) NULL,
        billing_period VARCHAR(50) DEFAULT 'month' NULL,
        image_url VARCHAR(500) NULL,
        details_url VARCHAR(500) NULL,
        features LONGTEXT NULL,
        notes LONGTEXT NULL,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_active (is_active)
    )");

    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND is_active = 1");
        $stmt->execute([(int)$_GET['id']]);
        echo json_encode(['success'=>true,'data'=>$stmt->fetch()]);
        exit;
    }

    $stmt = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY created_at DESC");
    echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}
?>


