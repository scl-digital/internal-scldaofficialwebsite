<?php
/**
 * Test Script - Project Management API
 * Validates all endpoints and functionality
 */
require_once __DIR__ . '/_json_api_bootstrap.php';

$results = [
    'database' => [],
    'apis' => [],
    'errors' => []
];

try {
    // Test database connection
    require_once 'api/db.php';
    $results['database']['connection'] = 'OK';
    
    // Check tables
    $tables = ['clients', 'projects', 'milestones', 'tasks', 'activity_log', 'attachments'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        $exists = $stmt->rowCount() > 0;
        $results['database']['tables'][$table] = $exists ? 'EXISTS' : 'MISSING';
    }
    
    // Get project count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $results['database']['project_count'] = $count;
    
    // Get activity log count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM activity_log");
    $logCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $results['database']['activity_log_count'] = $logCount;
    
} catch (Exception $e) {
    $results['errors'][] = "Database Error: " . $e->getMessage();
}

try {
    // Test Projects API
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['limit'] = 5;
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    ob_start();
    chdir(__DIR__ . '/admin');
    include 'projects_api.php';
    chdir(__DIR__);
    $output = ob_get_clean();
    $apiResult = json_decode($output, true);
    $results['apis']['projects_get'] = $apiResult['success'] ? 'OK' : 'FAILED';
    if ($apiResult['success'] && isset($apiResult['data'])) {
        $results['apis']['projects_loaded'] = count($apiResult['data']);
    }
} catch (Exception $e) {
    $results['errors'][] = "Projects API Error: " . $e->getMessage();
}

try {
    // Test Activity Log API
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['limit'] = 5;
    $_GET['sort'] = 'desc';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    ob_start();
    chdir(__DIR__ . '/admin');
    include 'activity_log_api.php';
    chdir(__DIR__);
    $output = ob_get_clean();
    $apiResult = json_decode($output, true);
    $results['apis']['activity_log_get'] = $apiResult['success'] ? 'OK' : 'FAILED';
    if ($apiResult['success'] && isset($apiResult['data'])) {
        $results['apis']['activity_logs_loaded'] = count($apiResult['data']);
    }
} catch (Exception $e) {
    $results['errors'][] = "Activity Log API Error: " . $e->getMessage();
}

$results['status'] = count($results['errors']) === 0 ? 'SUCCESS' : 'PARTIAL';
$results['timestamp'] = date('Y-m-d H:i:s');

echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
