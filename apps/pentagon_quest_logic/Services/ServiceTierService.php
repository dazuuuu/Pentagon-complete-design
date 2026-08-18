<?php

namespace App\Services;

use App\Models\ServiceTier;
use PDOException;

class ServiceTierService
{
    private ServiceTier $model;

    public function __construct()
    {
        $this->model = new ServiceTier();
    }

    public function getActive(): array
    {
        try {
            return array_map([$this, 'formatForView'], $this->model->allActive());
        } catch (PDOException) {
            return [];
        }
    }

    public function getAll(): array
    {
        return $this->model->all();
    }

    public function find(int $id): ?array
    {
        return $this->model->find($id);
    }

    public function create(array $data): int
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    private function formatForView(array $tier): array
    {
        $tier['feature_list'] = array_filter(array_map('trim', explode("\n", $tier['features'] ?? '')));
        return $tier;
    }
}
