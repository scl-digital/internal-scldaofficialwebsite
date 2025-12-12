<?php
header('Content-Type: application/json');
require_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // List all clients
        $stmt = $pdo->query('SELECT id, name, email, company, created_at FROM clients');
        $clients = $stmt->fetchAll();
        echo json_encode(['success' => true, 'data' => $clients]);
        break;
    case 'POST':
        // Create new client
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data['name'] || !$data['email'] || !$data['password']) {
            echo json_encode(['success' => false, 'error' => 'Name, email, and password required.']);
            exit;
        }
        // Check for duplicate email
        $stmt = $pdo->prepare('SELECT id FROM clients WHERE email = ?');
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'error' => 'A client with this email already exists.']);
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO clients (name, email, password, company) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $data['name'],
            $data['email'],
            trim($data['password']), // Store as plain text for now
            $data['company'] ?? null
        ]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;
    case 'PUT':
        // Update client
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data['id']) {
            echo json_encode(['success' => false, 'error' => 'Client ID required.']);
            exit;
        }
        // Check for duplicate email (if changing email)
        if (isset($data['email'])) {
            $stmt = $pdo->prepare('SELECT id FROM clients WHERE email = ? AND id != ?');
            $stmt->execute([$data['email'], $data['id']]);
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'error' => 'A client with this email already exists.']);
                exit;
            }
        }
        $fields = [];
        $params = [];
        if (isset($data['name'])) { $fields[] = 'name = ?'; $params[] = $data['name']; }
        if (isset($data['email'])) { $fields[] = 'email = ?'; $params[] = $data['email']; }
        if (isset($data['company'])) { $fields[] = 'company = ?'; $params[] = $data['company']; }
        if (isset($data['password']) && $data['password']) { $fields[] = 'password = ?'; $params[] = trim($data['password']); }
        if (!$fields) {
            echo json_encode(['success' => false, 'error' => 'No fields to update.']);
            exit;
        }
        $params[] = $data['id'];
        $sql = 'UPDATE clients SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true]);
        break;
    case 'DELETE':
        // Delete client
        parse_str(file_get_contents('php://input'), $data);
        if (!$data['id']) {
            echo json_encode(['success' => false, 'error' => 'Client ID required.']);
            exit;
        }
        $stmt = $pdo->prepare('DELETE FROM clients WHERE id = ?');
        $stmt->execute([$data['id']]);
        echo json_encode(['success' => true]);
        break;
    case 'PATCH':
        // Reset password for existing client
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data['id'] || !$data['password']) {
            echo json_encode(['success' => false, 'error' => 'Client ID and new password required.']);
            exit;
        }
        $stmt = $pdo->prepare('UPDATE clients SET password = ? WHERE id = ?');
        $stmt->execute([trim($data['password']), $data['id']]);
        echo json_encode(['success' => true]);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
} 