<?php
// fix_localhost_db.php
// Script to replace production domain with localhost in database content

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/app/config/db.php';

$db = db();

$productionDomain = 'https://infopeaceinfinite.id';
$localDomain = 'http://localhost/eventprint';

echo "<h1>EventPrint DB Fixer</h1>";
echo "<p>Replacing <code>$productionDomain</code> with <code>$localDomain</code>...</p>";

$tables = [
    'hero_slides' => ['image', 'cta_link'],
    'products' => ['thumbnail', 'images'], // images might be JSON
    'settings' => ['logo', 'favicon', 'og_image'],
    'testimonials' => ['photo'],
    'posts' => ['thumbnail', 'content'], // content might have img src
    'page_contents' => ['value']
];

foreach ($tables as $table => $columns) {
    // Check if table exists
    $check = $db->query("SHOW TABLES LIKE '$table'");
    if ($check->num_rows === 0) {
        echo "<p style='color:orange'>Skipping table <b>$table</b> (not found)</p>";
        continue;
    }

    echo "<h3>Table: $table</h3><ul>";

    foreach ($columns as $col) {
        // Check if column exists
        $colCheck = $db->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
        if ($colCheck->num_rows === 0)
            continue;

        $sql = "UPDATE `$table` SET `$col` = REPLACE(`$col`, '$productionDomain', '$localDomain') WHERE `$col` LIKE '%$productionDomain%'";

        try {
            if ($db->query($sql)) {
                $affected = $db->affected_rows;
                if ($affected > 0) {
                    echo "<li style='color:green'>Updated <b>$col</b>: $affected rows changed.</li>";
                } else {
                    echo "<li style='color:gray'>Checked <b>$col</b>: No replacement needed.</li>";
                }
            } else {
                echo "<li style='color:red'>Error updating <b>$col</b>: " . $db->error . "</li>";
            }
        } catch (Exception $e) {
            echo "<li style='color:red'>Exception: " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";
}

echo "<hr><p>Done! Please check <a href='$localDomain'>$localDomain</a> (Close browser/clear cache first!)</p>";
