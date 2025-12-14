<?php
// public/index.php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$appConfig = require __DIR__ . '/../app/config/app.php';
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/core/route.php';
require_once __DIR__ . '/../app/core/controller.php';
require_once __DIR__ . '/../app/core/auth.php';


// init router
$router = new Router($appConfig);

// load routes
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/admin.php';

// parsing path (strip /eventprint/public)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$path = '/' . trim(str_replace($scriptDir, '', $uri), '/');

// DEBUG
// echo "<pre>METHOD: {$_SERVER['REQUEST_METHOD']}\nURI: {$uri}\nPATH: {$path}</pre>";


// dispatch
$router->dispatch($path, $_SERVER['REQUEST_METHOD']);
