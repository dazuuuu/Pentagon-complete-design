<?php

/**
 * Application bootstrap — loads Composer autoloader and environment.
 * Composer, vendor, and .env live in this same folder (pentagon_quest_logic).
 */

use App\Helpers\Path;
use App\Helpers\Session;
use Dotenv\Dotenv;

if (defined('PENTAGON_BOOTSTRAPPED')) {
    return;
}

define('PENTAGON_BOOTSTRAPPED', true);

require_once __DIR__ . '/vendor/autoload.php';

if (file_exists(Path::env())) {
    Dotenv::createImmutable(Path::logic())->safeLoad();
}

$app = require Path::config('app.php');
date_default_timezone_set($app['timezone'] ?? 'Africa/Nairobi');
Session::start();
