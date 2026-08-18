<?php
/**
 * Pentagon Quest — Services Page
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\OfferingService;
use App\Services\ServiceTierService;

$offeringService = new OfferingService();
$tierService = new ServiceTierService();
$services = $offeringService->getActive();
$tiers = $tierService->getActive();

$page_title       = 'Our Services — Pentagon Quest Tours & Safaris';
$page_description = 'From wildlife game drives to cultural immersions, explore the full range of safari services offered by Pentagon Quest.';
$current_page     = 'services.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">What We Do</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Safari Services</h1>
  </div>
</section>

<!-- Services Grid -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Core Offerings</span>
      <h2 class="section-title-modern">Everything You Need for Africa</h2>
    </div>

    <div class="row g-4">
      <?php foreach ($services as $s): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); height: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid var(--green);">
          <h4 style="margin-bottom: 16px; color: var(--green);"><?php echo htmlspecialchars($s['title']); ?></h4>
          <p style="font-size: 0.95rem;"><?php echo htmlspecialchars($s['description'] ?? $s['desc'] ?? ''); ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Pricing Tiers -->
<section class="section-pad" style="background: var(--charcoal); color: #fff;">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Investment</span>
      <h2 class="section-title-modern" style="color: #fff;">Safari Tiers</h2>
    </div>

    <div class="row g-4">
      <?php foreach ($tiers as $tier): ?>
      <div class="col-md-4 reveal"<?php echo !empty($tier['is_popular']) ? ' style="transform: scale(1.05);"' : ''; ?>>
        <div style="<?php echo !empty($tier['is_popular'])
          ? 'background: var(--gold); padding: 40px; border-radius: var(--radius-md); color: var(--charcoal);'
          : 'background: rgba(255,255,255,0.05); padding: 40px; border-radius: var(--radius-md); border: 1px solid rgba(255,255,255,0.1);'; ?>">
          <h4 style="<?php echo !empty($tier['is_popular']) ? 'font-weight: 800;' : 'color: var(--gold);'; ?>"><?php echo htmlspecialchars($tier['name']); ?></h4>
          <div style="font-size: 2rem; font-weight: 800; margin: 20px 0;">$<?php echo number_format((float) $tier['price']); ?><span style="font-size: 0.8rem; opacity: 0.6;">/person</span></div>
          <ul class="list-unstyled" style="font-size: 0.9rem; <?php echo !empty($tier['is_popular']) ? 'font-weight: 600;' : 'opacity: 0.7;'; ?> margin-bottom: 30px;">
            <?php foreach ($tier['feature_list'] ?? [] as $feature): ?>
            <li>✓ <?php echo htmlspecialchars($feature); ?></li>
            <?php endforeach; ?>
          </ul>
          <a href="<?php echo pq_url('contact.php'); ?>" class="btn-hero <?php echo !empty($tier['is_popular']) ? '' : 'btn-hero-primary'; ?>" style="<?php echo !empty($tier['is_popular']) ? 'background: var(--charcoal); color: #fff; ' : ''; ?>width: 100%; justify-content: center;"><?php echo !empty($tier['is_popular']) ? 'Most Popular' : 'Get Quote'; ?></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
