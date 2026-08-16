<?php

namespace App\Models;

class Gallery extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM gallery WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM gallery ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM gallery WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO gallery (title, category, image_url, sort_order, status) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['category'],
            $data['image_url'],
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE gallery SET title = ?, category = ?, image_url = ?, sort_order = ?, status = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['title'],
            $data['category'],
            $data['image_url'],
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM gallery WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
