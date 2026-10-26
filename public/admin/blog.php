<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Helpers\Upload;
use App\Services\BlogService;

Auth::requireAuth();
Session::start();

$service = new BlogService();
$view = $_GET['view'] ?? 'list';
$editId = (int) ($_GET['id'] ?? 0);

if (Upload::postTooLarge()) {
    Session::flash('error', Upload::postTooLargeMessage());
    header('Location: blog.php' . ($view !== 'list' ? '?view=' . urlencode($view) . ($editId ? '&id=' . $editId : '') : ''));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
    $action = isset($_POST['delete_id']) ? 'delete' : ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);
    $redirect = 'blog.php';

    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'category' => trim($_POST['category'] ?? '') ?: 'General',
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'image_url' => trim($_POST['image_url'] ?? ''),
        'status' => ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
        'sort_order' => (int) ($_POST['sort_order'] ?? 0),
    ];

    try {
        if ($action === 'create' || $action === 'update') {
            if ($data['title'] === '') {
                throw new \RuntimeException('Please give the post a title.');
            }
            if ($action === 'create') {
                $id = $service->create($data);
                Session::flash('message', 'Post published.');
            } else {
                $service->update($id, $data);
                Session::flash('message', 'Post updated.');
            }
            $service->setCoverImage($id, $_FILES['cover_image'] ?? []);
            $redirect = 'blog.php?view=edit&id=' . $id;
        } elseif ($action === 'delete') {
            $service->delete((int) $_POST['delete_id']);
            Session::flash('message', 'Post deleted.');
        } elseif ($action === 'bulk') {
            $ids = array_map('intval', (array) ($_POST['ids'] ?? []));
            $bulk = $_POST['bulk_action'] ?? '';
            foreach ($ids as $postId) {
                $post = $service->find($postId);
                if (!$post) {
                    continue;
                }
                match ($bulk) {
                    'delete' => $service->delete($postId),
                    'publish' => $service->update($postId, array_merge($post, ['status' => 'active'])),
                    'hide' => $service->update($postId, array_merge($post, ['status' => 'inactive'])),
                    default => null,
                };
            }
            if ($ids && $bulk) {
                Session::flash('message', count($ids) . ' post(s) updated.');
            }
        }
    } catch (\RuntimeException $e) {
        Session::flash('error', $e->getMessage());
        if ($action === 'update') {
            $redirect = 'blog.php?view=edit&id=' . $id;
        } elseif ($action === 'create' && $id === 0) {
            $redirect = 'blog.php?view=new';
        } elseif ($action === 'create') {
            $redirect = 'blog.php?view=edit&id=' . $id;
        }
    }

    header('Location: ' . $redirect);
    exit;
}

$message = Session::flash('message');
$error = Session::flash('error');

$post = null;
if ($view === 'edit') {
    $post = $editId ? $service->find($editId) : null;
    if (!$post) {
        $view = 'list';
        $error = $error ?: 'That post no longer exists.';
    }
}

$items = $service->getAll();
$categories = array_values(array_unique(array_filter(array_map(fn ($p) => trim((string) $p['category']), $items))));
sort($categories);

