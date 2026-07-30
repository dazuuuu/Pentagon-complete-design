<?php
/**
 * Pentagon Quest — Destination Details Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\DestinationService;
use App\Services\OfferService;

$destinationService = new DestinationService();
$destinationId = (int) ($_GET['id'] ?? 0);
$destination = $destinationId ? $destinationService->find($destinationId) : null;

if (!$destination || $destination['status'] !== 'active') {
    header('Location: index.php');
    exit;
}

$offerService = new OfferService();
$claimedOffer = null;
$requestedOfferId = (int) ($_GET['offer'] ?? 0);
if ($requestedOfferId) {
    $candidate = $offerService->find($requestedOfferId);
    if ($candidate && $candidate['target_type'] === 'destination' && (int) $candidate['destination_id'] === $destinationId) {
        $claimedOffer = $candidate;
    }
}

$images = $destinationService->getImages($destinationId);
$cover = $destination['image_url'] ?? '';
$hasCoverPhoto = $cover !== '' && (str_starts_with($cover, 'assets/') || str_starts_with($cover, 'http'));

$page_title       = $destination['name'] . ' — Pentagon Quest';
$page_description = !empty($destination['description']) ? mb_strimwidth(strip_tags($destination['description']), 0, 160, '...') : 'Discover ' . $destination['name'] . ' with Pentagon Quest.';
$current_page     = 'destinations.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 320px; padding: 100px 0 40px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);"><?php echo htmlspecialchars($destination['country']); ?></span>
    <h1 class="hero-title" style="font-size: clamp(1.8rem, 5vw, 3.2rem);"><?php echo htmlspecialchars($destination['name']); ?></h1>
  </div>
</section>

<section class="section-pad" style="padding-top: 60px;">
  <div class="container">
    <div class="row g-5">

      <!-- Details -->
      <div class="col-lg-7 reveal">

        <div style="border-radius: var(--radius-md); overflow: hidden; height: 360px; position: relative; background: var(--green); margin-bottom: 24px;">
          <?php if ($hasCoverPhoto): ?>
            <img src="<?php echo htmlspecialchars($cover); ?>" alt="<?php echo htmlspecialchars($destination['name']); ?>" style="width:100%; height:100%; object-fit: cover;">
          <?php else: ?>
            <svg width="100%" height="100%" viewBox="0 0 800 360" style="opacity: 0.8;">
              <rect width="800" height="360" fill="<?php echo htmlspecialchars($cover ?: 'var(--green)'); ?>"/>
              <path d="M0,360 Q200,260 400,300 Q600,340 800,260 L800,360 L0,360 Z" fill="rgba(255,255,255,0.15)"/>
            </svg>
          <?php endif; ?>
        </div>

        <?php if (!empty($images)): ?>
        <div class="d-flex flex-wrap gap-2 mb-4">
          <?php foreach ($images as $image): ?>
          <div style="width: 110px; height: 85px; border-radius: 8px; overflow: hidden;">
            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="" style="width:100%; height:100%; object-fit: cover;">
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h2 style="font-size: 1.5rem; margin-bottom: 16px;">About <?php echo htmlspecialchars($destination['name']); ?></h2>
        <?php if (!empty($destination['description'])): ?>
          <p style="white-space: pre-line;"><?php echo nl2br(htmlspecialchars($destination['description'])); ?></p>
        <?php else: ?>
          <p>Discover the beauty of <?php echo htmlspecialchars($destination['name']); ?>, <?php echo htmlspecialchars($destination['country']); ?> — one of Pentagon Quest's signature safari destinations.</p>
        <?php endif; ?>

        <a href="tours.php" style="display: inline-block; margin-top: 16px; font-weight: 600; font-size: 0.9rem; color: var(--green);">← View tours in this region</a>
      </div>

      <!-- Enquiry CTA -->
      <div class="col-lg-5 reveal">
        <div style="background: #fff; padding: 32px; border-radius: var(--radius-md); box-shadow: 0 20px 40px rgba(0,0,0,0.05); position: sticky; top: 24px;">
          <h3 style="margin-bottom: 8px; font-family: var(--font-heading);">Enquire About This Destination</h3>
          <p style="font-size: 0.9rem; margin-bottom: 24px;">Interested in <strong><?php echo htmlspecialchars($destination['name']); ?></strong>? Send us your details below and our safari specialists will get back to you.</p>

          <?php if ($claimedOffer): ?>
          <div style="background: var(--gold-soft); color: var(--charcoal); padding: 14px 16px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 0.9rem;">
            <strong>Claiming offer:</strong> <?php echo htmlspecialchars($claimedOffer['title']); ?><?php echo !empty($claimedOffer['badge']) ? ' (' . htmlspecialchars($claimedOffer['badge']) . ')' : ''; ?>
          </div>
          <?php endif; ?>

          <a href="#trip-request" class="btn-hero btn-hero-primary" style="width: 100%; justify-content: center;">Send a Trip Request ↓</a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php
$enquiryInterest = $destination['name'];
$enquiryOfferId = $claimedOffer['id'] ?? null;
$enquiryOfferLabel = $claimedOffer['title'] ?? '';
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
