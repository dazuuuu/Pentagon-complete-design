#!/usr/bin/env php
<?php

/**
 * Run all database migrations and seed data.
 * Usage: php apps/database/migrate.php
 */

use App\Core\Database;
use App\Helpers\Path;
use Dotenv\Dotenv;
use PDOException;

require_once Path::vendor('autoload.php');

if (file_exists(Path::env())) {
    Dotenv::createImmutable(Path::root())->safeLoad();
}

echo "Running migrations...\n";

$db = Database::connection();
$migrationDir = Path::database('migrations');
$files = glob($migrationDir . DIRECTORY_SEPARATOR . '*.sql');
sort($files);

foreach ($files as $file) {
    $sql = file_get_contents($file);
    $db->exec($sql);
    echo '  ✓ ' . basename($file) . "\n";
}

$seedFile = Path::database('seeds', 'seed_initial_data.sql');
if (file_exists($seedFile)) {
    echo "Seeding data...\n";
    $seedSql = file_get_contents($seedFile);
    foreach (array_filter(array_map('trim', explode(';', $seedSql))) as $statement) {
        if ($statement !== '') {
            try {
                $db->exec($statement);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate') === false) {
                    echo '  ! Seed warning: ' . $e->getMessage() . "\n";
                }
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

echo "Done.\n";
