<?php
require_once 'config.php';

try {
    $db = getDB();
    
    $reviews = $db->query('SELECT COUNT(*) FROM reviews')->fetchColumn();
    $businesses = $db->query('SELECT COUNT(*) FROM businesses')->fetchColumn();
    $users = $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    
    response([
        'success' => true,
        'data' => [
            'reviews' => (int)$reviews,
            'businesses' => (int)$businesses,
            'users' => (int)$users
        ]
    ]);
} catch (Exception $e) {
    response(['success' => false, 'message' => $e->getMessage()]);
}
?>