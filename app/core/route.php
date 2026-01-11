<?php
// app/core/route.php

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
            'action' => $action,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(string $path, string $method): void
    {
        $routes = $this->routes[$method] ?? [];

        // 1) Exact match
        if (isset($routes[$path])) {
            $this->runRoute($routes[$path]);
            return;
        }

        // 2) Route dengan parameter, contoh: /admin/products/edit/{id}
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
    }

    protected function runRoute(array $routeInfo, array $params = []): void
    {
        // ========== INI BAGIAN PENTING: MIDDLEWARE ==========
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
        // =====================================================

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
