<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\OfferingService;
use App\Services\ServiceTierService;

Auth::requireAuth();
Session::start();

$offeringService = new OfferingService();
$tierService = new ServiceTierService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';

  if (str_starts_with($action, 'offering_')) {
    $data = [
      'title' => trim($_POST['title'] ?? ''),
      'description' => trim($_POST['description'] ?? ''),
      'status' => $_POST['status'] ?? 'active',
      'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    if ($action === 'offering_create') {
      $offeringService->create($data);
      Session::flash('message', 'Service offering created.');
    } elseif ($action === 'offering_update') {
      $offeringService->update((int) $_POST['id'], $data);
      Session::flash('message', 'Service offering updated.');
    } elseif ($action === 'offering_delete') {
      $offeringService->delete((int) $_POST['id']);
      Session::flash('message', 'Service offering deleted.');
    }
  } elseif (str_starts_with($action, 'tier_')) {
    $data = [
      'name' => trim($_POST['name'] ?? ''),
      'price' => (float) ($_POST['price'] ?? 0),
      'features' => trim($_POST['features'] ?? ''),
      'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
      'status' => $_POST['status'] ?? 'active',
      'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    if ($action === 'tier_create') {
      $tierService->create($data);
      Session::flash('message', 'Pricing tier created.');
    } elseif ($action === 'tier_update') {
      $tierService->update((int) $_POST['id'], $data);
      Session::flash('message', 'Pricing tier updated.');
    } elseif ($action === 'tier_delete') {
      $tierService->delete((int) $_POST['id']);
      Session::flash('message', 'Pricing tier deleted.');
    }
  }

  header('Location: services.php');
  exit;
}

$offerings = $offeringService->getAll();
$tiers = $tierService->getAll();
$pageTitle = 'Services';
$currentAdminPage = 'services';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4 mb-4">
  <h2 class="mb-3">Core Offerings</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="offering_create">
    <div class="col-md-4"><input class="form-control" name="title" placeholder="Title" required></div>
    <div class="col-md-5"><input class="form-control" name="description" placeholder="Description" required></div>
    <div class="col-md-1"><input class="form-control" name="sort_order" type="number" value="0"></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add</button></div>
  </form>

  <table class="table table-sm align-middle">
    <thead><tr><th>Title</th><th>Description</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($offerings as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="offering_update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($item['title']); ?>"></td>
          <td><input class="form-control form-control-sm" name="description" value="<?php echo htmlspecialchars($item['description'] ?? ''); ?>"></td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td class="d-flex gap-1">
            <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this offering?');">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="offering_delete">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
          </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div class="content-card p-4">
  <h2 class="mb-3">Pricing Tiers</h2>

  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="tier_create">
    <div class="col-md-3"><input class="form-control" name="name" placeholder="Tier name (e.g. Classic)" required></div>
    <div class="col-md-2"><input class="form-control" name="price" type="number" step="0.01" placeholder="Price / person" required></div>
    <div class="col-md-1"><input class="form-control" name="sort_order" type="number" value="0"></div>
    <div class="col-md-2 form-check pt-2"><input class="form-check-input" type="checkbox" name="is_popular" id="popular"><label for="popular" class="form-check-label">Most Popular</label></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add</button></div>
    <div class="col-12"><textarea class="form-control" name="features" placeholder="One feature per line" rows="3"></textarea></div>
  </form>

  <table class="table table-sm align-middle">
    <thead><tr><th>Name</th><th>Price</th><th>Popular</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($tiers as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="tier_update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="name" value="<?php echo htmlspecialchars($item['name']); ?>"></td>
          <td><input class="form-control form-control-sm" name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($item['price']); ?>"></td>
          <td class="text-center"><input type="checkbox" name="is_popular" <?php echo $item['is_popular'] ? 'checked' : ''; ?>></td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td class="d-flex gap-1">
            <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
            <input type="hidden" name="features" value="<?php echo htmlspecialchars($item['features'] ?? ''); ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this tier?');">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="tier_delete">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
          </td>
      </tr>
      <tr>
        <td colspan="5" class="pt-0">
          <details>
            <summary class="text-muted small">Features</summary>
            <form method="POST" class="d-flex flex-column gap-2 my-2">
              <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
              <input type="hidden" name="action" value="tier_update">
              <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
              <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
              <input type="hidden" name="price" value="<?php echo htmlspecialchars($item['price']); ?>">
              <input type="hidden" name="status" value="<?php echo htmlspecialchars($item['status']); ?>">
              <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
              <?php if ($item['is_popular']): ?><input type="hidden" name="is_popular" value="1"><?php endif; ?>
              <textarea class="form-control form-control-sm" name="features" rows="3" placeholder="One feature per line"><?php echo htmlspecialchars($item['features'] ?? ''); ?></textarea>
              <button class="btn btn-sm btn-outline-primary align-self-start">Save Features</button>
            </form>
          </details>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
