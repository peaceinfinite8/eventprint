<?php
// app/controllers/OurStorePublicController.php

require_once __DIR__ . '/../core/Controller.php';

class OurStorePublicController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    public function index(): void
    {
        // Settings auto-injected

        // Fetch all stores
        $stores = [];
        $res = $this->db->query("
            SELECT id, name, slug, office_type, address, city, phone, whatsapp, gmaps_url, thumbnail
            FROM our_store
            WHERE is_active=1
            ORDER BY sort_order ASC, name ASC
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $stores[] = $r;
            }
        }

        $this->renderFrontend('our_store/index', [
            'page' => 'our_home',
            'title' => 'Our Home - Lokasi Toko',
            // settings auto-injected
            'stores' => $stores,
            'additionalJs' => [
                'frontend/js/render/renderOurHome.js'
            ]
        ]);
    }

    public function apiStores(): void
    {
        header('Content-Type: application/json');

        // Fetch all active stores
        $stores = [];
        $res = $this->db->query("
            SELECT id, name, slug, office_type, address, city, phone, whatsapp, gmaps_url, thumbnail
            FROM our_store
            WHERE is_active=1
            ORDER BY sort_order ASC, name ASC
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $stores[] = $r;
            }
        }

        echo json_encode([
            'success' => true,
            'stores' => $stores
        ]);
    }
}
