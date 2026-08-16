<?php

namespace App\Services;

use App\Core\Database;
use PDO;

class MigrationService
{
    public static function pending(): array
    {
        $applied = self::appliedNames();
        $pending = [];

        foreach (self::migrationFiles() as $file) {
            $name = basename($file, '.php');
            if (!in_array($name, $applied, true)) {
                $pending[] = $name;
            }
        }

        return $pending;
    }

    public static function appliedRecords(): array
    {
        $pdo = Database::connection();
        self::ensureTable();

        return $pdo->query(
            'SELECT migration, applied_at FROM migrations ORDER BY id DESC'
        )->fetchAll();
    }

    /**
     * First-run only: create tables so setup/login can work.
     * After the shop is installed, new PHP migrations wait for an admin click.
     */
    public static function bootstrapIfNeeded(): int
    {
        if (self::isInstalled()) {
            return 0;
        }

        return self::runPending();
    }

    public static function isInstalled(): bool
    {
        try {
            $pdo = Database::connection();
            $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
            return (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function runPending(): int
    {
        $pdo = Database::connection();
        $applied = self::appliedNames();

        $ran = 0;
        foreach (self::migrationFiles() as $file) {
            $name = basename($file, '.php');
            if (in_array($name, $applied, true)) {
                continue;
            }
            $migration = require $file;
            if (!is_array($migration) || !array_key_exists('up', $migration)) {
                throw new \RuntimeException("Migration {$name} must return an array with an 'up' key.");
            }
            self::executeUp($pdo, $migration['up'], $name);
            $pdo->prepare('INSERT INTO migrations (migration) VALUES (?)')->execute([$name]);
            $ran++;
        }

        return $ran;
    }

    private static function executeUp(PDO $pdo, mixed $up, string $name): void
    {
        if (is_callable($up)) {
            $up($pdo);
            return;
        }
        if (is_string($up) && trim($up) !== '') {
            $pdo->exec($up);
            return;
        }

        throw new \RuntimeException("Migration {$name} is missing a valid up action.");
    }

    private static function appliedNames(): array
    {
        $pdo = Database::connection();
        self::ensureTable();
        return $pdo->query('SELECT migration FROM migrations')->fetchAll(PDO::FETCH_COLUMN);
    }

    private static function ensureTable(): void
    {
        Database::connection()->exec(
            'CREATE TABLE IF NOT EXISTS migrations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    private static function migrationFiles(): array
    {
        $files = glob(dirname(__DIR__, 2) . '/database/migrations/*.php') ?: [];
        sort($files);
        return $files;
    }
}
