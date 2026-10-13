<?php
return [
    'up' => "CREATE TABLE IF NOT EXISTS ads (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(191) NOT NULL,
        description TEXT DEFAULT NULL,
        badge VARCHAR(60) DEFAULT NULL,
        media_path VARCHAR(255) DEFAULT NULL,
        media_type ENUM('image','video') DEFAULT NULL,
        link_url VARCHAR(500) DEFAULT NULL,
        cta_label VARCHAR(60) DEFAULT NULL,
        show_countdown TINYINT(1) NOT NULL DEFAULT 0,
        countdown_label VARCHAR(80) DEFAULT NULL,
        countdown_to DATETIME DEFAULT NULL,
        starts_at DATETIME NOT NULL,
        ends_at DATETIME NOT NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    'down' => 'DROP TABLE IF EXISTS ads',
];
