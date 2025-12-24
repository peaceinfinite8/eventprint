<?php
require_once __DIR__ . '/../app/config/db.php';
$db = db();
$res = $db->query("SHOW COLUMNS FROM product_categories LIKE 'whatsapp_number'");
if ($res && $res->num_rows == 0) {
    if ($db->query("ALTER TABLE product_categories ADD whatsapp_number VARCHAR(50) NULL AFTER icon")) {
        echo "Column whatsapp_number added to product_categories.\n";
    } else {
        echo "Error: " . $db->error . "\n";
    }
} else {
    echo "Column whatsapp_number already exists.\n";
}
