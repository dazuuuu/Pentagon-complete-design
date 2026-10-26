<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\GalleryService;

Auth::requireAuth();
Session::start();

$service = new GalleryService();
$message = Session::flash('message');
$error = Session::flash('error');

if (\App\Helpers\Upload::postTooLarge()) {
  Session::flash('error', \App\Helpers\Upload::postTooLargeMessage());
  header('Location: gallery.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';

  try {
    $data = [
      'title' => trim($_POST['title'] ?? ''),
      'category' => trim($_POST['category'] ?? ''),
      'image_url' => trim($_POST['image_url'] ?? ''),
      'sort_order' => (int) ($_POST['sort_order'] ?? 0),
      'status' => $_POST['status'] ?? 'active',
    ];

    if ($action === 'create') {
      if (!empty($_FILES['images']['name'][0] ?? '')) {
        $service->createMany($data, $_FILES['images']);
      } else {
        $service->create($data);
      }
      Session::flash('message', 'Gallery item(s) created.');
    } elseif ($action === 'update') {
      $paths = \App\Helpers\Upload::storeMany($_FILES['images'] ?? [], 'gallery');
      if ($paths !== []) {
        $old = $service->find((int) $_POST['id']);
        if ($old && str_starts_with((string) $old['image_url'], 'assets/images/uploads/')) {
          \App\Helpers\Upload::delete($old['image_url']);
        }
        $data['image_url'] = $paths[0];
      }
      $service->update((int) $_POST['id'], $data);
      Session::flash('message', 'Gallery item updated.');
    } elseif ($action === 'delete') {
      $service->delete((int) $_POST['id']);
      Session::flash('message', 'Gallery item deleted.');
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
  }

  header('Location: gallery.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Gallery';
$currentAdminPage = 'gallery';
include __DIR__ . '/includes/header.php';

echo pq_admin_alerts($message, $error);
echo pq_admin_sheet([
  'id' => 'gallery',
  'title' => 'Gallery',
  'singular' => 'Picture',
  'intro' => 'Click any row to edit it. When adding, choose several pictures at once to create one entry per picture.',
  'empty' => 'No gallery pictures yet.',
  'rows' => $items,
  'thumb' => fn ($r) => $r['image_url'] ?: null,
  'columns' => [
    ['label' => 'Title', 'value' => fn ($r) => $r['title']],
    ['label' => 'Category', 'filter' => true, 'value' => fn ($r) => $r['category']],
    ['label' => 'Order', 'class' => 'num', 'value' => fn ($r) => (int) $r['sort_order']],
    ['label' => 'Status', 'html' => true, 'value' => fn ($r) => pq_admin_status_badge($r['status'])],
  ],
  'fields' => [
    ['name' => 'images', 'label' => 'Picture(s)', 'type' => 'images', 'preview' => fn ($r) => $r['image_url'] ?: null],
    ['name' => 'title', 'label' => 'Title', 'required' => true, 'col' => 6],
    ['name' => 'category', 'label' => 'Category', 'required' => true, 'col' => 6],
    ['name' => 'image_url', 'label' => '…or image URL', 'placeholder' => 'Optional — an uploaded picture overrides this'],
    ['name' => 'status', 'label' => 'Visibility', 'type' => 'status', 'col' => 6],
    ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'default' => 0, 'col' => 6],
  ],
]);

include __DIR__ . '/includes/footer.php';
