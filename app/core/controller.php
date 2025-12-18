<?php

class Controller
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    protected function baseUrl(string $path = ''): string
    {
        $base = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
        return $base . '/' . ltrim($path, '/');
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] ??= ['success' => null, 'error' => null];
        if ($type === 'success') $_SESSION['flash']['success'] = $message;
        if ($type === 'error')   $_SESSION['flash']['error']   = $message;
    }

    protected function pullFlash(): array
    {
        $flash = $_SESSION['flash'] ?? ['success' => null, 'error' => null];
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

    protected function normalizeUrl(string $pathOrUrl): string
    {
        if (preg_match('#^https?://#i', $pathOrUrl)) return $pathOrUrl;

        $base = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');

        if (str_starts_with($pathOrUrl, '/')) return $base . $pathOrUrl;
        return $base . '/' . ltrim($pathOrUrl, '/');
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

    // ==========================================================
    // FRONTEND RENDER (SATU-SATUNYA pintu untuk frontend)
    // ==========================================================
    public function renderFrontend(string $viewName, array $vars = [], string $title = 'EventPrint'): void
    {
        $root = realpath(__DIR__ . '/../..');
        if (!$root) { http_response_code(500); exit('Project root not found'); }

        $viewName = ltrim(preg_replace('/\.php$/', '', $viewName), '/');

        $layoutPath = $root . '/views/frontend/layout/main.php';
        $viewPath   = $root . '/views/frontend/' . $viewName . '.php';

        if (!file_exists($layoutPath)) { http_response_code(500); exit('Layout not found: ' . $layoutPath); }
        if (!file_exists($viewPath))   { http_response_code(404); exit('View not found: ' . $viewPath); }

        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');

        $vars = array_merge($vars, [
            'baseUrl'     => $baseUrl,
            'title'       => $title,
            'page'        => $vars['page'] ?? explode('/', $viewName)[0],
            '__viewPath'  => $viewPath,
            'flash'       => $this->pullFlash(),
        ]);

        // layout membaca $vars
        require $layoutPath;
    }

    // ==========================================================
    // ADMIN RENDER (pintu admin)
    // ==========================================================
    protected function renderAdmin(string $view, array $data = [], string $title = ''): void
    {
        if ($title !== '') $data['title'] = $title;

        $data['baseUrl'] = rtrim($this->config['base_url'] ?? '/eventprint/public', '/');
        $data['flash']   = $this->pullFlash();

        $viewFile = __DIR__ . '/../../views/admin/' . $view . '.php';
        if (!file_exists($viewFile)) { http_response_code(500); exit('Admin view not found: ' . $viewFile); }

        $vars = $data;
        $vars['__viewPath'] = $viewFile;

        $layout = __DIR__ . '/../../views/admin/layout/main.php';
        if (!file_exists($layout)) { http_response_code(500); exit('Admin layout not found: ' . $layout); }

        require $layout;
    }
}
