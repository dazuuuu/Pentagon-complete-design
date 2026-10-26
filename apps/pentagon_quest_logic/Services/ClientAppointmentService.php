<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientAppointment;
use PDOException;

class ClientAppointmentService
{
    private ClientAppointment $model;
    private Client $clientModel;
    private MailService $mail;

    public function __construct()
    {
        $this->model = new ClientAppointment();
        $this->clientModel = new Client();
        $this->mail = new MailService();
    }

    public function allForClient(int $clientId): array
    {
        try {
            return $this->model->allForClient($clientId);
        } catch (PDOException) {
            return [];
        }
    }

    public function create(array $data): int
    {
        $id = $this->model->create($data);
        $client = $this->clientModel->find((int) $data['client_id']);
        $appointment = $this->model->find($id);
        if ($client && $appointment) {
            $this->mail->sendAppointmentNotification($client, $appointment);
        }
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $updated = $this->model->update($id, $data);
        if ($updated) {
            $appointment = $this->model->find($id);
            if ($appointment) {
                $client = $this->clientModel->find((int) $appointment['client_id']);
                if ($client) {
                    $this->mail->sendAppointmentNotification($client, $appointment);
                }
            }
        }
        return $updated;
    }

    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }

    public function upcoming(int $limit = 50): array
    {
        try {
            return $this->model->upcoming($limit);
        } catch (PDOException) {
            return [];
        }
    }
}
