<?php

/**
 * Compatibility router for accidentally nested dev-server commands.
 *
 * If the server is started from public/ with:
 *   php -S localhost:8000 -t public public/router.php
 * PHP looks for public/public/router.php. This shim hands control back to the
 * real router one level up.
 */
return require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'router.php';
