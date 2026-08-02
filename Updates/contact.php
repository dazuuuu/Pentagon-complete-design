<?php
/**
 * Pentagon Safaris — Contact Page (Modern Redesign)
 */
require_once __DIR__ . '/includes/bootstrap.php';

$page_title       = 'Contact Us — Pentagon Safaris';
$page_description = 'Reach Pentagon Safaris for local tours, international trips, MICE programs, transfers, air ticketing, and custom travel planning.';
$current_page     = 'contact.php';
$base_path        = '';

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

<?php
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
