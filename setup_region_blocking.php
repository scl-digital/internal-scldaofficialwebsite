<!DOCTYPE html>
<html>
<head>
    <title>Setup Region Blocking - SCL</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .container { background: #f5f5f5; padding: 20px; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .btn:hover { background: #005a87; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Region Blocking Setup</h1>
        <p>This will create the necessary database table for region blocking functionality.</p>
        
        <?php
        if (isset($_POST['setup'])) {
            try {
                require_once 'api/db.php';
                
                $sql = "CREATE TABLE IF NOT EXISTS `blocked_regions` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `country_code` varchar(2) NOT NULL COMMENT 'ISO 3166-1 alpha-2 country code',
                  `country_name` varchar(100) NOT NULL COMMENT 'Full country name',
                  `reason` text DEFAULT NULL COMMENT 'Reason for blocking (compliance, license, etc.)',
                  `blocked_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'When the region was blocked',
                  `blocked_by` varchar(50) DEFAULT NULL COMMENT 'Admin who blocked the region',
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `unique_country` (`country_code`),
                  KEY `idx_blocked_at` (`blocked_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Regions blocked for compliance reasons'";
                
                $pdo->exec($sql);
                
                // Test the table
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM blocked_regions");
                $result = $stmt->fetch();
                
                echo '<div class="success">';
                echo '<h3>✓ Setup Complete!</h3>';
                echo '<p>blocked_regions table created successfully.</p>';
                echo '<p>Current blocked regions: ' . $result['count'] . '</p>';
                echo '<p><a href="admin/region-block.html" style="color: #007cba;">Go to Region Block Manager</a></p>';
                echo '</div>';
                
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<h3>✗ Setup Failed</h3>';
                echo '<p>Error: ' . $e->getMessage() . '</p>';
                echo '</div>';
            }
        }
        ?>
        
        <?php if (!isset($_POST['setup'])): ?>
        <form method="post">
            <button type="submit" name="setup" class="btn">Create Database Table</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
