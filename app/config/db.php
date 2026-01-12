<?php


$config = require __DIR__ . '/app.php';

if (($config['env'] ?? 'production') === 'local') {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'eventprint'; // Pastikan buat database 'eventprint' di localhost phpMyAdmin
} else {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db = 'eventprint';
}

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_errno) {
    if ($config['debug']) {
        die("DB Error: " . $mysqli->connect_error);
    } else {
        die("Database connection error.");
    }
}

$mysqli->query("SET time_zone = '+07:00'");

// helper global simpel
function db()
{
    global $mysqli;
    return $mysqli;
}
