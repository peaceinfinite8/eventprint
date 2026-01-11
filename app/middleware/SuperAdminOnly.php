<?php
require_once __DIR__ . '/../core/auth.php';

class SuperAdminOnly
{
    public function handle(): void
    {
        if (!Auth::check() || !Auth::isSuperAdmin()) {
            $_SESSION['flash'] = ['success' => null, 'error' => 'Hanya super admin yang boleh akses menu ini.'];
            $config = require __DIR__ . '/../config/app.php';
            $baseUrl = rtrim($config['base_url'] ?? '', '/');
            if (empty($baseUrl)) {
                $baseUrl = 'http://localhost/eventprint';
            }
            header('Location: ' . $baseUrl . '/admin/dashboard');
            exit;
        }

        $u = Auth::user();
        if (isset($u['is_active']) && (int) $u['is_active'] === 0) {
            Auth::logout();
            session_start();
            $_SESSION['flash'] = ['success' => null, 'error' => 'Akun nonaktif.'];
            $config = require __DIR__ . '/../config/app.php';
            $baseUrl = rtrim($config['base_url'] ?? '/eventprint/public', '/');
            header('Location: ' . $baseUrl . '/admin/login');
            exit;
        }
    }
}
