<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\SiteSettingsService;
use App\Services\StoryMediaService;

Auth::requireAuth();
Session::start();

$settingsService = new SiteSettingsService();
$mediaService = new StoryMediaService();
$message = Session::flash('message');
$error = Session::flash('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  $action = $_POST['action'] ?? 'save_text';

  try {
    if ($action === 'upload_image') {
      $mediaService->addImages($_FILES['images'] ?? [], trim($_POST['caption'] ?? ''));
      Session::flash('message', 'Image(s) uploaded.');
    } elseif ($action === 'delete_image') {
      $mediaService->deleteImage((int) $_POST['image_id']);
      Session::flash('message', 'Image removed.');
    } else {
      $settingsService->set('our_story_tag', trim($_POST['our_story_tag'] ?? ''));
      $settingsService->set('our_story_heading', trim($_POST['our_story_heading'] ?? ''));
      $settingsService->set('our_story_text_1', trim($_POST['our_story_text_1'] ?? ''));
      $settingsService->set('our_story_text_2', trim($_POST['our_story_text_2'] ?? ''));
      $settingsService->set('our_story_stat_1_value', trim($_POST['our_story_stat_1_value'] ?? ''));
      $settingsService->set('our_story_stat_1_label', trim($_POST['our_story_stat_1_label'] ?? ''));
      $settingsService->set('our_story_stat_2_value', trim($_POST['our_story_stat_2_value'] ?? ''));
      $settingsService->set('our_story_stat_2_label', trim($_POST['our_story_stat_2_label'] ?? ''));
      Session::flash('message', 'Our Story content saved.');
    }
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
  }

  header('Location: our-story.php');
  exit;
}

$values = $settingsService->getAll();
$images = $mediaService->getAll();
$pageTitle = 'Our Story';
$currentAdminPage = 'our-story';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4 mb-4">
  <h2 class="mb-3">Our Story — Text</h2>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" class="row g-3">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="save_text">
    <div class="col-md-4">
      <label class="form-label small text-muted mb-1">Section tag (e.g. "Since 2010")</label>
      <input class="form-control" name="our_story_tag" value="<?php echo htmlspecialchars($values['our_story_tag'] ?? ''); ?>">
    </div>
    <div class="col-md-8">
      <label class="form-label small text-muted mb-1">Heading</label>
      <input class="form-control" name="our_story_heading" value="<?php echo htmlspecialchars($values['our_story_heading'] ?? ''); ?>" placeholder="Born in Africa. Built for Explorers.">
    </div>
    <div class="col-12">
      <label class="form-label small text-muted mb-1">Paragraph 1</label>
      <textarea class="form-control" name="our_story_text_1" rows="3"><?php echo htmlspecialchars($values['our_story_text_1'] ?? ''); ?></textarea>
    </div>
    <div class="col-12">
      <label class="form-label small text-muted mb-1">Paragraph 2</label>
      <textarea class="form-control" name="our_story_text_2" rows="3"><?php echo htmlspecialchars($values['our_story_text_2'] ?? ''); ?></textarea>
    </div>
    <div class="col-md-3">
      <label class="form-label small text-muted mb-1">Stat 1 value</label>
      <input class="form-control" name="our_story_stat_1_value" value="<?php echo htmlspecialchars($values['our_story_stat_1_value'] ?? ''); ?>" placeholder="2.4k+">
    </div>
    <div class="col-md-3">
      <label class="form-label small text-muted mb-1">Stat 1 label</label>
      <input class="form-control" name="our_story_stat_1_label" value="<?php echo htmlspecialchars($values['our_story_stat_1_label'] ?? ''); ?>" placeholder="Happy Travellers">
    </div>
    <div class="col-md-3">
      <label class="form-label small text-muted mb-1">Stat 2 value</label>
      <input class="form-control" name="our_story_stat_2_value" value="<?php echo htmlspecialchars($values['our_story_stat_2_value'] ?? ''); ?>" placeholder="14">
    </div>
    <div class="col-md-3">
      <label class="form-label small text-muted mb-1">Stat 2 label</label>
      <input class="form-control" name="our_story_stat_2_label" value="<?php echo htmlspecialchars($values['our_story_stat_2_label'] ?? ''); ?>" placeholder="Countries Covered">
    </div>
    <div class="col-12">
      <button class="btn btn-success">Save Text</button>
    </div>
  </form>
</div>

<div class="content-card p-4">
  <h2 class="mb-3">Our Story — Images</h2>
  <p class="text-muted small">The first image is used as the main "Our Story" image on the about page; additional images can be used as a gallery.</p>

  <form method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center mb-4">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <input type="hidden" name="action" value="upload_image">
    <input class="form-control" type="file" name="images[]" accept="image/*" multiple style="max-width:360px;">
    <input class="form-control" name="caption" placeholder="Caption (optional)" style="max-width:260px;">
    <button class="btn btn-outline-success">Upload</button>
  </form>

  <div class="d-flex flex-wrap gap-3">
    <?php foreach ($images as $image): ?>
    <div class="position-relative">
      <img src="../<?php echo htmlspecialchars($image['image_path']); ?>" style="width:140px;height:100px;object-fit:cover;border-radius:8px;">
      <form method="POST" class="position-absolute top-0 end-0" onsubmit="return confirm('Remove this image?');">
        <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
        <input type="hidden" name="action" value="delete_image">
        <input type="hidden" name="image_id" value="<?php echo (int) $image['id']; ?>">
        <button class="btn btn-sm btn-danger py-0 px-1">&times;</button>
      </form>
      <?php if (!empty($image['caption'])): ?>
      <div class="small text-muted mt-1" style="max-width:140px;"><?php echo htmlspecialchars($image['caption']); ?></div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php if (!$images): ?>
    <p class="text-muted small">No images uploaded yet. The about page will show its default illustration until you add one.</p>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
