<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Controllers\Api\DestinationController;
use App\Controllers\Api\TourController;
use App\Controllers\Api\GalleryController;
use App\Controllers\Api\TestimonialController;
use App\Controllers\Api\ContactController;
use App\Controllers\Api\SubscriptionController;
use App\Helpers\Response;

$method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$path = preg_replace('#^api/#', '', $path);

$routes = [
    'GET' => [
        'destinations' => [DestinationController::class, 'index'],
        'tours' => [TourController::class, 'index'],
        'gallery' => [GalleryController::class, 'index'],
        'testimonials' => [TestimonialController::class, 'index'],
    ],
    'POST' => [
        'contact' => [ContactController::class, 'store'],
        'subscribe' => [SubscriptionController::class, 'store'],
    ],
];

if (!isset($routes[$method][$path])) {
    Response::json(['success' => false, 'message' => 'Not found'], 404);
}

[$class, $action] = $routes[$method][$path];
$controller = new $class();
$controller->$action();
