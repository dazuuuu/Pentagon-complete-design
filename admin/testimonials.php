<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\TestimonialService;

Auth::requireAuth();
Session::start();

$service = new TestimonialService();
$message = Session::flash('message');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $data = [
    'author_name' => trim($_POST['author_name'] ?? ''),
    'author_location' => trim($_POST['author_location'] ?? ''),
    'quote' => trim($_POST['quote'] ?? ''),
    'accent_color' => $_POST['accent_color'] ?? 'gold',
    'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    'status' => $_POST['status'] ?? 'active',
  ];

  if (($_POST['action'] ?? '') === 'create') {
    $service->create($data);
    Session::flash('message', 'Testimonial created.');
  } elseif (($_POST['action'] ?? '') === 'update') {
    $service->update((int) $_POST['id'], $data);
    Session::flash('message', 'Testimonial updated.');
  } elseif (($_POST['action'] ?? '') === 'delete') {
    $service->delete((int) $_POST['id']);
    Session::flash('message', 'Testimonial deleted.');
  }

  header('Location: testimonials.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Testimonials';
$currentAdminPage = 'testimonials';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Manage Testimonials</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>

  <form method="POST" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-2"><input class="form-control" name="author_name" placeholder="Author" required></div>
    <div class="col-md-2"><input class="form-control" name="author_location" placeholder="Location"></div>
    <div class="col-md-6"><input class="form-control" name="quote" placeholder="Quote" required></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add</button></div>
  </form>

  <table class="table table-sm">
    <thead><tr><th>Author</th><th>Location</th><th>Quote</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="author_name" value="<?php echo htmlspecialchars($item['author_name']); ?>"></td>
          <td><input class="form-control form-control-sm" name="author_location" value="<?php echo htmlspecialchars($item['author_location'] ?? ''); ?>"></td>
          <td><input class="form-control form-control-sm" name="quote" value="<?php echo htmlspecialchars($item['quote']); ?>"></td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td class="d-flex gap-1">
            <input type="hidden" name="accent_color" value="<?php echo htmlspecialchars($item['accent_color']); ?>">
            <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this testimonial?');">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
          </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
