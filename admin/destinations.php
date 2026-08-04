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
        Session::flash('message', 'Destination updated.');
      } elseif ($action === 'delete') {
        $service->delete((int) $_POST['id']);
        Session::flash('message', 'Destination deleted.');
      }
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
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

  <form method="POST" enctype="multipart/form-data" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-3"><input class="form-control" name="name" placeholder="Name" required></div>
    <div class="col-md-2"><input class="form-control" name="country" placeholder="Country" required></div>
    <div class="col-md-3"><input class="form-control" name="image_url" placeholder="Image URL / CSS color (optional)"></div>
    <div class="col-md-1"><input class="form-control" name="sort_order" type="number" value="0"></div>
    <div class="col-md-1 form-check pt-2"><input class="form-check-input" type="checkbox" name="is_featured" id="featured"><label for="featured" class="form-check-label">Featured</label></div>
    <div class="col-md-1"><button class="btn btn-success w-100">Add</button></div>
    <div class="col-12"><textarea class="form-control" name="description" placeholder="Description (shown on the destination's details page)" rows="2"></textarea></div>
    <div class="col-md-6">
      <label class="form-label small text-muted mb-0">1. Cover image</label>
      <input class="form-control" type="file" name="cover_image" accept="image/*">
      <div class="form-text">Uploading here overrides the URL/color field above.</div>
    </div>
    <div class="col-md-6">
      <label class="form-label small text-muted mb-0">2. Additional pictures (gallery)</label>
      <input class="form-control" type="file" name="images[]" accept="image/*" multiple>
      <div class="form-text">Shown on the destination's details page. JPG, PNG, GIF, or WEBP — max 8MB each.</div>
    </div>
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
            <input type="hidden" name="description" value="<?php echo htmlspecialchars($item['description'] ?? ''); ?>">
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
      <tr>
        <td colspan="5" class="pt-0">
          <details>
            <summary class="text-muted small">Description &amp; photos (<?php echo count($service->getImages((int) $item['id'])); ?> extra picture<?php echo count($service->getImages((int) $item['id'])) === 1 ? '' : 's'; ?>)</summary>

            <div class="row g-3 my-1">
              <div class="col-md-6">
                <label class="small text-muted mb-1 d-block">Description</label>
                <form method="POST" class="d-flex flex-column gap-2">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="update_description">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <textarea class="form-control form-control-sm" name="description" rows="3"><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                  <button class="btn btn-sm btn-outline-primary align-self-start">Save Description</button>
                </form>
              </div>
              <div class="col-md-6">
                <label class="small text-muted mb-1 d-block">Cover image</label>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <?php if (!empty($item['image_url']) && (str_starts_with($item['image_url'], 'assets/') || str_starts_with($item['image_url'], 'http'))): ?>
                  <img src="<?php echo (str_starts_with($item['image_url'], 'http') ? '' : '../') . htmlspecialchars($item['image_url']); ?>" style="width:90px;height:70px;object-fit:cover;border-radius:6px;">
                  <?php endif; ?>
                </div>
                <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="update_cover">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <input class="form-control form-control-sm" type="file" name="cover_image" accept="image/*" style="max-width:260px;">
                  <button class="btn btn-sm btn-outline-primary">Replace</button>
                </form>
              </div>
            </div>

            <label class="small text-muted mb-1 d-block">Additional pictures (gallery)</label>
            <div class="d-flex flex-wrap gap-2 my-2">
              <?php foreach ($service->getImages((int) $item['id']) as $image): ?>
              <div class="position-relative">
                <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" style="width:90px;height:70px;object-fit:cover;border-radius:6px;">
                <form method="POST" class="position-absolute top-0 end-0" onsubmit="return confirm('Remove this picture?');">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="delete_image">
                  <input type="hidden" name="image_id" value="<?php echo (int) $image['id']; ?>">
                  <button class="btn btn-sm btn-danger py-0 px-1">&times;</button>
                </form>
              </div>
              <?php endforeach; ?>
            </div>
            <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
              <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
              <input type="hidden" name="action" value="upload_images">
              <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
              <input class="form-control form-control-sm" type="file" name="images[]" accept="image/*" multiple style="max-width:320px;">
              <button class="btn btn-sm btn-outline-success">Upload</button>
            </form>
          </details>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
