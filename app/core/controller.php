<?php

class Controller
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        // Load helpers
        require_once __DIR__ . '/../helpers/url.php';
        require_once __DIR__ . '/../helpers/view.php';
        require_once __DIR__ . '/auth.php';
        require_once __DIR__ . '/../helpers/Security.php';
    }

    protected function requireAuth(): void
    {
        if (!Auth::check()) {
            $this->redirect('admin/login');
        }
    }

    protected function validateCsrf(): void
    {
        Security::requireCsrfToken();
    }

    protected function baseUrl(string $path = ''): string
    {
        $base = rtrim($this->config['base_url'] ?? '/eventprint', '/');
        return $base . '/' . ltrim($path, '/');
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] ??= ['success' => null, 'error' => null];
        if ($type === 'success')
            $_SESSION['flash']['success'] = $message;
        if ($type === 'error')
            $_SESSION['flash']['error'] = $message;
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function normalizeUrl(string $pathOrUrl): string
    {
        if (preg_match('#^https?://#i', $pathOrUrl))
            return $pathOrUrl;

        $base = rtrim($this->config['base_url'] ?? '/eventprint', '/');

        if (str_starts_with($pathOrUrl, '/'))
            return $base . $pathOrUrl;
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

    // ========== PULL FLASH dari Session ==========
    protected function pullFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    // ========== GET SETTINGS (Centralized) ==========
    protected function getSettings(): array
    {
        static $settings = null;
        if ($settings === null) {
            require_once __DIR__ . '/../models/Setting.php';
            $settingModel = new Setting();
            $settings = $settingModel->getAll();
        }
        return $settings;
    }

    // ========== GET FOOTER CONTENT (Centralized) ==========
    protected function getFooterContent(): array
    {
        static $footer = null;
        if ($footer === null) {
            $db = db();
            $content = [];
            $res = $db->query("SELECT field, value FROM page_contents WHERE page_slug='footer' AND section='main'");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $content[$r['field']] = $r['value'];
                }
            }
            $footer = $content;
        }
        return $footer;
    }

    // ========== RENDER FRONTEND VIEW ==========
    public function renderFrontend(string $viewName, array $vars = [], string $title = 'EventPrint'): void
    {
        $root = realpath(__DIR__ . '/../..');
        if (!$root) {
            http_response_code(500);
            exit('Project root not found');
        }

        $viewName = ltrim(preg_replace('/\.php$/', '', $viewName), '/');

        $layoutPath = $root . '/views/frontend/layout/main.php';
        $viewPath = $root . '/views/frontend/' . $viewName . '.php';

        if (!file_exists($layoutPath)) {
            http_response_code(500);
            exit('Layout not found: ' . $layoutPath);
        }
        if (!file_exists($viewPath)) {
            http_response_code(404);
            exit('View not found: ' . $viewPath);
        }

        $baseUrl = rtrim($this->config['base_url'] ?? '/eventprint', '/');

        // Auto-inject settings to all frontend views
        $settings = $this->getSettings();

        $vars = array_merge($vars, [
            'baseUrl' => $baseUrl,
            'title' => $title,
            'page' => $vars['page'] ?? explode('/', $viewName)[0],
            '__viewPath' => $viewPath,
            'flash' => $this->pullFlash(),
            'settings' => $settings, // Auto-injected
            'footer' => $this->getFooterContent(), // Auto-injected
        ]);

        // layout membaca $vars
        require $layoutPath;
    }

    // ==========================================================
    // ADMIN RENDER (pintu admin)
    // ==========================================================
    protected function renderAdmin(string $view, array $data = [], string $title = ''): void
    {
        if ($title !== '')
            $data['title'] = $title;

        $data['baseUrl'] = rtrim($this->config['base_url'] ?? '/eventprint', '/');
        $data['flash'] = $this->pullFlash();

        $viewFile = __DIR__ . '/../../views/admin/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            exit('Admin view not found: ' . $viewFile);
        }

        $vars = $data;
        $vars['__viewPath'] = $viewFile;

        $layout = __DIR__ . '/../../views/admin/layout/main.php';
        if (!file_exists($layout)) {
            http_response_code(500);
            exit('Admin layout not found: ' . $layout);
        }

        require $layout;
    }
}
