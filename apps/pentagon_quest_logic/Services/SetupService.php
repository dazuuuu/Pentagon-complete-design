<?php

namespace App\Services;

use App\Core\Database;
use App\Helpers\Path;
use App\Models\Admin;
use PDO;
use PDOException;
use RuntimeException;

class SetupService
{
    public function lockPath(): string
    {
        return Path::logic('installed.lock');
    }

    public function isInstalled(): bool
    {
        return is_file($this->lockPath());
    }

    public function markInstalled(string $adminEmail = ''): void
    {
        $payload = json_encode([
            'installed_at' => date('c'),
            'admin_email' => $adminEmail,
        ], JSON_PRETTY_PRINT);
        file_put_contents($this->lockPath(), $payload ?: date('c'));
    }

    /**
     * @param array{host:string,port:int|string,name:string,user:string,pass:string} $db
     */
    public function writeEnv(array $db, array $admin = []): void
    {
        $existing = $this->existingEnv();

        $values = array_merge($existing, [
            'APP_ENV' => $existing['APP_ENV'] ?? 'local',
            'APP_URL' => $existing['APP_URL'] ?? 'http://localhost:8000',
            'DB_HOST' => $db['host'],
            'DB_PORT' => (string) $db['port'],
            'DB_NAME' => $db['name'],
            'DB_USER' => $db['user'],
            'DB_PASS' => $db['pass'],
        ]);

        if ($admin !== []) {
            $values['ADMIN_EMAIL'] = $admin['email'] ?? ($values['ADMIN_EMAIL'] ?? '');
            $values['ADMIN_NAME'] = $admin['name'] ?? ($values['ADMIN_NAME'] ?? '');
            if (!empty($admin['password'])) {
                $values['ADMIN_PASSWORD'] = $admin['password'];
            }
        }

        $order = [
            'APP_ENV', 'APP_URL',
            'DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS',
            'SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_PASS', 'SMTP_ENCRYPTION',
            'MAIL_FROM_EMAIL', 'MAIL_FROM_NAME', 'MAIL_ADMIN_EMAIL',
            'ADMIN_EMAIL', 'ADMIN_PASSWORD', 'ADMIN_NAME',
        ];

        $lines = [];
        foreach ($order as $key) {
            $lines[] = $this->envLine($key, (string) ($values[$key] ?? ''));
        }

        foreach ($values as $key => $value) {
            if (!in_array($key, $order, true)) {
                $lines[] = $this->envLine($key, (string) $value);
            }
        }

        if (file_put_contents(Path::env(), implode("\n", $lines) . "\n") === false) {
            throw new RuntimeException('Could not write .env. Check folder permissions on apps/pentagon_quest_logic/.');
        }

        foreach ($values as $key => $value) {
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv($key . '=' . $value);
        }

        Database::reset();
    }

    /**
     * Create the database if needed, then connect to it.
     *
     * @param array{host:string,port:int|string,name:string,user:string,pass:string} $db
     */
    public function prepareDatabase(array $db): void
    {
        $name = $this->safeDatabaseName($db['name']);
        $host = trim($db['host']);
        $port = (int) $db['port'];
        $user = $db['user'];
        $pass = $db['pass'];

        try {
            $server = new PDO(
                sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port),
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Could not connect to MySQL: ' . $e->getMessage());
        }

        $server->exec('CREATE DATABASE IF NOT EXISTS `' . $name . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $server = null;

        $this->writeEnv($db);
        Database::connection();
    }

    /**
     * @return list<string>
     */
    public function runMigrations(): array
    {
        $db = Database::connection();
        $updateService = new UpdateService();
        $updateService->ensureTrackingTables();

        $applied = [];
        $files = glob(Path::database('migrations') . DIRECTORY_SEPARATOR . '*.sql') ?: [];
        sort($files);

        foreach ($files as $file) {
            $filename = basename($file);
            if ($updateService->isMigrationApplied($filename)) {
                $applied[] = $filename . ' (already applied)';
                continue;
            }
            $sql = file_get_contents($file);
            if ($sql === false) {
                throw new RuntimeException('Could not read migration ' . $filename);
            }
            $db->exec($sql);
            $updateService->markMigrationApplied($filename);
            $applied[] = $filename;
        }

        return $applied;
    }

    public function seed(): void
    {
        $seedFile = Path::database('seeds', 'seed_initial_data.sql');
        if (!is_file($seedFile)) {
            return;
        }

        $db = Database::connection();
        $seedSql = file_get_contents($seedFile) ?: '';
        foreach (array_filter(array_map('trim', explode(';', $seedSql))) as $statement) {
            if ($statement === '' || str_starts_with($statement, '--')) {
                continue;
            }
            try {
                $db->exec($statement);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * @return list<array{filename: string, success: bool, message: string}>
     */
    public function runUpdates(): array
    {
        return (new UpdateService())->applyPending();
    }

    public function adminExists(): bool
    {
        try {
            $count = (int) Database::connection()->query('SELECT COUNT(*) FROM admins')->fetchColumn();
            return $count > 0;
        } catch (PDOException) {
            return false;
        }
    }

    public function createAdmin(string $name, string $email, string $password): void
    {
        $name = trim($name);
        $email = trim($email);
        if ($name === '' || $email === '' || $password === '') {
            throw new RuntimeException('Admin name, email, and password are required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('Please enter a valid admin email address.');
        }
        if (strlen($password) < 8) {
            throw new RuntimeException('Admin password must be at least 8 characters.');
        }

        $model = new Admin();
        $existing = $model->findByEmail($email);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($existing) {
            $stmt = Database::connection()->prepare('UPDATE admins SET name = ?, password_hash = ? WHERE id = ?');
            $stmt->execute([$name, $hash, $existing['id']]);
            return;
        }

        $model->create($name, $email, $hash);
    }

    /**
     * @return array<string, string>
     */
    public function existingEnv(): array
    {
        $path = Path::env();
        $source = is_file($path) ? $path : Path::logic('.env.example');
        if (!is_file($source)) {
            return [];
        }

        $parsed = [];
        foreach (file($source, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $parsed[trim($key)] = trim($value, " \t\"'");
        }

        return $parsed;
    }

    private function safeDatabaseName(string $name): string
    {
        $name = trim($name);
        if ($name === '' || !preg_match('/^[A-Za-z0-9_]+$/', $name)) {
            throw new RuntimeException('Database name may only contain letters, numbers, and underscores.');
        }
        return $name;
    }

    private function envLine(string $key, string $value): string
    {
        if ($value === '' || preg_match('/^[A-Za-z0-9_.\\/@:+-]*$/', $value)) {
            return $key . '=' . $value;
        }

        return $key . '="' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }
}
