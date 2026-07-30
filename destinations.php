<?php
/**
 * Pentagon Quest — Destinations Page (Modern Redesign)
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\DestinationService;

$destinationService = new DestinationService();
$destinations = $destinationService->getActive();

$page_title       = 'Safari Destinations — Kenya, Tanzania, Uganda, Rwanda & Beyond';
$page_description = 'Explore Pentagon Quest\'s safari destinations across Africa. From Kenya\'s Masai Mara and Tanzania\'s Serengeti to Uganda\'s gorilla forests and Rwanda\'s volcanic highlands — discover your perfect African adventure.';
$current_page     = 'destinations.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Explore the Continent</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Safari Destinations</h1>

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

<!-- Destinations Grid -->
<section class="section-pad">
  <div class="container">
    <div class="row g-4">
      <?php foreach ($destinations as $d):
        $img = $d['image_url'] ?? '';
        $hasPhoto = $img !== '' && (str_starts_with($img, 'assets/') || str_starts_with($img, 'http'));
        $swatch = $hasPhoto ? 'var(--green)' : ($img !== '' ? $img : 'var(--green)');
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <a href="destination-details.php?id=<?php echo (int) $d['id']; ?>" class="blog-card d-block" style="color: inherit;">
          <div style="height: 260px; <?php echo $hasPhoto ? '' : 'background: ' . htmlspecialchars($swatch) . ';'; ?> position: relative;">
            <?php if ($hasPhoto): ?>
              <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($d['name']); ?>" style="width:100%; height:100%; object-fit: cover; position: absolute; inset: 0;">
            <?php else: ?>
              <svg width="100%" height="100%" viewBox="0 0 400 260" opacity="0.6">
                <rect width="400" height="260" fill="<?php echo htmlspecialchars($swatch); ?>"/>
                <path d="M0,260 Q100,180 200,220 Q300,260 400,180 L400,260 L0,260 Z" fill="rgba(255,255,255,0.1)"/>
              </svg>
            <?php endif; ?>
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.55), transparent 55%);"></div>
            <?php if ($d['is_featured']): ?>
            <span style="position: absolute; top: 20px; left: 20px; background: var(--gold); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;">Featured</span>
            <?php endif; ?>
            <div style="position: absolute; bottom: 20px; left: 20px; color: #fff;">
              <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--gold);"><?php echo htmlspecialchars($d['country']); ?></span>
              <h3 style="margin: 5px 0 0;"><?php echo htmlspecialchars($d['name']); ?></h3>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
      <?php if (empty($destinations)): ?>
      <div class="col-12 text-center reveal">
        <p>No destinations available right now — check back soon.</p>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
