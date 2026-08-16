<?php

namespace App\Models;

class TourImage extends BaseModel
{
    public function allForTour(int $tourId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM tour_images WHERE tour_id = ? ORDER BY sort_order ASC, id ASC');
        $stmt->execute([$tourId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tour_images WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(int $tourId, string $imagePath, int $sortOrder = 0): int
    {
        $stmt = $this->db->prepare('INSERT INTO tour_images (tour_id, image_path, sort_order) VALUES (?, ?, ?)');
        $stmt->execute([$tourId, $imagePath, $sortOrder]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tour_images WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
