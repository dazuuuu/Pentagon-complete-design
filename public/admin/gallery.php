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
?>
<div class="content-card p-4">
  <h2 class="mb-3">Manage Gallery</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-4"><input class="form-control" name="title" placeholder="Title" required></div>
    <div class="col-md-3"><input class="form-control" name="category" placeholder="Category" required></div>
    <div class="col-md-3"><input class="form-control" name="image_url" placeholder="Image URL (used if no file chosen)"></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add</button></div>
    <div class="col-md-12">
      <input class="form-control" type="file" name="images[]" accept="image/*" multiple>
      <div class="form-text">Select multiple pictures from your device — each one is added as its own gallery item under this title/category.</div>
    </div>
  </form>

  <table class="table table-sm">
    <thead><tr><th>Picture</th><th>Title</th><th>Category</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td>
            <?php if (str_starts_with($item['image_url'], 'assets/') || str_starts_with($item['image_url'], 'http')): ?>
              <img src="<?php echo (str_starts_with($item['image_url'], 'http') ? '' : '../') . htmlspecialchars($item['image_url']); ?>" style="width:70px;height:55px;object-fit:cover;border-radius:6px;">
            <?php endif; ?>
          </td>
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
