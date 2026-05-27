<?php
require_once 'api/config.php';

$hash = password_hash('Admin1.', PASSWORD_BCRYPT);

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Admin', 'admin@trustreview.com', $hash, 'admin']);
    echo json_encode(['success' => true, 'message' => 'Admin creado correctamente']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>