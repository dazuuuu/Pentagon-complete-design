<?php

namespace App\Services;

use App\Helpers\Validator;
use App\Models\Enquiry;
use PDOException;

class EnquiryService
{
    private Enquiry $model;
    private MailService $mail;
    private ClientService $clients;

    public function __construct()
    {
        $this->model = new Enquiry();
        $this->mail = new MailService();
        $this->clients = new ClientService();
    }

    public function submit(array $data): array
    {
        $errors = Validator::required($data, ['full_name', 'email']);
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
                'phone' => trim($data['phone'] ?? ''),
                'interest' => trim($data['interest'] ?? ''),
                'offer_id' => (int) ($data['offer_id'] ?? 0) ?: null,
                'message' => trim($data['message'] ?? ''),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'email_sent' => 0,
            ]);
            $data['full_name'] = trim($data['full_name']);
            $data['message'] = trim($data['message'] ?? '');

            $sent = $this->mail->sendEnquiryNotification($data);
            if ($sent) {
                $this->model->markEmailSent($id);
            }
            $this->mail->sendEnquiryConfirmation($data);

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

    /**
     * Accept an enquiry: email the customer, convert them into a scheduled
     * client record, and mark the enquiry as accepted.
     */
    public function accept(int $id): bool
    {
        $enquiry = $this->model->find($id);
        if (!$enquiry) {
            return false;
        }

        $this->mail->sendEnquiryAccepted($enquiry);

        $this->clients->create([
            'enquiry_id' => $enquiry['id'],
            'full_name' => $enquiry['full_name'],
            'email' => $enquiry['email'],
            'phone' => $enquiry['phone'] ?? null,
            'interest' => $enquiry['interest'] ?? null,
            'status' => 'scheduled',
        ]);

        return $this->model->updateStatus($id, 'accepted');
    }

    /**
     * Reject an enquiry: email the customer and mark the enquiry as rejected.
     */
    public function reject(int $id): bool
    {
        $enquiry = $this->model->find($id);
        if (!$enquiry) {
            return false;
        }

        $this->mail->sendEnquiryRejected($enquiry);

        return $this->model->updateStatus($id, 'rejected');
    }

    /**
     * Email a custom quote (price + message) to the enquiry's customer.
     */
    public function sendQuote(int $id, string $priceText, string $quoteMessage): bool
    {
        $enquiry = $this->model->find($id);
        if (!$enquiry) {
            return false;
        }

        return $this->mail->sendCustomQuote($enquiry, trim($priceText), trim($quoteMessage));
    }
}
