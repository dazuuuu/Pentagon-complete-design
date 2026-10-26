<?php

namespace App\Models;

class ClientFollowUp extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO client_follow_ups (client_id, follow_up_date, note, is_done) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['client_id'],
            $data['follow_up_date'],
            $data['note'] ?? null,
            !empty($data['is_done']) ? 1 : 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM client_follow_ups WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function allForClient(int $clientId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM client_follow_ups WHERE client_id = ? ORDER BY follow_up_date ASC');
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public function toggleDone(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE client_follow_ups SET is_done = NOT is_done WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM client_follow_ups WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
