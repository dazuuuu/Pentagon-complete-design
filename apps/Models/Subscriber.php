<?php

namespace App\Models;

class Subscriber extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM subscribers WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $email): int
    {
        $stmt = $this->db->prepare('INSERT INTO subscribers (email, status) VALUES (?, ?)');
        $stmt->execute([$email, 'active']);
        return (int) $this->db->lastInsertId();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM subscribers ORDER BY subscribed_at DESC')->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM subscribers WHERE status = 'active'")->fetchColumn();
    }

    public function unsubscribe(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE subscribers SET status = 'unsubscribed' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countSince(string $datetime): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM subscribers WHERE subscribed_at >= ?');
        $stmt->execute([$datetime]);
        return (int) $stmt->fetchColumn();
    }

    public function monthlyCounts(int $months = 6): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(subscribed_at, '%Y-%m') AS ym, COUNT(*) AS c FROM subscribers
             WHERE subscribed_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
             GROUP BY ym ORDER BY ym ASC"
        );
        $stmt->execute([$months]);
        return $stmt->fetchAll();
    }
}
