<?php
/**
 * Pentagon Quest — Services Page (Modern Redesign)
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\ServiceTierService;

$tierService = new ServiceTierService();
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
    <span class="section-tag" style="color: var(--gold-soft);">Travel Services</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Everything you need before, during, and after the journey.</h1>
    <p class="hero-subtitle" style="max-width: 700px; margin-left: auto; margin-right: auto;">From airport transfers and air ticketing to hotels, safaris, group logistics, and international tours, we coordinate the moving parts.</p>
  </div>
</section>

<!-- Our Services -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Our Services</span>
      <h2 class="section-title-modern">Travel support for the whole journey, not just the booking.</h2>
      <p style="max-width: 640px; margin: 0 auto;">Pentagon Quest brings together transport, stays, ticketing, tours, and group logistics so every moving part feels intentional.</p>
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

<?php
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
