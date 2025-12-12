<?php
/**
 * Final Test: API Endpoints from AdminPortal Context
 */
require_once __DIR__ . '/_json_api_bootstrap.php';

$test = [
    'timestamp' => date('Y-m-d H:i:s'),
    'tests' => [],
    'errors' => []
];

// Simulate being called from AdminPortal/
$_SERVER['REQUEST_URI'] = '/AdminPortal/billing-admin.html';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

try {
    // Test 1: Direct Projects API Call
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = ['limit' => 5, 'page' => 1];
    
    ob_start();
    include __DIR__ . '/admin/projects_api.php';
    $output = ob_get_clean();
    
    $result = json_decode($output, true);
    $test['tests']['projects_api'] = [
        'endpoint' => '/api/admin/projects_api.php',
        'method' => 'GET',
        'success' => $result['success'] ?? false,
        'data_count' => count($result['data'] ?? []),
        'total' => $result['pagination']['total'] ?? 0,
        'sample_project' => isset($result['data'][0]) ? [
            'id' => $result['data'][0]['id'] ?? null,
            'title' => $result['data'][0]['title'] ?? null,
            'client_name' => $result['data'][0]['client_name'] ?? null,
            'status' => $result['data'][0]['status'] ?? null,
        ] : null
    ];
    
} catch (Exception $e) {
    $test['errors'][] = 'Projects API Error: ' . $e->getMessage();
}

try {
    // Test 2: Activity Log API
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = ['limit' => 10, 'page' => 1, 'sort' => 'desc'];
    
    ob_start();
    include __DIR__ . '/admin/activity_log_api.php';
    $output = ob_get_clean();
    
    $result = json_decode($output, true);
    $test['tests']['activity_log_api'] = [
        'endpoint' => '/api/admin/activity_log_api.php',
        'method' => 'GET',
        'success' => $result['success'] ?? false,
        'data_count' => count($result['data'] ?? []),
        'total' => $result['pagination']['total'] ?? 0
    ];
    
} catch (Exception $e) {
    $test['errors'][] = 'Activity Log API Error: ' . $e->getMessage();
}

try {
    // Test 3: Tasks API with milestones action
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET = ['action' => 'milestones', 'project_id' => 14, 'limit' => 10];
    
    ob_start();
    include __DIR__ . '/admin/tasks_api.php';
    $output = ob_get_clean();
    
    $result = json_decode($output, true);
    $test['tests']['tasks_api_milestones'] = [
        'endpoint' => '/api/admin/tasks_api.php?action=milestones',
        'method' => 'GET',
        'success' => $result['success'] ?? false,
        'data_count' => count($result['data'] ?? [])
    ];
    
} catch (Exception $e) {
    $test['errors'][] = 'Tasks API Milestones Error: ' . $e->getMessage();
}

// Summary
$test['status'] = count($test['errors']) === 0 ? 'ALL_TESTS_PASSED' : 'SOME_TESTS_FAILED';
$test['tests_passed'] = count(array_filter($test['tests'], fn($t) => $t['success'] ?? false));
$test['tests_failed'] = count($test['tests']) - $test['tests_passed'];

echo json_encode($test, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
