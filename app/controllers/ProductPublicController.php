<?php
// app/controllers/ProductPublicController.php

require_once __DIR__ . '/../models/Product.php';

class ProductPublicController extends Controller
{
    public function index()
    {
        $this->view('frontend/product/index', [
            'title' => 'Produk - EventPrint',
            'page'  => 'products',
        ]);
    }

    public function show($id)
{
    $id = (int)$id;
    if ($id <= 0) {
        http_response_code(404);
        $this->view('frontend/errors/404', [
            'title' => 'Produk tidak ditemukan',
            'page'  => 'product_detail', // ✅
        ]);
        return;
    }

    $m = new Product();
    $product = $m->findPublicById($id);

    if (!$product) {
        http_response_code(404);
        $this->view('frontend/errors/404', [
            'title' => 'Produk tidak ditemukan',
            'page'  => 'product_detail', // ✅
        ]);
        return;
    }

    $this->view('frontend/product/show', [
        'title'     => 'Detail Produk - EventPrint',
        'page'      => 'product_detail', // ✅
        'productId' => $id,              // ✅ penting buat data-product-id
    ]);
}


    // /product-detail?slug=xxx (PAGE)
    public function detailBySlug()
    {
        $slug = trim($_GET['slug'] ?? '');
        if ($slug === '') {
            http_response_code(404);
            $this->view('frontend/errors/404', [
                'title' => 'Produk tidak ditemukan',
                'page'  => 'product_detail',
            ]);
            return;
        }

        // halaman show tetap dirender oleh JS (ambil detail dari API)
        $this->view('frontend/product/show', [
            'title' => 'Detail Produk - EventPrint',
            'page'  => 'product_detail',
            'slug'  => $slug,
        ]);
    }

    // API LIST
    public function apiList()
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $m = new Product();
            $items = $m->getPublicList();
            $categories = $m->getPublicCategories();

            echo json_encode([
                'ok' => true,
                'categories' => $categories,
                'items' => $items,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }

    // API DETAIL BY SLUG
    public function apiDetailBySlug($slug)
    {
        header('Content-Type: application/json; charset=utf-8');

        $slug = trim((string)$slug);
        if ($slug === '') {
            http_response_code(404);
            echo json_encode(['ok' => false, 'message' => 'Slug tidak valid'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $item = (new Product())->findPublicBySlug($slug);
        if (!$item) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'message' => 'Produk tidak ditemukan'], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['ok' => true, 'item' => $item], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
