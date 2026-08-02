<?php

return [
    'host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
    'port' => (int) ($_ENV['SMTP_PORT'] ?? 587),
    'username' => $_ENV['SMTP_USER'] ?? '',
    'password' => $_ENV['SMTP_PASS'] ?? '',
    'encryption' => $_ENV['SMTP_ENCRYPTION'] ?? 'tls',
    'from_email' => $_ENV['MAIL_FROM_EMAIL'] ?? 'noreply@pentagonquest.com',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Pentagon Safaris',
    'admin_email' => $_ENV['MAIL_ADMIN_EMAIL'] ?? 'info@pentagonquest.com',
];
