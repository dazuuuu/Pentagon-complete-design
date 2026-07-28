<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\EnquiryService;

Auth::requireAuth();
Session::start();

$service = new EnquiryService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';
  $id = (int) ($_POST['id'] ?? 0);

  if ($action === 'accept') {
    $service->accept($id);
    Session::flash('message', 'Enquiry accepted — client notified and added to Clients.');
  } elseif ($action === 'reject') {
    $service->reject($id);
    Session::flash('message', 'Enquiry rejected — customer notified.');
  } elseif ($action === 'send_quote') {
    $service->sendQuote($id, trim($_POST['price'] ?? ''), trim($_POST['quote_message'] ?? ''));
    Session::flash('message', 'Quote emailed to the customer.');
  }

  header('Location: enquiries.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Enquiries';
$currentAdminPage = 'enquiries';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Contact Enquiries</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <table class="table table-sm align-middle">
    <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Phone</th><th>Interested In</th><th>Message</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['created_at']); ?></td>
        <td><?php echo htmlspecialchars($item['full_name']); ?></td>
        <td><?php echo htmlspecialchars($item['email']); ?></td>
        <td><?php echo htmlspecialchars($item['phone'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['interest'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($item['message'] ?? ''); ?></td>
        <td>
          <?php
            $status = $item['status'] ?? 'pending';
            $badge = ['pending' => 'secondary', 'accepted' => 'success', 'rejected' => 'danger'][$status] ?? 'secondary';
          ?>
          <span class="badge bg-<?php echo $badge; ?>"><?php echo htmlspecialchars(ucfirst($status)); ?></span>
        </td>
        <td class="d-flex gap-1">
          <?php if (($item['status'] ?? 'pending') === 'pending'): ?>
          <form method="POST" onsubmit="return confirm('Accept this enquiry? The customer will be emailed and added to Clients.');">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="accept">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <button class="btn btn-sm btn-success">Accept</button>
          </form>
          <form method="POST" onsubmit="return confirm('Reject this enquiry? The customer will be emailed.');">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="reject">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <button class="btn btn-sm btn-outline-danger">Reject</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <tr>
        <td colspan="8" class="pt-0">
          <details>
            <summary class="text-muted small">Send a quote</summary>
            <form method="POST" class="row g-2 my-2 align-items-end" style="max-width:700px;">
              <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
              <input type="hidden" name="action" value="send_quote">
              <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
              <div class="col-md-3">
                <label class="form-label small text-muted mb-1">Quoted Price</label>
                <input class="form-control form-control-sm" name="price" placeholder="e.g. $1,750">
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted mb-1">Message</label>
                <textarea class="form-control form-control-sm" name="quote_message" rows="1" placeholder="Anything you'd like to add for the customer"></textarea>
              </div>
              <div class="col-md-3">
                <button class="btn btn-sm btn-outline-success w-100">Email Quote to Customer</button>
              </div>
            </form>
          </details>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
