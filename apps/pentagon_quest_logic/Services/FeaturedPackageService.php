<?php

namespace App\Services;

use App\Models\FeaturedPackage;
use PDOException;

class FeaturedPackageService
{
    private FeaturedPackage $model;

    public function __construct()
    {
        $this->model = new FeaturedPackage();
    }

    public function getActive(): array
    {
        try {
            return $this->model->allActive();
        } catch (PDOException) {
            return [];
        }
    }

    public function getAll(): array
    {
        return $this->model->all();
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
