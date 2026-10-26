<?php

/**
 * No-login database diagnostic + migration runner.
 *
 * Use this when the admin panel itself is broken (e.g. wrong DB credentials
 * causing a 500), so you don't need a working admin login to fix the DB.
 *
 * Visit: https://yoursite.com/migrate.php?key=<config/app.php 'migrate_key'>
 * Change that key in apps/pentagon_quest_logic/config/app.php before hosting.
 */

require_once __DIR__ . '/includes/bootstrap.php';

use App\Core\Database;
use App\Helpers\Path;
use App\Services\SetupService;

$appConfig = require Path::config('app.php');
$requiredKey = (string) ($appConfig['migrate_key'] ?? '');
$providedKey = (string) ($_GET['key'] ?? $_POST['key'] ?? '');

$authorized = $requiredKey !== '' && hash_equals($requiredKey, $providedKey);

header('Content-Type: text/html; charset=UTF-8');

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Pentagon Safaris — DB Diagnostic</title>';
echo '<style>body{font-family:ui-monospace,Menlo,Consolas,monospace;background:#111;color:#eee;padding:32px;max-width:820px;margin:0 auto;line-height:1.6}
h1{font-size:1.3rem} .ok{color:#6fdc8c} .fail{color:#ff7a7a} .box{background:#1c1c1c;border:1px solid #333;border-radius:8px;padding:16px;margin:16px 0}
code{background:#252525;padding:2px 6px;border-radius:4px} button{background:#D4AF37;color:#111;border:0;padding:10px 18px;border-radius:6px;font-weight:700;cursor:pointer}
table{border-collapse:collapse;width:100%} td,th{border:1px solid #333;padding:6px 10px;text-align:left;font-size:.9rem}</style></head><body>';
echo '<h1>Pentagon Safaris — Database Diagnostic</h1>';

if (!$authorized) {
    echo '<div class="box"><p class="fail">Missing or incorrect <code>?key=</code>.</p>';
    echo '<p>Open <code>apps/pentagon_quest_logic/config/app.php</code> on the server, find <code>migrate_key</code>, and visit this page with <code>?key=THAT_VALUE</code> in the URL.</p></div>';
    echo '</body></html>';
    exit;
}

// --- 1. Show what config/database.php currently points at (no password shown) ---
$dbConfig = require Path::config('database.php');
echo '<div class="box"><strong>Current config/database.php</strong><table>';
foreach (['host', 'port', 'name', 'user'] as $key) {
    echo '<tr><td>' . h($key) . '</td><td>' . h((string) ($dbConfig[$key] ?? '')) . '</td></tr>';
}
echo '<tr><td>pass</td><td>' . (($dbConfig['pass'] ?? '') === '' ? '<em>(empty)</em>' : '(set, ' . strlen((string) $dbConfig['pass']) . ' characters)') . '</td></tr>';
echo '</table></div>';

// --- 2. Test the actual connection and show the real error if it fails ---
echo '<div class="box"><strong>Connection test</strong><br>';
$pdo = null;
try {
    $pdo = Database::connection();
    echo '<p class="ok">Connected successfully.</p>';
    $dbNameRow = $pdo->query('SELECT DATABASE() AS db')->fetch();
    echo '<p>Connected to database: <code>' . h((string) ($dbNameRow['db'] ?? '?')) . '</code></p>';
} catch (\Throwable $e) {
    echo '<p class="fail">Connection failed: ' . h($e->getMessage()) . '</p>';
    echo '<p>This is almost always wrong host/port/database name/user/password in <code>config/database.php</code>, or the database user not having access from this server. Fix those values, then reload this page.</p>';
}
echo '</div>';

if ($pdo === null) {
    echo '</body></html>';
    exit;
}

// --- 3. List tables that exist, so you can see if the schema is empty ---
try {
    $tables = $pdo->query('SHOW TABLES')->fetchAll(\PDO::FETCH_COLUMN);
    echo '<div class="box"><strong>Tables currently in this database (' . count($tables) . ')</strong><br>';
    echo $tables === [] ? '<p class="fail">None — this database is empty. Run migrations below.</p>' : '<p>' . h(implode(', ', $tables)) . '</p>';
    echo '</div>';
} catch (\Throwable $e) {
    echo '<div class="box"><p class="fail">Could not list tables: ' . h($e->getMessage()) . '</p></div>';
}

// --- 3b. Row counts for key content tables, so you can see at a glance
//         whether seeding/admin data actually made it in ---
try {
    $contentTables = ['blog_posts', 'experiences', 'destinations', 'tours', 'testimonials', 'offers', 'admins'];
    echo '<div class="box"><strong>Row counts</strong><table>';
    foreach ($contentTables as $table) {
        if (!in_array($table, $tables ?? [], true)) {
            echo '<tr><td>' . h($table) . '</td><td class="fail">table missing</td></tr>';
            continue;
        }
        $count = (int) $pdo->query('SELECT COUNT(*) FROM `' . $table . '`')->fetchColumn();
        echo '<tr><td>' . h($table) . '</td><td class="' . ($count > 0 ? 'ok' : 'fail') . '">' . $count . '</td></tr>';
    }
    echo '</table></div>';
} catch (\Throwable $e) {
    echo '<div class="box"><p class="fail">Could not count rows: ' . h($e->getMessage()) . '</p></div>';
}

// --- 4. Run pending migrations on request ---
$setup = new SetupService();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'run_migrations') {
    echo '<div class="box"><strong>Running pending migrations…</strong><br>';
    try {
        $applied = $setup->runMigrations(false);
        $newlyApplied = array_values(array_filter($applied, static fn (string $e): bool => !str_ends_with($e, '(already applied)')));
        if ($newlyApplied === []) {
            echo '<p>No pending migrations — everything is already applied.</p>';
        } else {
            echo '<p class="ok">Applied ' . count($newlyApplied) . ' migration(s):</p><ul>';
            foreach ($newlyApplied as $file) {
                echo '<li>' . h($file) . '</li>';
            }
            echo '</ul>';
        }
    } catch (\Throwable $e) {
        echo '<p class="fail">Migration run failed: ' . h($e->getMessage()) . '</p>';
    }
    echo '</div>';
}

try {
    $catalog = $setup->requiredMigrationsCatalog();
    $pending = array_values(array_filter($catalog, static fn (array $i): bool => $i['pending']));
    echo '<div class="box"><strong>Migration files (' . count($catalog) . ' total, ' . count($pending) . ' pending)</strong>';
    if ($pending !== []) {
        echo '<ul>';
        foreach ($pending as $item) {
            echo '<li>' . h($item['filename']) . '</li>';
        }
        echo '</ul>';
        echo '<form method="POST"><input type="hidden" name="key" value="' . h($providedKey) . '"><input type="hidden" name="action" value="run_migrations"><button type="submit">Run ' . count($pending) . ' pending migration(s)</button></form>';
    } else {
        echo '<p class="ok">Nothing pending.</p>';
    }
    echo '</div>';
} catch (\Throwable $e) {
    echo '<div class="box"><p class="fail">Could not read migrations: ' . h($e->getMessage()) . '</p></div>';
}

echo '<p style="color:#888;font-size:.85rem">Once the admin panel works again, remove or rename this file — it does not require login, only the key above.</p>';
echo '</body></html>';
