<?php
/**
 * Pentagon Quest — Blog Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\BlogService;

$blogService = new BlogService();
$posts = $blogService->getActive();

$page_title       = 'Safari Stories & Insights | Pentagon Quest Blog';
$page_description = 'Explore the wild heart of Africa through our stories. Photography tips, packing guides, and deep dives into the Great Migration.';
$current_page     = 'blog.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Safari Stories</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Our Blog</h1>
  </div>
</section>

<!-- Blog Grid -->
<section class="section-pad">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($posts as $post): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="blog-card">
          <div style="height: 230px; <?php echo pq_cover_style($post['image_url'] ?? '', 'var(--sand)'); ?>; position: relative;">
            <svg width="100%" height="100%" viewBox="0 0 400 230" opacity="0.1">
              <rect width="400" height="230" fill="var(--green)"/>
            </svg>
            <span style="position: absolute; top: 20px; left: 20px; background: var(--gold); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;"><?php echo htmlspecialchars($post['category'] ?? $post['cat'] ?? 'Story'); ?></span>
          </div>
          <div style="padding: 30px;">
            <span style="font-size: 0.8rem; opacity: 0.5; display: block; margin-bottom: 10px;"><?php echo htmlspecialchars(pq_format_date($post['created_at'] ?? $post['date'] ?? '')); ?></span>
            <h3 style="font-size: 1.25rem; margin-bottom: 15px;"><?php echo htmlspecialchars($post['title']); ?></h3>
            <p style="font-size: 0.95rem; margin-bottom: 25px;"><?php echo htmlspecialchars($post['excerpt'] ?? $post['desc'] ?? ''); ?></p>
            <a href="<?php echo pq_url('blog-detail.php?id=' . (int) $post['id']); ?>" style="font-weight: 700; color: var(--gold); font-size: 0.9rem; border-bottom: 2px solid var(--gold); padding-bottom: 2px;">Read Full Story</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if ($posts === []): ?>
      <div class="col-12">
        <div class="blog-card p-5 text-center">
          <h3 class="mb-2">No blog posts yet</h3>
          <p class="mb-0">Publish active posts from the admin Blog menu and they will appear here.</p>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Newsletter Section -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-lg-6">
        <span class="section-tag">Newsletter</span>
        <h2 class="section-title-modern">Join the Pride</h2>
        <p>Get monthly safari inspiration, wildlife updates, and exclusive offers delivered to your inbox.</p>
        <form class="mt-4 d-flex gap-2" method="POST" action="<?php echo pq_url('handlers/index.php'); ?>">
          <?php echo pq_csrf_field(); ?>
          <input type="hidden" name="_action" value="subscribe">
          <input type="hidden" name="redirect" value="<?php echo htmlspecialchars(pq_url('blog.php')); ?>">
          <input type="email" name="email" placeholder="Your email address" required style="flex: 1; padding: 15px 25px; border-radius: 40px; border: 1px solid #ddd; outline: none;">
          <button type="submit" class="btn-hero btn-hero-primary" style="border: none;">Subscribe</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
