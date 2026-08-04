<?php

namespace App\Helpers;

class Path
{
    private static ?string $root = null;

    /**
     * Project root directory (pentagon-quest/).
     */
    public static function root(): string
    {
        if (self::$root === null) {
            // apps/Helpers/Path.php -> go up two levels to project root
            self::$root = realpath(dirname(__DIR__, 2)) ?: dirname(__DIR__, 2);
        }

        return self::$root;
    }

    /**
     * Join path segments using the OS directory separator.
     */
    public static function join(string ...$segments): string
    {
        $path = self::root();

        foreach ($segments as $segment) {
            $segment = trim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $segment), DIRECTORY_SEPARATOR);
            if ($segment !== '') {
                $path .= DIRECTORY_SEPARATOR . $segment;
            }
        }

        return $path;
    }

    public static function apps(string ...$segments): string
    {
        return self::join('apps', ...$segments);
    }

    public static function config(string $file = ''): string
    {
        return $file === '' ? self::apps('config') : self::apps('config', $file);
    }

    public static function database(string ...$segments): string
    {
        return self::join('apps', 'database', ...$segments);
    }

    public static function includes(string ...$segments): string
    {
        return self::join('includes', ...$segments);
    }

    public static function vendor(string $file = ''): string
    {
        return $file === '' ? self::join('vendor') : self::join('vendor', $file);
    }

    public static function env(): string
    {
        return self::join('.env');
    }

    public static function publicPath(string ...$segments): string
    {
        return self::join(...$segments);
    }

    /**
     * Absolute, site-root-relative base URL path — e.g. "/" when the app is
     * hosted at a domain root, or "/pentagon-quest/" when it lives in a
     * subdirectory (as it does locally). Always ends with a trailing slash.
     *
     * Computed from SCRIPT_NAME (the internally-rewritten script, which
     * always lives in the same flat project directory) rather than the
     * request path, so it stays correct for clean URLs with extra segments
     * such as /tours/12 — a link built relative to that path would otherwise
     * resolve against the "/12" segment and 404.
     */
    public static function baseUrl(): string
    {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $scriptDir = rtrim(str_replace('\\', '/', $scriptDir), '/');

        return $scriptDir . '/';
    }
}
