<?php

namespace App\Models;

class Offer extends BaseModel
{
    private const SELECT = "SELECT o.*
        FROM offers o";

    public function activeBannerOffer(): ?array
    {
        $stmt = $this->db->prepare(
            self::SELECT . " WHERE o.status = 'active' AND o.is_banner = 1 AND (o.end_time IS NULL OR o.end_time > NOW()) ORDER BY o.id DESC LIMIT 1"
        );
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

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
            'INSERT INTO offers (title, description, image_url, video_url, is_banner, end_time, badge, link_url, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['video_url'] ?? null,
            $data['is_banner'] ?? 0,
            $data['end_time'] ?? null,
            $data['badge'] ?? null,
            $data['link_url'] ?? null,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE offers SET title = ?, description = ?, image_url = ?, video_url = ?, is_banner = ?, end_time = ?, badge = ?, link_url = ?, status = ?, sort_order = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['image_url'] ?? null,
            $data['video_url'] ?? null,
            $data['is_banner'] ?? 0,
            $data['end_time'] ?? null,
            $data['badge'] ?? null,
            $data['link_url'] ?? null,
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
