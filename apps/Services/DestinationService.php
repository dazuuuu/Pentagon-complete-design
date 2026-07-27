<?php

namespace App\Services;

use App\Models\Destination;
use PDOException;

class DestinationService
{
    private Destination $model;

    public function __construct()
    {
        $this->model = new Destination();
    }

    public function getActive(): array
    {
        try {
            return $this->model->allActive();
        } catch (PDOException) {
            return $this->fallbackFeatured();
        }
    }

    public function getFeatured(int $limit = 3): array
    {
        try {
            $items = $this->model->featured($limit);
            if (empty($items)) {
                $items = array_slice($this->model->allActive(), 0, $limit);
            }
            return $items;
        } catch (PDOException) {
            return array_slice($this->fallbackFeatured(), 0, $limit);
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

    private function fallbackFeatured(): array
    {
        return [
            ['name' => 'Masai Mara', 'country' => 'Kenya', 'image_url' => 'var(--green)'],
            ['name' => 'Serengeti', 'country' => 'Tanzania', 'image_url' => 'var(--charcoal)'],
            ['name' => 'Bwindi Forest', 'country' => 'Uganda', 'image_url' => 'var(--green-light)'],
        ];
    }
}
