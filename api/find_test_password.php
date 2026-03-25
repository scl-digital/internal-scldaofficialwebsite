<?php
require_once 'db.php';
try {
    $stmt = $pdo->query("SELECT email, password FROM clients WHERE email = '+27631250268' LIMIT 1");
    $user = $stmt->fetch();
    echo json_encode(['success' => true, 'email' => $user['email'], 'password' => $user['password']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
