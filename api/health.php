<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
// Include db.php which will establish $pdo or emit an error JSON and exit on failure
require_once 'db.php';

// If we got here, DB connection succeeded
echo json_encode([
    'success' => true,
    'db_connected' => true,
]);


