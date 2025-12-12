<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Invalid request method']);
        exit;
    }

    if (empty($_FILES['proof'])) {
        echo json_encode(['success' => false, 'error' => 'No file uploaded (use field name \"proof\")']);
        exit;
    }

    $allowed = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
    $file = $_FILES['proof'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'error' => 'Upload error code: ' . $file['error']]);
        exit;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowed)) {
        echo json_encode(['success' => false, 'error' => 'File type not allowed']);
        exit;
    }

    $ext = '';
    switch ($mime) {
        case 'application/pdf': $ext = 'pdf'; break;
        case 'image/png': $ext = 'png'; break;
        case 'image/jpeg': $ext = 'jpg'; break;
        default: $ext = 'bin';
    }

    $uploadsDir = __DIR__ . '/../uploads/payment_proofs';
    if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);

    $basename = bin2hex(random_bytes(8));
    $filename = $basename . '.' . $ext;
    $target = $uploadsDir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file']);
        exit;
    }

    // Optionally record to payments table if order reference provided
    $order_ref = isset($_POST['order_ref']) ? trim($_POST['order_ref']) : null;
    $client_email = isset($_POST['email']) ? trim($_POST['email']) : null;
    if ($order_ref || $client_email) {
        try {
            $stmt = $pdo->prepare('INSERT INTO payment_proofs (order_ref, client_email, file_path, created_at) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$order_ref, $client_email, '/uploads/payment_proofs/' . $filename]);
        } catch (Exception $e) {
            // ignore if table doesn't exist; it's optional
        }
    }

    $url = dirname($_SERVER['SCRIPT_NAME']) . '/../uploads/payment_proofs/' . $filename;
    echo json_encode(['success' => true, 'file' => '/uploads/payment_proofs/' . $filename]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
