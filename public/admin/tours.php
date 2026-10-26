<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\TourService;
use App\Services\DestinationService;

Auth::requireAuth();
Session::start();

$tourService = new TourService();
$destinationService = new DestinationService();
$message = Session::flash('message');
$error = Session::flash('error');

if (\App\Helpers\Upload::postTooLarge()) {
  Session::flash('error', \App\Helpers\Upload::postTooLargeMessage());
  header('Location: tours.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';

  try {
    if ($action === 'upload_images') {
      $tourService->addImages((int) $_POST['id'], $_FILES['images'] ?? []);
      Session::flash('message', 'Images uploaded.');
    } elseif ($action === 'delete_image') {
      $tourService->deleteImage((int) $_POST['image_id']);
      Session::flash('message', 'Image removed.');
    } elseif ($action === 'update_cover') {
      $tourService->setCoverImage((int) $_POST['id'], $_FILES['cover_image'] ?? []);
      Session::flash('message', 'Cover image updated.');
    } elseif ($action === 'update_description') {
      $tour = $tourService->find((int) $_POST['id']);
      if ($tour) {
        $tourService->update((int) $_POST['id'], array_merge($tour, ['description' => trim($_POST['description'] ?? '')]));
      }
      Session::flash('message', 'Description updated.');
    } else {
      $data = [
        'title' => trim($_POST['title'] ?? ''),
        'destination_id' => (int) ($_POST['destination_id'] ?? 0) ?: null,
        'country' => trim($_POST['country'] ?? ''),
        'tour_type' => trim($_POST['tour_type'] ?? ''),
        'duration' => trim($_POST['duration'] ?? ''),
        'price' => (float) ($_POST['price'] ?? 0),
        'badge' => trim($_POST['badge'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'status' => $_POST['status'] ?? 'active',
      ];

      if ($action === 'create') {
        $id = $tourService->create($data);
        if (!empty($_FILES['cover_image']['name'] ?? '')) {
          $tourService->setCoverImage($id, $_FILES['cover_image']);
        }
        if (!empty($_FILES['images']['name'][0] ?? '')) {
          $tourService->addImages($id, $_FILES['images']);
        }
        Session::flash('message', 'Tour created.');
      } elseif ($action === 'update') {
        $tourService->update((int) $_POST['id'], $data);
        $tourService->setCoverImage((int) $_POST['id'], $_FILES['cover_image'] ?? []);
        $tourService->addImages((int) $_POST['id'], $_FILES['images'] ?? []);
        Session::flash('message', 'Tour updated.');
      } elseif ($action === 'delete') {
        $tourService->delete((int) $_POST['id']);
        Session::flash('message', 'Tour deleted.');
      }
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
  }

  $reopen = in_array($action, ['update', 'delete_image'], true) ? (int) ($_POST['id'] ?? 0) : 0;
  header('Location: tours.php' . ($reopen ? '?edit=' . $reopen : ''));
  exit;
}

$items = $tourService->getAll();
$destinations = $destinationService->getAll();
$destinationOptions = ['' => '— None —'];
$destinationNames = [];
foreach ($destinations as $d) {
  $destinationOptions[(string) $d['id']] = $d['name'] . ' (' . $d['country'] . ')';
  $destinationNames[(int) $d['id']] = $d['name'];
}
$pageTitle = 'Tours';
$currentAdminPage = 'tours';
include __DIR__ . '/includes/header.php';

echo pq_admin_alerts($message, $error);
echo pq_admin_sheet([
  'id' => 'tours',
  'title' => 'Tours',
  'singular' => 'Tour',
  'intro' => 'Click any row to edit it.',
  'empty' => 'No tours yet.',
  'rows' => $items,
  'thumb' => fn ($r) => $r['image_url'] ?: null,
  'gallery' => fn ($r) => array_map(fn ($i) => ['id' => $i['id'], 'path' => $i['image_path']], $tourService->getImages((int) $r['id'])),
  'columns' => [
    ['label' => 'Title', 'value' => fn ($r) => $r['title']],
    ['label' => 'Destination', 'filter' => true, 'value' => fn ($r) => $destinationNames[(int) ($r['destination_id'] ?? 0)] ?? '—'],
    ['label' => 'Country', 'filter' => true, 'value' => fn ($r) => $r['country']],
    ['label' => 'Type', 'filter' => true, 'value' => fn ($r) => $r['tour_type']],
    ['label' => 'Duration', 'value' => fn ($r) => $r['duration']],
    ['label' => 'Price', 'class' => 'num', 'value' => fn ($r) => number_format((float) $r['price'], 2)],
    ['label' => 'Badge', 'value' => fn ($r) => $r['badge'] ?: '—'],
    ['label' => 'Status', 'html' => true, 'value' => fn ($r) => pq_admin_status_badge($r['status'])],
  ],
  'fields' => [
    ['name' => 'title', 'label' => 'Title', 'required' => true],
    ['name' => 'destination_id', 'label' => 'Destination', 'type' => 'select', 'options' => $destinationOptions, 'col' => 6],
    ['name' => 'country', 'label' => 'Country', 'required' => true, 'col' => 6],
    ['name' => 'tour_type', 'label' => 'Type', 'required' => true, 'col' => 4, 'placeholder' => 'e.g. Wildlife'],
    ['name' => 'duration', 'label' => 'Duration', 'required' => true, 'col' => 4, 'placeholder' => 'e.g. 5 Days'],
    ['name' => 'price', 'label' => 'Price', 'type' => 'price', 'required' => true, 'col' => 4],
    ['name' => 'badge', 'label' => 'Badge', 'col' => 6, 'placeholder' => 'Optional, e.g. Best Seller'],
    ['name' => 'status', 'label' => 'Visibility', 'type' => 'status', 'col' => 6],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 5, 'placeholder' => "Shown on the tour's details page"],
    ['name' => 'cover_image', 'label' => 'Cover image', 'type' => 'image', 'preview' => fn ($r) => $r['image_url'] ?: null],
    ['name' => 'image_url', 'label' => '…or cover image URL', 'placeholder' => 'Optional — an uploaded image overrides this'],
    ['name' => 'images', 'label' => 'Add gallery pictures', 'type' => 'images'],
  ],
]);

include __DIR__ . '/includes/footer.php';
