<?php
// verify_settings_db.php (Fixed)
$config = require 'app/config/db.php';

$db = new mysqli($config['host'], $config['username'], $config['password'], $config['database']);
if ($db->connect_error)
    die("DB Connection Failed: " . $db->connect_error);

$res = $db->query("SHOW COLUMNS FROM settings LIKE 'gmaps_embed'");
if ($res && $res->num_rows > 0) {
    echo "Column 'gmaps_embed' exists.\n";
} else {
    echo "Column 'gmaps_embed' MISSING. Adding it...\n";
    $sql = "ALTER TABLE settings ADD COLUMN gmaps_embed TEXT NULL AFTER whatsapp";
    if ($db->query($sql)) {
        echo "Column added successfully.\n";
    } else {
        echo "Error adding column: " . $db->error . "\n";
    }
}
