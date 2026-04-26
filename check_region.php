<?php
/**
 * Region Check Gateway
 * Checks if a selected region is blocked and redirects accordingly
 */

header('Content-Type: application/json');

// Include database connection
require_once 'api/db.php';

// Get selected country from request
$country = $_GET['country'] ?? $_POST['country'] ?? '';

if (empty($country)) {
    echo json_encode([
        'success' => false,
        'message' => 'No country selected',
        'redirect' => null
    ]);
    exit;
}

// Check if region is blocked
try {
    $stmt = $pdo->prepare("SELECT * FROM blocked_regions WHERE country_code = ?");
    $stmt->execute([strtoupper($country)]);
    $blocked = $stmt->fetch();
    
    if ($blocked) {
        // Region is blocked - redirect to blocked page
        echo json_encode([
            'success' => true,
            'blocked' => true,
            'message' => 'Access from your region is restricted',
            'country_name' => $blocked['country_name'],
            'reason' => $blocked['reason'],
            'redirect' => 'blocked.html'
        ]);
    } else {
        // Region is allowed - continue to main site
        echo json_encode([
            'success' => true,
            'blocked' => false,
            'message' => 'Access granted',
            'redirect' => null
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error checking region: ' . $e->getMessage(),
        'redirect' => null
    ]);
}
?>
