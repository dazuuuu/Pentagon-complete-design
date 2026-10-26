<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\OfferingService;
use App\Services\ServiceTierService;

Auth::requireAuth();
Session::start();

$offeringService = new OfferingService();
$tierService = new ServiceTierService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';

  if (str_starts_with($action, 'offering_')) {
    $data = [
      'title' => trim($_POST['title'] ?? ''),
      'description' => trim($_POST['description'] ?? ''),
      'status' => $_POST['status'] ?? 'active',
      'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    if ($action === 'offering_create') {
      $offeringService->create($data);
      Session::flash('message', 'Service offering created.');
    } elseif ($action === 'offering_update') {
      $offeringService->update((int) $_POST['id'], $data);
      Session::flash('message', 'Service offering updated.');
    } elseif ($action === 'offering_delete') {
      $offeringService->delete((int) $_POST['id']);
      Session::flash('message', 'Service offering deleted.');
    }
  } elseif (str_starts_with($action, 'tier_')) {
    $data = [
      'name' => trim($_POST['name'] ?? ''),
      'price' => (float) ($_POST['price'] ?? 0),
      'features' => trim($_POST['features'] ?? ''),
      'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
      'status' => $_POST['status'] ?? 'active',
      'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    if ($action === 'tier_create') {
      $tierService->create($data);
      Session::flash('message', 'Pricing tier created.');
    } elseif ($action === 'tier_update') {
      $tierService->update((int) $_POST['id'], $data);
      Session::flash('message', 'Pricing tier updated.');
    } elseif ($action === 'tier_delete') {
      $tierService->delete((int) $_POST['id']);
      Session::flash('message', 'Pricing tier deleted.');
    }
  }

  header('Location: services.php');
  exit;
}

$offerings = $offeringService->getAll();
$tiers = $tierService->getAll();
$pageTitle = 'Services';
$currentAdminPage = 'services';
include __DIR__ . '/includes/header.php';

echo pq_admin_alerts($message ?? null, $error ?? null);
echo pq_admin_sheet([
  'id' => 'offerings',
  'title' => 'Core Offerings',
  'singular' => 'Offering',
  'intro' => 'Click any row to edit it.',
  'empty' => 'No offerings yet.',
  'rows' => $offerings,
  'actions' => ['create' => 'offering_create', 'update' => 'offering_update', 'delete' => 'offering_delete'],
  'columns' => [
    ['label' => 'Title', 'value' => fn ($r) => $r['title']],
    ['label' => 'Description', 'class' => 'wrap', 'value' => fn ($r) => mb_strimwidth((string) $r['description'], 0, 140, '…')],
    ['label' => 'Order', 'class' => 'num', 'value' => fn ($r) => (int) $r['sort_order']],
    ['label' => 'Status', 'html' => true, 'value' => fn ($r) => pq_admin_status_badge($r['status'])],
  ],
  'fields' => [
    ['name' => 'title', 'label' => 'Title', 'required' => true],
    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea', 'rows' => 4, 'required' => true],
    ['name' => 'status', 'label' => 'Visibility', 'type' => 'status', 'col' => 6],
    ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'default' => 0, 'col' => 6],
  ],
]);
echo pq_admin_sheet([
  'id' => 'tiers',
  'title' => 'Pricing Tiers',
  'singular' => 'Tier',
  'empty' => 'No pricing tiers yet.',
  'rows' => $tiers,
  'actions' => ['create' => 'tier_create', 'update' => 'tier_update', 'delete' => 'tier_delete'],
  'columns' => [
    ['label' => 'Name', 'value' => fn ($r) => $r['name']],
    ['label' => 'Price', 'class' => 'num', 'value' => fn ($r) => number_format((float) $r['price'], 2)],
    ['label' => 'Features', 'class' => 'wrap', 'value' => fn ($r) => mb_strimwidth(str_replace(["\r\n", "\n"], ' · ', (string) $r['features']), 0, 120, '…')],
    ['label' => 'Popular', 'filter' => true, 'html' => true, 'value' => fn ($r) => $r['is_popular'] ? '<span class="sheet-yes"><i class="bi bi-star-fill"></i> Yes</span>' : '<span class="sheet-no">No</span>'],
    ['label' => 'Status', 'html' => true, 'value' => fn ($r) => pq_admin_status_badge($r['status'])],
  ],
  'fields' => [
    ['name' => 'name', 'label' => 'Tier name', 'required' => true, 'col' => 6, 'placeholder' => 'e.g. Classic'],
    ['name' => 'price', 'label' => 'Price / person', 'type' => 'price', 'required' => true, 'col' => 6],
    ['name' => 'features', 'label' => 'Features', 'type' => 'textarea', 'rows' => 6, 'placeholder' => 'One feature per line'],
    ['name' => 'status', 'label' => 'Visibility', 'type' => 'status', 'col' => 4],
    ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'default' => 0, 'col' => 4],
    ['name' => 'is_popular', 'label' => 'Most popular', 'type' => 'checkbox', 'col' => 4],
  ],
]);

include __DIR__ . '/includes/footer.php';
