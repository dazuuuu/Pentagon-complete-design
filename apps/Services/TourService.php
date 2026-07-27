<?php

namespace App\Services;

use App\Models\Tour;
use PDOException;

class TourService
{
    private Tour $model;

    public function __construct()
    {
        $this->model = new Tour();
    }

    public function getActive(): array
    {
        try {
            return array_map([$this, 'formatForView'], $this->model->allActive());
        } catch (PDOException) {
            return $this->fallbackTours();
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

    private function formatForView(array $tour): array
    {
        return [
            'id' => $tour['id'],
            'title' => $tour['title'],
            'dest' => $tour['country'],
            'type' => $tour['tour_type'],
            'dur' => $tour['duration'],
            'price' => '$' . number_format((float) $tour['price'], 0),
            'badge' => $tour['badge'] ?? 'Tour',
            'description' => $tour['description'] ?? '',
        ];
    }

    private function fallbackTours(): array
    {
        return [
            ['id' => 1, 'title' => 'Masai Mara Great Migration Safari', 'dest' => 'Kenya', 'type' => 'Wildlife Safari', 'dur' => '7 Days', 'price' => '$1,850', 'badge' => 'Best Seller', 'description' => ''],
            ['id' => 2, 'title' => 'Serengeti & Ngorongoro Crater', 'dest' => 'Tanzania', 'type' => 'Wildlife Safari', 'dur' => '9 Days', 'price' => '$2,200', 'badge' => 'Popular', 'description' => ''],
        ];
    }
}
