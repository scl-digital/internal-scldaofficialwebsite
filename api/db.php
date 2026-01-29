<?php
// Database connection with environment overrides and safe fallbacks for XAMPP

$host = getenv('SCL_DB_HOST') ?: '127.0.0.1';
$db   = getenv('SCL_DB_NAME') ?: 'u261025466_Website';
$user = getenv('SCL_DB_USER') ?: 'u261025466_SCLDAWebsite';
$pass = getenv('SCL_DB_PASS') ?: 'SCLDAWebsite@25';
$charset = 'utf8mb4';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

function scl_attempt_pdo($host, $db, $user, $pass, $charset, $options) {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        return null;
    }
}

$attempts = [];

// Attempt 1: XAMPP default root/no password with same DB (local dev first)
$attempts[] = [$host, $db, 'root', ''];

// Attempt 2: XAMPP default root/no password with common local DB name
if ($db !== 'clientportal') {
    $attempts[] = [$host, 'clientportal', 'root', ''];
}

// Attempt 3: Provided/env credentials (production)
if (!($user === 'root' && $pass === '')) {
    $attempts[] = [$host, $db, $user, $pass];
}

$pdo = null;
$tried = [];
foreach ($attempts as [$h, $d, $u, $p]) {
    $pdo = scl_attempt_pdo($h, $d, $u, $p, $charset, $options);
    $tried[] = [
        'host' => $h,
        'db' => $d,
        'user' => $u,
    ];
    if ($pdo) {
        // Connected successfully; stop trying further
        break;
    }
}

if (!$pdo) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed for all attempts.',
        'attempts' => $tried,
        'hint' => 'Update api/db.php env vars (SCL_DB_HOST, SCL_DB_NAME, SCL_DB_USER, SCL_DB_PASS) or create the database and ensure credentials are correct. On XAMPP, you can try root with no password and database "clientportal".'
    ]);
    exit;
}
?>