<?php

/**
 * Compatibility router for the PHP built-in server.
 *
 * Resolves public/ or public_html/ so this works from the project root:
 *   php -S localhost:8000 -t public router.php
 *   php -S localhost:8000 -t public public/router.php
 */

$loadedPaths = false;
$search = [
    __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'paths.php',
    __DIR__ . DIRECTORY_SEPARATOR . 'public_html' . DIRECTORY_SEPARATOR . 'paths.php',
    __DIR__ . DIRECTORY_SEPARATOR . 'paths.php',
];

$cwd = getcwd();
if (is_string($cwd) && $cwd !== '') {
    $search[] = $cwd . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'paths.php';
    $search[] = $cwd . DIRECTORY_SEPARATOR . 'public_html' . DIRECTORY_SEPARATOR . 'paths.php';
    $search[] = $cwd . DIRECTORY_SEPARATOR . 'paths.php';
}

foreach ($search as $file) {
    if (is_file($file)) {
        require_once $file;
        $loadedPaths = true;
        break;
    }
}

if (!$loadedPaths) {
    throw new RuntimeException(
        "Failed opening required 'public/router.php'. The path handler could not find public/paths.php or public_html/paths.php from " . __DIR__ . '.'
    );
}

return require pentagon_locate_public_router();
