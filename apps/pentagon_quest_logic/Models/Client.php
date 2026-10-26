<?php

namespace App\Models;

class Client extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clients (enquiry_id, full_name, email, phone, interest, country_of_origin, purpose_of_visit, profile_picture, status, scheduled_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['enquiry_id'] ?? null,
            $data['full_name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['interest'] ?? null,
            $data['country_of_origin'] ?? null,
            $data['purpose_of_visit'] ?? null,
            $data['profile_picture'] ?? null,
            $data['status'] ?? 'scheduled',
            $data['scheduled_date'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Full-row update covering every editable client field, following the
     * same pattern as Tour::update().
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE clients SET full_name = ?, email = ?, phone = ?, interest = ?, country_of_origin = ?, purpose_of_visit = ?, profile_picture = ?, status = ?, scheduled_date = ? WHERE id = ?'
        );
        return $stmt->execute([
            $data['full_name'],
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['interest'] ?? null,
            $data['country_of_origin'] ?? null,
            $data['purpose_of_visit'] ?? null,
            $data['profile_picture'] ?? null,
            $data['status'] ?? 'scheduled',
            $data['scheduled_date'] ?? null,
            $id,
        ]);
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

    public function updateStatus(int $id, string $status, ?string $scheduledDate = null): bool
    {
        $stmt = $this->db->prepare('UPDATE clients SET status = ?, scheduled_date = ? WHERE id = ?');
        return $stmt->execute([$status, $scheduledDate, $id]);
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

    /**
     * Parameterized search/filter for the Reports page. Never string-concatenates
     * raw user input into SQL — all filters are bound placeholders.
     *
     * $filters keys: q, status, country, date_from, date_to (all optional).
     */
    public function search(array $filters): array
    {
        $where = [];
        $params = [];

        $q = trim((string) ($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = '(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $status = trim((string) ($filters['status'] ?? ''));
        if ($status !== '') {
            $where[] = 'status = ?';
            $params[] = $status;
        }

        $country = trim((string) ($filters['country'] ?? ''));
        if ($country !== '') {
            $where[] = 'country_of_origin = ?';
            $params[] = $country;
        }

        $dateFrom = trim((string) ($filters['date_from'] ?? ''));
        if ($dateFrom !== '') {
            $where[] = 'DATE(created_at) >= ?';
            $params[] = $dateFrom;
        }

        $dateTo = trim((string) ($filters['date_to'] ?? ''));
        if ($dateTo !== '') {
            $where[] = 'DATE(created_at) <= ?';
            $params[] = $dateTo;
        }

        $sql = 'SELECT * FROM clients';
        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Distinct list of countries on file, for the Reports page filter select.
     */
    public function distinctCountries(): array
    {
        $rows = $this->db->query(
            "SELECT DISTINCT country_of_origin FROM clients WHERE country_of_origin IS NOT NULL AND country_of_origin <> '' ORDER BY country_of_origin ASC"
        )->fetchAll();
        return array_column($rows, 'country_of_origin');
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
