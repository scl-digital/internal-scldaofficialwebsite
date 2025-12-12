<?php
/**
 * Debug: Test Projects API Loading
 */
require_once __DIR__ . '/_json_api_bootstrap.php';

$debug = [
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => [],
    'database' => [],
    'api_test' => []
];

try {
    // Check environment
    $debug['environment']['php_version'] = phpversion();
    $debug['environment']['pdo_available'] = extension_loaded('pdo');
    $debug['environment']['pdo_mysql'] = extension_loaded('pdo_mysql');
    
    // Check database connection
    require_once 'db.php';
    $debug['database']['connection'] = 'OK';
    
    // Check projects table
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM projects");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $debug['database']['project_count'] = $count;
    
    // Test the exact API query
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_GET['limit'] = 5;
    $_GET['page'] = 1;
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    
    // Simulate the API call
    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(100, max(1, (int)($_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;
    
    $filter_status = $_GET['status'] ?? null;
    $filter_client = $_GET['client_id'] ?? null;
    
    $where = [];
    $params = [];
    
    if ($filter_status) {
        $where[] = "p.status = ?";
        $params[] = $filter_status;
    }
    
    if ($filter_client) {
        $where[] = "p.client_id = ?";
        $params[] = (int)$filter_client;
    }
    
    $where_clause = $where ? " WHERE " . implode(" AND ", $where) : "";
    
    // Get total count
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM projects p" . $where_clause);
    $stmt->execute($params);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $debug['database']['total_records'] = $total;
    
    // Get data
    $stmt = $pdo->prepare("
        SELECT 
            p.*,
            c.name as client_name,
            c.email as client_email,
            COUNT(DISTINCT m.id) as milestone_count,
            COUNT(DISTINCT t.id) as task_count
        FROM projects p
        LEFT JOIN clients c ON p.client_id = c.id
        LEFT JOIN milestones m ON p.id = m.project_id
        LEFT JOIN tasks t ON p.id = t.project_id
        " . $where_clause . "
        GROUP BY p.id
        ORDER BY p.deadline ASC, p.created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $debug['database']['returned_records'] = count($projects);
    $debug['database']['first_record'] = isset($projects[0]) ? $projects[0] : null;
    
    // API response format
    $debug['api_test']['response_format'] = [
        'success' => true,
        'data' => $projects,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => ceil($total / $limit)
        ]
    ];
    
} catch (Exception $e) {
    $debug['errors'][] = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];
}

echo json_encode($debug, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
