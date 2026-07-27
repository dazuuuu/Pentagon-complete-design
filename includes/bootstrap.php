<?php

/**
 * Application bootstrap — loads Composer autoloader and environment.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenvPath = dirname(__DIR__);
if (file_exists($dotenvPath . '/.env')) {
    Dotenv::createImmutable($dotenvPath)->safeLoad();
}

date_default_timezone_set('Africa/Nairobi');
