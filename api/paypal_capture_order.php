<?php
// Ensure API returns JSON on errors instead of HTML (PHP notices/fatals)
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ob_start();
header('Content-Type: application/json');

set_exception_handler(function($e){
    if (!headers_sent()) header('Content-Type: application/json');
    http_response_code(500);
    error_log('Uncaught exception: ' . $e->getMessage() . " | " . $e->getTraceAsString());
    try { @file_put_contents(__DIR__ . '/logs/api_errors.log', json_encode(['ts'=>date('c'),'type'=>'exception','message'=>$e->getMessage(),'trace'=>$e->getTraceAsString()]) . PHP_EOL, FILE_APPEND | LOCK_EX); } catch (Exception $ex) {}
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    @ob_end_flush();
    exit;
});

set_error_handler(function($errno, $errstr, $errfile, $errline){
    // Ignore deprecation warnings (E_DEPRECATED = 8192) - they should not cause 500 errors
    if ($errno === E_DEPRECATED || $errno === 8192) {
        return true; // Continue execution without error
    }
    if (!headers_sent()) header('Content-Type: application/json');
    http_response_code(500);
    error_log("PHP error [$errno]: $errstr in $errfile:$errline");
    try { @file_put_contents(__DIR__ . '/logs/api_errors.log', json_encode(['ts'=>date('c'),'type'=>'php_error','errno'=>$errno,'error'=>$errstr,'file'=>$errfile,'line'=>$errline]) . PHP_EOL, FILE_APPEND | LOCK_EX); } catch (Exception $ex) {}
    echo json_encode(['success' => false, 'error' => $errstr]);
    @ob_end_flush();
    exit;
});

require_once __DIR__ . '/PayPalClient.php';
require_once __DIR__ . '/db.php';

// Lightweight request logging to help diagnose 500 errors (no secrets)
try {
    $logDir = __DIR__ . '/logs';
    if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
    $logFile = $logDir . '/api_errors.log';
    $entry = [
        'ts' => date('c'),
        'uri' => $_SERVER['REQUEST_URI'] ?? '',
        'method' => $_SERVER['REQUEST_METHOD'] ?? '',
        'remote' => $_SERVER['REMOTE_ADDR'] ?? '',
        'body' => substr(file_get_contents('php://input'),0,10000)
    ];
    @file_put_contents($logFile, json_encode($entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
} catch (Exception $e) {
    // ignore logging errors
}

use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $orderId = $data['orderID'] ?? null;
    if (!$orderId) {
        echo json_encode(['success' => false, 'error' => 'orderID required']);
        exit;
    }

    $client = PayPalClient::client();
    $request = new OrdersCaptureRequest($orderId);
    $request->prefer('return=representation');

    $response = $client->execute($request);

    // Basic verification: ensure capture completed and amounts/currency present
    $result = $response->result;
    $status = $result->status ?? null;

    // Try to find captured amount and currency in response
    $captureAmount = null;
    $captureCurrency = null;
    if (!empty($result->purchase_units) && is_array($result->purchase_units)) {
        $pu = $result->purchase_units[0];
        if (!empty($pu->payments) && !empty($pu->payments->captures) && is_array($pu->payments->captures)) {
            $cap = $pu->payments->captures[0];
            if (!empty($cap->amount)) {
                $captureAmount = $cap->amount->value ?? null;
                $captureCurrency = $cap->amount->currency_code ?? null;
            }
        }
    }

    if (strtoupper($status) !== 'COMPLETED') {
        echo json_encode(['success' => false, 'error' => 'Payment not completed', 'status' => $status, 'result' => $result]);
        exit;
    }

    if (!$captureAmount || !$captureCurrency) {
        // As a fallback, attempt to GET the order details
        try {
            $getReq = new OrdersGetRequest($orderId);
            $getResp = $client->execute($getReq);
            $getResult = $getResp->result;
            if (!empty($getResult->purchase_units) && is_array($getResult->purchase_units)) {
                $pu2 = $getResult->purchase_units[0];
                if (!empty($pu2->payments) && !empty($pu2->payments->captures) && is_array($pu2->payments->captures)) {
                    $cap2 = $pu2->payments->captures[0];
                    $captureAmount = $cap2->amount->value ?? $captureAmount;
                    $captureCurrency = $cap2->amount->currency_code ?? $captureCurrency;
                }
            }
        } catch (Exception $e) {
            // ignore, will validate below
        }
    }

    // Basic sanity checks: must have positive amount and currency
    if (!$captureAmount || floatval($captureAmount) <= 0 || !$captureCurrency) {
        echo json_encode(['success' => false, 'error' => 'Unable to verify captured payment amount/currency', 'details' => ['amount' => $captureAmount, 'currency' => $captureCurrency]]);
        exit;
    }

    // At this point payment looks valid; you should persist booking/order here
    // Persist a simple orders record
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS paypal_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            paypal_order_id VARCHAR(255) NOT NULL,
            capture_id VARCHAR(255) DEFAULT NULL,
            amount DECIMAL(18,2) DEFAULT NULL,
            currency VARCHAR(10) DEFAULT NULL,
            status VARCHAR(50) DEFAULT NULL,
            payer_email VARCHAR(200) DEFAULT NULL,
            raw_response LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // attempt to extract capture id and payer email
        $captureId = null;
        if (!empty($result->purchase_units[0]->payments->captures[0]->id)) {
            $captureId = $result->purchase_units[0]->payments->captures[0]->id;
        }
        $payerEmail = null;
        if (!empty($result->payer->email_address)) {
            $payerEmail = $result->payer->email_address;
        }

        $insert = $pdo->prepare("INSERT INTO paypal_orders (paypal_order_id, capture_id, amount, currency, status, payer_email, raw_response) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([
            $orderId,
            $captureId,
            $captureAmount,
            $captureCurrency,
            $status,
            $payerEmail,
            json_encode($result)
        ]);
    } catch (Exception $e) {
        // Log but don't fail the response
        error_log('Failed to persist paypal_orders: ' . $e->getMessage());
    }

    @ob_end_clean();
    echo json_encode(['success' => true, 'result' => $result, 'captured' => ['amount' => $captureAmount, 'currency' => $captureCurrency]]);
} catch (Exception $e) {
    http_response_code(500);
    @ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

?>
