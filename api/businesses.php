<?php
require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$data   = json_decode(file_get_contents('php://input'), true);

if ($method === 'GET') {
    try {
        $db = getDB();

        if (isset($_GET['id'])) {
            $stmt = $db->prepare('SELECT * FROM businesses WHERE id = ?');
            $stmt->execute([$_GET['id']]);
            $business = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$business) response(['success' => false, 'message' => 'Negocio no encontrado'], 404);
            response(['success' => true, 'data' => $business]);
        }

        $where = '';
        $params = [];
        if (isset($_GET['category']) && $_GET['category']) {
            $where    = 'WHERE category = ?';
            $params[] = $_GET['category'];
        }

        $stmt = $db->prepare("SELECT b.*, COALESCE(AVG(r.rating), 0) as avg_rating, COUNT(r.id) as review_count FROM businesses b LEFT JOIN reviews r ON b.id = r.business_id $where GROUP BY b.id ORDER BY avg_rating DESC");
        $stmt->execute($params);
        $businesses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        response(['success' => true, 'data' => $businesses]);

    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'POST') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('INSERT INTO businesses (name, category, description, image) VALUES (?, ?, ?, ?)');
        $stmt->execute([$data['name'], $data['category'], $data['description'] ?? '', $data['image'] ?? '🏢']);
        response(['success' => true, 'id' => $db->lastInsertId()]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'PUT') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('UPDATE businesses SET name=?, category=?, description=?, image=? WHERE id=?');
        $stmt->execute([$data['name'], $data['category'], $data['description'], $data['image'], $data['id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}

if ($method === 'DELETE') {
    try {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM businesses WHERE id = ?');
        $stmt->execute([$data['id']]);
        response(['success' => true]);
    } catch (Exception $e) {
        response(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>