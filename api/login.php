<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = isset($data['email']) ? trim($data['email']) : '';
$password = isset($data['password']) ? trim($data['password']) : '';
$user_type = $data['user_type'] ?? 'client';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'error' => 'Email and password required.']);
    exit;
}

$table = $user_type === 'staff' || $user_type === 'admin' ? 'staff' : 'clients';

try {
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    // Debug logging - remove this after fixing
    $debug_info = [
        'email_searched' => $email,
        'table' => $table,
        'user_found' => $user ? 'yes' : 'no',
        'user_email' => $user['email'] ?? 'none',
        'stored_password_length' => isset($user['password']) ? strlen($user['password']) : 'none'
    ];
    
    // Only use plain text comparison (no hashing)
    $isValidPassword = false;
    if ($user) {
        $storedPassword = $user['password'] ?? '';
        if ($storedPassword) {
            $isValidPassword = trim($storedPassword) === trim($password);
            $debug_info['password_check'] = 'plain_text_compare';
            $debug_info['stored_password'] = $storedPassword;
            $debug_info['input_password'] = $password;
            $debug_info['plain_result'] = $isValidPassword;
        }
    }
    if ($user && $isValidPassword) {
        // For admin, check role
        if ($user_type === 'admin' && ($user['role'] ?? '') !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Not an admin user.']);
            exit;
        }
        unset($user['password']);
        echo json_encode(['success' => true, 'user' => $user]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid credentials.', 'debug' => $debug_info]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'debug' => ['exception' => true]]);
} 