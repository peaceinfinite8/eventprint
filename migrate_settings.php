<?php
// migrate_settings.php
require_once __DIR__ . '/app/config/db.php';

$db = db();

$columnsToAdd = [
    'operating_hours' => "VARCHAR(255) DEFAULT NULL AFTER gmaps_embed",
    'sales_contacts' => "TEXT DEFAULT NULL AFTER operating_hours"
];

echo "Checking settings table...\n";

foreach ($columnsToAdd as $col => $def) {
    if (!$db->query("SELECT $col FROM settings LIMIT 1")) {
        echo "Column '$col' is missing. Adding...\n";
        $sql = "ALTER TABLE settings ADD COLUMN $col $def";
        if ($db->query($sql)) {
            echo "SUCCESS: Added $col\n";
        } else {
            echo "ERROR: Failed to add $col. " . $db->error . "\n";
        }
    } else {
        echo "Column '$col' already exists.\n";
    }
}

echo "Migration check complete.\n";
