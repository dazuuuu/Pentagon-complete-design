<?php

namespace App\Models;

class Poster extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM posters WHERE status = 'active' ORDER BY sort_order ASC, id DESC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM posters ORDER BY sort_order ASC, id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM posters WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO posters (title, description, image_url, link_url, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? '',
            $data['image_url'],
            $data['link_url'] ?? '',
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE posters SET title = ?, description = ?, image_url = ?, link_url = ?, sort_order = ?, status = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? '',
            $data['image_url'],
            $data['link_url'] ?? '',
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM posters WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
