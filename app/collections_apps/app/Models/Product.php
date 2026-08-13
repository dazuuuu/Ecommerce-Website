<?php

namespace App\Models;

use App\Core\Database;

class Product
{
    public static function all(): array
    {
        $rows = Database::connection()->query('SELECT * FROM products ORDER BY id ASC')->fetchAll();
        return array_map([self::class, 'toArray'], $rows);
    }

    public static function findByCode(string $code): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM products WHERE product_code = ?');
        $stmt->execute([$code]);
        $row = $stmt->fetch();
        return $row ? self::toArray($row) : null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? self::toArray($row) : null;
    }

    /** Raw DB row (not shaped for the storefront) — used by admin forms/listings. */
    public static function findRawById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function search(string $term = ''): array
    {
        $pdo = Database::connection();
        if ($term === '') {
            return $pdo->query('SELECT * FROM products ORDER BY id DESC')->fetchAll();
        }
        $stmt = $pdo->prepare('SELECT * FROM products WHERE name LIKE ? OR product_code LIKE ? ORDER BY id DESC');
        $stmt->execute(["%$term%", "%$term%"]);
        return $stmt->fetchAll();
    }

    public static function onOffer(): array
    {
        return Database::connection()->query('SELECT * FROM products WHERE is_sale = 1 ORDER BY updated_at DESC')->fetchAll();
    }

    public static function notOnOffer(): array
    {
        return Database::connection()->query('SELECT id, name, product_code, price FROM products WHERE is_sale = 0 ORDER BY name ASC')->fetchAll();
    }

    public static function nextProductCode(): string
    {
        $max = (int) Database::connection()
            ->query("SELECT MAX(CAST(SUBSTRING_INDEX(product_code, '-', -1) AS UNSIGNED)) FROM products WHERE product_code LIKE 'pentagon-%'")
            ->fetchColumn();
        return 'pentagon-' . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    public static function create(array $data): int
    {
        $data['product_code'] = self::nextProductCode();
        $cols = implode(', ', array_keys($data));
        $params = implode(', ', array_map(fn($k) => ":$k", array_keys($data)));
        $pdo = Database::connection();
        $pdo->prepare("INSERT INTO products ($cols) VALUES ($params)")->execute($data);
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $sql = 'UPDATE products SET ' . implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data))) . ' WHERE id = :id';
        $data['id'] = $id;
        Database::connection()->prepare($sql)->execute($data);
    }

    public static function delete(int $id): void
    {
        Database::connection()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    }

    public static function updateOfferPricing(int $id, float $price, ?float $originalPrice): void
    {
        Database::connection()
            ->prepare('UPDATE products SET price = ?, original_price = ?, is_sale = 1 WHERE id = ?')
            ->execute([$price, $originalPrice, $id]);
    }

    public static function removeFromOffers(int $id): void
    {
        Database::connection()->prepare('UPDATE products SET is_sale = 0, original_price = NULL WHERE id = ?')->execute([$id]);
    }

    public static function addToOffers(int $id): void
    {
        Database::connection()->prepare('UPDATE products SET is_sale = 1 WHERE id = ?')->execute([$id]);
    }

    /** Converts a DB row into the shape storefront templates/JS expect. */
    public static function toArray(array $row): array
    {
        return [
            'id' => $row['product_code'],
            'dbId' => (int) $row['id'],
            'name' => $row['name'],
            'subtitle' => $row['subtitle'],
            'price' => (float) $row['price'],
            'originalPrice' => $row['original_price'] !== null ? (float) $row['original_price'] : null,
            'category' => $row['category_key'],
            'subCategory' => $row['sub_category'],
            'colors' => json_decode($row['colors'] ?? '[]', true) ?: [],
            'sizes' => json_decode($row['sizes'] ?? '[]', true) ?: [],
            'images' => json_decode($row['images'] ?? '[]', true) ?: [],
            'description' => $row['description'],
            'details' => json_decode($row['details'] ?? '[]', true) ?: [],
            'fabric' => $row['fabric'],
            'fit' => $row['fit'],
            'isNew' => (bool) $row['is_new'],
            'isBestSeller' => (bool) $row['is_best_seller'],
            'isSale' => (bool) $row['is_sale'],
            'rating' => (float) $row['rating'],
            'reviewCount' => (int) $row['review_count'],
            'inStock' => (bool) $row['in_stock'],
            'featuredInLookbook' => (bool) $row['featured_in_lookbook'],
        ];
    }
}
