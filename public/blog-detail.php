<?php
/**
 * Pentagon Quest — Blog Detail Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\BlogService;

$blogService = new BlogService();
$post = $blogService->find((int) ($_GET['id'] ?? 0));

if (!$post || ($post['status'] ?? 'inactive') !== 'active') {
    http_response_code(404);
}

$page_title = $post ? $post['title'] : 'Story Not Found';
$page_description = $post ? ($post['excerpt'] ?? '') : 'The requested story could not be found.';
$current_page = 'blog.php';
$base_path = '';
include __DIR__ . '/includes/header.php';
?>

<section class="page-hero" style="min-height: 420px; padding: 120px 0 70px;">
  <div class="hero-video-bg" style="<?php echo $post ? pq_cover_style($post['image_url'] ?? '', 'var(--charcoal)') : ''; ?>"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);"><?php echo htmlspecialchars($post['category'] ?? 'Story'); ?></span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4.2rem);"><?php echo htmlspecialchars($page_title); ?></h1>
    <?php if ($post): ?>
    <p style="color: rgba(255,255,255,0.75);"><?php echo htmlspecialchars(pq_format_date($post['created_at'] ?? '')); ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section-pad">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <?php if (!$post): ?>
        <div class="blog-card p-5 text-center">
          <h2 class="mb-3">Story not found</h2>
          <a href="<?php echo pq_url('blog.php'); ?>" class="btn-hero btn-hero-primary d-inline-flex">Back to Blog</a>
        </div>
        <?php else: ?>
        <article class="blog-card" style="padding: clamp(28px, 5vw, 56px);">
          <?php if (!empty($post['excerpt'])): ?>
          <p style="font-size: 1.25rem; color: var(--text-main);"><?php echo htmlspecialchars($post['excerpt']); ?></p>
          <?php endif; ?>
          <div style="white-space: pre-line; font-size: 1.05rem; color: var(--text-muted); line-height: 1.85;"><?php echo htmlspecialchars($post['content'] ?: $post['excerpt'] ?: ''); ?></div>
        </article>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
