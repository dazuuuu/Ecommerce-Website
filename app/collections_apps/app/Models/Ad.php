<?php

namespace App\Models;

use App\Core\Database;

class Ad
{
    private const FIELDS = [
        'title', 'description', 'badge', 'media_path', 'media_type', 'link_url', 'cta_label',
        'show_countdown', 'countdown_label', 'countdown_to', 'starts_at', 'ends_at', 'is_active',
    ];

    public static function all(): array
    {
        return Database::connection()->query('SELECT * FROM ads ORDER BY id DESC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM ads WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** The ad to show right now: active, inside its window, most recently started first. */
    public static function current(): ?array
    {
        $now = date('Y-m-d H:i:s');
        $stmt = Database::connection()->prepare(
            'SELECT * FROM ads WHERE is_active = 1 AND starts_at <= ? AND ends_at > ? ORDER BY starts_at DESC, id DESC LIMIT 1'
        );
        $stmt->execute([$now, $now]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::connection();
        $pdo->prepare('INSERT INTO ads (' . implode(', ', self::FIELDS) . ') VALUES (' . implode(', ', array_map(fn($f) => ":$f", self::FIELDS)) . ')')
            ->execute(self::pick($data));
        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $sets = implode(', ', array_map(fn($f) => "$f = :$f", self::FIELDS));
        $params = self::pick($data);
        $params['id'] = $id;
        Database::connection()->prepare("UPDATE ads SET $sets WHERE id = :id")->execute($params);
    }

    public static function setActive(int $id, bool $active): void
    {
        Database::connection()->prepare('UPDATE ads SET is_active = ? WHERE id = ?')->execute([$active ? 1 : 0, $id]);
    }

    public static function delete(int $id): void
    {
        Database::connection()->prepare('DELETE FROM ads WHERE id = ?')->execute([$id]);
    }

    /** Shape sent to the storefront JS. */
    public static function toPublic(array $ad): array
    {
        $countdownTo = null;
        if (!empty($ad['show_countdown'])) {
            $countdownTo = strtotime($ad['countdown_to'] ?: $ad['ends_at']);
        }
        return [
            'id' => (int) $ad['id'],
            'title' => $ad['title'],
            'description' => $ad['description'],
            'badge' => $ad['badge'],
            'mediaUrl' => $ad['media_path'] ? imageUrl($ad['media_path']) : null,
            'mediaType' => $ad['media_type'],
            'linkUrl' => $ad['link_url'],
            'ctaLabel' => $ad['cta_label'] ?: 'Shop now',
            'countdownLabel' => $ad['countdown_label'] ?: 'Ends in',
            'countdownToMs' => $countdownTo ? $countdownTo * 1000 : null,
            'endsAtMs' => strtotime($ad['ends_at']) * 1000,
            'serverNowMs' => time() * 1000,
        ];
    }

    private static function pick(array $data): array
    {
        $out = [];
        foreach (self::FIELDS as $f) {
            $out[$f] = $data[$f] ?? null;
        }
        return $out;
    }
}
