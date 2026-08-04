<?php

namespace App\Services;

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

    public function getAll(): array
    {
        return array_map([$this, 'formatForView'], $this->model->all());
    }

    public function find(int $id): ?array
    {
        $offer = $this->model->find($id);
        return $offer ? $this->formatForView($offer) : null;
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

    public function getClaimants(int $offerId): array
    {
        return $this->model->claimants($offerId);
    }

    private function formatForView(array $offer): array
    {
        if (!empty($offer['tour_id'])) {
            $offer['target_type'] = 'tour';
            $offer['target_name'] = $offer['tour_title'];
            $offer['target_image'] = $offer['tour_image'];
            $offer['target_url'] = 'tour-details.php?id=' . $offer['tour_id'] . '&offer=' . $offer['id'];
        } elseif (!empty($offer['destination_id'])) {
            $offer['target_type'] = 'destination';
            $offer['target_name'] = $offer['destination_name'];
            $offer['target_image'] = $offer['destination_image'];
            $offer['target_url'] = 'destination-details.php?id=' . $offer['destination_id'] . '&offer=' . $offer['id'];
        } else {
            $offer['target_type'] = null;
            $offer['target_name'] = null;
            $offer['target_image'] = null;
            $offer['target_url'] = 'contact.php';
        }

        return $offer;
    }
}
