<?php
// app/middleware/AuthRequired.php

require_once __DIR__ . '/../core/Auth.php';

class AuthRequired
{
    public function handle(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (Auth::check()) {
            return;
        }

        $config  = require __DIR__ . '/../config/app.php';
        $baseUrl = rtrim($config['base_url'] ?? '/eventprint/public', '/');

        // pakai flash yang dipakai layout/main.php
        $_SESSION['flash'] = [
            'success' => null,
            'error'   => 'Silakan login terlebih dahulu.',
        ];

        header('Location: ' . $baseUrl . '/admin/login');
        exit;
    }
}
