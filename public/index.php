<?php
/**
 * Pentagon Quest — Home Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\BlogService;
use App\Services\DestinationService;
use App\Services\ExperienceService;
use App\Services\FeaturedPackageService;
use App\Services\HomeMediaService;
use App\Services\OfferService;
use App\Services\TestimonialService;
use App\Services\TourService;

$destinationService = new DestinationService();
$testimonialService = new TestimonialService();
$experienceService = new ExperienceService();
$featuredPackageService = new FeaturedPackageService();
$homeMediaService = new HomeMediaService();
$offerService = new OfferService();
$blogService = new BlogService();
$tourService = new TourService();

$destinations = $destinationService->getFeatured(3);
$testimonials = $testimonialService->getActive();
$experiences = $experienceService->getActive();
$offers = array_slice($offerService->getActive(), 0, 2);
$blogs = array_slice($blogService->getActive(), 0, 3);
$activeTours = $tourService->getActive();
$tourTypes = array_values(array_unique(array_filter(array_map(static fn (array $tour): string => (string) ($tour['type'] ?? ''), $activeTours))));
$tourDurations = array_values(array_unique(array_filter(array_map(static fn (array $tour): string => (string) ($tour['dur'] ?? ''), $activeTours))));
sort($tourTypes);
sort($tourDurations);
$homeHeroVideo = $homeMediaService->heroVideoPath();
$posters = $homeMediaService->showPosters() ? $homeMediaService->activePosters() : [];
$featuredPackageRows = $featuredPackageService->getActive();

$page_title       = 'Pentagon Quest — Authentic African Safari Expeditions';
$page_description = 'Discover the heart of Africa with Pentagon Quest. Bespoke 4x4 wildlife expeditions, cultural immersions, and luxury safari experiences.';
$current_page     = 'index.php';
$base_path        = '';
include __DIR__ . '/includes/header.php';
?>

<!-- Refined Hero Section -->
<section class="modern-hero">
  <div class="hero-video-bg">
    <video autoplay muted loop playsinline poster="<?php echo $base; ?>assets/images/start-here.jpeg">
      <?php
        $heroVideoExt = strtolower(pathinfo($homeHeroVideo, PATHINFO_EXTENSION));
        $heroVideoType = $heroVideoExt === 'webm' ? 'webm' : ($heroVideoExt === 'mov' ? 'quicktime' : 'mp4');
      ?>
      <source src="<?php echo $base . ltrim($homeHeroVideo, '/'); ?>" type="video/<?php echo $heroVideoType; ?>">
    </video>
  </div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">

        <div class="reveal">
          <div class="hero-eyebrow">Groups, Couples, Families</div>
          <h1 class="hero-title" style="font-family: var(--font-display);">We make every safari a wonderful experience &amp; memories.</h1>

          <div class="hero-btns">
            <a href="<?php echo pq_url('destinations.php'); ?>" class="btn-hero btn-hero-primary">
              Plan a custom Tour
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="<?php echo pq_url('contact.php'); ?>" class="btn-hero" style="background: #fff; color: #121212;">
              Plan Your Journey
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>
        </div>

        <!-- Search Bar -->
        <form class="search-row-wrap reveal" style="transition-delay: 0.2s;" action="<?php echo pq_url('destinations.php'); ?>" method="get">
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
              <option value="">All Experiences</option>
              <?php foreach ($tourTypes as $type): ?>
              <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="search-field">
            <label>Duration</label>
            <select name="duration">
              <option value="">Any Duration</option>
              <?php foreach ($tourDurations as $duration): ?>
              <option value="<?php echo htmlspecialchars($duration); ?>"><?php echo htmlspecialchars($duration); ?></option>
              <?php endforeach; ?>
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

<?php if ($featuredPackageRows !== []): ?>
<!-- Featured Packages -->
<section class="featured-packages-section" id="featured-packages">
  <div class="container">
    <div class="featured-packages-head reveal">
      <div>
        <h2>Featured packages ready<br>for booking.</h2>
        <p>Select an offer poster, view the key package details, then call or WhatsApp Pentagon Safaris to reserve your spot.</p>
      </div>
      <a class="featured-packages-view" href="#posters">View All Offers</a>
    </div>

    <div class="featured-package-layout">
      <div class="package-rate-card reveal">
        <div class="package-rate-card-top">
          <div>
            <span>Pentagon Safaris · The Safari Xplus</span>
            <h3>Coast Kenya</h3>
          </div>
          <a class="package-book-pill" href="<?php echo pq_url('contact.php'); ?>">Book Now</a>
        </div>
        <div class="package-rate-scroll">
          <table class="package-rate-table">
            <thead>
              <tr>
                <th>Hotel</th>
                <th>Meal</th>
                <th>Location</th>
                <th>2 Nights</th>
                <th>3 Nights</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($featuredPackageRows as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['hotel']); ?></td>
                <td><?php echo htmlspecialchars($row['meal']); ?></td>
                <td><?php echo htmlspecialchars($row['location']); ?></td>
                <td><?php echo htmlspecialchars($row['two_nights_price']); ?></td>
                <td><?php echo htmlspecialchars($row['three_nights_price']); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="package-card-footer">@pentagonsafaris | +254720 090751 | info@pentagonsafaris.com | www.pentagonsafaris.com</div>
      </div>

      <div class="package-summary reveal">
        <span class="section-tag">Coast Kenya Offers</span>
        <h3>Beach hotel packages for Watamu, Diani, and Bamburi.</h3>
        <p>Compare coastal hotel stays for two or three nights, including all-inclusive and breakfast options across selected resorts.</p>
        <table class="package-summary-table">
          <thead>
            <tr>
              <th>Offer</th>
              <th>Detail</th>
              <th>Price</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (array_slice($featuredPackageRows, 0, 4) as $row): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['hotel']); ?></td>
              <td><?php echo htmlspecialchars($row['location']); ?></td>
              <td>From <?php echo htmlspecialchars($row['two_nights_price']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="package-actions">
          <a class="package-action-primary" href="tel:+254720090751">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.08 5.18 2 2 0 0 1 5.06 3h3a2 2 0 0 1 2 1.72c.12.9.32 1.77.59 2.61a2 2 0 0 1-.45 2.11L9 10.64a16 16 0 0 0 4.36 4.36l1.2-1.2a2 2 0 0 1 2.11-.45c.84.27 1.71.47 2.61.59A2 2 0 0 1 22 16.92z"/></svg>
            Call to Book
          </a>
          <a class="package-action-secondary" href="https://wa.me/254720090751">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-8.5 8.5 8.71 8.71 0 0 1-4.19-1.06L3 21l1.78-5.53A8.4 8.4 0 1 1 21 11.5z"/></svg>
            WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($posters !== []): ?>
<!-- Homepage Posters -->
<section class="section-pad posters-section" id="posters">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-7">
        <span class="section-tag">Featured Posters</span>
        <h2 class="section-title-modern">Safari Highlights</h2>
      </div>
      <div class="col-lg-5 text-lg-end">
        <p>Seasonal trips, signature experiences, and handpicked safari moments.</p>
      </div>
    </div>
    <div class="poster-grid">
      <?php foreach ($posters as $poster): ?>
      <?php $posterUrl = str_starts_with($poster['image_url'], 'http') ? $poster['image_url'] : $base . ltrim($poster['image_url'], '/'); ?>
      <button
        class="poster-card reveal"
        type="button"
        data-poster-lightbox
        data-poster-src="<?php echo htmlspecialchars($posterUrl, ENT_QUOTES, 'UTF-8'); ?>"
        data-poster-title="<?php echo htmlspecialchars($poster['title'], ENT_QUOTES, 'UTF-8'); ?>"
        data-poster-description="<?php echo htmlspecialchars($poster['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
        aria-label="Open <?php echo htmlspecialchars($poster['title'], ENT_QUOTES, 'UTF-8'); ?> poster"
      >
        <img src="<?php echo htmlspecialchars($posterUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($poster['title'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="poster-caption"><h3><?php echo htmlspecialchars($poster['title']); ?></h3><p><?php echo htmlspecialchars($poster['description'] ?? ''); ?></p></div>
      </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<div class="poster-lightbox" id="posterLightbox" aria-hidden="true">
  <button class="poster-lightbox-close" type="button" aria-label="Close poster">&times;</button>
  <div class="poster-lightbox-dialog" role="dialog" aria-modal="true" aria-labelledby="posterLightboxTitle">
    <img src="" alt="" id="posterLightboxImage">
    <div class="poster-lightbox-copy">
      <h3 id="posterLightboxTitle"></h3>
      <p id="posterLightboxDescription"></p>
    </div>
  </div>
</div>
<?php endif; ?>

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
          <a href="<?php echo pq_url($offer['target_url'] ?? 'contact.php'); ?>" class="btn-hero" style="<?php echo $i === 1 ? 'background: var(--charcoal); color: #fff;' : 'background: #fff; color: var(--charcoal);'; ?>"><?php echo $i === 1 ? 'Learn More' : 'Claim Offer'; ?></a>
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
        <a href="<?php echo pq_url('blog.php'); ?>" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">View All Stories</a>
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
            <a href="<?php echo pq_url('blog.php'); ?>" style="font-weight: 700; color: var(--gold); font-size: 0.9rem;">Read Story →</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
