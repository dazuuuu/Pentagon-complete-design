<?php

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;

Session::start();
Auth::startSession();

$pageTitle = $pageTitle ?? 'Admin';
$adminName = Auth::name();
$initials = strtoupper(substr($adminName ?: 'A', 0, 1));

$navItems = [
  'dashboard' => ['label' => 'Dashboard', 'href' => 'dashboard.php', 'icon' => 'bi-grid-1x2-fill'],
  'destinations' => ['label' => 'Destinations', 'href' => 'destinations.php', 'icon' => 'bi-geo-alt-fill'],
  'tours' => ['label' => 'Tours', 'href' => 'tours.php', 'icon' => 'bi-compass-fill'],
  'gallery' => ['label' => 'Gallery', 'href' => 'gallery.php', 'icon' => 'bi-images'],
  'experiences' => ['label' => 'Experiences', 'href' => 'experiences.php', 'icon' => 'bi-camera-reels-fill'],
  'testimonials' => ['label' => 'Testimonials', 'href' => 'testimonials.php', 'icon' => 'bi-chat-quote-fill'],
  'blog' => ['label' => 'Blog', 'href' => 'blog.php', 'icon' => 'bi-journal-richtext'],
  'services' => ['label' => 'Services', 'href' => 'services.php', 'icon' => 'bi-briefcase-fill'],
  'offers' => ['label' => 'Offers', 'href' => 'offers.php', 'icon' => 'bi-tags-fill'],
  'enquiries' => ['label' => 'Enquiries', 'href' => 'enquiries.php', 'icon' => 'bi-envelope-fill'],
  'clients' => ['label' => 'Clients', 'href' => 'clients.php', 'icon' => 'bi-people-fill'],
  'subscribers' => ['label' => 'Subscribers', 'href' => 'subscribers.php', 'icon' => 'bi-megaphone-fill'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?> | Pentagon Quest Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --pq-bg: #f4f7fe;
      --pq-brand: #4318ff;
      --pq-brand-soft: #e9e3ff;
      --pq-text-soft: #707eae;
      --pq-green: #05cd99;
      --pq-red: #e31a1a;
    }
    body { background: var(--pq-bg); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }

    .sidebar {
      min-height: 100vh;
      background: #fff;
      box-shadow: 0 4px 30px rgba(0,0,0,.04);
      padding: 1.5rem 1rem;
    }
    .sidebar-brand { font-weight: 800; font-size: 1.15rem; color: #1b2559; padding: .5rem .75rem 1.5rem; display: flex; align-items: center; gap: .5rem; }
    .sidebar-brand i { color: var(--pq-brand); }
    .sidebar nav a {
      display: flex; align-items: center; gap: .75rem;
      color: var(--pq-text-soft); text-decoration: none;
      font-weight: 600; font-size: .9rem;
      padding: .7rem .9rem; border-radius: .65rem; margin-bottom: .2rem;
      transition: background .15s, color .15s;
    }
    .sidebar nav a i { font-size: 1.05rem; width: 1.2rem; text-align: center; }
    .sidebar nav a:hover { background: var(--pq-bg); color: #1b2559; }
    .sidebar nav a.active { background: var(--pq-brand); color: #fff; box-shadow: 0 8px 20px rgba(67,24,255,.25); }
    .sidebar nav a.logout { color: var(--pq-red); }

    .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .topbar .crumb { color: var(--pq-text-soft); font-size: .8rem; }
    .topbar .crumb strong { color: #1b2559; font-size: 1.4rem; display: block; margin-top: .1rem; }
    .topbar-search { background: #fff; border-radius: 12px; padding: .5rem 1rem; display: flex; align-items: center; gap: .5rem; min-width: 220px; box-shadow: 0 4px 20px rgba(0,0,0,.03); }
    .topbar-search input { border: none; outline: none; background: transparent; font-size: .85rem; width: 100%; }
    .topbar-icon { width: 42px; height: 42px; border-radius: 12px; background: #fff; display: flex; align-items: center; justify-content: center; color: #1b2559; box-shadow: 0 4px 20px rgba(0,0,0,.03); text-decoration: none; }
    .topbar-avatar { width: 42px; height: 42px; border-radius: 50%; background: var(--pq-brand); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; }

    .content-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.05); }

    .stat-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.05); padding: 1.25rem; display: flex; align-items: center; gap: 1rem; height: 100%; }
    .stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #fff; flex-shrink: 0; }
    .stat-value { font-size: 1.4rem; font-weight: 800; color: #1b2559; }
    .stat-label { font-size: .8rem; color: var(--pq-text-soft); }
    .stat-delta.up { color: var(--pq-green); }
    .stat-delta.down { color: var(--pq-red); }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-3 col-lg-2 sidebar">
      <div class="sidebar-brand"><i class="bi bi-compass"></i> Pentagon Admin</div>
      <nav class="d-flex flex-column">
        <?php foreach ($navItems as $key => $item): ?>
        <a href="<?php echo $item['href']; ?>" class="<?php echo ($currentAdminPage ?? '') === $key ? 'active' : ''; ?>">
          <i class="bi <?php echo $item['icon']; ?>"></i> <?php echo htmlspecialchars($item['label']); ?>
        </a>
        <?php endforeach; ?>
        <hr class="my-2">
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </nav>
    </div>
    <div class="col-md-9 col-lg-10 p-4">
      <div class="topbar">
        <div class="crumb">Pages / <?php echo htmlspecialchars($pageTitle); ?><strong><?php echo htmlspecialchars($pageTitle); ?></strong></div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <div class="topbar-search d-none d-md-flex">
            <i class="bi bi-search text-muted"></i>
            <input type="text" placeholder="Search">
          </div>
          <a href="enquiries.php" class="topbar-icon" title="Enquiries"><i class="bi bi-bell"></i></a>
          <span class="topbar-avatar" title="<?php echo htmlspecialchars($adminName); ?>"><?php echo htmlspecialchars($initials); ?></span>
        </div>
      </div>
