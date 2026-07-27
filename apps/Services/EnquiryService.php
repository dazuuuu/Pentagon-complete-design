<?php

namespace App\Services;

use App\Helpers\Validator;
use App\Models\Enquiry;
use PDOException;

class EnquiryService
{
    private Enquiry $model;
    private MailService $mail;

    public function __construct()
    {
        $this->model = new Enquiry();
        $this->mail = new MailService();
    }

    public function submit(array $data): array
    {
        $errors = Validator::required($data, ['full_name', 'email', 'message']);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        if (!Validator::email($data['email'])) {
            return ['success' => false, 'errors' => ['email' => 'Please provide a valid email address.']];
        }

        try {
            $id = $this->model->create([
                'full_name' => trim($data['full_name']),
                'email' => trim($data['email']),
                'message' => trim($data['message']),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'email_sent' => 0,
            ]);

            $sent = $this->mail->sendEnquiryNotification($data);
            if ($sent) {
                $this->model->markEmailSent($id);
                $this->mail->sendEnquiryConfirmation($data);
            }

            return ['success' => true, 'message' => 'Enquiry sent successfully.'];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => ['general' => 'Unable to save enquiry. Please try again later.']];
        }
    }

    public function getAll(): array
    {
        return $this->model->all();
    }

    public function count(): int
    {
        try {
            return $this->model->count();
        } catch (PDOException) {
            return 0;
        }
    }
}
