<?php
/**
 * Pentagon Safaris — Shared Header Component (Modern Redesign)
 */
$base = !empty($base_path) ? rtrim($base_path, '/') . '/' : \App\Helpers\Path::baseUrl();

// Self-referencing canonical URL, built from the actual clean address bar
// path. Pairs with the deliberate choice not to force-redirect the old
// *.php URLs (see .htaccess) — this is what actually tells search engines
// which URL is canonical, without the risk of a blanket redirect rule.
$canonicalPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?> | Pentagon Safaris</title>
  <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
  <link rel="canonical" href="https://pentagonquest.com<?php echo htmlspecialchars($canonicalPath); ?>">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&family=Tenor+Sans&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css?v=<?php echo @filemtime(dirname(__DIR__) . '/assets/css/style.css') ?: time(); ?>">
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?php echo $base; ?>assets/svgs/favicon.svg">
</head>
<body>

<!-- Modern Centered Navigation -->
<nav class="navbar navbar-pq navbar-expand-lg">
  <div class="container d-flex justify-content-between align-items-center">
    
    <!-- Brand / Logo -->
    <a class="navbar-brand" href="<?php echo $base; ?>">
      <img src="<?php echo $base; ?>assets/images/logo.png" alt="Pentagon Safaris Logo">
    </a>

    <!-- Navigation Pill (Centered on Desktop) -->
    <div class="collapse navbar-collapse justify-content-center" id="mainNav">
      <div class="nav-pill-container">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'destinations.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>destinations">Destinations</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'tours.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>tours">Tours</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'services.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>services">What We Do</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'mice.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>mice">MICE</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'blog.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>blog">Blog</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'gallery.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>gallery">Gallery</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'about.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>about">Our Story</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Contact Button -->
    <div class="d-none d-lg-block">
      <a href="<?php echo $base; ?>contact" class="btn-contact-pill">Contact Us</a>
    </div>

    <!-- Mobile Toggler -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

  </div>
</nav>

<!-- Ankara Accent Strip -->
<div class="ankara-accent"></div>

<!-- Mobile Off-canvas Menu -->
<div class="offcanvas offcanvas-end pq-offcanvas" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
  <div class="offcanvas-header">
    <a href="<?php echo $base; ?>" class="offcanvas-brand" id="mobileNavLabel">
      <img src="<?php echo $base; ?>assets/images/logo.png" alt="Pentagon Safaris Logo">
    </a>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="pq-offcanvas-nav">
      <li>
        <a class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          Home
        </a>
      </li>
      <li>
        <a class="<?php echo ($current_page === 'destinations.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>destinations">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 12-9 12s-9-5-9-12a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Destinations
        </a>
      </li>
      <li>
        <a class="<?php echo ($current_page === 'tours.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>tours">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
          Tours
        </a>
      </li>
      <li>
        <a class="<?php echo ($current_page === 'services.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>services">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
          What We Do
        </a>
      </li>
      <li>
        <a class="<?php echo ($current_page === 'mice.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>mice">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          MICE
        </a>
      </li>
      <li>
        <a class="<?php echo ($current_page === 'blog.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>blog">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Blog
        </a>
      </li>
      <li>
        <a class="<?php echo ($current_page === 'gallery.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>gallery">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
          Gallery
        </a>
      </li>
      <li>
        <a class="<?php echo ($current_page === 'about.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>about">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
          Our Story
        </a>
      </li>
    </ul>
    <a href="<?php echo $base; ?>contact" class="btn-contact-pill pq-offcanvas-contact">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
      Contact Us
    </a>
  </div>
</div>
