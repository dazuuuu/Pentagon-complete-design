<?php
/**
 * Pentagon Safaris — About Us Page (Modern Redesign)
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\TestimonialService;

$testimonialService = new TestimonialService();
$testimonials = $testimonialService->getActive();

$page_title       = 'Our Story — Pentagon Safaris';
$page_description = 'Based in Nairobi, Pentagon Safaris provides leisure, business, safari, and exclusive travel services for individuals and groups.';
$current_page     = 'about.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Our Story</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">About Pentagon Safaris</h1>
  </div>
</section>

<!-- About Pentagon Safaris -->
<section class="section-pad">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <div style="position: relative; border-radius: var(--radius-md); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); min-height: 480px;">
          <img src="assets/images/climbing-mt-kenya.jpg" alt="Pentagon Safaris" style="width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0;">
          <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(18,18,18,0.75), transparent 55%);"></div>
          <div class="promise-badge">
            <span>Our Promise</span>
            <p>We do not just sell packages. We design travel solutions.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <span class="section-tag">About Pentagon Safaris</span>
        <h2 class="section-title-modern">A Kenyan travel company built around care, flexibility, and detail.</h2>
        <p style="margin-bottom: 32px;">Based in Nairobi, Pentagon Safaris provides leisure, business, safari, and exclusive travel services for individuals and groups. Our work is rooted in professional standards, ethical service, and the belief that excellent travel is created through near-obsessive attention to detail.</p>

        <div class="row g-3 mb-4">
          <div class="col-4"><div class="stat-box"><h4>KE.</h4><span>Proudly Kenyan</span></div></div>
          <div class="col-4"><div class="stat-box"><h4>24/7</h4><span>Travel Accessibility</span></div></div>
          <div class="col-4"><div class="stat-box"><h4>E.A.</h4><span>East Africa Expertise</span></div></div>
        </div>

        <div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg></div>
            <div>
              <h5>Dream-first planning</h5>
              <p>We start by understanding the feeling, purpose, and pace of your trip before shaping the itinerary.</p>
            </div>
          </div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 20l-5.5-2.5a1 1 0 0 1-.5-.87V5a1 1 0 0 1 1.5-.87L9 6.5"/><path d="M9 20l6-3 6 3V6.5L15 3.5 9 6.5"/><path d="M9 6.5v13.5"/><path d="M15 3.5v16.5"/></svg></div>
            <div>
              <h5>Custom-fit routes</h5>
              <p>From weekend getaways to complex business travel, every journey is tailored around your needs.</p>
            </div>
          </div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 8.5a5.5 5.5 0 0 1 9-4.24A5.5 5.5 0 0 1 21 8.5c0 5-9 12-9 12s-9-7-9-12z"/></svg></div>
            <div>
              <h5>Human support</h5>
              <p>Our team stays available, practical, and close so travel feels organized from start to finish.</p>
            </div>
          </div>
        </div>
      </div>
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

<!-- Message from the Director -->
<section class="section-pad">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-4 reveal">
        <div class="director-photo-wrap">
          <img src="assets/images/pentagon-director.jpeg" alt="Director, Pentagon Safaris" style="width: 100%; height: 420px; object-fit: cover; display: block;">
          <div class="director-tag">
            <span>Director</span>
            <h5>Pentagon Safaris</h5>
          </div>
        </div>
      </div>
      <div class="col-lg-8 reveal">
        <span class="section-tag">Message from the Director</span>
        <h2 class="section-title-modern">Every journey deserves thoughtful planning, honest guidance, and a team that truly cares.</h2>
        <p style="margin-bottom: 20px;">Thank you for trusting Pentagon Safaris as your travel partner. It is a privilege to help you, your family, your team, or your clients discover places and experiences that stay with you long after the journey ends.</p>
        <p style="margin-bottom: 32px;">Whether you are planning a weekend escape, a family holiday, a safari, an international trip, or a corporate program, my promise is simple: we will listen carefully, plan professionally, and handle the details with the seriousness your travel deserves.</p>

        <div class="mb-4">
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20l9-5-9-5-9 5 9 5z"/><path d="M12 12 3 7l9-5 9 5-9 5z"/></svg></div>
            <div>
              <h5>Custom travel solutions</h5>
              <p>We listen first, then design local and international trips around your needs, timing, and budget.</p>
            </div>
          </div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div>
            <div>
              <h5>Business and group travel</h5>
              <p>Our team supports meetings, incentives, conferences, exhibitions, retreats, and group logistics.</p>
            </div>
          </div>
          <div class="feature-row">
            <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.9 6.3 6.9.8-5.1 4.8 1.4 6.8L12 17.3 5.9 20.7l1.4-6.8-5.1-4.8 6.9-.8L12 2z"/></svg></div>
            <div>
              <h5>Detail-led service</h5>
              <p>Good travel is more than a good price. We focus on reliable planning, care, and memorable experiences.</p>
            </div>
          </div>
        </div>

        <a href="mailto:info@pentagonsafaris.com" class="btn-hero" style="background: var(--charcoal); color: #fff; display: inline-flex;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M22 6 12 13 2 6"/></svg>
          info@pentagonsafaris.com
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials -->
<section class="section-pad" style="background: var(--sand);">
  <div class="container">
    <div class="text-center mb-5">
      <span class="section-tag">Feedback</span>
      <h2 class="section-title-modern">What Our Explorers Say</h2>
    </div>
    <div class="row g-4">
      <?php foreach ($testimonials as $testimonial):
        $accent = $testimonial['accent_color'] ?? 'gold';
      ?>
      <div class="col-md-6 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); border-left: 5px solid var(--<?php echo htmlspecialchars($accent); ?>); box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
          <p style="font-style: italic; font-size: 1.1rem; margin-bottom: 20px;">"<?php echo htmlspecialchars($testimonial['quote']); ?>"</p>
          <h5 style="margin-bottom: 0;"><?php echo htmlspecialchars($testimonial['author_name']); ?></h5>
          <span style="font-size: 0.8rem; opacity: 0.6;"><?php echo htmlspecialchars($testimonial['author_location'] ?? ''); ?></span>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($testimonials)): ?>
      <div class="col-12 text-center"><p>No testimonials yet.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
