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

        // Fetch all stores WITH email and hours
        $stores = [];
        $res = $this->db->query("
            SELECT id, name, slug, office_type, address, city, phone, email, whatsapp, hours, gmaps_url, thumbnail
            FROM our_store
            WHERE is_active=1
            ORDER BY sort_order ASC, name ASC
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $stores[] = $r;
            }
        }

        // Fetch ALL gallery images from all stores for machine gallery display
        $galleryImages = [];
        $res = $this->db->query("
            SELECT g.id, g.image_path, g.caption, g.sort_order, s.name as store_name
            FROM our_store_gallery g
            JOIN our_store s ON g.store_id = s.id
            WHERE s.is_active = 1
            ORDER BY g.sort_order ASC, g.id ASC
        ");

        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $galleryImages[] = [
                    'id' => $r['id'],
                    'image' => $r['image_path'],
                    'title' => $r['store_name'],
                    'caption' => $r['caption'] ?: 'Gallery Photo'
                ];
            }
        }

        // Fetch machines data from page_contents
        $machinesRaw = [];
        $res = $this->db->query("
            SELECT item_key, field, value
            FROM page_contents
            WHERE page_slug='our-home' AND section='machines'
            ORDER BY CAST(item_key AS UNSIGNED) ASC, field ASC
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $machinesRaw[] = $r;
            }
        }

        // Transform machines: group by item_key
        $machines = [];
        foreach ($machinesRaw as $row) {
            $key = $row['item_key'];
            if (!isset($machines[$key])) {
                $machines[$key] = ['id' => $key];
            }
            $machines[$key][$row['field']] = $row['value'];
        }
        $machines = array_values($machines); // re-index to 0-based array

        // Combine machines from page_contents with user-uploaded gallery
        $allGallery = array_merge($machines, $galleryImages);

        // Fetch page content headers (new)
        $content = [];
        $res = $this->db->query("
            SELECT field, value
            FROM page_contents
            WHERE page_slug='our-home' AND section='our_home_content'
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $content[$r['field']] = $r['value'];
            }
        }

        $this->renderFrontend('pages/our_home', [
            'page' => 'our_home',
            'title' => ($content['page_title'] ?? 'Our Home') . ' - EventPrint',
            // settings auto-injected
            'stores' => $stores,
            'machines' => $allGallery,
            'content' => $content, // Pass content headers
            // REFERENCE SCRIPT ORDER: utils → app → renderOurHome
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
