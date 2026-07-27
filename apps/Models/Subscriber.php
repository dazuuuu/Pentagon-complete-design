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
}
