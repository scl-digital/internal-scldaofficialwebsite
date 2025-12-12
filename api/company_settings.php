<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

// Company settings API for managing business information on invoices
try {
    // Create company_settings table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS company_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        company_name VARCHAR(200) NOT NULL DEFAULT 'SCL Digital Agency',
        company_email VARCHAR(200) NOT NULL DEFAULT 'info@softwarecreativelabs.com',
        company_phone VARCHAR(50) DEFAULT '+27 21 123 4567',
        company_address TEXT DEFAULT 'Cape Town, South Africa',
        company_website VARCHAR(200) DEFAULT 'https://softwarecreativelabs.com',
        logo_url VARCHAR(500) DEFAULT '/images/SCL_Blue.png',
        tax_number VARCHAR(100) DEFAULT '',
        registration_number VARCHAR(100) DEFAULT '',
            bank_details TEXT DEFAULT '',
            paypal_client_id VARCHAR(500) DEFAULT '',
            paypal_client_secret VARCHAR(500) DEFAULT '',
            paypal_mode ENUM('sandbox','live') DEFAULT 'sandbox',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Ensure new PayPal columns exist (for upgrades where table previously existed)
    try {
        $cols = $pdo->query("SHOW COLUMNS FROM company_settings LIKE 'paypal_client_id'")->fetch();
        if (!$cols) {
            $pdo->exec("ALTER TABLE company_settings ADD COLUMN paypal_client_id VARCHAR(500) DEFAULT ''");
        }
        $cols = $pdo->query("SHOW COLUMNS FROM company_settings LIKE 'paypal_client_secret'")->fetch();
        if (!$cols) {
            $pdo->exec("ALTER TABLE company_settings ADD COLUMN paypal_client_secret VARCHAR(500) DEFAULT ''");
        }
        $cols = $pdo->query("SHOW COLUMNS FROM company_settings LIKE 'paypal_mode'")->fetch();
        if (!$cols) {
            $pdo->exec("ALTER TABLE company_settings ADD COLUMN paypal_mode ENUM('sandbox','live') DEFAULT 'sandbox'");
        }
        $cols = $pdo->query("SHOW COLUMNS FROM company_settings LIKE 'paypal_enabled'")->fetch();
        if (!$cols) {
            $pdo->exec("ALTER TABLE company_settings ADD COLUMN paypal_enabled TINYINT(1) DEFAULT 0");
        }
        $cols = $pdo->query("SHOW COLUMNS FROM company_settings LIKE 'currency'")->fetch();
        if (!$cols) {
            $pdo->exec("ALTER TABLE company_settings ADD COLUMN currency VARCHAR(10) DEFAULT 'ZAR'");
        }
    } catch (Exception $e) {
        // Ignore errors here; table may not exist or permissions may prevent ALTER
    }

    // Insert default settings if table is empty
    $stmt = $pdo->query("SELECT COUNT(*) FROM company_settings");
    if ((int)$stmt->fetchColumn() === 0) {
            $pdo->exec("INSERT INTO company_settings (company_name, company_email, company_phone, company_address, company_website, logo_url, paypal_client_id, paypal_client_secret, paypal_mode) VALUES 
                ('SCL Digital Agency (Pty) Ltd', 'info@softwarecreativelabs.com', '+27 21 123 4567', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'sandbox')");
    }

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM company_settings ORDER BY id DESC LIMIT 1");
        $settings = $stmt->fetch();

        // Determine if this request is coming from the admin UI (allows returning sensitive secret)
        $isAdmin = isset($_SERVER['HTTP_X_ADMIN_API']) && $_SERVER['HTTP_X_ADMIN_API'] === '1';

        // Build response but omit sensitive fields unless admin header present
        $public = $settings ?: [];
        if (!$isAdmin) {
            unset($public['paypal_client_secret']);
        }

        echo json_encode(['success' => true, 'data' => $public]);
        exit;
    }

    if ($method === 'PUT' || $method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Update or insert company settings
            $stmt = $pdo->prepare("INSERT INTO company_settings (company_name, company_email, company_phone, company_address, company_website, logo_url, tax_number, registration_number, bank_details, paypal_client_id, paypal_client_secret, paypal_mode, paypal_enabled, currency) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) 
                               ON DUPLICATE KEY UPDATE 
                               company_name = VALUES(company_name),
                               company_email = VALUES(company_email),
                               company_phone = VALUES(company_phone),
                               company_address = VALUES(company_address),
                               company_website = VALUES(company_website),
                               logo_url = VALUES(logo_url),
                               tax_number = VALUES(tax_number),
                               registration_number = VALUES(registration_number),
                               bank_details = VALUES(bank_details),
                                   paypal_client_id = VALUES(paypal_client_id),
                                   paypal_client_secret = VALUES(paypal_client_secret),
                                   paypal_mode = VALUES(paypal_mode),
                                   paypal_enabled = VALUES(paypal_enabled),
                                   currency = VALUES(currency),
                               updated_at = CURRENT_TIMESTAMP");
        
        $stmt->execute([
            $data['company_name'] ?? 'SCL Digital Agency',
            $data['company_email'] ?? 'info@softwarecreativelabs.com',
            $data['company_phone'] ?? '+27 21 123 4567',
            $data['company_address'] ?? 'Cape Town, South Africa',
            $data['company_website'] ?? 'https://softwarecreativelabs.com',
            $data['logo_url'] ?? '/images/SCL_Blue.png',
            $data['tax_number'] ?? '',
            $data['registration_number'] ?? '',
                $data['bank_details'] ?? '',
                $data['paypal_client_id'] ?? '',
                $data['paypal_client_secret'] ?? '',
                $data['paypal_mode'] ?? 'sandbox',
                $data['paypal_enabled'] ?? 0,
                $data['currency'] ?? 'ZAR'
        ]);
        
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
