<?php
// Download Event Ticket
header('Content-Type: text/html');

require_once __DIR__ . '/db.php';

try {
    $bookingRef = $_GET['ref'] ?? '';
    
    if (empty($bookingRef)) {
        die('Booking reference is required');
    }
    
    // Get booking details
    $stmt = $pdo->prepare("
        SELECT eb.*, e.title as event_title, e.event_date, e.location 
        FROM event_bookings eb
        LEFT JOIN events e ON eb.event_id = e.id
        WHERE eb.booking_reference = ?
    ");
    $stmt->execute([$bookingRef]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        die('Booking not found');
    }
    
    // Check if ticket file exists
    if (!empty($booking['ticket_pdf_path']) && file_exists($booking['ticket_pdf_path'])) {
        // Serve existing ticket
        $content = file_get_contents($booking['ticket_pdf_path']);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="ticket_' . $bookingRef . '.pdf"');
        echo $content;
    } else {
        // Generate ticket on the fly
        require_once __DIR__ . '/generate_event_ticket.php';
        $result = generateEventTicket($booking['id'], $pdo);
        
        if ($result['success'] && !empty($result['ticket_path']) && file_exists($result['ticket_path'])) {
            $content = file_get_contents($result['ticket_path']);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="ticket_' . $bookingRef . '.pdf"');
            echo $content;
        } else {
            die('Ticket not found. Please contact support.');
        }
    }
    
} catch (Exception $e) {
    error_log('Download ticket error: ' . $e->getMessage());
    die('Error downloading ticket: ' . $e->getMessage());
}
?>
