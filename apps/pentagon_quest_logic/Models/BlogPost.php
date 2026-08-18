<?php

namespace App\Models;

class BlogPost extends BaseModel
{
    public function allActive(): array
    {
        return $this->db->query(
            "SELECT * FROM blog_posts WHERE status = 'active' ORDER BY sort_order ASC, id DESC"
        )->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM blog_posts ORDER BY sort_order ASC, id DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM blog_posts WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO blog_posts (title, category, excerpt, content, image_url, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['category'],
            $data['excerpt'] ?? null,
            $data['content'] ?? null,
            $data['image_url'] ?? null,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE blog_posts SET title = ?, category = ?, excerpt = ?, content = ?, image_url = ?, status = ?, sort_order = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['title'],
            $data['category'],
            $data['excerpt'] ?? null,
            $data['content'] ?? null,
            $data['image_url'] ?? null,
            $data['status'] ?? 'active',
            $data['sort_order'] ?? 0,
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM blog_posts WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn();
    }
}
