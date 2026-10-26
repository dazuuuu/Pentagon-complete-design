<?php
/**
 * Pentagon Safaris — Shared Header Component
 */
require_once __DIR__ . '/bootstrap.php';

use App\Helpers\Path;
use App\Services\HomeMediaService;
use App\Services\DestinationService;
use App\Services\OfferService;

$base = !empty($base_path) ? rtrim($base_path, '/') . '/' : Path::baseUrl();
$cssPath = Path::publicPath('assets', 'css', 'style.css');
$cssVersion = is_file($cssPath) ? filemtime($cssPath) : time();
$homeMediaService = new HomeMediaService();
$activeBanner = (new OfferService())->getActiveBannerOffer();
$siteLogo = $homeMediaService->logoPath();
$showPosterNav = $homeMediaService->showPosters() && $homeMediaService->activePosters() !== [];
$navDestinations = (new DestinationService())->getActive();

// Absolute site URL, needed because social crawlers (Facebook/WhatsApp/X/LinkedIn)
// ignore relative og:url / og:image values.
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443 ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$siteOrigin = $scheme . '://' . $host;
$absoluteBase = $siteOrigin . $base;

$ogTitle = trim((string) ($page_title ?? 'Pentagon Safaris'));
$ogDescription = trim((string) ($page_description ?? 'Pentagon Safaris — authentic African safari expeditions, tours, and travel experiences.'));

// Any page can set $page_image (relative path or absolute URL) before including
// this header — e.g. a blog post's featured image or a tour's cover photo.
// Falls back to the site logo so every shared link still carries a thumbnail.
$rawPageImage = trim((string) ($page_image ?? ''));
if ($rawPageImage === '') {
    $rawPageImage = $siteLogo;
}
$ogImage = str_starts_with($rawPageImage, 'http://') || str_starts_with($rawPageImage, 'https://')
    ? $rawPageImage
    : $absoluteBase . ltrim($rawPageImage, '/');

