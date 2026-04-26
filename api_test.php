<?php
// Quick API test
header('Content-Type: application/json');

try {
    require_once 'api/db.php';
    
    // Test the list endpoint
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM blocked_regions");
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'message' => 'API is working correctly',
        'blocked_count' => $result['count'],
        'database_connected' => true
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'API test failed: ' . $e->getMessage(),
        'database_connected' => false
    ]);
}
?>
