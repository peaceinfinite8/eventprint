<?php
// app/helpers/logging.php
// Activity logging helper for realtime log viewer

/**
 * Log an event to activity_logs table
 * 
 * @param string $level 'info'|'warning'|'error'
 * @param string $source 'api'|'admin'|'system'
 * @param string $message Log message (max 255 chars)
 * @param array $context Additional context data (stored as JSON)
 * @return bool Success status
 */
function log_event(string $level, string $source, string $message, array $context = []): bool
{
    try {
        $db = db();

        // Validate level and source
        $validLevels = ['info', 'warning', 'error'];
        $validSources = ['api', 'admin', 'system'];

        if (!in_array($level, $validLevels)) {
            $level = 'info';
        }
        if (!in_array($source, $validSources)) {
            $source = 'system';
        }

        // Truncate message if too long
        $message = mb_substr($message, 0, 255);

        // Convert context to JSON
        $contextJson = !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : null;

        $stmt = $db->prepare("
            INSERT INTO activity_logs (level, source, message, context)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param('ssss', $level, $source, $message, $contextJson);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    } catch (Exception $e) {
        // Silent fail - don't break application if logging fails
        error_log("Logging error: " . $e->getMessage());
        return false;
    }
}

/**
 * Log admin action
 */
function log_admin_action(string $action, string $details, array $context = []): bool
{
    $user = $_SESSION['user'] ?? null;
    $context['user_id'] = $user['id'] ?? null;
    $context['username'] = $user['username'] ?? 'unknown';

    $message = "$action: $details";
    return log_event('info', 'admin', $message, $context);
}

/**
 * Log API request
 */
function log_api_request(string $method, string $path, int $status, float $durationMs): bool
{
    $message = "$method $path - $status";
    $context = [
        'method' => $method,
        'path' => $path,
        'status' => $status,
        'duration_ms' => round($durationMs, 2),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];

    $level = $status >= 500 ? 'error' : ($status >= 400 ? 'warning' : 'info');
    return log_event($level, 'api', $message, $context);
}

/**
 * Log system event
 */
function log_system_event(string $event, array $context = []): bool
{
    return log_event('info', 'system', $event, $context);
}
