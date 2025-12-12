<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'db.php';

// Create banners table if it doesn't exist
function createBannersTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        subtitle VARCHAR(255),
        description TEXT,
        image_url VARCHAR(500),
        button_text VARCHAR(100),
        button_link VARCHAR(500),
        is_active BOOLEAN DEFAULT TRUE,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Initialize table
createBannersTable($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            // Get all banners
            $stmt = $pdo->prepare("SELECT * FROM banners ORDER BY sort_order ASC, created_at DESC");
            $stmt->execute();
            $banners = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $banners
            ]);
            break;
            
        case 'POST':
            // Create new banner
            $title = $input['title'] ?? '';
            $subtitle = $input['subtitle'] ?? '';
            $description = $input['description'] ?? '';
            $image_url = $input['image_url'] ?? '';
            $button_text = $input['button_text'] ?? '';
            $button_link = $input['button_link'] ?? '';
            $is_active = $input['is_active'] ?? true;
            $sort_order = $input['sort_order'] ?? 0;
            
            if (empty($title)) {
                throw new Exception('Title is required');
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO banners (title, subtitle, description, image_url, button_text, button_link, is_active, sort_order)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$title, $subtitle, $description, $image_url, $button_text, $button_link, $is_active, $sort_order]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Banner created successfully',
                'id' => $pdo->lastInsertId()
            ]);
            break;
            
        case 'PUT':
            // Update banner
            $id = $input['id'] ?? null;
            if (!$id) {
                throw new Exception('Banner ID is required');
            }
            
            $title = $input['title'] ?? '';
            $subtitle = $input['subtitle'] ?? '';
            $description = $input['description'] ?? '';
            $image_url = $input['image_url'] ?? '';
            $button_text = $input['button_text'] ?? '';
            $button_link = $input['button_link'] ?? '';
            $is_active = $input['is_active'] ?? true;
            $sort_order = $input['sort_order'] ?? 0;
            
            if (empty($title)) {
                throw new Exception('Title is required');
            }
            
            $stmt = $pdo->prepare("
                UPDATE banners 
                SET title = ?, subtitle = ?, description = ?, image_url = ?, 
                    button_text = ?, button_link = ?, is_active = ?, sort_order = ?
                WHERE id = ?
            ");
            
            $stmt->execute([$title, $subtitle, $description, $image_url, $button_text, $button_link, $is_active, $sort_order, $id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Banner updated successfully'
            ]);
            break;
            
        case 'DELETE':
            // Delete banner
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('Banner ID is required');
            }
            
            $stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Banner deleted successfully'
            ]);
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
