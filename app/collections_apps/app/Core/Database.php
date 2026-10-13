<?php

namespace App\Core;

use PDO;

/**
 * The one and only database connection for the whole app (every page, the
 * migration runner and the seeder). Edit the credentials below for the
 * server this copy runs on — local or live hosting.
 */
class Database
{
    private const HOST = '127.0.0.1';
    private const PORT = '3306';
    private const NAME = 'pentagon_collections';
    private const USER = 'root';
    private const PASS = '';

    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $dsn = 'mysql:host=' . self::HOST . ';port=' . self::PORT . ';dbname=' . self::NAME . ';charset=utf8mb4';
            self::$connection = new PDO($dsn, self::USER, self::PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$connection;
    }
}
