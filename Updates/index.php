<?php
/**
 * Pentagon Safaris — Home Page (Refined & Restructured)
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\DestinationService;
use App\Services\TestimonialService;
use App\Services\ExperienceService;
use App\Services\OfferService;
use App\Services\BlogService;
use App\Services\GalleryService;

$destinationService = new DestinationService();
$testimonialService = new TestimonialService();
$experienceService = new ExperienceService();
$offerService = new OfferService();
$blogService = new BlogService();
$galleryService = new GalleryService();
$destinations = $destinationService->getFeatured(3);
$directions = array_slice($destinationService->getActive(), 0, 4);
$testimonials = $testimonialService->getActive();
$experiences = $experienceService->getActive();
$featuredExperience = $experiences[0] ?? null;
$otherExperiences = array_slice($experiences, 1, 3);
$offers = array_slice($offerService->getActive(), 0, 2);
$blogs = array_slice($blogService->getActive(), 0, 3);
$galleryPreview = array_slice($galleryService->getActive(), 0, 12);

$page_title       = 'Pentagon Safaris - Top Roadtrips & Tours Travel Company';
$page_description = 'Pentagon Safaris provides leisure, business, safari, and exclusive travel services for clients who want reliable planning and memorable experiences.';
$current_page     = 'index.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Refined Hero Section -->
<section class="modern-hero">
  <video class="hero-video" autoplay muted loop playsinline>
    <source src="<?php echo $base; ?>assets/videos/safaris.mp4" type="video/mp4">
  </video>
  <div class="hero-video-overlay"></div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">

        <div class="reveal">
          <span class="section-tag" style="color: var(--gold-soft); text-align: center;">Groups, Couples, Families</span>
          <h1 class="hero-title" style="font-family: var(--font-display);">We make every safari a wonderful experience &amp; memories.</h1>

          <div class="hero-btns">
            <a href="<?php echo $base; ?>request-quote" class="btn-hero" style="background: var(--gold); color: var(--charcoal);">
              Plan a Custom Tour
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="<?php echo $base; ?>tours" class="btn-hero" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: #fff;">
              See Packages
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

<!-- Featured Packages / Coast Kenya Offers -->
<section class="section-pad" style="background: var(--charcoal); color: #fff;">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-7">
        <span class="section-tag">Current Offers</span>
        <h2 class="section-title-modern" style="color: #fff;">Featured packages ready for booking.</h2>
        <p style="color: rgba(255,255,255,0.6); max-width: 520px;">Select an offer poster, view the key package details, then call or WhatsApp Pentagon Safaris to reserve your spot.</p>
      </div>
      <div class="col-lg-5 text-lg-end">
        <a href="#exclusive-offers" class="btn-hero" style="background: #fff; color: var(--charcoal); display: inline-flex;">View All Offers</a>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-7 reveal">
        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); padding: 32px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
            <div>
              <span style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--gold); font-weight: 700;">Pentagon Safaris &middot; The Safari Xplus</span>
              <h3 style="margin-top: 6px; margin-bottom: 0;">Coast Kenya</h3>
            </div>
            <a href="https://wa.me/254718620982" class="btn-hero" style="background: var(--gold); color: var(--charcoal); padding: 12px 24px;">Book Now</a>
          </div>
          <div style="overflow-x: auto;">
            <table class="pq-table">
              <thead>
                <tr><th>Hotel</th><th>Meal</th><th>Location</th><th>2 Nights</th><th>3 Nights</th></tr>
              </thead>
              <tbody>
                <?php
                $coastOffers = [
                  ['Turtle Bay Beach Club', 'All Inclusive', 'Watamu', '205', '289'],
                  ['Papillon Lagoon Reef', 'All Inclusive', 'Diani', '261', '526'],
                  ['Diani Reef Beach Resort & Spa', 'Breakfast', 'Diani', '366', '530'],
                  ['Neptune Beach Hotel', 'All Inclusive', 'Bamburi', '372', '539'],
                  ['Diani Sea Resort', 'All Inclusive', 'Diani', '392', '568'],
                  ['Bamburi Beach Hotel', 'All Inclusive', 'Bamburi', '445', '649'],
                  ['Diani Sea Lodge', 'All Inclusive', 'Diani', '275', '393'],
                  ['Southern Pal Beach Resort', 'All Inclusive', 'Diani', '507', '741'],
                  ['Leopard Beach Resort & Spa', 'Breakfast', 'Diani', '215', '303'],
                ];
                foreach ($coastOffers as $row): ?>
                <tr>
                  <td style="font-weight: 600;"><?php echo htmlspecialchars($row[0]); ?></td>
                  <td style="color: var(--gold-soft);"><?php echo htmlspecialchars($row[1]); ?></td>
                  <td><?php echo htmlspecialchars($row[2]); ?></td>
                  <td>$<?php echo htmlspecialchars($row[3]); ?></td>
                  <td>$<?php echo htmlspecialchars($row[4]); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.85rem; color: rgba(255,255,255,0.6);">
            @pentagonsafaris &nbsp;|&nbsp; +254 718 620982 / +254 726 528015 &nbsp;|&nbsp; pentagonquest@gmail.com &nbsp;|&nbsp; www.pentagonquest.com
          </div>
        </div>
      </div>

      <div class="col-lg-5 reveal">
        <span class="section-tag">Coast Kenya Offers</span>
        <h3 style="color: #fff; margin-bottom: 16px;">Beach hotel packages for Watamu, Diani, and Bamburi.</h3>
        <p style="color: rgba(255,255,255,0.6); margin-bottom: 24px;">Compare coastal hotel stays for two or three nights, including all-inclusive and breakfast options across selected resorts.</p>

        <table class="pq-table" style="margin-bottom: 24px;">
          <thead><tr><th>Offer</th><th>Detail</th><th>Price</th></tr></thead>
          <tbody>
            <tr><td style="font-weight: 600;">Turtle Bay Beach Club</td><td style="color: var(--gold-soft);">Watamu</td><td>From $205</td></tr>
            <tr><td style="font-weight: 600;">Papillon Lagoon Reef</td><td style="color: var(--gold-soft);">Diani</td><td>From $261</td></tr>
            <tr><td style="font-weight: 600;">Diani Reef Beach Resort & Spa</td><td style="color: var(--gold-soft);">Diani</td><td>From $366</td></tr>
          </tbody>
        </table>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <a href="tel:+254718620982" class="btn-hero" style="background: var(--gold); color: var(--charcoal);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            Call to Book
          </a>
          <a href="https://wa.me/254718620982" class="btn-hero" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: #fff;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            WhatsApp
          </a>
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
        $detailUrl = !empty($d['id']) ? $base . 'destinations/' . (int) $d['id'] : null;
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

<!-- About Pentagon Safaris -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <div style="position: relative; border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); min-height: 480px;">
          <img src="assets/images/climbing-mt-kenya.jpg" alt="Pentagon Safaris" style="width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0;">
          <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(18,18,18,0.75), transparent 55%);"></div>
          <div class="promise-badge">
            <span>Our Promise</span>
            <p>We do not just sell packages. We design travel solutions.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <span class="section-tag">About Pentagon Safaris</span>
        <h2 class="section-title-modern">A Kenyan travel company built around care, flexibility, and detail.</h2>
        <p style="margin-bottom: 32px;">Based in Nairobi, Pentagon Safaris provides leisure, business, safari, and exclusive travel services for individuals and groups. Our work is rooted in professional standards, ethical service, and the belief that excellent travel is created through near-obsessive attention to detail.</p>

        <div class="row g-3 mb-4">
          <div class="col-4"><div class="stat-box"><h4>KE.</h4><span>Proudly Kenyan</span></div></div>
          <div class="col-4"><div class="stat-box"><h4>24/7</h4><span>Travel Accessibility</span></div></div>
          <div class="col-4"><div class="stat-box"><h4>E.A.</h4><span>East Africa Expertise</span></div></div>
        </div>

        <div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg></div>
            <div>
              <h5>Dream-first planning</h5>
              <p>We start by understanding the feeling, purpose, and pace of your trip before shaping the itinerary.</p>
            </div>
          </div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 20l-5.5-2.5a1 1 0 0 1-.5-.87V5a1 1 0 0 1 1.5-.87L9 6.5"/><path d="M9 20l6-3 6 3V6.5L15 3.5 9 6.5"/><path d="M9 6.5v13.5"/><path d="M15 3.5v16.5"/></svg></div>
            <div>
              <h5>Custom-fit routes</h5>
              <p>From weekend getaways to complex business travel, every journey is tailored around your needs.</p>
            </div>
          </div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8.5a5.5 5.5 0 0 1 9-4.24A5.5 5.5 0 0 1 21 8.5c0 5-9 12-9 12s-9-7-9-12z"/></svg></div>
            <div>
              <h5>Human support</h5>
              <p>Our team stays available, practical, and close so travel feels organized from start to finish.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Our Services -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Our Services</span>
      <h2 class="section-title-modern">Travel support for the whole journey, not just the booking.</h2>
      <p style="max-width: 640px; margin: 0 auto;">Pentagon Safaris brings together transport, stays, ticketing, tours, and group logistics so every moving part feels intentional.</p>
    </div>
    <div class="row g-4">
      <?php
      $homeServices = [
        ['icon' => '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-1 .1-1.3.5l-.7.8c-.4.5-.3 1.2.3 1.5L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 2.8 6.6c.3.6 1 .7 1.5.3l.8-.7c.4-.3.6-.8.5-1.3z"/>', 'title' => 'Airport Transfers', 'desc' => 'Efficient, reliable transfers planned around your arrival, departure, group size, and comfort needs.'],
        ['icon' => '<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M9 6v12" stroke-dasharray="2 2"/>', 'title' => 'Air Ticketing', 'desc' => 'Domestic, regional, and international air ticketing support with practical routing guidance.'],
        ['icon' => '<path d="M3 21h18M5 21V9l7-5 7 5v12M9 21v-6h6v6"/>', 'title' => 'Hotel Bookings', 'desc' => 'Affordable, mid-range, and luxury accommodation options matched to your budget and travel style.'],
        ['icon' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/>', 'title' => 'Safaris In Africa', 'desc' => 'Eastern and southern Africa safari planning for wildlife, culture, landscapes, and group experiences.'],
        ['icon' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>', 'title' => 'MICE Travel', 'desc' => 'Meetings, incentives, conferences, and exhibitions with complete program and logistics management.'],
        ['icon' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>', 'title' => 'Local & International Tours', 'desc' => 'Kenya getaways, beach tours, city tours, and international escapes across Africa, Europe, UAE, and Asia.'],
      ];
      foreach ($homeServices as $s): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="service-mini-card">
          <div class="icon-badge"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $s['icon']; ?></svg></div>
          <div>
            <h5><?php echo htmlspecialchars($s['title']); ?></h5>
            <p><?php echo htmlspecialchars($s['desc']); ?></p>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Featured Directions -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-8">
        <span class="section-tag">Featured Directions</span>
        <h2 class="section-title-modern">Start local. Go global. Keep the planning sharp.</h2>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="<?php echo $base; ?>request-quote" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">Request an Itinerary →</a>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($directions as $dir):
        $dImg = $dir['image_url'] ?? '';
        $dHasPhoto = $dImg !== '' && (str_starts_with($dImg, 'assets/') || str_starts_with($dImg, 'http'));
        $dSwatch = $dHasPhoto ? 'var(--green)' : ($dImg !== '' ? $dImg : 'var(--green)');
        $dUrl = !empty($dir['id']) ? $base . 'destinations/' . (int) $dir['id'] : $base . 'destinations';
      ?>
      <div class="col-lg-3 col-md-6 reveal">
        <a href="<?php echo htmlspecialchars($dUrl); ?>" class="direction-card" <?php echo $dHasPhoto ? '' : 'style="background: ' . htmlspecialchars($dSwatch) . ';"'; ?>>
          <?php if ($dHasPhoto): ?>
          <img src="<?php echo htmlspecialchars($dImg); ?>" alt="<?php echo htmlspecialchars($dir['name']); ?>" style="width:100%; height:100%; object-fit: cover; position: absolute; inset: 0;">
          <?php endif; ?>
          <div class="direction-overlay"></div>
          <div class="direction-content">
            <span class="direction-label"><?php echo htmlspecialchars($dir['country'] ?? ''); ?></span>
            <h4 style="color: #fff; margin-bottom: 0;"><?php echo htmlspecialchars($dir['name']); ?></h4>
            <?php if (!empty($dir['description'])): ?>
            <p><?php echo htmlspecialchars($dir['description']); ?></p>
            <?php endif; ?>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
      <?php if (empty($directions)): ?>
      <div class="col-12 text-center"><p>More directions coming soon.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Travel Gallery -->
<section class="section-pad">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-8">
        <span class="section-tag">Travel Gallery</span>
        <h2 class="section-title-modern">Seen on the road.</h2>
        <p style="max-width: 520px;">A curated preview of trips, places, and experiences from the Pentagon Safaris gallery.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="<?php echo $base; ?>gallery" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">View Full Gallery →</a>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($galleryPreview as $gi => $item):
        $gImg = $item['image_url'] ?? '';
        $gHasPhoto = $gImg !== '' && (str_starts_with($gImg, 'assets/') || str_starts_with($gImg, 'http'));
      ?>
      <div class="col-lg-3 col-md-6 reveal">
        <div class="gallery-tile">
          <?php if ($gHasPhoto): ?>
          <img src="<?php echo htmlspecialchars($gImg); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width:100%; height:100%; object-fit: cover; position: absolute; inset: 0;">
          <?php else: ?>
          <svg width="100%" height="100%" viewBox="0 0 400 300">
            <rect width="400" height="300" fill="var(--green)"/>
            <path d="M0,300 Q100,200 200,250 Q300,300 400,200 L400,300 L0,300 Z" fill="var(--green-light)"/>
          </svg>
          <?php endif; ?>
          <div class="gallery-tile-overlay"></div>
          <span class="gallery-tile-badge"><?php echo str_pad((string) ($gi + 1), 2, '0', STR_PAD_LEFT); ?></span>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($galleryPreview)): ?>
      <div class="col-12 text-center"><p>No gallery photos yet.</p></div>
      <?php endif; ?>
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
        <a href="<?php echo $base; ?>experiences/<?php echo (int) $featuredExperience['id']; ?>" class="experience-item d-block" style="background: var(--green); color: inherit;">
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
          <a href="<?php echo $base; ?>experiences/<?php echo (int) $exp['id']; ?>" class="experience-item d-block reveal flex-fill" style="background: var(--gold); color: inherit; min-height: 130px;">
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

<!-- Message from the Director -->
<section class="section-pad">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-4 reveal">
        <div class="director-photo-wrap">
          <img src="assets/images/pentagon-director.jpeg" alt="Director, Pentagon Safaris" style="width: 100%; height: 420px; object-fit: cover; display: block;">
          <div class="director-tag">
            <span>Director</span>
            <h5>Pentagon Safaris</h5>
          </div>
        </div>
      </div>
      <div class="col-lg-8 reveal">
        <span class="section-tag">Message from the Director</span>
        <h2 class="section-title-modern">Every journey deserves thoughtful planning, honest guidance, and a team that truly cares.</h2>
        <p style="margin-bottom: 20px;">Thank you for trusting Pentagon Safaris as your travel partner. It is a privilege to help you, your family, your team, or your clients discover places and experiences that stay with you long after the journey ends.</p>
        <p style="margin-bottom: 32px;">Whether you are planning a weekend escape, a family holiday, a safari, an international trip, or a corporate program, my promise is simple: we will listen carefully, plan professionally, and handle the details with the seriousness your travel deserves.</p>

        <div class="mb-4">
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20l9-5-9-5-9 5 9 5z"/><path d="M12 12 3 7l9-5 9 5-9 5z"/></svg></div>
            <div>
              <h5>Custom travel solutions</h5>
              <p>We listen first, then design local and international trips around your needs, timing, and budget.</p>
            </div>
          </div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div>
            <div>
              <h5>Business and group travel</h5>
              <p>Our team supports meetings, incentives, conferences, exhibitions, retreats, and group logistics.</p>
            </div>
          </div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.8 1.4 6.8L12 17.3 5.9 20.7l1.4-6.8-5.1-4.8 6.9-.8L12 2z"/></svg></div>
            <div>
              <h5>Detail-led service</h5>
              <p>Good travel is more than a good price. We focus on reliable planning, care, and memorable experiences.</p>
            </div>
          </div>
        </div>

        <a href="mailto:pentagonquest@gmail.com" class="btn-hero" style="background: var(--charcoal); color: #fff; display: inline-flex;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M22 6 12 13 2 6"/></svg>
          pentagonquest@gmail.com
        </a>
      </div>
    </div>
  </div>
</section>

<!-- MICE Tourism -->
<section class="section-pad" style="background: var(--charcoal); color: #fff;">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <span class="section-tag">MICE Tourism</span>
        <h2 class="section-title-modern" style="color: #fff;">Corporate travel that moves people and business forward.</h2>
        <p style="color: rgba(255,255,255,0.6); margin-bottom: 32px;">East Africa offers exceptional venues for meetings, incentives, conferences, and exhibitions. Pentagon Safaris helps design, plan, and manage programs that motivate teams, reward performance, and keep logistics calm.</p>

        <div class="row g-3">
          <?php
          $miceItems = [
            ['icon' => '<path d="M9 11V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6"/><rect x="4" y="11" width="16" height="10" rx="2"/>', 'label' => 'Program Design'],
            ['icon' => '<rect x="1" y="7" width="15" height="12" rx="2"/><path d="M16 11h3l4 4v4h-7z"/><circle cx="6" cy="21" r="2"/><circle cx="19" cy="21" r="2"/>', 'label' => 'Group Logistics'],
            ['icon' => '<circle cx="12" cy="8" r="6"/><path d="M9 14l-3 7 6-3 6 3-3-7"/>', 'label' => 'Incentive Travel'],
            ['icon' => '<path d="M17 20h5v-2a4 4 0 0 0-3-3.87"/><path d="M9 20H4v-2a4 4 0 0 1 3-3.87"/><circle cx="9" cy="7" r="4"/><circle cx="17" cy="7" r="4"/>', 'label' => 'Partner Coordination'],
          ];
          foreach ($miceItems as $m): ?>
          <div class="col-md-6">
            <div class="mice-mini-card">
              <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $m['icon']; ?></svg></div>
              <span><?php echo htmlspecialchars($m['label']); ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <div style="position: relative; border-radius: var(--radius-md); overflow: hidden; min-height: 420px;">
          <svg width="100%" height="100%" viewBox="0 0 600 450" style="position: absolute; inset: 0;">
            <rect width="600" height="450" fill="var(--green)"/>
            <path d="M0,450 Q150,350 300,400 Q450,450 600,350 L600,450 L0,450 Z" fill="var(--green-light)"/>
          </svg>
          <div class="support-badge">
            <span>Complete Support</span>
            <p>From venue search to guest movement, we manage the moving parts.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Why Choose Us -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Why Choose Us</span>
      <h2 class="section-title-modern">Professional enough for business. Personal enough for the trip of a lifetime.</h2>
    </div>
    <div class="row g-4">
      <?php
      $whyChooseUs = [
        ['icon' => '<path d="M8 21h8M12 17v4M7 4h10v4a5 5 0 0 1-10 0V4z"/><path d="M17 5h3a2 2 0 0 1-2 4M7 5H4a2 2 0 0 0 2 4"/>', 'title' => 'Exemplary service', 'desc' => 'High-quality customer care designed to help you experience Kenya, East Africa, and the world with confidence.'],
        ['icon' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 4-6 8-6s8 2 8 6"/><path d="M17 8l2 2 3-3"/>', 'title' => 'Personalized planning', 'desc' => 'We custom design travel solutions around your requirements, opportunities, challenges, and travel style.'],
        ['icon' => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>', 'title' => 'Expertise to deliver', 'desc' => 'Our team brings practical tourism experience and a professional approach to each detail of the journey.'],
        ['icon' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>', 'title' => 'Operational efficiency', 'desc' => 'We pay close attention to coordination, timing, communication, and reliable service delivery.'],
        ['icon' => '<path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z"/><path d="M9 12l2 2 4-4"/>', 'title' => 'Trusted relationships', 'desc' => 'We deliver what we promise, honor obligations, and keep ethics at the center of our work.'],
      ];
      foreach ($whyChooseUs as $w): ?>
      <div class="col-lg col-md-6 reveal">
        <div class="pillar-card-sm">
          <div class="icon-badge"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $w['icon']; ?></svg></div>
          <h5 style="margin-bottom: 10px;"><?php echo htmlspecialchars($w['title']); ?></h5>
          <p style="font-size: 0.9rem;"><?php echo htmlspecialchars($w['desc']); ?></p>
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
<section id="exclusive-offers" class="section-pad" style="background: var(--charcoal); color: #fff;">
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
        <a href="<?php echo $base; ?>blog" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">View All Stories</a>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($blogs as $b):
        $bImg = $b['image_url'] ?? '';
        $bHasPhoto = $bImg !== '' && (str_starts_with($bImg, 'assets/') || str_starts_with($bImg, 'http'));
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <a href="<?php echo $base; ?>blog/<?php echo (int) $b['id']; ?>" class="blog-card d-block" style="color: inherit;">
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

<?php
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
