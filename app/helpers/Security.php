<?php
// app/helpers/Security.php

class Security
{
    // Satu sumber kebenaran untuk nama key di session
    private const SESSION_KEY = '_csrf_token';

    /**
     * Ambil CSRF token saat ini (atau generate kalau belum ada).
     */
    public static function csrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Validasi CSRF token untuk request POST.
     * Lempar Exception kalau token tidak valid / kosong.
     */
    public static function requireCsrfToken(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // cuma jaga POST, GET dibiarkan
            return;
        }

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? '';
        $postedToken  = $_POST['_token'] ?? '';

        // gagal kalau:
        // - session token kosong
        // - token dari form kosong
        // - atau tidak sama
        if (!$sessionToken || !$postedToken || !hash_equals($sessionToken, $postedToken)) {
            throw new Exception('CSRF token tidak valid atau sesi kadaluarsa.');
        }
    }
}
