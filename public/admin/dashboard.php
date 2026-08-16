<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Services\DestinationService;
use App\Services\TourService;
use App\Services\GalleryService;
use App\Services\TestimonialService;
use App\Services\EnquiryService;
use App\Services\SubscriptionService;
use App\Services\ClientService;

Auth::requireAuth();

$destinationService = new DestinationService();
$tourService = new TourService();
$galleryService = new GalleryService();
$testimonialService = new TestimonialService();
$enquiryService = new EnquiryService();
$subscriptionService = new SubscriptionService();
$clientService = new ClientService();

$pageTitle = 'Dashboard';
$currentAdminPage = 'dashboard';

$sevenDaysAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
$fourteenDaysAgo = date('Y-m-d H:i:s', strtotime('-14 days'));

function pq_delta(int $thisPeriod, int $lastPeriod): array
{
    if ($lastPeriod === 0) {
        return $thisPeriod > 0 ? ['pct' => null, 'dir' => 'up'] : ['pct' => 0.0, 'dir' => 'up'];
    }
    $pct = round((($thisPeriod - $lastPeriod) / $lastPeriod) * 100, 1);
    return ['pct' => abs($pct), 'dir' => $pct >= 0 ? 'up' : 'down'];
}

function pq_monthly_series(array $rows, int $months): array
{
    $counts = [];
    foreach ($rows as $r) {
        $counts[$r['ym']] = (int) $r['c'];
    }
    $labels = [];
    $data = [];
    for ($i = $months - 1; $i >= 0; $i--) {
        $ym = date('Y-m', strtotime("-{$i} months"));
        $labels[] = date('M', strtotime("-{$i} months"));
        $data[] = $counts[$ym] ?? 0;
    }
    return [$labels, $data];
}

$enquiriesThisWeek = $enquiryService->countSince($sevenDaysAgo);
$enquiriesLastWeek = $enquiryService->countSince($fourteenDaysAgo) - $enquiriesThisWeek;
$enquiriesDelta = pq_delta($enquiriesThisWeek, $enquiriesLastWeek);

$clientsThisWeek = $clientService->countSince($sevenDaysAgo);
$clientsLastWeek = $clientService->countSince($fourteenDaysAgo) - $clientsThisWeek;
$clientsDelta = pq_delta($clientsThisWeek, $clientsLastWeek);

$subsThisWeek = $subscriptionService->countSince($sevenDaysAgo);
$subsLastWeek = $subscriptionService->countSince($fourteenDaysAgo) - $subsThisWeek;
$subsDelta = pq_delta($subsThisWeek, $subsLastWeek);

$statCards = [
    ['label' => 'Enquiries', 'value' => $enquiryService->count(), 'icon' => 'bi-envelope-fill', 'bg' => '#4318ff', 'delta' => $enquiriesDelta],
    ['label' => 'Clients', 'value' => $clientService->count(), 'icon' => 'bi-people-fill', 'bg' => '#05cd99', 'delta' => $clientsDelta],
    ['label' => 'Subscribers', 'value' => $subscriptionService->count(), 'icon' => 'bi-megaphone-fill', 'bg' => '#ffb547', 'delta' => $subsDelta],
    ['label' => 'Tours', 'value' => count($tourService->getAll()), 'icon' => 'bi-compass-fill', 'bg' => '#e31a8f', 'delta' => null],
];

[$enquiryLabels, $enquiryData] = pq_monthly_series($enquiryService->monthlyCounts(6), 6);
[$subsLabels, $subsData] = pq_monthly_series($subscriptionService->monthlyCounts(6), 6);

$recentEnquiries = array_slice($enquiryService->getAll(), 0, 5);
$recentClients = array_slice($clientService->getAll(), 0, 5);

