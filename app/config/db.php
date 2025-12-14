<?php


$config = require __DIR__ . '/app.php';

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'digital_printing_cms';

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_errno) {
    if ($config['debug']) {
        die("DB Error: " . $mysqli->connect_error);
    } else {
        die("Database connection error.");
    }
}

// helper global simpel
function db()
{
    global $mysqli;
    return $mysqli;
}
