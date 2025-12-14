<?php
// app/controllers/HomePublicController.php

require_once __DIR__ . '/../models/Product.php';
$view = realpath(__DIR__ . '/../views/frontend/home/index.php');
if (!$view) { die("Home view not found"); }

$layout = realpath(__DIR__ . '/../views/frontend/layout/main.php');
if (!$layout) { die("Layout main not found"); }

require $layout;

class HomePublicController
{
    public function index()
    {
        $productModel = new Product();

        $services = $productModel->getPublicServices(8, true);

        $vars = [
            'baseUrl'         => '/eventprint/public',
            'title'           => 'EventPrint — Home',
            'services'        => $services,

            // ganti ini sesuai struktur upload kamu
            'productUploadDir' => 'product', // atau 'products'
        ];

        $view = __DIR__ . '/../views/frontend/home/index.php';
        require __DIR__ . '/../views/frontend/layout/main.php';
    }


}
