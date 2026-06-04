<?php
/**
 * Test region_block.php directly
 */

header('Content-Type: text/plain');

echo "Testing region_block.php...\n\n";

// Include db.php first
require_once __DIR__ . '/db.php';

echo "After including db.php:\n";
echo "PDO exists: " . (isset($pdo) ? "YES" : "NO") . "\n";

if ($pdo) {
    echo "Testing query...\n";
    try {
        $stmt = $pdo->query("SELECT * FROM blocked_regions");
        $rows = $stmt->fetchAll();
        echo "Query success! Rows: " . count($rows) . "\n";
    } catch (Exception $e) {
        echo "Query error: " . $e->getMessage() . "\n";
    }
} else {
    echo "PDO is null!\n";
}