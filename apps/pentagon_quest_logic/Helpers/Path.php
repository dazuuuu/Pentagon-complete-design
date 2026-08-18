<?php

namespace App\Helpers;

/**
 * File path handler for local and hosted layouts.
 *
 * Local:
 *   <project>/apps/pentagon_quest_logic/
 *   <project>/public/
 *
 * Hosting (document root is public_html; app stays outside it):
 *   <home>/apps/pentagon_quest_logic/
 *   <home>/public_html/   ← contents of public/
 */
class Path
{
    private static ?string $root = null;
    private static ?string $logic = null;
    private static ?string $webRoot = null;

    public static function logicDir(): string
    {
        if (self::$logic === null) {
            if (defined('PENTAGON_LOGIC_DIR') && is_dir(PENTAGON_LOGIC_DIR)) {
                self::$logic = rtrim((string) PENTAGON_LOGIC_DIR, '/\\');
            } else {
                $env = getenv('PQ_APP_PATH');
                if (is_string($env) && $env !== '' && is_dir($env)) {
                    self::$logic = realpath($env) ?: rtrim($env, '/\\');
                } else {
                    // Helpers/Path.php → pentagon_quest_logic
                    self::$logic = realpath(dirname(__DIR__)) ?: dirname(__DIR__);
                }
            }
        }

        return self::$logic;
    }

    /**
     * Project root: parent of apps/ (or parent of the logic folder if apps/ is omitted).
     */
    public static function root(): string
    {
        if (self::$root === null) {
            if (defined('PENTAGON_PROJECT_ROOT') && is_dir(PENTAGON_PROJECT_ROOT)) {
                self::$root = rtrim((string) PENTAGON_PROJECT_ROOT, '/\\');
            } else {
                $logic = self::logicDir();
                $parent = dirname($logic);
                self::$root = basename($parent) === 'apps' ? dirname($parent) : $parent;
            }
        }

        return self::$root;
    }

    /**
     * Document root: public/ locally, public_html (or www/htdocs) on hosting.
     */
    public static function webRoot(): string
    {
        if (self::$webRoot === null) {
            if (defined('PENTAGON_WEB_ROOT') && is_dir(PENTAGON_WEB_ROOT)) {
                self::$webRoot = rtrim((string) PENTAGON_WEB_ROOT, '/\\');
                return self::$webRoot;
            }

            $env = getenv('PQ_PUBLIC_PATH');
            if (is_string($env) && $env !== '' && is_dir($env)) {
                self::$webRoot = realpath($env) ?: rtrim($env, '/\\');
                return self::$webRoot;
            }

            $docRootRaw = (string) ($_SERVER['DOCUMENT_ROOT'] ?? '');
            $docRoot = $docRootRaw !== '' ? realpath($docRootRaw) : false;
            if ($docRoot !== false && self::looksLikeWebRoot($docRoot)) {
                self::$webRoot = $docRoot;
                return self::$webRoot;
            }

            $root = self::root();
            $candidates = [
                $root . DIRECTORY_SEPARATOR . 'public_html',
                $root . DIRECTORY_SEPARATOR . 'public',
                $root . DIRECTORY_SEPARATOR . 'www',
                $root . DIRECTORY_SEPARATOR . 'htdocs',
                dirname(self::logicDir()) . DIRECTORY_SEPARATOR . 'public_html',
                dirname(self::logicDir()) . DIRECTORY_SEPARATOR . 'public',
            ];

            foreach ($candidates as $candidate) {
                $real = realpath($candidate);
                if ($real !== false && self::looksLikeWebRoot($real)) {
                    self::$webRoot = $real;
                    return self::$webRoot;
                }
            }

            self::$webRoot = realpath($root . DIRECTORY_SEPARATOR . 'public')
                ?: ($root . DIRECTORY_SEPARATOR . 'public');
        }

        return self::$webRoot;
    }

    private static function looksLikeWebRoot(string $dir): bool
    {
        return is_file($dir . DIRECTORY_SEPARATOR . 'index.php')
            && (
                is_dir($dir . DIRECTORY_SEPARATOR . 'includes')
                || is_file($dir . DIRECTORY_SEPARATOR . 'paths.php')
                || is_file($dir . DIRECTORY_SEPARATOR . 'router.php')
            );
    }

    /**
     * Join path segments onto an absolute base directory.
     */
    public static function append(string $base, string ...$segments): string
    {
        $path = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $base), DIRECTORY_SEPARATOR);

        foreach ($segments as $segment) {
            $segment = trim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $segment), DIRECTORY_SEPARATOR);
            if ($segment !== '') {
                $path .= DIRECTORY_SEPARATOR . $segment;
            }
        }

        return $path;
    }

    /**
     * Join path segments onto the project root.
     */
    public static function join(string ...$segments): string
    {
        return self::append(self::root(), ...$segments);
    }

    public static function logic(string ...$segments): string
    {
        return self::append(self::logicDir(), ...$segments);
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

    /**
     * Complete CREATE TABLE files derived from Models/, parent tables first.
     */
    public static function requiredMigrations(string ...$segments): string
    {
        return self::database('required_migrations', ...$segments);
    }

    public static function updates(string ...$segments): string
    {
        return self::logic('updates', ...$segments);
    }

    public static function includes(string ...$segments): string
    {
        return self::append(self::webRoot(), 'includes', ...$segments);
    }

    public static function vendor(string $file = ''): string
    {
        return $file === '' ? self::logic('vendor') : self::logic('vendor', $file);
    }

    public static function env(): string
    {
        return self::logic('.env');
    }

    public static function publicPath(string ...$segments): string
    {
        return self::append(self::webRoot(), ...$segments);
    }

    /**
     * Site-root-relative base URL path. Always ends with a trailing slash.
     */
    public static function baseUrl(): string
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
        if ($scriptName === '' || $scriptName === 'Standard input code' || !str_starts_with($scriptName, '/')) {
            $scriptName = '/index.php';
        }
        $scriptDir = dirname($scriptName);
        $scriptDir = preg_replace('#/(?:admin|setup|handlers|api)(?:/.*)?$#', '', $scriptDir) ?? $scriptDir;

        $docRootRaw = (string) ($_SERVER['DOCUMENT_ROOT'] ?? '');
        $docRoot = $docRootRaw !== '' ? realpath($docRootRaw) : false;
        $webRoot = realpath(self::webRoot());

        if ($docRoot && $webRoot && $docRoot !== $webRoot && str_starts_with($webRoot, $docRoot)) {
            $prefix = substr($webRoot, strlen(rtrim($docRoot, DIRECTORY_SEPARATOR)));
            $prefix = '/' . trim(str_replace('\\', '/', $prefix), '/');
            return rtrim($prefix, '/') . '/';
        }

        $scriptDir = preg_replace('#/(?:public_html|public|www|htdocs)$#', '', $scriptDir) ?? $scriptDir;
        $scriptDir = rtrim($scriptDir, '/');

        if ($scriptDir === '' || $scriptDir === '.' || $scriptDir === '\\') {
            return '/';
        }

        return $scriptDir . '/';
    }
}
