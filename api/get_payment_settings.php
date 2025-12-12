<?php
// Ensure API endpoints return JSON only and do not leak HTML/PHP errors
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ob_start();
header('Content-Type: application/json');

set_exception_handler(function($e){
    if (!headers_sent()) header('Content-Type: application/json');
    http_response_code(500);
    error_log('Uncaught exception: ' . $e->getMessage() . " | " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    @ob_end_flush();
    exit;
});

set_error_handler(function($errno, $errstr, $errfile, $errline){
    if (!headers_sent()) header('Content-Type: application/json');
    http_response_code(500);
    error_log("PHP error [$errno]: $errstr in $errfile:$errline");
    echo json_encode(['success' => false, 'error' => $errstr]);
    @ob_end_flush();
    exit;
});

require_once 'db.php';

try {
    // Read latest company settings
    $stmt = $pdo->query("SELECT * FROM company_settings ORDER BY id DESC LIMIT 1");
    $settings = $stmt->fetch();

    $clientId = $settings['paypal_client_id'] ?? '';
    $mode = $settings['paypal_mode'] ?? 'sandbox';
    // Currency column may not exist; default to ZAR for this project
    $currency = isset($settings['currency']) && $settings['currency'] ? $settings['currency'] : 'ZAR';
    // Use explicit paypal_enabled column if present, otherwise fallback to presence of client id
    if (isset($settings['paypal_enabled'])) {
        $enabled = ($settings['paypal_enabled'] == '1' || $settings['paypal_enabled'] === 1) ? '1' : '0';
    } else {
        $enabled = ($clientId && trim($clientId) !== '') ? '1' : '0';
    }

    // Public response: do NOT include any secret values
    $public = [
        'paypal_enabled' => $enabled,
        'paypal_client_id' => $clientId,
        'paypal_mode' => $mode,
        'currency' => $currency
    ];

    @ob_end_clean();
    echo json_encode(['success' => true, 'data' => $public]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    @ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
