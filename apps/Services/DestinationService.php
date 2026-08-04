<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Models\Destination;
use App\Models\DestinationImage;
use PDOException;

class DestinationService
{
    private Destination $model;
    private DestinationImage $imageModel;

    public function __construct()
    {
        $this->model = new Destination();
        $this->imageModel = new DestinationImage();
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
        foreach ($this->imageModel->allForDestination($id) as $image) {
            Upload::delete($image['image_path']);
        }
        return $this->model->delete($id);
    }

    public function getImages(int $destinationId): array
    {
        return $this->imageModel->allForDestination($destinationId);
    }

    /**
     * Store uploaded files (raw $_FILES['x'] entry) and attach them to a destination.
     */
    public function addImages(int $destinationId, array $files): array
    {
        $paths = Upload::storeMany($files, 'destinations');
        foreach ($paths as $path) {
            $this->imageModel->create($destinationId, $path);
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

    /**
     * Replace a destination's cover image from an uploaded file (raw $_FILES['x'] entry).
     * Returns the new image path, or null if no file was submitted.
     */
    public function setCoverImage(int $id, array $file): ?string
    {
        $path = Upload::store($file, 'destinations');
        if ($path === null) {
            return null;
        }

        $destination = $this->model->find($id);
        if (!$destination) {
            return null;
        }

        if (!empty($destination['image_url']) && str_starts_with($destination['image_url'], 'assets/images/uploads/')) {
            Upload::delete($destination['image_url']);
        }

        $this->model->update($id, array_merge($destination, ['image_url' => $path]));
        return $path;
    }

    private function fallbackFeatured(): array
    {
        return [
            ['id' => null, 'name' => 'Masai Mara', 'country' => 'Kenya', 'image_url' => 'var(--green)'],
            ['id' => null, 'name' => 'Serengeti', 'country' => 'Tanzania', 'image_url' => 'var(--charcoal)'],
            ['id' => null, 'name' => 'Bwindi Forest', 'country' => 'Uganda', 'image_url' => 'var(--green-light)'],
        ];
    }
}
