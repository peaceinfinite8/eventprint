<?php
// POSISI: app/controllers/FrontendController.php

class FrontendController extends Controller
{
    public function home()
        {
            $baseUrl = $this->baseUrl(); // dari Controller baseUrl()
            $vars = [
                'baseUrl' => $baseUrl,
                'title' => 'Home - EventPrint | Digital Printing & Media Promosi',
                'metaDescription' => 'EventPrint - Solusi cetak digital berkualitas untuk kebutuhan event dan promosi Anda.',
                'page' => 'home',
                'data' => null, // nanti bisa preload dari DB
            ];

            $contentView = __DIR__ . '/../../views/frontend/home/index.php';
            require __DIR__ . '/../../views/frontend/layout/main.php';
        }

}
