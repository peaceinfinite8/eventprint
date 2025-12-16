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

    // app/core/Controller.php
    protected function view(string $view, array $vars = [])
{
    // ROOT PROJECT: eventprint/
    $root = realpath(__DIR__ . '/../..');
    if (!$root) {
        http_response_code(500);
        exit('Project root not found');
    }

    // view file
    $viewPath = $root . '/views/' . $view . '.php';
    if (!file_exists($viewPath)) {
        http_response_code(500);
        exit('View not found: ' . $viewPath);
    }

    // inject baseUrl DARI CONFIG (SATU SUMBER)
    $vars['baseUrl'] = rtrim($this->config['base_url'], '/');
    $vars['__viewPath'] = $viewPath;

    // layout frontend
    $layoutPath = $root . '/views/frontend/layout/main.php';
    if (!file_exists($layoutPath)) {
        http_response_code(500);
        exit('Layout not found: ' . $layoutPath);
    }

    require $layoutPath;
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

    // app/core/controller.php

public function renderFrontend(string $viewName, array $vars = [], string $title = 'EventPrint'): void
{
    $root = realpath(__DIR__ . '/../..'); // dari app/core ke root project
    if (!$root) die("Root path tidak ditemukan.");

    // normalisasi: hilangin .php & slash depan
    $viewName = ltrim($viewName, '/');
    $viewName = preg_replace('/\.php$/', '', $viewName);

    $layout = $root . '/views/frontend/layout/main.php';
    $view   = $root . '/views/frontend/' . $viewName . '.php';

    if (!file_exists($layout)) die("Layout frontend tidak ditemukan: $layout");
    if (!file_exists($view))   die("View frontend tidak ditemukan: $view");

    // baseUrl AUTO (biar ga hardcode)
    $baseUrl = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    // contoh: /eventprint/public
    if ($baseUrl === '') $baseUrl = '';

    $vars['baseUrl'] = $vars['baseUrl'] ?? $baseUrl;
    $vars['title']   = $title;
    $vars['page']    = $vars['page'] ?? explode('/', $viewName)[0];

    require $layout;
}


}
