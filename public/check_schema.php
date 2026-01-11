<?php
// public/check_schema.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Manual Root Detection (Copied from corrected index.php)
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
    die("start db error");
}

$db = db();

echo "<h1>Schema Inspector</h1>";

$tables = ['products', 'hero_slides', 'page_contents', 'testimonials'];

foreach ($tables as $t) {
    echo "<h3>Table: $t</h3>";
    $res = $db->query("SHOW COLUMNS FROM $t");
    if ($res) {
        echo "<table border='1' style='border-collapse:collapse; width:100%;'>";
        echo "<tr style='background:#ccc'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($row = $res->fetch_assoc()) {
            // Highlight potential missing fields
            $style = '';
            if (in_array($row['Field'], ['currency', 'shopee_url', 'tokopedia_url', 'discount_type'])) {
                $style = 'background: #dff0d8; font-weight:bold;';
            }
            echo "<tr style='$style'>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div style='color:red'>Table exists? NO (" . $db->error . ")</div>";
    }
}
