<?php
// Quick script to create database tables
require_once __DIR__ . '/db.php';

// Read and execute SQL schema
$sql = file_get_contents(__DIR__ . '/event_bookings_schema.sql');

// Split by semicolons and execute each statement
$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue;
    }
    
    try {
        if ($conn->query($statement)) {
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        } else {
            echo "✗ Error: " . $conn->error . "\n";
        }
    } catch (Exception $e) {
        echo "✗ Exception: " . $e->getMessage() . "\n";
    }
}

echo "\n✅ Database setup complete!\n";
?>
