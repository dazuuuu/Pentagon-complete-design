<?php
/**
 * Pentagon Quest — Blog Post Details Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\BlogService;

$blogService = new BlogService();
$postId = (int) ($_GET['id'] ?? 0);
$post = $postId ? $blogService->find($postId) : null;

if (!$post || $post['status'] !== 'active') {
    header('Location: blog.php');
    exit;
}

$cover = $post['image_url'] ?? '';
$hasCoverPhoto = $cover !== '' && (str_starts_with($cover, 'assets/') || str_starts_with($cover, 'http'));

$page_title       = $post['title'] . ' — Pentagon Quest Blog';
$page_description = !empty($post['excerpt']) ? mb_strimwidth(strip_tags($post['excerpt']), 0, 160, '...') : 'Read ' . $post['title'] . ' on the Pentagon Quest blog.';
$current_page     = 'blog.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 320px; padding: 100px 0 40px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);"><?php echo htmlspecialchars($post['category']); ?> · <?php echo htmlspecialchars(date('F j, Y', strtotime($post['created_at']))); ?></span>
    <h1 class="hero-title" style="font-size: clamp(1.8rem, 5vw, 3.2rem);"><?php echo htmlspecialchars($post['title']); ?></h1>
  </div>
</section>

<section class="section-pad" style="padding-top: 60px;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 reveal">

        <div style="border-radius: var(--radius-md); overflow: hidden; height: 360px; position: relative; background: var(--sand); margin-bottom: 32px;">
          <?php if ($hasCoverPhoto): ?>
            <img src="<?php echo htmlspecialchars($cover); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width:100%; height:100%; object-fit: cover;">
          <?php else: ?>
            <svg width="100%" height="100%" viewBox="0 0 800 360" style="opacity: 0.15;">
              <rect width="800" height="360" fill="var(--green)"/>
            </svg>
          <?php endif; ?>
        </div>

        <?php if (!empty($post['excerpt'])): ?>
        <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 24px;"><?php echo htmlspecialchars($post['excerpt']); ?></p>
        <?php endif; ?>

        <?php if (!empty($post['content'])): ?>
          <div style="white-space: pre-line; font-size: 1rem;"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
        <?php else: ?>
          <p>Full story coming soon.</p>
        <?php endif; ?>

        <a href="blog.php" style="display: inline-block; margin-top: 32px; font-weight: 600; font-size: 0.9rem; color: var(--green);">← Back to all stories</a>
      </div>
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
        <form method="POST" action="handlers/subscribe" class="mt-4 d-flex gap-2">
          <input type="hidden" name="redirect" value="/blog-details.php?id=<?php echo (int) $post['id']; ?>">
          <input type="email" name="email" placeholder="Your email address" required style="flex: 1; padding: 15px 25px; border-radius: 40px; border: 1px solid #ddd; outline: none;">
          <button type="submit" class="btn-hero btn-hero-primary" style="border: none;">Subscribe</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
