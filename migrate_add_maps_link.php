<?php
// migrate_add_maps_link.php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'eventprint';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add maps_link column
    $sql = "ALTER TABLE settings ADD COLUMN maps_link TEXT DEFAULT NULL AFTER address";
    $pdo->exec($sql);
    echo "Column 'maps_link' added successfully to 'settings' table.\n";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column 'maps_link' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>