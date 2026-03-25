<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$client_id = $data['client_id'] ?? $_GET['client_id'] ?? null;

if (!$client_id) {
    echo json_encode(['success' => false, 'error' => 'Client ID required.']);
    exit;
}

try {
    // Join projects with services to get purchased product details
    $stmt = $pdo->prepare('
        SELECT 
            p.id as project_id,
            p.title as project_name,
            p.status as project_status,
            p.progress,
            p.start_date,
            p.deadline,
            p.description as project_description,
            s.name as service_name,
            s.description as service_description,
            s.price_amount,
            s.price_period,
            s.billing_period,
            s.category
        FROM projects p 
        LEFT JOIN services s ON p.service_id = s.id 
        WHERE p.client_id = ?
        ORDER BY p.created_at DESC
    ');
    $stmt->execute([$client_id]);
    $products = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $products]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
