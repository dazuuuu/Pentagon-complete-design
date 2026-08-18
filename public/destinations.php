<?php
/**
 * Pentagon Quest — Destinations Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\DestinationService;
use App\Services\TourService;

$destinationService = new DestinationService();
$tourService = new TourService();
$destinations = $destinationService->getActive();
$tours = $tourService->getActive();
$selectedDestination = trim($_GET['destination'] ?? '');
$selectedType = trim($_GET['type'] ?? '');
$selectedDuration = trim($_GET['duration'] ?? '');

$countries = array_values(array_unique(array_filter(array_map(static fn (array $d): string => (string) ($d['country'] ?? ''), $destinations))));
$tourTypes = array_values(array_unique(array_filter(array_map(static fn (array $t): string => (string) ($t['type'] ?? ''), $tours))));
$tourDurations = array_values(array_unique(array_filter(array_map(static fn (array $t): string => (string) ($t['dur'] ?? ''), $tours))));
sort($countries);
sort($tourTypes);
sort($tourDurations);

$filteredTours = array_values(array_filter($tours, static function (array $tour) use ($selectedDestination, $selectedType, $selectedDuration): bool {
    if ($selectedDestination !== '' && strcasecmp((string) ($tour['dest'] ?? ''), $selectedDestination) !== 0) {
        return false;
    }
    if ($selectedType !== '' && strcasecmp((string) ($tour['type'] ?? ''), $selectedType) !== 0) {
        return false;
    }
    if ($selectedDuration !== '' && strcasecmp((string) ($tour['dur'] ?? ''), $selectedDuration) !== 0) {
        return false;
    }
    return true;
}));

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
    <form class="search-row-wrap reveal mt-5" method="get">
      <div class="search-field">
        <label>Destination</label>
        <select name="destination">
          <option value="">All Countries</option>
          <?php foreach ($countries as $country): ?>
          <option value="<?php echo htmlspecialchars($country); ?>" <?php echo $selectedDestination === $country ? 'selected' : ''; ?>><?php echo htmlspecialchars($country); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="search-field">
        <label>Tour Type</label>
        <select name="type">
          <option value="">All Types</option>
          <?php foreach ($tourTypes as $type): ?>
          <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $selectedType === $type ? 'selected' : ''; ?>><?php echo htmlspecialchars($type); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="search-field">
        <label>Duration</label>
        <select name="duration">
          <option value="">Any Duration</option>
          <?php foreach ($tourDurations as $duration): ?>
          <option value="<?php echo htmlspecialchars($duration); ?>" <?php echo $selectedDuration === $duration ? 'selected' : ''; ?>><?php echo htmlspecialchars($duration); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn-search-go" type="submit">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
    </form>
  </div>
</section>

<!-- Destinations Grid -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Where to go</span>
      <h2 class="section-title-modern">Published Destinations</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($destinations as $destination): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="blog-card">
          <div style="height: 280px; <?php echo pq_cover_style($destination['image_url'] ?? '', 'var(--green)'); ?>; position: relative;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.05), rgba(0,0,0,0.72));"></div>
            <div style="position: absolute; bottom: 24px; left: 24px; right: 24px; color: #fff;">
              <span style="font-size: 0.75rem; text-transform: uppercase; font-weight: 800; color: var(--gold); letter-spacing: .12em;"><?php echo htmlspecialchars($destination['country']); ?></span>
              <h3 style="margin: 8px 0 10px; color:#fff;"><?php echo htmlspecialchars($destination['name']); ?></h3>
              <p style="color: rgba(255,255,255,0.76); font-size: .92rem; margin: 0;"><?php echo htmlspecialchars(mb_strimwidth($destination['description'] ?? '', 0, 120, '...')); ?></p>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if ($destinations === []): ?>
      <div class="col-12">
        <div class="blog-card p-5 text-center">
          <h3 class="mb-2">No destinations published yet</h3>
          <p class="mb-0">Add active destinations from the admin Destinations menu.</p>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Tours Grid -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Tours</span>
      <h2 class="section-title-modern">Matching Tours</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($filteredTours as $tour): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div style="background: #fff; border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); transition: var(--transition);" onmouseover="this.style.transform='translateY(-10px)'" onmouseout="this.style.transform='translateY(0)'">
          <div style="height: 230px; <?php echo pq_cover_style($tour['image_url'] ?? '', 'var(--green)'); ?>; position: relative;">
             <svg width="100%" height="100%" viewBox="0 0 400 230" style="opacity: 0.8;">
               <rect width="400" height="230" fill="transparent"/>
               <path d="M0,230 Q100,160 200,200 Q300,240 400,180 L400,230 L0,230 Z" fill="var(--green-light)"/>
             </svg>
             <span style="position: absolute; top: 20px; left: 20px; background: var(--gold); color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;"><?php echo htmlspecialchars($tour['badge']); ?></span>
          </div>
          <div style="padding: 24px;">
            <div class="d-flex justify-content-between mb-2" style="font-size: 0.8rem; color: var(--gold); font-weight: 700;">
              <span><?php echo htmlspecialchars($tour['dest']); ?></span>
              <span><?php echo htmlspecialchars($tour['dur']); ?></span>
            </div>
            <h3 style="font-size: 1.25rem; margin-bottom: 12px;"><?php echo htmlspecialchars($tour['title']); ?></h3>
            <p style="font-size: 0.9rem; margin-bottom: 20px;"><?php echo htmlspecialchars($tour['description'] !== '' ? $tour['description'] : 'Experience the best of ' . $tour['dest'] . ' with our expert guides.'); ?></p>
            <div class="d-flex justify-content-between align-items-center">
              <span style="font-size: 1.2rem; font-weight: 800;"><?php echo htmlspecialchars($tour['price']); ?></span>
              <a href="<?php echo pq_url('contact.php'); ?>" style="font-weight: 600; font-size: 0.9rem; color: var(--green);">Book Now →</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if ($filteredTours === []): ?>
      <div class="col-12">
        <div class="blog-card p-5 text-center">
          <h3 class="mb-2">No tours match this selection</h3>
          <p class="mb-0">Adjust the filters or add active tours from the admin Tours menu.</p>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
