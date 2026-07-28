<?php

namespace App\Services;

use App\Models\Client;
use PDOException;

class ClientService
{
    private Client $model;

    public function __construct()
    {
        $this->model = new Client();
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

    public function updateStatus(int $id, string $status): bool
    {
        return $this->model->updateStatus($id, $status);
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

    public function countSince(string $datetime): int
    {
        try {
            return $this->model->countSince($datetime);
        } catch (PDOException) {
            return 0;
        }
    }

    public function monthlyCounts(int $months = 6): array
    {
        try {
            return $this->model->monthlyCounts($months);
        } catch (PDOException) {
            return [];
        }
    }
}
