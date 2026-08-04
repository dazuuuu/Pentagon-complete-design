<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\ClientService;
use App\Services\TestimonialService;

Auth::requireAuth();
Session::start();

$service = new ClientService();
$testimonialService = new TestimonialService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';
  $id = (int) ($_POST['id'] ?? 0);

  if ($action === 'update_status') {
    $service->updateStatus($id, $_POST['status'] ?? 'scheduled', trim($_POST['scheduled_date'] ?? '') ?: null);
    Session::flash('message', 'Client status updated — customer notified by email.');
  } elseif ($action === 'delete') {
    $service->delete($id);
    Session::flash('message', 'Client removed.');
  } elseif ($action === 'add_testimonial') {
    $client = $service->find($id);
    if ($client) {
      $testimonialService->create([
        'client_id' => $client['id'],
        'author_name' => $client['full_name'],
        'author_location' => trim($_POST['author_location'] ?? '') ?: ($client['interest'] ?? ''),
        'quote' => trim($_POST['quote'] ?? ''),
        'status' => 'inactive',
      ]);
      Session::flash('message', 'Testimonial submitted — review and publish it below when ready.');
    }
  } elseif ($action === 'toggle_testimonial') {
    $testimonial = $testimonialService->find((int) $_POST['testimonial_id']);
    if ($testimonial) {
      $testimonial['status'] = $testimonial['status'] === 'active' ? 'inactive' : 'active';
      $testimonialService->update((int) $testimonial['id'], $testimonial);
      Session::flash('message', $testimonial['status'] === 'active' ? 'Testimonial published to the homepage.' : 'Testimonial unpublished.');
    }
  }

  header('Location: clients.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Clients';
$currentAdminPage = 'clients';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Clients</h2>
  <p class="text-muted small">Clients are created automatically when you accept an enquiry.</p>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <table class="table table-sm align-middle">
    <thead><tr><th>Since</th><th>Name</th><th>Email</th><th>Phone</th><th>Interested In</th><th>Status / Scheduled Date</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['created_at']); ?></td>
        <td><?php echo htmlspecialchars($item['full_name']); ?></td>
        <td><?php echo htmlspecialchars($item['email'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['phone'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['interest'] ?? ''); ?></td>
        <td class="d-flex gap-1">
          <form method="POST" class="d-flex gap-1">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <select class="form-select form-select-sm" name="status">
              <option value="scheduled" <?php echo $item['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
              <option value="completed" <?php echo $item['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
              <option value="cancelled" <?php echo $item['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <input type="date" class="form-control form-control-sm" name="scheduled_date" value="<?php echo htmlspecialchars($item['scheduled_date'] ?? ''); ?>" style="width: 145px;">
            <button class="btn btn-sm btn-primary">Save</button>
          </form>
        </td>
        <td>
          <form method="POST" onsubmit="return confirm('Remove this client?');">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <button class="btn btn-sm btn-outline-danger">Remove</button>
          </form>
        </td>
      </tr>
      <tr>
        <td colspan="7" class="pt-0">
          <details>
            <?php $clientTestimonials = $testimonialService->getForClient((int) $item['id']); ?>
            <summary class="text-muted small">Testimonial (<?php echo count($clientTestimonials); ?>)</summary>

            <?php foreach ($clientTestimonials as $t): ?>
            <div class="d-flex align-items-start gap-2 my-2 p-2 border rounded">
              <div class="flex-grow-1">
                <div class="fst-italic small">"<?php echo htmlspecialchars($t['quote']); ?>"</div>
                <span class="badge bg-<?php echo $t['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo $t['status'] === 'active' ? 'Live on homepage' : 'Pending review'; ?></span>
              </div>
              <form method="POST">
                <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                <input type="hidden" name="action" value="toggle_testimonial">
                <input type="hidden" name="testimonial_id" value="<?php echo (int) $t['id']; ?>">
                <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                <button class="btn btn-sm <?php echo $t['status'] === 'active' ? 'btn-outline-secondary' : 'btn-outline-success'; ?>"><?php echo $t['status'] === 'active' ? 'Unpublish' : 'Publish'; ?></button>
              </form>
            </div>
            <?php endforeach; ?>

            <form method="POST" class="d-flex flex-column gap-2 my-2" style="max-width:500px;">
              <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
              <input type="hidden" name="action" value="add_testimonial">
              <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
              <textarea class="form-control form-control-sm" name="quote" rows="2" placeholder="What did this client say about their trip?" required></textarea>
              <input class="form-control form-control-sm" name="author_location" placeholder="Shown under their name (defaults to their interest)">
              <button class="btn btn-sm btn-outline-primary align-self-start">Submit Testimonial</button>
            </form>
          </details>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
      <tr><td colspan="7" class="text-center text-muted">No clients yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
