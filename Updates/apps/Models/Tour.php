<?php

namespace App\Models;

use PDO;

class Tour extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT t.*, d.name AS destination_name
             FROM tours t
             LEFT JOIN destinations d ON d.id = t.destination_id
             WHERE t.status = 'active'
             ORDER BY t.id ASC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT t.*, d.name AS destination_name
             FROM tours t
             LEFT JOIN destinations d ON d.id = t.destination_id
             ORDER BY t.id ASC'
        )->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tours WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tours (title, destination_id, country, tour_type, duration, price, badge, description, image_url, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['destination_id'] ?: null,
            $data['country'],
            $data['tour_type'],
            $data['duration'],
            $data['price'],
            $data['badge'] ?? null,
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE tours SET title = ?, destination_id = ?, country = ?, tour_type = ?, duration = ?, price = ?, badge = ?, description = ?, image_url = ?, status = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['title'],
            $data['destination_id'] ?: null,
            $data['country'],
            $data['tour_type'],
            $data['duration'],
            $data['price'],
            $data['badge'] ?? null,
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tours WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
