#!/usr/bin/env php
<?php

/**
 * Run baseline database migrations, seed data, then pending updates.
 * Usage: php apps/pentagon_quest_logic/database/migrate.php
 */

use App\Core\Database;
use App\Helpers\Path;
use App\Services\UpdateService;
use Dotenv\Dotenv;
use PDOException;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(Path::env())) {
    Dotenv::createImmutable(Path::logic())->safeLoad();
}

echo "Running migrations...\n";

$db = Database::connection();
$updateService = new UpdateService();
$updateService->ensureTrackingTables();

$migrationDir = Path::database('migrations');
$files = glob($migrationDir . DIRECTORY_SEPARATOR . '*.sql') ?: [];
sort($files);

foreach ($files as $file) {
    $filename = basename($file);
    if ($updateService->isMigrationApplied($filename)) {
        echo "  – {$filename} (already applied)\n";
        continue;
    }

    try {
        $sql = file_get_contents($file);
        $db->exec($sql);
        $updateService->markMigrationApplied($filename);
        echo "  ✓ {$filename}\n";
    } catch (PDOException $e) {
        echo "  ! {$filename}: " . $e->getMessage() . "\n";
        throw $e;
    }
}

$seedFile = Path::database('seeds', 'seed_initial_data.sql');
if (file_exists($seedFile)) {
    echo "Seeding data...\n";
    $seedSql = file_get_contents($seedFile);
    foreach (array_filter(array_map('trim', explode(';', $seedSql))) as $statement) {
        if ($statement === '' || str_starts_with($statement, '--')) {
            continue;
        }
        try {
            $db->exec($statement);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate') === false) {
                echo '  ! Seed warning: ' . $e->getMessage() . "\n";
            }
        }
    }
    echo "  ✓ seed_initial_data.sql\n";
}

$adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'admin@pentagonquest.com';
$adminPassword = $_ENV['ADMIN_PASSWORD'] ?? 'admin123';
$adminName = $_ENV['ADMIN_NAME'] ?? 'Site Admin';
$adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);

$stmt = $db->prepare('SELECT id FROM admins WHERE email = ? LIMIT 1');
$stmt->execute([$adminEmail]);
if (!$stmt->fetch()) {
    $insert = $db->prepare('INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)');
    $insert->execute([$adminName, $adminEmail, $adminHash]);
    echo "  ✓ Default admin created ({$adminEmail})\n";
}

echo "Running pending updates...\n";
$results = $updateService->applyPending();
if ($results === []) {
    echo "  – none pending\n";
} else {
    foreach ($results as $result) {
        $mark = $result['success'] ? '✓' : '!';
        echo "  {$mark} {$result['filename']}" . ($result['success'] ? '' : ': ' . $result['message']) . "\n";
    }
}

echo "Done.\n";
