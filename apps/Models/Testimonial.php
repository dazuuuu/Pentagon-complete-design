<?php

namespace App\Models;

class Testimonial extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM testimonials WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM testimonials ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM testimonials WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByClient(int $clientId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM testimonials WHERE client_id = ? ORDER BY id DESC');
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO testimonials (client_id, author_name, author_location, quote, accent_color, sort_order, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['client_id'] ?? null,
            $data['author_name'],
            $data['author_location'] ?? null,
            $data['quote'],
            $data['accent_color'] ?? 'gold',
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE testimonials SET author_name = ?, author_location = ?, quote = ?, accent_color = ?, sort_order = ?, status = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['author_name'],
            $data['author_location'] ?? null,
            $data['quote'],
            $data['accent_color'] ?? 'gold',
            $data['sort_order'] ?? 0,
            $data['status'] ?? 'active',
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM testimonials WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
