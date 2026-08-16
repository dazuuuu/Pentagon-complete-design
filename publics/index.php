<?php
/**
 * Pentagon Quest — Home Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\BlogService;
use App\Services\DestinationService;
use App\Services\ExperienceService;
use App\Services\OfferService;
use App\Services\TestimonialService;

$destinationService = new DestinationService();
$testimonialService = new TestimonialService();
$experienceService = new ExperienceService();
$offerService = new OfferService();
$blogService = new BlogService();

$destinations = $destinationService->getFeatured(3);
$testimonials = $testimonialService->getActive();
$experiences = $experienceService->getActive();
$offers = array_slice($offerService->getActive(), 0, 2);
$blogs = array_slice($blogService->getActive(), 0, 3);

if ($experiences === []) {
    $experiences = [
        ['title' => 'The Great Migration Expedition', 'description' => 'A glimpse into the authentic African journeys we craft.'],
        ['title' => 'Cultural Immersion', 'description' => 'Respectful encounters connected to Africa\'s living culture.'],
    ];
}

if ($offers === []) {
    $offers = [
        ['title' => 'Early Bird Safari 2026', 'badge' => 'Limited Time', 'description' => 'Book your 2026 safari by December and enjoy 15% off all inclusive packages.', 'target_url' => 'contact.php'],
        ['title' => 'Self-Drive Expedition', 'badge' => 'New Launch', 'description' => 'Experience the freedom of Africa with our new fully-equipped 4x4 self-drive rentals.', 'target_url' => 'services.php'],
    ];
}

$page_title       = 'Pentagon Quest — Authentic African Safari Expeditions';
$page_description = 'Discover the heart of Africa with Pentagon Quest. Bespoke 4x4 wildlife expeditions, cultural immersions, and luxury safari experiences.';
$current_page     = 'index.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Refined Hero Section -->
<section class="modern-hero">
  <div class="hero-video-bg"></div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">

        <div class="reveal">
          <h1 class="hero-title" style="font-family: var(--font-display);">Bespoke African Safari Expeditions</h1>
          <p class="hero-subtitle">Crafting authentic journeys across the wild heart of the continent.</p>

          <div class="hero-btns">
            <a href="<?php echo $base; ?>destinations.php" class="btn-hero btn-hero-primary">
              Explore Destinations
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="<?php echo $base; ?>contact.php" class="btn-hero" style="background: #fff; color: #121212;">
              Plan Your Journey
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>
        </div>

        <!-- Search Bar -->
        <form class="search-row-wrap reveal" style="transition-delay: 0.2s;" action="<?php echo $base; ?>destinations.php" method="get">
          <div class="search-field">
            <label>Destination</label>
            <select name="destination">
              <option value="">All Destinations</option>
              <?php foreach ($destinationService->getActive() as $option): ?>
              <option value="<?php echo htmlspecialchars($option['country'] ?? $option['name']); ?>"><?php echo htmlspecialchars($option['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="search-field">
            <label>Experience</label>
            <select name="type">
              <option value="">Wildlife Safari</option>
              <option>Cultural Tour</option>
              <option>Gorilla Trekking</option>
            </select>
          </div>
          <div class="search-field">
            <label>Duration</label>
            <select name="duration">
              <option value="">7-14 Days</option>
            </select>
          </div>
          <button class="btn-search-go" type="submit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </button>
        </form>

      </div>
    </div>
  </div>
</section>

<!-- 1. Popular Destinations -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Where to go</span>
      <h2 class="section-title-modern">Popular Destinations</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($destinations as $d): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="blog-card">
          <div style="height: 300px; <?php echo pq_cover_style($d['image_url'] ?? '', 'var(--green)'); ?>; position: relative;">
            <svg width="100%" height="100%" viewBox="0 0 400 300" opacity="0.6">
              <rect width="400" height="300" fill="transparent"/>
              <path d="M0,300 Q100,200 200,250 Q300,300 400,200 L400,300 L0,300 Z" fill="rgba(255,255,255,0.1)"/>
            </svg>
            <div style="position: absolute; bottom: 20px; left: 20px; color: #fff;">
              <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--gold);"><?php echo htmlspecialchars($d['country']); ?></span>
              <h3 style="margin: 5px 0 0;"><?php echo htmlspecialchars($d['name']); ?></h3>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 2. Previous Experiences -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="row align-items-center mb-5">
      <div class="col-lg-6">
        <span class="section-tag">Our Legacy</span>
        <h2 class="section-title-modern">Previous Experiences</h2>
      </div>
      <div class="col-lg-6 text-lg-end">
        <p>A glimpse into the authentic African journeys we've crafted for explorers over the years.</p>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach (array_slice($experiences, 0, 2) as $i => $experience): ?>
      <div class="<?php echo $i === 0 ? 'col-lg-8' : 'col-lg-4'; ?> reveal">
        <div class="experience-item" style="<?php echo $i === 0 ? 'background: var(--green);' : 'background: var(--gold);'; ?>">
          <svg width="100%" height="100%" viewBox="0 0 <?php echo $i === 0 ? '800' : '400'; ?> 450" opacity="0.4">
            <rect width="<?php echo $i === 0 ? '800' : '400'; ?>" height="450" fill="<?php echo $i === 0 ? 'var(--green)' : 'var(--gold)'; ?>"/>
            <text x="50%" y="50%" text-anchor="middle" fill="#fff" font-family="var(--font-heading)" font-size="<?php echo $i === 0 ? '24' : '20'; ?>"><?php echo htmlspecialchars(strtoupper($experience['title'])); ?></text>
          </svg>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 3. Testimonials -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Feedback</span>
      <h2 class="section-title-modern">What Our Explorers Say</h2>
    </div>
    <div class="row g-4">
      <?php foreach (array_slice($testimonials, 0, 2) as $testimonial): ?>
      <div class="col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); border-left: 5px solid var(--<?php echo htmlspecialchars($testimonial['accent_color'] ?? 'gold'); ?>); box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
          <p style="font-style: italic; font-size: 1.1rem; margin-bottom: 20px;">"<?php echo htmlspecialchars($testimonial['quote']); ?>"</p>
          <h5 style="margin-bottom: 0;"><?php echo htmlspecialchars($testimonial['author_name']); ?></h5>
          <span style="font-size: 0.8rem; opacity: 0.6;"><?php echo htmlspecialchars($testimonial['author_location'] ?? ''); ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 4. Exclusive Offers -->
<section class="section-pad" style="background: var(--charcoal); color: #fff;">
  <div class="container">
    <div class="row g-4">
      <?php foreach (array_slice($offers, 0, 2) as $i => $offer): ?>
      <div class="col-lg-6 reveal">
        <div class="offer-card"<?php echo $i === 1 ? ' style="background: var(--gold); color: var(--charcoal);"' : ''; ?>>
          <span class="section-tag" style="color: <?php echo $i === 1 ? 'var(--green)' : 'var(--gold-soft)'; ?>;"><?php echo htmlspecialchars($offer['badge'] ?? 'Offer'); ?></span>
          <h3 style="<?php echo $i === 1 ? '' : 'color: #fff; '; ?>margin-bottom: 15px;"><?php echo htmlspecialchars($offer['title']); ?></h3>
          <p style="<?php echo $i === 1 ? 'opacity: 0.8; ' : 'color: rgba(255,255,255,0.7); '; ?>margin-bottom: 25px;"><?php echo htmlspecialchars($offer['description'] ?? ''); ?></p>
          <a href="<?php echo $base . ltrim($offer['target_url'] ?? 'contact.php', '/'); ?>" class="btn-hero" style="<?php echo $i === 1 ? 'background: var(--charcoal); color: #fff;' : 'background: #fff; color: var(--charcoal);'; ?>"><?php echo $i === 1 ? 'Learn More' : 'Claim Offer'; ?></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 5. Latest from the Blog -->
<section class="section-pad">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-6">
        <span class="section-tag">Stories</span>
        <h2 class="section-title-modern">Latest from the Blog</h2>
      </div>
      <div class="col-lg-6 text-lg-end">
        <a href="<?php echo $base; ?>blog.php" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">View All Stories</a>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($blogs as $b): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="blog-card">
          <div style="height: 200px; <?php echo pq_cover_style($b['image_url'] ?? '', 'var(--sand)'); ?>;">
            <svg width="100%" height="100%" viewBox="0 0 400 200" opacity="0.2">
              <rect width="400" height="200" fill="var(--green)"/>
            </svg>
          </div>
          <div style="padding: 24px;">
            <span style="font-size: 0.75rem; opacity: 0.5;"><?php echo htmlspecialchars(pq_format_date($b['created_at'] ?? $b['date'] ?? '')); ?></span>
            <h4 style="margin: 10px 0 20px; font-size: 1.1rem;"><?php echo htmlspecialchars($b['title']); ?></h4>
            <a href="<?php echo $base; ?>blog.php" style="font-weight: 700; color: var(--gold); font-size: 0.9rem;">Read Story →</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
