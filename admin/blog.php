<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\BlogService;

Auth::requireAuth();
Session::start();

$service = new BlogService();
$message = Session::flash('message');
$error = Session::flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? '';

  try {
    if ($action === 'update_cover') {
      $service->setCoverImage((int) $_POST['id'], $_FILES['cover_image'] ?? []);
      Session::flash('message', 'Cover image updated.');
    } else {
      $data = [
        'title' => trim($_POST['title'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'status' => $_POST['status'] ?? 'active',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
      ];

      if ($action === 'create') {
        $id = $service->create($data);
        if (!empty($_FILES['cover_image']['name'] ?? '')) {
          $service->setCoverImage($id, $_FILES['cover_image']);
        }
        Session::flash('message', 'Post created.');
      } elseif ($action === 'update') {
        $service->update((int) $_POST['id'], $data);
        Session::flash('message', 'Post updated.');
      } elseif ($action === 'delete') {
        $service->delete((int) $_POST['id']);
        Session::flash('message', 'Post deleted.');
      }
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
  }

  header('Location: blog.php');
  exit;
}

$items = $service->getAll();
$pageTitle = 'Blog';
$currentAdminPage = 'blog';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Manage Blog</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="row g-2 mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-md-5"><input class="form-control" name="title" placeholder="Title" required></div>
    <div class="col-md-3"><input class="form-control" name="category" placeholder="Category" required></div>
    <div class="col-md-2"><input class="form-control" name="sort_order" type="number" placeholder="Sort" value="0"></div>
    <div class="col-md-2"><button class="btn btn-success w-100">Add Post</button></div>
    <div class="col-12"><textarea class="form-control" name="excerpt" placeholder="Short excerpt (shown on the blog grid card)" rows="2"></textarea></div>
    <div class="col-12"><textarea class="form-control" name="content" placeholder="Full article content (shown on the post's details page)" rows="5"></textarea></div>
    <div class="col-md-6">
      <label class="form-label small text-muted mb-0">Cover image</label>
      <input class="form-control" type="file" name="cover_image" accept="image/*">
    </div>
  </form>

  <table class="table table-sm align-middle">
    <thead><tr><th>Title</th><th>Category</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <td><input class="form-control form-control-sm" name="title" value="<?php echo htmlspecialchars($item['title']); ?>"></td>
          <td><input class="form-control form-control-sm" name="category" value="<?php echo htmlspecialchars($item['category']); ?>"></td>
          <td>
            <select class="form-select form-select-sm" name="status">
              <option value="active" <?php echo $item['status'] === 'active' ? 'selected' : ''; ?>>active</option>
              <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>inactive</option>
            </select>
          </td>
          <td class="d-flex gap-1">
            <input type="hidden" name="excerpt" value="<?php echo htmlspecialchars($item['excerpt'] ?? ''); ?>">
            <input type="hidden" name="content" value="<?php echo htmlspecialchars($item['content'] ?? ''); ?>">
            <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>">
            <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
            <button class="btn btn-sm btn-primary">Save</button>
        </form>
        <form method="POST" onsubmit="return confirm('Delete this post?');">
          <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
          <button class="btn btn-sm btn-danger">Delete</button>
        </form>
          </td>
      </tr>
      <tr>
        <td colspan="4" class="pt-0">
          <details>
            <summary class="text-muted small">Excerpt, content &amp; cover image</summary>
            <div class="row g-3 my-1">
              <div class="col-md-6">
                <label class="small text-muted mb-1 d-block">Excerpt</label>
                <form method="POST" class="d-flex flex-column gap-2">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <input type="hidden" name="title" value="<?php echo htmlspecialchars($item['title']); ?>">
                  <input type="hidden" name="category" value="<?php echo htmlspecialchars($item['category']); ?>">
                  <input type="hidden" name="status" value="<?php echo htmlspecialchars($item['status']); ?>">
                  <input type="hidden" name="image_url" value="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>">
                  <input type="hidden" name="sort_order" value="<?php echo (int) $item['sort_order']; ?>">
                  <textarea class="form-control form-control-sm" name="excerpt" rows="2"><?php echo htmlspecialchars($item['excerpt'] ?? ''); ?></textarea>
                  <textarea class="form-control form-control-sm" name="content" rows="6" placeholder="Full article content"><?php echo htmlspecialchars($item['content'] ?? ''); ?></textarea>
                  <button class="btn btn-sm btn-outline-primary align-self-start">Save Content</button>
                </form>
              </div>
              <div class="col-md-6">
                <label class="small text-muted mb-1 d-block">Cover image</label>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <?php if (!empty($item['image_url'])): ?>
                  <img src="<?php echo (str_starts_with($item['image_url'], 'http') ? '' : '../') . htmlspecialchars($item['image_url']); ?>" style="width:120px;height:80px;object-fit:cover;border-radius:6px;">
                  <?php endif; ?>
                </div>
                <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
                  <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
                  <input type="hidden" name="action" value="update_cover">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <input class="form-control form-control-sm" type="file" name="cover_image" accept="image/*" style="max-width:260px;">
                  <button class="btn btn-sm btn-outline-primary">Replace</button>
                </form>
              </div>
            </div>
          </details>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
