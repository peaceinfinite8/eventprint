<?php
/* ============================================================================
   public/routes/web.php — Public Routes
   ========================================================================== */

/* PAGES */
$router->get('/', 'HomePublicController@index');

$router->get('/products', 'ProductPublicController@index');
$router->get('/products/{id}', 'ProductPublicController@show');

$router->get('/product-detail', 'ProductPublicController@detailBySlug');
$router->get('/product/{slug}', 'ProductPublicController@detailBySlug');

$router->get('/our-home', 'OurStorePublicController@index');

$router->get('/blog', 'BlogPublicController@index');
$router->get('/blog/{slug}', 'BlogPublicController@show');
$router->get('/articles', 'BlogPublicController@index');
$router->get('/articles/{slug}', 'BlogPublicController@show');

$router->get('/contact', 'ContactPublicController@index');
$router->post('/contact/send', 'ContactPublicController@send');

/* PUBLIC API (JSON) */
$router->get('/api/settings', 'ApiController@settings');
$router->get('/api/home', 'HomePublicController@apiHome');

$router->get('/api/products', 'ProductPublicController@apiList');
$router->get('/api/products/{id}', 'ProductPublicController@apiDetail');
$router->get('/api/products/slug/{slug}', 'ProductPublicController@apiDetailBySlug');
$router->get('/api/products/{id}/pricing', 'ProductPublicController@apiPricing');
$router->get('/api/categories', 'ProductPublicController@apiCategories');

$router->get('/api/blog', 'BlogPublicController@apiBlog');
$router->get('/api/blog/{slug}', 'BlogPublicController@apiBlogDetail');
$router->get('/api/posts', 'BlogPublicController@apiPosts');

$router->get('/api/contact', 'ContactPublicController@apiContact');
$router->get('/api/our-home', 'OurStorePublicController@apiStores');
$router->get('/api/testimonials', 'ApiController@testimonials');

/* LEGACY DATA */
$router->get('/data/products.json', 'FrontendDataController@serve', ['file' => 'products.json']);
$router->get('/data/{file}', 'FrontendDataController@serve');

/* PRICING */
$router->get('/pricing/options', 'PricingController@options');
$router->post('/pricing/calc', 'PricingController@calc');
