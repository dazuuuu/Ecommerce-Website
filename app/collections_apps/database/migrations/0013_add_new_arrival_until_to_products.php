<?php
return [
    'up' => "ALTER TABLE products
        ADD COLUMN new_arrival_until DATETIME DEFAULT NULL AFTER is_new",
    'down' => 'ALTER TABLE products DROP COLUMN new_arrival_until',
];
