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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!Session::verifyCsrf($_POST['csrf'] ?? '')) {
    Session::flash('error', 'Invalid request.');
    header('Location: destinations.php');
    exit;
  }

  $data = [
    'name' => trim($_POST['name'] ?? ''),
    'country' => trim($_POST['country'] ?? ''),
    'image_url' => trim($_POST['image_url'] ?? ''),
    'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
    'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    'status' => $_POST['status'] ?? 'active',
  ];

  if (($_POST['action'] ?? '') === 'create') {
    $service->create($data);
    Session::flash('message', 'Destination created.');
  } elseif (($_POST['action'] ?? '') === 'update') {
    $service->update((int) $_POST['id'], $data);
    Session::flash('message', 'Destination updated.');
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $service->delete((int) $_POST['id']);
    Session::flash('message', 'Destination deleted.');
  }

  header('Location: destinations.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Destinations';
$currentAdminPage = 'destinations';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Manage Destinations</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-3"><input class="form-control" name="name" placeholder="Name" required></div>
    <div class="col-md-2"><input class="form-control" name="country" placeholder="Country" required></div>
    <div class="col-md-3"><input class="form-control" name="image_url" placeholder="Image URL / CSS color"></div>
    <div class="col-md-1"><input class="form-control" name="sort_order" type="number" value="0"></div>
    <div class="col-md-1 form-check pt-2"><input class="form-check-input" type="checkbox" name="is_featured" id="featured"><label for="featured" class="form-check-label">Featured</label></div>
    <div class="col-md-1"><button class="btn btn-success w-100">Add</button></div>
  </form>

  <table class="table table-sm">
    <thead><tr><th>Name</th><th>Country</th><th>Featured</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item['name']); ?>"></td>
          <td><input class="form-control form-control-sm" name="country" value="<?php echo htmlspecialchars($item['country']); ?>"></td>
          <td class="text-center"><input type="checkbox" name="is_featured" <?php echo $item['is_featured'] ? 'checked' : ''; ?>></td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td class="d-flex gap-1">
            <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>">
            <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this destination?');">
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
