<?php

require_once __DIR__ . '/../../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Helpers\Path;

Auth::requireAuth();
Session::start();

header('Content-Type: application/json');

// TinyMCE's images_upload_handler posts multipart with no custom fields easily,
// so we accept the CSRF token either as a header or a POST field.
$csrf = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (!Session::verifyCsrf($csrf)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit;
}

try {
    $path = Upload::store($_FILES['file'] ?? [], 'blog-content');
    if ($path === null) {
        throw new RuntimeException('No file uploaded.');
    }
    $base = \App\Helpers\Path::baseUrl();
    // TinyMCE expects { location: "url" }
    echo json_encode(['location' => $base . $path]);
} catch (\RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
