<?php
/**
 * Pentagon Quest — Tour Details Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\TourService;
use App\Services\OfferService;

$tourService = new TourService();
$tourId = (int) ($_GET['id'] ?? 0);
$tour = $tourId ? $tourService->find($tourId) : null;

if (!$tour || $tour['status'] !== 'active') {
    header('Location: tours.php');
    exit;
}

$offerService = new OfferService();
$claimedOffer = null;
$requestedOfferId = (int) ($_GET['offer'] ?? 0);
if ($requestedOfferId) {
    $candidate = $offerService->find($requestedOfferId);
    if ($candidate && $candidate['target_type'] === 'tour' && (int) $candidate['tour_id'] === $tourId) {
        $claimedOffer = $candidate;
    }
}

$images = $tourService->getImages($tourId);
$cover = $tour['image_url'] ?? '';
$hasCoverPhoto = $cover !== '' && (str_starts_with($cover, 'assets/') || str_starts_with($cover, 'http'));

$page_title       = $tour['title'] . ' — Pentagon Quest';
$page_description = !empty($tour['description']) ? mb_strimwidth(strip_tags($tour['description']), 0, 160, '...') : 'Discover ' . $tour['title'] . ' with Pentagon Quest.';
$current_page     = 'tours.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 320px; padding: 100px 0 40px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);"><?php echo htmlspecialchars($tour['country']); ?> · <?php echo htmlspecialchars($tour['tour_type']); ?></span>
    <h1 class="hero-title" style="font-size: clamp(1.8rem, 5vw, 3.2rem);"><?php echo htmlspecialchars($tour['title']); ?></h1>
  </div>
</section>

<section class="section-pad" style="padding-top: 60px;">
  <div class="container">
    <div class="row g-5">

      <!-- Details -->
      <div class="col-lg-7 reveal">

        <div style="border-radius: var(--radius-md); overflow: hidden; height: 360px; position: relative; background: var(--green); margin-bottom: 24px;">
          <?php if ($hasCoverPhoto): ?>
            <img src="<?php echo htmlspecialchars($cover); ?>" alt="<?php echo htmlspecialchars($tour['title']); ?>" style="width:100%; height:100%; object-fit: cover;">
          <?php else: ?>
            <svg width="100%" height="100%" viewBox="0 0 800 360" style="opacity: 0.8;">
              <rect width="800" height="360" fill="var(--green)"/>
              <path d="M0,360 Q200,260 400,300 Q600,340 800,260 L800,360 L0,360 Z" fill="var(--green-light)"/>
            </svg>
          <?php endif; ?>
          <?php if (!empty($tour['badge'])): ?>
          <span style="position: absolute; top: 20px; left: 20px; background: var(--gold); color: #fff; padding: 6px 16px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;"><?php echo htmlspecialchars($tour['badge']); ?></span>
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

        <div class="d-flex flex-wrap gap-4 mb-4" style="font-size: 0.9rem;">
          <div><strong style="color: var(--gold);">Duration:</strong> <?php echo htmlspecialchars($tour['duration']); ?></div>
          <div><strong style="color: var(--gold);">Type:</strong> <?php echo htmlspecialchars($tour['tour_type']); ?></div>
          <div><strong style="color: var(--gold);">Price:</strong> $<?php echo number_format((float) $tour['price'], 0); ?></div>
        </div>

        <h2 style="font-size: 1.5rem; margin-bottom: 16px;">About This Tour</h2>
        <?php if (!empty($tour['description'])): ?>
          <p style="white-space: pre-line;"><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>
        <?php else: ?>
          <p>Experience the best of <?php echo htmlspecialchars($tour['country']); ?> with our expert guides on this <?php echo htmlspecialchars($tour['duration']); ?> <?php echo htmlspecialchars($tour['tour_type']); ?>.</p>
        <?php endif; ?>
      </div>

      <!-- Enquiry CTA -->
      <div class="col-lg-5 reveal">
        <div style="background: #fff; padding: 32px; border-radius: var(--radius-md); box-shadow: 0 20px 40px rgba(0,0,0,0.05); position: sticky; top: 24px;">
          <h3 style="margin-bottom: 8px; font-family: var(--font-heading);">Enquire About This Tour</h3>
          <p style="font-size: 0.9rem; margin-bottom: 24px;">Interested in <strong><?php echo htmlspecialchars($tour['title']); ?></strong>? Send us your details below and our safari specialists will get back to you.</p>

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
$enquiryInterest = $tour['title'];
$enquiryOfferId = $claimedOffer['id'] ?? null;
$enquiryOfferLabel = $claimedOffer['title'] ?? '';
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
