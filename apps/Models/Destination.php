<?php

namespace App\Models;

class Destination extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM destinations WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        )->fetchAll();
    }

    public function featured(int $limit = 3): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM destinations WHERE status = 'active' AND is_featured = 1 ORDER BY sort_order ASC, id ASC LIMIT ?"
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM destinations ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM destinations WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO destinations (name, country, image_url, is_featured, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['country'],
            $data['image_url'] ?? null,
            $data['is_featured'] ?? 0,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE destinations SET name = ?, country = ?, image_url = ?, is_featured = ?, sort_order = ?, status = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['name'],
            $data['country'],
            $data['image_url'] ?? null,
            $data['is_featured'] ?? 0,
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM destinations WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
