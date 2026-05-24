<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$data   = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
        $stmt->execute();
        response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'POST') {
    try {
        $db   = getDB();
        $hash = password_hash($data['password'] ?? '123456', PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$data['name'], $data['email'], $hash, $data['role'] ?? 'usuario']);
        response(['success' => true, 'id' => $db->lastInsertId()]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'PUT') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('UPDATE users SET name=?, email=?, role=? WHERE id=?');
        $stmt->execute([$data['name'], $data['email'], $data['role'], $data['id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'DELETE') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$data['id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>