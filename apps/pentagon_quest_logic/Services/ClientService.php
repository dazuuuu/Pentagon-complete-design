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
     * Register a brand-new client (not created via an enquiry accept) and
     * email them a welcome confirmation — skipped silently if they have no
     * email or SMTP isn't configured yet.
     */
    public function register(array $data): int
    {
        $id = $this->model->create($data);
        $client = $this->model->find($id);
        if ($client) {
            $this->mail->sendClientRegistrationWelcome($client);
        }
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->update($id, $data);
    }

    public function search(array $filters): array
    {
        try {
            return $this->model->search($filters);
        } catch (PDOException) {
            return [];
        }
    }

    public function distinctCountries(): array
    {
        try {
            return $this->model->distinctCountries();
        } catch (PDOException) {
            return [];
        }
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
