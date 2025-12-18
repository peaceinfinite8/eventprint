<?php
// app/helpers/Security.php

class Security
{
    private const KEY = '_csrf_token';

    public static function csrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    public static function requireCsrfToken(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $sessionToken = $_SESSION[self::KEY] ?? '';
    $postedToken  = $_POST['csrf_token'] ?? ($_POST['_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));

    $sessionToken = is_string($sessionToken) ? $sessionToken : '';
    $postedToken  = is_string($postedToken)  ? $postedToken  : '';

    if ($sessionToken === '' || $postedToken === '' || !hash_equals($sessionToken, $postedToken)) {
        throw new Exception('CSRF token tidak valid atau sesi kadaluarsa.');
    }
}

}
