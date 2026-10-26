<?php

namespace App\Services;

use App\Models\ClientFollowUp;
use PDOException;

class ClientFollowUpService
{
    private ClientFollowUp $model;

    public function __construct()
    {
        $this->model = new ClientFollowUp();
    }

    public function allForClient(int $clientId): array
    {
        try {
            return $this->model->allForClient($clientId);
        } catch (PDOException) {
            return [];
        }
    }

    public function create(array $data): int
    {
        return $this->model->create($data);
    }

    public function toggleDone(int $id): bool
    {
        return $this->model->toggleDone($id);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
}
