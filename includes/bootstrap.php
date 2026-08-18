<?php

/**
 * Application bootstrap — loads Composer autoloader and environment.
 */

use App\Helpers\Path;
use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(Path::env())) {
    Dotenv::createImmutable(Path::root())->safeLoad();
}

date_default_timezone_set('Africa/Nairobi');
