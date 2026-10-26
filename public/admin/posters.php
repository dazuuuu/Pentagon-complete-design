<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Services\HomeMediaService;

Auth::requireAuth();
Session::start();

$service = new HomeMediaService();
$view = $_GET['view'] ?? 'list';
$editId = (int) ($_GET['id'] ?? 0);

if (Upload::postTooLarge()) {
    Session::flash('error', Upload::postTooLargeMessage());
    header('Location: posters.php' . ($view !== 'list' ? '?view=' . urlencode($view) . ($editId ? '&id=' . $editId : '') : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
    $action = isset($_POST['delete_id']) ? 'delete' : ($_POST['action'] ?? '');
    $redirect = 'posters.php';

    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'link_url' => trim($_POST['link_url'] ?? ''),
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
    ];

    try {
        if ($action === 'save_settings') {
            $service->setShowPosters(($_POST['show_posters'] ?? '') === '1');
            Session::flash('message', 'Display setting saved.');
        } elseif ($action === 'create') {
            $service->createPoster($data, $_FILES['poster'] ?? []);
            Session::flash('message', 'Poster published.');
        } elseif ($action === 'update') {
            $id = (int) ($_POST['id'] ?? 0);
            $service->updatePoster($id, $data, $_FILES['poster'] ?? []);
            Session::flash('message', 'Poster updated.');
            $redirect = 'posters.php?view=edit&id=' . $id;
        } elseif ($action === 'delete') {
            $service->deletePoster((int) ($_POST['delete_id'] ?? $_POST['id'] ?? 0));
            Session::flash('message', 'Poster deleted.');
        } elseif ($action === 'bulk') {
            $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
            $bulk = $_POST['bulk_action'] ?? '';
            foreach ($ids as $id) {
                match ($bulk) {
                    'delete' => $service->deletePoster($id),
                    'publish' => $service->setPosterStatus($id, 'active'),
                    'hide' => $service->setPosterStatus($id, 'inactive'),
                    default => null,
                };
            }
            if ($ids && $bulk) {
                Session::flash('message', count($ids) . ' poster(s) updated.');
            }
        }
    } catch (Throwable $e) {
        Session::flash('error', $e->getMessage());
        if ($action === 'create') {
            $redirect = 'posters.php?view=new';
        } elseif ($action === 'update') {
            $redirect = 'posters.php?view=edit&id=' . (int) ($_POST['id'] ?? 0);
        }
    }

    header('Location: ' . $redirect);
    exit;
}

$message = Session::flash('message');
$error = Session::flash('error');
$schemaMissing = false;

try {
    $posters = $service->allPosters();
    $showPosters = $service->showPosters();
} catch (Throwable $e) {
    $posters = [];
    $showPosters = false;
    $schemaMissing = true;
    $error = $error ?: 'Poster tables are not ready yet. Run update 024_home_media.sql from the Updates page.';
}

$poster = null;
if ($view === 'edit') {
    $poster = $editId ? $service->findPoster($editId) : null;
    if (!$poster) {
        $view = 'list';
        $error = $error ?: 'That poster no longer exists.';
    }
}

$pageTitle = 'Posters';
$currentAdminPage = 'posters';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?php echo pq_admin_e($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo pq_admin_e($error); ?></div><?php endif; ?>

<?php if ($schemaMissing): ?>
<div class="content-card p-4">
  <h5 class="mb-2">Poster update required</h5>
  <p class="text-muted mb-3">Open Updates and run pending update <code>024_home_media.sql</code> before uploading posters.</p>
  <a class="btn btn-primary" href="updates.php">Open Updates</a>
</div>

<?php elseif ($view === 'new' || $view === 'edit'): ?>
<?php $isEdit = $view === 'edit'; ?>
<div class="wp-head">
  <a class="wp-back" href="posters.php"><i class="bi bi-arrow-left"></i> All Posters</a>
  <h2><?php echo $isEdit ? 'Edit Poster' : 'Add New Poster'; ?></h2>
</div>

<form method="POST" enctype="multipart/form-data" class="row g-3">
  <?php echo pq_admin_csrf(); ?>
  <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
  <?php if ($isEdit): ?><input type="hidden" name="id" value="<?php echo (int) $poster['id']; ?>"><?php endif; ?>

  <div class="col-lg-8">
    <div class="wp-box">
      <div class="wp-box-head">Poster image</div>
      <div class="wp-box-body">
        <?php echo pq_admin_dropzone('poster', false, !$isEdit, $isEdit ? $poster['image_url'] : null, $isEdit ? 'Choose a new image to replace the current one.' : ''); ?>
      </div>
    </div>
    <div class="wp-box">
      <div class="wp-box-head">Details <span class="wp-optional">— all optional</span></div>
      <div class="wp-box-body">
        <div class="mb-3">
          <label class="form-label small fw-semibold">Title</label>
          <input class="form-control" name="title" value="<?php echo pq_admin_e($poster['title'] ?? ''); ?>" placeholder="Shown on the poster card and pop-up">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Description</label>
          <textarea class="form-control" name="description" rows="3"><?php echo pq_admin_e($poster['description'] ?? ''); ?></textarea>
        </div>
        <div>
          <label class="form-label small fw-semibold">Reference link</label>
          <input class="form-control" name="link_url" type="url" value="<?php echo pq_admin_e($poster['link_url'] ?? ''); ?>" placeholder="https://">
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="wp-box">
      <div class="wp-box-head">Publish</div>
      <div class="wp-box-body">
        <label class="form-label small fw-semibold">Visibility</label>
        <select class="form-select mb-3" name="status">
          <option value="active" <?php echo ($poster['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Published</option>
          <option value="inactive" <?php echo ($poster['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Hidden</option>
        </select>
        <label class="form-label small fw-semibold">Order <span class="wp-optional">(lower shows first)</span></label>
        <input class="form-control" type="number" name="sort_order" value="<?php echo (int) ($poster['sort_order'] ?? 0); ?>">
      </div>
      <div class="wp-box-foot">
        <?php if ($isEdit): ?>
        <button class="wp-delete-link" form="wp-row-action" name="delete_id" value="<?php echo (int) $poster['id']; ?>" data-confirm="Delete this poster permanently?">Move to trash</button>
        <?php else: ?><span></span><?php endif; ?>
        <button class="btn btn-primary"><?php echo $isEdit ? 'Update' : 'Publish'; ?></button>
      </div>
    </div>
    <?php if (!$isEdit): ?>
    <details class="wp-box">
      <summary class="wp-box-head" style="cursor:pointer;">Use an image URL instead</summary>
      <div class="wp-box-body">
        <input class="form-control" name="image_url" placeholder="https://… (used if no file is chosen)">
      </div>
    </details>
    <?php endif; ?>
  </div>
</form>
<?php echo pq_admin_row_action_form(); ?>

<?php else: ?>
<?php
  $published = count(array_filter($posters, fn ($p) => $p['status'] === 'active'));
?>
<div class="wp-head">
  <h2>Posters</h2>
  <a class="wp-add-new" href="posters.php?view=new"><i class="bi bi-plus-lg"></i> Add New</a>
  <form method="POST" class="ms-auto d-flex align-items-center gap-2">
    <?php echo pq_admin_csrf(); ?>
    <input type="hidden" name="action" value="save_settings">
    <input type="hidden" name="show_posters" value="<?php echo $showPosters ? '0' : '1'; ?>">
    <span class="small text-muted">Homepage section:</span>
    <button class="btn btn-sm <?php echo $showPosters ? 'btn-success' : 'btn-outline-secondary'; ?>">
      <i class="bi <?php echo $showPosters ? 'bi-eye' : 'bi-eye-slash'; ?>"></i> <?php echo $showPosters ? 'Visible' : 'Hidden'; ?>
    </button>
  </form>
</div>
<div class="wp-subsubsub">
  All (<?php echo count($posters); ?>) <span>|</span> Published (<?php echo $published; ?>) <span>|</span> Hidden (<?php echo count($posters) - $published; ?>)
</div>

<div class="content-card p-3">
  <?php if ($posters === []): ?>
  <div class="wp-empty">
    <i class="bi bi-file-image"></i>
    No posters yet. <a href="posters.php?view=new">Upload your first poster</a>.
  </div>
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
      <button class="btn btn-sm btn-outline-secondary" data-confirm="Apply this action to the selected posters?">Apply</button>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table wp-table">
      <thead><tr>
        <th class="wp-check"><input class="form-check-input" type="checkbox" data-check-all aria-label="Select all"></th>
        <th style="width:90px;">Image</th><th>Title</th><th>Status</th><th>Order</th><th>Date</th>
      </tr></thead>
      <tbody>
        <?php foreach ($posters as $item): ?>
        <?php $title = trim((string) $item['title']); ?>
        <tr>
          <td><input class="form-check-input" type="checkbox" name="ids[]" value="<?php echo (int) $item['id']; ?>" form="wp-bulk"></td>
          <td><a href="posters.php?view=edit&id=<?php echo (int) $item['id']; ?>"><img class="wp-thumb" src="<?php echo pq_admin_e(pq_admin_media($item['image_url'])); ?>" alt="" loading="lazy"></a></td>
          <td>
            <a class="wp-title<?php echo $title === '' ? ' is-untitled' : ''; ?>" href="posters.php?view=edit&id=<?php echo (int) $item['id']; ?>"><?php echo pq_admin_e($title !== '' ? $title : '(no title)'); ?></a>
            <?php if (!empty($item['description'])): ?><div class="small text-muted text-truncate" style="max-width:420px;"><?php echo pq_admin_e($item['description']); ?></div><?php endif; ?>
            <div class="wp-row-actions">
              <a href="posters.php?view=edit&id=<?php echo (int) $item['id']; ?>">Edit</a><span class="sep">|</span>
              <a href="<?php echo pq_admin_e(pq_admin_media($item['image_url'])); ?>" target="_blank" rel="noopener">View image</a><span class="sep">|</span>
              <button class="danger" form="wp-row-action" name="delete_id" value="<?php echo (int) $item['id']; ?>" data-confirm="Delete this poster permanently?">Delete</button>
            </div>
          </td>
          <td><?php echo pq_admin_status_badge($item['status']); ?></td>
          <td><?php echo (int) $item['sort_order']; ?></td>
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
