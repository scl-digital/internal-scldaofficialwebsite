<?php
// Save Email Settings from Admin Panel
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_encryption', 'from_email', 'from_name'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
            exit;
        }
    }
    
    // Check if settings exist
    $existing = $pdo->query("SELECT id FROM email_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update existing settings
        if (!empty($data['smtp_password'])) {
            // Password provided, update it
            $stmt = $pdo->prepare("
                UPDATE email_settings 
                SET smtp_host = ?, smtp_port = ?, smtp_username = ?, smtp_password = ?, 
                    smtp_encryption = ?, from_email = ?, from_name = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['smtp_host'],
                $data['smtp_port'],
                $data['smtp_username'],
                $data['smtp_password'],
                $data['smtp_encryption'],
                $data['from_email'],
                $data['from_name'],
                $existing['id']
            ]);
        } else {
            // No password provided, don't update it
            $stmt = $pdo->prepare("
                UPDATE email_settings 
                SET smtp_host = ?, smtp_port = ?, smtp_username = ?, 
                    smtp_encryption = ?, from_email = ?, from_name = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['smtp_host'],
                $data['smtp_port'],
                $data['smtp_username'],
                $data['smtp_encryption'],
                $data['from_email'],
                $data['from_name'],
                $existing['id']
            ]);
        }
    } else {
        // Insert new settings
        $stmt = $pdo->prepare("
            INSERT INTO email_settings 
            (smtp_host, smtp_port, smtp_username, smtp_password, smtp_encryption, from_email, from_name) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['smtp_host'],
            $data['smtp_port'],
            $data['smtp_username'],
            $data['smtp_password'] ?? '',
            $data['smtp_encryption'],
            $data['from_email'],
            $data['from_name']
        ]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Email settings saved successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    error_log('Save email settings error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
