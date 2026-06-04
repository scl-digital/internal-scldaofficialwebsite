<?php
/**
 * Simple test to check database connection
 */

header('Content-Type: text/plain');

require_once __DIR__ . '/db.php';

if (isset($pdo)) {
    echo "Connected! PDO object exists.\n";
    
    // Try a simple query
    try {
        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "Query works! Result: " . print_r($result, true) . "\n";
    } catch (Exception $e) {
        echo "Query failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "Failed to connect to database.\n";
    
    // Check what was tried
    global $attempts, $tried;
    echo "\nAttempts made:\n";
    print_r($tried);
}