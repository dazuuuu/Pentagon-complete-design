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
        Session::flash('message', 'Tour updated.');
      } elseif ($action === 'delete') {
        $tourService->delete((int) $_POST['id']);
        Session::flash('message', 'Tour deleted.');
      }
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
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
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-3"><input class="form-control" name="title" placeholder="Title" required></div>
    <div class="col-md-2"><input class="form-control" name="country" placeholder="Country" required></div>
    <div class="col-md-2"><input class="form-control" name="tour_type" placeholder="Type" required></div>
    <div class="col-md-1"><input class="form-control" name="duration" placeholder="Duration" required></div>
    <div class="col-md-1"><input class="form-control" name="price" type="number" step="0.01" placeholder="Price" required></div>
    <div class="col-md-1"><input class="form-control" name="badge" placeholder="Badge"></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add Tour</button></div>
    <div class="col-12"><textarea class="form-control" name="description" placeholder="Description (shown on the tour's details page)" rows="2"></textarea></div>
    <div class="col-md-6">
      <label class="form-label small text-muted mb-0">1. Cover image</label>
      <input class="form-control" type="file" name="cover_image" accept="image/*">
      <input class="form-control mt-1" name="image_url" placeholder="...or paste a cover image URL instead">
    </div>
    <div class="col-md-6">
      <label class="form-label small text-muted mb-0">2. Additional pictures (gallery)</label>
      <input class="form-control" type="file" name="images[]" accept="image/*" multiple>
      <div class="form-text">Shown on the tour's details page. JPG, PNG, GIF, or WEBP — max 8MB each.</div>
    </div>
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
            <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>">
            <input type="hidden" name="destination_id" value="<?php echo htmlspecialchars((string) ($item['destination_id'] ?? '')); ?>">
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
      <tr>
        <td colspan="7" class="pt-0">
          <details>
            <summary class="text-muted small">Description &amp; photos (<?php echo count($tourService->getImages((int) $item['id'])); ?> extra picture<?php echo count($tourService->getImages((int) $item['id'])) === 1 ? '' : 's'; ?>)</summary>

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
                  <?php if (!empty($item['image_url'])): ?>
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
              <?php foreach ($tourService->getImages((int) $item['id']) as $image): ?>
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
