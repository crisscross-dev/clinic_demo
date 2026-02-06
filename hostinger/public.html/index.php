<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Absolute path to your Laravel app folder
$laravelApp = __DIR__ . '/../SAMUEL_CLINIC';

// Check maintenance mode
if (file_exists($laravelApp . '/storage/framework/maintenance.php')) {
    require $laravelApp . '/storage/framework/maintenance.php';
}

// Autoloader
require $laravelApp . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $laravelApp . '/bootstrap/app.php';



/** @var Application $app */
$app->handleRequest(Request::capture());
