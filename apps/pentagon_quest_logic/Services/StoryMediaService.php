<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Models\StoryMedia;
use PDOException;

class StoryMediaService
{
    private StoryMedia $model;

    public function __construct()
    {
        $this->model = new StoryMedia();
    }

    public function getAll(): array
    {
        try {
            return $this->model->all();
        } catch (PDOException) {
            return [];
        }
    }

    /**
     * Store uploaded files (raw $_FILES['x'] entry) as story media.
     */
    public function addImages(array $files, string $caption = ''): array
    {
        $paths = Upload::storeMany($files, 'story');
        foreach ($paths as $path) {
            $this->model->create($path, $caption);
        }
        return $paths;
    }

    public function deleteImage(int $id): bool
    {
        $image = $this->model->find($id);
        if (!$image) {
            return false;
        }
        Upload::delete($image['image_path']);
        return $this->model->delete($id);
    }
}
