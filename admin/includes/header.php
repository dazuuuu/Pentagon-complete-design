<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;

Session::start();
Auth::startSession();

$pageTitle = $pageTitle ?? 'Admin';
$adminName = Auth::name();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?> | Pentagon Quest Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f4f6f8; }
    .sidebar { min-height: 100vh; background: #1a2e1a; }
    .sidebar a { color: rgba(255,255,255,.8); text-decoration: none; display: block; padding: .6rem 1rem; border-radius: .5rem; }
    .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,.1); color: #fff; }
    .content-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,.05); }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3 col-lg-2 sidebar p-3 text-white">
      <h5 class="mb-4">Pentagon Admin</h5>
      <p class="small text-white-50 mb-3">Hello, <?php echo htmlspecialchars($adminName); ?></p>
      <nav class="d-flex flex-column gap-1">
        <a href="dashboard.php" class="<?php echo ($currentAdminPage ?? '') === 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
        <a href="destinations.php" class="<?php echo ($currentAdminPage ?? '') === 'destinations' ? 'active' : ''; ?>">Destinations</a>
        <a href="tours.php" class="<?php echo ($currentAdminPage ?? '') === 'tours' ? 'active' : ''; ?>">Tours</a>
        <a href="gallery.php" class="<?php echo ($currentAdminPage ?? '') === 'gallery' ? 'active' : ''; ?>">Gallery</a>
        <a href="testimonials.php" class="<?php echo ($currentAdminPage ?? '') === 'testimonials' ? 'active' : ''; ?>">Testimonials</a>
        <a href="enquiries.php" class="<?php echo ($currentAdminPage ?? '') === 'enquiries' ? 'active' : ''; ?>">Enquiries</a>
        <a href="subscribers.php" class="<?php echo ($currentAdminPage ?? '') === 'subscribers' ? 'active' : ''; ?>">Subscribers</a>
        <a href="logout.php" class="mt-3">Logout</a>
      </nav>
    </div>
    <div class="col-md-9 col-lg-10 p-4">
