<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\GalleryService;

Auth::requireAuth();
Session::start();

$service = new GalleryService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $data = [
    'title' => trim($_POST['title'] ?? ''),
    'category' => trim($_POST['category'] ?? ''),
    'image_url' => trim($_POST['image_url'] ?? ''),
    'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    'status' => $_POST['status'] ?? 'active',
  ];

  if (($_POST['action'] ?? '') === 'create') {
    $service->create($data);
    Session::flash('message', 'Gallery item created.');
  } elseif (($_POST['action'] ?? '') === 'update') {
    $service->update((int) $_POST['id'], $data);
    Session::flash('message', 'Gallery item updated.');
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $service->delete((int) $_POST['id']);
    Session::flash('message', 'Gallery item deleted.');
  }

  header('Location: gallery.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Gallery';
$currentAdminPage = 'gallery';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Manage Gallery</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-4"><input class="form-control" name="title" placeholder="Title" required></div>
    <div class="col-md-3"><input class="form-control" name="category" placeholder="Category" required></div>
    <div class="col-md-3"><input class="form-control" name="image_url" placeholder="Image URL"></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add</button></div>
  </form>

  <table class="table table-sm">
    <thead><tr><th>Title</th><th>Category</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($item['title']); ?>"></td>
          <td><input class="form-control form-control-sm" name="category" value="<?php echo htmlspecialchars($item['category']); ?>"></td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td class="d-flex gap-1">
            <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($item['image_url']); ?>">
            <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this item?');">
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
