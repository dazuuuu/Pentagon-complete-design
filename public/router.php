<?php

/**
 * Front controller for the PHP built-in server.
 *
 * Local (project root):
 *   php -S localhost:8000 -t public public/router.php
 *   php -S localhost:8000 -t public router.php
 *
 * Local (inside public/, the public_html equivalent):
 *   php -S localhost:8000 router.php
 */

require_once __DIR__ . '/paths.php';

$public = PENTAGON_WEB_ROOT;
if (realpath($public) && realpath(getcwd() ?: '') !== realpath($public)) {
    chdir($public);
}

set_include_path($public . PATH_SEPARATOR . get_include_path());

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$uri = urldecode(parse_url($requestUri, PHP_URL_PATH) ?: '/');
$query = parse_url($requestUri, PHP_URL_QUERY);
$suffix = $query ? '?' . $query : '';
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
    $webRootReal = realpath($public);
    $fileReal = realpath($path);
    if ($webRootReal && $fileReal && str_starts_with($fileReal, $webRootReal . DIRECTORY_SEPARATOR)) {
        $docRootRaw = (string) ($_SERVER['DOCUMENT_ROOT'] ?? '');
        $docRootReal = $docRootRaw !== '' ? realpath($docRootRaw) : false;
        if ($docRootReal && $webRootReal && $docRootReal === $webRootReal) {
            return false;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $types = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'xml' => 'application/xml',
            'txt' => 'text/plain; charset=UTF-8',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
        ];
        header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
        header('Content-Length: ' . (string) filesize($path));
        readfile($path);
        return true;
    }
}

if (is_dir($path) && is_file($path . DIRECTORY_SEPARATOR . 'index.php')) {
    require $path . DIRECTORY_SEPARATOR . 'index.php';
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
