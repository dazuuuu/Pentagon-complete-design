<?php
/**
 * Pentagon Quest — MICE Tourism Page
 * Meetings, Incentives, Conferences, and Exhibitions.
 */
$page_title       = 'MICE Tourism — Pentagon Quest';
$page_description = 'We design and manage corporate travel programs that motivate teams, support business goals, and keep group logistics clean.';
$current_page     = 'mice.php';
$base_path        = '';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero" style="min-height: 400px; padding: 100px 0 60px;">
  <div class="hero-video-bg"></div>
  <div class="container text-center" style="position: relative; z-index: 2;">
    <span class="section-tag" style="color: var(--gold-soft);">MICE Tourism</span>
    <h1 class="hero-title" style="font-size: clamp(2rem, 6vw, 4rem);">Meetings, incentives, conferences, and exhibitions handled with precision.</h1>
    <p class="hero-subtitle" style="max-width: 700px; margin-left: auto; margin-right: auto;">We design and manage corporate travel programs that motivate teams, support business goals, and keep group logistics clean.</p>
  </div>
</section>

<!-- MICE Tourism -->
<section class="section-pad" style="background: var(--charcoal); color: #fff;">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <span class="section-tag">MICE Tourism</span>
        <h2 class="section-title-modern" style="color: #fff;">Corporate travel that moves people and business forward.</h2>
        <p style="color: rgba(255,255,255,0.6); margin-bottom: 32px;">East Africa offers exceptional venues for meetings, incentives, conferences, and exhibitions. Pentagon Quest helps design, plan, and manage programs that motivate teams, reward performance, and keep logistics calm.</p>

        <div class="row g-3">
          <?php
          $miceItems = [
            ['icon' => '<path d="M9 11V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6"/><rect x="4" y="11" width="16" height="10" rx="2"/>', 'label' => 'Program Design'],
            ['icon' => '<rect x="1" y="7" width="15" height="12" rx="2"/><path d="M16 11h3l4 4v4h-7z"/><circle cx="6" cy="21" r="2"/><circle cx="19" cy="21" r="2"/>', 'label' => 'Group Logistics'],
            ['icon' => '<circle cx="12" cy="8" r="6"/><path d="M9 14l-3 7 6-3 6 3-3-7"/>', 'label' => 'Incentive Travel'],
            ['icon' => '<path d="M17 20h5v-2a4 4 0 0 0-3-3.87"/><path d="M9 20H4v-2a4 4 0 0 1 3-3.87"/><circle cx="9" cy="7" r="4"/><circle cx="17" cy="7" r="4"/>', 'label' => 'Partner Coordination'],
          ];
          foreach ($miceItems as $m): ?>
          <div class="col-md-6">
            <div class="mice-mini-card">
              <div class="icon-badge"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?php echo $m['icon']; ?></svg></div>
              <span><?php echo htmlspecialchars($m['label']); ?></span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="col-lg-6 reveal">
        <div style="position: relative; border-radius: var(--radius-md); overflow: hidden; min-height: 420px;">
          <svg width="100%" height="100%" viewBox="0 0 600 450" style="position: absolute; inset: 0;">
            <rect width="600" height="450" fill="var(--green)"/>
            <path d="M0,450 Q150,350 300,400 Q450,450 600,350 L600,450 L0,450 Z" fill="var(--green-light)"/>
          </svg>
          <div class="support-badge">
            <span>Complete Support</span>
            <p>From venue search to guest movement, we manage the moving parts.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
$enquiryTravelType = 'MICE / Corporate Travel';
include 'includes/enquiry-section.php';
include 'includes/footer.php';
?>
