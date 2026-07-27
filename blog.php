<?php
/**
 * Pentagon Quest — Blog Page
 * SEO-optimised standalone page
 */
$page_title       = 'Safari Stories & Insights | Pentagon Quest Blog';
$page_description = 'Explore the wild heart of Africa through our stories. Photography tips, packing guides, and deep dives into the Great Migration.';
$current_page     = 'blog.php';
$base_path        = '';
include 'includes/header.php';

$posts = [
  ['title' => 'Top 10 Safari Photography Tips', 'date' => 'July 15, 2026', 'cat' => 'Photography', 'desc' => 'Capture the perfect shot with our expert guide to wildlife photography in the African bush.'],
  ['title' => 'What to Pack for Your First Safari', 'date' => 'July 10, 2026', 'cat' => 'Travel Guide', 'desc' => 'From neutral clothing to essential gear, here is everything you need to pack for your adventure.'],
  ['title' => 'Understanding the Great Migration', 'date' => 'July 05, 2026', 'cat' => 'Wildlife', 'desc' => 'A deep dive into one of nature\'s greatest spectacles: the annual trek of millions of wildebeest.'],
  ['title' => 'The Hidden Gems of Namibia', 'date' => 'June 28, 2026', 'cat' => 'Destinations', 'desc' => 'Beyond the dunes: discovering the secret landscapes and wildlife of the Namib desert.'],
  ['title' => 'A Guide to Cultural Etiquette', 'cat' => 'Culture', 'date' => 'June 20, 2026', 'desc' => 'How to respectfully engage with local communities during your African safari.'],
  ['title' => 'Sustainable Safari: Our Commitment', 'cat' => 'Sustainability', 'date' => 'June 15, 2026', 'desc' => 'Learn how Pentagon Quest is working to preserve Africa\'s wild spaces for future generations.']
];
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
          <div style="height: 230px; background: var(--sand); position: relative;">
            <svg width="100%" height="100%" viewBox="0 0 400 230" opacity="0.1">
              <rect width="400" height="230" fill="var(--green)"/>
            </svg>
            <span style="position: absolute; top: 20px; left: 20px; background: var(--gold); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;"><?php echo $post['cat']; ?></span>
          </div>
          <div style="padding: 30px;">
            <span style="font-size: 0.8rem; opacity: 0.5; display: block; margin-bottom: 10px;"><?php echo $post['date']; ?></span>
            <h3 style="font-size: 1.25rem; margin-bottom: 15px;"><?php echo $post['title']; ?></h3>
            <p style="font-size: 0.95rem; margin-bottom: 25px;"><?php echo $post['desc']; ?></p>
            <a href="#" style="font-weight: 700; color: var(--gold); font-size: 0.9rem; border-bottom: 2px solid var(--gold); padding-bottom: 2px;">Read Full Story</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-5">
      <nav aria-label="Page navigation">
        <ul class="pagination" style="gap: 10px;">
          <li class="page-item"><a class="page-link active" href="#" style="background: var(--gold); border-color: var(--gold); color: #fff; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">1</a></li>
          <li class="page-item"><a class="page-link" href="#" style="background: transparent; border-color: #ddd; color: var(--charcoal); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">2</a></li>
          <li class="page-item"><a class="page-link" href="#" style="background: transparent; border-color: #ddd; color: var(--charcoal); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">3</a></li>
        </ul>
      </nav>
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
        <form class="mt-4 d-flex gap-2">
          <input type="email" placeholder="Your email address" style="flex: 1; padding: 15px 25px; border-radius: 40px; border: 1px solid #ddd; outline: none;">
          <button type="submit" class="btn-hero btn-hero-primary" style="border: none;">Subscribe</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
