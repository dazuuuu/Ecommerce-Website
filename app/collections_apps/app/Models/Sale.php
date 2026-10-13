<?php

namespace App\Models;

use App\Core\Database;

class Sale
{
    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $pdo->prepare(
            'INSERT INTO sales (customer_name, payment_method, mpesa_code, cash_amount, mpesa_amount, total, admin_id)
             VALUES (:customer_name, :payment_method, :mpesa_code, :cash_amount, :mpesa_amount, :total, :admin_id)'
        )->execute($data);
        return (int) $pdo->lastInsertId();
    }

    public static function all(int $limit = 100): array
    {
        $limit = (int) $limit;
        return Database::connection()
            ->query("SELECT * FROM sales ORDER BY created_at DESC LIMIT {$limit}")
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM sales WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
}
