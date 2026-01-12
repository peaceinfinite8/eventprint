<?php
// app/middleware/AuthRequired.php

require_once __DIR__ . '/../core/auth.php';

class AuthRequired
{
    public function handle(): void
    {
        // Only start session if not already active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!Auth::check()) {
            // Detect if this is an API request
            $requestUri = $_SERVER['REQUEST_URI'] ?? '';
            $isApiRequest = (strpos($requestUri, '/api/') !== false);

            if ($isApiRequest) {
                // For API requests: return JSON 401 instead of redirecting
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'success' => false,
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required. Please log in.'
                ]);
                exit;
            }

            // For regular pages: redirect to login
            $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'), '/\\');
            header('Location: ' . $basePath . '/admin/login');
            exit;
        }
    }
}
