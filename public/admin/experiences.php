<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Services\ExperienceService;

Auth::requireAuth();
Session::start();

$service = new ExperienceService();
$view = $_GET['view'] ?? 'list';
$editId = (int) ($_GET['id'] ?? 0);

if (Upload::postTooLarge()) {
    Session::flash('error', Upload::postTooLargeMessage());
    header('Location: experiences.php' . ($view === 'edit' && $editId ? '?view=edit&id=' . $editId : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
    $action = isset($_POST['delete_id']) ? 'delete' : (isset($_POST['delete_image']) ? 'delete_image' : ($_POST['action'] ?? ''));
    $id = (int) ($_POST['id'] ?? 0);
    $redirect = 'experiences.php';

    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    try {
        if ($action === 'create') {
            $service->createWithImages($data, $_FILES['images'] ?? []);
            Session::flash('message', 'Pictures uploaded and published to the homepage.');
        } elseif ($action === 'update') {
            $service->update($id, $data);
            $service->addImages($id, $_FILES['images'] ?? []);
            Session::flash('message', 'Experience updated.');
            $redirect = 'experiences.php?view=edit&id=' . $id;
        } elseif ($action === 'delete_image') {
            $service->deleteImage((int) $_POST['delete_image']);
            Session::flash('message', 'Picture removed.');
            $redirect = 'experiences.php?view=edit&id=' . $id;
        } elseif ($action === 'delete') {
            $service->delete((int) $_POST['delete_id']);
            Session::flash('message', 'Experience deleted.');
        } elseif ($action === 'bulk') {
            $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
            $bulk = $_POST['bulk_action'] ?? '';
            foreach ($ids as $itemId) {
                match ($bulk) {
                    'delete' => $service->delete($itemId),
                    'publish' => $service->setStatus($itemId, 'active'),
                    'hide' => $service->setStatus($itemId, 'inactive'),
                    default => null,
                };
            }
            if ($ids && $bulk) {
                Session::flash('message', count($ids) . ' experience(s) updated.');
            }
        }
    } catch (Throwable $e) {
        Session::flash('error', $e->getMessage());
        if ($action === 'update' || $action === 'delete_image') {
            $redirect = 'experiences.php?view=edit&id=' . $id;
        }
    }

    header('Location: ' . $redirect);
    exit;
}

$message = Session::flash('message');
$error = Session::flash('error');

$experience = null;
if ($view === 'edit') {
    $experience = $editId ? $service->find($editId) : null;
    if (!$experience) {
        $view = 'list';
        $error = $error ?: 'That experience no longer exists.';
    }
}

$items = $view === 'list' ? $service->getAll() : [];
$pageTitle = 'Experiences';
$currentAdminPage = 'experiences';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?php echo pq_admin_e($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo pq_admin_e($error); ?></div><?php endif; ?>

<?php if ($view === 'edit'): ?>
<?php $images = $service->getImages((int) $experience['id']); ?>
<div class="wp-head">
  <a class="wp-back" href="experiences.php"><i class="bi bi-arrow-left"></i> All Experiences</a>
  <h2>Edit Experience</h2>
</div>

<form method="POST" enctype="multipart/form-data" class="row g-3">
  <?php echo pq_admin_csrf(); ?>
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="id" value="<?php echo (int) $experience['id']; ?>">

  <div class="col-lg-8">
    <div class="wp-box">
      <div class="wp-box-head">Pictures (<?php echo count($images); ?>)</div>
      <div class="wp-box-body">
        <?php if ($images !== []): ?>
        <div class="wp-media-grid mb-3">
          <?php foreach ($images as $image): ?>
          <div class="wp-media-item">
            <img src="<?php echo pq_admin_e(pq_admin_media($image['image_path'])); ?>" alt="" loading="lazy">
            <button type="submit" form="wp-image-action" name="delete_image" value="<?php echo (int) $image['id']; ?>" title="Remove picture" data-confirm="Remove this picture?">&times;</button>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php echo pq_admin_dropzone('images', true, false, null, 'Add more pictures — they are uploaded when you click Update.'); ?>
      </div>
    </div>
    <div class="wp-box">
      <div class="wp-box-head">Caption <span class="wp-optional">— optional</span></div>
      <div class="wp-box-body">
        <input class="form-control mb-3" name="title" value="<?php echo pq_admin_e($experience['title']); ?>" placeholder="Title, e.g. Maasai Mara, July 2026">
        <textarea class="form-control" name="description" rows="3" placeholder="Short description"><?php echo pq_admin_e($experience['description'] ?? ''); ?></textarea>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="wp-box">
      <div class="wp-box-head">Publish</div>
      <div class="wp-box-body">
        <label class="form-label small fw-semibold">Visibility</label>
        <select class="form-select mb-3" name="status">
          <option value="active" <?php echo $experience['status'] === 'active' ? 'selected' : ''; ?>>Published</option>
          <option value="inactive" <?php echo $experience['status'] === 'inactive' ? 'selected' : ''; ?>>Hidden</option>
        </select>
        <input type="hidden" name="sort_order" value="<?php echo (int) $experience['sort_order']; ?>">
        <div class="small text-muted">Added <?php echo pq_admin_date($experience['created_at'] ?? null); ?></div>
      </div>
      <div class="wp-box-foot">
        <button class="wp-delete-link" form="wp-row-action" name="delete_id" value="<?php echo (int) $experience['id']; ?>" data-confirm="Delete this experience and all its pictures?">Move to trash</button>
        <button class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</form>
<form method="POST" id="wp-image-action" hidden>
  <?php echo pq_admin_csrf(); ?>
  <input type="hidden" name="id" value="<?php echo (int) $experience['id']; ?>">
</form>
<?php echo pq_admin_row_action_form(); ?>

<?php else: ?>
<div class="wp-head">
  <h2>Experiences</h2>
  <span class="small text-muted">Pictures here appear in the homepage “Previous Experiences” gallery.</span>
</div>

<form method="POST" enctype="multipart/form-data" class="wp-box">
  <?php echo pq_admin_csrf(); ?>
  <input type="hidden" name="action" value="create">
  <div class="wp-box-head">Upload pictures</div>
  <div class="wp-box-body">
    <?php echo pq_admin_dropzone('images', true, true); ?>
    <details class="mt-3">
      <summary class="small text-muted" style="cursor:pointer;">Add a caption <span class="wp-optional">(optional)</span></summary>
      <div class="row g-2 mt-1">
        <div class="col-md-5"><input class="form-control" name="title" placeholder="Title, e.g. Maasai Mara, July 2026"></div>
        <div class="col-md-7"><input class="form-control" name="description" placeholder="Short description"></div>
      </div>
    </details>
  </div>
  <div class="wp-box-foot">
    <span class="small text-muted">Pictures go live on the homepage straight away.</span>
    <button class="btn btn-primary"><i class="bi bi-upload"></i> Upload</button>
  </div>
</form>

<div class="content-card p-3">
  <?php if ($items === []): ?>
  <div class="wp-empty"><i class="bi bi-images"></i>No experiences yet — drop some pictures above.</div>
  <?php else: ?>
  <form method="POST" id="wp-bulk">
    <?php echo pq_admin_csrf(); ?>
    <input type="hidden" name="action" value="bulk">
    <div class="wp-bulk">
      <select class="form-select form-select-sm" name="bulk_action">
        <option value="">Bulk actions</option>
        <option value="publish">Publish</option>
        <option value="hide">Hide</option>
        <option value="delete">Delete</option>
      </select>
      <button class="btn btn-sm btn-outline-secondary" data-confirm="Apply this action to the selected experiences?">Apply</button>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table wp-table">
      <thead><tr>
        <th class="wp-check"><input class="form-check-input" type="checkbox" data-check-all aria-label="Select all"></th>
        <th>Pictures</th><th>Caption</th><th>Status</th><th>Date</th>
      </tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <?php
          $images = $service->getImages((int) $item['id']);
          $title = trim((string) $item['title']);
          $editUrl = 'experiences.php?view=edit&id=' . (int) $item['id'];
        ?>
        <tr>
          <td><input class="form-check-input" type="checkbox" name="ids[]" value="<?php echo (int) $item['id']; ?>" form="wp-bulk"></td>
          <td>
            <a class="wp-thumbs" href="<?php echo $editUrl; ?>">
              <?php foreach (array_slice($images, 0, 4) as $image): ?>
              <img src="<?php echo pq_admin_e(pq_admin_media($image['image_path'])); ?>" alt="" loading="lazy">
              <?php endforeach; ?>
              <?php if (count($images) > 4): ?><span class="more">+<?php echo count($images) - 4; ?></span><?php endif; ?>
              <?php if ($images === []): ?><span class="more text-danger">No pictures</span><?php endif; ?>
            </a>
          </td>
          <td>
            <a class="wp-title<?php echo $title === '' ? ' is-untitled' : ''; ?>" href="<?php echo $editUrl; ?>"><?php echo pq_admin_e($title !== '' ? $title : '(no caption)'); ?></a>
            <div class="small text-muted"><?php echo count($images); ?> picture<?php echo count($images) === 1 ? '' : 's'; ?></div>
            <div class="wp-row-actions">
              <a href="<?php echo $editUrl; ?>">Edit</a><span class="sep">|</span>
              <button class="danger" form="wp-row-action" name="delete_id" value="<?php echo (int) $item['id']; ?>" data-confirm="Delete this experience and all its pictures?">Delete</button>
            </div>
          </td>
          <td><?php echo pq_admin_status_badge($item['status']); ?></td>
          <td class="small text-muted"><?php echo pq_admin_date($item['created_at'] ?? null); ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>
<?php echo pq_admin_row_action_form(); ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
