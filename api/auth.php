<?php
require_once 'config.php';

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

if ($action === 'register') {
    $name     = trim($data['name'] ?? '');
    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$name || !$email || !$password) {
        response(['success' => false, 'message' => 'Rellena todos los campos']);
    }

    try {
        $db = getDB();

        $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            response(['success' => false, 'message' => 'El email ya está registrado']);
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $hash, 'usuario']);
        $userId = $db->lastInsertId();

        $token = bin2hex(random_bytes(32));
        $user  = ['id' => (int)$userId, 'name' => $name, 'email' => $email, 'role' => 'usuario'];

        response(['success' => true, 'user' => $user, 'token' => $token]);

    } catch (Exception $e) {
        response(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
}

if ($action === 'login') {
    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        response(['success' => false, 'message' => 'Rellena todos los campos']);
    }

    try {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            response(['success' => false, 'message' => 'Email o contraseña incorrectos']);
        }

        $token    = bin2hex(random_bytes(32));
        $userData = ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email'], 'role' => $user['role']];

        response(['success' => true, 'user' => $userData, 'token' => $token]);

    } catch (Exception $e) {
        response(['success' => false, 'message' => 'Error en el servidor: ' . $e->getMessage()]);
    }
}

response(['success' => false, 'message' => 'Acción no válida']);
?>