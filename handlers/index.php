<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Helpers\Path;
use App\Services\EnquiryService;
use App\Services\SubscriptionService;

$method = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('#^.*handlers/#', '', trim($requestPath, '/'));

// Callers (includes/enquiry-section.php, includes/footer.php, etc.) already
// build a fully-correct absolute redirect path via App\Helpers\Path::baseUrl()
// or REQUEST_URI, so it already accounts for a subdirectory install (e.g.
// /pentagon-quest/tours/12) — it just needs a same-site sanity check here,
// not another prefix on top (that would double it).
$resolveRedirect = static function (string $redirect): string {
    $isSameSitePath = str_starts_with($redirect, '/')
        && !str_starts_with($redirect, '//')
        && !str_contains($redirect, '://');

    return $isSameSitePath ? $redirect : Path::baseUrl();
};

if ($method === 'POST' && $path === 'contact') {
    $service = new EnquiryService();
    $result = $service->submit($_POST);

    if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    $redirect = $resolveRedirect($_POST['redirect'] ?? Path::baseUrl() . 'contact');
    $param = $result['success'] ? 'success=1' : 'error=1';
    $separator = str_contains($redirect, '?') ? '&' : '?';
    header('Location: ' . $redirect . $separator . $param);
    exit;
}

if ($method === 'POST' && $path === 'subscribe') {
    $service = new SubscriptionService();
    $result = $service->subscribe($_POST['email'] ?? '');

    if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    $redirect = $resolveRedirect($_POST['redirect'] ?? Path::baseUrl());
    $param = $result['success'] ? 'subscribed=1' : 'subscribe_error=1';
    $separator = str_contains($redirect, '?') ? '&' : '?';
    header('Location: ' . $redirect . $separator . $param);
    exit;
}

http_response_code(404);
echo 'Not found';
