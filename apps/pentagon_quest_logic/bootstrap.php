<?php

/**
 * Application bootstrap — loads Composer autoloader and config.
 * Composer and vendor live in this same folder (pentagon_quest_logic).
 * All settings (DB, mail, app) come from plain PHP files in config/ —
 * no .env file is used anywhere in this app.
 */

use App\Helpers\Path;
use App\Helpers\Session;

if (defined('PENTAGON_BOOTSTRAPPED')) {
    return;
}

define('PENTAGON_BOOTSTRAPPED', true);

require_once __DIR__ . '/vendor/autoload.php';

$app = require Path::config('app.php');
date_default_timezone_set($app['timezone'] ?? 'Africa/Nairobi');
Session::start();
