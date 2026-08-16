<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Helpers\Path;
use App\Helpers\Session;
use App\Services\EnquiryService;
use App\Services\SubscriptionService;

$method = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
$path = preg_replace('#^.*handlers/#', '', trim($requestPath, '/'));
$path = $path === 'index.php' ? '' : $path;
if ($path === '' && !empty($_POST['_action'])) {
    $path = preg_replace('/[^a-z]/', '', (string) $_POST['_action']);
}

$resolveRedirect = static function (string $redirect): string {
    $isSameSitePath = str_starts_with($redirect, '/')
        && !str_starts_with($redirect, '//')
        && !str_contains($redirect, '://');

    return $isSameSitePath ? $redirect : Path::baseUrl();
};

if ($method !== 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit;
}

if (!Session::verifyCsrf($_POST['_csrf'] ?? null)) {
    $redirect = $resolveRedirect($_POST['redirect'] ?? Path::baseUrl());
    $separator = str_contains($redirect, '?') ? '&' : '?';
    header('Location: ' . $redirect . $separator . 'error=1');
    exit;
}

if ($path === 'contact') {
    $service = new EnquiryService();
    $result = $service->submit($_POST);

    if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    $redirect = $resolveRedirect($_POST['redirect'] ?? Path::baseUrl() . 'contact.php');
    $param = $result['success'] ? 'success=1' : 'error=1';
    $separator = str_contains($redirect, '?') ? '&' : '?';
    header('Location: ' . $redirect . $separator . $param);
    exit;
}

if ($path === 'subscribe') {
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
