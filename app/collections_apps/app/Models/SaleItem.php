<?php

namespace App\Models;

use App\Core\Database;

class SaleItem
{
    public static function create(int $saleId, array $item): void
    {
        Database::connection()->prepare(
            'INSERT INTO sale_items (sale_id, product_id, product_name, quantity, unit_price)
             VALUES (?, ?, ?, ?, ?)'
        )->execute([
            $saleId, $item['product_id'], $item['product_name'], $item['quantity'], $item['unit_price'],
        ]);
    }

    public static function forSale(int $saleId): array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM sale_items WHERE sale_id = ?');
        $stmt->execute([$saleId]);
        return $stmt->fetchAll();
    }
}
