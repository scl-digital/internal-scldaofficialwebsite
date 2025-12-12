<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$clientId = $_GET['client_id'] ?? $_POST['client_id'] ?? null;
if (!$clientId) {
    $input = json_decode(file_get_contents('php://input'), true);
    $clientId = $input['client_id'] ?? null;
}

if (!$clientId) {
    echo json_encode(['success' => false, 'error' => 'client_id required']);
    exit;
}

try {
    // Sum unpaid invoices (status sent or overdue) minus payments
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount),0) AS total_due FROM invoices WHERE client_id = ? AND status IN ('sent','overdue')");
    $stmt->execute([$clientId]);
    $totalDue = (float)$stmt->fetchColumn();

    $pstmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) AS total_paid FROM payments WHERE client_id = ? AND status = 'completed'");
    $pstmt->execute([$clientId]);
    $totalPaid = (float)$pstmt->fetchColumn();

    $balance = max(0, $totalDue - $totalPaid);

    $ilst = $pdo->prepare('SELECT id, invoice_number, total_amount, status, due_date, issue_date FROM invoices WHERE client_id = ? ORDER BY issue_date DESC LIMIT 10');
    $ilst->execute([$clientId]);
    $invoices = $ilst->fetchAll();

    echo json_encode(['success' => true, 'balance' => $balance, 'invoices' => $invoices]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
