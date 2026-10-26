<?php

/**
 * General app settings. Edit directly when hosting — no .env file is used.
 */

return [
    'env' => 'local',
    'url' => 'http://localhost',
    'timezone' => 'Africa/Nairobi',
    'pretty_urls' => false,

    // Secret key for /migrate.php (the no-login DB diagnostic + migration
    // runner). Change this to your own value when hosting, then visit
    // https://yoursite.com/migrate.php?key=<this value>
    'migrate_key' => 'pq-migrate-2026',
];
