<?php
/**
 * Pentagon Safaris — Dynamic XML Sitemap
 * Served at /sitemap.xml via the .htaccess rewrite rule below. Includes
 * every static page plus every active tour, destination, and blog post at
 * their clean URLs, so new content is picked up automatically without
 * hand-editing this file.
 */
require_once __DIR__ . '/includes/bootstrap.php';

use App\Services\TourService;
use App\Services\DestinationService;
use App\Services\BlogService;

$domain = 'https://pentagonquest.com';
$today = date('Y-m-d');

$staticPages = [
    ['path' => '/', 'changefreq' => 'weekly', 'priority' => '1.0'],
    ['path' => '/destinations', 'changefreq' => 'weekly', 'priority' => '0.9'],
    ['path' => '/tours', 'changefreq' => 'weekly', 'priority' => '0.9'],
    ['path' => '/services', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['path' => '/mice', 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['path' => '/gallery', 'changefreq' => 'monthly', 'priority' => '0.7'],
    ['path' => '/blog', 'changefreq' => 'weekly', 'priority' => '0.7'],
    ['path' => '/about', 'changefreq' => 'monthly', 'priority' => '0.8'],
    ['path' => '/contact', 'changefreq' => 'monthly', 'priority' => '0.9'],
];

try {
    $tours = (new TourService())->getActive();
} catch (Throwable) {
    $tours = [];
}
try {
    $destinations = (new DestinationService())->getActive();
} catch (Throwable) {
    $destinations = [];
}
try {
    $blogPosts = (new BlogService())->getActive();
} catch (Throwable) {
    $blogPosts = [];
}

header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($staticPages as $page): ?>
  <url>
    <loc><?php echo htmlspecialchars($domain . $page['path']); ?></loc>
    <lastmod><?php echo $today; ?></lastmod>
    <changefreq><?php echo $page['changefreq']; ?></changefreq>
    <priority><?php echo $page['priority']; ?></priority>
  </url>
<?php endforeach; ?>
<?php foreach ($tours as $tour): ?>
  <url>
    <loc><?php echo htmlspecialchars($domain . '/tours/' . (int) $tour['id']); ?></loc>
    <lastmod><?php echo $today; ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
<?php foreach ($destinations as $destination): ?>
  <url>
    <loc><?php echo htmlspecialchars($domain . '/destinations/' . (int) $destination['id']); ?></loc>
    <lastmod><?php echo $today; ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
<?php endforeach; ?>
<?php foreach ($blogPosts as $post): ?>
  <url>
    <loc><?php echo htmlspecialchars($domain . '/blog/' . (int) $post['id']); ?></loc>
    <lastmod><?php echo $today; ?></lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.5</priority>
  </url>
<?php endforeach; ?>
</urlset>
