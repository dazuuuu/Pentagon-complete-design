<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Services\EnquiryService;
use App\Services\SubscriptionService;

$method = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('#^.*handlers/#', '', trim($requestPath, '/'));

// Site may be hosted in a subdirectory (e.g. /pentagon-quest/handlers/contact)
// rather than at the domain root — recover that prefix so root-absolute
// redirects below land back in the same subdirectory.
$basePath = '';
if (preg_match('#^(.*)/handlers/#', $requestPath, $m)) {
    $basePath = $m[1];
}

$resolveRedirect = static function (string $redirect) use ($basePath): string {
    return str_starts_with($redirect, '/') ? $basePath . $redirect : $redirect;
};

if ($method === 'POST' && $path === 'contact') {
    $service = new EnquiryService();
    $result = $service->submit($_POST);

    if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    $redirect = $resolveRedirect($_POST['redirect'] ?? '/contact.php');
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

    $redirect = $resolveRedirect($_POST['redirect'] ?? '/index.php');
    $param = $result['success'] ? 'subscribed=1' : 'subscribe_error=1';
    $separator = str_contains($redirect, '?') ? '&' : '?';
    header('Location: ' . $redirect . $separator . $param);
    exit;
}

http_response_code(404);
echo 'Not found';
