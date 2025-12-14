<?php
require_once __DIR__ . '/../core/Auth.php';

class AdminOnly
{
    public function handle(): void
    {
        if (!Auth::check() || !Auth::isAdmin()) {
            $_SESSION['flash'] = ['success' => null, 'error' => 'Akses ditolak.'];
            $config  = require __DIR__ . '/../config/app.php';
            $baseUrl = rtrim($config['base_url'] ?? '/eventprint/public', '/');
            header('Location: ' . $baseUrl . '/admin/dashboard');
            exit;
        }

        $u = Auth::user();
        if (isset($u['is_active']) && (int)$u['is_active'] === 0) {
            Auth::logout();
            session_start();
            $_SESSION['flash'] = ['success' => null, 'error' => 'Akun nonaktif.'];
            $config  = require __DIR__ . '/../config/app.php';
            $baseUrl = rtrim($config['base_url'] ?? '/eventprint/public', '/');
            header('Location: ' . $baseUrl . '/admin/login');
            exit;
        }
    }
}
