<?php
// database/run_migrations.php
// Simple migration runner for EventPrint

require_once __DIR__ . '/../app/config/db.php';

$db = db();

$migrations = [
    '005_activity_logs.sql',
    '006_product_enhancements.sql',
    '007_blog_external_links.sql'
];

echo "EventPrint Database Migrations\n";
echo "==============================\n\n";

foreach ($migrations as $migration) {
    $filePath = __DIR__ . '/migrations/' . $migration;

    if (!file_exists($filePath)) {
        echo "[SKIP] {$migration} - File not found\n";
        continue;
    }

    echo "[RUN]  {$migration}...\n";

    $sql = file_get_contents($filePath);

    // Split by semicolons (simple approach)
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (empty($statement) || str_starts_with($statement, '--')) {
            continue;
        }

        try {
            if (!$db->query($statement)) {
                // Check if error is "Duplicate column" or "Table already exists" (can skip)
                $error = $db->error;
                if (
                    stripos($error, 'Duplicate column') !== false ||
                    stripos($error, 'already exists') !== false
                ) {
                    echo "  [INFO] Already applied: " . substr($error, 0, 50) . "...\n";
                } else {
                    echo "  [ERROR] " . $error . "\n";
                    echo "  SQL: " . substr($statement, 0, 100) . "...\n";
                }
            }
        } catch (Exception $e) {
            echo "  [ERROR] " . $e->getMessage() . "\n";
        }
    }

    echo "[DONE] {$migration}\n\n";
}

echo "Migration complete!\n";
