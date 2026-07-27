<?php

namespace App\Helpers;

class Sanitizer
{
    public static function string(?string $value): string
    {
        return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
    }

    public static function text(?string $value): string
    {
        return trim((string) $value);
    }
}
