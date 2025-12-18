<?php
// public/routes/web.php

// ======================= PUBLIC PAGES =======================

// Home
$router->get('/', 'HomePublicController@index');

// Products (page list)
$router->get('/products', 'ProductPublicController@index');
$router->get('/products/{id}', 'ProductPublicController@show');

// Product detail (slug via query: /product-detail?slug=xxx)
$router->get('/product-detail', 'ProductPublicController@detailBySlug');

// Our Home
$router->get('/our-home', 'OurStorePublicController@index');

// Blog (alias biar fleksibel)
$router->get('/blog', 'BlogPublicController@index');
$router->get('/blog/{slug}', 'BlogPublicController@show');
$router->get('/articles', 'BlogPublicController@index');
$router->get('/articles/{slug}', 'BlogPublicController@show');

// Contact
$router->get('/contact', 'ContactPublicController@index');
$router->post('/contact/send', 'ContactPublicController@send');

// ======================= PUBLIC API (JSON) =======================
// Ini yang dipakai renderer JS kalau kamu mau mode “data driven”
$router->get('/api/home', 'HomePublicController@apiHome');
$router->get('/api/products', 'ProductPublicController@apiList');
$router->get('/api/products/{id}', 'ProductPublicController@apiDetail');
$router->get('/api/products/slug/{slug}', 'ProductPublicController@apiDetailBySlug');
$router->get('/api/categories', 'ProductPublicController@apiCategories');
$router->get('/api/blog', 'BlogPublicController@apiBlog');
$router->get('/api/contact', 'ContactPublicController@apiContact');
$router->get('/api/our-home', 'OurStorePublicController@apiStores');



// Pricing
$router->get('/pricing/options', 'PricingController@options');
$router->post('/pricing/calc', 'PricingController@calc');
