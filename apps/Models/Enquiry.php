<?php

namespace App\Models;

class Enquiry extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO enquiries (full_name, email, message, ip_address, email_sent) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['message'],
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

    public function all(): array
    {
        return $this->db->query('SELECT * FROM enquiries ORDER BY created_at DESC')->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM enquiries')->fetchColumn();
    }
}
