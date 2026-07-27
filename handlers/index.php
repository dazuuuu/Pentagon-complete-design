<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Services\EnquiryService;
use App\Services\SubscriptionService;

$method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = preg_replace('#^handlers/#', '', $path);

if ($method === 'POST' && $path === 'contact') {
    $service = new EnquiryService();
    $result = $service->submit($_POST);

    if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    $redirect = $_POST['redirect'] ?? '../contact.php';
    $param = $result['success'] ? 'success=1' : 'error=1';
    header('Location: ' . $redirect . '?' . $param);
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

    $redirect = $_POST['redirect'] ?? '../index.php';
    $param = $result['success'] ? 'subscribed=1' : 'subscribe_error=1';
    header('Location: ' . $redirect . '?' . $param);
    exit;
}

http_response_code(404);
echo 'Not found';
