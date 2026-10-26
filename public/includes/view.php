<?php

/**
 * Presentation helpers for public pages. No business logic lives here.
 */

use App\Helpers\Path;

function pq_asset_url(string $path): string
{
    return Path::baseUrl() . ltrim($path, '/');
}

/**
 * Public URL for a stored image/video path. Accepts absolute URLs, site-root
 * paths, and the web-relative paths Upload::store() saves (tolerating stray
 * "public/" or "../" prefixes that older records or hand-typed URLs carry).
 */
function pq_media_url(?string $path): string
{
    $path = trim((string) $path);
    if ($path === '' || preg_match('#^(?:https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
        return $path;
    }

    $path = str_replace('\\', '/', $path);
    $path = preg_replace('#^(?:\./|\.\./)+#', '', $path) ?? $path;
    $path = preg_replace('#^/?(?:public_html|public)/#', '', $path) ?? $path;

    return Path::baseUrl() . ltrim($path, '/');
}

function pq_cover_style(?string $value, string $fallback = 'assets/images/start-here.jpeg'): string
{
    $value = trim((string) $value);
    $fallback = trim($fallback);
    if ($fallback === '' || str_starts_with($fallback, 'var(') || str_starts_with($fallback, '#') || str_starts_with($fallback, 'rgb')) {
        $fallback = 'assets/images/start-here.jpeg';
    }

    if ($value === '' || str_starts_with($value, 'var(') || str_starts_with($value, '#') || str_starts_with($value, 'rgb')) {
        $value = $fallback;
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

function pq_url(string $path = ''): string
{
    $base = Path::baseUrl();
    $path = trim($path);

    if ($path === '' || $path === '/' || $path === 'index.php') {
        return $base;
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '#')) {
        return $path;
    }

    $fragment = '';
    if (str_contains($path, '#')) {
        [$path, $fragment] = explode('#', $path, 2);
        $fragment = '#' . $fragment;
    }

    $query = '';
    if (str_contains($path, '?')) {
        [$path, $query] = explode('?', $path, 2);
        $query = '?' . $query;
    }

    static $appConfig = null;
    $appConfig ??= require Path::config('app.php');
    $prettyUrls = (bool) ($appConfig['pretty_urls'] ?? false);
    if ($prettyUrls) {
        $path = preg_replace('#(?:^|/)index\.php$#', '', $path) ?? $path;
        $path = preg_replace('#\.php$#', '', $path) ?? $path;
    }

    $path = ltrim($path, '/');

    return $base . $path . $query . $fragment;
}

function pq_csrf_field(): string
{
    $token = \App\Helpers\Session::csrfToken();
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}
