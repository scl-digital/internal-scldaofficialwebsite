<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
try {
    if (!file_exists($autoloadPath)) {
        throw new Exception('Composer autoload file not found. Please install dependencies and verify vendor/autoload.php exists.');
    }
    require_once $autoloadPath;
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Composer autoload failure: ' . $e->getMessage()]);
    exit;
}

try {
    if (!class_exists(PHPMailer::class)) {
        echo json_encode(['success' => false, 'error' => 'PHPMailer is not available. Verify composer dependencies and PHP version.']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $required = ['client_name', 'client_email', 'proposal_title', 'proposal_number'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['success' => false, 'error' => "Missing required field: $field"]);
            exit;
        }
    }

    $clientName = trim($data['client_name']);
    $clientEmail = filter_var($data['client_email'], FILTER_VALIDATE_EMAIL);
    if (!$clientEmail) {
        echo json_encode(['success' => false, 'error' => 'Invalid client email address']);
        exit;
    }

    $proposalTitle = trim($data['proposal_title']);
    $proposalNumber = trim($data['proposal_number']);
    $companyName = trim($data['company_name'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $messageNote = trim($data['message'] ?? '');

    $settings = $pdo->query("SELECT * FROM email_settings ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$settings || empty($settings['smtp_host']) || empty($settings['smtp_port']) || empty($settings['smtp_username']) || empty($settings['smtp_password']) || empty($settings['from_email'])) {
        echo json_encode(['success' => false, 'error' => 'SMTP email settings are not configured correctly']);
        exit;
    }

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $settings['smtp_host'];
    $mail->Port = (int) $settings['smtp_port'];
    $mail->SMTPAuth = true;
    $mail->Username = $settings['smtp_username'];
    $mail->Password = $settings['smtp_password'];
    $mail->CharSet = 'UTF-8';

    $encryption = strtolower(trim($settings['smtp_encryption'] ?? ''));
    if ($encryption === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($encryption === 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mail->SMTPSecure = false;
        $mail->SMTPAutoTLS = false;
    }

    $mail->setFrom($settings['from_email'], $settings['from_name'] ?? 'SCL Agency');
    $mail->addAddress($clientEmail, $clientName);
    $mail->addReplyTo($settings['from_email'], $settings['from_name'] ?? 'SCL Agency');

    $mail->Subject = "Proposal {$proposalNumber}: {$proposalTitle}";
    $mail->isHTML(true);
    $mail->Body = generateProposalEmailHTML($clientName, $companyName, $phone, $proposalTitle, $proposalNumber, $messageNote);
    $mail->AltBody = generateProposalEmailText($clientName, $companyName, $phone, $proposalTitle, $proposalNumber, $messageNote);

    if ($mail->send()) {
        echo json_encode(['success' => true, 'message' => 'Proposal email sent successfully']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Email send failed: ' . $mail->ErrorInfo]);
    }

} catch (Exception $e) {
    error_log('Send proposal email error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function generateProposalEmailHTML($clientName, $companyName, $phone, $proposalTitle, $proposalNumber, $messageNote) {
    $companySection = $companyName ? '<div><strong>Company:</strong> ' . htmlspecialchars($companyName) . '</div>' : '';
    $phoneSection = $phone ? '<div><strong>Phone:</strong> ' . htmlspecialchars($phone) . '</div>' : '';
    $messageSection = $messageNote ? '<p>' . nl2br(htmlspecialchars($messageNote)) . '</p>' : '<p>Please review the proposal details below and let us know if you would like to move forward.</p>';

    return '<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { margin: 0; padding: 0; font-family: Arial, sans-serif; background: #f4f7fb; color: #20232a; }
.container { max-width: 680px; margin: 0 auto; padding: 24px; }
.header { padding: 28px 24px; background: linear-gradient(135deg,#0f172a 0%,#111827 100%); color: #fff; border-radius: 16px 16px 0 0; }
.header h1 { margin: 0; font-size: 26px; }
.content { background: #ffffff; padding: 24px; border-radius: 0 0 16px 16px; border: 1px solid #e5e7eb; }
.summary { margin: 20px 0; padding: 20px; background: #f8fafc; border-radius: 12px; border: 1px solid #e5e7eb; }
.summary div { margin-bottom: 10px; }
.button { display: inline-block; padding: 12px 24px; background: #2563eb; color: #ffffff; text-decoration: none; border-radius: 8px; }
.footer { margin-top: 24px; font-size: 14px; color: #6b7280; }
</style>
</head>
<body>
<div class="container">
<div class="header">
<h1>Proposal Ready for Review</h1>
<p style="margin-top:10px;font-size:15px;line-height:1.6;color:rgba(255,255,255,.8)">A new proposal has been prepared for you by SCL Agency.</p>
</div>
<div class="content">
<p>Hello ' . htmlspecialchars($clientName) . ',</p>
' . $messageSection . '
<div class="summary">
<div><strong>Proposal:</strong> ' . htmlspecialchars($proposalTitle) . '</div>
<div><strong>Proposal #:</strong> ' . htmlspecialchars($proposalNumber) . '</div>
' . $companySection . '
' . $phoneSection . '
<div><strong>Prepared by:</strong> SCL Agency</div>
</div>
<p>If you have any questions, simply reply to this email and we will get back to you shortly.</p>
<p>Thank you,<br>SCL Agency</p>
<div class="footer">
<p>SCL Agency<br>info@scldigitalagency.com</p>
<p>This is an automated message from SCL Agency.</p>
</div>
</div>
</div>
</body>
</html>';
}

function generateProposalEmailText($clientName, $companyName, $phone, $proposalTitle, $proposalNumber, $messageNote) {
    $companySection = $companyName ? "Company: $companyName\n" : '';
    $phoneSection = $phone ? "Phone: $phone\n" : '';
    $messageSection = $messageNote ? "$messageNote\n\n" : 'Please review the proposal details below and let us know if you would like to move forward.\n\n';

    return "Hello $clientName,\n\n" .
           $messageSection .
           "Proposal: $proposalTitle\n" .
           "Proposal #: $proposalNumber\n" .
           $companySection .
           $phoneSection .
           "Prepared by: SCL Agency\n\n" .
           "If you have any questions, simply reply to this email and we will get back to you shortly.\n\n" .
           "Thank you,\nSCL Agency\ninfo@scldigitalagency.com";
}
