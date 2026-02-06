<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
// Updated path: Laravel app is now in SAMUEL_CLINIC folder outside public_html
if (file_exists($maintenance = __DIR__ . '/../SAMUEL_CLINIC/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
// Updated path: vendor folder is in Laravel app directory
require __DIR__ . '/../SAMUEL_CLINIC/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
// Updated path: bootstrap folder is in Laravel app directory
/** @var Application $app */
$app = require_once __DIR__ . '/../SAMUEL_CLINIC/bootstrap/app.php';

$app->handleRequest(Request::capture());
