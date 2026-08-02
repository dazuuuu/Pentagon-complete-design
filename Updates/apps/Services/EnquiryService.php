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

    /**
     * Accepts either the classic separate `email` (+ optional `phone`) fields,
     * or a single combined `contact` field (used by the site-wide "Send a Trip
     * Request" form, where a customer may enter an email OR a phone number).
     * `destination` + `travel_type` are folded into `interest` when no explicit
     * `interest` override was already provided (e.g. a Safari Tier name).
     */
    public function submit(array $data): array
    {
        $errors = Validator::required($data, ['full_name']);

        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $contact = trim($data['contact'] ?? '');

        if ($contact !== '') {
            if (Validator::email($contact)) {
                $email = $email !== '' ? $email : $contact;
            } else {
                $phone = $phone !== '' ? $phone : $contact;
            }
        }

        if ($email === '' && $phone === '') {
            $errors['contact'] = 'Please provide an email address or phone number.';
        } elseif ($email !== '' && !Validator::email($email)) {
            $errors['email'] = 'Please provide a valid email address.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $interest = trim($data['interest'] ?? '');
        if ($interest === '') {
            $interest = implode(' — ', array_filter([trim($data['destination'] ?? ''), trim($data['travel_type'] ?? '')]));
        }

        try {
            $id = $this->model->create([
                'full_name' => trim($data['full_name']),
                'email' => $email !== '' ? $email : null,
                'phone' => $phone !== '' ? $phone : null,
                'interest' => $interest,
                'offer_id' => (int) ($data['offer_id'] ?? 0) ?: null,
                'message' => trim($data['message'] ?? ''),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'email_sent' => 0,
            ]);

            $mailData = [
                'full_name' => trim($data['full_name']),
                'email' => $email,
                'phone' => $phone,
                'interest' => $interest,
                'message' => trim($data['message'] ?? ''),
            ];

            if ($email !== '') {
                $sent = $this->mail->sendEnquiryNotification($mailData);
                if ($sent) {
                    $this->model->markEmailSent($id);
                }
                $this->mail->sendEnquiryConfirmation($mailData);
            } else {
                $notifyData = $mailData;
                $notifyData['email'] = 'Not provided — phone: ' . $phone;
                $sent = $this->mail->sendEnquiryNotification($notifyData);
                if ($sent) {
                    $this->model->markEmailSent($id);
                }
            }

            return ['success' => true, 'message' => 'Enquiry sent successfully.'];
        } catch (PDOException) {
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
     * Accept an enquiry: email the customer (including the scheduled date,
     * when given), convert them into a scheduled client record, and mark the
     * enquiry as accepted.
     */
    public function accept(int $id, ?string $scheduledDate = null): bool
    {
        $enquiry = $this->model->find($id);
        if (!$enquiry) {
            return false;
        }

        $this->mail->sendEnquiryAccepted($enquiry, $scheduledDate);

        $this->clients->create([
            'enquiry_id' => $enquiry['id'],
            'full_name' => $enquiry['full_name'],
            'email' => $enquiry['email'],
            'phone' => $enquiry['phone'] ?? null,
            'interest' => $enquiry['interest'] ?? null,
            'status' => 'scheduled',
            'scheduled_date' => $scheduledDate,
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
