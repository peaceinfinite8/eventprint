<?php
// app/core/Auth.php

class Auth
{
    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::ensureSession();
        return !empty($_SESSION['user']);
    }

    public static function user(): ?array
    {
        self::ensureSession();
        return $_SESSION['user'] ?? null;
    }

    public static function login(array $user): void
    {
        self::ensureSession();

        $_SESSION['user'] = [
            'id'        => isset($user['id']) ? (int)$user['id'] : null,
            'name'      => $user['name']  ?? ($user['email'] ?? ''),
            'email'     => $user['email'] ?? '',
            'role'      => strtolower($user['role'] ?? 'admin'),
            'is_active' => isset($user['is_active']) ? (int)$user['is_active'] : 1,
        ];
    }

    public static function logout(): void
    {
        self::ensureSession();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        @session_destroy();
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        if (!$user) return false;

        $role = strtolower($user['role'] ?? '');
        return in_array($role, ['admin', 'super_admin'], true);
    }

    public static function isSuperAdmin(): bool
    {
        $user = self::user();
        if (!$user) return false;

        return strtolower($user['role'] ?? '') === 'super_admin';
    }
}
