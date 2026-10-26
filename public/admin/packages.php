<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\FeaturedPackageService;

Auth::requireAuth();
Session::start();

$service = new FeaturedPackageService();
$message = Session::flash('message');
$error = Session::flash('error');
$schemaMissing = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $data = [
        'hotel' => trim($_POST['hotel'] ?? ''),
        'meal' => trim($_POST['meal'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'two_nights_price' => trim($_POST['two_nights_price'] ?? ''),
        'three_nights_price' => trim($_POST['three_nights_price'] ?? ''),
        'status' => $_POST['status'] ?? 'active',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    try {
        if ($action === 'create') {
            $service->create($data);
            Session::flash('message', 'Package created.');
        } elseif ($action === 'update') {
            $service->update((int) $_POST['id'], $data);
            Session::flash('message', 'Package updated.');
        } elseif ($action === 'delete') {
            $service->delete((int) $_POST['id']);
            Session::flash('message', 'Package deleted.');
        }
    } catch (Throwable $e) {
        Session::flash('error', $e->getMessage());
    }

    header('Location: packages.php');
    exit;
}

try {
    $items = $service->getAll();
} catch (Throwable $e) {
    $items = [];
    $schemaMissing = true;
    $error = $error ?: 'Package tables are not ready yet. Run update 025_featured_packages.sql from the Updates page.';
}

$pageTitle = 'Packages';
$currentAdminPage = 'packages';
include __DIR__ . '/includes/header.php';

echo pq_admin_alerts($message, $error);
if (!empty($schemaMissing)) {
    echo '<div class="content-card p-4"><h5>Package update required</h5><p class="text-muted">Open Updates and run <code>025_featured_packages.sql</code>.</p><a class="btn btn-primary" href="updates.php">Open Updates</a></div>';
} else {
    echo pq_admin_sheet([
      'id' => 'packages',
      'title' => 'Featured Packages',
      'singular' => 'Package',
      'intro' => 'Rows of the rate table in the homepage "Featured packages" section. Click a row to edit it.',
      'empty' => 'No packages yet.',
      'rows' => $items,
      'columns' => [
        ['label' => 'Package', 'value' => fn ($r) => $r['hotel']],
        ['label' => 'Meal', 'filter' => true, 'value' => fn ($r) => $r['meal'] ?: '—'],
        ['label' => 'Location', 'filter' => true, 'value' => fn ($r) => $r['location'] ?: '—'],
        ['label' => '2 Nights', 'class' => 'num', 'value' => fn ($r) => $r['two_nights_price'] ?: '—'],
        ['label' => '3 Nights', 'class' => 'num', 'value' => fn ($r) => $r['three_nights_price'] ?: '—'],
        ['label' => 'Order', 'class' => 'num', 'value' => fn ($r) => (int) $r['sort_order']],
        ['label' => 'Status', 'html' => true, 'value' => fn ($r) => pq_admin_status_badge($r['status'])],
      ],
      'fields' => [
        ['name' => 'hotel', 'label' => 'Hotel / package', 'required' => true],
        ['name' => 'meal', 'label' => 'Meal', 'col' => 6, 'placeholder' => 'e.g. Half board'],
        ['name' => 'location', 'label' => 'Location', 'col' => 6],
        ['name' => 'two_nights_price', 'label' => '2 nights price', 'col' => 6],
        ['name' => 'three_nights_price', 'label' => '3 nights price', 'col' => 6],
        ['name' => 'status', 'label' => 'Visibility', 'type' => 'status', 'col' => 6],
        ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'default' => 0, 'col' => 6],
      ],
    ]);
}

include __DIR__ . '/includes/footer.php';
