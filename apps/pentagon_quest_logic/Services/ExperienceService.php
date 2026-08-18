<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Models\Experience;
use App\Models\ExperienceImage;
use PDOException;

class ExperienceService
{
    private Experience $model;
    private ExperienceImage $imageModel;

    public function __construct()
    {
        $this->model = new Experience();
        $this->imageModel = new ExperienceImage();
    }

    public function getActive(): array
    {
        try {
            $experiences = $this->model->allActive();
        } catch (PDOException) {
            return [];
        }

        foreach ($experiences as &$experience) {
            $experience['images'] = $this->imageModel->allForExperience((int) $experience['id']);
        }

        return $experiences;
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
        foreach ($this->imageModel->allForExperience($id) as $image) {
            Upload::delete($image['image_path']);
        }
        return $this->model->delete($id);
    }

    public function getImages(int $experienceId): array
    {
        return $this->imageModel->allForExperience($experienceId);
    }

    /**
     * Store uploaded files (raw $_FILES['x'] entry) and attach them to an experience.
     */
    public function addImages(int $experienceId, array $files): array
    {
        $paths = Upload::storeMany($files, 'experiences');
        foreach ($paths as $path) {
            $this->imageModel->create($experienceId, $path);
        }
        return $paths;
    }

    public function deleteImage(int $imageId): bool
    {
        $image = $this->imageModel->find($imageId);
        if (!$image) {
            return false;
        }
        Upload::delete($image['image_path']);
        return $this->imageModel->delete($imageId);
    }
}
