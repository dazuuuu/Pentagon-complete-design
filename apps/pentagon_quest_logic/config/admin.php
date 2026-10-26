<?php

/**
 * Default admin name/email shown as pre-filled suggestions on the /setup
 * install wizard. The actual admin password is never stored in a config
 * file — it's hashed straight into the `admins` database table.
 */

return [
    'name' => 'Site Admin',
    'email' => 'admin@pentagonsafaris.com',
];