$ogUrl = $siteOrigin . ($_SERVER['REQUEST_URI'] ?? $base);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?> | Pentagon Safaris</title>
  <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
  <link rel="canonical" href="<?php echo htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8'); ?>">

  <!-- Open Graph (Facebook, WhatsApp, LinkedIn, Slack, etc.) -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Pentagon Safaris">
  <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($ogDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($ogUrl, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image:secure_url" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">

  <!-- Twitter/X Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($ogTitle, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($ogDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&family=Tenor+Sans&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css?v=<?php echo $cssVersion; ?>">

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?php echo $base; ?>assets/svgs/favicon.svg">
</head>
<body>

<?php if ($activeBanner): ?>
<div id="pq-offer-popup" class="pq-offer-popup d-none">
  <?php if (!empty($activeBanner['video_url'])): ?>
    <video src="<?php echo htmlspecialchars($base . ltrim($activeBanner['video_url'], '/')); ?>" autoplay loop muted playsinline class="pq-offer-image"></video>
  <?php elseif (!empty($activeBanner['image_url'])): ?>
    <img src="<?php echo htmlspecialchars($base . ltrim($activeBanner['image_url'], '/')); ?>" alt="Offer Image" class="pq-offer-image">
  <?php endif; ?>
  <div class="pq-offer-content">
    <button id="pq-offer-close" class="btn-close btn-close-white pq-offer-close-btn" aria-label="Close"></button>
    <?php if ($activeBanner['badge']): ?>
      <span class="badge bg-warning text-dark mb-2"><?php echo htmlspecialchars($activeBanner['badge']); ?></span>
    <?php endif; ?>
    <h5 class="fw-bold text-white mb-2"><?php echo htmlspecialchars($activeBanner['title']); ?></h5>
    <?php if ($activeBanner['description']): ?>
      <p class="text-white-50 small mb-3"><?php echo htmlspecialchars($activeBanner['description']); ?></p>
    <?php endif; ?>
    <?php if ($activeBanner['end_time']): ?>
    <div id="pq-banner-countdown" class="d-flex gap-2 text-white font-monospace fw-bold mb-3 justify-content-center" data-endtime="<?php echo htmlspecialchars($activeBanner['end_time']); ?>">
    </div>
    <?php endif; ?>
    <?php if (!empty($activeBanner['target_url'])): ?>
      <a href="<?php echo htmlspecialchars($activeBanner['target_url']); ?>" class="btn btn-sm btn-light rounded-pill px-4 fw-bold w-100" target="_blank">Explore</a>
    <?php endif; ?>
  </div>
</div>

<div id="pq-offer-bubble" class="pq-offer-bubble d-none" title="Show Offers">
  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
  </svg>
</div>

<style>
.pq-offer-popup {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 320px;
  background: rgba(27, 37, 89, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 16px;
  z-index: 1050;
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.pq-offer-popup.d-none {
  opacity: 0;
  transform: translateY(20px);
  pointer-events: none;
  display: flex !important; /* Override bootstrap d-none for transition */
}
.pq-offer-image {
  width: 100%;
  height: 160px;
  object-fit: cover;
}
.pq-offer-content {
  padding: 20px;
  position: relative;
  text-align: center;
}
.pq-offer-close-btn {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 10;
  background-color: rgba(0,0,0,0.3);
  border-radius: 50%;
  padding: 8px;
}
.pq-countdown-block {
  background: rgba(255,255,255,0.15);
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 0.9rem;
}
.pq-offer-bubble {
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 60px;
  height: 60px;
  background: var(--pq-brand, #4318ff);
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  cursor: pointer;
  z-index: 1040;
  transition: transform 0.2s ease;
}
.pq-offer-bubble:hover {
  transform: scale(1.1);
}
@media (max-width: 768px) {
  .pq-offer-popup {
    bottom: 20px;
    right: 20px;
    left: 20px;
    width: auto;
  }
  .pq-offer-bubble {
    bottom: 20px;
    right: 20px;
  }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const popup = document.getElementById('pq-offer-popup');
  const bubble = document.getElementById('pq-offer-bubble');
  const closeBtn = document.getElementById('pq-offer-close');
  
  if (!popup) return;
  
  const bannerId = '<?php echo $activeBanner['id']; ?>';
  // Use sessionStorage so it only persists for the current browser session
  const isDismissed = sessionStorage.getItem('pq_offer_dismissed_' + bannerId);
  
  let timer;
  
  if (!isDismissed) {
    // Show popup
    setTimeout(() => popup.classList.remove('d-none'), 500);
  } else {
    // Show bubble instead
    bubble.classList.remove('d-none');
  }
  
  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      popup.classList.add('d-none');
      setTimeout(() => bubble.classList.remove('d-none'), 300);
      sessionStorage.setItem('pq_offer_dismissed_' + bannerId, 'true');
    });
  }
  
  if (bubble) {
    bubble.addEventListener('click', function() {
      bubble.classList.add('d-none');
      popup.classList.remove('d-none');
    });
  }
  
  const cdEl = document.getElementById('pq-banner-countdown');
  if (cdEl && cdEl.dataset.endtime) {
    const endStr = cdEl.dataset.endtime.replace(' ', 'T');
    const end = new Date(endStr).getTime();
    
    const updateCountdown = () => {
      const now = new Date().getTime();
      const distance = end - now;
      
      if (distance < 0) {
        popup.style.display = 'none';
        bubble.style.display = 'none';
        clearInterval(timer);
        return;
      }
      
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);
      
      cdEl.innerHTML = 
        '<span class="pq-countdown-block">' + days + 'd</span>' +
        '<span class="pq-countdown-block">' + hours + 'h</span>' +
        '<span class="pq-countdown-block">' + minutes + 'm</span>' +
        '<span class="pq-countdown-block">' + seconds + 's</span>';
    };
    
    updateCountdown();
    timer = setInterval(updateCountdown, 1000);
  }
});
</script>
<?php endif; ?>

<!-- Modern Centered Navigation -->
<nav class="navbar navbar-pq navbar-expand-lg">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- Brand / Logo -->
    <a class="navbar-brand" href="<?php echo pq_url(); ?>">
      <img src="<?php echo $base . ltrim($siteLogo, '/'); ?>" alt="Pentagon Safaris Logo">
    </a>

    <!-- Navigation Pill (Centered on Desktop) -->
    <div class="collapse navbar-collapse justify-content-center" id="mainNav">
      <div class="nav-pill-container">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'home') ? 'active' : ''; ?>" href="<?php echo pq_url(); ?>">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo ($current_page === 'destinations') ? 'active' : ''; ?>" href="<?php echo pq_url('destinations'); ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">Destinations</a>
            <ul class="dropdown-menu pq-dropdown-menu">
              <?php foreach ($navDestinations as $navDestination): ?>
              <li><a class="dropdown-item" href="<?php echo pq_url('destinations?destination=' . urlencode($navDestination['name'])); ?>"><?php echo htmlspecialchars($navDestination['name']); ?></a></li>
              <?php endforeach; ?>
              <?php if ($navDestinations !== []): ?>
              <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="<?php echo pq_url('destinations'); ?>">View All Destinations</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'services') ? 'active' : ''; ?>" href="<?php echo pq_url('services'); ?>">What We Do</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'blog') ? 'active' : ''; ?>" href="<?php echo pq_url('blog'); ?>">Blog</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'gallery') ? 'active' : ''; ?>" href="<?php echo pq_url('gallery'); ?>">Gallery</a>
          </li>
          <?php if ($showPosterNav): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo pq_url() . '#posters'; ?>">Posters</a>
          </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'about') ? 'active' : ''; ?>" href="<?php echo pq_url('about'); ?>">Our Story</a>
          </li>
          <li class="nav-item d-lg-none">
            <a class="nav-link <?php echo ($current_page === 'contact') ? 'active' : ''; ?>" href="<?php echo pq_url('contact'); ?>">Contact Us</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Contact Button -->
    <div class="d-none d-lg-block">
      <a href="<?php echo pq_url('contact'); ?>" class="btn-contact-pill">Contact Us</a>
    </div>

    <!-- Mobile Toggler -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

  </div>
</nav>

<!-- Ankara Accent Strip -->
<div class="ankara-accent"></div>
