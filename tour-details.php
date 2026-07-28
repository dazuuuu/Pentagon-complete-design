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

$form_success = isset($_GET['success']);
$form_error = isset($_GET['error']);

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

      <!-- Enquiry Sidebar -->
      <div class="col-lg-5 reveal">
        <div style="background: #fff; padding: 32px; border-radius: var(--radius-md); box-shadow: 0 20px 40px rgba(0,0,0,0.05); position: sticky; top: 24px;">
          <h3 style="margin-bottom: 8px; font-family: var(--font-heading);">Enquire About This Tour</h3>
          <p style="font-size: 0.9rem; margin-bottom: 24px;">Interested in <strong><?php echo htmlspecialchars($tour['title']); ?></strong>? Send us your details and our safari specialists will get back to you.</p>

          <?php if ($claimedOffer): ?>
          <div style="background: var(--gold-soft); color: var(--charcoal); padding: 14px 16px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 0.9rem;">
            <strong>Claiming offer:</strong> <?php echo htmlspecialchars($claimedOffer['title']); ?><?php echo !empty($claimedOffer['badge']) ? ' (' . htmlspecialchars($claimedOffer['badge']) . ')' : ''; ?>
          </div>
          <?php endif; ?>

          <?php if ($form_success): ?>
          <div style="background: var(--green); color: #fff; padding: 16px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 0.9rem;">
            <strong>Enquiry Sent!</strong> We'll contact you within 24 hours.
          </div>
          <?php endif; ?>
          <?php if ($form_error): ?>
          <div style="background: #b42318; color: #fff; padding: 16px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 0.9rem;">
            <strong>Unable to send enquiry.</strong> Please check your details and try again.
          </div>
          <?php endif; ?>

          <form method="POST" action="handlers/contact">
            <input type="hidden" name="redirect" value="/tour-details.php?id=<?php echo (int) $tour['id']; ?><?php echo $claimedOffer ? '&offer=' . (int) $claimedOffer['id'] : ''; ?>">
            <input type="hidden" name="interest" value="<?php echo htmlspecialchars($tour['title']); ?>">
            <?php if ($claimedOffer): ?>
            <input type="hidden" name="offer_id" value="<?php echo (int) $claimedOffer['id']; ?>">
            <?php endif; ?>
            <div class="mb-3">
              <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 6px;">Full Name</label>
              <input type="text" name="full_name" required style="width: 100%; padding: 12px 20px; border-radius: 30px; border: 1px solid #ddd; outline: none;">
            </div>
            <div class="mb-3">
              <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 6px;">Email Address</label>
              <input type="email" name="email" required style="width: 100%; padding: 12px 20px; border-radius: 30px; border: 1px solid #ddd; outline: none;">
            </div>
            <div class="mb-3">
              <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 6px;">Contact Number <span style="font-weight: 400; text-transform: none;">(optional)</span></label>
              <input type="text" name="phone" style="width: 100%; padding: 12px 20px; border-radius: 30px; border: 1px solid #ddd; outline: none;">
            </div>
            <div class="mb-3">
              <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 6px;">Message <span style="font-weight: 400; text-transform: none;">(optional)</span></label>
              <textarea name="message" style="width: 100%; padding: 16px 20px; border-radius: 20px; border: 1px solid #ddd; outline: none;" rows="4"></textarea>
            </div>
            <button type="submit" class="btn-hero btn-hero-primary" style="width: 100%; justify-content: center; border: none;">Send Enquiry</button>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
