<?php

require_once __DIR__ . '/../includes/bootstrap.php';

use App\Core\Auth;
use App\Helpers\Session;
use App\Services\SiteSettingsService;

Auth::requireAuth();
Session::start();

$service = new SiteSettingsService();
$message = Session::flash('message');
$error = Session::flash('error');

$socialKeys = [
  'social_instagram' => 'Instagram URL',
  'social_facebook' => 'Facebook URL',
  'social_tiktok' => 'TikTok URL',
  'social_youtube' => 'YouTube URL',
  'social_linkedin' => 'LinkedIn URL',
  'social_google_profile' => 'Google Business Profile URL',
  'social_whatsapp' => 'WhatsApp phone number (e.g. 254718620982)',
  'social_phone' => 'Phone number (e.g. 254718620982)',
  'social_email' => 'Contact email address',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Session::verifyCsrf($_POST['csrf'] ?? '')) {
  try {
    foreach (array_keys($socialKeys) as $key) {
      $service->set($key, trim($_POST[$key] ?? ''));
    }
    Session::flash('message', 'Settings saved.');
  } catch (\RuntimeException $e) {
    Session::flash('error', $e->getMessage());
  }

  header('Location: settings.php');
  exit;
}

$values = $service->getAll();
$pageTitle = 'Site Settings';
$currentAdminPage = 'settings';
include __DIR__ . '/includes/header.php';
?>
<div class="content-card p-4">
  <h2 class="mb-3">Social Links &amp; Contact</h2>
  <p class="text-muted small">These values power the social icons shown in the site footer. Leave a field blank to hide that icon.</p>
  <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

  <form method="POST" class="row g-3">
    <input type="hidden" name="csrf" value="<?php echo Session::csrfToken(); ?>">
    <?php foreach ($socialKeys as $key => $label): ?>
    <div class="col-md-6">
      <label class="form-label small text-muted mb-1"><?php echo htmlspecialchars($label); ?></label>
      <input class="form-control" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($values[$key] ?? ''); ?>">
    </div>
    <?php endforeach; ?>
    <div class="col-12">
      <button class="btn btn-success">Save Settings</button>
    </div>
  </form>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
