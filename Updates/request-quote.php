<?php
/**
 * Pentagon Safaris — Service Quote Request Page
 * A dedicated form (separate from the general contact form) for requesting
 * a quote on a specific Safari Tier. Lands in the admin Enquiries CRM, where
 * an admin can email the customer a custom quote.
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Helpers\Path;
use App\Services\ServiceTierService;

$tierService = new ServiceTierService();
$tierId = (int) ($_GET['tier'] ?? 0);
$tier = $tierId ? $tierService->find($tierId) : null;

if (!$tier || $tier['status'] !== 'active') {
    header('Location: ' . Path::baseUrl() . 'services');
    exit;
}

$form_success = isset($_GET['success']);
$form_error = isset($_GET['error']);

$page_title       = 'Request a Quote — ' . $tier['name'] . ' — Pentagon Safaris';
$page_description = 'Request a personalised quote for the ' . $tier['name'] . ' safari tier from Pentagon Safaris.';
$current_page     = 'services.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 320px; padding: 100px 0 40px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Safari Tiers</span>
    <h1 class="hero-title" style="font-size: clamp(1.8rem, 5vw, 3.2rem);">Request a Quote</h1>
  </div>
</section>

<section class="section-pad" style="padding-top: 60px;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); box-shadow: 0 20px 40px rgba(0,0,0,0.05);">
          <span class="section-tag">You're Requesting</span>
          <h2 style="margin-bottom: 8px;"><?php echo htmlspecialchars($tier['name']); ?></h2>
          <p style="font-size: 1.3rem; font-weight: 800; color: var(--green); margin-bottom: 24px;">$<?php echo number_format((float) $tier['price'], 0); ?><span style="font-size: 0.8rem; font-weight: 400; opacity: 0.6;">/person (starting)</span></p>

          <?php if ($form_success): ?>
          <div style="background: var(--green); color: #fff; padding: 16px; border-radius: var(--radius-md); margin-bottom: 24px; font-size: 0.9rem;">
            <strong>Request Sent!</strong> Our team will email you a personalised quote shortly.
          </div>
          <?php endif; ?>
          <?php if ($form_error): ?>
          <div style="background: #b42318; color: #fff; padding: 16px; border-radius: var(--radius-md); margin-bottom: 24px; font-size: 0.9rem;">
            <strong>Unable to send your request.</strong> Please check your details and try again.
          </div>
          <?php endif; ?>

          <form method="POST" action="<?php echo $base; ?>handlers/contact">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($base . 'request-quote?tier=' . (int) $tier['id']); ?>">
            <input type="hidden" name="interest" value="<?php echo htmlspecialchars($tier['name']); ?> (Safari Tier Quote)">
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
              <textarea name="message" style="width: 100%; padding: 16px 20px; border-radius: 20px; border: 1px solid #ddd; outline: none;" rows="4" placeholder="Group size, travel dates, anything else we should know..."></textarea>
            </div>
            <button type="submit" class="btn-hero btn-hero-primary" style="width: 100%; justify-content: center; border: none;">Request Quote</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
