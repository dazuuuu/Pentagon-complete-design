<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\DestinationService;

Auth::requireAuth();
Session::start();

$service = new DestinationService();
$message = Session::flash('message');
$error = Session::flash('error');

if (\App\Helpers\Upload::postTooLarge()) {
  Session::flash('error', \App\Helpers\Upload::postTooLargeMessage());
  header('Location: destinations.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!Session::verifyCsrf($_POST['csrf'] ?? '')) {
    Session::flash('error', 'Invalid request.');
    header('Location: destinations.php');
    exit;
  }

  $action = $_POST['action'] ?? '';

  try {
    if ($action === 'upload_images') {
      $service->addImages((int) $_POST['id'], $_FILES['images'] ?? []);
      Session::flash('message', 'Images uploaded.');
    } elseif ($action === 'delete_image') {
      $service->deleteImage((int) $_POST['image_id']);
      Session::flash('message', 'Image removed.');
    } elseif ($action === 'update_cover') {
      $service->setCoverImage((int) $_POST['id'], $_FILES['cover_image'] ?? []);
      Session::flash('message', 'Cover image updated.');
    } elseif ($action === 'update_description') {
      $destination = $service->find((int) $_POST['id']);
      if ($destination) {
        $service->update((int) $_POST['id'], array_merge($destination, ['description' => trim($_POST['description'] ?? '')]));
      }
      Session::flash('message', 'Description updated.');
    } else {
      $data = [
        'name' => trim($_POST['name'] ?? ''),
        'country' => trim($_POST['country'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
      ];

      if ($action === 'create') {
        $id = $service->create($data);
        if (!empty($_FILES['cover_image']['name'] ?? '')) {
          $service->setCoverImage($id, $_FILES['cover_image']);
        }
        if (!empty($_FILES['images']['name'][0] ?? '')) {
          $service->addImages($id, $_FILES['images']);
        }
        Session::flash('message', 'Destination created.');
      } elseif ($action === 'update') {
        $service->update((int) $_POST['id'], $data);
        $service->setCoverImage((int) $_POST['id'], $_FILES['cover_image'] ?? []);
        $service->addImages((int) $_POST['id'], $_FILES['images'] ?? []);
        Session::flash('message', 'Destination updated.');
      } elseif ($action === 'delete') {
        $service->delete((int) $_POST['id']);
        Session::flash('message', 'Destination deleted.');
      }
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
  }

  $reopen = in_array($action, ['update', 'delete_image'], true) ? (int) ($_POST['id'] ?? 0) : 0;
  header('Location: destinations.php' . ($reopen ? '?edit=' . $reopen : ''));
  exit;
}

$items = $service->getAll();
$pageTitle = 'Destinations';
$currentAdminPage = 'destinations';
include __DIR__ . '/includes/header.php';

echo pq_admin_alerts($message, $error);
echo pq_admin_sheet([
  'id' => 'destinations',
  'title' => 'Destinations',
  'singular' => 'Destination',
  'intro' => 'Click any row to edit it. Featured destinations show on the homepage.',
  'empty' => 'No destinations yet.',
  'rows' => $items,
  'thumb' => fn ($r) => (str_starts_with((string) $r['image_url'], 'assets/') || str_starts_with((string) $r['image_url'], 'http')) ? $r['image_url'] : null,
  'gallery' => fn ($r) => array_map(fn ($i) => ['id' => $i['id'], 'path' => $i['image_path']], $service->getImages((int) $r['id'])),
  'columns' => [
    ['label' => 'Name', 'value' => fn ($r) => $r['name']],
    ['label' => 'Country', 'filter' => true, 'value' => fn ($r) => $r['country']],
    ['label' => 'Featured', 'filter' => true, 'html' => true, 'value' => fn ($r) => $r['is_featured'] ? '<span class="sheet-yes"><i class="bi bi-star-fill"></i> Yes</span>' : '<span class="sheet-no">No</span>'],
    ['label' => 'Pictures', 'class' => 'num', 'value' => fn ($r) => count($service->getImages((int) $r['id']))],
    ['label' => 'Order', 'class' => 'num', 'value' => fn ($r) => (int) $r['sort_order']],
    ['label' => 'Status', 'html' => true, 'value' => fn ($r) => pq_admin_status_badge($r['status'])],
  ],
  'fields' => [
    ['name' => 'name', 'label' => 'Name', 'required' => true, 'col' => 6],
    ['name' => 'country', 'label' => 'Country', 'required' => true, 'col' => 6],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 5, 'placeholder' => "Shown on the destination's details page"],
    ['name' => 'cover_image', 'label' => 'Cover image', 'type' => 'image', 'preview' => fn ($r) => (str_starts_with((string) $r['image_url'], 'assets/') || str_starts_with((string) $r['image_url'], 'http')) ? $r['image_url'] : null],
    ['name' => 'image_url', 'label' => '…or cover image URL / CSS colour', 'placeholder' => 'Optional — an uploaded image overrides this'],
    ['name' => 'images', 'label' => 'Add gallery pictures', 'type' => 'images'],
    ['name' => 'status', 'label' => 'Visibility', 'type' => 'status', 'col' => 4],
    ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'default' => 0, 'col' => 4],
    ['name' => 'is_featured', 'label' => 'Featured on homepage', 'type' => 'checkbox', 'col' => 4],
  ],
]);

include __DIR__ . '/includes/footer.php';
