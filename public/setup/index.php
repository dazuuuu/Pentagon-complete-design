<?php

require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Helpers\Path;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Services\SetupService;

$setup = new SetupService();
$base = Path::baseUrl();
$env = $setup->existingEnv();
$installed = $setup->isInstalled();

$step = $installed ? 'done' : (string) ($_POST['step'] ?? $_GET['step'] ?? 'database');
$error = '';
$log = [];

$dbDefaults = [
    'host' => $env['DB_HOST'] ?? 'localhost',
    'port' => $env['DB_PORT'] ?? '3306',
    'name' => $env['DB_NAME'] ?? 'pentagon_quest',
    'user' => $env['DB_USER'] ?? 'root',
    'pass' => $env['DB_PASS'] ?? '',
];

$adminDefaults = [
    'name' => $env['ADMIN_NAME'] ?? 'Site Admin',
    'email' => $env['ADMIN_EMAIL'] ?? 'admin@pentagonquest.com',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$installed) {
    if (!Session::verifyCsrf($_POST['_csrf'] ?? null)) {
        $error = 'Invalid security token. Refresh the page and try again.';
    } elseif ($step === 'database') {
        $dbDefaults = [
            'host' => trim((string) ($_POST['db_host'] ?? 'localhost')),
            'port' => trim((string) ($_POST['db_port'] ?? '3306')),
            'name' => trim((string) ($_POST['db_name'] ?? 'pentagon_quest')),
            'user' => trim((string) ($_POST['db_user'] ?? 'root')),
            'pass' => (string) ($_POST['db_pass'] ?? ''),
        ];

        try {
            $setup->prepareDatabase($dbDefaults);
            $log = $setup->runMigrations(true);
            $setup->seed();
            $setup->runUpdates();
            $step = 'admin';
        } catch (Throwable $e) {
            $error = $e->getMessage();
            $step = 'database';
        }
    } elseif ($step === 'admin') {
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
                $setup->createAdmin($adminDefaults['name'], $adminDefaults['email'], $password);
                $setup->writeEnv([
                    'host' => $_ENV['DB_HOST'] ?? $dbDefaults['host'],
                    'port' => $_ENV['DB_PORT'] ?? $dbDefaults['port'],
                    'name' => $_ENV['DB_NAME'] ?? $dbDefaults['name'],
                    'user' => $_ENV['DB_USER'] ?? $dbDefaults['user'],
                    'pass' => $_ENV['DB_PASS'] ?? $dbDefaults['pass'],
                ], [
                    'name' => $adminDefaults['name'],
                    'email' => $adminDefaults['email'],
                    'password' => $password,
                ]);
                $setup->markInstalled($adminDefaults['email']);
                $installed = true;
                $step = 'done';
            } catch (Throwable $e) {
                $error = $e->getMessage();
                $step = 'admin';
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
  <title>Setup | Pentagon Quest</title>
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
    <div class="tag">Pentagon Quest</div>
    <h1><?php echo $step === 'done' ? 'Setup complete' : 'Install the website'; ?></h1>
    <p class="lead">
      <?php if ($step === 'database'): ?>
        Connect MySQL, then we will rebuild the schema from required_migrations (this replaces existing Pentagon Quest tables in that database) and seed the site content.
      <?php elseif ($step === 'admin'): ?>
        Migrations finished. Create the administrator account used at <code>/admin/</code>.
      <?php else: ?>
        The database is ready. You can open the public site or the admin portal.
      <?php endif; ?>
    </p>

    <div class="steps">
      <span class="on"></span>
      <span class="<?php echo in_array($step, ['admin', 'done'], true) ? 'on' : ''; ?>"></span>
      <span class="<?php echo $step === 'done' ? 'on' : ''; ?>"></span>
    </div>

    <?php if ($error): ?>
      <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($step === 'database'): ?>
      <form method="POST">
        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(Session::csrfToken()); ?>">
        <input type="hidden" name="step" value="database">
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
        <button class="btn" type="submit">Run migrations</button>
      </form>
    <?php elseif ($step === 'admin'): ?>
      <?php if ($log): ?>
        <div class="ok">Migrations applied.</div>
        <div class="log"><?php echo htmlspecialchars(implode("\n", $log)); ?></div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars(Session::csrfToken()); ?>">
        <input type="hidden" name="step" value="admin">
        <label>Admin name</label>
        <input type="text" name="admin_name" value="<?php echo htmlspecialchars($adminDefaults['name']); ?>" required>
        <label>Admin email</label>
        <input type="email" name="admin_email" value="<?php echo htmlspecialchars($adminDefaults['email']); ?>" required>
        <label>Password (min 8 characters)</label>
        <input type="password" name="admin_password" required minlength="8">
        <label>Confirm password</label>
        <input type="password" name="admin_password_confirm" required minlength="8">
        <button class="btn" type="submit">Create admin and open site</button>
      </form>
    <?php else: ?>
      <div class="ok">Pentagon Quest is installed. Setup is locked so it cannot be run again unless you delete <code>apps/pentagon_quest_logic/installed.lock</code>.</div>
      <div class="actions">
        <a class="btn" href="<?php echo htmlspecialchars($base); ?>">Open the website</a>
        <a class="btn secondary" href="<?php echo htmlspecialchars($base); ?>admin/login.php">Open admin login</a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
