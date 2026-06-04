<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

// Get client ID from session or parameter
$client_id = $_GET['client_id'] ?? null;

if (!$client_id) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Client ID is required'
    ]);
    exit;
}

try {
    // Get domains for specific client
    $stmt = $pdo->prepare("SELECT * FROM domains WHERE client_id = ? ORDER BY domain_name ASC");
    $stmt->execute([$client_id]);
    $domains = $stmt->fetchAll();
    
    // Get domain statistics for client
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
    $stats = $statsStmt->fetch();
    
    echo json_encode([
        'success' => true,
        'data' => $domains,
        'stats' => $stats
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch domains'
    ]);
}
?>
