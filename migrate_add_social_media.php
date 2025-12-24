<?php
require_once __DIR__ . '/app/config/db.php';

$db = db();

$columnsToAdd = [
    'tiktok' => "VARCHAR(255) DEFAULT NULL AFTER instagram",
    'twitter' => "VARCHAR(255) DEFAULT NULL AFTER tiktok",
    'youtube' => "VARCHAR(255) DEFAULT NULL AFTER twitter",
    'linkedin' => "VARCHAR(255) DEFAULT NULL AFTER youtube",
    'facebook' => "VARCHAR(255) DEFAULT NULL AFTER address" // ensuring facebook exists or position check
];

echo "Checking 'settings' table columns...\n";

// Get current columns
$res = $db->query("SHOW COLUMNS FROM settings");
$existingColumns = [];
while ($row = $res->fetch_assoc()) {
    $existingColumns[] = $row['Field'];
}

foreach ($columnsToAdd as $col => $def) {
    if (!in_array($col, $existingColumns)) {
        echo "Adding column '$col'...\n";
        $sql = "ALTER TABLE settings ADD COLUMN $col $def";
        if ($db->query($sql)) {
            echo "SUCCESS: Column '$col' added.\n";
        } else {
            echo "ERROR: Failed to add column '$col'. " . $db->error . "\n";
        }
    } else {
        echo "Column '$col' already exists. Skipping.\n";
    }
}

echo "Migration/Check complete.\n";
