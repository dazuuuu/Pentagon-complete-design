<?php

namespace App\Models;

class DestinationImage extends BaseModel
{
    public function allForDestination(int $destinationId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM destination_images WHERE destination_id = ? ORDER BY sort_order ASC, id ASC');
        $stmt->execute([$destinationId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM destination_images WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(int $destinationId, string $imagePath, int $sortOrder = 0): int
    {
        $stmt = $this->db->prepare('INSERT INTO destination_images (destination_id, image_path, sort_order) VALUES (?, ?, ?)');
        $stmt->execute([$destinationId, $imagePath, $sortOrder]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM destination_images WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
