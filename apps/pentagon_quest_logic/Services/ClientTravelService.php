<?php

namespace App\Services;

use App\Models\ClientTravel;
use PDOException;

class ClientTravelService
{
    private ClientTravel $model;

    public function __construct()
    {
        $this->model = new ClientTravel();
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

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
}
