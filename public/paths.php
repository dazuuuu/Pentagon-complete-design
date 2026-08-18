<?php

/**
 * Pentagon Quest path handler (web-root copy).
 *
 * This file ships inside public/ and is uploaded as public_html/paths.php.
 * It locates the backend package, which lives outside the document root:
 *
 *   <root>/apps/pentagon_quest_logic/   backend (Composer, vendor, .env)
 *   <root>/public/                      local document root
 *   <root>/public_html/                 hosted document root (contents of public/)
 *
 * Optional overrides:
 *   PQ_APP_PATH      absolute path to pentagon_quest_logic
 *   PQ_PUBLIC_PATH   absolute path to the web root
 */

if (defined('PENTAGON_PATHS_LOADED')) {
    return;
}

define('PENTAGON_PATHS_LOADED', true);

if (!function_exists('pentagon_normalize_dir')) {
    function pentagon_normalize_dir(?string $path): ?string
    {
        if ($path === null || trim($path) === '') {
            return null;
        }

        $real = realpath($path);
        if ($real !== false && is_dir($real)) {
            return $real;
        }

        return is_dir($path) ? rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path), DIRECTORY_SEPARATOR) : null;
    }

    function pentagon_is_logic_dir(string $dir): bool
    {
        return is_file($dir . DIRECTORY_SEPARATOR . 'bootstrap.php')
            && is_file($dir . DIRECTORY_SEPARATOR . 'composer.json');
    }

    function pentagon_is_web_root(string $dir): bool
    {
        return is_file($dir . DIRECTORY_SEPARATOR . 'index.php')
            && (
                is_file($dir . DIRECTORY_SEPARATOR . 'paths.php')
                || is_dir($dir . DIRECTORY_SEPARATOR . 'includes')
                || is_file($dir . DIRECTORY_SEPARATOR . 'router.php')
            );
    }

    /**
     * @param list<string|null> $candidates
     */
    function pentagon_first_existing_dir(array $candidates, callable $isValid): ?string
    {
        $seen = [];

        foreach ($candidates as $candidate) {
            $dir = pentagon_normalize_dir($candidate);
            if ($dir === null || isset($seen[$dir])) {
                continue;
            }
            $seen[$dir] = true;
            if ($isValid($dir)) {
                return $dir;
            }
        }

        return null;
    }

    function pentagon_search_parents(string $start, int $levels = 5): array
    {
        $dirs = [];
        $current = pentagon_normalize_dir($start) ?? $start;

        for ($i = 0; $i <= $levels; $i++) {
            $dirs[] = $current;
            $parent = dirname($current);
            if ($parent === $current) {
                break;
            }
            $current = $parent;
        }

        return $dirs;
    }

    function pentagon_logic_candidates_from(string $origin): array
    {
        $candidates = [];

        foreach (pentagon_search_parents($origin) as $dir) {
            $candidates[] = $dir;
            $candidates[] = $dir . DIRECTORY_SEPARATOR . 'pentagon_quest_logic';
            $candidates[] = $dir . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . 'pentagon_quest_logic';
        }

        return $candidates;
    }

    function pentagon_locate_logic_dir(): string
    {
        if (defined('PENTAGON_LOGIC_DIR')) {
            $existing = pentagon_normalize_dir(PENTAGON_LOGIC_DIR);
            if ($existing !== null && pentagon_is_logic_dir($existing)) {
                return $existing;
            }
        }

        $env = getenv('PQ_APP_PATH');
        $candidates = [];

        if (is_string($env) && $env !== '') {
            array_unshift($candidates, $env);
        }

        $origins = array_filter([
            __DIR__,
            dirname(__DIR__),
            getcwd() ?: null,
            $_SERVER['DOCUMENT_ROOT'] ?? null,
        ]);

        foreach ($origins as $origin) {
            $candidates = array_merge($candidates, pentagon_logic_candidates_from((string) $origin));
        }

        $found = pentagon_first_existing_dir($candidates, 'pentagon_is_logic_dir');
        if ($found !== null) {
            return $found;
        }

        throw new RuntimeException(
            'Cannot locate apps/pentagon_quest_logic. Keep that folder next to public_html (or public), or set PQ_APP_PATH to its absolute path.'
        );
    }

    function pentagon_web_root_candidates_from(string $origin, string $logicDir): array
    {
        $root = dirname($logicDir);
        if (basename($root) === 'apps') {
            $root = dirname($root);
        }

        $named = [];
        foreach (['public_html', 'public', 'www', 'htdocs'] as $name) {
            $named[] = $root . DIRECTORY_SEPARATOR . $name;
            $named[] = dirname($logicDir) . DIRECTORY_SEPARATOR . $name;
        }

        return array_merge([
            getenv('PQ_PUBLIC_PATH') ?: null,
            $_SERVER['DOCUMENT_ROOT'] ?? null,
            __DIR__,
            $origin,
        ], $named);
    }

    function pentagon_locate_web_root(string $logicDir): string
    {
        if (defined('PENTAGON_WEB_ROOT')) {
            $existing = pentagon_normalize_dir(PENTAGON_WEB_ROOT);
            if ($existing !== null && pentagon_is_web_root($existing)) {
                return $existing;
            }
        }

        $docRoot = pentagon_normalize_dir($_SERVER['DOCUMENT_ROOT'] ?? null);
        if ($docRoot !== null && pentagon_is_web_root($docRoot)) {
            return $docRoot;
        }

        $candidates = pentagon_web_root_candidates_from(__DIR__, $logicDir);
        $found = pentagon_first_existing_dir($candidates, 'pentagon_is_web_root');
        if ($found !== null) {
            return $found;
        }

        if (pentagon_is_web_root(__DIR__)) {
            return pentagon_normalize_dir(__DIR__) ?? __DIR__;
        }

        throw new RuntimeException(
            'Cannot locate the public web root (public/ or public_html/). Set PQ_PUBLIC_PATH if the site files are in a custom folder.'
        );
    }

    function pentagon_locate_public_router(): string
    {
        $self = realpath(__FILE__) ?: __FILE__;
        $routerNextToThis = dirname($self) . DIRECTORY_SEPARATOR . 'router.php';

        $candidates = [$routerNextToThis];
        $bases = array_filter([
            __DIR__,
            dirname(__DIR__),
            getcwd() ?: null,
            $_SERVER['DOCUMENT_ROOT'] ?? null,
        ]);

        foreach ($bases as $base) {
            foreach (['public', 'public_html', 'www', 'htdocs'] as $name) {
                $candidates[] = rtrim((string) $base, '/\\') . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'router.php';
            }
            $candidates[] = rtrim((string) $base, '/\\') . DIRECTORY_SEPARATOR . 'router.php';
        }

        $seen = [];
        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real === false || isset($seen[$real]) || !is_file($real)) {
                continue;
            }
            $seen[$real] = true;

            $contents = @file_get_contents($real, false, null, 0, 400) ?: '';
            if (str_contains($contents, 'Front controller for the PHP built-in server')) {
                return $real;
            }
        }

        throw new RuntimeException(
            "Failed opening required 'public/router.php'. Start the server from the project root with `php -S localhost:8000 -t public public/router.php`, or from public/ with `php -S localhost:8000 router.php`."
        );
    }
}

$pentagonLogicDir = pentagon_locate_logic_dir();
$pentagonWebRoot = pentagon_locate_web_root($pentagonLogicDir);

if (!defined('PENTAGON_LOGIC_DIR')) {
    define('PENTAGON_LOGIC_DIR', $pentagonLogicDir);
}

if (!defined('PENTAGON_WEB_ROOT')) {
    define('PENTAGON_WEB_ROOT', $pentagonWebRoot);
}

if (!defined('PENTAGON_PROJECT_ROOT')) {
    $projectRoot = dirname($pentagonLogicDir);
    if (basename($projectRoot) === 'apps') {
        $projectRoot = dirname($projectRoot);
    }
    define('PENTAGON_PROJECT_ROOT', $projectRoot);
}
