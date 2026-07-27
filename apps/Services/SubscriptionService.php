<?php

namespace App\Services;

use App\Helpers\Validator;
use App\Models\Subscriber;
use PDOException;

class SubscriptionService
{
    private Subscriber $model;
    private MailService $mail;

    public function __construct()
    {
        $this->model = new Subscriber();
        $this->mail = new MailService();
    }

    public function subscribe(string $email): array
    {
        $email = trim($email);
        if (!Validator::email($email)) {
            return ['success' => false, 'errors' => ['email' => 'Please provide a valid email address.']];
        }

        try {
            $existing = $this->model->findByEmail($email);
            if ($existing) {
                return ['success' => false, 'errors' => ['email' => 'This email is already subscribed.']];
            }

            $this->model->create($email);
            $this->mail->sendSubscriptionWelcome($email);

            return ['success' => true, 'message' => 'Thank you for subscribing!'];
        } catch (PDOException $e) {
            return ['success' => false, 'errors' => ['general' => 'Unable to subscribe. Please try again later.']];
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

    public function unsubscribe(int $id): bool
    {
        return $this->model->unsubscribe($id);
    }
}
