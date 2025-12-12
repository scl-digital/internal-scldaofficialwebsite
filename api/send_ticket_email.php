<?php
// Send Event Ticket Email using PHPMailer
function sendTicketEmail($bookingId, $pdo) {
    try {
        // Get booking details
        $stmt = $pdo->prepare("
            SELECT eb.*, e.title as event_title 
            FROM event_bookings eb
            LEFT JOIN events e ON eb.event_id = e.id
            WHERE eb.id = ?
        ");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            return ['success' => false, 'error' => 'Booking not found'];
        }
        
        // Get email settings
        $emailSettings = $pdo->query("SELECT * FROM email_settings ORDER BY id DESC LIMIT 1")->fetch();

        
        if (!$emailSettings || empty($emailSettings['smtp_username'])) {
            error_log("Email settings not configured");
            return ['success' => false, 'error' => 'Email settings not configured'];
        }
        
        // Use PHP's built-in mail() function (PHPMailer not installed)
        return sendEmailFallback($booking, $emailSettings);
        
    } catch (Exception $e) {
        error_log('Email sending error: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function sendEmailFallback($booking, $emailSettings) {
    $to = $booking['customer_email'];
    $subject = 'Your Event Ticket - ' . ($booking['event_title'] ?? 'Event');
    $message = generateEmailText($booking);
    $headers = "From: " . $emailSettings['from_name'] . " <" . $emailSettings['from_email'] . ">\r\n";
    $headers .= "Reply-To: " . $emailSettings['from_email'] . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    if (mail($to, $subject, generateEmailHTML($booking), $headers)) {
        return ['success' => true, 'message' => 'Email sent via mail()'];
    } else {
        return ['success' => false, 'error' => 'Failed to send email'];
    }
}

function generateEmailHTML($booking) {
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1e348d 0%, #3641b5 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px; }
        .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .detail-label { font-weight: bold; color: #666; }
        .detail-value { color: #333; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 14px; }
        .button { display: inline-block; padding: 12px 30px; background: #30adbe; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Booking Confirmed!</h1>
            <p>Thank you for your purchase</p>
        </div>
        
        <div class="content">
            <p>Dear ' . htmlspecialchars($booking['customer_name']) . ',</p>
            
            <p>Your payment has been successfully processed and your event ticket has been confirmed!</p>
            
            <div class="booking-details">
                <h3 style="margin-top: 0; color: #1e348d;">Booking Details</h3>
                
                <div class="detail-row">
                    <span class="detail-label">Booking Reference:</span>
                    <span class="detail-value" style="font-weight: bold; color: #1e348d;">' . htmlspecialchars($booking['booking_reference']) . '</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Event:</span>
                    <span class="detail-value">' . htmlspecialchars($booking['event_title'] ?? 'Event') . '</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Number of Tickets:</span>
                    <span class="detail-value">' . htmlspecialchars($booking['ticket_count']) . '</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value" style="font-weight: bold; color: #30adbe;">' . htmlspecialchars($booking['currency']) . ' ' . number_format($booking['total_amount'], 2) . '</span>
                </div>
                
                <div class="detail-row" style="border-bottom: none;">
                    <span class="detail-label">Payment Status:</span>
                    <span class="detail-value" style="color: #28a745;">✓ Completed</span>
                </div>
            </div>
            
            <p><strong>Your ticket is attached to this email.</strong> Please download and save it. You can either print it or show it on your mobile device at the event entrance.</p>
            
            <p>The QR code on your ticket will be scanned for verification, so please ensure it\'s clearly visible.</p>
            
            <p>We look forward to seeing you at the event!</p>
            
            <p>If you have any questions, please don\'t hesitate to contact us.</p>
        </div>
        
        <div class="footer">
            <p><strong>SCL Digital Agency</strong></p>
            <p>Email: info@scldigitalagency.com | Website: www.scldigitalagency.com</p>
            <p style="font-size: 12px; margin-top: 15px;">This is an automated email. Please do not reply directly to this message.</p>
        </div>
    </div>
</body>
</html>';
}

function generateEmailText($booking) {
    return "Dear " . $booking['customer_name'] . ",\n\n" .
           "Your payment has been successfully processed and your event ticket has been confirmed!\n\n" .
           "Booking Details:\n" .
           "- Booking Reference: " . $booking['booking_reference'] . "\n" .
           "- Event: " . ($booking['event_title'] ?? 'Event') . "\n" .
           "- Number of Tickets: " . $booking['ticket_count'] . "\n" .
           "- Total Amount: " . $booking['currency'] . " " . number_format($booking['total_amount'], 2) . "\n" .
           "- Payment Status: Completed\n\n" .
           "Your ticket is attached to this email. Please download and save it.\n\n" .
           "We look forward to seeing you at the event!\n\n" .
           "Best regards,\n" .
           "SCL Digital Agency Team";
}
?>
