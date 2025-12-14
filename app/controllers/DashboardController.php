<?php
// app/controllers/DashboardController.php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/OurStore.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/ContactMessage.php';

class DashboardController extends Controller
{
    protected Product $product;
    protected OurStore $store;
    protected Post $post;
    protected ContactMessage $message;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->product = new Product();
        $this->store   = new OurStore();
        $this->post    = new Post();
        $this->message = new ContactMessage();
    }

    public function index()
    {
        $stats = [
            'products'       => $this->product->countAll(),
            'our_store'      => $this->store->countAll(),
            'blog'           => $this->post->countPublished(),
            'contact_unread' => $this->message->countUnread(),
        ];

        // ====== Produk terbaru + diskon + pagination (10 per page)
        $prodPage    = max(1, (int)($_GET['prod_page'] ?? 1));
        $prodPerPage = 12;
        $latestProductsResult = $this->product->getLatestWithDiscountsPaginated($prodPage, $prodPerPage);

        $latestStores   = $this->store->getLatest(2);
        $latestPosts    = $this->post->getLatest(5);
        $latestContacts = $this->message->getLatest(5);
        $hero           = $this->getHeroData();

        $this->renderAdmin('dashboard/index', [
            'stats' => [
                'products'       => $stats['products'],
                'stores'         => $stats['our_store'], // biar view lama nggak rusak
                'blog'           => $stats['blog'],
                'contact_unread' => $stats['contact_unread'],
            ],

            'latestProducts'   => $latestProductsResult['items'],
            'latestProductsPg' => [
                'total'    => $latestProductsResult['total'],
                'page'     => $latestProductsResult['page'],
                'per_page' => $latestProductsResult['per_page'],
            ],

            'latestStores'     => $latestStores,
            'latestPosts'      => $latestPosts,
            'latestContacts'   => $latestContacts,
            'hero'             => $hero,
        ], 'Dashboard');
    }

    protected function getHeroData(): array
    {
        $db  = db();
        $sql = "SELECT field, value
                FROM page_contents
                WHERE page_slug = 'home'
                  AND section   = 'hero'";
        $res = $db->query($sql);

        $data = [
            'title'       => '',
            'subtitle'    => '',
            'button_text' => '',
            'button_link' => '',
        ];

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $field = $row['field'];
                if (array_key_exists($field, $data)) {
                    $data[$field] = (string)$row['value'];
                }
            }
        }

        return $data;
    }
}
