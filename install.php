<?php
require_once 'api/config.php';

try {
    $db = getDB();

    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'usuario',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS categories (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        icon VARCHAR(20) DEFAULT '📁',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS businesses (
        id SERIAL PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        category VARCHAR(100) NOT NULL,
        description TEXT,
        image VARCHAR(20) DEFAULT '🏢',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS reviews (
        id SERIAL PRIMARY KEY,
        business_id INT NOT NULL,
        user_id INT NOT NULL,
        rating SMALLINT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS favorites (
        id SERIAL PRIMARY KEY,
        user_id INT NOT NULL,
        business_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
        UNIQUE (user_id, business_id)
    )");

    $count = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO categories (name, icon) VALUES
            ('Restaurantes', '🍽️'),
            ('Tiendas', '🛍️'),
            ('Hoteles', '🏨'),
            ('Salud y Belleza', '💆'),
            ('Tecnología', '💻'),
            ('Ocio', '🎭'),
            ('Educación', '📚'),
            ('Servicios', '🔧')
        ");
    }

    $count = $db->query("SELECT COUNT(*) FROM businesses")->fetchColumn();
    if ($count == 0) {
        $db->exec("INSERT INTO businesses (name, category, description, image) VALUES
            ('La Taberna del Sur', 'Restaurantes', 'Auténtica cocina andaluza en el corazón de Sevilla.', '🍽️'),
            ('TechStore Sevilla', 'Tecnología', 'Tu tienda de tecnología de confianza.', '💻'),
            ('Hotel Giralda', 'Hoteles', 'Hotel boutique de 4 estrellas con vistas a la Giralda.', '🏨'),
            ('Peluquería Estilo', 'Salud y Belleza', 'Salón de peluquería y estética con más de 15 años.', '💆'),
            ('Librería El Rincón', 'Educación', 'Librería independiente especializada en literatura española.', '📚'),
            ('Cine Lumière', 'Ocio', 'Cine de autor con las mejores películas independientes.', '🎭')
        ");
    }

    echo json_encode(['success' => true, 'message' => 'Base de datos instalada correctamente']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>