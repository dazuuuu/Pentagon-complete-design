<?php
/**
 * Pentagon Quest — Tours Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\TourService;
use App\Services\DestinationService;
use App\Services\GalleryService;

$tourService = new TourService();
$destinationService = new DestinationService();
$galleryService = new GalleryService();
$tours = $tourService->getActive();
$directions = array_slice($destinationService->getActive(), 0, 4);
$galleryPreview = array_slice($galleryService->getActive(), 0, 12);

$page_title       = 'Safari Tours — Kenya, Tanzania, Uganda, Rwanda & Beyond';
$page_description = 'Browse Pentagon Quest\'s safari tours across Africa. From Masai Mara migration safaris to Serengeti and gorilla trekking expeditions — find your perfect African adventure.';
$current_page     = 'tours.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Tours and Safaris</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Kenyan getaways, African safaris, and international escapes.</h1>
    <p class="hero-subtitle" style="max-width: 700px; margin-left: auto; margin-right: auto;">Explore custom local and international tours designed around your destination, timing, comfort level, and travel goals.</p>

    <!-- Search Bar in Hero -->
    <div class="search-row-wrap reveal mt-5">
      <div class="search-field">
        <label>Destination</label>
        <select><option>All Countries</option></select>
      </div>
      <div class="search-field">
        <label>Tour Type</label>
        <select><option>All Types</option></select>
      </div>
      <div class="search-field">
        <label>Budget</label>
        <select><option>Any Budget</option></select>
      </div>
      <button class="btn-search-go">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
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
        <p style="color: rgba(255,255,255,0.6); max-width: 520px;">Select an offer poster, view the key package details, then call or WhatsApp Pentagon Quest to reserve your spot.</p>
      </div>
      <div class="col-lg-5 text-lg-end">
        <a href="index.php#exclusive-offers" class="btn-hero" style="background: #fff; color: var(--charcoal); display: inline-flex;">View All Offers</a>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-7 reveal">
        <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: var(--radius-md); padding: 32px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
            <div>
              <span style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--gold); font-weight: 700;">Pentagon Quest &middot; The Safari Xplus</span>
              <h3 style="margin-top: 6px; margin-bottom: 0;">Coast Kenya</h3>
            </div>
            <a href="https://wa.me/254733238091" class="btn-hero" style="background: var(--gold); color: var(--charcoal); padding: 12px 24px;">Book Now</a>
          </div>
          <div style="overflow-x: auto;">
            <table class="pq-table">
              <thead>
                <tr><th>Hotel</th><th>Meal</th><th>Location</th><th>2 Nights</th><th>3 Nights</th></tr>
              </thead>
              <tbody>
                <?php
                $coastOffers = [
                  ['Turtle Bay Beach Club', 'All Inclusive', 'Watamu', '26,672', '37,508'],
                  ['Papillon Lagoon Reef', 'All Inclusive', 'Diani', '33,896', '68,344'],
                  ['Diani Reef Beach Resort & Spa', 'Breakfast', 'Diani', '47,570', '68,888'],
                  ['Neptune Beach Hotel', 'All Inclusive', 'Bamburi', '48,344', '70,016'],
                  ['Diani Sea Resort', 'All Inclusive', 'Diani', '50,924', '73,886'],
                  ['Bamburi Beach Hotel', 'All Inclusive', 'Bamburi', '57,890', '84,335'],
                  ['Diani Sea Lodge', 'All Inclusive', 'Diani', '35,702', '51,053'],
                  ['Southern Pal Beach Resort', 'All Inclusive', 'Diani', '65,888', '96,332'],
                  ['Leopard Beach Resort & Spa', 'Breakfast', 'Diani', '27,962', '39,443'],
                ];
                foreach ($coastOffers as $row): ?>
                <tr>
                  <td style="font-weight: 600;"><?php echo htmlspecialchars($row[0]); ?></td>
                  <td style="color: var(--gold-soft);"><?php echo htmlspecialchars($row[1]); ?></td>
                  <td><?php echo htmlspecialchars($row[2]); ?></td>
                  <td><?php echo htmlspecialchars($row[3]); ?></td>
                  <td><?php echo htmlspecialchars($row[4]); ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.85rem; color: rgba(255,255,255,0.6);">
            @pentagonquest &nbsp;|&nbsp; 0733 238091 / 0708 420952 &nbsp;|&nbsp; pentagonquest@gmail.com &nbsp;|&nbsp; www.pentagonquest.com
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
            <tr><td style="font-weight: 600;">Turtle Bay Beach Club</td><td style="color: var(--gold-soft);">Watamu</td><td>From 26,672</td></tr>
            <tr><td style="font-weight: 600;">Papillon Lagoon Reef</td><td style="color: var(--gold-soft);">Diani</td><td>From 33,896</td></tr>
            <tr><td style="font-weight: 600;">Diani Reef Beach Resort & Spa</td><td style="color: var(--gold-soft);">Diani</td><td>From 47,570</td></tr>
          </tbody>
        </table>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <a href="tel:+254733238091" class="btn-hero" style="background: var(--gold); color: var(--charcoal);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            Call to Book
          </a>
          <a href="https://wa.me/254733238091" class="btn-hero" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: #fff;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Featured Directions -->
<section class="section-pad">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-8">
        <span class="section-tag">Featured Directions</span>
        <h2 class="section-title-modern">Start local. Go global. Keep the planning sharp.</h2>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="request-quote.php" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">Request an Itinerary →</a>
      </div>
    </div>
    <div class="row g-4">
      <?php foreach ($directions as $dir):
        $dImg = $dir['image_url'] ?? '';
        $dHasPhoto = $dImg !== '' && (str_starts_with($dImg, 'assets/') || str_starts_with($dImg, 'http'));
        $dSwatch = $dHasPhoto ? 'var(--green)' : ($dImg !== '' ? $dImg : 'var(--green)');
        $dUrl = !empty($dir['id']) ? 'destination-details.php?id=' . (int) $dir['id'] : 'destinations.php';
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

<!-- Tour Gallery -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-8">
        <span class="section-tag">Tour Gallery</span>
        <h2 class="section-title-modern">A closer look at the experiences.</h2>
        <p style="max-width: 520px;">Browse a preview of the places, groups, routes, and travel moments connected to our tours and safaris.</p>
      </div>
      <div class="col-lg-4 text-lg-end">
        <a href="gallery.php" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">View Full Gallery →</a>
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

<!-- Tours Grid -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Browse Tours</span>
      <h2 class="section-title-modern">All Tour Packages</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($tours as $tour):
        $img = $tour['image_url'] ?? '';
        $hasPhoto = $img !== '' && (str_starts_with($img, 'assets/') || str_starts_with($img, 'http'));
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div style="background: #fff; border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: var(--transition);" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
          <a href="tour-details.php?id=<?php echo (int) $tour['id']; ?>" style="display: block; color: inherit;">
          <div style="height: 230px; background: var(--green); position: relative;">
             <?php if ($hasPhoto): ?>
               <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" style="width:100%; height:100%; object-fit: cover; position: absolute; inset: 0;">
             <?php else: ?>
               <svg width="100%" height="100%" viewBox="0 0 400 230" style="opacity: 0.8;">
                 <rect width="400" height="230" fill="var(--green)"/>
                 <path d="M0,230 Q100,160 200,200 Q300,240 400,180 L400,230 L0,230 Z" fill="var(--green-light)"/>
               </svg>
             <?php endif; ?>
             <span style="position: absolute; top: 20px; left: 20px; background: var(--gold); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;"><?php echo htmlspecialchars($tour['badge']); ?></span>
          </div>
          <div style="padding: 24px;">
            <div class="d-flex justify-content-between mb-2" style="font-size: 0.8rem; color: var(--gold); font-weight: 700;">
              <span><?php echo htmlspecialchars($tour['dest']); ?></span>
              <span><?php echo htmlspecialchars($tour['dur']); ?></span>
            </div>
            <h3 style="font-size: 1.25rem; margin-bottom: 12px;"><?php echo htmlspecialchars($tour['title']); ?></h3>
            <p style="font-size: 0.9rem; margin-bottom: 20px;">Experience the best of <?php echo htmlspecialchars($tour['dest']); ?> with our expert guides.</p>
            <div class="d-flex justify-content-between align-items-center">
              <span style="font-size: 1.2rem; font-weight: 800;"><?php echo htmlspecialchars($tour['price']); ?></span>
              <span style="font-weight: 600; font-size: 0.9rem; color: var(--green);">View Details →</span>
            </div>
          </div>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
$enquiryTravelType = 'Safari';
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
