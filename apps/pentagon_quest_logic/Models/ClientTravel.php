<?php

namespace App\Models;

class ClientTravel extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO client_travels (client_id, destination, travel_date, return_date, status, notes) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['client_id'],
            $data['destination'] ?? null,
            $data['travel_date'],
            $data['return_date'] ?? null,
            $data['status'] ?? 'planned',
            $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM client_travels WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function allForClient(int $clientId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM client_travels WHERE client_id = ? ORDER BY travel_date DESC');
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE client_travels SET destination = ?, travel_date = ?, return_date = ?, status = ?, notes = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['destination'] ?? null,
            $data['travel_date'],
            $data['return_date'] ?? null,
            $data['status'] ?? 'planned',
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM client_travels WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
