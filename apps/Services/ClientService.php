<?php

namespace App\Services;

use App\Models\Client;
use PDOException;

class ClientService
{
    private Client $model;
    private MailService $mail;

    public function __construct()
    {
        $this->model = new Client();
        $this->mail = new MailService();
    }

    public function getAll(): array
    {
        return $this->model->all();
    }

    public function find(int $id): ?array
    {
        return $this->model->find($id);
    }

    public function create(array $data): int
    {
        return $this->model->create($data);
    }

    /**
     * Update a client's status/scheduled date and email them the change
     * (skipped silently when the client has no email on file).
     */
    public function updateStatus(int $id, string $status, ?string $scheduledDate = null): bool
    {
        $updated = $this->model->updateStatus($id, $status, $scheduledDate);
        if ($updated) {
            $client = $this->model->find($id);
            if ($client) {
                $this->mail->sendClientStatusUpdate($client, $status, $scheduledDate);
            }
        }
        return $updated;
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function count(): int
    {
        try {
            return $this->model->count();
        } catch (PDOException) {
            return 0;
        }
    }

    public function countSince(string $datetime): int
    {
        try {
            return $this->model->countSince($datetime);
        } catch (PDOException) {
            return 0;
        }
    }

    public function monthlyCounts(int $months = 6): array
    {
        try {
            return $this->model->monthlyCounts($months);
        } catch (PDOException) {
            return [];
        }
    }
}
