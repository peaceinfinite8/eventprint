<?php
require_once __DIR__ . '/../app/config/db.php';
$db = db();

// Check if column exists
$result = $db->query("SHOW COLUMNS FROM testimonials LIKE 'bg_color'");
if ($result->num_rows === 0) {
    echo "Adding bg_color column to testimonials...\n";
    $db->query("ALTER TABLE testimonials ADD COLUMN bg_color VARCHAR(20) DEFAULT '#0EA5E9'");
    echo "Column added successfully.\n";
} else {
    echo "Column bg_color already exists.\n";
}
