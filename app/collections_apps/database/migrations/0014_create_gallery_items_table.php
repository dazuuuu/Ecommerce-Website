<?php
return [
    'up' => "CREATE TABLE IF NOT EXISTS gallery_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        image VARCHAR(255) NOT NULL,
        title VARCHAR(255) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        is_new TINYINT(1) NOT NULL DEFAULT 0,
        new_arrival_until DATETIME DEFAULT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    'down' => 'DROP TABLE IF EXISTS gallery_items',
];