$pageTitle = 'Blog';
$currentAdminPage = 'blog';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?><div class="alert alert-success"><?php echo pq_admin_e($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo pq_admin_e($error); ?></div><?php endif; ?>

<?php if ($view === 'new' || $view === 'edit'): ?>
<?php $isEdit = $view === 'edit'; ?>
<div class="wp-head">
  <a class="wp-back" href="blog.php"><i class="bi bi-arrow-left"></i> All Posts</a>
  <h2><?php echo $isEdit ? 'Edit Post' : 'Add New Post'; ?></h2>
  <?php if ($isEdit): ?><a class="wp-add-new" href="blog.php?view=new"><i class="bi bi-plus-lg"></i> Add New</a><?php endif; ?>
</div>

<form method="POST" enctype="multipart/form-data" class="row g-3" id="postForm">
  <?php echo pq_admin_csrf(); ?>
  <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
  <?php if ($isEdit): ?>
  <input type="hidden" name="id" value="<?php echo (int) $post['id']; ?>">
  <input type="hidden" name="image_url" value="<?php echo pq_admin_e($post['image_url'] ?? ''); ?>">
  <?php endif; ?>

  <div class="col-lg-8">
    <input class="form-control wp-editor-title mb-3" name="title" value="<?php echo pq_admin_e($post['title'] ?? ''); ?>" placeholder="Add title" required autofocus>
    <textarea class="form-control rte-content" name="content" rows="18"><?php echo pq_admin_e($post['content'] ?? ''); ?></textarea>
  </div>

  <div class="col-lg-4">
    <div class="wp-box">
      <div class="wp-box-head">Publish</div>
      <div class="wp-box-body">
        <label class="form-label small fw-semibold">Visibility</label>
        <select class="form-select mb-3" name="status">
          <option value="active" <?php echo ($post['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Published</option>
          <option value="inactive" <?php echo ($post['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Draft (hidden)</option>
        </select>
        <label class="form-label small fw-semibold">Order <span class="wp-optional">(lower shows first)</span></label>
        <input class="form-control" type="number" name="sort_order" value="<?php echo (int) ($post['sort_order'] ?? 0); ?>">
        <?php if ($isEdit && ($post['status'] ?? '') === 'active'): ?>
        <a class="small d-inline-block mt-3" href="<?php echo pq_admin_e(pq_url('blog-detail?id=' . (int) $post['id'])); ?>" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View post</a>
        <?php endif; ?>
      </div>
      <div class="wp-box-foot">
        <?php if ($isEdit): ?>
        <button class="wp-delete-link" form="wp-row-action" name="delete_id" value="<?php echo (int) $post['id']; ?>" data-confirm="Delete this post permanently?">Move to trash</button>
        <?php else: ?><span></span><?php endif; ?>
        <button class="btn btn-primary"><?php echo $isEdit ? 'Update' : 'Publish'; ?></button>
      </div>
    </div>

    <div class="wp-box">
      <div class="wp-box-head">Category</div>
      <div class="wp-box-body">
        <input class="form-control" name="category" list="blogCategories" value="<?php echo pq_admin_e($post['category'] ?? ''); ?>" placeholder="e.g. Travel Tips">
        <datalist id="blogCategories">
          <?php foreach ($categories as $category): ?><option value="<?php echo pq_admin_e($category); ?>"><?php endforeach; ?>
        </datalist>
      </div>
    </div>

    <div class="wp-box">
      <div class="wp-box-head">Featured image</div>
      <div class="wp-box-body">
        <?php echo pq_admin_dropzone('cover_image', false, false, $post['image_url'] ?? null, ($post['image_url'] ?? '') !== '' ? 'Choose a new image to replace it.' : ''); ?>
      </div>
    </div>

    <div class="wp-box">
      <div class="wp-box-head">Excerpt <span class="wp-optional">— shown on the blog grid</span></div>
      <div class="wp-box-body">
        <textarea class="form-control" name="excerpt" rows="3"><?php echo pq_admin_e($post['excerpt'] ?? ''); ?></textarea>
      </div>
    </div>
  </div>
</form>
<?php echo pq_admin_row_action_form(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
(function () {
  var CSRF = <?php echo json_encode(Session::csrfToken()); ?>;
  var UPLOAD_URL = 'ajax/blog-image-upload.php';
  var LINK_PREVIEW_URL = 'ajax/link-preview.php';

  // --- Custom plugin: Insert Link Card -----------------------------------
  tinymce.PluginManager.add('linkcard', function (editor) {
    editor.ui.registry.addButton('linkcard', {
      icon: 'link',
      tooltip: 'Insert Link Card (with preview)',
      onAction: function () {
        editor.windowManager.open({
          title: 'Insert Link Card',
          body: {
            type: 'panel',
            items: [{ type: 'input', name: 'url', label: 'URL', placeholder: 'https://example.com/article' }]
          },
          buttons: [
            { type: 'cancel', text: 'Cancel' },
            { type: 'submit', text: 'Insert', primary: true }
          ],
          onSubmit: function (api) {
            var data = api.getData();
            var url = (data.url || '').trim();
            if (!url) { api.close(); return; }
            api.disable('submit');

            var fd = new FormData();
            fd.append('csrf', CSRF);
            fd.append('url', url);

            fetch(LINK_PREVIEW_URL, { method: 'POST', body: fd, credentials: 'same-origin' })
              .then(function (r) { return r.json(); })
              .then(function (res) {
                api.close();
                if (res.error) {
                  alert('Could not fetch link preview: ' + res.error);
                  return;
                }
                var img = res.image_url ? '<img src="' + res.image_url + '" alt="">' : '';
                var html = '<div class="link-preview-card" data-url="' + url + '">' +
                  (img ? '<div class="link-preview-card-image">' + img + '</div>' : '') +
                  '<div class="link-preview-card-body">' +
                  '<div class="link-preview-card-title">' + (res.title || url) + '</div>' +
                  (res.description ? '<div class="link-preview-card-desc">' + res.description + '</div>' : '') +
                  '<a href="' + url + '" target="_blank" rel="noopener noreferrer" class="link-preview-card-btn">Visit site</a>' +
                  '</div></div><p>&nbsp;</p>';
                editor.insertContent(html);
              })
              .catch(function () {
                api.close();
                alert('Could not fetch link preview. Please check the URL and try again.');
              });
          }
        });
      }
    });
  });

  // --- Custom plugin: Insert FAQ Section ----------------------------------
  tinymce.PluginManager.add('faqblock', function (editor) {
    editor.ui.registry.addButton('faqblock', {
      icon: 'help',
      tooltip: 'Insert FAQ Section',
      onAction: function () {
        var count = parseInt(prompt('How many FAQ questions do you want to add?', '3'), 10);
        if (!count || count < 1) { return; }
        var html = '<div class="faq-block">';
        for (var i = 1; i <= count; i++) {
          html += '<details><summary>Question ' + i + '</summary><div>Answer goes here.</div></details>';
        }
        html += '</div><p>&nbsp;</p>';
        editor.insertContent(html);
      }
    });
  });

  // A WordPress "classic editor"-style setup: one clean toolbar row with the
  // everyday tools, everything else tucked behind the "…" overflow button.
  tinymce.init({
    selector: 'textarea.rte-content',
    height: 560,
    menubar: false,
    statusbar: true,
    elementpath: false,
    plugins: 'lists link image media table code wordcount autolink autoresize linkcard faqblock',
    toolbar: 'blocks | bold italic | bullist numlist blockquote | alignleft aligncenter alignright | link image media | ' +
      'underline forecolor removeformat | table linkcard faqblock | undo redo | code',
    toolbar_mode: 'sliding',
    min_height: 480,
    max_height: 1200,
    block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4',
    placeholder: 'Start writing…',
    object_resizing: 'img,video,iframe',
    image_advtab: true,
    image_dimensions: true,
    image_caption: true,
    image_class_list: [
      { title: 'Default', value: '' },
      { title: 'Small (25%)', value: 'blog-img-25' },
      { title: 'Medium (50%)', value: 'blog-img-50' },
      { title: 'Large (75%)', value: 'blog-img-75' },
      { title: 'Full width', value: 'blog-img-100' }
    ],
    media_dimensions: false,
    content_style: 'body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;font-size:16px;line-height:1.7;max-width:760px;margin:1rem auto;padding:0 1rem;} ' +
      'img,video,iframe{max-width:100%;height:auto;} video,iframe{aspect-ratio:16/9;} ' +
      '.blog-img-25{width:25%;height:auto;} .blog-img-50{width:50%;height:auto;} ' +
      '.blog-img-75{width:75%;height:auto;} .blog-img-100{width:100%;height:auto;}',
    automatic_uploads: true,
    images_upload_credentials: true,
    images_upload_handler: function (blobInfo) {
      return new Promise(function (resolve, reject) {
        var fd = new FormData();
        fd.append('file', blobInfo.blob(), blobInfo.filename());
        fd.append('csrf', CSRF);
        fetch(UPLOAD_URL, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-CSRF-Token': CSRF } })
          .then(function (r) { return r.json(); })
          .then(function (res) { if (res.location) { resolve(res.location); } else { reject(res.error || 'Upload failed'); } })
          .catch(function () { reject('Upload failed'); });
      });
    },
    media_live_embeds: true,
    table_default_attributes: { border: '1' },
    branding: false,
    promotion: false
  });

  // Make sure the editor's HTML is in the textarea before the upload-progress
  // handler (in the footer) serialises the form.
  document.getElementById('postForm').addEventListener('submit', function () {
    if (window.tinymce) tinymce.triggerSave();
  }, true);
})();
</script>

<?php else: ?>
<?php $published = count(array_filter($items, fn ($p) => $p['status'] === 'active')); ?>
<div class="wp-head">
  <h2>Posts</h2>
  <a class="wp-add-new" href="blog.php?view=new"><i class="bi bi-plus-lg"></i> Add New</a>
</div>
<div class="wp-subsubsub">
  All (<?php echo count($items); ?>) <span>|</span> Published (<?php echo $published; ?>) <span>|</span> Drafts (<?php echo count($items) - $published; ?>)
</div>

<div class="content-card p-3">
  <?php if ($items === []): ?>
  <div class="wp-empty"><i class="bi bi-journal-text"></i>No posts yet. <a href="blog.php?view=new">Write your first post</a>.</div>
  <?php else: ?>
  <form method="POST" id="wp-bulk">
    <?php echo pq_admin_csrf(); ?>
    <input type="hidden" name="action" value="bulk">
    <div class="wp-bulk">
      <select class="form-select form-select-sm" name="bulk_action">
        <option value="">Bulk actions</option>
        <option value="publish">Publish</option>
        <option value="hide">Move to drafts</option>
        <option value="delete">Delete</option>
      </select>
      <button class="btn btn-sm btn-outline-secondary" data-confirm="Apply this action to the selected posts?">Apply</button>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table wp-table">
      <thead><tr>
        <th class="wp-check"><input class="form-check-input" type="checkbox" data-check-all aria-label="Select all"></th>
        <th style="width:90px;"></th><th>Title</th><th>Category</th><th>Status</th><th>Date</th>
      </tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
        <?php $editUrl = 'blog.php?view=edit&id=' . (int) $item['id']; ?>
        <tr>
          <td><input class="form-check-input" type="checkbox" name="ids[]" value="<?php echo (int) $item['id']; ?>" form="wp-bulk"></td>
          <td>
            <a href="<?php echo $editUrl; ?>">
              <?php if (!empty($item['image_url'])): ?>
              <img class="wp-thumb" src="<?php echo pq_admin_e(pq_admin_media($item['image_url'])); ?>" alt="" loading="lazy">
              <?php else: ?>
              <span class="wp-thumb d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></span>
              <?php endif; ?>
            </a>
          </td>
          <td>
            <a class="wp-title" href="<?php echo $editUrl; ?>"><?php echo pq_admin_e($item['title']); ?></a>
            <?php if (!empty($item['excerpt'])): ?><div class="small text-muted text-truncate" style="max-width:460px;"><?php echo pq_admin_e($item['excerpt']); ?></div><?php endif; ?>
            <div class="wp-row-actions">
              <a href="<?php echo $editUrl; ?>">Edit</a><span class="sep">|</span>
              <?php if ($item['status'] === 'active'): ?>
              <a href="<?php echo pq_admin_e(pq_url('blog-detail?id=' . (int) $item['id'])); ?>" target="_blank" rel="noopener">View</a><span class="sep">|</span>
              <?php endif; ?>
              <button class="danger" form="wp-row-action" name="delete_id" value="<?php echo (int) $item['id']; ?>" data-confirm="Delete this post permanently?">Delete</button>
            </div>
          </td>
          <td><?php echo pq_admin_e($item['category']); ?></td>
          <td><?php echo pq_admin_status_badge($item['status']); ?></td>
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
