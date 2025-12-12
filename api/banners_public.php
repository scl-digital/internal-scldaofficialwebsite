<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

try {
    // Get only active banners for public display
    $stmt = $pdo->prepare("SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC");
    $stmt->execute();
    $banners = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $banners
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to fetch banners'
    ]);
}
?>
