<?php
/**
 * Test script to block a region for testing purposes
 */

require_once 'api/db.php';

// Block South Africa for testing
$countryCode = 'ZA';
$countryName = 'South Africa';
$reason = 'Testing region blocking functionality';

try {
    $stmt = $pdo->prepare("
        INSERT INTO blocked_regions (country_code, country_name, reason) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE reason = VALUES(reason), blocked_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$countryCode, $countryName, $reason]);
    
    echo "✓ South Africa (ZA) has been blocked for testing\n";
    echo "✓ You can now test the blocking by selecting South Africa on the main page\n";
    
    // Verify it's blocked
    $checkStmt = $pdo->prepare("SELECT * FROM blocked_regions WHERE country_code = ?");
    $checkStmt->execute([$countryCode]);
    $blocked = $checkStmt->fetch();
    
    if ($blocked) {
        echo "✓ Verification: Region is successfully blocked\n";
        echo "  - Country: " . $blocked['country_name'] . "\n";
        echo "  - Reason: " . $blocked['reason'] . "\n";
        echo "  - Blocked at: " . $blocked['blocked_at'] . "\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

echo "\nTo test:\n";
echo "1. Go to: http://localhost/internal-scldaofficialwebsite/\n";
echo "2. Select '🇿🇦 South Africa' from the region dropdown\n";
echo "3. Click 'INITIATE ACCESS'\n";
echo "4. You should see 'ACCESS DENIED' message\n";
echo "\nTo unblock when done:\n";
echo "1. Go to: http://localhost/internal-scldaofficialwebsite/admin/region-block.html\n";
echo "2. Admin key: scl_admin_secret_key_2024\n";
echo "3. Click UNBLOCK next to South Africa\n";
?>
