<?php

namespace App\Models;

class Client extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clients (enquiry_id, full_name, email, phone, interest, status) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['enquiry_id'] ?? null,
            $data['full_name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['interest'] ?? null,
            $data['status'] ?? 'scheduled',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM clients ORDER BY created_at DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clients WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE clients SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM clients WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM clients')->fetchColumn();
    }

    public function countSince(string $datetime): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM clients WHERE created_at >= ?');
        $stmt->execute([$datetime]);
        return (int) $stmt->fetchColumn();
    }

    public function monthlyCounts(int $months = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM clients
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY ym ORDER BY ym ASC"
        );
        $stmt->execute([$months]);
        return $stmt->fetchAll();
    }
}
