<?php
// app/routes/web.php

// PUBLIC PAGES
$router->get('/', 'HomePublicController@index');

// PRODUCTS PAGES (render view)
$router->get('/products', 'ProductPublicController@index');
$router->get('/products/{id}', 'ProductPublicController@show'); // /products/3

// PRODUCTS API (JSON)
$router->get('/api/products', 'ProductPublicController@apiList');
$router->get('/api/products/{id}', 'ProductPublicController@apiDetail');

// PRICING (kamu sudah punya calc; options sudah kamu pakai)
$router->get('/pricing/options', 'PricingController@options'); // kalau belum ada method options, lihat bagian 4
$router->post('/pricing/calc', 'PricingController@calc');
