<?php
require_once __DIR__ . '/../app/config/db.php';
$db = db();
$res = $db->query("DESCRIBE settings");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
}
