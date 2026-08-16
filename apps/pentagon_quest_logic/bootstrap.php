<?php

/**
 * Application bootstrap — loads Composer autoloader and environment.
 */

use App\Helpers\Path;
use App\Helpers\Session;
use Dotenv\Dotenv;

if (defined('PENTAGON_BOOTSTRAPPED')) {
    return;
}

define('PENTAGON_BOOTSTRAPPED', true);

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

if (file_exists(Path::env())) {
    Dotenv::createImmutable(Path::root())->safeLoad();
}

$app = require Path::config('app.php');
date_default_timezone_set($app['timezone'] ?? 'Africa/Nairobi');
Session::start();
