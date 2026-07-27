<?php
/**
 * Pentagon Quest — Home Page (Refined & Restructured)
 */
$page_title       = 'Pentagon Quest — Authentic African Safari Expeditions';
$page_description = 'Discover the heart of Africa with Pentagon Quest. Bespoke 4x4 wildlife expeditions, cultural immersions, and luxury safari experiences.';
$current_page     = 'index.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Refined Hero Section -->
<section class="modern-hero">
  <div class="hero-video-bg"></div>
  
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <div class="reveal">
          <h1 class="hero-title" style="font-family: var(--font-display);">Bespoke African Safari Expeditions</h1>
          <p class="hero-subtitle">Crafting authentic journeys across the wild heart of the continent.</p>
          
          <div class="hero-btns">
            <a href="destinations.php" class="btn-hero btn-hero-primary">
              Explore Destinations 
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="contact.php" class="btn-hero" style="background: #fff; color: #121212;">
              Plan Your Journey
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
          </div>
        </div>

        <!-- Search Bar -->
        <div class="search-row-wrap reveal" style="transition-delay: 0.2s;">
          <div class="search-field">
            <label>Destination</label>
            <select><option>All Destinations</option><option>Kenya</option><option>Tanzania</option></select>
          </div>
          <div class="search-field">
            <label>Experience</label>
            <select><option>Wildlife Safari</option><option>Cultural Tour</option></select>
          </div>
          <div class="search-field">
            <label>Duration</label>
            <select><option>7-14 Days</option></select>
          </div>
          <button class="btn-search-go">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          </button>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- 1. Popular Destinations -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Where to go</span>
      <h2 class="section-title-modern">Popular Destinations</h2>
    </div>
    <div class="row g-4">
      <?php
      $destinations = [
        ['name' => 'Masai Mara', 'country' => 'Kenya', 'img' => 'var(--green)'],
        ['name' => 'Serengeti', 'country' => 'Tanzania', 'img' => 'var(--charcoal)'],
        ['name' => 'Bwindi Forest', 'country' => 'Uganda', 'img' => 'var(--green-light)']
      ];
      foreach ($destinations as $d):
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="blog-card">
          <div style="height: 300px; background: <?php echo $d['img']; ?>; position: relative;">
            <svg width="100%" height="100%" viewBox="0 0 400 300" opacity="0.6">
              <rect width="400" height="300" fill="<?php echo $d['img']; ?>"/>
              <path d="M0,300 Q100,200 200,250 Q300,300 400,200 L400,300 L0,300 Z" fill="rgba(255,255,255,0.1)"/>
            </svg>
            <div style="position: absolute; bottom: 20px; left: 20px; color: #fff;">
              <span style="font-size: 0.7rem; text-transform: uppercase; font-weight: 700; color: var(--gold);"><?php echo $d['country']; ?></span>
              <h3 style="margin: 5px 0 0;"><?php echo $d['name']; ?></h3>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 2. Previous Experiences -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="row align-items-center mb-5">
      <div class="col-lg-6">
        <span class="section-tag">Our Legacy</span>
        <h2 class="section-title-modern">Previous Experiences</h2>
      </div>
      <div class="col-lg-6 text-lg-end">
        <p>A glimpse into the authentic African journeys we've crafted for explorers over the years.</p>
      </div>
    </div>
    <div class="row g-4">
      <div class="col-lg-8 reveal">
        <div class="experience-item" style="background: var(--green);">
          <svg width="100%" height="100%" viewBox="0 0 800 450" opacity="0.4">
            <rect width="800" height="450" fill="var(--green)"/>
            <text x="50%" y="50%" text-anchor="middle" fill="#fff" font-family="var(--font-heading)" font-size="24">THE GREAT MIGRATION EXPEDITION</text>
          </svg>
        </div>
      </div>
      <div class="col-lg-4 reveal">
        <div class="experience-item" style="background: var(--gold);">
          <svg width="100%" height="100%" viewBox="0 0 400 450" opacity="0.4">
            <rect width="400" height="450" fill="var(--gold)"/>
            <text x="50%" y="50%" text-anchor="middle" fill="#fff" font-family="var(--font-heading)" font-size="20">CULTURAL IMMERSION</text>
          </svg>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 3. Testimonials -->
