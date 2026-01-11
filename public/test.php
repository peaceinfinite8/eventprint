<?php
require_once __DIR__ . '/../app/config/db.php';

try {
    $db = db();

    // Check current column type
    $result = $db->query("SHOW COLUMNS FROM product_categories LIKE 'icon'");
    $row = $result->fetch_assoc();
    echo "Current column type: " . $row['Type'] . "\n";

    // update to VARCHAR(255)
    $db->query("ALTER TABLE product_categories MODIFY COLUMN icon VARCHAR(255) DEFAULT NULL");

    // Check again
    $result = $db->query("SHOW COLUMNS FROM product_categories LIKE 'icon'");
    $row = $result->fetch_assoc();
    echo "New column type: " . $row['Type'] . "\n";

    echo "Schema updated successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
