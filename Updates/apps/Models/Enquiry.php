<?php

namespace App\Models;

class Enquiry extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO enquiries (full_name, email, phone, interest, offer_id, message, ip_address, email_sent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['phone'] ?? null,
            $data['interest'] ?? null,
            $data['offer_id'] ?: null,
            $data['message'] ?? null,
            $data['ip_address'] ?? null,
            $data['email_sent'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function markEmailSent(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE enquiries SET email_sent = 1 WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM enquiries WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE enquiries SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM enquiries ORDER BY created_at DESC')->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM enquiries')->fetchColumn();
    }

    public function countSince(string $datetime): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM enquiries WHERE created_at >= ?');
        $stmt->execute([$datetime]);
        return (int) $stmt->fetchColumn();
    }

    public function monthlyCounts(int $months = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS c FROM enquiries
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY ym ORDER BY ym ASC"
        );
        $stmt->execute([$months]);
        return $stmt->fetchAll();
    }
}
