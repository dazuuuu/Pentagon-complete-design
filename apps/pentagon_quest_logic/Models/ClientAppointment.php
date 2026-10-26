<?php

namespace App\Models;

class ClientAppointment extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO client_appointments (client_id, appointment_date, purpose, status, notes) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['client_id'],
            $data['appointment_date'],
            $data['purpose'] ?? null,
            $data['status'] ?? 'scheduled',
            $data['notes'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM client_appointments WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function allForClient(int $clientId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM client_appointments WHERE client_id = ? ORDER BY appointment_date DESC');
        $stmt->execute([$clientId]);
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE client_appointments SET appointment_date = ?, purpose = ?, status = ?, notes = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['appointment_date'],
            $data['purpose'] ?? null,
            $data['status'] ?? 'scheduled',
            $data['notes'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM client_appointments WHERE id = ?');
        return $stmt->execute([$id]);
    }

    /**
     * All scheduled appointments across every client, soonest first, joined
     * to clients for display.
     */
    public function upcoming(int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, c.full_name, c.email
             FROM client_appointments a
             JOIN clients c ON c.id = a.client_id
             WHERE a.status = 'scheduled'
             ORDER BY a.appointment_date ASC
             LIMIT ?"
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
