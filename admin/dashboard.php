<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Services\DestinationService;
use App\Services\TourService;
use App\Services\GalleryService;
use App\Services\TestimonialService;
use App\Services\EnquiryService;
use App\Services\SubscriptionService;

Auth::requireAuth();

$destinationService = new DestinationService();
$tourService = new TourService();
$galleryService = new GalleryService();
$testimonialService = new TestimonialService();
$enquiryService = new EnquiryService();
$subscriptionService = new SubscriptionService();

$pageTitle = 'Dashboard';
$currentAdminPage = 'dashboard';

include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-4">Dashboard</h2>
  <div class="row g-3">
    <div class="col-md-4"><div class="p-3 border rounded">Destinations: <strong><?php echo count($destinationService->getAll()); ?></strong></div></div>
    <div class="col-md-4"><div class="p-3 border rounded">Tours: <strong><?php echo count($tourService->getAll()); ?></strong></div></div>
    <div class="col-md-4"><div class="p-3 border rounded">Gallery Items: <strong><?php echo count($galleryService->getAll()); ?></strong></div></div>
    <div class="col-md-4"><div class="p-3 border rounded">Testimonials: <strong><?php echo count($testimonialService->getAll()); ?></strong></div></div>
    <div class="col-md-4"><div class="p-3 border rounded">Enquiries: <strong><?php echo $enquiryService->count(); ?></strong></div></div>
    <div class="col-md-4"><div class="p-3 border rounded">Subscribers: <strong><?php echo $subscriptionService->count(); ?></strong></div></div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
