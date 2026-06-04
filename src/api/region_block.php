<?php
/**
 * Region Block Management API (Database-based)
 * 
 * Endpoints:
 *   GET  /api/region_block.php?action=status&country=US - Check if region is blocked
 *   GET  /api/region_block.php?action=list               - List all blocked regions (admin)
 *   POST /api/region_block.php                            - Block/unblock regions (admin)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(json_encode(['success' => true]));
}

// Include database connection
require_once __DIR__ . '/db.php';

// Admin secret key - CHANGE THIS in production!
$ADMIN_KEY = getenv('SCL_ADMIN_KEY') ?: 'scl_admin_secret_key_2024';

// Country code to name mapping
$countryNames = [
    'ZA' => 'South Africa',
    'US' => 'United States',
    'GB' => 'United Kingdom',
    'NG' => 'Nigeria',
    'KE' => 'Kenya',
    'FR' => 'France',
    'DE' => 'Germany',
    'OTHER' => 'Other Regions',
    'CN' => 'China',
    'RU' => 'Russia',
    'KR' => 'South Korea',
    'JP' => 'Japan',
    'IN' => 'India',
    'BR' => 'Brazil',
    'MX' => 'Mexico',
    'AU' => 'Australia',
    'CA' => 'Canada',
    'ES' => 'Spain',
    'IT' => 'Italy',
    'NL' => 'Netherlands',
];

// Response helper
function jsonResponse($success, $message, $data = null) {
    $response = ['success' => $success, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

// Get action from request
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$country = $_GET['country'] ?? $_POST['country'] ?? '';
$adminKey = $_GET['admin_key'] ?? $_POST['admin_key'] ?? '';

// Handle different actions
switch ($action) {
    
    // Check if a specific region is blocked
    case 'status':
        if (empty($country)) {
            jsonResponse(false, 'Country code required');
        }
        
        $stmt = $pdo->prepare("SELECT * FROM blocked_regions WHERE country_code = ?");
        $stmt->execute([strtoupper($country)]);
        $blocked = $stmt->fetch();
        
        if ($blocked) {
            jsonResponse(true, 'Region is blocked', [
                'blocked' => true,
                'country_code' => $blocked['country_code'],
                'country_name' => $blocked['country_name'],
                'blocked_at' => $blocked['blocked_at'],
                'reason' => $blocked['reason']
            ]);
        } else {
            jsonResponse(true, 'Region is allowed', [
                'blocked' => false,
                'country_code' => strtoupper($country)
            ]);
        }
        break;
    
    // List all blocked regions (admin only)
    case 'list':
        $stmt = $pdo->query("SELECT * FROM blocked_regions ORDER BY blocked_at DESC");
        $blocked = $stmt->fetchAll();
        
        jsonResponse(true, 'Blocked regions retrieved', [
            'blocked_regions' => $blocked,
            'total' => count($blocked)
        ]);
        break;
    
    // Block a region (admin only)
    case 'block':
        if ($adminKey !== $ADMIN_KEY) {
            jsonResponse(false, 'Unauthorized - invalid admin key');
        }
        
        if (empty($country)) {
            jsonResponse(false, 'Country code required');
        }
        
        $countryCode = strtoupper($country);
        $countryName = $countryNames[$countryCode] ?? $_POST['country_name'] ?? 'Unknown';
        $reason = $_POST['reason'] ?? 'Manually blocked by admin';
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO blocked_regions (country_code, country_name, reason) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE reason = VALUES(reason), blocked_at = CURRENT_TIMESTAMP
            ");
            $stmt->execute([$countryCode, $countryName, $reason]);
            
            jsonResponse(true, "Region {$countryCode} ({$countryName}) has been blocked");
        } catch (Exception $e) {
            jsonResponse(false, 'Failed to block region: ' . $e->getMessage());
        }
        break;
    
    // Unblock a region (admin only)
    case 'unblock':
        if ($adminKey !== $ADMIN_KEY) {
            jsonResponse(false, 'Unauthorized - invalid admin key');
        }
        
        if (empty($country)) {
            jsonResponse(false, 'Country code required');
        }
        
        $countryCode = strtoupper($country);
        
        try {
            $stmt = $pdo->prepare("DELETE FROM blocked_regions WHERE country_code = ?");
            $stmt->execute([$countryCode]);
            
            if ($stmt->rowCount() > 0) {
                jsonResponse(true, "Region {$countryCode} has been unblocked");
            } else {
                jsonResponse(false, "Region {$countryCode} was not blocked");
            }
        } catch (Exception $e) {
            jsonResponse(false, 'Failed to unblock region: ' . $e->getMessage());
        }
        break;
    
    // Get all available regions
    case 'regions':
        jsonResponse(true, 'Available regions', [
            'regions' => $countryNames
        ]);
        break;
    
    default:
        jsonResponse(false, 'Invalid action. Use: status, list, block, unblock, or regions');
}