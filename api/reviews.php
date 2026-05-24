<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$data   = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    try {
        $db = getDB();

        if (isset($_GET['business_id'])) {
            $stmt = $db->prepare('SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.business_id = ? ORDER BY r.created_at DESC');
            $stmt->execute([$_GET['business_id']]);
            response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }

        $stmt = $db->prepare('SELECT r.*, u.name as user_name, b.name as business_name FROM reviews r JOIN users u ON r.user_id = u.id JOIN businesses b ON r.business_id = b.id ORDER BY r.created_at DESC');
        $stmt->execute();
        response(['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);

    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'POST') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('INSERT INTO reviews (business_id, user_id, rating, comment) VALUES (?, ?, ?, ?)');
        $stmt->execute([$data['business_id'], $data['user_id'], $data['rating'], $data['comment']]);
        response(['success' => true, 'id' => $db->lastInsertId()]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'PUT') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('UPDATE reviews SET rating=?, comment=? WHERE id=?');
        $stmt->execute([$data['rating'], $data['comment'], $data['id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'DELETE') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM reviews WHERE id = ?');
        $stmt->execute([$data['id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>