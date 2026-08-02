<?php

namespace App\Core;

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::startSession();
        return !empty($_SESSION['admin_id']);
    }

    public static function id(): ?int
    {
        self::startSession();
        return isset($_SESSION['admin_id']) ? (int) $_SESSION['admin_id'] : null;
    }

    public static function login(int $adminId, string $name): void
    {
        self::startSession();
        $_SESSION['admin_id'] = $adminId;
        $_SESSION['admin_name'] = $name;
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function name(): string
    {
        self::startSession();
        return $_SESSION['admin_name'] ?? 'Admin';
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: login.php');
            exit;
        }
    }
}
