<?php
// app/core/route.php

require_once __DIR__ . '/../helpers/ActivityLogger.php';

class Router
{
    protected array $routes = [];
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function get(string $path, string $action, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $action, $middlewares);
    }

    public function post(string $path, string $action, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $action, $middlewares);
    }

    protected function addRoute(string $method, string $path, string $action, array $middlewares): void
    {
        $this->routes[$method][$path] = [
            'action'      => $action,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(string $path, string $method): void
    {
        try {
            $routes = $this->routes[$method] ?? [];

            // 1) Exact match
            if (isset($routes[$path])) {
                $this->runRoute($routes[$path]);
                return;
            }

            // 2) Route params: /admin/products/edit/{id}
            foreach ($routes as $routePath => $routeInfo) {
                $pattern = preg_replace('#\{[^}]+\}#', '([^/]+)', $routePath);
                $pattern = "#^" . $pattern . "$#";

                if (preg_match($pattern, $path, $matches)) {
                    array_shift($matches);
                    $this->runRoute($routeInfo, $matches);
                    return;
                }
            }

            http_response_code(404);
            echo "404 Not Found";
            return;

        } catch (\Throwable $e) {
            // Log error (jangan biarin Fatal)
            if (class_exists('ActivityLogger')) {
                ActivityLogger::error('system', 'Unhandled exception in router', [
                    'error'  => $e->getMessage(),
                    'file'   => $e->getFile(),
                    'line'   => $e->getLine(),
                    'path'   => $path,
                    'method' => $method,
                ]);
            } else {
                error_log("[Unhandled] " . $e->getMessage());
            }

            // Redirect balik + flash message biar user gak nyangkut di error page
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }
            $_SESSION['flash_error'] = $e->getMessage();

            $back = $_SERVER['HTTP_REFERER'] ?? ($this->config['base_url'] ?? '/');
            header("Location: " . $back);
            exit;
        }
    }

    protected function runRoute(array $routeInfo, array $params = []): void
    {
        // Middleware
        foreach ($routeInfo['middlewares'] as $middlewareClass) {
            $file = __DIR__ . '/../middleware/' . $middlewareClass . '.php';
            if (!file_exists($file)) {
                throw new Exception("Middleware file not found: $middlewareClass");
            }

            require_once $file;

            if (!class_exists($middlewareClass)) {
                throw new Exception("Middleware class not found: $middlewareClass");
            }

            $middleware = new $middlewareClass();
            $middleware->handle();
        }

        // Controller
        [$controllerName, $method] = explode('@', $routeInfo['action']);

        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: $controllerName");
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            throw new Exception("Controller class not found: $controllerName");
        }

        $controller = new $controllerName($this->config);

        if (!method_exists($controller, $method)) {
            throw new Exception("Method $method not found in $controllerName");
        }

        call_user_func_array([$controller, $method], $params);
    }
}
