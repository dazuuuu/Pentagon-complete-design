<?php

require_once dirname(__DIR__, 2) . '/includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;

require_once __DIR__ . '/ui.php';

Session::start();
Auth::startSession();

$pageTitle = $pageTitle ?? 'Admin';
$adminName = Auth::name();
$initials = strtoupper(substr($adminName ?: 'A', 0, 1));

$navItems = [
  'dashboard' => ['label' => 'Dashboard', 'href' => 'dashboard.php', 'icon' => 'bi-grid-1x2-fill'],
  'homepage' => ['label' => 'Homepage Media', 'href' => 'homepage.php', 'icon' => 'bi-house-heart-fill'],
  'posters' => ['label' => 'Posters', 'href' => 'posters.php', 'icon' => 'bi-file-image-fill'],
  'destinations' => ['label' => 'Destinations', 'href' => 'destinations.php', 'icon' => 'bi-geo-alt-fill'],
  'tours' => ['label' => 'Tours', 'href' => 'tours.php', 'icon' => 'bi-compass-fill'],
  'gallery' => ['label' => 'Gallery', 'href' => 'gallery.php', 'icon' => 'bi-images'],
  'experiences' => ['label' => 'Experiences', 'href' => 'experiences.php', 'icon' => 'bi-camera-reels-fill'],
  'testimonials' => ['label' => 'Testimonials', 'href' => 'testimonials.php', 'icon' => 'bi-chat-quote-fill'],
  'blog' => ['label' => 'Blog', 'href' => 'blog.php', 'icon' => 'bi-journal-richtext'],
  'our-story' => ['label' => 'Our Story', 'href' => 'our-story.php', 'icon' => 'bi-book-half'],
  'settings' => ['label' => 'Site Settings', 'href' => 'settings.php', 'icon' => 'bi-gear-fill'],
  'services' => ['label' => 'Services', 'href' => 'services.php', 'icon' => 'bi-briefcase-fill'],
  'offers' => ['label' => 'Offers', 'href' => 'offers.php', 'icon' => 'bi-tags-fill'],
  'packages' => ['label' => 'Packages', 'href' => 'packages.php', 'icon' => 'bi-table'],
  'enquiries' => ['label' => 'Enquiries', 'href' => 'enquiries.php', 'icon' => 'bi-envelope-fill'],
  'clients' => ['label' => 'Clients', 'href' => 'clients.php', 'icon' => 'bi-people-fill'],
  'reports' => ['label' => 'Reports', 'href' => 'reports.php', 'icon' => 'bi-file-earmark-spreadsheet-fill'],
  'subscribers' => ['label' => 'Subscribers', 'href' => 'subscribers.php', 'icon' => 'bi-megaphone-fill'],
  'updates' => ['label' => 'Updates', 'href' => 'updates.php', 'icon' => 'bi-arrow-repeat'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?> | Pentagon Safaris Admin</title>
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
      height: 100vh;
      position: sticky;
      top: 0;
      overflow-y: auto;
      background: #fff;
      box-shadow: 0 4px 30px rgba(0,0,0,.04);
    }
    .sidebar-inner { padding: 1.5rem 1rem; }
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

    /* ── WordPress-style list & edit screens ── */
    .wp-head { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .wp-head h2 { font-size: 1.45rem; font-weight: 700; color: #1b2559; margin: 0; }
    .wp-add-new { border: 1px solid var(--pq-brand); color: var(--pq-brand); background: #fff; border-radius: 8px; padding: .3rem .8rem; font-weight: 600; font-size: .85rem; text-decoration: none; }
    .wp-add-new:hover { background: var(--pq-brand); color: #fff; }
    .wp-back { color: var(--pq-text-soft); text-decoration: none; font-size: .85rem; }
    .wp-subsubsub { display: flex; gap: .25rem; flex-wrap: wrap; font-size: .85rem; margin-bottom: .75rem; color: var(--pq-text-soft); }
    .wp-subsubsub a { color: var(--pq-brand); text-decoration: none; }
    .wp-subsubsub a.current { color: #1b2559; font-weight: 700; }
    .wp-table { margin: 0; }
    .wp-table thead th { font-size: .8rem; text-transform: uppercase; letter-spacing: .03em; color: var(--pq-text-soft); font-weight: 700; border-bottom-width: 1px; white-space: nowrap; }
    .wp-table td { vertical-align: middle; padding-top: .75rem; padding-bottom: .75rem; }
    .wp-table .wp-check { width: 36px; }
    .wp-table .wp-thumb { width: 72px; height: 72px; object-fit: cover; border-radius: 8px; background: var(--pq-bg); display: block; }
    .wp-thumbs { display: flex; gap: 4px; align-items: center; }
    .wp-thumbs img { width: 52px; height: 52px; object-fit: cover; border-radius: 6px; }
    .wp-thumbs .more { font-size: .75rem; color: var(--pq-text-soft); font-weight: 600; padding-left: 4px; }
    .wp-title { font-weight: 700; color: #1b2559; text-decoration: none; }
    .wp-title:hover { color: var(--pq-brand); }
    .wp-title.is-untitled { color: var(--pq-text-soft); font-style: italic; font-weight: 500; }
    .wp-row-actions { font-size: .78rem; margin-top: .25rem; visibility: hidden; }
    .wp-table tr:hover .wp-row-actions, .wp-table tr:focus-within .wp-row-actions { visibility: visible; }
    @media (hover: none) { .wp-row-actions { visibility: visible; } }
    .wp-row-actions a, .wp-row-actions button { color: var(--pq-brand); text-decoration: none; background: none; border: 0; padding: 0; font: inherit; }
    .wp-row-actions .sep { color: #c3c4c7; margin: 0 .3rem; }
    .wp-row-actions .danger { color: var(--pq-red); }
    .wp-status { display: inline-block; font-size: .72rem; font-weight: 700; padding: .2rem .55rem; border-radius: 999px; }
    .wp-status.is-live { background: #e6faf5; color: #02855f; }
    .wp-status.is-draft { background: #f1f1f4; color: #6b6f80; }
    .wp-bulk { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; margin-bottom: .75rem; }
    .wp-bulk select { width: auto; }
    .wp-empty { text-align: center; padding: 3rem 1rem; color: var(--pq-text-soft); }
    .wp-empty i { font-size: 2.2rem; display: block; margin-bottom: .5rem; color: #c3c8e0; }

    .wp-editor-title { font-size: 1.35rem; font-weight: 600; padding: .65rem .9rem; border-radius: 10px; }
    .wp-box { background: #fff; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,.05); margin-bottom: 1rem; }
    .wp-box-head { padding: .8rem 1rem; border-bottom: 1px solid #eef0f6; font-weight: 700; color: #1b2559; font-size: .9rem; }
    .wp-box-body { padding: 1rem; }
    .wp-box-foot { padding: .75rem 1rem; border-top: 1px solid #eef0f6; background: #fafbff; border-radius: 0 0 14px 14px; display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
    .wp-delete-link { color: var(--pq-red); font-size: .85rem; background: none; border: 0; padding: 0; text-decoration: underline; }
    .wp-optional { font-weight: 400; color: var(--pq-text-soft); font-size: .78rem; }

    .wp-dropzone { position: relative; display: block; border: 2px dashed #cdd3ea; border-radius: 14px; background: #fafbff; cursor: pointer; transition: border-color .15s, background .15s; margin: 0; }
    .wp-dropzone:hover, .wp-dropzone.is-over { border-color: var(--pq-brand); background: var(--pq-brand-soft); }
    .wp-dropzone input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; }
    .wp-dropzone-empty { display: flex; flex-direction: column; align-items: center; gap: .25rem; padding: 2rem 1rem; text-align: center; color: #1b2559; }
    .wp-dropzone-empty i { font-size: 2rem; color: var(--pq-brand); }
    .wp-dropzone-empty small { color: var(--pq-text-soft); }
    .wp-dropzone-previews { display: none; flex-wrap: wrap; gap: .5rem; padding: .75rem; }
    .wp-dropzone.has-files .wp-dropzone-empty { padding: .9rem 1rem .25rem; }
    .wp-dropzone.has-files .wp-dropzone-empty i, .wp-dropzone.has-files .wp-dropzone-empty small { display: none; }
    .wp-dropzone.has-files .wp-dropzone-previews { display: flex; }
    .wp-dropzone-previews img { width: 96px; height: 96px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
    .wp-dropzone.is-single .wp-dropzone-previews img { width: 100%; height: auto; max-height: 320px; object-fit: contain; background: #fff; }

    .wp-media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: .75rem; }
    .wp-media-item { position: relative; border-radius: 10px; overflow: hidden; aspect-ratio: 1; background: var(--pq-bg); }
    .wp-media-item img { width: 100%; height: 100%; object-fit: cover; }
    .wp-media-item button { position: absolute; top: 6px; right: 6px; width: 28px; height: 28px; border-radius: 50%; border: 0; background: rgba(0,0,0,.6); color: #fff; line-height: 1; }
    .wp-media-item button:hover { background: var(--pq-red); }

    .wp-progress { position: fixed; left: 0; right: 0; top: 0; z-index: 3000; height: 4px; background: transparent; display: none; }
    .wp-progress span { display: block; height: 100%; width: 0; background: var(--pq-brand); transition: width .2s; }
    .wp-progress-label { position: fixed; top: 12px; left: 50%; transform: translateX(-50%); z-index: 3000; background: #1b2559; color: #fff; font-size: .85rem; padding: .4rem .9rem; border-radius: 999px; display: none; }

    /* ── Spreadsheet list + slide-in editor ── */
    .sheet-head { display: flex; align-items: flex-end; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .sheet-head h2 { font-size: 1.4rem; font-weight: 700; color: #1b2559; margin: 0; display: flex; align-items: center; gap: .5rem; }
    .sheet-head p { margin: .25rem 0 0; color: var(--pq-text-soft); font-size: .88rem; }
    .sheet-count { font-size: .75rem; font-weight: 700; background: var(--pq-brand-soft); color: var(--pq-brand); border-radius: 999px; padding: .15rem .6rem; }
    .sheet-wrap { max-height: 70vh; overflow: auto; border: 1px solid #dfe3ec; border-radius: 10px; }
    .sheet-table { margin: 0; font-size: .88rem; border-collapse: separate; border-spacing: 0; }
    .sheet-table thead th { position: sticky; top: 0; z-index: 2; background: #f3f5f9; color: #3b4563; font-weight: 700; font-size: .78rem; text-transform: uppercase; letter-spacing: .03em; border-bottom: 1px solid #dfe3ec; white-space: nowrap; padding: .6rem .75rem; }
    .sheet-table td { border-bottom: 1px solid #edf0f5; border-right: 1px solid #f1f3f7; padding: .55rem .75rem; vertical-align: middle; color: #1b2559; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .sheet-table th:not(:last-child) { border-right: 1px solid #e3e7ef; }
    .sheet-table tbody tr { cursor: pointer; transition: background .1s; }
    .sheet-table tbody tr:nth-child(even) td { background: #fbfcfe; }
    .sheet-table tbody tr:hover td, .sheet-table tbody tr:focus td { background: #eef2ff; outline: none; }
    .sheet-table .sheet-num { width: 48px; text-align: center; color: #8a93ad; background: #f6f7fa !important; font-variant-numeric: tabular-nums; font-size: .78rem; }
    .sheet-table .sheet-thumb-col { width: 64px; padding: .35rem .5rem; }
    .sheet-thumb { width: 48px; height: 36px; object-fit: cover; border-radius: 5px; display: block; background: var(--pq-bg); }
    .sheet-thumb-empty { display: flex; align-items: center; justify-content: center; color: #b8bfd4; }
    .sheet-table .sheet-actions-col { width: 80px; text-align: right; }
    .sheet-edit { font-size: .78rem; white-space: nowrap; }
    .sheet-table td.num { text-align: right; font-variant-numeric: tabular-nums; }
    .sheet-table td.wrap { white-space: normal; min-width: 220px; }
    .sheet-yes { color: var(--pq-green); font-weight: 700; }
    .sheet-no { color: #c3c8d6; }

    .sheet-editor { --bs-offcanvas-width: min(620px, 100vw); }
    .sheet-editor .offcanvas-header { border-bottom: 1px solid #eef0f6; }
    .sheet-editor .offcanvas-title { font-weight: 700; color: #1b2559; }
    .sheet-editor .form-label { font-size: .8rem; font-weight: 600; color: #3b4563; margin-bottom: .3rem; }
    .sheet-editor .form-control, .sheet-editor .form-select { border-radius: 10px; border-color: #e0e5f2; }
    .sheet-editor .form-control:focus, .sheet-editor .form-select:focus { border-color: var(--pq-brand); box-shadow: 0 0 0 3px rgba(67,24,255,.12); }
    .sheet-switch { padding-top: .2rem; }
    .sheet-editor-foot { display: flex; align-items: center; gap: .5rem; padding: .9rem 1rem; border-top: 1px solid #eef0f6; background: #fafbff; }
    .sheet-editor .wp-dropzone.is-single .wp-dropzone-previews img { max-height: 200px; }
    .sheet-editor .wp-media-grid { grid-template-columns: repeat(auto-fill, minmax(96px, 1fr)); }

    /* ── Advanced table search ── */
    .pq-search { display: flex; flex-wrap: wrap; gap: .5rem; align-items: center; padding: .75rem; margin-bottom: .75rem; background: var(--pq-bg); border-radius: 12px; }
    .pq-search-input { position: relative; flex: 1 1 240px; }
    .pq-search-input i { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: var(--pq-text-soft); }
    .pq-search-input input { padding-left: 2.2rem; border-radius: 10px; border-color: #e0e5f2; }
    .pq-search select { width: auto; max-width: 200px; border-radius: 10px; border-color: #e0e5f2; font-size: .85rem; }
    .pq-search-count { font-size: .8rem; color: var(--pq-text-soft); margin-left: auto; white-space: nowrap; }
    .pq-search-clear { font-size: .8rem; }
    th.pq-sortable { cursor: pointer; user-select: none; }
    th.pq-sortable::after { content: '\2195'; opacity: .3; margin-left: .3rem; font-size: .75em; }
    th.pq-sortable.is-asc::after { content: '\2191'; opacity: .9; }
    th.pq-sortable.is-desc::after { content: '\2193'; opacity: .9; }
    mark.pq-hit { background: #fff3b0; padding: 0; border-radius: 2px; }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <div class="offcanvas-md offcanvas-start col-md-3 col-lg-2 sidebar" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
      <div class="offcanvas-header d-md-none pt-4 pb-0 px-4">
        <h5 class="offcanvas-title sidebar-brand p-0 m-0" id="sidebarMenuLabel"><i class="bi bi-compass"></i> Pentagon Safaris</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body d-flex flex-column sidebar-inner">
        <div class="sidebar-brand d-none d-md-flex"><i class="bi bi-compass"></i> Pentagon Safaris</div>
        <nav class="d-flex flex-column w-100">
          <?php foreach ($navItems as $key => $item): ?>
          <a href="<?php echo $item['href']; ?>" class="<?php echo ($currentAdminPage ?? '') === $key ? 'active' : ''; ?>">
            <i class="bi <?php echo $item['icon']; ?>"></i> <?php echo htmlspecialchars($item['label']); ?>
          </a>
          <?php endforeach; ?>
          <hr class="my-2">
          <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </nav>
      </div>
    </div>
    <div class="col-md-9 col-lg-10 p-4">
      <div class="topbar">
        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-light d-md-none border-0 shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
            <i class="bi bi-list fs-4"></i>
          </button>
          <div class="crumb">Pages / <?php echo htmlspecialchars($pageTitle); ?><strong><?php echo htmlspecialchars($pageTitle); ?></strong></div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap mt-2 mt-sm-0">
          <div class="topbar-search d-none d-md-flex">
            <i class="bi bi-search text-muted"></i>
            <input type="search" id="adminTopSearch" placeholder="Search this page…" autocomplete="off">
          </div>
          <a href="enquiries.php" class="topbar-icon" title="Enquiries"><i class="bi bi-bell"></i></a>
          <span class="topbar-avatar" title="<?php echo htmlspecialchars($adminName); ?>"><?php echo htmlspecialchars($initials); ?></span>
        </div>
      </div>
