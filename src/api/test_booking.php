<?php
// Test save_event_booking.php endpoint
header('Content-Type: text/plain');

echo "Testing save_event_booking.php...\n\n";

// Simulate POST data
$testData = [
    'event_id' => 1,
    'customer_name' => 'Test User',
    'customer_email' => 'test@example.com',
    'organization' => 'Test Org',
    'ticket_count' => 1,
    'total_amount' => '500.00',
    'currency' => 'ZAR',
    'paypal_order_id' => 'TEST-ORDER-123'
];

echo "Test data:\n";
print_r($testData);
echo "\n";

// Make request to save_event_booking.php
$ch = curl_init('http://localhost/mostrecent.softwarecreativelabs.com/api/save_event_booking.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response;
echo "\n\n";

// Try to decode JSON
$decoded = json_decode($response, true);
if ($decoded) {
    echo "JSON decoded successfully:\n";
    print_r($decoded);
} else {
    echo "JSON decode failed!\n";
    echo "JSON error: " . json_last_error_msg() . "\n";
}
?>
