<?php

namespace App\Models;

use App\Core\Database;

class GalleryItem
{
    public static function all(): array
    {
        self::expireMaturedNewArrivals();
        return Database::connection()
            ->query('SELECT * FROM gallery_items ORDER BY sort_order ASC, id DESC')
            ->fetchAll();
    }

    public static function find(int $id): ?array
    {
        self::expireMaturedNewArrivals();
        $stmt = Database::connection()->prepare('SELECT * FROM gallery_items WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $cols = implode(', ', array_keys($data));
        $params = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
        $pdo = Database::connection();
        $pdo->prepare("INSERT INTO gallery_items ($cols) VALUES ($params)")->execute($data);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $sql = 'UPDATE gallery_items SET ' . implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data))) . ' WHERE id = :id';
        $data['id'] = $id;
        Database::connection()->prepare($sql)->execute($data);
    }

    public static function delete(int $id): void
    {
        Database::connection()->prepare('DELETE FROM gallery_items WHERE id = ?')->execute([$id]);
    }

    public static function expireMaturedNewArrivals(): void
    {
        Database::connection()
            ->exec('UPDATE gallery_items SET is_new = 0, new_arrival_until = NULL WHERE is_new = 1 AND new_arrival_until IS NOT NULL AND new_arrival_until <= NOW()');
    }
}
