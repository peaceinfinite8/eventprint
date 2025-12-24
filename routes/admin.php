<?php
// public/routes/admin.php

// ======================= AUTH =========================
$router->get('/admin/login', 'UserController@showLogin');
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
$router->post('/admin/products/{id}/options/group/store', 'ProductOptionController@storeGroup', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/options/group/update/{id}', 'ProductOptionController@updateGroup', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/options/group/delete/{id}', 'ProductOptionController@deleteGroup', ['AuthRequired', 'SuperAdminOnly']);

$router->post('/admin/options/group/{id}/value/store', 'ProductOptionController@storeValue', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/options/value/update/{id}', 'ProductOptionController@updateValue', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/options/value/delete/{id}', 'ProductOptionController@deleteValue', ['AuthRequired', 'SuperAdminOnly']);

// ✅ /admin/products => ALL PRODUK (CRUD)
$router->get('/admin/products', 'ProductController@adminList', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/products/list', 'ProductController@adminList', ['AuthRequired', 'SuperAdminOnly']);

$router->get('/admin/products/create', 'ProductController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/products/store', 'ProductController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/products/edit/{id}', 'ProductController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/products/update/{id}', 'ProductController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/products/delete/{id}', 'ProductController@delete', ['AuthRequired', 'SuperAdminOnly']);

// ✅ PER-PRODUCT OPTIONS (Material & Lamination)
$router->get('/admin/products/{id}/product-options', 'ProductOptionsController2@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/products/{id}/product-options/save', 'ProductOptionsController2@save', ['AuthRequired', 'SuperAdminOnly']);

// ======================= PRODUCT CATEGORIES =======================
$router->get('/admin/product-categories', 'ProductCategoryController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/product-categories/create', 'ProductCategoryController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/product-categories/store', 'ProductCategoryController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/product-categories/edit/{id}', 'ProductCategoryController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/product-categories/update/{id}', 'ProductCategoryController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/product-categories/delete/{id}', 'ProductCategoryController@delete', ['AuthRequired', 'SuperAdminOnly']);

// ======================= DISCOUNT =======================
$router->get('/admin/discounts', 'DiscountController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/discounts/create', 'DiscountController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/discounts/store', 'DiscountController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/discounts/edit/{id}', 'DiscountController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/discounts/update/{id}', 'DiscountController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/discounts/delete/{id}', 'DiscountController@delete', ['AuthRequired', 'SuperAdminOnly']);

// ======================= OUR HOME =======================
// Store Management
$router->get('/admin/our-home/stores', 'OurStoreController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/our-home/stores/create', 'OurStoreController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/our-home/stores/store', 'OurStoreController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/our-home/stores/edit/{id}', 'OurStoreController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/our-home/stores/update/{id}', 'OurStoreController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/our-home/stores/delete/{id}', 'OurStoreController@delete', ['AuthRequired', 'SuperAdminOnly']);

// Gallery Management (Independent)// ✅ GALLERY
$router->get('/admin/our-home/gallery', 'OurStoreController@galleryIndex', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/our-home/gallery/create', 'OurStoreController@galleryCreate', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/our-home/gallery/store', 'OurStoreController@galleryStore', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/our-home/gallery/edit/{id}', 'OurStoreController@galleryEdit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/our-home/gallery/update/{id}', 'OurStoreController@galleryUpdate', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/our-home/gallery/delete/{id}', 'OurStoreController@galleryDelete', ['AuthRequired', 'SuperAdminOnly']);

// ✅ CONTENT HEADERS
$router->get('/admin/our-home/content', 'OurStoreController@content', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/our-home/content/update', 'OurStoreController@contentUpdate', ['AuthRequired', 'SuperAdminOnly']);

// ======================= TESTIMONIALS =======================
$router->get('/admin/testimonials', 'TestimonialController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/testimonials/create', 'TestimonialController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/testimonials/store', 'TestimonialController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/testimonials/edit/{id}', 'TestimonialController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/testimonials/update/{id}', 'TestimonialController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/testimonials/delete/{id}', 'TestimonialController@delete', ['AuthRequired', 'SuperAdminOnly']);

// ======================= BLOG / ARTIKEL =======================
$router->get('/admin/blog', 'BlogController@index', ['AuthRequired', 'AdminOnly']);
$router->get('/admin/blog/create', 'BlogController@create', ['AuthRequired', 'AdminOnly']);
$router->post('/admin/blog/store', 'BlogController@store', ['AuthRequired', 'AdminOnly']);
$router->get('/admin/blog/edit/{id}', 'BlogController@edit', ['AuthRequired', 'AdminOnly']);
$router->post('/admin/blog/update/{id}', 'BlogController@update', ['AuthRequired', 'AdminOnly']);
$router->post('/admin/blog/delete/{id}', 'BlogController@delete', ['AuthRequired', 'AdminOnly']);

// ======================= CONTACT (HUB + pesan) =======================
$router->get('/admin/contact', 'ContactController@adminIndex', ['AuthRequired', 'AdminOnly']);
$router->get('/admin/contact/messages', 'ContactController@adminMessages', ['AuthRequired', 'AdminOnly']);
$router->get('/admin/contact/{id}', 'ContactController@adminShow', ['AuthRequired', 'AdminOnly']);
$router->post('/admin/contact/{id}/delete', 'ContactController@adminDelete', ['AuthRequired', 'SuperAdminOnly']);

// ======================= HOME CONTENT =======================
$router->get('/admin/home', 'HomeController@index', ['AuthRequired', 'SuperAdminOnly']);

// HERO SLIDES
$router->get('/admin/home/hero', 'HomeController@heroIndex', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/home/hero/create', 'HomeController@heroCreateForm', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/hero/store', 'HomeController@heroStore', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/home/hero/edit/{id}', 'HomeController@heroEditForm', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/hero/update/{id}', 'HomeController@heroUpdate', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/hero/delete/{id}', 'HomeController@heroDelete', ['AuthRequired', 'SuperAdminOnly']);


// ✅ EDIT HOME CONTENT (CTA + Contact + Category mapping)
$router->get('/admin/home/content', 'HomeController@content', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/content/update', 'HomeController@contentUpdate', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/category-map', 'HomeController@updateHomeCategoryMap', ['AuthRequired', 'SuperAdminOnly']);

// ✅ WHY CHOOSE US
$router->get('/admin/home/why-choose', 'HomeController@whyChoose', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/why-choose/update', 'HomeController@whyChooseUpdate', ['AuthRequired', 'SuperAdminOnly']);

// ✅ SMALL BANNERS (PROMO)
$router->get('/admin/home/small-banner', 'HomeController@smallBannerIndex', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/home/small-banner/create', 'HomeController@smallBannerCreateForm', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/small-banner/store', 'HomeController@smallBannerStore', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/home/small-banner/edit/{id}', 'HomeController@smallBannerEditForm', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/small-banner/update/{id}', 'HomeController@smallBannerUpdate', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/home/small-banner/delete/{id}', 'HomeController@smallBannerDelete', ['AuthRequired', 'SuperAdminOnly']);


// ======================= FOOTER =======================
$router->get('/admin/footer', 'FooterController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/footer/update', 'FooterController@update', ['AuthRequired', 'SuperAdminOnly']);

// ======================= SETTINGS =======================
$router->get('/admin/settings', 'SettingsController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/settings/update', 'SettingsController@update', ['AuthRequired', 'SuperAdminOnly']);

// ======================= SYSTEM LOGS (SUPER ADMIN ONLY) =======================
$router->get('/admin/system-logs', 'SystemLogsController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/api/system-logs', 'SystemLogsController@apiLogs', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/system-logs/clear-old', 'SystemLogsController@clearOld', ['AuthRequired', 'SuperAdminOnly']);

// ======================= CONTACT MESSAGES =======================
$router->get('/admin/contact-messages', 'ContactMessagesController@index', ['AuthRequired', 'AdminOnly']);
$router->get('/admin/contact-messages/{id}', 'ContactMessagesController@view', ['AuthRequired', 'AdminOnly']);
$router->post('/admin/contact-messages/{id}/toggle-read', 'ContactMessagesController@toggleRead', ['AuthRequired', 'AdminOnly']);
$router->post('/admin/contact-messages/{id}/delete', 'ContactMessagesController@delete', ['AuthRequired', 'SuperAdminOnly']);

// ======================= USERS =======================
$router->get('/admin/users', 'UsersController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/users/create', 'UsersController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/users/store', 'UsersController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/users/edit/{id}', 'UsersController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/users/update/{id}', 'UsersController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/users/delete/{id}', 'UsersController@delete', ['AuthRequired', 'SuperAdminOnly']);

// ======================= MATERIALS =======================
$router->get('/admin/materials', 'MaterialController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/materials/create', 'MaterialController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/materials/store', 'MaterialController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/materials/edit/{id}', 'MaterialController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/materials/update/{id}', 'MaterialController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/materials/delete/{id}', 'MaterialController@delete', ['AuthRequired', 'SuperAdminOnly']);

// ======================= LAMINATIONS =======================
$router->get('/admin/laminations', 'LaminationController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/laminations/create', 'LaminationController@create', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/laminations/store', 'LaminationController@store', ['AuthRequired', 'SuperAdminOnly']);
$router->get('/admin/laminations/edit/{id}', 'LaminationController@edit', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/laminations/update/{id}', 'LaminationController@update', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/laminations/delete/{id}', 'LaminationController@delete', ['AuthRequired', 'SuperAdminOnly']);

// ======================= CATEGORY OPTIONS MAPPING =======================
$router->get('/admin/category-options', 'CategoryOptionsController@index', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/category-options/save', 'CategoryOptionsController@save', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/category-options/copy', 'CategoryOptionsController@copy', ['AuthRequired', 'SuperAdminOnly']);


// ======================= TIER PRICING =======================
$router->get('/admin/tier-pricing', 'TierPricingController@index', ['AuthRequired', 'SuperAdminOnly']);
// API
$router->get('/admin/api/products/{id}/tiers', 'TierPricingController@apiList', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/api/products/{id}/tiers/store', 'TierPricingController@apiStore', ['AuthRequired', 'SuperAdminOnly']);
$router->post('/admin/api/tiers/delete/{id}', 'TierPricingController@apiDelete', ['AuthRequired', 'SuperAdminOnly']);
