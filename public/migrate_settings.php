<?php
// public/migrate_settings.php

// Enable Error Reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Root Detection
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    $root = __DIR__;
} elseif (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    $root = dirname(__DIR__);
} else {
    die("Root detection failed");
}

if (file_exists($root . '/app/config/db.php')) {
    require_once $root . '/app/config/db.php';
} else {
    die("DB config not found");
}

$db = db();
echo "<h1>Settings Migration</h1>";

// Columns to add
$cols = [
    'home_print_category_id' => 'INT(11) DEFAULT 0',
    'home_media_category_id' => 'INT(11) DEFAULT 0',
    'home_merch_category_id' => 'INT(11) DEFAULT 0',
];

foreach ($cols as $col => $def) {
    // Check if exists
    $check = $db->query("SHOW COLUMNS FROM settings LIKE '$col'");
    if ($check && $check->num_rows > 0) {
        echo "<p>Column <b>$col</b> already exists. Skipping.</p>";
    } else {
        // Add column
        $sql = "ALTER TABLE settings ADD COLUMN $col $def";
        if ($db->query($sql)) {
            echo "<p style='color:green'>Added column <b>$col</b> successfully.</p>";
        } else {
            echo "<p style='color:red'>Failed to add <b>$col</b>: " . $db->error . "</p>";
        }
    }
}

echo "<h3>Migration Completed.</h3>";
