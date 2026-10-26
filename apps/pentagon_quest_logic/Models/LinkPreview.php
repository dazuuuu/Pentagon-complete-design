<?php

namespace App\Models;

class LinkPreview extends BaseModel
{
    public function findByUrl(string $url): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM link_previews WHERE url_hash = ? LIMIT 1');
        $stmt->execute([sha1($url)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function upsert(string $url, ?string $title, ?string $description, ?string $imageUrl): array
    {
        $hash = sha1($url);
        $stmt = $this->db->prepare(
            'INSERT INTO link_previews (url, url_hash, title, description, image_url, fetched_at)
             VALUES (?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE title = VALUES(title), description = VALUES(description),
               image_url = VALUES(image_url), fetched_at = NOW()'
        );
        $stmt->execute([$url, $hash, $title, $description, $imageUrl]);

        return $this->findByUrl($url) ?? [
            'url' => $url,
            'title' => $title,
            'description' => $description,
            'image_url' => $imageUrl,
        ];
    }
}
