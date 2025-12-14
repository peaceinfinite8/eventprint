<?php
// routes/admin.php

// ======================= AUTH =========================
$router->get('/admin/login',  'UserController@showLogin');
$router->post('/admin/login', 'UserController@login');
$router->get('/admin/logout', 'UserController@logout', ['AuthRequired']);

// ======================= DASHBOARD ====================
$router->get('/admin/dashboard', 'DashboardController@index', ['AuthRequired']);

// ======================= PRODUCTS =======================

// ✅ OPTIONS VIEW (admin + superadmin) -> role dicek di controller
$router->get(
    '/admin/products/{id}/options',
    'ProductOptionController@index',
    ['AuthRequired']
);

// ✅ SUPERADMIN CRUD group/value
$router->post(
    '/admin/products/{id}/options/group/store',
    'ProductOptionController@storeGroup',
    ['AuthRequired','SuperAdminOnly']
);
$router->post(
    '/admin/options/group/update/{id}',
    'ProductOptionController@updateGroup',
    ['AuthRequired','SuperAdminOnly']
);
$router->post(
    '/admin/options/group/delete/{id}',
    'ProductOptionController@deleteGroup',
    ['AuthRequired','SuperAdminOnly']
);

$router->post(
    '/admin/options/group/{id}/value/store',
    'ProductOptionController@storeValue',
    ['AuthRequired','SuperAdminOnly']
);
$router->post(
    '/admin/options/value/update/{id}',
    'ProductOptionController@updateValue',
    ['AuthRequired','SuperAdminOnly']
);
$router->post(
    '/admin/options/value/delete/{id}',
    'ProductOptionController@deleteValue',
    ['AuthRequired','SuperAdminOnly']
);

// ✅ /admin/products => ALL PRODUK (CRUD)
$router->get(
    '/admin/products',
    'ProductController@adminList',
    ['AuthRequired', 'SuperAdminOnly']
);

// optional: biar link lama tetap hidup
$router->get(
    '/admin/products/list',
    'ProductController@adminList',
    ['AuthRequired', 'SuperAdminOnly']
);

$router->get('/admin/products/create',        'ProductController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/products/store',        'ProductController@store',  ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/products/edit/{id}',     'ProductController@edit',   ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/products/update/{id}',  'ProductController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/products/delete/{id}',  'ProductController@delete', ['AuthRequired', 'SuperAdminOnly']);



// PUBLIC API
$router->get('/api/products', 'ProductPublicController@index');
$router->get('/api/products/{id}', 'ProductPublicController@show');

// PUBLIC pricing calc (customer)
$router->get('/pricing/options', 'PricingController@options');
$router->post('/pricing/calc', 'PricingController@calc');


// ======================= PRODUCT CATEGORIES =======================

$router->get('/admin/product-categories',             'ProductCategoryController@index',  ['AuthRequired','SuperAdminOnly']);
$router->get('/admin/product-categories/create',      'ProductCategoryController@create', ['AuthRequired','SuperAdminOnly']);
$router->post('/admin/product-categories/store',      'ProductCategoryController@store',  ['AuthRequired','SuperAdminOnly']);
$router->get('/admin/product-categories/edit/{id}',   'ProductCategoryController@edit',   ['AuthRequired','SuperAdminOnly']);
$router->post('/admin/product-categories/update/{id}','ProductCategoryController@update', ['AuthRequired','SuperAdminOnly']);
$router->post('/admin/product-categories/delete/{id}','ProductCategoryController@delete', ['AuthRequired','SuperAdminOnly']);

// ======================= DISCOUNT =======================

