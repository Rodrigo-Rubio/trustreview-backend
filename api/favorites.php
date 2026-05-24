<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$data   = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT b.* FROM favorites f JOIN businesses b ON f.business_id = b.id WHERE f.user_id = ?');
        $stmt->execute([$_GET['user_id']]);
        response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'POST') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT id FROM favorites WHERE user_id = ? AND business_id = ?');
        $stmt->execute([$data['user_id'], $data['business_id']]);
        if ($stmt->fetch()) {
            response(['success' => false, 'message' => 'Ya está en favoritos']);
        }
        $stmt = $db->prepare('INSERT INTO favorites (user_id, business_id) VALUES (?, ?)');
        $stmt->execute([$data['user_id'], $data['business_id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'DELETE') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM favorites WHERE user_id = ? AND business_id = ?');
        $stmt->execute([$data['user_id'], $data['business_id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>