<?php
// app/controllers/OurStorePublicController.php

require_once __DIR__ . '/../models/OurStore.php';

class OurStorePublicController extends Controller
{
    protected OurStore $store;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->store = new OurStore();
    }

    /**
     * GET /our-home
     * List semua lokasi / cabang Our Store.
     */
    public function index()
    {
        $items = $this->store->publicList();

        // SEMENTARA: tetap pakai view 'our_store/index'
        // supaya lu gak perlu utak-atik frontend dulu.
        $this->renderFrontend('our_store/index', [
            'items' => $items,
        ], 'Our Store');
    }

    /**
     * GET /our-home/{slug}
     * Detail satu lokasi Our Store.
     */
    public function show($slug)
    {
        $slug = trim($slug ?? '');
        if ($slug === '') {
            http_response_code(404);
            echo "Data tidak ditemukan.";
            return;
        }

        $item = $this->store->publicFindBySlug($slug);
        if (!$item) {
            http_response_code(404);
            echo "Data tidak ditemukan.";
            return;
        }

        $this->renderFrontend('our_store/show', [
            'item' => $item,
        ], $item['title'] ?? 'Detail Our Store');
    }
}
