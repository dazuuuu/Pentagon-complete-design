<?php

/**
 * Front controller for the PHP built-in server.
 * Run from this folder (the public_html equivalent):
 *
 *   php -S localhost:8000 router.php
 */

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = urldecode(parse_url($requestUri, PHP_URL_PATH) ?: '/');
$query = parse_url($requestUri, PHP_URL_QUERY);
$suffix = $query ? '?' . $query : '';
$public = __DIR__;
$path = $public . $uri;

if ($uri === '/index.php') {
    header('Location: /' . $suffix, true, 301);
    return true;
}

if (preg_match('#^/(.+)\.php$#', $uri, $match)) {
    header('Location: /' . $match[1] . $suffix, true, 301);
    return true;
}

if ($uri !== '/' && is_file($path)) {
    return false;
}

if (is_dir($path) && is_file($path . '/index.php')) {
    require $path . '/index.php';
    return true;
}

if (preg_match('#^/setup(/index\.php)?/?$#', $uri)) {
    require $public . '/setup/index.php';
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
