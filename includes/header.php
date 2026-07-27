<?php
/**
 * Pentagon Quest — Shared Header Component (Modern Redesign)
 */
$base = isset($base_path) ? $base_path : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?> | Pentagon Quest</title>
  <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@300;400;500;600;700&family=Tenor+Sans&display=swap" rel="stylesheet">
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?php echo $base; ?>assets/svgs/favicon.svg">
</head>
<body>

<!-- Modern Centered Navigation -->
<nav class="navbar navbar-pq navbar-expand-lg">
  <div class="container d-flex justify-content-between align-items-center">
    
    <!-- Brand / Logo -->
    <a class="navbar-brand" href="<?php echo $base; ?>index.php">
      <img src="<?php echo $base; ?>assets/images/logo.png" alt="Pentagon Quest Logo">
    </a>

    <!-- Navigation Pill (Centered on Desktop) -->
    <div class="collapse navbar-collapse justify-content-center" id="mainNav">
      <div class="nav-pill-container">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'index.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>index.php">Home</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?php echo ($current_page === 'destinations.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>destinations.php">Destinations</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'services.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>services.php">What We Do</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'blog.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>blog.php">Blog</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'gallery.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>gallery.php">Gallery</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'about.php') ? 'active' : ''; ?>" href="<?php echo $base; ?>about.php">Our Story</a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Contact Button -->
    <div class="d-none d-lg-block">
      <a href="<?php echo $base; ?>contact.php" class="btn-contact-pill">Contact Us</a>
    </div>

    <!-- Mobile Toggler -->
    <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

  </div>
</nav>

<!-- Ankara Accent Strip -->
<div class="ankara-accent"></div>
