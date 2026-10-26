<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\LinkPreviewService;

Auth::requireAuth();
Session::start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Session::verifyCsrf($_POST['csrf'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid request.']);
    exit;
}

$url = trim($_POST['url'] ?? '');
if ($url === '') {
    http_response_code(400);
    echo json_encode(['error' => 'A URL is required.']);
    exit;
}

try {
    $service = new LinkPreviewService();
    $preview = $service->fetch($url);
    echo json_encode([
        'url' => $preview['url'] ?? $url,
        'title' => $preview['title'] ?? '',
        'description' => $preview['description'] ?? '',
        'image_url' => $preview['image_url'] ?? '',
    ]);
} catch (\RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