include __DIR__ . '/includes/header.php';
?>
<div class="row g-3 mb-4">
  <?php foreach ($statCards as $card): ?>
  <div class="col-md-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background: <?php echo $card['bg']; ?>;"><i class="bi <?php echo $card['icon']; ?>"></i></div>
      <div>
        <div class="stat-value"><?php echo (int) $card['value']; ?></div>
        <div class="stat-label"><?php echo htmlspecialchars($card['label']); ?></div>
        <?php if ($card['delta'] !== null): ?>
          <?php if ($card['delta']['pct'] === null): ?>
            <span class="stat-delta up small"><i class="bi bi-arrow-up-short"></i> new this week</span>
          <?php else: ?>
            <span class="stat-delta <?php echo $card['delta']['dir']; ?> small">
              <i class="bi bi-arrow-<?php echo $card['delta']['dir']; ?>-short"></i> <?php echo $card['delta']['pct']; ?>% vs last week
            </span>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
  <div class="col-lg-6">
    <div class="content-card p-4 h-100">
      <h6 class="mb-3">Enquiries (last 6 months)</h6>
      <canvas id="enquiriesChart" height="180"></canvas>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="content-card p-4 h-100">
      <h6 class="mb-3">New Subscribers (last 6 months)</h6>
      <canvas id="subscribersChart" height="180"></canvas>
    </div>
  </div>
</div>

<div class="content-card p-4 mb-4">
  <h6 class="mb-3">Site Content</h6>
  <div class="row g-3 text-center">
    <div class="col-6 col-md-3">
      <div class="stat-value"><?php echo count($destinationService->getAll()); ?></div>
      <div class="stat-label">Destinations</div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-value"><?php echo count($galleryService->getAll()); ?></div>
      <div class="stat-label">Gallery Items</div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-value"><?php echo count($testimonialService->getAll()); ?></div>
      <div class="stat-label">Testimonials</div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-value"><?php echo count($tourService->getAll()); ?></div>
      <div class="stat-label">Tours</div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="content-card p-4 h-100">
      <h6 class="mb-3">Recent Enquiries</h6>
      <table class="table table-sm align-middle mb-0">
        <tbody>
          <?php foreach ($recentEnquiries as $item): ?>
          <tr>
            <td>
              <div class="fw-semibold"><?php echo htmlspecialchars($item['full_name']); ?></div>
              <div class="text-muted small"><?php echo htmlspecialchars($item['interest'] ?? 'General enquiry'); ?></div>
            </td>
            <td class="text-end text-muted small"><?php echo htmlspecialchars(date('M j', strtotime($item['created_at']))); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recentEnquiries)): ?>
          <tr><td class="text-muted text-center">No enquiries yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="content-card p-4 h-100">
      <h6 class="mb-3">Recent Clients</h6>
      <table class="table table-sm align-middle mb-0">
        <tbody>
          <?php foreach ($recentClients as $item): ?>
          <tr>
            <td>
              <div class="fw-semibold"><?php echo htmlspecialchars($item['full_name']); ?></div>
              <div class="text-muted small"><?php echo htmlspecialchars($item['interest'] ?? ''); ?></div>
            </td>
            <td class="text-end">
              <span class="badge bg-<?php echo ['scheduled' => 'primary', 'completed' => 'success', 'cancelled' => 'secondary'][$item['status']] ?? 'secondary'; ?>">
                <?php echo htmlspecialchars(ucfirst($item['status'])); ?>
              </span>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recentClients)): ?>
          <tr><td class="text-muted text-center">No clients yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
  new Chart(document.getElementById('enquiriesChart'), {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($enquiryLabels); ?>,
      datasets: [{
        data: <?php echo json_encode($enquiryData); ?>,
        backgroundColor: '#4318ff',
        borderRadius: 6,
        maxBarThickness: 28
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });

  new Chart(document.getElementById('subscribersChart'), {
    type: 'line',
    data: {
      labels: <?php echo json_encode($subsLabels); ?>,
      datasets: [{
        data: <?php echo json_encode($subsData); ?>,
        borderColor: '#05cd99',
        backgroundColor: 'rgba(5,205,153,0.1)',
        fill: true,
        tension: 0.35,
        pointRadius: 3
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    }
  });
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
