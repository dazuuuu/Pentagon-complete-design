<?php

return [
    'env' => $_ENV['APP_ENV'] ?? 'local',
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost', '/'),
    'timezone' => 'Africa/Nairobi',
];
