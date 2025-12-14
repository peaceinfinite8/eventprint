<?php
// app/core/Controller.php

class Controller
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function baseUrl(string $path = ''): string
    {
        $base = rtrim($this->config['base_url'] ?? '', '/');
        return $base . '/' . ltrim($path, '/');
    }

    protected function setFlash(string $type, string $message): void
    {
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = ['success' => null, 'error' => null];
        }
        if ($type === 'success') $_SESSION['flash']['success'] = $message;
        if ($type === 'error')   $_SESSION['flash']['error'] = $message;
    }

    protected function pullFlash(): array
    {
        // dukung format baru
        $flash = $_SESSION['flash'] ?? ['success' => null, 'error' => null];

        // dukung format lama (yang lu pakai di banyak file)
        if (!empty($_SESSION['flash_success'])) $flash['success'] = $_SESSION['flash_success'];
        if (!empty($_SESSION['flash_error']))   $flash['error']   = $_SESSION['flash_error'];

        unset($_SESSION['flash'], $_SESSION['flash_success'], $_SESSION['flash_error']);
        return $flash;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function redirectWithSuccess(string $pathOrUrl, string $message): void
    {
        $this->setFlash('success', $message);
        $this->redirect($this->normalizeUrl($pathOrUrl));
    }

    protected function redirectWithError(string $pathOrUrl, string $message): void
    {
        $this->setFlash('error', $message);
        $this->redirect($this->normalizeUrl($pathOrUrl));
    }

    private function normalizeUrl(string $pathOrUrl): string
    {
        // kalau sudah full url, biarkan
        if (preg_match('#^https?://#i', $pathOrUrl)) return $pathOrUrl;

        // kalau sudah diawali /eventprint/public..., biarkan
        if (str_starts_with($pathOrUrl, '/')) {
            return rtrim($this->config['base_url'] ?? '', '/') . $pathOrUrl;
        }

        // kalau cuma "admin/users" dll
        return $this->baseUrl($pathOrUrl);
    }

    protected function renderAdmin(string $view, array $data = [], string $title = '')
    {
        if ($title !== '') {
            $data['title'] = $title;
        }

        $data['baseUrl'] = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
        $data['flash']   = $this->pullFlash(); // <-- penting: ambil flash dari session

        $viewFile = __DIR__ . '/../../views/admin/' . $view . '.php';
        $view     = $viewFile;
        $vars     = $data;

        require __DIR__ . '/../../views/admin/layout/main.php';
    }

    protected function renderFrontend(string $view, array $data = [], string $title = '')
    {
        if ($title !== '') {
            $data['title'] = $title;
        }

        $data['baseUrl'] = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
        $data['flash']   = $this->pullFlash();

        $viewFile = __DIR__ . '/../../views/frontend/' . $view . '.php';
        $view     = $viewFile;
        $vars     = $data;

        require __DIR__ . '/../../views/frontend/layout/main.php';
    }
}
