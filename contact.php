<?php
/**
 * Pentagon Quest — Contact Page (Modern Redesign)
 */
$page_title       = 'Contact Us — Pentagon Quest';
$page_description = 'Reach Pentagon Quest for local tours, international trips, MICE programs, transfers, air ticketing, and custom travel planning.';
$current_page     = 'contact.php';
$base_path        = '';

$form_success = isset($_GET['success']);
$form_error = isset($_GET['error']);

include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">Get In Touch</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Contact Us</h1>
  </div>
</section>

<!-- Contact Section -->
<section class="section-pad">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-7 reveal">
        <div style="background: #fff; padding: 40px; border-radius: var(--radius-md); box-shadow: 0 20px 40px rgba(0,0,0,0.05);">
          <h2 style="margin-bottom: 24px; font-family: var(--font-heading);">Start Planning</h2>
          
          <?php if ($form_success): ?>
          <div style="background: var(--green); color: #fff; padding: 20px; border-radius: var(--radius-md); margin-bottom: 24px;">
            <strong>Enquiry Sent!</strong> Our safari specialists will contact you within 24 hours.
          </div>
          <?php endif; ?>
          <?php if ($form_error): ?>
          <div style="background: #b42318; color: #fff; padding: 20px; border-radius: var(--radius-md); margin-bottom: 24px;">
            <strong>Unable to send enquiry.</strong> Please check your details and try again.
          </div>
          <?php endif; ?>

          <form method="POST" action="handlers/contact">
            <input type="hidden" name="redirect" value="/contact.php">
            <div class="row g-3">
              <div class="col-md-6">
                <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">Full Name</label>
                <input type="text" name="full_name" required style="width: 100%; padding: 12px 20px; border-radius: 30px; border: 1px solid #ddd; outline: none;">
              </div>
              <div class="col-md-6">
                <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">Email Address</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px 20px; border-radius: 30px; border: 1px solid #ddd; outline: none;">
              </div>
              <div class="col-12">
                <label style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; display: block; margin-bottom: 8px;">Your Safari Dream</label>
                <textarea name="message" required style="width: 100%; padding: 20px; border-radius: 20px; border: 1px solid #ddd; outline: none;" rows="5"></textarea>
              </div>
              <div class="col-12">
                <button type="submit" class="btn-hero btn-hero-primary" style="width: 100%; justify-content: center; border: none;">Send Enquiry</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      
      <div class="col-lg-5 reveal">
        <div style="background: var(--green); color: #fff; padding: 40px; border-radius: var(--radius-md); height: 100%;">
          <h2 style="margin-bottom: 32px; font-family: var(--font-heading); color: var(--gold);">Reach Us Directly</h2>
          
          <div style="margin-bottom: 32px;">
            <h5 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em; color: var(--gold-soft);">Phone & WhatsApp</h5>
            <p style="font-size: 1.2rem; font-weight: 700;">+254 726 528015<br>+254 718 620982</p>
          </div>

          <div style="margin-bottom: 32px;">
            <h5 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em; color: var(--gold-soft);">Email</h5>
            <p style="font-size: 1.2rem; font-weight: 700;">info@pentagonquest.com</p>
          </div>

          <div>
            <h5 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.1em; color: var(--gold-soft);">Office Address</h5>
            <p style="font-size: 1.1rem;">P.O. Box 23575-00100, Nairobi, Kenya<br>Offices in Nairobi, Kericho &amp; Eldoret</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
