<?php
$url = 'http://localhost/internal-scldaofficialwebsite/api/login.php';
$data = [
    'email' => '+27631250268',
    'password' => 'SC0808@lda',
    'user_type' => 'client'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
