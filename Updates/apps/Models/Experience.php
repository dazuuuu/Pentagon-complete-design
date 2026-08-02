<?php

namespace App\Models;

class Experience extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM experiences WHERE status = 'active' ORDER BY created_at DESC, id DESC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM experiences ORDER BY created_at DESC, id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM experiences WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO experiences (title, description, status, sort_order) VALUES (?, ?, ?, ?)'
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
            'UPDATE experiences SET title = ?, description = ?, status = ?, sort_order = ? WHERE id = ?'
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
        $stmt = $this->db->prepare('DELETE FROM experiences WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
