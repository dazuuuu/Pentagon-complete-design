<?php

namespace App\Models;

class Offer extends BaseModel
{
    private const SELECT = "SELECT o.*,
            t.title AS tour_title, t.image_url AS tour_image,
            d.name AS destination_name, d.image_url AS destination_image
        FROM offers o
        LEFT JOIN tours t ON o.tour_id = t.id
        LEFT JOIN destinations d ON o.destination_id = d.id";

    public function allActive(): array
    {
        return $this->db->query(
            self::SELECT . " WHERE o.status = 'active' ORDER BY o.sort_order ASC, o.id DESC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query(self::SELECT . ' ORDER BY o.sort_order ASC, o.id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(self::SELECT . ' WHERE o.id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO offers (title, description, badge, tour_id, destination_id, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['badge'] ?? null,
            $data['tour_id'] ?: null,
            $data['destination_id'] ?: null,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE offers SET title = ?, description = ?, badge = ?, tour_id = ?, destination_id = ?, status = ?, sort_order = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['badge'] ?? null,
            $data['tour_id'] ?: null,
            $data['destination_id'] ?: null,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM offers WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function claimants(int $offerId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM enquiries WHERE offer_id = ? ORDER BY created_at DESC, id DESC');
        $stmt->execute([$offerId]);
        return $stmt->fetchAll();
    }
}
