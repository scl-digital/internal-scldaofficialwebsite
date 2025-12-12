<?php
header('Content-Type: application/json');
require_once '../db.php';

// Services API for admin CRUD
// Fields: id, name, slug, category, description, price_amount, price_period, image_url, details_url, is_active, created_at

try {
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

    // Ensure columns exist when updating from older schemas
    $ensureCols = [
        'slug' => 'VARCHAR(200) UNIQUE NULL',
        'category' => 'VARCHAR(100) NULL',
        'description' => 'TEXT NULL',
        'price_amount' => 'DECIMAL(10,2) DEFAULT 0',
        'price_period' => 'VARCHAR(50) NULL',
        'billing_period' => 'VARCHAR(50) DEFAULT \'month\' NULL',
        'image_url' => 'VARCHAR(500) NULL',
        'details_url' => 'VARCHAR(500) NULL',
        'features' => 'LONGTEXT NULL',
        'notes' => 'LONGTEXT NULL',
        'is_active' => 'TINYINT(1) DEFAULT 1'
    ];
    foreach ($ensureCols as $col => $def) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services' AND COLUMN_NAME = ?");
        $stmt->execute([$col]);
        if ((int)$stmt->fetchColumn() === 0) {
            $pdo->exec("ALTER TABLE services ADD COLUMN $col $def");
        }
    }

    // Seed ALL services from static HTML if table empty
    $count = (int)$pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    if ($count === 0) {
        $ins = $pdo->prepare("INSERT INTO services (name, slug, category, description, price_amount, price_period, image_url, details_url, is_active) VALUES (?,?,?,?,?,?,?,?,?)");
        $seedData = [
            // Core Services
            ['Development','development','Our Services','Custom software, web & mobile development',1000,'','/images/Services/Software_Developer.png','/services-platform/our_services.html#development',1],
            ['Design','design','Our Services','Branding, UI/UX & creative assets',2000,'','/images/Services/Innovative.png','/services-platform/our_services.html#design',1],
            ['Marketing','marketing','Our Services','Performance marketing & growth',4000,'','/images/Services/Strategy.png','/services-platform/our_services.html#marketing',1],
            ['Hosting','hosting','Our Services','Managed cloud hosting & DevOps',6000,'','/images/Services/Top IT Services Company.png','/services-platform/our_services.html#hosting',1],
            ['SecurePay','securepay','Our Services','Payment integration & compliance',12000,'ZAR','/images/payments_card.png','/services-platform/our_services.html#securepay',1],
            
            // Hosting Services
            ['Starter Hosting','starter-hosting','Hosting','5GB SSD Storage, Unlimited Bandwidth, 5 Email Accounts',99,'month','/images/Services/Software_Developer.png','/services-platform/our_services.html#hosting',1],
            ['Business Hosting','business-hosting','Hosting','20GB SSD Storage, Unlimited Bandwidth, 25 Email Accounts',199,'month','/images/Services/Software_Developer.png','/services-platform/our_services.html#hosting',1],
            ['Premium Hosting','premium-hosting','Hosting','50GB SSD Storage, Unlimited Bandwidth, Unlimited Email Accounts',399,'month','/images/Services/Software_Developer.png','/services-platform/our_services.html#hosting',1],
            
            // Website Development
            ['Professional Website','professional-website','Development','10 Pages Design, Mobile Responsive, Blog Integration, SEO Optimized',15000,'','/images/Services/Software_Developer.png','/services-platform/our_services.html#development',1],
            ['Premium Website','premium-website','Development','Unlimited Pages, Custom Design, Blog & CMS, E-commerce Ready',25000,'','/images/Services/Software_Developer.png','/services-platform/our_services.html#development',1],
            
            // E-commerce
            ['Starter E-commerce Store','starter-store','E-commerce','Up to 50 Products, Payment Gateway, Order Management',12000,'','/images/Services/Software_Developer.png','/services-platform/our_services.html#ecommerce',1],
            ['Professional E-commerce Store','professional-store','E-commerce','Unlimited Products, Multiple Payment Methods, Advanced Inventory',20000,'','/images/Services/Software_Developer.png','/services-platform/our_services.html#ecommerce',1],
            ['Enterprise E-commerce Store','enterprise-store','E-commerce','Unlimited Products, Multi-vendor Support, Advanced Analytics',35000,'','/images/Services/Software_Developer.png','/services-platform/our_services.html#ecommerce',1],
            
            // Social Media Marketing
            ['Professional Social Media Management','professional-social','Marketing','4 Platforms, 20 Posts per Month, Custom Content Creation',7500,'month','/images/Services/Strategy.png','/services-platform/our_services.html#marketing',1],
            ['Branded Storytelling','branded-storytelling','Marketing','4 Platforms, 20 Posts per Month, Custom Content Creation',7500,'month','/images/Services/Strategy.png','/services-platform/our_services.html#marketing',1],
            ['Product Campaign Content','product-campaign','Marketing','All Major Platforms, 30+ Posts per Month, Premium Content Creation',15000,'month','/images/Services/Strategy.png','/services-platform/our_services.html#marketing',1],
            ['Motion Visual Design','motion-design','Marketing','All Major Platforms, 30+ Posts per Month, Premium Content Creation',15000,'month','/images/Services/Strategy.png','/services-platform/our_services.html#marketing',1],
            
            // Branding & Design
            ['Personal Brand Identity Kit','personal-brand','Design','Logo Design, Color Palette, Typography, Business Card Design',2500,'month','/images/Services/Innovative.png','/services-platform/our_services.html#design',1],
            ['Product Brand Kit','product-brand','Design','Complete Brand Identity, Stationery Design, Social Media Templates',15000,'','/images/Services/Innovative.png','/services-platform/our_services.html#design',1],
            ['Event Identity Kit','event-identity','Design','Complete Brand Identity, Marketing Materials, Website Design',30000,'','/images/Services/Innovative.png','/services-platform/our_services.html#design',1],
            ['Company Identity Kit','company-identity','Design','Complete Brand Identity, Marketing Materials, Website Design',30000,'','/images/Services/Innovative.png','/services-platform/our_services.html#design',1],
            
            // Payment Solutions
            ['SecurePay Gateway','securepay-gateway','SecurePay','Complete payment processing solution with advanced security',2500,'month','/images/payments_card.png','/services-platform/our_services.html#securepay',1],
            
            // Design Packages
            ['Basic Design Package','basic-design','Design','5 Social Media Posts, 1 Banner Design, 1 Flyer Design',2500,'','/images/Services/Innovative.png','/services-platform/our_services.html#design',1],
            ['Professional Design Package','professional-design','Design','15 Social Media Posts, 3 Banner Designs, 2 Flyer Designs',5000,'','/images/Services/Innovative.png','/services-platform/our_services.html#design',1],
            ['Premium Design Package','premium-design','Design','30 Social Media Posts, 5 Banner Designs, 5 Flyer Designs',10000,'','/images/Services/Innovative.png','/services-platform/our_services.html#design',1],
        ];
        foreach ($seedData as $s) { $ins->execute($s); }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Failed to ensure services table: '.$e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

function read_json() {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
            $stmt->execute([(int)$_GET['id']]);
            echo json_encode(['success'=>true,'data'=>$stmt->fetch()]);
            exit;
        }
        $stmt = $pdo->query("SELECT * FROM services ORDER BY is_active DESC, created_at DESC");
        echo json_encode(['success'=>true,'data'=>$stmt->fetchAll()]);
        exit;
    }

    if ($method === 'POST') {
        $d = read_json();
        if (!isset($d['name']) || trim($d['name'])==='') { echo json_encode(['success'=>false,'error'=>'name required']); exit; }
        $cols = ['name','slug','category','description','price_amount','price_period','billing_period','image_url','details_url','features','notes','is_active'];
        $stmt = $pdo->prepare('INSERT INTO services ('.implode(',',$cols).') VALUES ('.implode(',', array_fill(0,count($cols),'?')).')');
        $vals = [];
        foreach ($cols as $c) { $vals[] = $d[$c] ?? null; }
        $stmt->execute($vals);
        echo json_encode(['success'=>true,'id'=>$pdo->lastInsertId()]);
        exit;
    }

    if ($method === 'PUT' || $method === 'PATCH') {
        $d = read_json();
        if (!isset($d['id'])) { echo json_encode(['success'=>false,'error'=>'id required']); exit; }
        $fields=[]; $values=[];
        foreach (['name','slug','category','description','price_amount','price_period','billing_period','image_url','details_url','features','notes','is_active'] as $c) {
            if (array_key_exists($c,$d)) { $fields[] = "$c = ?"; $values[] = $d[$c]; }
        }
        if (!$fields) { echo json_encode(['success'=>false,'error'=>'no fields']); exit; }
        $values[] = $d['id'];
        $stmt = $pdo->prepare('UPDATE services SET '.implode(',',$fields).' WHERE id = ?');
        $stmt->execute($values);
        echo json_encode(['success'=>true]);
        exit;
    }

    if ($method === 'DELETE') {
        $d = read_json();
        if (!isset($d['id'])) { echo json_encode(['success'=>false,'error'=>'id required']); exit; }
        $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
        $stmt->execute([(int)$d['id']]);
        echo json_encode(['success'=>true]);
        exit;
    }

    echo json_encode(['success'=>false,'error'=>'invalid method']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}

?>


