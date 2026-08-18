<?php

namespace App\Models;

class HomeSetting extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT setting_key, setting_value FROM home_settings')->fetchAll();
    }

    public function get(string $key): ?string
    {
        $stmt = $this->db->prepare('SELECT setting_value FROM home_settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value === false ? null : (string) $value;
    }

    public function set(string $key, string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO home_settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        $stmt->execute([$key, $value]);
    }
}
