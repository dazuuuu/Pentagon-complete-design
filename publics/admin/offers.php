<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\OfferService;
use App\Services\TourService;
use App\Services\DestinationService;

Auth::requireAuth();
Session::start();

$service = new OfferService();
$tourService = new TourService();
$destinationService = new DestinationService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';

  if ($action === 'create' || $action === 'update') {
    $linkType = $_POST['link_type'] ?? 'tour';
    $data = [
      'title' => trim($_POST['title'] ?? ''),
      'description' => trim($_POST['description'] ?? ''),
      'badge' => trim($_POST['badge'] ?? ''),
      'tour_id' => $linkType === 'tour' ? (int) ($_POST['tour_id'] ?? 0) : null,
      'destination_id' => $linkType === 'destination' ? (int) ($_POST['destination_id'] ?? 0) : null,
      'status' => $_POST['status'] ?? 'active',
      'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    if ($action === 'create') {
      $service->create($data);
      Session::flash('message', 'Offer created.');
    } else {
      $service->update((int) $_POST['id'], $data);
      Session::flash('message', 'Offer updated.');
    }
  } elseif ($action === 'delete') {
    $service->delete((int) $_POST['id']);
    Session::flash('message', 'Offer deleted.');
  }

  header('Location: offers.php');
  exit;
}

$items = $service->getAll();
$tours = $tourService->getAll();
$destinations = $destinationService->getAll();
$pageTitle = 'Offers';
$currentAdminPage = 'offers';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Manage Offers</h2>
  <p class="text-muted small">Offers appear in the homepage "Exclusive Offers" section and link straight to the tour or destination they apply to. Anyone who enquires from an offer page shows up as a claimant below.</p>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-4"><input class="form-control" name="title" placeholder="Offer title" required></div>
    <div class="col-md-2"><input class="form-control" name="badge" placeholder="Badge (e.g. 15% OFF)"></div>
    <div class="col-md-1"><input class="form-control" name="sort_order" type="number" value="0"></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add Offer</button></div>
    <div class="col-12"><textarea class="form-control" name="description" placeholder="Short description" rows="2"></textarea></div>
    <div class="col-md-2 d-flex align-items-center gap-2">
      <div class="form-check">
        <input class="form-check-input" type="radio" name="link_type" value="tour" id="linkTour" checked>
        <label class="form-check-label" for="linkTour">Tour</label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="link_type" value="destination" id="linkDest">
        <label class="form-check-label" for="linkDest">Destination</label>
      </div>
    </div>
    <div class="col-md-5">
      <select class="form-select" name="tour_id">
        <option value="">— Select a tour —</option>
        <?php foreach ($tours as $t): ?>
        <option value="<?php echo (int) $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-5">
      <select class="form-select" name="destination_id">
        <option value="">— Select a destination —</option>
        <?php foreach ($destinations as $d): ?>
        <option value="<?php echo (int) $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>

  <table class="table table-sm align-middle">
    <thead><tr><th>Title</th><th>Linked To</th><th>Status</th><th>Claims</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($item['title']); ?>"></td>
          <td>
            <?php if ($item['target_type']): ?>
              <span class="badge bg-secondary text-capitalize"><?php echo htmlspecialchars($item['target_type']); ?></span>
              <?php echo htmlspecialchars($item['target_name'] ?? ''); ?>
            <?php else: ?>
              <span class="text-muted">Not linked</span>
            <?php endif; ?>
          </td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td><?php echo count($service->getClaimants((int) $item['id'])); ?></td>
          <td class="d-flex gap-1">
            <input type="hidden" name="description" value="<?php echo htmlspecialchars($item['description'] ?? ''); ?>">
            <input type="hidden" name="badge" value="<?php echo htmlspecialchars($item['badge'] ?? ''); ?>">
            <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
            <input type="hidden" name="link_type" value="<?php echo $item['target_type'] === 'destination' ? 'destination' : 'tour'; ?>">
            <input type="hidden" name="tour_id" value="<?php echo (int) ($item['tour_id'] ?? 0); ?>">
            <input type="hidden" name="destination_id" value="<?php echo (int) ($item['destination_id'] ?? 0); ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this offer?');">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
          </td>
      </tr>
      <?php $claimants = $service->getClaimants((int) $item['id']); ?>
      <?php if (!empty($claimants)): ?>
      <tr>
        <td colspan="5" class="pt-0">
          <details>
            <summary class="text-muted small">Who claimed this offer (<?php echo count($claimants); ?>)</summary>
            <table class="table table-sm mt-2">
              <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach ($claimants as $c): ?>
                <tr>
                  <td><?php echo htmlspecialchars($c['created_at']); ?></td>
                  <td><?php echo htmlspecialchars($c['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($c['email']); ?></td>
                  <td><?php echo htmlspecialchars(ucfirst($c['status'])); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </details>
        </td>
      </tr>
      <?php endif; ?>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
      <tr><td colspan="5" class="text-center text-muted">No offers yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
