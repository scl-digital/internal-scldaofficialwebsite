<?php
if (defined('SCL_JSON_BOOTSTRAP_INCLUDED')) return;
define('SCL_JSON_BOOTSTRAP_INCLUDED', true);

// Disable HTML error display for API endpoints; always return JSON on failure
@ini_set('display_errors', '0');
@ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ob_start();
if (!headers_sent()) header('Content-Type: application/json');

set_exception_handler(function($e){
    if (!headers_sent()) header('Content-Type: application/json');
    http_response_code(500);
    error_log('Uncaught exception: ' . $e->getMessage() . " | " . $e->getTraceAsString());
    @ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
});

set_error_handler(function($errno, $errstr, $errfile, $errline){
    if (!headers_sent()) header('Content-Type: application/json');
    http_response_code(500);
    error_log("PHP error [$errno]: $errstr in $errfile:$errline");
    @ob_end_clean();
    echo json_encode(['success' => false, 'error' => $errstr]);
    exit;
});

// Helper to safely finish the request with JSON (clears any buffered output)
function scl_json_response($data) {
    @ob_end_clean();
    if (!headers_sent()) header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

?>
