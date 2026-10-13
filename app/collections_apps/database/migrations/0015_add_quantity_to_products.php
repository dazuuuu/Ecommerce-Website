<?php
return [
    // Idempotent: some databases already have `quantity` (added by hand before this migration existed).
    'up' => function (PDO $pdo): void {
        $exists = (int) $pdo->query(
            "SELECT COUNT(*) FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = 'quantity'"
        )->fetchColumn();
        if (!$exists) {
            $pdo->exec('ALTER TABLE products ADD COLUMN quantity INT UNSIGNED NOT NULL DEFAULT 10 AFTER in_stock');
        }
    },
    'down' => 'ALTER TABLE products DROP COLUMN quantity',
];
