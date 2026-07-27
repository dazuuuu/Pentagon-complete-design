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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
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

  if (($_POST['action'] ?? '') === 'create') {
    $tourService->create($data);
    Session::flash('message', 'Tour created.');
  } elseif (($_POST['action'] ?? '') === 'update') {
    $tourService->update((int) $_POST['id'], $data);
    Session::flash('message', 'Tour updated.');
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $tourService->delete((int) $_POST['id']);
    Session::flash('message', 'Tour deleted.');
  }

  header('Location: tours.php');
  exit;
}

$items = $tourService->getAll();
$destinations = $destinationService->getAll();
$pageTitle = 'Tours';
$currentAdminPage = 'tours';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Manage Tours</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-3"><input class="form-control" name="title" placeholder="Title" required></div>
    <div class="col-md-2"><input class="form-control" name="country" placeholder="Country" required></div>
    <div class="col-md-2"><input class="form-control" name="tour_type" placeholder="Type" required></div>
    <div class="col-md-1"><input class="form-control" name="duration" placeholder="Duration" required></div>
    <div class="col-md-1"><input class="form-control" name="price" type="number" step="0.01" placeholder="Price" required></div>
    <div class="col-md-1"><input class="form-control" name="badge" placeholder="Badge"></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add Tour</button></div>
  </form>

  <table class="table table-sm align-middle">
    <thead><tr><th>Title</th><th>Country</th><th>Type</th><th>Duration</th><th>Price</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($item['title']); ?>"></td>
          <td><input class="form-control form-control-sm" name="country" value="<?php echo htmlspecialchars($item['country']); ?>"></td>
          <td><input class="form-control form-control-sm" name="tour_type" value="<?php echo htmlspecialchars($item['tour_type']); ?>"></td>
          <td><input class="form-control form-control-sm" name="duration" value="<?php echo htmlspecialchars($item['duration']); ?>"></td>
          <td><input class="form-control form-control-sm" name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($item['price']); ?>"></td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td class="d-flex gap-1">
            <input type="hidden" name="badge" value="<?php echo htmlspecialchars($item['badge'] ?? ''); ?>">
            <input type="hidden" name="description" value="<?php echo htmlspecialchars($item['description'] ?? ''); ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this tour?');">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
          </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
