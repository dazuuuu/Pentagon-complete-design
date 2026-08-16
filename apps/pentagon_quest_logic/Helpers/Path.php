<?php

namespace App\Helpers;

class Path
{
    private static ?string $root = null;

    /**
     * Project root directory (contains apps/ and publics/).
     */
    public static function root(): string
    {
        if (self::$root === null) {
            // apps/pentagon_quest_logic/Helpers/Path.php -> three levels up
            self::$root = realpath(dirname(__DIR__, 3)) ?: dirname(__DIR__, 3);
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

    public static function logic(string ...$segments): string
    {
        return self::join('apps', 'pentagon_quest_logic', ...$segments);
    }

    /**
     * Alias for the backend package directory.
     */
    public static function apps(string ...$segments): string
    {
        return self::logic(...$segments);
    }

    public static function config(string $file = ''): string
    {
        return $file === '' ? self::logic('config') : self::logic('config', $file);
    }

    public static function database(string ...$segments): string
    {
        return self::logic('database', ...$segments);
    }

    public static function updates(string ...$segments): string
    {
        return self::logic('updates', ...$segments);
    }

    public static function includes(string ...$segments): string
    {
        return self::join('publics', 'includes', ...$segments);
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
        return self::join('publics', ...$segments);
    }

    /**
     * Absolute, site-root-relative base URL path.
     * Always ends with a trailing slash.
     *
     * Strips /publics and /admin from SCRIPT_NAME so asset URLs stay correct
     * whether the document root is the repo or the publics/ folder.
     */
    public static function baseUrl(): string
    {
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $scriptDir = str_replace('\\', '/', $scriptDir);
        $scriptDir = preg_replace('#(?:^|/)publics(?:/admin)?$#', '', $scriptDir) ?? $scriptDir;
        $scriptDir = preg_replace('#(?:^|/)admin$#', '', $scriptDir) ?? $scriptDir;
        $scriptDir = rtrim($scriptDir, '/');

        return $scriptDir . '/';
    }
}
