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
        $body = '<h2>New Enquiry</h2>'
            . '<p><strong>Name:</strong> ' . htmlspecialchars($data['full_name']) . '</p>'
            . '<p><strong>Email:</strong> ' . htmlspecialchars($data['email']) . '</p>';

        if (!empty($data['phone'])) {
            $body .= '<p><strong>Phone:</strong> ' . htmlspecialchars($data['phone']) . '</p>';
        }
        if (!empty($data['interest'])) {
            $body .= '<p><strong>Interested in:</strong> ' . htmlspecialchars($data['interest']) . '</p>';
        }
        if (!empty($data['message'])) {
            $body .= '<p><strong>Message:</strong></p><p>' . nl2br(htmlspecialchars($data['message'])) . '</p>';
        }

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

    public function sendEnquiryAccepted(array $data): bool
    {
        $subject = 'Your safari enquiry has been accepted!';
        $interestLine = !empty($data['interest'])
            ? sprintf('for <strong>%s</strong> ', htmlspecialchars($data['interest']))
            : '';
        $body = sprintf(
            '<p>Hi %s,</p><p>Great news — your enquiry %shas been accepted by our safari specialists. We will be in touch shortly to finalise your travel arrangements.</p><p>Best regards,<br>Pentagon Quest Team</p>',
            htmlspecialchars($data['full_name']),
            $interestLine
        );

        return $this->send($data['email'], $subject, $body);
    }

    public function sendEnquiryRejected(array $data): bool
    {
        $subject = 'Update on your safari enquiry';
        $body = sprintf(
            '<p>Hi %s,</p><p>Thank you for your interest in Pentagon Quest. Unfortunately we are unable to accommodate your enquiry at this time. Please feel free to reach out again or explore our other tours and destinations.</p><p>Best regards,<br>Pentagon Quest Team</p>',
            htmlspecialchars($data['full_name'])
        );

        return $this->send($data['email'], $subject, $body);
    }

    public function sendCustomQuote(array $data, string $priceText, string $quoteMessage): bool
    {
        $subject = 'Your Pentagon Quest Quote' . (!empty($data['interest']) ? ' — ' . $data['interest'] : '');
        $body = sprintf('<p>Hi %s,</p>', htmlspecialchars($data['full_name']));

        if (!empty($data['interest'])) {
            $body .= sprintf('<p><strong>Regarding:</strong> %s</p>', htmlspecialchars($data['interest']));
        }
        if ($priceText !== '') {
            $body .= sprintf('<p><strong>Quoted Price:</strong> %s</p>', htmlspecialchars($priceText));
        }
        if ($quoteMessage !== '') {
            $body .= '<p>' . nl2br(htmlspecialchars($quoteMessage)) . '</p>';
        }

        $body .= '<p>Best regards,<br>Pentagon Quest Team</p>';

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
