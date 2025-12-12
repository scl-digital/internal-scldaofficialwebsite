<?php
require_once __DIR__ . '/_json_api_bootstrap.php';
require_once 'db.php';

try {
    // Create milestones table
    $pdo->exec("CREATE TABLE IF NOT EXISTS milestones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        due_date DATE,
        status ENUM('pending', 'in_progress', 'completed', 'overdue') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        INDEX idx_project (project_id),
        INDEX idx_status (status)
    )");
    
    // Create tasks table
    $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        milestone_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        due_date DATE,
        status ENUM('pending', 'in_progress', 'completed', 'overdue') DEFAULT 'pending',
        priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (milestone_id) REFERENCES milestones(id) ON DELETE CASCADE,
        INDEX idx_milestone (milestone_id),
        INDEX idx_status (status),
        INDEX idx_priority (priority)
    )");
    
    // Create activity log table
    $pdo->exec("CREATE TABLE IF NOT EXISTS activity_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        user_type ENUM('client', 'staff') NOT NULL,
        user_id INT NOT NULL,
        action VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
        INDEX idx_project (project_id),
        INDEX idx_created (created_at)
    )");
    
    // Insert sample data for existing projects
    $stmt = $pdo->query("SELECT id FROM projects LIMIT 1");
    $project = $stmt->fetch();
    
    if ($project) {
        $project_id = $project['id'];
        
        // Insert sample milestones
        $milestones = [
            ['title' => 'Project Planning & Setup', 'due_date' => date('Y-m-d', strtotime('+1 week')), 'status' => 'completed'],
            ['title' => 'Design & Wireframes', 'due_date' => date('Y-m-d', strtotime('+2 weeks')), 'status' => 'in_progress'],
            ['title' => 'Development Phase', 'due_date' => date('Y-m-d', strtotime('+4 weeks')), 'status' => 'pending'],
            ['title' => 'Testing & Quality Assurance', 'due_date' => date('Y-m-d', strtotime('+6 weeks')), 'status' => 'pending'],
            ['title' => 'Launch & Deployment', 'due_date' => date('Y-m-d', strtotime('+8 weeks')), 'status' => 'pending']
        ];
        
        foreach ($milestones as $milestone) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO milestones (project_id, title, due_date, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$project_id, $milestone['title'], $milestone['due_date'], $milestone['status']]);
            $milestone_id = $pdo->lastInsertId();
            
            // Insert sample tasks for each milestone
            $tasks = [
                ['title' => 'Requirements gathering', 'status' => 'completed'],
                ['title' => 'Project timeline creation', 'status' => 'completed'],
                ['title' => 'Team assignment', 'status' => 'in_progress']
            ];
            
            foreach ($tasks as $task) {
                $stmt = $pdo->prepare("INSERT IGNORE INTO tasks (milestone_id, title, status) VALUES (?, ?, ?)");
                $stmt->execute([$milestone_id, $task['title'], $task['status']]);
            }
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Project tracker tables created successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
