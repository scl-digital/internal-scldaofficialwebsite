<?php
header('Content-Type: application/json');
require_once '../db.php';

// Ensure events table and related tables exist (lightweight migration)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        client_id INT NULL,
        title VARCHAR(150) NOT NULL,
        event_date DATETIME NULL,
        location VARCHAR(255) NULL,
        status ENUM('draft','published','archived') DEFAULT 'draft',
        description TEXT NULL,
        event_type VARCHAR(100) NULL,
        image_url VARCHAR(500) NULL,
        pdf_url VARCHAR(500) NULL,
        video_url VARCHAR(500) NULL,
        agenda TEXT NULL,
        overview TEXT NULL,
        map_embed TEXT NULL,
        ad_left_url VARCHAR(500) NULL,
        ad_left_link VARCHAR(500) NULL,
        ad_right_url VARCHAR(500) NULL,
        ad_right_link VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_date (event_date),
        INDEX idx_status (status)
    )");

    // Backfill/migrate: add missing columns if the table existed before
    $columnsToEnsure = [
        'event_type' => "VARCHAR(100) NULL",
        'image_url' => "VARCHAR(500) NULL",
        'pdf_url' => "VARCHAR(500) NULL",
        'video_url' => "VARCHAR(500) NULL",
        'agenda' => "TEXT NULL",
        'overview' => "TEXT NULL",
        'map_embed' => "TEXT NULL",
        'ad_left_url' => "VARCHAR(500) NULL",
        'ad_left_link' => "VARCHAR(500) NULL",
        'ad_right_url' => "VARCHAR(500) NULL",
        'ad_right_link' => "VARCHAR(500) NULL",
    ];

    foreach ($columnsToEnsure as $colName => $colDef) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'events' AND COLUMN_NAME = ?");
        $stmt->execute([$colName]);
        $exists = (int)$stmt->fetchColumn() > 0;
        if (!$exists) {
            $pdo->exec("ALTER TABLE events ADD COLUMN $colName $colDef");
        }
    }

    // Speakers for events
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_speakers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        name VARCHAR(150) NOT NULL,
        position VARCHAR(200) NULL,
        image_url VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_event (event_id),
        CONSTRAINT fk_event_speakers_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    )");

    // Partners for events
    $pdo->exec("CREATE TABLE IF NOT EXISTS event_partners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_id INT NOT NULL,
        name VARCHAR(150) NOT NULL,
        image_url VARCHAR(500) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_event (event_id),
        CONSTRAINT fk_event_partners_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
    )");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to ensure events table: ' . $e->getMessage()]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // If id provided, return single with nested details
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $stmt = $pdo->prepare("SELECT e.id, e.client_id, c.name AS client_name, c.email AS client_email, e.title, e.event_date, e.location, e.status, e.description,
                                           e.event_type, e.image_url, e.pdf_url, e.video_url, e.agenda, e.overview, e.map_embed,
                                           e.ad_left_url, e.ad_left_link, e.ad_right_url, e.ad_right_link,
                                           e.created_at
                                    FROM events e
                                    LEFT JOIN clients c ON c.id = e.client_id
                                    WHERE e.id = ?");
            $stmt->execute([$id]);
            $event = $stmt->fetch();
            if (!$event) { echo json_encode(['success'=>false,'error'=>'Not found']); exit; }
            $speakers = $pdo->prepare('SELECT id, name, position, image_url FROM event_speakers WHERE event_id = ? ORDER BY id');
            $speakers->execute([$id]);
            $partners = $pdo->prepare('SELECT id, name, image_url FROM event_partners WHERE event_id = ? ORDER BY id');
            $partners->execute([$id]);
            $event['speakers'] = $speakers->fetchAll();
            $event['partners'] = $partners->fetchAll();
            echo json_encode(['success' => true, 'data' => $event]);
            exit;
        }
        // List events, include client info if available
        $stmt = $pdo->query("SELECT e.id, e.client_id, c.name AS client_name, c.email AS client_email, e.title, e.event_date, e.location, e.status, e.description,
                                    e.event_type, e.image_url, e.pdf_url
                             FROM events e
                             LEFT JOIN clients c ON c.id = e.client_id
                             ORDER BY COALESCE(e.event_date, e.created_at) DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        exit;
    }

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['title']) || trim($data['title']) === '') { echo json_encode(['success'=>false,'error'=>'title is required']); exit; }
        $client_id = isset($data['client_id']) && $data['client_id'] !== '' ? (int)$data['client_id'] : null;
        $title = trim($data['title']);
        $event_date = $data['event_date'] ?? null; // Expecting 'YYYY-MM-DDTHH:MM' or 'YYYY-MM-DD'
        $location = $data['location'] ?? null;
        $status = $data['status'] ?? 'draft';
        $description = $data['description'] ?? null;
        $event_type = $data['event_type'] ?? null;
        $image_url = $data['image_url'] ?? null;
        $pdf_url = $data['pdf_url'] ?? null;
        $video_url = $data['video_url'] ?? null;
        $agenda = $data['agenda'] ?? null;
        $overview = $data['overview'] ?? null;
        $map_embed = $data['map_embed'] ?? null;
        $ad_left_url = $data['ad_left_url'] ?? null;
        $ad_left_link = $data['ad_left_link'] ?? null;
        $ad_right_url = $data['ad_right_url'] ?? null;
        $ad_right_link = $data['ad_right_link'] ?? null;
        $stmt = $pdo->prepare('INSERT INTO events (client_id, title, event_date, location, status, description, event_type, image_url, pdf_url, video_url, agenda, overview, map_embed, ad_left_url, ad_left_link, ad_right_url, ad_right_link) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$client_id, $title, $event_date, $location, $status, $description, $event_type, $image_url, $pdf_url, $video_url, $agenda, $overview, $map_embed, $ad_left_url, $ad_left_link, $ad_right_url, $ad_right_link]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        exit;
    }

    if ($method === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) { echo json_encode(['success'=>false,'error'=>'Event ID required']); exit; }
        $fields = [];
        $params = [];
        foreach (['client_id','title','event_date','location','status','description','event_type','image_url','pdf_url','video_url','agenda','overview','map_embed','ad_left_url','ad_left_link','ad_right_url','ad_right_link'] as $f) {
            if (array_key_exists($f, $data)) { $fields[] = "$f = ?"; $params[] = $data[$f]; }
        }
        if (!$fields) { echo json_encode(['success'=>false,'error'=>'No fields to update']); exit; }
        $params[] = $data['id'];
        $sql = 'UPDATE events SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id'])) { echo json_encode(['success'=>false,'error'=>'Event ID required']); exit; }
        $stmt = $pdo->prepare('DELETE FROM events WHERE id = ?');
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true]);
        exit;
    }

    // Nested resources: speakers and partners
    if ($method === 'PATCH') {
        // Support adding/removing speakers/partners via action
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'] ?? '';
        if ($action === 'add_speaker') {
            $stmt = $pdo->prepare('INSERT INTO event_speakers (event_id, name, position, image_url) VALUES (?,?,?,?)');
            $stmt->execute([(int)$data['event_id'], $data['name'] ?? '', $data['position'] ?? null, $data['image_url'] ?? null]);
            echo json_encode(['success'=>true, 'id'=>$pdo->lastInsertId()]); exit;
        }
        if ($action === 'delete_speaker') {
            $stmt = $pdo->prepare('DELETE FROM event_speakers WHERE id = ?');
            $stmt->execute([(int)$data['id']]);
            echo json_encode(['success'=>true]); exit;
        }
        if ($action === 'add_partner') {
            $stmt = $pdo->prepare('INSERT INTO event_partners (event_id, name, image_url) VALUES (?,?,?)');
            $stmt->execute([(int)$data['event_id'], $data['name'] ?? '', $data['image_url'] ?? null]);
            echo json_encode(['success'=>true, 'id'=>$pdo->lastInsertId()]); exit;
        }
        if ($action === 'delete_partner') {
            $stmt = $pdo->prepare('DELETE FROM event_partners WHERE id = ?');
            $stmt->execute([(int)$data['id']]);
            echo json_encode(['success'=>true]); exit;
        }
        echo json_encode(['success'=>false,'error'=>'Invalid action']); exit;
    }

    echo json_encode(['success' => false, 'error' => 'Invalid request method or parameters']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


