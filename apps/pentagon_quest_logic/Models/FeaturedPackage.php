<?php

namespace App\Models;

class FeaturedPackage extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM featured_packages WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM featured_packages ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM featured_packages WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO featured_packages (hotel, meal, location, two_nights_price, three_nights_price, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['hotel'],
            $data['meal'] ?? '',
            $data['location'] ?? '',
            $data['two_nights_price'] ?? '',
            $data['three_nights_price'] ?? '',
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE featured_packages SET hotel = ?, meal = ?, location = ?, two_nights_price = ?, three_nights_price = ?, status = ?, sort_order = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['hotel'],
            $data['meal'] ?? '',
            $data['location'] ?? '',
            $data['two_nights_price'] ?? '',
            $data['three_nights_price'] ?? '',
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM featured_packages WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
