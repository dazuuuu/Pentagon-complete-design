<?php
/**
 * Pentagon Quest — Services Page (Modern Redesign)
 */
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
      <?php
      $services = [
        ['title' => 'Wildlife Game Drives', 'desc' => 'Track the Big Five across Africa\'s finest reserves with expert naturalist guides.'],
        ['title' => 'Gorilla Trekking', 'desc' => 'Secure permits and handle all logistics for profound encounters in Uganda and Rwanda.'],
        ['title' => 'Mountain Climbing', 'desc' => 'Guided ascents of Kilimanjaro and Mount Kenya with KPAP-certified porter welfare.'],
        ['title' => 'Cultural Immersions', 'desc' => 'Authentic encounters with Maasai and other communities connecting you to living heritage.'],
        ['title' => 'Photography Safaris', 'desc' => 'Specialist expeditions led by professionals with photography-optimised vehicles.'],
        ['title' => 'Luxury Lodge Bookings', 'desc' => 'Exclusive access to Africa\'s finest eco-lodges and private conservancies.']
      ];
      foreach ($services as $s):
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); height: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid var(--green);">
          <h4 style="margin-bottom: 16px; color: var(--green);"><?php echo $s['title']; ?></h4>
          <p style="font-size: 0.95rem;"><?php echo $s['desc']; ?></p>
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
      <div class="col-md-4 reveal">
        <div style="background: rgba(255,255,255,0.05); padding: 40px; border-radius: var(--radius-md); border: 1px solid rgba(255,255,255,0.1);">
          <h4 style="color: var(--gold);">Essential</h4>
          <div style="font-size: 2rem; font-weight: 800; margin: 20px 0;">$800<span style="font-size: 0.8rem; opacity: 0.6;">/person</span></div>
          <ul class="list-unstyled" style="font-size: 0.9rem; opacity: 0.7; margin-bottom: 30px;">
            <li>✓ Shared 4WD Vehicle</li>
            <li>✓ Tented Camp Stay</li>
            <li>✓ Full Board Meals</li>
          </ul>
          <a href="contact.php" class="btn-hero btn-hero-primary" style="width: 100%; justify-content: center;">Get Quote</a>
        </div>
      </div>
      <div class="col-md-4 reveal" style="transform: scale(1.05);">
        <div style="background: var(--gold); padding: 40px; border-radius: var(--radius-md); color: var(--charcoal);">
          <h4 style="font-weight: 800;">Classic</h4>
          <div style="font-size: 2rem; font-weight: 800; margin: 20px 0;">$1,800<span style="font-size: 0.8rem; opacity: 0.6;">/person</span></div>
          <ul class="list-unstyled" style="font-size: 0.9rem; font-weight: 600; margin-bottom: 30px;">
            <li>✓ Private 4WD Vehicle</li>
            <li>✓ Mid-range Lodges</li>
            <li>✓ All Park Fees</li>
          </ul>
          <a href="contact.php" class="btn-hero" style="background: var(--charcoal); color: #fff; width: 100%; justify-content: center;">Most Popular</a>
        </div>
      </div>
      <div class="col-md-4 reveal">
        <div style="background: rgba(255,255,255,0.05); padding: 40px; border-radius: var(--radius-md); border: 1px solid rgba(255,255,255,0.1);">
          <h4 style="color: var(--gold);">Premium</h4>
          <div style="font-size: 2rem; font-weight: 800; margin: 20px 0;">$3,500<span style="font-size: 0.8rem; opacity: 0.6;">/person</span></div>
          <ul class="list-unstyled" style="font-size: 0.9rem; opacity: 0.7; margin-bottom: 30px;">
            <li>✓ Luxury Fly-in Safari</li>
            <li>✓ Exclusive Conservancies</li>
            <li>✓ All-Inclusive Drinks</li>
          </ul>
          <a href="contact.php" class="btn-hero btn-hero-primary" style="width: 100%; justify-content: center;">Get Quote</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
