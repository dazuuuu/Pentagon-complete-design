<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Models\BlogPost;
use PDOException;

class BlogService
{
    private BlogPost $model;

    public function __construct()
    {
        $this->model = new BlogPost();
    }

    public function getActive(): array
    {
        try {
            return $this->model->allActive();
        } catch (PDOException) {
            return $this->fallbackPosts();
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
        $post = $this->model->find($id);
        if ($post && !empty($post['image_url']) && str_starts_with($post['image_url'], 'assets/images/uploads/')) {
            Upload::delete($post['image_url']);
        }
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

    /**
     * Replace a post's cover image from an uploaded file (raw $_FILES['x'] entry).
     * Returns the new image path, or null if no file was submitted.
     */
    public function setCoverImage(int $id, array $file): ?string
    {
        $path = Upload::store($file, 'blog');
        if ($path === null) {
            return null;
        }

        $post = $this->model->find($id);
        if (!$post) {
            return null;
        }

        if (!empty($post['image_url']) && str_starts_with($post['image_url'], 'assets/images/uploads/')) {
            Upload::delete($post['image_url']);
        }

        $this->model->update($id, array_merge($post, ['image_url' => $path]));
        return $path;
    }

    private function fallbackPosts(): array
    {
        return [
            ['id' => 0, 'title' => 'Top 10 Safari Photography Tips', 'category' => 'Photography', 'excerpt' => 'Capture the perfect shot with our expert guide to wildlife photography in the African bush.', 'image_url' => '', 'created_at' => '2026-07-15'],
            ['id' => 0, 'title' => 'What to Pack for Your First Safari', 'category' => 'Travel Guide', 'excerpt' => 'From neutral clothing to essential gear, here is everything you need to pack for your adventure.', 'image_url' => '', 'created_at' => '2026-07-10'],
            ['id' => 0, 'title' => 'Understanding the Great Migration', 'category' => 'Wildlife', 'excerpt' => "A deep dive into one of nature's greatest spectacles: the annual trek of millions of wildebeest.", 'image_url' => '', 'created_at' => '2026-07-05'],
            ['id' => 0, 'title' => 'The Hidden Gems of Namibia', 'category' => 'Destinations', 'excerpt' => 'Beyond the dunes: discovering the secret landscapes and wildlife of the Namib desert.', 'image_url' => '', 'created_at' => '2026-06-28'],
            ['id' => 0, 'title' => 'A Guide to Cultural Etiquette', 'category' => 'Culture', 'excerpt' => 'How to respectfully engage with local communities during your African safari.', 'image_url' => '', 'created_at' => '2026-06-20'],
            ['id' => 0, 'title' => 'Sustainable Safari: Our Commitment', 'category' => 'Sustainability', 'excerpt' => "Learn how Pentagon Quest is working to preserve Africa's wild spaces for future generations.", 'image_url' => '', 'created_at' => '2026-06-15'],
        ];
    }
}