<section class="section-pad">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Feedback</span>
      <h2 class="section-title-modern">What Our Explorers Say</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); border-left: 5px solid var(--gold); box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
          <p style="font-style: italic; font-size: 1.1rem; margin-bottom: 20px;">"The most authentic safari experience I've ever had. Pentagon Quest's attention to detail and knowledge of the land is unparalleled."</p>
          <h5 style="margin-bottom: 0;">Sarah Jenkins</h5>
          <span style="font-size: 0.8rem; opacity: 0.6;">United Kingdom</span>
        </div>
      </div>
      <div class="col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); border-left: 5px solid var(--green); box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
          <p style="font-style: italic; font-size: 1.1rem; margin-bottom: 20px;">"From the moment we landed in Nairobi, everything was seamless. The 4x4 expedition was rugged yet incredibly comfortable."</p>
          <h5 style="margin-bottom: 0;">Mark Thompson</h5>
          <span style="font-size: 0.8rem; opacity: 0.6;">USA</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 4. Exclusive Offers -->
<section class="section-pad" style="background: var(--charcoal); color: #fff;">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-6 reveal">
        <div class="offer-card">
          <span class="section-tag" style="color: var(--gold-soft);">Limited Time</span>
          <h3 style="color: #fff; margin-bottom: 15px;">Early Bird Safari 2026</h3>
          <p style="color: rgba(255,255,255,0.7); margin-bottom: 25px;">Book your 2026 safari by December and enjoy 15% off all inclusive packages.</p>
          <a href="contact.php" class="btn-hero" style="background: #fff; color: var(--charcoal);">Claim Offer</a>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <div class="offer-card" style="background: var(--gold); color: var(--charcoal);">
          <span class="section-tag" style="color: var(--green);">New Launch</span>
          <h3 style="margin-bottom: 15px;">Self-Drive Expedition</h3>
          <p style="opacity: 0.8; margin-bottom: 25px;">Experience the freedom of Africa with our new fully-equipped 4x4 self-drive rentals.</p>
          <a href="services.php" class="btn-hero" style="background: var(--charcoal); color: #fff;">Learn More</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 5. Latest from the Blog -->
<section class="section-pad">
  <div class="container">
    <div class="row align-items-end mb-5">
      <div class="col-lg-6">
        <span class="section-tag">Stories</span>
        <h2 class="section-title-modern">Latest from the Blog</h2>
      </div>
      <div class="col-lg-6 text-lg-end">
        <a href="blog.php" style="color: var(--gold); font-weight: 700; border-bottom: 2px solid var(--gold); padding-bottom: 5px;">View All Stories</a>
      </div>
    </div>
    <div class="row g-4">
      <?php
      $blogs = [
        ['title' => 'Top 10 Safari Photography Tips', 'date' => 'July 15, 2026'],
        ['title' => 'What to Pack for Your First Safari', 'date' => 'July 10, 2026'],
        ['title' => 'Understanding the Great Migration', 'date' => 'July 05, 2026']
      ];
      foreach ($blogs as $b):
      ?>
      <div class="col-lg-4 col-md-6 reveal">
        <div class="blog-card">
          <div style="height: 200px; background: var(--sand);">
            <svg width="100%" height="100%" viewBox="0 0 400 200" opacity="0.2">
              <rect width="400" height="200" fill="var(--green)"/>
            </svg>
          </div>
          <div style="padding: 24px;">
            <span style="font-size: 0.75rem; opacity: 0.5;"><?php echo $b['date']; ?></span>
            <h4 style="margin: 10px 0 20px; font-size: 1.1rem;"><?php echo $b['title']; ?></h4>
            <a href="blog.php" style="font-weight: 700; color: var(--gold); font-size: 0.9rem;">Read Story →</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
