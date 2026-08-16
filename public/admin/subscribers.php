<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\SubscriptionService;

Auth::requireAuth();
Session::start();

$service = new SubscriptionService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  if (($_POST['action'] ?? '') === 'unsubscribe') {
    $service->unsubscribe((int) $_POST['id']);
    Session::flash('message', 'Subscriber unsubscribed.');
    header('Location: subscribers.php');
    exit;
  }
}

$items = $service->getAll();
$pageTitle = 'Subscribers';
$currentAdminPage = 'subscribers';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Newsletter Subscribers</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <table class="table table-sm">
    <thead><tr><th>Email</th><th>Status</th><th>Subscribed At</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['email']); ?></td>
        <td><?php echo htmlspecialchars($item['status']); ?></td>
        <td><?php echo htmlspecialchars($item['subscribed_at']); ?></td>
        <td>
          <?php if ($item['status'] === 'active'): ?>
          <form method="POST" onsubmit="return confirm('Unsubscribe this email?');">
            <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
            <input type="hidden" name="action" value="unsubscribe">
            <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
            <button class="btn btn-sm btn-outline-danger">Unsubscribe</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
