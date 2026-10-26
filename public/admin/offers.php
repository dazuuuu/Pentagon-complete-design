<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\OfferService;

Auth::requireAuth();
Session::start();

$service = new OfferService();
$message = Session::flash('message');

if (\App\Helpers\Upload::postTooLarge()) {
  Session::flash('error', \App\Helpers\Upload::postTooLargeMessage());
  header('Location: offers.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';

  if ($action === 'create' || $action === 'update') {
    $data = [
      'title' => trim($_POST['title'] ?? ''),
      'description' => trim($_POST['description'] ?? ''),
      'badge' => trim($_POST['badge'] ?? ''),
      'is_banner' => isset($_POST['is_banner']) ? 1 : 0,
      'end_time' => !empty($_POST['end_time']) ? $_POST['end_time'] : null,
      'link_url' => trim($_POST['link_url'] ?? ''),
      'status' => $_POST['status'] ?? 'active',
      'sort_order' => (int) ($_POST['sort_order'] ?? 0),
      'image_url' => trim($_POST['image_url'] ?? ''),
      'video_url' => trim($_POST['video_url'] ?? ''),
    ];

    try {
      if ($action === 'create') {
        $service->create($data, $_FILES['offer_image'] ?? [], $_FILES['offer_video'] ?? []);
        Session::flash('message', 'Ad/Campaign created.');
      } else {
        $service->update((int) $_POST['id'], $data, $_FILES['offer_image'] ?? [], $_FILES['offer_video'] ?? []);
        Session::flash('message', 'Ad/Campaign updated.');
      }
    } catch (\RuntimeException $e) {
      Session::flash('error', $e->getMessage());
    }
  } elseif ($action === 'delete') {
    $service->delete((int) $_POST['id']);
    Session::flash('message', 'Ad/Campaign deleted.');
  }

  header('Location: offers.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Ads / Campaigns';
$currentAdminPage = 'offers';
include __DIR__ . '/includes/header.php';

echo pq_admin_alerts($message, $error ?? null);
echo pq_admin_sheet([
  'id' => 'offers',
  'title' => 'Ads & Campaigns',
  'singular' => 'Ad',
  'intro' => 'Click any row to edit it. Turn on "Site banner" to show an ad in the banner across the top of the site.',
  'empty' => 'No ads yet.',
  'rows' => $items,
  'thumb' => fn ($r) => $r['image_url'] ?: null,
  'columns' => [
    ['label' => 'Title', 'value' => fn ($r) => $r['title']],
    ['label' => 'Badge', 'value' => fn ($r) => $r['badge'] ?: '—'],
    ['label' => 'Banner', 'filter' => true, 'html' => true, 'value' => fn ($r) => $r['is_banner'] ? '<span class="sheet-yes">Yes</span>' : '<span class="sheet-no">No</span>'],
    ['label' => 'Video', 'html' => true, 'value' => fn ($r) => !empty($r['video_url']) ? '<i class="bi bi-camera-video-fill text-primary"></i>' : '<span class="sheet-no">—</span>'],
    ['label' => 'Ends', 'value' => fn ($r) => $r['end_time'] ? date('Y/m/d H:i', strtotime($r['end_time'])) : '—'],
    ['label' => 'Link', 'value' => fn ($r) => $r['link_url'] ?: '—'],
    ['label' => 'Order', 'class' => 'num', 'value' => fn ($r) => (int) $r['sort_order']],
    ['label' => 'Status', 'html' => true, 'value' => fn ($r) => pq_admin_status_badge($r['status'])],
  ],
  'fields' => [
    ['name' => 'title', 'label' => 'Ad title', 'required' => true],
    ['name' => 'badge', 'label' => 'Badge', 'col' => 6, 'placeholder' => 'e.g. 15% OFF'],
    ['name' => 'end_time', 'label' => 'Ends at', 'type' => 'datetime', 'col' => 6],
    ['name' => 'description', 'label' => 'Short description', 'type' => 'textarea', 'rows' => 3],
    ['name' => 'link_url', 'label' => 'Link', 'type' => 'url', 'placeholder' => 'https://…'],
    ['name' => 'offer_image', 'label' => 'Image', 'type' => 'image', 'preview' => fn ($r) => $r['image_url'] ?: null],
    ['name' => 'image_url', 'label' => '…or image URL', 'placeholder' => 'Optional — an uploaded image overrides this'],
    ['name' => 'offer_video', 'label' => 'Video (optional)', 'type' => 'video'],
    ['name' => 'video_url', 'type' => 'hidden'],
    ['name' => 'status', 'label' => 'Visibility', 'type' => 'status', 'col' => 4],
    ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'default' => 0, 'col' => 4],
    ['name' => 'is_banner', 'label' => 'Site banner', 'type' => 'checkbox', 'col' => 4],
  ],
]);

include __DIR__ . '/includes/footer.php';
