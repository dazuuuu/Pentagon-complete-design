<?php

/**
 * Outgoing email (SMTP) — THE single place to edit for mail credentials.
 *
 * No .env file is used anywhere in this app. Open this file directly on
 * your server and fill in your live SMTP details (Gmail app password,
 * SendGrid, your host's mail server, etc).
 */

return [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => '',
    'password' => '',
    'encryption' => 'tls',
    'from_email' => 'info@pentagonsafaris.com',
    'from_name' => 'Pentagon Safaris',
    'admin_email' => 'info@pentagonsafaris.com',
];
