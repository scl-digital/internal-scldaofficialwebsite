<?php
header('Content-Type: application/json');
require_once '../db.php';

try {
    // Create products table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        category VARCHAR(100) NULL,
        description TEXT NULL,
        price_amount DECIMAL(10,2) DEFAULT 0,
        price_period VARCHAR(50) NULL, -- e.g. month, Year
        rating DECIMAL(3,2) DEFAULT 0,
        reviews_count INT DEFAULT 0,
        image_url VARCHAR(500) NULL,
        icon_class VARCHAR(100) NULL,
        background_url VARCHAR(500) NULL,
        badge VARCHAR(50) NULL,
        cta_primary_text VARCHAR(100) NULL,
        cta_primary_link VARCHAR(500) NULL,
        cta_secondary_text VARCHAR(100) NULL,
        cta_secondary_link VARCHAR(500) NULL,
        is_featured TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_featured (is_featured)
    )");

    // Auto-migrate missing columns if needed
    $ensureCols = [
        'category' => "VARCHAR(100) NULL",
        'description' => "TEXT NULL",
        'price_amount' => "DECIMAL(10,2) DEFAULT 0",
        'price_period' => "VARCHAR(50) NULL",
        'rating' => "DECIMAL(3,2) DEFAULT 0",
        'reviews_count' => "INT DEFAULT 0",
        'image_url' => "VARCHAR(500) NULL",
        'icon_class' => "VARCHAR(100) NULL",
        'background_url' => "VARCHAR(500) NULL",
        'badge' => "VARCHAR(50) NULL",
        'cta_primary_text' => "VARCHAR(100) NULL",
        'cta_primary_link' => "VARCHAR(500) NULL",
        'cta_secondary_text' => "VARCHAR(100) NULL",
        'cta_secondary_link' => "VARCHAR(500) NULL",
        'is_featured' => "TINYINT(1) DEFAULT 0",
    ];
    foreach ($ensureCols as $col => $def) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = ?");
        $stmt->execute([$col]);
        if ((int)$stmt->fetchColumn() === 0) {
            $pdo->exec("ALTER TABLE products ADD COLUMN $col $def");
        }
    }

    // Seed three demo products if table empty
    $count = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count === 0) {
        $seed = $pdo->prepare("INSERT INTO products (title, category, description, price_amount, price_period, rating, reviews_count, image_url, icon_class, background_url, badge, cta_primary_text, cta_primary_link, cta_secondary_text, cta_secondary_link, is_featured)
                               VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        // Send Money
        $seed->execute([
            'Send Money. Fast. Anywhere.',
            'Finance',
            'Instantly transfer funds across borders with low fees and no headaches — built for the hustle, powered by SCL DA.',
            64.99,
            'month',
            0,
            0,
            '/slider1.jpg',
            'fa-solid fa-wallet',
            '/images/background-image-sized.webp',
            'Featured',
            'Download Now',
            '/applications/landing/index.html',
            'View Details',
            '#',
            1
        ]);
        // Events & Ticketing - ThinkBridge AI
        $seed->execute([
            'ThinkBridge AI',
            'Events & Ticketing',
            'Plan and manage events with ticket sales and QR check-in.',
            149.00,
            'Year',
            0,
            0,
            '/images/product_1.png',
            'fa-solid fa-ticket',
            '/images/background-image-sized.webp',
            'Coming Soon',
            'Download Now',
            '/applications/landing/index.html',
            'View Details',
            '#',
            1
        ]);
        // Another demo app
        $seed->execute([
            'Events & Ticketing Suite',
            'Events & Ticketing',
            'End-to-end event planning with seating charts and on-site check-in.',
            99.00,
            'month',
            0,
            0,
            '/images/product_1.png',
            'fa-solid fa-calendar-check',
            '/images/background-image-sized.webp',
            'Coming Soon',
            'Download Now',
            '/applications/landing/index.html',
            'View Details',
            '#',
            0
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Failed to ensure products table: '.$e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([(int)$_GET['id']]);
            $row = $stmt->fetch();
            echo json_encode(['success'=>true,'data'=>$row]);
            exit;
        }
        $stmt = $pdo->query("SELECT * FROM products ORDER BY is_featured DESC, created_at DESC");
        echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]);
        exit;
    }

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['title']) || trim($data['title'])==='') { echo json_encode(['success'=>false,'error'=>'title required']); exit; }
        $cols = ['title','category','description','price_amount','price_period','rating','reviews_count','image_url','icon_class','background_url','badge','cta_primary_text','cta_primary_link','cta_secondary_text','cta_secondary_link','is_featured'];
        $placeholders = implode(',', array_fill(0, count($cols), '?'));
        $stmt = $pdo->prepare('INSERT INTO products ('.implode(',',$cols).') VALUES ('.$placeholders.')');
        $vals = [];
        foreach ($cols as $c) { $vals[] = $data[$c] ?? null; }
        $stmt->execute($vals);
        echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
        exit;
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) { echo json_encode(['success'=>false,'error'=>'id required']); exit; }
        $fields=[]; $values=[];
        foreach (['title','category','description','price_amount','price_period','rating','reviews_count','image_url','icon_class','background_url','badge','cta_primary_text','cta_primary_link','cta_secondary_text','cta_secondary_link','is_featured'] as $c) {
            if (array_key_exists($c,$data)) { $fields[] = "$c = ?"; $values[] = $data[$c]; }
        }
        if (!$fields) { echo json_encode(['success'=>false,'error'=>'no fields']); exit; }
        $values[] = $data['id'];
        $stmt = $pdo->prepare('UPDATE products SET '.implode(',',$fields).' WHERE id = ?');
        $stmt->execute($values);
        echo json_encode(['success'=>true]);
        exit;
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) { echo json_encode(['success'=>false,'error'=>'id required']); exit; }
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([(int)$data['id']]);
        echo json_encode(['success'=>true]);
        exit;
    }

    echo json_encode(['success'=>false,'error'=>'invalid method']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}


