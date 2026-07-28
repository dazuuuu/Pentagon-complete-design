<?php
/**
 * Pentagon Quest — Home Page (Refined & Restructured)
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\DestinationService;
use App\Services\TestimonialService;
use App\Services\ExperienceService;
use App\Services\OfferService;
use App\Services\BlogService;

$destinationService = new DestinationService();
$testimonialService = new TestimonialService();
$experienceService = new ExperienceService();
$offerService = new OfferService();
$blogService = new BlogService();
$destinations = $destinationService->getFeatured(3);
$testimonials = $testimonialService->getActive();
$experiences = $experienceService->getActive();
$featuredExperience = $experiences[0] ?? null;
$otherExperiences = array_slice($experiences, 1, 3);
$offers = array_slice($offerService->getActive(), 0, 2);
$blogs = array_slice($blogService->getActive(), 0, 3);

$page_title       = 'Pentagon Quest - Top Roadtrips & Tours Travel Company';
$page_description = 'Pentagon Quest provides leisure, business, safari, and exclusive travel services for clients who want reliable planning and memorable experiences.';
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
          <h1 class="hero-title" style="font-family: var(--font-display);">Travel That Feels Handled</h1>
          <p class="hero-subtitle">Curated travel experiences for people who want vivid places, clean planning, and trips that do not feel copied from everyone else's itinerary.</p>
          
          <div class="hero-btns">
            <a href="destinations.php" class="btn-hero btn-hero-primary">
              Explore Destinations 
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="contact.php" class="btn-hero" style="background: #fff; color: #121212;">
              Plan Your Journey
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>
        </div>

        <!-- Search Bar -->
        <div class="search-row-wrap reveal" style="transition-delay: 0.2s;">
          <div class="search-field">
            <label>Destination</label>
            <select><option>All Destinations</option><option>Kenya</option><option>Tanzania</option></select>
          </div>
          <div class="search-field">
            <label>Experience</label>
            <select><option>Wildlife Safari</option><option>Cultural Tour</option></select>
          </div>
          <div class="search-field">
            <label>Duration</label>
            <select><option>7-14 Days</option></select>
          </div>
          <button class="btn-search-go">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </button>
        </div>

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
      <?php foreach ($destinations as $d):
        $img = $d['image_url'] ?? '';
        $hasPhoto = $img !== '' && (str_starts_with($img, 'assets/') || str_starts_with($img, 'http'));
        $swatch = $hasPhoto ? 'var(--green)' : ($img !== '' ? $img : 'var(--green)');
        $detailUrl = !empty($d['id']) ? 'destination-details.php?id=' . (int) $d['id'] : null;
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <a href="<?php echo $detailUrl ? htmlspecialchars($detailUrl) : '#'; ?>" class="blog-card d-block" style="color: inherit;">
          <div style="height: 300px; <?php echo $hasPhoto ? '' : 'background: ' . htmlspecialchars($swatch) . ';'; ?> position: relative;">
            <?php if ($hasPhoto): ?>
              <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($d['name']); ?>" style="width:100%; height:100%; object-fit: cover; position: absolute; inset: 0;">
            <?php else: ?>
              <svg width="100%" height="100%" viewBox="0 0 400 300" opacity="0.6">
                <rect width="400" height="300" fill="<?php echo htmlspecialchars($swatch); ?>"/>
                <path d="M0,300 Q100,200 200,250 Q300,300 400,200 L400,300 L0,300 Z" fill="rgba(255,255,255,0.1)"/>
              </svg>
            <?php endif; ?>
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.55), transparent 55%);"></div>
            <div style="position: absolute; bottom: 20px; left: 20px; color: #fff;">
              <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--gold);"><?php echo htmlspecialchars($d['country']); ?></span>
              <h3 style="margin: 5px 0 0;"><?php echo htmlspecialchars($d['name']); ?></h3>
            </div>
          </div>
        </a>
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
      <div class="col-lg-8 reveal">
        <?php if ($featuredExperience): ?>
        <a href="experience-details.php?id=<?php echo (int) $featuredExperience['id']; ?>" class="experience-item d-block" style="background: var(--green); color: inherit;">
          <?php if (!empty($featuredExperience['images'])): ?>
          <div id="expFeatured" class="carousel slide h-100" data-bs-ride="carousel" data-bs-pause="false" style="position:absolute; inset:0;">
            <div class="carousel-inner h-100">
              <?php foreach ($featuredExperience['images'] as $i => $image): ?>
              <div class="carousel-item h-100 <?php echo $i === 0 ? 'active' : ''; ?>">
                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" style="width:100%; height:100%; object-fit:cover;">
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php else: ?>
          <svg width="100%" height="100%" viewBox="0 0 800 450" opacity="0.4" style="position:absolute; inset:0;">
            <rect width="800" height="450" fill="var(--green)"/>
          </svg>
          <?php endif; ?>
          <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.65), transparent 55%);"></div>
          <div style="position: absolute; bottom: 24px; left: 24px; right: 24px; color: #fff; z-index: 2;">
            <h3 style="font-family: var(--font-heading); margin-bottom: 8px;"><?php echo htmlspecialchars($featuredExperience['title']); ?></h3>
            <?php if (!empty($featuredExperience['description'])): ?>
            <p style="color: rgba(255,255,255,0.85); font-size: 0.95rem; margin-bottom: 0;"><?php echo htmlspecialchars($featuredExperience['description']); ?></p>
            <?php endif; ?>
          </div>
        </a>
        <?php else: ?>
        <div class="experience-item" style="background: var(--green);">
          <svg width="100%" height="100%" viewBox="0 0 800 450" opacity="0.4">
            <rect width="800" height="450" fill="var(--green)"/>
            <text x="50%" y="50%" text-anchor="middle" fill="#fff" font-family="var(--font-heading)" font-size="20">More stories coming soon</text>
          </svg>
        </div>
        <?php endif; ?>
      </div>
      <div class="col-lg-4 d-flex flex-column gap-4">
        <?php if (!empty($otherExperiences)): ?>
          <?php foreach ($otherExperiences as $exp): ?>
          <a href="experience-details.php?id=<?php echo (int) $exp['id']; ?>" class="experience-item d-block reveal flex-fill" style="background: var(--gold); color: inherit; min-height: 130px;">
            <?php if (!empty($exp['images'])): ?>
            <div id="expSmall<?php echo (int) $exp['id']; ?>" class="carousel slide h-100" data-bs-ride="carousel" data-bs-pause="false" style="position:absolute; inset:0;">
              <div class="carousel-inner h-100">
                <?php foreach ($exp['images'] as $i => $image): ?>
                <div class="carousel-item h-100 <?php echo $i === 0 ? 'active' : ''; ?>">
                  <img src="<?php echo htmlspecialchars($image['image_path']); ?>" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <?php endforeach; ?>
              </div>
            </div>
            <?php else: ?>
            <svg width="100%" height="100%" viewBox="0 0 400 200" opacity="0.4" style="position:absolute; inset:0;">
              <rect width="400" height="200" fill="var(--gold)"/>
            </svg>
            <?php endif; ?>
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.55), transparent 60%);"></div>
            <div style="position: absolute; bottom: 16px; left: 16px; right: 16px; color: #fff; z-index: 2;">
              <h5 style="font-family: var(--font-heading); margin-bottom: 0; font-size: 1rem;"><?php echo htmlspecialchars($exp['title']); ?></h5>
            </div>
          </a>
          <?php endforeach; ?>
        <?php else: ?>
        <div class="experience-item reveal" style="background: var(--gold);">
          <svg width="100%" height="100%" viewBox="0 0 400 450" opacity="0.4">
            <rect width="400" height="450" fill="var(--gold)"/>
          </svg>
        </div>
        <?php endif; ?>
      </div>
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
      <?php foreach ($testimonials as $testimonial):
        $accent = $testimonial['accent_color'] ?? 'gold';
      ?>
      <div class="col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); border-left: 5px solid var(--<?php echo htmlspecialchars($accent); ?>); box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
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
<?php if (!empty($offers)): ?>
<section class="section-pad" style="background: var(--charcoal); color: #fff;">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($offers as $i => $offer): $gold = $i === 1; ?>
      <div class="col-lg-6 reveal">
        <div class="offer-card" <?php echo $gold ? 'style="background: var(--gold); color: var(--charcoal);"' : ''; ?>>
          <?php if (!empty($offer['badge'])): ?>
          <span class="section-tag" style="color: <?php echo $gold ? 'var(--green)' : 'var(--gold-soft)'; ?>;"><?php echo htmlspecialchars($offer['badge']); ?></span>
          <?php endif; ?>
          <h3 style="<?php echo $gold ? '' : 'color: #fff;'; ?> margin-bottom: 15px;"><?php echo htmlspecialchars($offer['title']); ?></h3>
          <?php if (!empty($offer['description'])): ?>
          <p style="<?php echo $gold ? 'opacity: 0.8;' : 'color: rgba(255,255,255,0.7);'; ?> margin-bottom: 25px;"><?php echo htmlspecialchars($offer['description']); ?></p>
          <?php endif; ?>
          <a href="<?php echo htmlspecialchars($offer['target_url']); ?>" class="btn-hero" style="<?php echo $gold ? 'background: var(--charcoal); color: #fff;' : 'background: #fff; color: var(--charcoal);'; ?>">Claim Offer</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- 5. Latest from the Blog -->
<section class="section-pad">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-6">
        <span class="section-tag">Stories</span>
        <h2 class="section-title-modern">Latest from the Blog</h2>
      </div>
      <div class="col-lg-6 text-lg-end">
        <a href="blog.php" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">View All Stories</a>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($blogs as $b):
        $bImg = $b['image_url'] ?? '';
        $bHasPhoto = $bImg !== '' && (str_starts_with($bImg, 'assets/') || str_starts_with($bImg, 'http'));
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <a href="blog-details.php?id=<?php echo (int) $b['id']; ?>" class="blog-card d-block" style="color: inherit;">
          <div style="height: 200px; background: var(--sand); position: relative;">
            <?php if ($bHasPhoto): ?>
              <img src="<?php echo htmlspecialchars($bImg); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>" style="width:100%; height:100%; object-fit: cover; position: absolute; inset: 0;">
            <?php else: ?>
              <svg width="100%" height="100%" viewBox="0 0 400 200" opacity="0.2">
                <rect width="400" height="200" fill="var(--green)"/>
              </svg>
            <?php endif; ?>
          </div>
          <div style="padding: 24px;">
            <span style="font-size: 0.75rem; opacity: 0.5;"><?php echo htmlspecialchars(date('F j, Y', strtotime($b['created_at']))); ?></span>
            <h4 style="margin: 10px 0 20px; font-size: 1.1rem;"><?php echo htmlspecialchars($b['title']); ?></h4>
            <span style="font-weight: 700; color: var(--gold); font-size: 0.9rem;">Read Story →</span>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
      <?php if (empty($blogs)): ?>
      <div class="col-12 text-center"><p>No stories published yet.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
