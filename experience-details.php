<?php
/**
 * Pentagon Quest — Experience Details Page
 * Not linked from the main navigation — reachable only via the homepage
 * "Previous Experiences" cards.
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\ExperienceService;

$experienceService = new ExperienceService();
$experienceId = (int) ($_GET['id'] ?? 0);
$experience = $experienceId ? $experienceService->find($experienceId) : null;

if (!$experience || $experience['status'] !== 'active') {
    header('Location: index.php');
    exit;
}

$images = $experienceService->getImages($experienceId);

$page_title       = $experience['title'] . ' — Pentagon Quest';
$page_description = !empty($experience['description']) ? mb_strimwidth(strip_tags($experience['description']), 0, 160, '...') : 'A Pentagon Quest travel experience.';
$current_page     = '';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 320px; padding: 100px 0 40px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Our Legacy</span>
    <h1 class="hero-title" style="font-size: clamp(1.8rem, 5vw, 3.2rem);"><?php echo htmlspecialchars($experience['title']); ?></h1>
  </div>
</section>

<section class="section-pad" style="padding-top: 60px;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9 reveal">

        <?php if (!empty($images)): ?>
        <div id="expGallery" class="carousel slide mb-4" data-bs-ride="carousel" style="border-radius: var(--radius-md); overflow: hidden;">
          <div class="carousel-inner" style="aspect-ratio: 16/9; background: var(--sand);">
            <?php foreach ($images as $i => $image): ?>
            <div class="carousel-item h-100 <?php echo $i === 0 ? 'active' : ''; ?>">
              <img src="<?php echo htmlspecialchars($image['image_path']); ?>" style="width:100%; height:100%; object-fit: cover;">
            </div>
            <?php endforeach; ?>
          </div>
          <?php if (count($images) > 1): ?>
          <button class="carousel-control-prev" type="button" data-bs-target="#expGallery" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#expGallery" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
          <?php endif; ?>
        </div>
        <?php else: ?>
        <div style="border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 16/9; background: var(--green); margin-bottom: 32px;"></div>
        <?php endif; ?>

        <?php if (!empty($experience['description'])): ?>
        <p style="white-space: pre-line; font-size: 1.05rem;"><?php echo nl2br(htmlspecialchars($experience['description'])); ?></p>
        <?php endif; ?>

        <div class="text-center mt-4">
          <a href="contact.php" class="btn-hero btn-hero-primary" style="display: inline-flex;">Plan Your Own Journey</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
