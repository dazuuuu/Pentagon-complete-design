<?php

namespace App\Services;

use App\Models\SiteSetting;
use PDOException;

class SiteSettingsService
{
    private SiteSetting $model;

    public function __construct()
    {
        $this->model = new SiteSetting();
    }

    public function get(string $key, string $default = ''): string
    {
        try {
            $value = $this->model->get($key);
            return $value === null || $value === '' ? $default : $value;
        } catch (PDOException) {
            return $default;
        }
    }

    public function getAll(): array
    {
        try {
            return $this->model->all();
        } catch (PDOException) {
            return [];
        }
    }

    public function set(string $key, string $value): bool
    {
        return $this->model->set($key, $value);
    }
}
