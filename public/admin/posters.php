<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\HomeMediaService;

Auth::requireAuth();
Session::start();

$service = new HomeMediaService();
$message = Session::flash('message');
$error = Session::flash('error');
$schemaMissing = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'save_settings') {
            $service->setShowPosters(($_POST['show_posters'] ?? '') === '1');
            Session::flash('message', 'Poster display setting saved.');
        } elseif ($action === 'create_poster') {
            $service->createPoster([
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'image_url' => trim($_POST['image_url'] ?? ''),
                'link_url' => trim($_POST['link_url'] ?? ''),
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
            ], $_FILES['poster'] ?? []);
            Session::flash('message', 'Poster uploaded.');
        } elseif ($action === 'update_poster') {
            $service->updatePoster((int) $_POST['id'], [
                'title' => trim($_POST['title'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'image_url' => trim($_POST['image_url'] ?? ''),
                'link_url' => trim($_POST['link_url'] ?? ''),
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
                'status' => $_POST['status'] ?? 'active',
            ]);
            Session::flash('message', 'Poster updated.');
        } elseif ($action === 'delete_poster') {
            $service->deletePoster((int) $_POST['id']);
            Session::flash('message', 'Poster deleted.');
        }
    } catch (Throwable $e) {
        Session::flash('error', $e->getMessage());
    }

    header('Location: posters.php');
    exit;
}

try {
    $posters = $service->allPosters();
    $showPosters = $service->showPosters();
} catch (Throwable $e) {
    $posters = [];
    $showPosters = false;
    $schemaMissing = true;
    $error = $error ?: 'Poster tables are not ready yet. Run update 024_home_media.sql from the Updates page.';
}

$pageTitle = 'Posters';
$currentAdminPage = 'posters';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<?php if ($schemaMissing): ?>
<div class="content-card p-4">
  <h5 class="mb-2">Poster update required</h5>
  <p class="text-muted mb-3">
    The poster upload menu needs the homepage media update before uploads can be saved.
    Open Updates and run pending update <code>024_home_media.sql</code>.
  </p>
  <a class="btn btn-primary" href="updates.php">Open Updates</a>
</div>
<?php else: ?>

<div class="row g-3 mb-4">
  <div class="col-lg-4">
    <div class="content-card p-4 h-100">
      <h5 class="mb-3">Homepage Poster Display</h5>
      <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
        <input type="hidden" name="action" value="save_settings">
        <div class="form-check form-switch mb-3">
          <input class="form-check-input" type="checkbox" role="switch" id="showPosters" name="show_posters" value="1" <?php echo $showPosters ? 'checked' : ''; ?>>
          <label class="form-check-label" for="showPosters">Show posters on homepage</label>
        </div>
        <button class="btn btn-primary">Save Setting</button>
      </form>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="content-card p-4 h-100">
      <h5 class="mb-2">Upload Poster</h5>
      <p class="text-muted mb-3">Uploaded posters appear in the homepage Featured Posters section and open in a pop-up when clicked.</p>
      <form method="POST" enctype="multipart/form-data" class="row g-2">
        <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
        <input type="hidden" name="action" value="create_poster">
        <div class="col-md-6"><input class="form-control" name="title" placeholder="Title" required></div>
        <div class="col-md-6"><input class="form-control" name="description" placeholder="Short description"></div>
        <div class="col-md-6"><input class="form-control" type="file" name="poster" accept="image/*"></div>
        <div class="col-md-6"><input class="form-control" name="image_url" placeholder="Image URL (used if no file chosen)"></div>
        <div class="col-md-4"><input class="form-control" name="link_url" placeholder="Optional reference URL"></div>
        <div class="col-md-3"><input class="form-control" name="sort_order" type="number" placeholder="Order" value="0"></div>
        <div class="col-md-3">
          <select class="form-select" name="status">
            <option value="active">active</option>
            <option value="inactive">inactive</option>
          </select>
        </div>
        <div class="col-md-2"><button class="btn btn-success w-100">Upload</button></div>
      </form>
    </div>
  </div>
</div>

<div class="content-card p-4">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h5 class="mb-0">Uploaded Posters</h5>
    <span class="badge text-bg-light"><?php echo count($posters); ?> total</span>
  </div>
  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead><tr><th>Preview</th><th>Title</th><th>Description</th><th>Reference URL</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($posters as $poster): ?>
        <tr>
          <form method="POST">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="update_poster">
            <input type="hidden" name="id" value="<?php echo (int) $poster['id']; ?>">
            <td>
              <?php $posterSrc = str_starts_with($poster['image_url'], 'http') ? $poster['image_url'] : '../' . $poster['image_url']; ?>
              <img src="<?php echo htmlspecialchars($posterSrc); ?>" alt="" style="width:70px;height:90px;object-fit:cover;border-radius:6px;">
            </td>
            <td><input class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($poster['title']); ?>"></td>
            <td><input class="form-control form-control-sm" name="description" value="<?php echo htmlspecialchars($poster['description'] ?? ''); ?>"></td>
            <td><input class="form-control form-control-sm" name="link_url" value="<?php echo htmlspecialchars($poster['link_url'] ?? ''); ?>"></td>
            <td><input class="form-control form-control-sm" type="number" name="sort_order" value="<?php echo (int) $poster['sort_order']; ?>"></td>
            <td>
              <select class="form-select form-select-sm" name="status">
                <option value="active" <?php echo $poster['status'] === 'active' ? 'selected' : ''; ?>>active</option>
                <option value="inactive" <?php echo $poster['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
              </select>
            </td>
            <td class="d-flex gap-1">
              <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($poster['image_url']); ?>">
              <button class="btn btn-sm btn-primary">Save</button>
          </form>
          <form method="POST" onsubmit="return confirm('Delete this poster?');">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="delete_poster">
            <input type="hidden" name="id" value="<?php echo (int) $poster['id']; ?>">
            <button class="btn btn-sm btn-danger">Delete</button>
          </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if ($posters === []): ?>
        <tr><td colspan="7" class="text-center text-muted">No posters uploaded yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
