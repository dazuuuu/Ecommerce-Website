<?php
/**
 * Application bootstrap — required once by public/index.php (the only PHP
 * file Apache ever executes; see public/.htaccess) before the router runs.
 */

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/Helpers/functions.php';

// Shown to admins in place of individual migration names when an update is available.
const APP_VERSION = '2.0';

date_default_timezone_set('Africa/Nairobi');
App\Core\PathHandler::init();
App\Core\Url::init();

error_reporting(E_ALL);
ini_set('display_errors', '1');
