<?php
/**
 * Migration runner.
 *   php app/collections_apps/database/migrate.php            Run all pending migrations
 *   php app/collections_apps/database/migrate.php --fresh    Drop all known tables, then run every migration
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Env;
use App\Core\Database;
use App\Services\MigrationService;

Env::load();
$pdo = Database::connection();

$fresh = in_array('--fresh', $argv, true);

if ($fresh) {
    echo "Dropping existing tables...\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (['order_items', 'orders', 'otp_codes', 'customers', 'products', 'categories', 'admins', 'seo_meta', 'store_settings', 'migrations'] as $table) {
        $pdo->exec("DROP TABLE IF EXISTS `$table`");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
}

$ran = MigrationService::runPending();
echo $ran ? "Ran {$ran} migration(s).\n" : "Nothing to migrate.\n";
