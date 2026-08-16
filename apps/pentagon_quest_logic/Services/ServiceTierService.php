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
            return $this->fallbackTiers();
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

    private function fallbackTiers(): array
    {
        return [
            ['id' => 0, 'name' => 'Essential', 'price' => 800, 'is_popular' => 0, 'feature_list' => ['Shared 4WD Vehicle', 'Tented Camp Stay', 'Full Board Meals']],
            ['id' => 0, 'name' => 'Classic', 'price' => 1800, 'is_popular' => 1, 'feature_list' => ['Private 4WD Vehicle', 'Mid-range Lodges', 'All Park Fees']],
            ['id' => 0, 'name' => 'Premium', 'price' => 3500, 'is_popular' => 0, 'feature_list' => ['Luxury Fly-in Safari', 'Exclusive Conservancies', 'All-Inclusive Drinks']],
        ];
    }
}
