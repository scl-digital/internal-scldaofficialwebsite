<?php
/**
 * Setup script for blocked_regions table
 * Run this once to create the table in the database
 */

header('Content-Type: text/plain');

// Include database connection
require_once __DIR__ . '/db.php';

echo "Setting up blocked_regions table...\n";

// SQL to create the table
$sql = "
CREATE TABLE IF NOT EXISTS `blocked_regions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(2) NOT NULL COMMENT 'ISO 3166-1 alpha-2 country code',
  `country_name` varchar(100) NOT NULL COMMENT 'Full country name',
  `reason` text DEFAULT NULL COMMENT 'Reason for blocking (compliance, license, etc.)',
  `blocked_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'When the region was blocked',
  `blocked_by` varchar(50) DEFAULT NULL COMMENT 'Admin who blocked the region',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_country` (`country_code`),
  KEY `idx_blocked_at` (`blocked_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Regions blocked for compliance reasons'
";

try {
    $pdo->exec($sql);
    echo "✓ blocked_regions table created successfully!\n";
    
    // Test with a sample entry (optional - remove if not needed)
    $testSql = "SELECT COUNT(*) as count FROM blocked_regions";
    $stmt = $pdo->query($testSql);
    $result = $stmt->fetch();
    
    echo "✓ Current blocked regions count: " . $result['count'] . "\n";
    echo "✓ Table setup complete! The region blocking feature is now ready.\n";
    
} catch (PDOException $e) {
    echo "✗ Error creating table: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nYou can now use the region block manager at:\n";
echo "http://localhost/internal-scldaofficialwebsite/admin/region-block.html\n";
?>
