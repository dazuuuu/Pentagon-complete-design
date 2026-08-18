<?php

namespace App\Models;

class Offering extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM service_offerings WHERE status = 'active' ORDER BY sort_order ASC, id ASC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM service_offerings ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM service_offerings WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO service_offerings (title, description, status, sort_order) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE service_offerings SET title = ?, description = ?, status = ?, sort_order = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM service_offerings WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM service_offerings')->fetchColumn();
    }
}
