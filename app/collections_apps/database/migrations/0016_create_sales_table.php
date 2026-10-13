<?php
return [
    'up' => "CREATE TABLE sales (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(191) NULL,
        payment_method ENUM('cash','mpesa','mixed') NOT NULL DEFAULT 'cash',
        mpesa_code VARCHAR(50) NULL,
        cash_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
        mpesa_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
        total DECIMAL(10,2) NOT NULL,
        admin_id INT UNSIGNED NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    'down' => 'DROP TABLE IF EXISTS sales',
];
