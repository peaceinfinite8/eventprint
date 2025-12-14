<?php
// app/controllers/ProductPublicController.php

require_once __DIR__ . '/../models/Product.php';

class ProductPublicController extends Controller
{
    protected Product $product;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->product = new Product();
    }

    private function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ============ PAGES ============
    public function index()
    {
        // ambil semua produk aktif (public)
        $products = $this->product->getPublicAll();

        $this->renderFrontend('product/index', [
            'products' => $products,
        ], 'Produk & Layanan');
    }

    // GET /products/{id}
    public function show($id)
    {
        $id = (int)$id;
        $product = $this->product->findPublicById($id);

        if (!$product) {
            http_response_code(404);
            echo "Produk tidak ditemukan.";
            return;
        }

        $this->renderFrontend('product/show', [
            'product' => $product,
        ], 'Detail Produk');
    }
}
