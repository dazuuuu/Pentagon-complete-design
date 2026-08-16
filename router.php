<?php

/**
 * Front controller for `php -S localhost:8080 router.php`
 * when the document root is the repository root.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$public = __DIR__ . '/publics';
$path = $public . $uri;

if ($uri !== '/' && is_file($path)) {
    return false;
}

if (is_dir($path) && is_file($path . '/index.php')) {
    require $path . '/index.php';
    return true;
}

if (preg_match('#^/handlers/#', $uri)) {
    require $public . '/handlers/index.php';
    return true;
}

if (preg_match('#^/api/#', $uri)) {
    require $public . '/api/index.php';
    return true;
}

if ($uri === '/sitemap.xml') {
    require $public . '/sitemap.php';
    return true;
}

$phpFile = $public . rtrim($uri, '/') . '.php';
if ($uri !== '/' && is_file($phpFile)) {
    require $phpFile;
    return true;
}

if ($uri === '/' || $uri === '') {
    require $public . '/index.php';
    return true;
}

http_response_code(404);
echo 'Not found';
