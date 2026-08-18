<?php

namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    /**
     * Lazy database handle so pages can fall back when MySQL is unavailable.
     */
    public function __get(string $name): mixed
    {
        if ($name === 'db') {
            return Database::connection();
        }

        throw new \Error('Undefined property ' . static::class . '::$' . $name);
    }
}
