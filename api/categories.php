<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$data   = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM categories ORDER BY name');
        $stmt->execute();
        response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'POST') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('INSERT INTO categories (name, icon) VALUES (?, ?)');
        $stmt->execute([$data['name'], $data['icon'] ?? '📁']);
        response(['success' => true, 'id' => $db->lastInsertId()]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'PUT') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('UPDATE categories SET name=?, icon=? WHERE id=?');
        $stmt->execute([$data['name'], $data['icon'], $data['id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'DELETE') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$data['id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>