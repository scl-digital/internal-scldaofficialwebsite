<?php
// Generate Event Ticket PDF with QR Code
function generateEventTicket($bookingId, $pdo) {
    try {
        // Get booking details
        $stmt = $pdo->prepare("
            SELECT eb.*, e.title as event_title, e.event_date, e.location 
            FROM event_bookings eb
            LEFT JOIN events e ON eb.event_id = e.id
            WHERE eb.id = ?
        ");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            return ['success' => false, 'error' => 'Booking not found'];
        }
        
        // Generate QR Code using Google Charts API
        $qrCodeUrl = 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . urlencode($booking['qr_code_data']);
        $qrCodeImage = @file_get_contents($qrCodeUrl);
        
        if ($qrCodeImage === false) {
            error_log("Failed to generate QR code from Google Charts API");
            $qrCodeImage = null;
        }
        
        // Create tickets directory if it doesn't exist
        $ticketsDir = __DIR__ . '/../tickets';
        if (!is_dir($ticketsDir)) {
            mkdir($ticketsDir, 0755, true);
        }
        
        // Generate PDF using TCPDF
        require_once(__DIR__ . '/../vendor/autoload.php');
        
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('SCL Digital Agency');
        $pdf->SetAuthor('SCL Digital Agency');
        $pdf->SetTitle('Event Ticket - ' . $booking['booking_reference']);
        $pdf->SetSubject('Event Ticket');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', '', 10);
        
        // Generate ticket HTML content
        $eventDate = !empty($booking['event_date']) ? date('l, F j, Y • g:i A', strtotime($booking['event_date'])) : 'TBA';
        
        $html = generateTicketHTML($booking, $qrCodeImage, $eventDate);
        
        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Save PDF
        $filename = 'ticket_' . $booking['booking_reference'] . '.pdf';
        $filepath = $ticketsDir . '/' . $filename;
        $pdf->Output($filepath, 'F');
        
        // Update booking with ticket path
        $updateStmt = $pdo->prepare("UPDATE event_bookings SET ticket_pdf_path = ? WHERE id = ?");
        $updateStmt->execute([$filepath, $bookingId]);
        
        return [
            'success' => true,
            'ticket_path' => $filepath,
            'ticket_url' => '/tickets/' . $filename
        ];
        
    } catch (Exception $e) {
        error_log('Ticket generation error: ' . $e->getMessage());
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function generateTicketHTML($booking, $qrCodeImage, $eventDate) {
    // Save QR code as temp file for PDF
    $qrCodePath = '';
    if ($qrCodeImage) {
        $tempDir = sys_get_temp_dir();
        $qrCodePath = $tempDir . '/qr_' . $booking['booking_reference'] . '.png';
        file_put_contents($qrCodePath, $qrCodeImage);
    }
    
    $html = '
    <style>
        .ticket-wrapper {
            border: 3px solid #1e348d;
            border-radius: 10px;
            overflow: hidden;
        }
        .ticket-header {
            background-color: #1e348d;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .ticket-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .ticket-subtitle {
            font-size: 12px;
        }
        .ticket-body {
            padding: 20px;
        }
        .admit-badge {
            background-color: #30adbe;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 10px;
        }
        .detail-row {
            border-bottom: 1px solid #eee;
            padding: 8px 0;
        }
        .detail-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #888;
            font-weight: bold;
        }
        .detail-value {
            font-size: 11px;
            color: #333;
            font-weight: normal;
        }
        .booking-ref-section {
            background-color: #1e348d;
            color: white;
            padding: 15px;
            text-align: center;
            margin: 15px 0;
            border-radius: 5px;
        }
        .booking-ref-label {
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .booking-ref-value {
            font-size: 20px;
            font-weight: bold;
            font-family: courier;
            letter-spacing: 2px;
        }
        .qr-section {
            text-align: center;
            margin: 15px 0;
        }
        .ticket-footer {
            background-color: #f9f9f9;
            padding: 15px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 2px solid #eee;
        }
        table {
            width: 100%;
        }
    </style>
    
    <div class="ticket-wrapper">
        <div class="ticket-header">
            <div class="ticket-title">' . htmlspecialchars($booking['event_title'] ?? 'Event Ticket') . '</div>
            <div class="ticket-subtitle">Admit One • ' . $eventDate . '</div>
        </div>
        
        <div class="ticket-body">
            <div class="admit-badge">✓ ADMIT ONE</div>
            
            <table cellpadding="5">
                <tr>
                    <td class="detail-label">ATTENDEE</td>
                    <td class="detail-value">' . htmlspecialchars($booking['customer_name']) . '</td>
                </tr>
                <tr>
                    <td class="detail-label">EMAIL</td>
                    <td class="detail-value">' . htmlspecialchars($booking['customer_email']) . '</td>
                </tr>';
    
    if (!empty($booking['organization'])) {
        $html .= '
                <tr>
                    <td class="detail-label">ORGANIZATION</td>
                    <td class="detail-value">' . htmlspecialchars($booking['organization']) . '</td>
                </tr>';
    }
    
    $html .= '
                <tr>
                    <td class="detail-label">TICKET TYPE</td>
                    <td class="detail-value">General Admission × ' . htmlspecialchars($booking['ticket_count']) . '</td>
                </tr>
                <tr>
                    <td class="detail-label">EVENT DATE</td>
                    <td class="detail-value">' . $eventDate . '</td>
                </tr>
                <tr>
                    <td class="detail-label">VENUE</td>
                    <td class="detail-value">' . htmlspecialchars($booking['location'] ?? 'TBA') . '</td>
                </tr>
                <tr>
                    <td class="detail-label">AMOUNT PAID</td>
                    <td class="detail-value">' . htmlspecialchars($booking['currency']) . ' ' . number_format($booking['total_amount'], 2) . '</td>
                </tr>
            </table>
            
            <div class="booking-ref-section">
                <div class="booking-ref-label">Booking Reference</div>
                <div class="booking-ref-value">' . htmlspecialchars($booking['booking_reference']) . '</div>
            </div>';
    
    if ($qrCodePath && file_exists($qrCodePath)) {
        $html .= '
            <div class="qr-section">
                <img src="' . $qrCodePath . '" width="150" height="150" />
                <br/><span style="font-size: 9px; color: #888;">Scan to Verify</span>
            </div>';
    }
    
    $html .= '
        </div>
        
        <div class="ticket-footer">
            <p><strong>Important:</strong> Please bring this ticket (printed or on mobile) to the event entrance.</p>
            <p>The QR code will be scanned for verification. Keep this ticket safe and do not share.</p>
            <p style="margin-top: 10px;">
                Issued by <strong>SCL Digital Agency</strong> • 
                Booking Date: ' . date('F j, Y', strtotime($booking['created_at'])) . ' • 
                Contact: info@scldigitalagency.com
            </p>
        </div>
    </div>';
    
    return $html;
}
?>
