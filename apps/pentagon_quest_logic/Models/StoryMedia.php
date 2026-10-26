<?php

namespace App\Models;

class StoryMedia extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM story_media ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM story_media WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $imagePath, string $caption = '', int $sortOrder = 0): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO story_media (image_path, caption, sort_order) VALUES (?, ?, ?)'
        );
        $stmt->execute([$imagePath, $caption, $sortOrder]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM story_media WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
