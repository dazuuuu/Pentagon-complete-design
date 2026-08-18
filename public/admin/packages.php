<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\FeaturedPackageService;

Auth::requireAuth();
Session::start();

$service = new FeaturedPackageService();
$message = Session::flash('message');
$error = Session::flash('error');
$schemaMissing = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $data = [
        'hotel' => trim($_POST['hotel'] ?? ''),
        'meal' => trim($_POST['meal'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'two_nights_price' => trim($_POST['two_nights_price'] ?? ''),
        'three_nights_price' => trim($_POST['three_nights_price'] ?? ''),
        'status' => $_POST['status'] ?? 'active',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    try {
        if ($action === 'create') {
            $service->create($data);
            Session::flash('message', 'Package created.');
        } elseif ($action === 'update') {
            $service->update((int) $_POST['id'], $data);
            Session::flash('message', 'Package updated.');
        } elseif ($action === 'delete') {
            $service->delete((int) $_POST['id']);
            Session::flash('message', 'Package deleted.');
        }
    } catch (Throwable $e) {
        Session::flash('error', $e->getMessage());
    }

    header('Location: packages.php');
    exit;
}

try {
    $items = $service->getAll();
} catch (Throwable $e) {
    $items = [];
    $schemaMissing = true;
    $error = $error ?: 'Package tables are not ready yet. Run update 025_featured_packages.sql from the Updates page.';
}

$pageTitle = 'Packages';
$currentAdminPage = 'packages';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<?php if ($schemaMissing): ?>
<div class="content-card p-4">
  <h5 class="mb-2">Package update required</h5>
  <p class="text-muted mb-3">Open Updates and run pending update <code>025_featured_packages.sql</code>.</p>
  <a class="btn btn-primary" href="updates.php">Open Updates</a>
</div>
<?php else: ?>
<div class="content-card p-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
    <div>
      <h2 class="mb-1">Featured Packages</h2>
      <p class="text-muted mb-0">These rows feed the black Featured Packages section on the homepage.</p>
    </div>
    <span class="badge text-bg-light"><?php echo count($items); ?> total</span>
  </div>

  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-3"><input class="form-control" name="hotel" placeholder="Hotel / package" required></div>
    <div class="col-md-2"><input class="form-control" name="meal" placeholder="Meal"></div>
    <div class="col-md-2"><input class="form-control" name="location" placeholder="Location"></div>
    <div class="col-md-2"><input class="form-control" name="two_nights_price" placeholder="2 nights"></div>
    <div class="col-md-2"><input class="form-control" name="three_nights_price" placeholder="3 nights"></div>
    <div class="col-md-1"><input class="form-control" name="sort_order" type="number" value="0"></div>
    <div class="col-md-2">
      <select class="form-select" name="status">
        <option value="active">active</option>
        <option value="inactive">inactive</option>
      </select>
    </div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add Package</button></div>
  </form>

  <div class="table-responsive">
    <table class="table table-sm align-middle">
      <thead><tr><th>Package</th><th>Meal</th><th>Location</th><th>2 Nights</th><th>3 Nights</th><th>Order</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
          <form method="POST">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <td><input class="form-control form-control-sm" name="hotel" value="<?php echo htmlspecialchars($item['hotel']); ?>"></td>
            <td><input class="form-control form-control-sm" name="meal" value="<?php echo htmlspecialchars($item['meal'] ?? ''); ?>"></td>
            <td><input class="form-control form-control-sm" name="location" value="<?php echo htmlspecialchars($item['location'] ?? ''); ?>"></td>
            <td><input class="form-control form-control-sm" name="two_nights_price" value="<?php echo htmlspecialchars($item['two_nights_price'] ?? ''); ?>"></td>
            <td><input class="form-control form-control-sm" name="three_nights_price" value="<?php echo htmlspecialchars($item['three_nights_price'] ?? ''); ?>"></td>
            <td><input class="form-control form-control-sm" name="sort_order" type="number" value="<?php echo (int) $item['sort_order']; ?>"></td>
            <td>
              <select class="form-select form-select-sm" name="status">
                <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
                <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
              </select>
            </td>
            <td class="d-flex gap-1">
              <button class="btn btn-sm btn-primary">Save</button>
          </form>
          <form method="POST" onsubmit="return confirm('Delete this package?');">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <button class="btn btn-sm btn-danger">Delete</button>
          </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if ($items === []): ?>
        <tr><td colspan="8" class="text-center text-muted">No packages yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
