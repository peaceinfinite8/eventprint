<?php
// app/controllers/ProductPublicController.php

require_once __DIR__ . '/../models/Product.php';

class ProductPublicController extends Controller
{
    public function index()
    {
        $m = new Product();
        $products = $m->getPublicAllWithCategory(200);

        $this->renderFrontend('product/index', [
  'title' => 'EventPrint — Produk & Layanan',
  'page'  => 'products',
  'products' => $products,
]);

    }

    public function detail($slug)
    {
        $m = new Product();
        $product = $m->findPublicBySlug($slug);

        $this->renderFrontend('product/show', [
  'title' => 'EventPrint — Detail Produk',
  'page'  => 'products',
  'css'   => ['/assets/frontend/css/product-detail.css'],
  'product' => $product,
]);

    }
}
