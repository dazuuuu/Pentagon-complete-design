<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Services\ClientService;

Auth::requireAuth();

$service = new ClientService();

$filters = [
  'q' => trim($_GET['q'] ?? ''),
  'status' => trim($_GET['status'] ?? ''),
  'country' => trim($_GET['country'] ?? ''),
  'date_from' => trim($_GET['date_from'] ?? ''),
  'date_to' => trim($_GET['date_to'] ?? ''),
];

$clients = $service->search($filters);
$countries = $service->distinctCountries();

// Group rows by calendar date (derived from created_at), most recent first.
$groups = [];
foreach ($clients as $client) {
  $date = date('Y-m-d', strtotime($client['created_at']));
  $groups[$date][] = $client;
}
krsort($groups);

$queryString = http_build_query(array_filter($filters, fn ($v) => $v !== ''));

$pageTitle = 'Reports';
$currentAdminPage = 'reports';
include __DIR__ . '/includes/header.php';
?>
<style>
  .admin-sheet-wrap { overflow-x: auto; }
  .admin-sheet {
    border-collapse: collapse;
    width: 100%;
    min-width: 900px;
    font-size: 13px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    background: #fff;
  }
  .admin-sheet th, .admin-sheet td {
    border: 1px solid #d7dbe3;
    padding: 6px 10px;
    white-space: nowrap;
  }
  .admin-sheet .col-letters th {
    background: #eef1f6;
    color: #6b7280;
    font-weight: 600;
    text-align: center;
    font-size: 11px;
    border-top: none;
  }
  .admin-sheet .col-letters .corner { background: #e3e7ee; }
  .admin-sheet .header-row th {
    background: #4318ff;
    color: #fff;
    font-weight: 700;
    text-align: left;
    position: sticky;
    top: 0;
  }
  .admin-sheet .row-num {
    background: #eef1f6;
    color: #6b7280;
    text-align: center;
    font-weight: 600;
    font-size: 11px;
    width: 34px;
  }
  .admin-sheet tbody tr.data-row:nth-child(even) td:not(.row-num) { background: #fafbfd; }
  .admin-sheet tbody tr.data-row:nth-child(odd) td:not(.row-num) { background: #ffffff; }
  .admin-sheet .date-group-row td {
    background: #e9e3ff;
    font-weight: 700;
    color: #1b2559;
  }
  .admin-sheet tbody tr.data-row td { white-space: normal; }
</style>

<div class="content-card p-4 mb-4">
  <h2 class="mb-3">Client Reports</h2>

  <form method="GET" class="row g-2 mb-3" id="reportFilterForm">
    <div class="col-md-4">
      <input type="text" class="form-control" id="liveSearch" name="q" placeholder="Search name, email, phone..." value="<?php echo htmlspecialchars($filters['q']); ?>">
    </div>
    <div class="col-md-2">
      <select class="form-select" name="status">
        <option value="">All statuses</option>
        <option value="scheduled" <?php echo $filters['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
        <option value="completed" <?php echo $filters['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
        <option value="cancelled" <?php echo $filters['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
      </select>
    </div>
    <div class="col-md-2">
      <select class="form-select" name="country">
        <option value="">All countries</option>
        <?php foreach ($countries as $c): ?>
        <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $filters['country'] === $c ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" class="form-control" name="date_from" value="<?php echo htmlspecialchars($filters['date_from']); ?>" title="From date">
    </div>
    <div class="col-md-2">
      <input type="date" class="form-control" name="date_to" value="<?php echo htmlspecialchars($filters['date_to']); ?>" title="To date">
    </div>
    <div class="col-12 d-flex gap-2">
      <button class="btn btn-primary btn-sm">Search</button>
      <a href="reports.php" class="btn btn-outline-secondary btn-sm">Reset</a>
      <a href="reports-export.php?format=xlsx&<?php echo htmlspecialchars($queryString); ?>" class="btn btn-outline-success btn-sm ms-auto">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
      </a>
      <a href="reports-export.php?format=pdf&<?php echo htmlspecialchars($queryString); ?>" class="btn btn-outline-danger btn-sm">
        <i class="bi bi-file-earmark-pdf"></i> Export PDF
      </a>
    </div>
  </form>

  <div class="admin-sheet-wrap">
    <table class="admin-sheet" id="sheetTable">
      <thead>
        <tr class="col-letters">
          <th class="corner"></th>
          <th>A</th><th>B</th><th>C</th><th>D</th><th>E</th><th>F</th><th>G</th><th>H</th>
        </tr>
        <tr class="header-row">
          <th class="row-num">#</th>
          <th>Date Registered</th>
          <th>Full Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Country of Origin</th>
          <th>Purpose of Visit</th>
          <th>Status</th>
          <th>Scheduled/Appointment Date</th>
        </tr>
      </thead>
      <tbody>
        <?php $rowNum = 1; ?>
        <?php if (empty($groups)): ?>
        <tr><td colspan="9" class="text-center text-muted">No clients match this search.</td></tr>
        <?php endif; ?>
        <?php foreach ($groups as $date => $rows): ?>
        <tr class="date-group-row">
          <td class="row-num"></td>
          <td colspan="8"><?php echo htmlspecialchars(date('F j, Y', strtotime($date))); ?> (<?php echo count($rows); ?> client<?php echo count($rows) === 1 ? '' : 's'; ?>)</td>
        </tr>
        <?php foreach ($rows as $row): ?>
        <tr class="data-row" data-search="<?php echo htmlspecialchars(strtolower($row['full_name'] . ' ' . ($row['email'] ?? '') . ' ' . ($row['phone'] ?? '') . ' ' . ($row['country_of_origin'] ?? '') . ' ' . ($row['purpose_of_visit'] ?? ''))); ?>">
          <td class="row-num"><?php echo $rowNum++; ?></td>
          <td><?php echo htmlspecialchars($row['created_at']); ?></td>
          <td><?php echo htmlspecialchars($row['full_name']); ?></td>
          <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($row['country_of_origin'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars($row['purpose_of_visit'] ?? ''); ?></td>
          <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
          <td><?php echo htmlspecialchars($row['scheduled_date'] ?? ''); ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  // Lightweight client-side live filter over the rows the server already
  // rendered — narrows the current page instantly while typing. Hitting
  // Enter (or Search) still does a real page reload that re-runs the
  // server-side Client::search() query, which is what the export buttons use.
  (function () {
    var input = document.getElementById('liveSearch');
    var table = document.getElementById('sheetTable');
    if (!input || !table) return;

    input.addEventListener('input', function () {
      var term = input.value.trim().toLowerCase();
      var rows = table.querySelectorAll('tbody tr');
      var lastGroupVisible = null;
      var groupRow = null;

      rows.forEach(function (row) {
        if (row.classList.contains('date-group-row')) {
          groupRow = row;
          groupRow.hidden = false;
          lastGroupVisible = false;
          return;
        }
        if (!row.classList.contains('data-row')) return;
        var haystack = row.getAttribute('data-search') || '';
        var visible = term === '' || haystack.indexOf(term) !== -1;
        row.hidden = !visible;
        if (visible && groupRow) {
          lastGroupVisible = true;
          groupRow.hidden = false;
        }
        if (groupRow && !visible) {
          // leave group visibility decision until next row is checked
        }
      });

      // Second pass: hide date-group headers with no visible rows under them.
      var currentGroup = null;
      var groupHasVisible = false;
      var allRows = Array.prototype.slice.call(rows).concat([null]);
      allRows.forEach(function (row) {
        if (row === null || row.classList.contains('date-group-row')) {
          if (currentGroup) currentGroup.hidden = !groupHasVisible;
          currentGroup = row;
          groupHasVisible = false;
          return;
        }
        if (row.classList.contains('data-row') && !row.hidden) {
          groupHasVisible = true;
        }
      });
    });
  })();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
