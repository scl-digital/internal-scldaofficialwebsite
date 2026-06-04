<?php
/**
 * Project Management Database Setup
 * Comprehensive schema for projects, tasks, milestones, and activity logs
 * Handles database creation and initialization with proper error handling
 */
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

$response = [
    'success' => false,
    'message' => '',
    'tables_created' => [],
    'tables_existed' => [],
    'errors' => []
];

try {
    // 1. Create Clients table (if not exists)
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS clients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            phone VARCHAR(20),
            company VARCHAR(255),
            address TEXT,
            city VARCHAR(100),
            postal_code VARCHAR(20),
            country VARCHAR(100),
            status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_status (status)
        )");
        $response['tables_created'][] = 'clients';
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            throw $e;
        }
        $response['tables_existed'][] = 'clients';
    }

    // 2. Create Projects table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            client_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT,
            status ENUM('pending', 'in_progress', 'review', 'completed', 'cancelled', 'on_hold') DEFAULT 'pending',
            progress INT DEFAULT 0 CHECK (progress >= 0 AND progress <= 100),
            amount DECIMAL(12, 2) DEFAULT 0,
            start_date DATE,
            deadline DATE,
            completed_date DATE NULL,
            notes LONGTEXT,
            attachments JSON DEFAULT '[]',
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
            INDEX idx_client_id (client_id),
            INDEX idx_status (status),
            INDEX idx_deadline (deadline),
            INDEX idx_created_at (created_at)
        )");
        $response['tables_created'][] = 'projects';
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            throw $e;
        }
        $response['tables_existed'][] = 'projects';
    }

    // 3. Create Milestones table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS milestones (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT,
            status ENUM('pending', 'in_progress', 'completed', 'overdue', 'on_hold') DEFAULT 'pending',
            progress INT DEFAULT 0 CHECK (progress >= 0 AND progress <= 100),
            due_date DATE,
            completed_date DATE NULL,
            order_index INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            INDEX idx_project_id (project_id),
            INDEX idx_status (status),
            INDEX idx_due_date (due_date)
        )");
        $response['tables_created'][] = 'milestones';
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            throw $e;
        }
        $response['tables_existed'][] = 'milestones';
    }

    // 4. Create Tasks table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            milestone_id INT NOT NULL,
            project_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description LONGTEXT,
            status ENUM('pending', 'in_progress', 'completed', 'overdue', 'on_hold', 'cancelled') DEFAULT 'pending',
            priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
            progress INT DEFAULT 0 CHECK (progress >= 0 AND progress <= 100),
            assigned_to INT,
            due_date DATE,
            completed_date DATE NULL,
            order_index INT DEFAULT 0,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (milestone_id) REFERENCES milestones(id) ON DELETE CASCADE,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            INDEX idx_milestone_id (milestone_id),
            INDEX idx_project_id (project_id),
            INDEX idx_status (status),
            INDEX idx_priority (priority),
            INDEX idx_due_date (due_date)
        )");
        $response['tables_created'][] = 'tasks';
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            throw $e;
        }
        $response['tables_existed'][] = 'tasks';
    }

    // 5. Create Activity Log table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS activity_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT,
            milestone_id INT,
            task_id INT,
            entity_type ENUM('project', 'milestone', 'task') NOT NULL,
            action VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            old_value LONGTEXT,
            new_value LONGTEXT,
            user_type ENUM('admin', 'client', 'staff', 'system') DEFAULT 'system',
            user_id INT,
            user_name VARCHAR(255),
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (milestone_id) REFERENCES milestones(id) ON DELETE SET NULL,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE SET NULL,
            INDEX idx_project_id (project_id),
            INDEX idx_entity (entity_type, entity_type),
            INDEX idx_action (action),
            INDEX idx_created_at (created_at)
        )");
        $response['tables_created'][] = 'activity_log';
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            throw $e;
        }
        $response['tables_existed'][] = 'activity_log';
    }

    // 6. Create Attachments table
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS attachments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            project_id INT,
            task_id INT,
            file_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL UNIQUE,
            file_size INT,
            file_type VARCHAR(50),
            uploaded_by INT,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
            FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
            INDEX idx_project_id (project_id),
            INDEX idx_task_id (task_id)
        )");
        $response['tables_created'][] = 'attachments';
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'already exists') === false) {
            throw $e;
        }
        $response['tables_existed'][] = 'attachments';
    }

    $response['success'] = true;
    $response['message'] = 'Database setup completed successfully!';
    
} catch (Exception $e) {
    http_response_code(500);
    $response['success'] = false;
    $response['message'] = 'Database setup failed';
    $response['errors'][] = $e->getMessage();
}

echo json_encode($response);
?>
