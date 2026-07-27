<?php
/**
 * Pentagon Quest — About Us Page (Modern Redesign)
 */
$page_title       = 'Our Story — Pentagon Quest Tours & Safaris';
$page_description = 'Learn about Pentagon Quest — our history, values, and the people who make your African safari dreams come true.';
$current_page     = 'about.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Our Story</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">About Pentagon Quest</h1>
  </div>
</section>

<!-- Our Story Content -->
<section class="section-pad">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <div style="position: relative; border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
           <svg width="100%" height="450" viewBox="0 0 500 450">
             <rect width="500" height="450" fill="var(--green)"/>
             <circle cx="420" cy="80" r="40" fill="var(--gold)" opacity="0.4"/>
             <path d="M0,450 Q125,350 250,400 Q375,450 500,350 L500,450 L0,450 Z" fill="var(--green-light)"/>
             <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#fff" font-family="var(--font-heading)" font-size="24" font-weight="800">15 YEARS OF EXCELLENCE</text>
           </svg>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <span class="section-tag">Since 2010</span>
        <h2 class="section-title-modern">Born in Africa.<br>Built for Explorers.</h2>
        <p style="margin-bottom: 24px;">Pentagon Quest Tours & Safaris was founded in Nairobi in 2010 by a team of passionate African naturalists who shared a single vision: to share the authentic magic of Africa with the world.</p>
        <p style="margin-bottom: 32px;">Over fifteen years, we have grown into a full-service safari company, operating across 14 countries and having guided over 2,400 travellers from every corner of the globe.</p>
        
        <div class="row g-4 mb-4">
          <div class="col-6">
            <h4 style="color: var(--gold); margin-bottom: 5px;">2.4k+</h4>
            <p style="font-size: 0.8rem; text-transform: uppercase;">Happy Travellers</p>
          </div>
          <div class="col-6">
            <h4 style="color: var(--gold); margin-bottom: 5px;">14</h4>
            <p style="font-size: 0.8rem; text-transform: uppercase;">Countries Covered</p>
          </div>
        </div>
        
        <a href="contact.php" class="btn-hero btn-hero-primary" style="display: inline-flex;">Start Your Journey</a>
      </div>
    </div>
  </div>
</section>

<!-- Values / Pillars -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Our Pillars</span>
      <h2 class="section-title-modern">The Five Pillars of Excellence</h2>
    </div>
    
    <div class="row g-4">
      <?php
      $pillars = [
        ['title' => 'Safety', 'desc' => 'Certified wilderness first-aiders and 24/7 emergency protocols on every expedition.'],
        ['title' => 'Authenticity', 'desc' => 'Genuine, respectful encounters deeply connected to Africa\'s living culture.'],
        ['title' => 'Expertise', 'desc' => 'Guides with average 12 years of field experience bringing the wild to life.'],
        ['title' => 'Sustainability', 'desc' => 'Strict low-impact principles supporting anti-poaching and community development.'],
        ['title' => 'Personalisation', 'desc' => 'Every itinerary is built from scratch around your specific interests and dreams.']
      ];
      foreach ($pillars as $p):
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); height: 100%; border-bottom: 4px solid var(--gold);">
          <h4 style="margin-bottom: 16px; color: var(--green);"><?php echo $p['title']; ?></h4>
          <p style="font-size: 0.95rem;"><?php echo $p['desc']; ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
