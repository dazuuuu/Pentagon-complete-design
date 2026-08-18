<?php

namespace App\Services;

use App\Core\Database;
use App\Helpers\Path;
use PDO;
use RuntimeException;
use Throwable;

/**
 * Applies incremental SQL/PHP patches dropped into
 * apps/pentagon_quest_logic/updates/ and records them so they run once.
 */
class UpdateService
{
    private const FILENAME_PATTERN = '/^[0-9]{3,}[a-zA-Z0-9_\-]*\.(sql|php)$/';

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function ensureTrackingTables(): void
    {
        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                filename VARCHAR(255) NOT NULL PRIMARY KEY,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        $this->db->exec(
            'CREATE TABLE IF NOT EXISTS schema_updates (
                filename VARCHAR(255) NOT NULL PRIMARY KEY,
                applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                status VARCHAR(20) NOT NULL DEFAULT \'applied\',
                error_message TEXT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function isMigrationApplied(string $filename): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM schema_migrations WHERE filename = ? LIMIT 1');
        $stmt->execute([$filename]);
        return (bool) $stmt->fetchColumn();
    }

    public function markMigrationApplied(string $filename): void
    {
        $stmt = $this->db->prepare('INSERT IGNORE INTO schema_migrations (filename) VALUES (?)');
        $stmt->execute([$filename]);
    }

    /**
     * @return list<array{filename: string, type: string, pending: bool, applied_at: ?string, error_message: ?string}>
     */
    public function catalog(): array
    {
        $this->ensureTrackingTables();

        $applied = [];
        foreach ($this->db->query('SELECT filename, applied_at, status, error_message FROM schema_updates')->fetchAll() as $row) {
            $applied[$row['filename']] = $row;
        }

        $items = [];
        foreach ($this->discoverFiles() as $filename) {
            $row = $applied[$filename] ?? null;
            $items[] = [
                'filename' => $filename,
                'type' => strtolower(pathinfo($filename, PATHINFO_EXTENSION)),
                'pending' => !$row || $row['status'] !== 'applied',
                'applied_at' => $row['applied_at'] ?? null,
                'error_message' => $row['error_message'] ?? null,
            ];
        }

        return $items;
    }

    /**
     * @return list<array{filename: string, success: bool, message: string}>
     */
    public function applyPending(): array
    {
        $results = [];
        foreach ($this->catalog() as $item) {
            if ($item['pending']) {
                $results[] = $this->applyOne($item['filename']);
            }
        }
        return $results;
    }

    /**
     * @return array{filename: string, success: bool, message: string}
     */
    public function applyOne(string $filename): array
    {
        $this->ensureTrackingTables();
        $filename = basename($filename);

        if (!preg_match(self::FILENAME_PATTERN, $filename)) {
            return ['filename' => $filename, 'success' => false, 'message' => 'Invalid update filename.'];
        }

        $path = Path::updates($filename);
        if (!is_file($path)) {
            return ['filename' => $filename, 'success' => false, 'message' => 'Update file not found.'];
        }

        try {
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($extension === 'sql') {
                $this->runSqlFile($path);
            } else {
                $this->runPhpFile($path);
            }

            $this->record($filename, 'applied', null);
            return ['filename' => $filename, 'success' => true, 'message' => 'Applied.'];
        } catch (Throwable $e) {
            $this->record($filename, 'failed', $e->getMessage());
            return ['filename' => $filename, 'success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @return list<string>
     */
    private function discoverFiles(): array
    {
        $dir = Path::updates();
        if (!is_dir($dir)) {
            return [];
        }

        $files = [];
        foreach (scandir($dir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (preg_match(self::FILENAME_PATTERN, $entry) && is_file($dir . DIRECTORY_SEPARATOR . $entry)) {
                $files[] = $entry;
            }
        }

        sort($files, SORT_NATURAL);
        return $files;
    }

    private function runSqlFile(string $path): void
    {
        $sql = file_get_contents($path);
        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException('Update SQL file is empty.');
        }
        $this->db->exec($sql);
    }

    private function runPhpFile(string $path): void
    {
        $callback = require $path;
        if (!is_callable($callback)) {
            throw new RuntimeException('PHP updates must return a callable: function (PDO $db): void');
        }
        $callback($this->db);
    }

    private function record(string $filename, string $status, ?string $error): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO schema_updates (filename, status, error_message)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE status = VALUES(status), error_message = VALUES(error_message), applied_at = CURRENT_TIMESTAMP'
        );
        $stmt->execute([$filename, $status, $error]);
    }
}
