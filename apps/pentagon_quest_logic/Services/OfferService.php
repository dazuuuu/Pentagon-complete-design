<?php

namespace App\Services;

use App\Helpers\Upload;
use App\Models\Offer;
use PDOException;

class OfferService
{
    private Offer $model;

    public function __construct()
    {
        $this->model = new Offer();
    }

    public function getActive(): array
    {
        try {
            return array_map([$this, 'formatForView'], $this->model->allActive());
        } catch (PDOException) {
            return [];
        }
    }

    public function getActiveBannerOffer(): ?array
    {
        try {
            $offer = $this->model->activeBannerOffer();
            return $offer ? $this->formatForView($offer) : null;
        } catch (PDOException) {
            return null;
        }
    }

    public function getAll(): array
    {
        return array_map([$this, 'formatForView'], $this->model->all());
    }

    public function find(int $id): ?array
    {
        $offer = $this->model->find($id);
        return $offer ? $this->formatForView($offer) : null;
    }

    public function create(array $data, array $imageFile = [], array $videoFile = []): int
    {
        $imagePath = Upload::store($imageFile, 'offers');
        if ($imagePath !== null) {
            $data['image_url'] = $imagePath;
        }
        $videoPath = Upload::storeVideo($videoFile, 'offers');
        if ($videoPath !== null) {
            $data['video_url'] = $videoPath;
        }
        return $this->model->create($data);
    }

    public function update(int $id, array $data, array $imageFile = [], array $videoFile = []): bool
    {
        $imagePath = Upload::store($imageFile, 'offers');
        if ($imagePath !== null) {
            $data['image_url'] = $imagePath;
        }
        $videoPath = Upload::storeVideo($videoFile, 'offers');
        if ($videoPath !== null) {
            $data['video_url'] = $videoPath;
        }
        return $this->model->update($id, $data);
    }

    public function delete(int $id): bool
    {
        $offer = $this->model->find($id);
        if ($offer && !empty($offer['image_url']) && str_starts_with($offer['image_url'], 'assets/images/uploads/')) {
            Upload::delete($offer['image_url']);
        }
        return $this->model->delete($id);
    }

    public function getClaimants(int $offerId): array
    {
        return $this->model->claimants($offerId);
    }

    private function formatForView(array $offer): array
    {
        $offer['target_url'] = $offer['link_url'] ?: null;
        return $offer;
    }
}
