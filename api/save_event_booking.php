<?php
// Save Event Booking after successful PayPal payment
ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['event_id', 'customer_name', 'customer_email', 'ticket_count', 'total_amount', 'paypal_order_id'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
            exit;
        }
    }
    
    // Generate unique booking reference
    $bookingReference = 'EVT-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    
    // Check if booking reference already exists (very unlikely but check anyway)
    $checkStmt = $pdo->prepare("SELECT id FROM event_bookings WHERE booking_reference = ?");
    $checkStmt->execute([$bookingReference]);
    if ($checkStmt->fetch()) {
        // Generate new one
        $bookingReference = 'EVT-' . date('Y') . '-' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    // Prepare QR code data (booking reference)
    $qrCodeData = $bookingReference;
    
    // Insert booking into database
    $stmt = $pdo->prepare("
        INSERT INTO event_bookings 
        (event_id, booking_reference, customer_name, customer_email, organization, ticket_count, total_amount, currency, paypal_order_id, payment_status, qr_code_data) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?)
    ");
    
    $stmt->execute([
        $data['event_id'],
        $bookingReference,
        $data['customer_name'],
        $data['customer_email'],
        $data['organization'] ?? '',
        $data['ticket_count'],
        $data['total_amount'],
        $data['currency'] ?? 'ZAR',
        $data['paypal_order_id'],
        $qrCodeData
    ]);
    
    $bookingId = $pdo->lastInsertId();
    
    // Generate PDF ticket
    require_once __DIR__ . '/generate_event_ticket.php';
    $pdfResult = generateEventTicket($bookingId, $pdo);
    
    if (!$pdfResult['success']) {
        error_log("PDF generation failed: " . ($pdfResult['error'] ?? 'Unknown error'));
    }
    
    // Send email with ticket
    require_once __DIR__ . '/send_ticket_email.php';
    $emailResult = sendTicketEmail($bookingId, $pdo);
    
    if (!$emailResult['success']) {
        error_log("Email sending failed: " . ($emailResult['error'] ?? 'Unknown error'));
    }
    
    echo json_encode([
        'success' => true,
        'booking_id' => $bookingId,
        'booking_reference' => $bookingReference,
        'customer_email' => $data['customer_email'],
        'pdf_generated' => $pdfResult['success'] ?? false,
        'email_sent' => $emailResult['success'] ?? false
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    error_log('Save booking error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
