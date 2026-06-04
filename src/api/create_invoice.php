<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$clientId = $input['client_id'] ?? null;
$amount = isset($input['amount']) ? (float)$input['amount'] : null;
$description = $input['description'] ?? null;
$dueDate = $input['due_date'] ?? null;
$issueDate = $input['issue_date'] ?? date('Y-m-d');
$status = $input['status'] ?? 'sent';
$items = $input['items'] ?? [];
$services = $input['services'] ?? [];

if (!$clientId || !$amount || !$dueDate) {
    echo json_encode(['success' => false, 'error' => 'client_id, amount, and due_date are required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Create invoice number
    $invoiceNumber = 'INV-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(3)));

    $totalAmount = $amount;
    $taxAmount = 0.00;

    $stmt = $pdo->prepare('INSERT INTO invoices (client_id, invoice_number, amount, tax_amount, total_amount, status, due_date, issue_date, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$clientId, $invoiceNumber, $amount, $taxAmount, $totalAmount, $status, $dueDate, $issueDate, $description]);
    $invoiceId = (int)$pdo->lastInsertId();

    // Handle services or line items
    if (is_array($services) && count($services) > 0) {
        // Create invoice items from selected services
        $itemStmt = $pdo->prepare('INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)');
        foreach ($services as $service) {
            $serviceName = $service['service_name'] ?? 'Service';
            $servicePrice = isset($service['price']) ? (float)$service['price'] : 0.0;
            $itemStmt->execute([$invoiceId, $serviceName, 1.0, $servicePrice, $servicePrice]);
        }
    } elseif (is_array($items) && count($items) > 0) {
        // Legacy line items support
        $itemStmt = $pdo->prepare('INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)');
        foreach ($items as $item) {
            $itemDesc = $item['description'] ?? '';
            $qty = isset($item['quantity']) ? (float)$item['quantity'] : 1.0;
            $unit = isset($item['unit_price']) ? (float)$item['unit_price'] : 0.0;
            $total = $qty * $unit;
            $itemStmt->execute([$invoiceId, $itemDesc, $qty, $unit, $total]);
        }
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'invoice_id' => $invoiceId, 'invoice_number' => $invoiceNumber]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
