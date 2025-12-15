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

    public function index()
    {
        // ambil semua store aktif untuk public
        $stores = $this->store->publicList(200);

        // pilih store utama: HQ kalau ada, kalau tidak ambil yang pertama
        $main = null;
        foreach ($stores as $s) {
            if (($s['office_type'] ?? '') === 'hq') {
                $main = $s;
                break;
            }
        }
        if (!$main) $main = $stores[0] ?? null;

        $this->renderFrontend('our_store/index', [
            'page'      => 'our-home',
            'stores'    => $stores,
            'storeMain' => $main,
        ], 'Our Home');
    }
}
