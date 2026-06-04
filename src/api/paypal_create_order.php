<?php
// Ensure we never output HTML errors from API endpoints — always return JSON
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
ob_start();
header('Content-Type: application/json');

set_exception_handler(function($e){
    if (!headers_sent()) header('Content-Type: application/json');
    http_response_code(500);
    error_log('Uncaught exception: ' . $e->getMessage() . " | " . $e->getTraceAsString());
    // also write to local api log for easier debugging
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
    // also write to local api log for easier debugging
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

use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $amount = $data['amount'] ?? null;
    $currency = $data['currency'] ?? 'ZAR';

    if (!$amount) {
        echo json_encode(['success' => false, 'error' => 'Amount required']);
        exit;
    }

    $client = PayPalClient::client();
    $request = new OrdersCreateRequest();
    $request->prefer('return=representation');

    // Build purchase unit with optional items list
    $purchaseUnit = [
        'amount' => [
            'currency_code' => $currency,
            'value' => number_format((float)$amount, 2, '.', '')
        ]
    ];

    if (!empty($data['items']) && is_array($data['items'])) {
        $items = [];
        $sum = 0.0;
        foreach ($data['items'] as $it) {
            $price = floatval($it['unit_amount']['value'] ?? ($it['unit_amount'] ?? 0));
            $qty = intval($it['quantity'] ?? 1);
            $items[] = [
                'name' => substr($it['name'] ?? 'Item', 0, 127),
                'unit_amount' => [ 'currency_code' => $currency, 'value' => number_format($price, 2, '.', '') ],
                'quantity' => (string)$qty
            ];
            $sum += $price * $qty;
        }
        // If sum differs from provided amount, include breakdown
        $purchaseUnit['items'] = $items;
        $purchaseUnit['amount']['breakdown'] = [
            'item_total' => [ 'currency_code' => $currency, 'value' => number_format($sum, 2, '.', '') ]
        ];
        // Keep total value from client but it's recommended they match
        $purchaseUnit['amount']['value'] = number_format((float)$amount, 2, '.', '');
    }

    $request->body = [
        'intent' => 'CAPTURE',
        'purchase_units' => [$purchaseUnit],
        'application_context' => [
            'return_url' => (isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'/').'/services-platform/checkout.html',
            'cancel_url' => (isset($_SERVER['HTTP_ORIGIN'])?$_SERVER['HTTP_ORIGIN']:'/').'/services-platform/checkout.html'
        ]
    ];

    $response = $client->execute($request);
    $order = $response->result;
    // find approval link
    $approve = null;
    foreach ($order->links as $link) {
        if ($link->rel === 'approve') { $approve = $link->href; break; }
    }

    @ob_end_clean();
    echo json_encode(['success' => true, 'order' => $order, 'approve_url' => $approve]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('PayPal create_order error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
    @ob_end_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage(), 'debug' => $e->getTraceAsString()]);
}

?>
