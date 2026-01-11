<?php
$path = __DIR__ . '/test.txt';
$result = file_put_contents($path, "OK " . date("c"));

var_dump([
    "path" => $path,
    "is_writable" => is_writable(__DIR__),
    "write_result" => $result
]);
