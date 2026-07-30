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
        $subject = 'New Trip Enquiry — ' . $data['full_name'];

        $rows = $this->detailRow('Name', htmlspecialchars($data['full_name']));
        $rows .= $this->detailRow('Email', htmlspecialchars($data['email'] ?? 'Not provided'));
        if (!empty($data['phone'])) {
            $rows .= $this->detailRow('Phone', htmlspecialchars($data['phone']));
        }
        if (!empty($data['interest'])) {
            $rows .= $this->detailRow('Interested in', htmlspecialchars($data['interest']));
        }

        $content = '<p style="margin:0 0 20px;">A new trip enquiry just came in through the website.</p>'
            . $this->detailTable($rows);

        if (!empty($data['message'])) {
            $content .= '<p style="margin:24px 0 6px; font-weight:700; color:#1A1A1A;">Message</p>'
                . '<p style="margin:0; color:#444; line-height:1.6;">' . nl2br(htmlspecialchars($data['message'])) . '</p>';
        }

        return $this->send($this->config['admin_email'], $subject, $this->template('New Trip Enquiry', $content));
    }

    public function sendEnquiryConfirmation(array $data): bool
    {
        $subject = 'We received your enquiry — Pentagon Quest';
        $content = sprintf(
            '<p style="margin:0 0 16px;">Hi %s,</p>'
            . '<p style="margin:0 0 16px; line-height:1.6;">Thank you for reaching out to Pentagon Quest. We have received your trip enquiry and one of our travel specialists will be in touch with you within 24 hours.</p>'
            . '<p style="margin:0 0 16px; line-height:1.6;">In the meantime, feel free to browse our tours and destinations for inspiration.</p>',
            htmlspecialchars($data['full_name'])
        );

        return $this->send(
            $data['email'],
            $subject,
            $this->template('Thank You for Reaching Out', $content, ['label' => 'Explore Our Tours', 'url' => $this->siteUrl('tours.php')])
        );
    }

    public function sendEnquiryAccepted(array $data, ?string $scheduledDate = null): bool
    {
        $subject = 'Your trip enquiry has been accepted!';
        $interestLine = !empty($data['interest'])
            ? sprintf(' for <strong>%s</strong>', htmlspecialchars($data['interest']))
            : '';

        $content = sprintf(
            '<p style="margin:0 0 16px;">Hi %s,</p>'
            . '<p style="margin:0 0 16px; line-height:1.6;">Great news — your enquiry%s has been accepted by our travel specialists. We will be in touch shortly to finalise your travel arrangements.</p>',
            htmlspecialchars($data['full_name']),
            $interestLine
        );

        if ($scheduledDate) {
            $content .= $this->infoBox('Scheduled Date', htmlspecialchars(date('F j, Y', strtotime($scheduledDate))));
        }

        return $this->send($data['email'], $subject, $this->template('Enquiry Accepted', $content));
    }

    /**
     * Notify a client when their trip's status or scheduled date changes
     * (set from the Clients CRM page).
     */
    public function sendClientStatusUpdate(array $client, string $status, ?string $scheduledDate = null): bool
    {
        $labels = [
            'scheduled' => 'scheduled',
            'completed' => 'marked as completed',
            'cancelled' => 'cancelled',
        ];
        $label = $labels[$status] ?? $status;

        $subject = 'Update on your Pentagon Quest trip';
        $interestLine = !empty($client['interest'])
            ? sprintf(' for <strong>%s</strong>', htmlspecialchars($client['interest']))
            : '';

        $content = sprintf(
            '<p style="margin:0 0 16px;">Hi %s,</p>'
            . '<p style="margin:0 0 16px; line-height:1.6;">Your trip%s has been %s.</p>',
            htmlspecialchars($client['full_name']),
            $interestLine,
            $label
        );

        if ($status === 'scheduled' && $scheduledDate) {
            $content .= $this->infoBox('Scheduled Date', htmlspecialchars(date('F j, Y', strtotime($scheduledDate))));
        }

        return $this->send($client['email'] ?? null, $subject, $this->template('Trip Update', $content));
    }

    public function sendEnquiryRejected(array $data): bool
    {
        $subject = 'Update on your enquiry — Pentagon Quest';
        $content = sprintf(
            '<p style="margin:0 0 16px;">Hi %s,</p>'
            . '<p style="margin:0 0 16px; line-height:1.6;">Thank you for your interest in Pentagon Quest. Unfortunately we are unable to accommodate your enquiry at this time.</p>'
            . '<p style="margin:0; line-height:1.6;">Please feel free to reach out again or explore our other tours and destinations — we would love to help you plan a future trip.</p>',
            htmlspecialchars($data['full_name'])
        );

        return $this->send(
            $data['email'],
            $subject,
            $this->template('Enquiry Update', $content, ['label' => 'View Other Tours', 'url' => $this->siteUrl('tours.php')])
        );
    }

    public function sendCustomQuote(array $data, string $priceText, string $quoteMessage): bool
    {
        $subject = 'Your Pentagon Quest Quote' . (!empty($data['interest']) ? ' — ' . $data['interest'] : '');
        $content = sprintf('<p style="margin:0 0 16px;">Hi %s,</p>', htmlspecialchars($data['full_name']));

        if (!empty($data['interest'])) {
            $content .= sprintf('<p style="margin:0 0 16px; line-height:1.6;">Here is your personalised quote for <strong>%s</strong>.</p>', htmlspecialchars($data['interest']));
        }
        if ($priceText !== '') {
            $content .= $this->infoBox('Quoted Price', htmlspecialchars($priceText));
        }
        if ($quoteMessage !== '') {
            $content .= '<p style="margin:20px 0 0; line-height:1.6; color:#444;">' . nl2br(htmlspecialchars($quoteMessage)) . '</p>';
        }

        return $this->send($data['email'], $subject, $this->template('Your Travel Quote', $content));
    }

    public function sendSubscriptionWelcome(string $email): bool
    {
        $subject = 'Welcome to Pentagon Quest';
        $content = '<p style="margin:0 0 16px;">Thank you for subscribing to Pentagon Quest!</p>'
            . '<p style="margin:0; line-height:1.6;">Expect safari inspiration, destination guides, and exclusive travel offers straight to your inbox.</p>';

        return $this->send(
            $email,
            $subject,
            $this->template('Welcome Aboard', $content, ['label' => 'Explore Destinations', 'url' => $this->siteUrl('destinations.php')])
        );
    }

    /**
     * A single highlighted key/value row — used for scheduled dates, quoted
     * prices, and similar callouts inside an email body.
     */
    private function infoBox(string $label, string $value): string
    {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;"><tr>'
            . '<td style="background:#F4F1EA; border-left:4px solid #D4AF37; border-radius:8px; padding:16px 20px;">'
            . '<p style="margin:0; font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#888; font-weight:700;">' . $label . '</p>'
            . '<p style="margin:4px 0 0; font-size:16px; color:#1A1A1A; font-weight:700;">' . $value . '</p>'
            . '</td></tr></table>';
    }

    private function detailRow(string $label, string $value): string
    {
        return '<tr>'
            . '<td style="padding:10px 16px; border-bottom:1px solid #eee; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:#888; white-space:nowrap; vertical-align:top;">' . $label . '</td>'
            . '<td style="padding:10px 16px; border-bottom:1px solid #eee; font-size:14px; color:#1A1A1A;">' . $value . '</td>'
            . '</tr>';
    }

    private function detailTable(string $rows): string
    {
        return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F9F9F7; border-radius:8px; overflow:hidden;">' . $rows . '</table>';
    }

    private function siteUrl(string $path): string
    {
        $base = rtrim($_ENV['APP_URL'] ?? '', '/');
        return $base !== '' ? $base . '/' . ltrim($path, '/') : $path;
    }

    /**
     * Wrap a content fragment in the branded email shell: logo header, accent
     * stripe, white content card, dark footer with contact details, and an
     * optional call-to-action button.
     */
    private function template(string $heading, string $contentHtml, ?array $cta = null): string
    {
        $ctaHtml = '';
        if ($cta) {
            $ctaHtml = '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 0;"><tr><td style="border-radius:30px; background:#D4AF37;">'
                . '<a href="' . htmlspecialchars($cta['url']) . '" style="display:inline-block; padding:14px 32px; font-size:14px; font-weight:700; color:#121212; text-decoration:none; border-radius:30px;">' . htmlspecialchars($cta['label']) . '</a>'
                . '</td></tr></table>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pentagon Quest</title>
</head>
<body style="margin:0; padding:0; background:#F4F1EA; font-family: Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F4F1EA; padding:40px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:100%; background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 10px 30px rgba(0,0,0,0.08);">
          <tr>
            <td style="background:#FAF9F6; padding:28px 40px; text-align:center;">
              <img src="cid:pq-logo" alt="Pentagon Quest" width="150" style="display:inline-block; height:auto; max-width:150px;">
            </td>
          </tr>
          <tr>
            <td style="height:5px; line-height:5px; font-size:0; background:linear-gradient(90deg,#D4AF37 0%,#1B3022 25%,#D4AF37 50%,#121212 75%,#D4AF37 100%);">&nbsp;</td>
          </tr>
          <tr>
            <td style="padding:40px;">
              <p style="margin:0 0 20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:2px; color:#D4AF37;">Pentagon Quest</p>
              <h1 style="margin:0 0 20px; font-size:24px; line-height:1.3; color:#121212; font-family: Georgia, 'Times New Roman', serif;">{$heading}</h1>
              <div style="font-size:15px; color:#333;">
                {$contentHtml}
              </div>
              {$ctaHtml}
            </td>
          </tr>
          <tr>
            <td style="background:#121212; padding:32px 40px; text-align:center;">
              <p style="margin:0 0 8px; color:#D4AF37; font-weight:700; font-size:14px;">Pentagon Quest Tours &amp; Safaris</p>
              <p style="margin:0 0 6px; color:rgba(255,255,255,0.65); font-size:12px;">Nairobi, Kenya &middot; Offices in Nairobi, Eldoret and Kericho</p>
              <p style="margin:0 0 6px; color:rgba(255,255,255,0.65); font-size:12px;">+254 718 620982 &middot; +254 726 528015</p>
              <p style="margin:0; color:rgba(255,255,255,0.65); font-size:12px;">pentagonquest@gmail.com &middot; www.pentagonquest.com</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
    }

    private function send(?string $to, string $subject, string $body): bool
    {
        if (empty($to) || empty($this->config['username']) || empty($this->config['password'])) {
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

            // The From address must be the authenticated SMTP account (or an
            // alias Gmail is allowed to send as) — otherwise Gmail's outbound
            // spoofing protection lets the send() call report success while
            // the message is silently dropped or spam-filtered downstream.
            // The branded address (e.g. noreply@pentagonquest.com) is kept
            // as the Reply-To so replies still land in the right inbox.
            $mail->setFrom($this->config['username'], $this->config['from_name']);
            if (!empty($this->config['from_email']) && $this->config['from_email'] !== $this->config['username']) {
                $mail->addReplyTo($this->config['from_email'], $this->config['from_name']);
            }
            $mail->addAddress($to);

            // Embed the logo as a CID attachment (rather than linking to it)
            // so it renders reliably in every client's inbox, regardless of
            // whether the site is publicly hosted yet.
            $logoPath = Path::publicPath('assets', 'images', 'logo.png');
            if (is_file($logoPath)) {
                $mail->addEmbeddedImage($logoPath, 'pq-logo', 'logo.png');
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = trim(strip_tags(str_replace(['</p>', '<br>', '<br/>', '<br />'], "\n", $body)));

            return $mail->send();
        } catch (MailerException) {
            return false;
        }
    }
}
