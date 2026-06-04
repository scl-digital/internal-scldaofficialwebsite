<?php
header('Content-Type: application/json');
require_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // List all invoices with client info
        $stmt = $pdo->query("SELECT i.id, i.invoice_number, i.client_id, c.name AS client_name, c.email AS client_email, i.amount, i.tax_amount, i.total_amount, i.status, i.due_date, i.issue_date FROM invoices i JOIN clients c ON c.id = i.client_id ORDER BY i.issue_date DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) { echo json_encode(['success' => false, 'error' => 'Invoice ID required']); exit; }
        $fields = [];
        $params = [];
        foreach (['amount','tax_amount','total_amount','status','due_date','issue_date','description'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (!$fields) { echo json_encode(['success' => false, 'error' => 'No fields to update']); exit; }
        $params[] = $data['id'];
        $sql = 'UPDATE invoices SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) { echo json_encode(['success' => false, 'error' => 'Invoice ID required']); exit; }
        
        // Delete invoice items first (due to foreign key constraints)
        $itemStmt = $pdo->prepare('DELETE FROM invoice_items WHERE invoice_id = ?');
        $itemStmt->execute([$data['id']]);
        
        // Then delete the invoice
        $stmt = $pdo->prepare('DELETE FROM invoices WHERE id = ?');
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
