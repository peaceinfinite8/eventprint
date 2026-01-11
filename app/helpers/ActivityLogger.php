<?php
// app/helpers/ActivityLogger.php

class ActivityLogger
{
    /**
     * Log aktivitas ke table activity_logs
     *
     * Kolom yang dipakai (dari screenshot lu):
     * - level (info|warning|error)
     * - source (system|admin|auth|...)
     * - message (string)
     * - context (JSON string)
     * - created_at (auto / default / diisi)
     */
    public static function log(
        string $source,
        string $message,
        array $context = [],
        string $level = 'info'
    ): void {
        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $actor = self::detectActor();

            $meta = [
                'actor' => $actor,
                'ip' => self::clientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'path' => $_SERVER['REQUEST_URI'] ?? null,
                'method' => $_SERVER['REQUEST_METHOD'] ?? null,
            ];

            $payload = array_merge($meta, ['data' => $context]);

            $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // db() harus sudah ada di project lu (lu pake di HomeController)
            $db = db();

            // created_at: kalau table lu auto, boleh di-skip.
            // Tapi aman kita isi NOW() biar konsisten.
            $sql = "INSERT INTO activity_logs (`level`,`source`,`message`,`context`,`created_at`)
                    VALUES (?, ?, ?, ?, NOW())";

            $stmt = $db->prepare($sql);
            if (!$stmt) return;

            $stmt->bind_param("ssss", $level, $source, $message, $json);
            $stmt->execute();
            $stmt->close();
        } catch (\Throwable $e) {
            // Jangan sampai logging bikin request gagal
            error_log("[ActivityLogger] " . $e->getMessage());
        }
    }

    public static function info(string $source, string $message, array $context = []): void
    {
        self::log($source, $message, $context, 'info');
    }

    public static function warning(string $source, string $message, array $context = []): void
    {
        self::log($source, $message, $context, 'warning');
    }

    public static function error(string $source, string $message, array $context = []): void
    {
        self::log($source, $message, $context, 'error');
    }

    /**
     * Deteksi actor dari session (fallback multi-key).
     * Ini yang bikin user_id nggak null lagi kalau session lu ada.
     */
    private static function detectActor(): array
    {
        // Beberapa pola umum yang sering dipakai
        $candidates = [
            $_SESSION['user'] ?? null,
            $_SESSION['auth'] ?? null,
            $_SESSION['auth_user'] ?? null,
            $_SESSION['admin'] ?? null,
            $_SESSION['admin_user'] ?? null,
            $_SESSION['current_user'] ?? null,
        ];

        $picked = null;
        foreach ($candidates as $c) {
            if (is_array($c) && !empty($c)) {
                $picked = $c;
                break;
            }
        }

        // Kalau sessionnya object (kadang ada)
        if (!$picked) {
            foreach ($candidates as $c) {
                if (is_object($c)) {
                    $picked = (array)$c;
                    break;
                }
            }
        }

        $id = null;
        $email = null;
        $username = null;
        $role = null;

        if (is_array($picked)) {
            $id = $picked['id'] ?? ($picked['user_id'] ?? null);
            $email = $picked['email'] ?? null;
            $username = $picked['username'] ?? ($picked['name'] ?? null);
            $role = $picked['role'] ?? null;
        }

        return [
            'user_id' => $id !== null ? (int)$id : null,
            'email' => is_string($email) ? $email : null,
            'username' => is_string($username) ? $username : null,
            'role' => is_string($role) ? $role : null,
        ];
    }

    private static function clientIp(): ?string
    {
        $keys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];

        foreach ($keys as $k) {
            if (!empty($_SERVER[$k])) {
                $ip = $_SERVER[$k];

                // X_FORWARDED_FOR bisa berisi list
                if ($k === 'HTTP_X_FORWARDED_FOR' && strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }

                return is_string($ip) ? $ip : null;
            }
        }
        return null;
    }
}
