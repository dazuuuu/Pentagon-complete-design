<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\TestimonialService;

Auth::requireAuth();
Session::start();

$service = new TestimonialService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $data = [
    'author_name' => trim($_POST['author_name'] ?? ''),
    'author_location' => trim($_POST['author_location'] ?? ''),
    'quote' => trim($_POST['quote'] ?? ''),
    'accent_color' => $_POST['accent_color'] ?? 'gold',
    'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    'status' => $_POST['status'] ?? 'active',
  ];

  if (($_POST['action'] ?? '') === 'create') {
    $service->create($data);
    Session::flash('message', 'Testimonial created.');
  } elseif (($_POST['action'] ?? '') === 'update') {
    $service->update((int) $_POST['id'], $data);
    Session::flash('message', 'Testimonial updated.');
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $service->delete((int) $_POST['id']);
    Session::flash('message', 'Testimonial deleted.');
  }

  header('Location: testimonials.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Testimonials';
$currentAdminPage = 'testimonials';
include __DIR__ . '/includes/header.php';

echo pq_admin_alerts($message, $error ?? null);
echo pq_admin_sheet([
  'id' => 'testimonials',
  'title' => 'Testimonials',
  'singular' => 'Testimonial',
  'intro' => 'Click any row to edit it.',
  'empty' => 'No testimonials yet.',
  'rows' => $items,
  'columns' => [
    ['label' => 'Author', 'value' => fn ($r) => $r['author_name']],
    ['label' => 'Location', 'value' => fn ($r) => $r['author_location'] ?: '—'],
    ['label' => 'Quote', 'class' => 'wrap', 'value' => fn ($r) => mb_strimwidth((string) $r['quote'], 0, 140, '…')],
    ['label' => 'Accent', 'filter' => true, 'value' => fn ($r) => ucfirst((string) $r['accent_color'])],
    ['label' => 'Status', 'html' => true, 'value' => fn ($r) => pq_admin_status_badge($r['status'])],
  ],
  'fields' => [
    ['name' => 'author_name', 'label' => 'Author', 'required' => true, 'col' => 6],
    ['name' => 'author_location', 'label' => 'Location', 'col' => 6],
    ['name' => 'quote', 'label' => 'Quote', 'type' => 'textarea', 'rows' => 5, 'required' => true],
    ['name' => 'accent_color', 'label' => 'Accent colour', 'type' => 'select', 'options' => ['gold' => 'Gold', 'green' => 'Green', 'earth' => 'Earth', 'charcoal' => 'Charcoal'], 'col' => 4],
    ['name' => 'status', 'label' => 'Visibility', 'type' => 'status', 'col' => 4],
    ['name' => 'sort_order', 'label' => 'Order', 'type' => 'number', 'default' => 0, 'col' => 4],
  ],
]);

include __DIR__ . '/includes/footer.php';
