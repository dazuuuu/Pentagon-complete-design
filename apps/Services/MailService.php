<?php

namespace App\Services;

use App\Helpers\Path;
use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    private array $config;

    public function __construct()
    {
        $this->config = require Path::config('mail.php');
    }

    public function sendEnquiryNotification(array $data): bool
    {
        $subject = 'New Safari Enquiry from ' . $data['full_name'];
        $body = sprintf(
            "<h2>New Enquiry</h2><p><strong>Name:</strong> %s</p><p><strong>Email:</strong> %s</p><p><strong>Message:</strong></p><p>%s</p>",
            htmlspecialchars($data['full_name']),
            htmlspecialchars($data['email']),
            nl2br(htmlspecialchars($data['message']))
        );

        return $this->send($this->config['admin_email'], $subject, $body);
    }

    public function sendEnquiryConfirmation(array $data): bool
    {
        $subject = 'We received your safari enquiry';
        $body = sprintf(
            '<p>Hi %s,</p><p>Thank you for contacting Pentagon Quest. Our safari specialists will get back to you within 24 hours.</p><p>Best regards,<br>Pentagon Quest Team</p>',
            htmlspecialchars($data['full_name'])
        );

        return $this->send($data['email'], $subject, $body);
    }

    public function sendSubscriptionWelcome(string $email): bool
    {
        $subject = 'Welcome to Pentagon Quest Newsletter';
        $body = '<p>Thank you for subscribing to Pentagon Quest!</p><p>Expect safari inspiration and exclusive offers in your inbox.</p>';

        return $this->send($email, $subject, $body);
    }

    private function send(string $to, string $subject, string $body): bool
    {
        if (empty($this->config['username']) || empty($this->config['password'])) {
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = $this->config['encryption'];
            $mail->Port = $this->config['port'];

            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            return $mail->send();
        } catch (MailerException) {
            return false;
        }
    }
}
