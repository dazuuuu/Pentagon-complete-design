<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Services\EnquiryService;

Auth::requireAuth();

$service = new EnquiryService();
$items = $service->getAll();
$pageTitle = 'Enquiries';
$currentAdminPage = 'enquiries';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Contact Enquiries</h2>
  <table class="table table-sm">
    <thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Message</th><th>Email Sent</th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?php echo htmlspecialchars($item['created_at']); ?></td>
        <td><?php echo htmlspecialchars($item['full_name']); ?></td>
        <td><?php echo htmlspecialchars($item['email']); ?></td>
        <td><?php echo htmlspecialchars($item['message']); ?></td>
        <td><?php echo $item['email_sent'] ? 'Yes' : 'No'; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
