<?php

namespace App\Services;

use App\Models\Offering;
use PDOException;

class OfferingService
{
    private Offering $model;

    public function __construct()
    {
        $this->model = new Offering();
    }

    public function getActive(): array
    {
        try {
            return $this->model->allActive();
        } catch (PDOException) {
            return $this->fallbackOfferings();
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

    public function count(): int
    {
        try {
            return $this->model->count();
        } catch (PDOException) {
            return 0;
        }
    }

    private function fallbackOfferings(): array
    {
        return [
            ['title' => 'Wildlife Game Drives', 'description' => "Track the Big Five across Africa's finest reserves with expert naturalist guides."],
            ['title' => 'Gorilla Trekking', 'description' => 'Secure permits and handle all logistics for profound encounters in Uganda and Rwanda.'],
            ['title' => 'Mountain Climbing', 'description' => 'Guided ascents of Kilimanjaro and Mount Kenya with KPAP-certified porter welfare.'],
        ];
    }
}
