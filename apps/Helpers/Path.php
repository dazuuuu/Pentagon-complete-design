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
}
