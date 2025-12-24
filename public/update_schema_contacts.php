<?php
require_once __DIR__ . '/../app/config/db.php';
$db = db();
// Check if sales_contacts exists
$res = $db->query("SHOW COLUMNS FROM settings LIKE 'sales_contacts'");
if ($res && $res->num_rows == 0) {
    if ($db->query("ALTER TABLE settings ADD sales_contacts TEXT NULL AFTER whatsapp")) {
        echo "Column sales_contacts added.\n";
    } else {
        echo "Error: " . $db->error . "\n";
    }
} else {
    echo "Column sales_contacts already exists.\n";
}
