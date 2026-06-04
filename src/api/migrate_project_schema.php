<?php
/**
 * Database Schema Migration
 * Aligns existing projects table with new schema requirements
 */
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$result = [
    'success' => false,
    'message' => '',
    'actions' => [],
    'errors' => []
];

try {
    // Check current projects table structure
    $stmt = $pdo->query("DESCRIBE projects");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_map(fn($c) => $c['Field'], $columns);
    
    $result['current_schema'] = $columnNames;
    
    // Check which columns need to be added
    $requiredColumns = [
        'amount' => "DECIMAL(12, 2) DEFAULT 0",
        'notes' => "LONGTEXT",
        'attachments' => "JSON DEFAULT '[]'",
        'created_by' => "INT",
        'completed_date' => "DATE NULL",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    // Add missing columns
    foreach ($requiredColumns as $col => $definition) {
        if (!in_array($col, $columnNames)) {
            $sql = "ALTER TABLE projects ADD COLUMN $col $definition";
            try {
                $pdo->exec($sql);
                $result['actions'][] = "Added column: $col";
            } catch (Exception $e) {
                $result['errors'][] = "Failed to add $col: " . $e->getMessage();
            }
        }
    }
    
    // Migrate data from budget to amount if needed
    if (in_array('budget', $columnNames) && in_array('amount', $columnNames)) {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM projects WHERE amount = 0 AND budget > 0");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
        if ($count > 0) {
            $pdo->exec("UPDATE projects SET amount = budget WHERE amount = 0 AND budget > 0");
            $result['actions'][] = "Migrated $count records from budget to amount";
        }
    }
    
    // Verify all columns now exist
    $stmt = $pdo->query("DESCRIBE projects");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $columnNames = array_map(fn($c) => $c['Field'], $columns);
    
    $result['updated_schema'] = $columnNames;
    
    // Check if all required columns exist
    $requiredCols = ['id', 'client_id', 'title', 'description', 'status', 'progress', 'amount', 'start_date', 'deadline', 'completed_date', 'notes', 'attachments', 'created_at', 'updated_at'];
    $missing = array_diff($requiredCols, $columnNames);
    
    if (count($missing) > 0) {
        $result['errors'][] = "Missing columns: " . implode(', ', $missing);
    } else {
        $result['success'] = true;
        $result['message'] = 'Database schema migration completed successfully';
        
        // Get counts
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM projects");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        $result['project_count'] = $total;
    }
    
} catch (Exception $e) {
    $result['message'] = 'Migration failed: ' . $e->getMessage();
    $result['errors'][] = $e->getMessage();
}

echo json_encode($result, JSON_PRETTY_PRINT);
?>
