<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\ExperienceService;

Auth::requireAuth();
Session::start();

$service = new ExperienceService();
$message = Session::flash('message');
$error = Session::flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';

  try {
    if ($action === 'upload_images') {
      $service->addImages((int) $_POST['id'], $_FILES['images'] ?? []);
      Session::flash('message', 'Images uploaded.');
    } elseif ($action === 'delete_image') {
      $service->deleteImage((int) $_POST['image_id']);
      Session::flash('message', 'Image removed.');
    } else {
      $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'status' => $_POST['status'] ?? 'active',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
      ];

      if ($action === 'create') {
        $id = $service->create($data);
        if (!empty($_FILES['images']['name'][0] ?? '')) {
          $service->addImages($id, $_FILES['images']);
        }
        Session::flash('message', 'Experience created.');
      } elseif ($action === 'update') {
        $service->update((int) $_POST['id'], $data);
        Session::flash('message', 'Experience updated.');
      } elseif ($action === 'delete') {
        $service->delete((int) $_POST['id']);
        Session::flash('message', 'Experience deleted.');
      }
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
  }

  header('Location: experiences.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Experiences';
$currentAdminPage = 'experiences';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Manage Previous Experiences</h2>
  <p class="text-muted small">Shown on the homepage: the latest experience appears in the big feature box, older ones in the small boxes. Add multiple pictures per experience — they display as a slider.</p>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-6"><input class="form-control" name="title" placeholder="Title" required></div>
    <div class="col-md-2"><input class="form-control" name="sort_order" type="number" placeholder="Sort" value="0"></div>
    <div class="col-md-4"><button class="btn btn-success w-100">Add Experience</button></div>
    <div class="col-12"><textarea class="form-control" name="description" placeholder="Short description" rows="2"></textarea></div>
    <div class="col-12">
      <input class="form-control" type="file" name="images[]" accept="image/*" multiple>
      <div class="form-text">Upload one or more pictures — they'll show as a slider on the homepage card.</div>
    </div>
  </form>

  <table class="table table-sm align-middle">
    <thead><tr><th>Title</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($item['title']); ?>"></td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td class="d-flex gap-1">
            <input type="hidden" name="description" value="<?php echo htmlspecialchars($item['description'] ?? ''); ?>">
            <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this experience?');">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
          </td>
      </tr>
      <tr>
        <td colspan="3" class="pt-0">
          <details>
            <summary class="text-muted small">Description &amp; pictures (<?php echo count($service->getImages((int) $item['id'])); ?>)</summary>

            <div class="my-2">
              <label class="small text-muted mb-1 d-block">Description</label>
              <form method="POST" class="d-flex flex-column gap-2">
                <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                <input type="hidden" name="title" value="<?php echo htmlspecialchars($item['title']); ?>">
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($item['status']); ?>">
                <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
                <textarea class="form-control form-control-sm" name="description" rows="2" style="max-width:500px;"><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
                <button class="btn btn-sm btn-outline-primary align-self-start">Save Description</button>
              </form>
            </div>

            <label class="small text-muted mb-1 d-block">Pictures</label>
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
