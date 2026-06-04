<?php
// Get Email Settings for Admin Configuration
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

try {
    $emailSettings = $pdo->query("SELECT * FROM email_settings ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    if ($emailSettings) {
        // Don't send password to frontend
        unset($emailSettings['smtp_password']);
        echo json_encode(['success' => true, 'data' => $emailSettings]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No email settings found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
