<?php
/**
 * Pentagon Safaris — front controller / router for public-facing pages.
 *
 * Maps clean request paths to views/<page>/index.php templates. Works both
 * under the PHP built-in server (via router.php) and real Apache hosting
 * (via .htaccess mod_rewrite), since both eventually funnel unmatched
 * requests to this file.
 */

require_once __DIR__ . '/includes/bootstrap.php';

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$path = urldecode($path);
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

$routes = [
    '/' => __DIR__ . '/views/home/index.php',
    '/about' => __DIR__ . '/views/about/index.php',
    '/services' => __DIR__ . '/views/services/index.php',
    '/destinations' => __DIR__ . '/views/destinations/index.php',
    '/gallery' => __DIR__ . '/views/gallery/index.php',
    '/blog' => __DIR__ . '/views/blog/index.php',
    '/blog-detail' => __DIR__ . '/views/blog-detail/index.php',
    '/contact' => __DIR__ . '/views/contact/index.php',
    '/sitemap.xml' => __DIR__ . '/views/sitemap/index.php',
];

if (isset($routes[$path])) {
    require $routes[$path];
    return;
}

// Fallback: /<segment> => views/<segment>/index.php, if it exists.
$segment = trim($path, '/');
if ($segment !== '' && !str_contains($segment, '/') && !str_contains($segment, '..')) {
    $candidate = __DIR__ . '/views/' . $segment . '/index.php';
    if (is_file($candidate)) {
        require $candidate;
        return;
    }
}

// Nothing matched: real 404.
http_response_code(404);
$page_title = 'Page Not Found';
$page_description = 'The page you are looking for could not be found.';
$current_page = '';
$base_path = '';

$header = __DIR__ . '/includes/header.php';
$footer = __DIR__ . '/includes/footer.php';

if (is_file($header) && is_file($footer)) {
    include $header;
    ?>
    <section class="section-pad">
      <div class="container text-center">
        <h1 class="section-title-modern">404 — Page Not Found</h1>
        <p>Sorry, we couldn't find the page you were looking for.</p>
        <a href="<?php echo pq_url(); ?>" class="btn-hero btn-hero-primary">Back to Home</a>
      </div>
    </section>
    <?php
    include $footer;
} else {
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404 Not Found</title></head>'
        . '<body style="font-family: sans-serif; text-align: center; padding: 80px 20px;">'
        . '<h1>404 — Page Not Found</h1><p>Sorry, we couldn\'t find that page.</p>'
        . '<a href="/">Back to Home</a></body></html>';
}
