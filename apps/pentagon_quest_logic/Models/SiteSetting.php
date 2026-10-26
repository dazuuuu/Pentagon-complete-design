<?php

namespace App\Models;

class SiteSetting extends BaseModel
{
    public function get(string $key): ?string
    {
        $stmt = $this->db->prepare('SELECT `value` FROM site_settings WHERE `key` = ? LIMIT 1');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value === false ? null : (string) $value;
    }

    public function all(): array
    {
        $rows = $this->db->query('SELECT `key`, `value` FROM site_settings')->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['key']] = $row['value'];
        }
        return $out;
    }

    public function set(string $key, string $value): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO site_settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)'
        );
        return $stmt->execute([$key, $value]);
    }
}
