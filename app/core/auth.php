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

    /**
     * Check if ANY admin user is logged in
     */
    public static function check(): bool
    {
        self::ensureSession();
        // Check for specific role sessions
        // If super_admin OR admin is logged in, return true
        return !empty($_SESSION['auth_role_super_admin']) || !empty($_SESSION['auth_role_admin']);
    }

    /**
     * Get currently logged in user
     * Priority: Super Admin > Admin
     */
    public static function user(): ?array
    {
        self::ensureSession();

        // Return Super Admin if logged in
        if (!empty($_SESSION['auth_role_super_admin'])) {
            return $_SESSION['auth_role_super_admin'];
        }

        // Return Admin if logged in
        if (!empty($_SESSION['auth_role_admin'])) {
            return $_SESSION['auth_role_admin'];
        }

        return null;
    }

    /**
     * Login user into role-specific session
     */
    public static function login(array $user): void
    {
        self::ensureSession();

        $role = strtolower($user['role'] ?? 'admin');

        // Prepare session data
        $sessionData = [
            'id' => isset($user['id']) ? (int) $user['id'] : null,
            'name' => $user['name'] ?? ($user['email'] ?? ''),
            'email' => $user['email'] ?? '',
            'role' => $role,
            'is_active' => isset($user['is_active']) ? (int) $user['is_active'] : 1,
        ];

        // Store based on role to allow concurrent sessions
        if ($role === 'super_admin') {
            $_SESSION['auth_role_super_admin'] = $sessionData;
        } else {
            // Default to admin key for 'admin' or other roles
            $_SESSION['auth_role_admin'] = $sessionData;
        }

        // LEGACY COMPATIBILITY: Keep 'user' key for now if needed by other parts, 
        // OR better: remove it to force use of correct role.
        // Given request for concurrent sessions, 'user' key is ambiguous.
        // We will only use role-specific keys.
        // However, if we need 'check()' to work, we updated check().
    }

    /**
     * Logout - Clear ALL admin sessions
     */
    public static function logout(): void
    {
        self::ensureSession();

        // Clear specific role sessions
        unset($_SESSION['auth_role_super_admin']);
        unset($_SESSION['auth_role_admin']);

        // Also clear legacy if it exists
        unset($_SESSION['user']);
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        if (!$user)
            return false;

        $role = strtolower($user['role'] ?? '');
        return in_array($role, ['admin', 'super_admin'], true);
    }

    public static function isSuperAdmin(): bool
    {
        // Explicitly check the super_admin session OR the current user context
        if (!empty($_SESSION['auth_role_super_admin']))
            return true;

        $user = self::user();
        if (!$user)
            return false;

        return strtolower($user['role'] ?? '') === 'super_admin';
    }
}
