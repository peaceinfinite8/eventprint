<?php
require_once __DIR__ . '/app/config/db.php';

$db = db();

$columnsToAdd = [
    'external_url' => "VARCHAR(255) DEFAULT NULL AFTER post_category",
    'link_target' => "VARCHAR(20) DEFAULT '_self' AFTER external_url"
];

echo "Checking 'posts' table columns...\n";

// Get current columns
$res = $db->query("SHOW COLUMNS FROM posts");
$existingColumns = [];
while ($row = $res->fetch_assoc()) {
    $existingColumns[] = $row['Field'];
}

foreach ($columnsToAdd as $col => $def) {
    if (!in_array($col, $existingColumns)) {
        echo "Adding column '$col'...\n";
        $sql = "ALTER TABLE posts ADD COLUMN $col $def";
        if ($db->query($sql)) {
            echo "SUCCESS: Column '$col' added.\n";
        } else {
            echo "ERROR: Failed to add column '$col'. " . $db->error . "\n";
        }
    } else {
        echo "Column '$col' already exists. Skipping.\n";
    }
}

echo "Migration complete.\n";
