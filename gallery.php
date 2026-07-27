<?php
/**
 * Pentagon Quest — Gallery Page (Modern Redesign)
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\GalleryService;

$galleryService = new GalleryService();
$gallery = $galleryService->getActive();

$page_title       = 'Safari Gallery — Pentagon Quest';
$page_description = 'A visual journey through Africa\'s wildlife and landscapes.';
$current_page     = 'gallery.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Visual Journey</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Safari Gallery</h1>
  </div>
</section>

<!-- Gallery Grid -->
<section class="section-pad">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($gallery as $item): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div style="position: relative; border-radius: var(--radius-md); overflow: hidden; aspect-ratio: 4/3; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
          <svg width="100%" height="100%" viewBox="0 0 400 300">
            <rect width="400" height="300" fill="var(--green)"/>
            <path d="M0,300 Q100,200 200,250 Q300,300 400,200 L400,300 L0,300 Z" fill="var(--green-light)"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#fff" font-family="var(--font-heading)" font-size="18" opacity="0.8"><?php echo $item['title']; ?></text>
          </svg>
          <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent); display: flex; align-items: flex-end; padding: 24px; opacity: 0; transition: var(--transition);" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
            <div>
              <span style="color: var(--gold); font-size: 0.7rem; font-weight: 700; text-transform: uppercase;"><?php echo $item['cat']; ?></span>
              <h4 style="color: #fff; margin: 5px 0 0;"><?php echo $item['title']; ?></h4>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
