<?php

/**
 * Presentation helpers for public pages. No business logic lives here.
 */

use App\Helpers\Path;

function pq_cover_style(?string $value, string $fallback = 'var(--green)'): string
{
    $value = trim((string) $value);
    if ($value === '') {
        return 'background: ' . $fallback;
    }
    if (str_starts_with($value, 'var(') || str_starts_with($value, '#') || str_starts_with($value, 'rgb')) {
        return 'background: ' . $value;
    }

    $url = str_starts_with($value, 'http') ? $value : Path::baseUrl() . ltrim($value, '/');
    return "background: url('" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "') center/cover no-repeat";
}

function pq_format_date(?string $date): string
{
    if ($date === null || $date === '') {
        return '';
    }
    $ts = strtotime($date);
    return $ts ? date('F j, Y', $ts) : $date;
}

function pq_csrf_field(): string
{
    $token = \App\Helpers\Session::csrfToken();
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}
