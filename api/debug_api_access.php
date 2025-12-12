<?php
/**
 * Debug: Check API Access & Session
 */
require_once __DIR__ . '/_json_api_bootstrap.php';

$debug = [
    'timestamp' => date('Y-m-d H:i:s'),
    'request' => [],
    'session' => [],
    'headers' => [],
    'api_path' => ''
];

// Check request method
$debug['request']['method'] = $_SERVER['REQUEST_METHOD'];
$debug['request']['url'] = $_SERVER['REQUEST_URI'] ?? 'N/A';
$debug['request']['remote_addr'] = $_SERVER['REMOTE_ADDR'] ?? 'N/A';

// Check session
$debug['session']['session_id'] = session_id();
$debug['session']['session_status'] = session_status();
$debug['session']['session_data_exists'] = isset($_SESSION);
$debug['session']['scl_admin'] = $_SESSION['scl_admin'] ?? null;

// Check headers
$debug['headers']['content_type'] = $_SERVER['CONTENT_TYPE'] ?? 'N/A';
$debug['headers']['accept'] = $_SERVER['HTTP_ACCEPT'] ?? 'N/A';
$debug['headers']['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'N/A';

// Check file path
$debug['api_path'] = __FILE__;

// Try to load the API
try {
    require_once 'db.php';
    
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['limit'] = 5;
    
    // Capture API output
    ob_start();
    include __DIR__ . '/admin/projects_api.php';
    $output = ob_get_clean();
    
    $parsed = json_decode($output, true);
    $debug['api_response']['success'] = $parsed['success'] ?? false;
    $debug['api_response']['has_data'] = isset($parsed['data']);
    $debug['api_response']['data_count'] = count($parsed['data'] ?? []);
    
} catch (Exception $e) {
    $debug['error'] = $e->getMessage();
}

echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
