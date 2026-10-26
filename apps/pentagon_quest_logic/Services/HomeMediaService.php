<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Models\HomeSetting;
use App\Models\Poster;
use PDOException;

class HomeMediaService
{
    private HomeSetting $settings;
    private Poster $posters;

    public function __construct()
    {
        $this->settings = new HomeSetting();
        $this->posters = new Poster();
    }

    public function logoPath(): string
    {
        return $this->setting('site_logo', 'assets/images/logo.png');
    }

    public function heroVideoPath(): string
    {
        return $this->setting('home_hero_video', 'assets/videos/safaris.mp4');
    }

    public function showPosters(): bool
    {
        return $this->setting('home_show_posters', '1') === '1';
    }

    public function setShowPosters(bool $show): void
    {
        $this->settings->set('home_show_posters', $show ? '1' : '0');
    }

    public function uploadLogo(array $file): ?string
    {
        $path = Upload::store($file, 'brand');
        if ($path !== null) {
            $this->settings->set('site_logo', $path);
        }
        return $path;
    }

    public function uploadHeroVideo(array $file): ?string
    {
        $path = Upload::storeVideo($file, 'home');
        if ($path !== null) {
            $this->settings->set('home_hero_video', $path);
        }
        return $path;
    }

    public function activePosters(): array
    {
        try {
            return $this->posters->allActive();
        } catch (PDOException) {
            return [];
        }
    }

    public function allPosters(): array
    {
        return $this->posters->all();
    }

    public function createPoster(array $data, array $file): int
    {
        $path = Upload::store($file, 'posters');
        if ($path === null && trim((string) ($data['image_url'] ?? '')) === '') {
            throw new \RuntimeException('Please choose a poster image or enter an image URL.');
        }
        return $this->posters->create(array_merge($data, [
            'title' => trim((string) ($data['title'] ?? '')),
            'image_url' => $path ?? trim($data['image_url']),
        ]));
    }

    public function findPoster(int $id): ?array
    {
        return $this->posters->find($id);
    }

    /**
     * Update a poster; a newly uploaded file replaces (and deletes) the old image.
     */
    public function updatePoster(int $id, array $data, array $file = []): bool
    {
        $poster = $this->posters->find($id);
        if (!$poster) {
            throw new \RuntimeException('Poster not found.');
        }

        $path = Upload::store($file, 'posters');
        $imageUrl = $path ?? (trim((string) ($data['image_url'] ?? '')) ?: $poster['image_url']);
        if ($imageUrl !== $poster['image_url'] && str_starts_with($poster['image_url'], 'assets/images/uploads/')) {
            Upload::delete($poster['image_url']);
        }

        return $this->posters->update($id, array_merge($data, ['image_url' => $imageUrl]));
    }

    public function setPosterStatus(int $id, string $status): bool
    {
        $poster = $this->posters->find($id);
        return $poster ? $this->posters->update($id, array_merge($poster, ['status' => $status])) : false;
    }

    public function deletePoster(int $id): bool
    {
        $poster = $this->posters->find($id);
        if ($poster && str_starts_with($poster['image_url'], 'assets/images/uploads/')) {
            Upload::delete($poster['image_url']);
        }
        return $this->posters->delete($id);
    }

    private function setting(string $key, string $fallback): string
    {
        try {
            return $this->settings->get($key) ?: $fallback;
        } catch (PDOException) {
            return $fallback;
        }
    }
}