$router->get('/admin/discounts',              'DiscountController@index',  ['AuthRequired','SuperAdminOnly']);
$router->get('/admin/discounts/create',       'DiscountController@create', ['AuthRequired','SuperAdminOnly']);
$router->post('/admin/discounts/store',       'DiscountController@store',  ['AuthRequired','SuperAdminOnly']);
$router->get('/admin/discounts/edit/{id}',    'DiscountController@edit',   ['AuthRequired','SuperAdminOnly']);
$router->post('/admin/discounts/update/{id}', 'DiscountController@update', ['AuthRequired','SuperAdminOnly']);
$router->post('/admin/discounts/delete/{id}', 'DiscountController@delete', ['AuthRequired','SuperAdminOnly']);


// ======================= OUR STORE ====

// HUB Our Store (kalau mau, atau langsung index)
$router->get(
    '/admin/our-store',
    'OurStoreController@index',
    ['AuthRequired', 'SuperAdminOnly']
);

$router->get(
    '/admin/our-store/create',
    'OurStoreController@create',
    ['AuthRequired', 'SuperAdminOnly']
);
$router->post(
    '/admin/our-store/store',
    'OurStoreController@store',
    ['AuthRequired', 'SuperAdminOnly']
);
$router->get(
    '/admin/our-store/edit/{id}',
    'OurStoreController@edit',
    ['AuthRequired', 'SuperAdminOnly']
);
$router->post(
    '/admin/our-store/update/{id}',
    'OurStoreController@update',
    ['AuthRequired', 'SuperAdminOnly']
);
$router->post(
    '/admin/our-store/delete/{id}',
    'OurStoreController@delete',
    ['AuthRequired', 'SuperAdminOnly']
);


// ======================= BLOG / ARTIKEL ==============

$router->get(
    '/admin/blog',
    'BlogController@index',
    ['AuthRequired', 'AdminOnly']
);
$router->get(
    '/admin/blog/create',
    'BlogController@create',
    ['AuthRequired', 'AdminOnly']
);
$router->post(
    '/admin/blog/store',
    'BlogController@store',
    ['AuthRequired', 'AdminOnly']
);
$router->get(
    '/admin/blog/edit/{id}',
    'BlogController@edit',
    ['AuthRequired', 'AdminOnly']
);
$router->post(
    '/admin/blog/update/{id}',
    'BlogController@update',
    ['AuthRequired', 'AdminOnly']
);
$router->post(
    '/admin/blog/delete/{id}',
    'BlogController@delete',
    ['AuthRequired', 'AdminOnly']
);


// ======================= CONTACT (HUB + pesan) =======

$router->get(
    '/admin/contact',
    'ContactController@adminIndex',
    ['AuthRequired', 'AdminOnly']
);

$router->get(
    '/admin/contact/messages',
    'ContactController@adminMessages',
    ['AuthRequired', 'AdminOnly']
);

$router->get(
    '/admin/contact/{id}',
    'ContactController@adminShow',
    ['AuthRequired', 'AdminOnly']
);

$router->post(
    '/admin/contact/{id}/delete',
    'ContactController@adminDelete',
    ['AuthRequired', 'SuperAdminOnly']
);


// ======================= HOME CONTENT (Hero, etc) =====

$router->get(
    '/admin/home',
    'HomeController@index',
    ['AuthRequired', 'AdminOnly']
);
$router->get(
    '/admin/home/hero',
    'HomeController@editHero',
    ['AuthRequired', 'SuperAdminOnly']
);
$router->post(
    '/admin/home/hero',
    'HomeController@updateHero',
    ['AuthRequired', 'SuperAdminOnly']
);

// ======================= SETTINGS (General config) =====

$router->get(
    '/admin/settings',
    'SettingsController@index',
    ['AuthRequired', 'SuperAdminOnly']
);

$router->post(
    '/admin/settings/update',
    'SettingsController@update',
    ['AuthRequired', 'SuperAdminOnly']
);

// ======================= USERS (Management) =============

$router->get('/admin/users', 'UsersController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/users/create', 'UsersController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/users/store', 'UsersController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/users/edit/{id}', 'UsersController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/users/update/{id}', 'UsersController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/users/delete/{id}', 'UsersController@delete', ['AuthRequired', 'SuperAdminOnly']);
