<?php

namespace App\Models;

class ServiceTier extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM service_tiers WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM service_tiers ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM service_tiers WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO service_tiers (name, price, features, is_popular, status, sort_order) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['price'],
            $data['features'] ?? null,
            $data['is_popular'] ?? 0,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE service_tiers SET name = ?, price = ?, features = ?, is_popular = ?, status = ?, sort_order = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['name'],
            $data['price'],
            $data['features'] ?? null,
            $data['is_popular'] ?? 0,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM service_tiers WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
