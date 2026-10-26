<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Helpers\Path;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Core\Auth;
use App\Services\SetupService;

$setup = new SetupService();
$base = Path::baseUrl();
$currentDb = require Path::config('database.php');
$installed = $setup->isInstalled();

$step = $installed ? 'done' : 'install';
$error = '';
$log = [];

$dbDefaults = [
    'host' => $currentDb['host'] ?? 'localhost',
    'port' => (string) ($currentDb['port'] ?? '3306'),
    'name' => $currentDb['name'] ?? 'pentagon_quest',
    'user' => $currentDb['user'] ?? 'root',
    'pass' => $currentDb['pass'] ?? '',
];

$adminDefaults = $setup->defaultAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$installed) {
    if (!Session::verifyCsrf($_POST['_csrf'] ?? null)) {
        $error = 'Invalid security token. Refresh the page and try again.';
    } else {
        $dbDefaults = [
            'host' => trim((string) ($_POST['db_host'] ?? 'localhost')),
            'port' => trim((string) ($_POST['db_port'] ?? '3306')),
            'name' => trim((string) ($_POST['db_name'] ?? 'pentagon_quest')),
            'user' => trim((string) ($_POST['db_user'] ?? 'root')),
            'pass' => (string) ($_POST['db_pass'] ?? ''),
        ];

        $adminDefaults['name'] = trim((string) ($_POST['admin_name'] ?? ''));
        $adminDefaults['email'] = trim((string) ($_POST['admin_email'] ?? ''));
        $password = (string) ($_POST['admin_password'] ?? '');
        $confirm = (string) ($_POST['admin_password_confirm'] ?? '');

        if ($password !== $confirm) {
            $error = 'Password confirmation does not match.';
        } elseif (!Validator::email($adminDefaults['email'])) {
            $error = 'Please enter a valid admin email address.';
        } else {
            try {
                $setup->prepareDatabase($dbDefaults);
                $log = $setup->runMigrations(true);
                $setup->seed();
                $setup->runUpdates();
                $admin = $setup->createAdmin($adminDefaults['name'], $adminDefaults['email'], $password);
                $setup->markInstalled($admin['email']);
                Auth::login($admin['id'], $admin['name']);
                header('Location: ' . pq_url('admin/dashboard.php'));
                exit;
            } catch (Throwable $e) {
                $error = $e->getMessage();
                $step = 'install';
            }
        }
    }
}

if ($installed) {
    $step = 'done';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup | Pentagon Safaris</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
  <link rel="icon" type="image/svg+xml" href="<?php echo htmlspecialchars($base); ?>assets/svgs/favicon.svg">
  <style>
    :root {
      --gold: #D4AF37;
      --green: #1B3022;
      --charcoal: #121212;
      --sand: #F4F1EA;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      font-family: 'Plus Jakarta Sans', sans-serif;
      background: var(--charcoal);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 32px 16px;
    }
    .card {
      width: 100%;
      max-width: 560px;
      background: #1a1a1a;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 20px;
      padding: 36px;
      box-shadow: 0 30px 80px rgba(0,0,0,.35);
    }
    h1 { font-family: 'Space Grotesk', sans-serif; margin: 0 0 8px; font-size: 1.8rem; }
    .tag { color: var(--gold); font-size: .75rem; letter-spacing: .18em; text-transform: uppercase; font-weight: 700; }
    p.lead { color: rgba(255,255,255,.65); margin: 0 0 28px; }
    label { display: block; font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin: 0 0 8px; }
    input {
      width: 100%;
      padding: 12px 16px;
      border-radius: 30px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.04);
      color: #fff;
      margin-bottom: 16px;
      outline: none;
    }
    input:focus { border-color: var(--gold); }
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 14px 24px;
      border: 0;
      border-radius: 40px;
      background: var(--gold);
      color: var(--charcoal);
      font-weight: 700;
      cursor: pointer;
      text-decoration: none;
      margin-top: 8px;
    }
    .btn.secondary { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,.2); }
    .error { background: #8a1f1f; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; font-size: .95rem; }
    .ok { background: var(--green); padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; }
    .steps { display: flex; gap: 8px; margin: 0 0 24px; }
    .steps span { flex: 1; height: 4px; border-radius: 4px; background: rgba(255,255,255,.12); }
    .steps span.on { background: var(--gold); }
    .log { font-size: .8rem; color: rgba(255,255,255,.55); max-height: 120px; overflow: auto; margin-bottom: 16px; }
    .actions { display: grid; gap: 10px; margin-top: 16px; }
  </style>
</head>
<body>
  <div class="card">
    <div class="tag">Pentagon Safaris</div>
    <h1><?php echo $step === 'done' ? 'Setup complete' : 'Install the website'; ?></h1>
    <p class="lead">
      <?php if ($step === 'install'): ?>
        Enter your hosting database credentials and first admin account. Setup will run required migrations, seed content, apply updates, create the admin, then log you into <code>/admin/</code>.
      <?php else: ?>
        The database is ready. You can open the public site or the admin portal.
      <?php endif; ?>
    </p>

    <div class="steps">
      <span class="on"></span>
      <span class="<?php echo $step === 'done' ? 'on' : ''; ?>"></span>
      <span class="<?php echo $step === 'done' ? 'on' : ''; ?>"></span>
    </div>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($step === 'install'): ?>
      <form method="POST">
        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(Session::csrfToken()); ?>">
        <label>Database host</label>
        <input type="text" name="db_host" value="<?php echo htmlspecialchars($dbDefaults['host']); ?>" required>
        <div class="row">
          <div>
            <label>Port</label>
            <input type="text" name="db_port" value="<?php echo htmlspecialchars((string) $dbDefaults['port']); ?>" required>
          </div>
          <div>
            <label>Database name</label>
            <input type="text" name="db_name" value="<?php echo htmlspecialchars($dbDefaults['name']); ?>" required>
          </div>
        </div>
        <label>Username</label>
        <input type="text" name="db_user" value="<?php echo htmlspecialchars($dbDefaults['user']); ?>" required>
        <label>Password</label>
        <input type="password" name="db_pass" value="<?php echo htmlspecialchars($dbDefaults['pass']); ?>">
        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,.1); margin: 14px 0 22px;">
        <label>Admin name</label>
        <input type="text" name="admin_name" value="<?php echo htmlspecialchars($adminDefaults['name']); ?>" required>
        <label>Admin email</label>
        <input type="email" name="admin_email" value="<?php echo htmlspecialchars($adminDefaults['email']); ?>" required>
        <label>Password (min 8 characters)</label>
        <input type="password" name="admin_password" required minlength="8">
        <label>Confirm password</label>
        <input type="password" name="admin_password_confirm" required minlength="8">
        <button class="btn" type="submit">Install and open admin</button>
      </form>
    <?php else: ?>
      <div class="ok">Pentagon Safaris is installed. Setup is locked so it cannot be run again unless you delete <code>apps/pentagon_quest_logic/installed.lock</code>.</div>
      <div class="actions">
        <a class="btn" href="<?php echo htmlspecialchars($base); ?>">Open the website</a>
        <a class="btn secondary" href="<?php echo htmlspecialchars(pq_url('admin/dashboard.php')); ?>">Open admin dashboard</a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
