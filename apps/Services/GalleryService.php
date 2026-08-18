<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Models\Gallery;
use PDOException;

class GalleryService
{
    private Gallery $model;

    public function __construct()
    {
        $this->model = new Gallery();
    }

    public function getActive(): array
    {
        try {
            return array_map([$this, 'formatForView'], $this->model->allActive());
        } catch (PDOException) {
            return $this->fallbackGallery();
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
        $item = $this->model->find($id);
        if ($item && str_starts_with($item['image_url'], 'assets/images/uploads/')) {
            Upload::delete($item['image_url']);
        }
        return $this->model->delete($id);
    }

    /**
     * Store multiple uploaded files (raw $_FILES['x'] entry), creating one gallery
     * row per file, all sharing the same title/category/sort_order/status.
     */
    public function createMany(array $commonData, array $files): array
    {
        $paths = Upload::storeMany($files, 'gallery');
        $ids = [];
        foreach ($paths as $path) {
            $ids[] = $this->model->create(array_merge($commonData, ['image_url' => $path]));
        }
        return $ids;
    }

    private function formatForView(array $item): array
    {
        return [
            'title' => $item['title'],
            'cat' => $item['category'],
            'image_url' => $item['image_url'],
        ];
    }

    private function fallbackGallery(): array
    {
        return [
            ['title' => 'Lion at Sunrise', 'cat' => 'Wildlife', 'image_url' => ''],
            ['title' => 'Elephant Herd', 'cat' => 'Wildlife', 'image_url' => ''],
        ];
    }
}
