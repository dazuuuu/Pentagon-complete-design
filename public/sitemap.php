<?php
require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/xml; charset=UTF-8');

$base = rtrim(($_ENV['APP_URL'] ?? 'https://pentagonquest.com'), '/');
$today = date('Y-m-d');
$pages = [
    ['path' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
    ['path' => '/destinations.php', 'priority' => '0.9', 'changefreq' => 'weekly'],
    ['path' => '/services.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['path' => '/gallery.php', 'priority' => '0.7', 'changefreq' => 'monthly'],
    ['path' => '/blog.php', 'priority' => '0.7', 'changefreq' => 'weekly'],
    ['path' => '/about.php', 'priority' => '0.8', 'changefreq' => 'monthly'],
    ['path' => '/contact.php', 'priority' => '0.9', 'changefreq' => 'monthly'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $page): ?>
  <url>
    <loc><?php echo htmlspecialchars($base . $page['path']); ?></loc>
    <lastmod><?php echo $today; ?></lastmod>
    <changefreq><?php echo $page['changefreq']; ?></changefreq>
    <priority><?php echo $page['priority']; ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
