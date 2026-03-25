<?php
require_once 'db.php';
try {
    $stmt = $pdo->query("SELECT email FROM clients LIMIT 1");
    $user = $stmt->fetch();
    echo json_encode(['success' => true, 'email' => $user['email'] ?? 'none']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
