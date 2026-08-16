#!/usr/bin/env php
<?php

/**
 * Run all database migrations, seed data, then pending updates.
 * Usage: php apps/pentagon_quest_logic/database/migrate.php
 */

use App\Helpers\Path;
use App\Services\SetupService;
use Dotenv\Dotenv;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(Path::env())) {
    Dotenv::createImmutable(Path::logic())->safeLoad();
}

$setup = new SetupService();

echo "Running migrations...\n";
foreach ($setup->runMigrations() as $file) {
    echo "  ✓ {$file}\n";
}

echo "Seeding data...\n";
$setup->seed();
echo "  ✓ seed_initial_data.sql\n";

$adminEmail = $_ENV['ADMIN_EMAIL'] ?? 'admin@pentagonquest.com';
$adminPassword = $_ENV['ADMIN_PASSWORD'] ?? 'admin123';
$adminName = $_ENV['ADMIN_NAME'] ?? 'Site Admin';
$setup->createAdmin($adminName, $adminEmail, $adminPassword);
echo "  ✓ Admin ready ({$adminEmail})\n";

echo "Running pending updates...\n";
$results = $setup->runUpdates();
if ($results === []) {
    echo "  – none pending\n";
} else {
    foreach ($results as $result) {
        $mark = $result['success'] ? '✓' : '!';
        echo "  {$mark} {$result['filename']}" . ($result['success'] ? '' : ': ' . $result['message']) . "\n";
    }
}

$setup->markInstalled($adminEmail);
echo "Done.\n";
