<?php
/**
 * Pentagon Quest — Services Page (Modern Redesign)
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\OfferingService;
use App\Services\ServiceTierService;

$offeringService = new OfferingService();
$tierService = new ServiceTierService();
$offerings = $offeringService->getActive();
$tiers = $tierService->getActive();

$page_title       = 'Our Services — Pentagon Quest Tours & Safaris';
$page_description = 'From airport transfers and air ticketing to hotels, safaris, group logistics, and international tours, we coordinate the moving parts.';
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
      <?php foreach ($offerings as $s): ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); height: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid var(--green);">
          <h4 style="margin-bottom: 16px; color: var(--green);"><?php echo htmlspecialchars($s['title']); ?></h4>
          <p style="font-size: 0.95rem;"><?php echo htmlspecialchars($s['description'] ?? ''); ?></p>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($offerings)): ?>
      <div class="col-12 text-center"><p>No services listed yet.</p></div>
      <?php endif; ?>
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
      <?php foreach ($tiers as $tier): $popular = !empty($tier['is_popular']); ?>
      <div class="col-md-4 reveal" <?php echo $popular ? 'style="transform: scale(1.05);"' : ''; ?>>
        <div style="<?php echo $popular ? 'background: var(--gold); color: var(--charcoal);' : 'background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);'; ?> padding: 40px; border-radius: var(--radius-md);">
          <h4 style="<?php echo $popular ? 'font-weight: 800;' : 'color: var(--gold);'; ?>"><?php echo htmlspecialchars($tier['name']); ?></h4>
          <div style="font-size: 2rem; font-weight: 800; margin: 20px 0;">$<?php echo number_format((float) $tier['price'], 0); ?><span style="font-size: 0.8rem; opacity: 0.6;">/person</span></div>
          <ul class="list-unstyled" style="font-size: 0.9rem; <?php echo $popular ? 'font-weight: 600;' : 'opacity: 0.7;'; ?> margin-bottom: 30px;">
            <?php foreach ($tier['feature_list'] as $feature): ?>
            <li>✓ <?php echo htmlspecialchars($feature); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php if ($popular): ?>
          <span style="display: block; text-align: center; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">★ Most Popular</span>
          <?php endif; ?>
          <a href="request-quote.php?tier=<?php echo (int) $tier['id']; ?>" class="btn-hero <?php echo $popular ? '' : 'btn-hero-primary'; ?>" style="<?php echo $popular ? 'background: var(--charcoal); color: #fff;' : ''; ?> width: 100%; justify-content: center;">Get Quote</a>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($tiers)): ?>
      <div class="col-12 text-center"><p style="color: rgba(255,255,255,0.7);">Pricing tiers coming soon.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
