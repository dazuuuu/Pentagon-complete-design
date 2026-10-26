<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\SetupService;
use App\Services\UpdateService;

Auth::requireAuth();

$pageTitle = 'Updates';
$currentAdminPage = 'updates';
$flash = '';
$flashType = 'success';
$results = [];
$migrationsFlash = '';
$migrationsFlashType = 'success';
$migrationsApplied = [];

$service = new UpdateService();
$setupService = new SetupService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::verifyCsrf($_POST['_csrf'] ?? null)) {
        $flash = 'Invalid security token. Please try again.';
        $flashType = 'danger';
    } else {
        $action = $_POST['action'] ?? 'apply_pending';
        if ($action === 'run_migrations') {
            try {
                $migrationsApplied = array_values(array_filter(
                    $setupService->runMigrations(false),
                    static fn (string $entry): bool => !str_ends_with($entry, '(already applied)')
                ));
                $migrationsFlash = $migrationsApplied === []
                    ? 'No pending database migrations to run.'
                    : count($migrationsApplied) . ' migration(s) applied successfully.';
            } catch (\Throwable $e) {
                $migrationsFlashType = 'danger';
                $migrationsFlash = 'Migration run failed: ' . $e->getMessage();
            }
        } elseif ($action === 'apply_one') {
            $results = [$service->applyOne((string) ($_POST['filename'] ?? ''))];
        } else {
            $results = $service->applyPending();
        }

        if ($action !== 'run_migrations') {
            if ($results === []) {
                $flash = 'No pending updates to run.';
            } else {
                $failed = array_filter($results, static fn (array $r): bool => !$r['success']);
                $flashType = $failed ? 'danger' : 'success';
                $flash = $failed
                    ? count($failed) . ' update(s) failed. See details below.'
                    : count($results) . ' update(s) applied successfully.';
            }
        }
    }
}

$migrationsCatalog = [];
$migrationsCatalogError = '';
try {
    $migrationsCatalog = $setupService->requiredMigrationsCatalog();
} catch (\Throwable $e) {
    $migrationsCatalogError = 'Could not read migrations: ' . $e->getMessage();
}
$pendingMigrationsCount = count(array_filter($migrationsCatalog, static fn (array $i): bool => $i['pending']));

$catalog = [];
$catalogError = '';
try {
    $catalog = $service->catalog();
} catch (Throwable $e) {
    $catalogError = 'Could not read updates: ' . $e->getMessage();
}

$pendingCount = count(array_filter($catalog, static fn (array $i): bool => $i['pending']));

include __DIR__ . '/includes/header.php';
?>

<?php if ($flash): ?>
  <div class="alert alert-<?php echo $flashType; ?>"><?php echo htmlspecialchars($flash); ?></div>
<?php endif; ?>

<?php if ($catalogError): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($catalogError); ?></div>
<?php endif; ?>

<?php if ($migrationsFlash): ?>
  <div class="alert alert-<?php echo $migrationsFlashType; ?>"><?php echo htmlspecialchars($migrationsFlash); ?></div>
<?php endif; ?>

<?php if ($migrationsCatalogError): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($migrationsCatalogError); ?></div>
<?php endif; ?>

<div class="content-card p-4 mb-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">Database migrations</h5>
      <p class="text-muted mb-0" style="max-width: 640px;">
        Creates any tables added by new features (e.g. <code>site_settings</code>, <code>link_previews</code>, <code>story_media</code>)
        by running the SQL files in <code>database/required_migrations/</code> that haven't been applied to this database yet.
        Safe to run any time — already-applied files are skipped.
      </p>
    </div>
    <form method="POST">
      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(Session::csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="action" value="run_migrations">
      <button class="btn btn-success" type="submit" <?php echo $pendingMigrationsCount ? '' : 'disabled'; ?>>
        Run <?php echo $pendingMigrationsCount; ?> pending migration<?php echo $pendingMigrationsCount === 1 ? '' : 's'; ?>
      </button>
    </form>
  </div>

  <?php if ($migrationsApplied): ?>
  <div class="mt-3">
    <div class="small text-muted mb-1">Applied:</div>
    <ul class="small mb-0">
      <?php foreach ($migrationsApplied as $filename): ?>
      <li><code><?php echo htmlspecialchars($filename); ?></code></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <?php if ($migrationsCatalog): ?>
  <div class="table-responsive mt-3">
    <table class="table table-sm align-middle mb-0">
      <thead><tr><th>File</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($migrationsCatalog as $item): ?>
        <tr>
          <td><code><?php echo htmlspecialchars($item['filename']); ?></code></td>
          <td>
            <?php if ($item['pending']): ?>
              <span class="badge text-bg-warning">Pending</span>
            <?php else: ?>
              <span class="badge text-bg-success">Applied</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<div class="content-card p-4 mb-4">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
    <div>
      <h5 class="mb-1">Schema &amp; content updates</h5>
      <p class="text-muted mb-0" style="max-width: 640px;">
        Drop numbered <code>.sql</code> or <code>.php</code> files into
        <code>apps/pentagon_quest_logic/updates/</code>, then run them here.
        Applied files are recorded and will not run again.
      </p>
    </div>
    <form method="POST">
      <?php echo '<input type="hidden" name="_csrf" value="' . htmlspecialchars(Session::csrfToken(), ENT_QUOTES, 'UTF-8') . '">'; ?>
      <input type="hidden" name="action" value="apply_pending">
      <button class="btn btn-success" type="submit" <?php echo $pendingCount ? '' : 'disabled'; ?>>
        Run <?php echo $pendingCount; ?> pending update<?php echo $pendingCount === 1 ? '' : 's'; ?>
      </button>
    </form>
  </div>
</div>

<div class="content-card p-4">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>File</th>
          <th>Type</th>
          <th>Status</th>
          <th>Applied</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$catalog): ?>
        <tr>
          <td colspan="5" class="text-muted">
            No update files found. Push files named like <code>001_add_column.sql</code> into the updates folder.
          </td>
        </tr>
        <?php endif; ?>
        <?php foreach ($catalog as $item): ?>
        <tr>
          <td><code><?php echo htmlspecialchars($item['filename']); ?></code></td>
          <td><?php echo htmlspecialchars(strtoupper($item['type'])); ?></td>
          <td>
            <?php if (!$item['pending']): ?>
              <span class="badge text-bg-success">Applied</span>
            <?php elseif (!empty($item['error_message'])): ?>
              <span class="badge text-bg-danger">Failed</span>
            <?php else: ?>
              <span class="badge text-bg-warning">Pending</span>
            <?php endif; ?>
            <?php if (!empty($item['error_message'])): ?>
              <div class="small text-danger mt-1"><?php echo htmlspecialchars($item['error_message']); ?></div>
            <?php endif; ?>
          </td>
          <td><?php echo htmlspecialchars($item['applied_at'] ?? '—'); ?></td>
          <td class="text-end">
            <?php if ($item['pending']): ?>
            <form method="POST" class="d-inline">
              <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(Session::csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
              <input type="hidden" name="action" value="apply_one">
              <input type="hidden" name="filename" value="<?php echo htmlspecialchars($item['filename']); ?>">
              <button class="btn btn-sm btn-outline-success" type="submit">Run</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
