<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Models\Tour;
use App\Models\TourImage;
use PDOException;

class TourService
{
    private Tour $model;
    private TourImage $imageModel;

    public function __construct()
    {
        $this->model = new Tour();
        $this->imageModel = new TourImage();
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
        foreach ($this->imageModel->allForTour($id) as $image) {
            Upload::delete($image['image_path']);
        }
        return $this->model->delete($id);
    }

    public function getImages(int $tourId): array
    {
        return $this->imageModel->allForTour($tourId);
    }

    /**
     * Store uploaded files (raw $_FILES['x'] entry) and attach them to a tour.
     */
    public function addImages(int $tourId, array $files): array
    {
        $paths = Upload::storeMany($files, 'tours');
        foreach ($paths as $path) {
            $this->imageModel->create($tourId, $path);
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
     * Replace a tour's cover image from an uploaded file (raw $_FILES['x'] entry).
     * Returns the new image path, or null if no file was submitted.
     */
    public function setCoverImage(int $id, array $file): ?string
    {
        $path = Upload::store($file, 'tours');
        if ($path === null) {
            return null;
        }

        $tour = $this->model->find($id);
        if (!$tour) {
            return null;
        }

        if (!empty($tour['image_url']) && str_starts_with($tour['image_url'], 'assets/images/uploads/')) {
            Upload::delete($tour['image_url']);
        }

        $this->model->update($id, array_merge($tour, ['image_url' => $path]));
        return $path;
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
            'image_url' => $tour['image_url'] ?? '',
        ];
    }

    private function fallbackTours(): array
    {
        return [
            ['id' => 1, 'title' => 'Masai Mara Great Migration Safari', 'dest' => 'Kenya', 'type' => 'Wildlife Safari', 'dur' => '7 Days', 'price' => '$1,850', 'badge' => 'Best Seller', 'description' => '', 'image_url' => ''],
            ['id' => 2, 'title' => 'Serengeti & Ngorongoro Crater', 'dest' => 'Tanzania', 'type' => 'Wildlife Safari', 'dur' => '9 Days', 'price' => '$2,200', 'badge' => 'Popular', 'description' => '', 'image_url' => ''],
        ];
    }
}
