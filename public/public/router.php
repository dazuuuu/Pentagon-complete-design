<?php

/**
 * Compatibility router for accidentally nested dev-server commands.
 *
 * If the server is started from public/ with:
 *   php -S 127.0.0.1:8080 -t public public/router.php
 * PHP looks for public/public/router.php. This shim hands control back to the
 * real router one level up.
 */
require dirname(__DIR__) . '/router.php';
