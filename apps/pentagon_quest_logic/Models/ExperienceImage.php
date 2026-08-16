<?php

namespace App\Models;

class ExperienceImage extends BaseModel
{
    public function allForExperience(int $experienceId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM experience_images WHERE experience_id = ? ORDER BY sort_order ASC, id ASC');
        $stmt->execute([$experienceId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM experience_images WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(int $experienceId, string $imagePath, int $sortOrder = 0): int
    {
        $stmt = $this->db->prepare('INSERT INTO experience_images (experience_id, image_path, sort_order) VALUES (?, ?, ?)');
        $stmt->execute([$experienceId, $imagePath, $sortOrder]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM experience_images WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
