<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

// Create domains table if it doesn't exist
function createDomainsTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS domains (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_id INT NOT NULL,
        domain_name VARCHAR(255) NOT NULL,
        status ENUM('Active', 'Pending', 'Expired', 'Suspended') DEFAULT 'Pending',
        expiry_date DATE,
        auto_renewal BOOLEAN DEFAULT FALSE,
        registrar VARCHAR(255),
        registration_date DATE,
        renewal_price DECIMAL(10,2),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_client_id (client_id),
        INDEX idx_domain_name (domain_name),
        INDEX idx_status (status),
        INDEX idx_expiry_date (expiry_date)
    )";
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Initialize table
createDomainsTable($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            // Get domains - can filter by client_id
            $client_id = $_GET['client_id'] ?? null;
            
            if ($client_id) {
                $stmt = $pdo->prepare("SELECT * FROM domains WHERE client_id = ? ORDER BY domain_name ASC");
                $stmt->execute([$client_id]);
            } else {
                $stmt = $pdo->prepare("SELECT d.*, c.name as client_name FROM domains d LEFT JOIN clients c ON d.client_id = c.id ORDER BY d.domain_name ASC");
                $stmt->execute();
            }
            
            $domains = $stmt->fetchAll();
            
            // Get domain statistics
            $stats = [];
            if ($client_id) {
                $statsStmt = $pdo->prepare("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN auto_renewal = 1 THEN 1 ELSE 0 END) as auto_renewal,
                        SUM(CASE WHEN expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND expiry_date > CURDATE() THEN 1 ELSE 0 END) as expiring_soon,
                        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending
                    FROM domains 
                    WHERE client_id = ?
                ");
                $statsStmt->execute([$client_id]);
            } else {
                $statsStmt = $pdo->prepare("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'Active' THEN 1 ELSE 0 END) as active,
                        SUM(CASE WHEN auto_renewal = 1 THEN 1 ELSE 0 END) as auto_renewal,
                        SUM(CASE WHEN expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND expiry_date > CURDATE() THEN 1 ELSE 0 END) as expiring_soon,
                        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending
                    FROM domains
                ");
                $statsStmt->execute();
            }
            $stats = $statsStmt->fetch();
            
            echo json_encode([
                'success' => true,
                'data' => $domains,
                'stats' => $stats
            ]);
            break;
            
        case 'POST':
            // Create new domain
            $client_id = $input['client_id'] ?? null;
            $domain_name = $input['domain_name'] ?? '';
            $status = $input['status'] ?? 'Pending';
            $expiry_date = $input['expiry_date'] ?? null;
            $auto_renewal = $input['auto_renewal'] ?? false;
            $registrar = $input['registrar'] ?? '';
            $registration_date = $input['registration_date'] ?? null;
            $renewal_price = $input['renewal_price'] ?? null;
            $notes = $input['notes'] ?? '';
            
            if (!$client_id || empty($domain_name)) {
                throw new Exception('Client ID and Domain Name are required');
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO domains (client_id, domain_name, status, expiry_date, auto_renewal, registrar, registration_date, renewal_price, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$client_id, $domain_name, $status, $expiry_date, $auto_renewal, $registrar, $registration_date, $renewal_price, $notes]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Domain created successfully',
                'id' => $pdo->lastInsertId()
            ]);
            break;
            
        case 'PUT':
            // Update domain
            $id = $input['id'] ?? null;
            if (!$id) {
                throw new Exception('Domain ID is required');
            }
            
            $client_id = $input['client_id'] ?? null;
            $domain_name = $input['domain_name'] ?? '';
            $status = $input['status'] ?? 'Pending';
            $expiry_date = $input['expiry_date'] ?? null;
            $auto_renewal = $input['auto_renewal'] ?? false;
            $registrar = $input['registrar'] ?? '';
            $registration_date = $input['registration_date'] ?? null;
            $renewal_price = $input['renewal_price'] ?? null;
            $notes = $input['notes'] ?? '';
            
            if (!$client_id || empty($domain_name)) {
                throw new Exception('Client ID and Domain Name are required');
            }
            
            $stmt = $pdo->prepare("
                UPDATE domains 
                SET client_id = ?, domain_name = ?, status = ?, expiry_date = ?, auto_renewal = ?, 
                    registrar = ?, registration_date = ?, renewal_price = ?, notes = ?
                WHERE id = ?
            ");
            
            $stmt->execute([$client_id, $domain_name, $status, $expiry_date, $auto_renewal, $registrar, $registration_date, $renewal_price, $notes, $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Domain updated successfully'
            ]);
            break;
            
        case 'DELETE':
            // Delete domain
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('Domain ID is required');
            }
            
            $stmt = $pdo->prepare("DELETE FROM domains WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Domain deleted successfully'
            ]);
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
