<?php
// app/controllers/BlogPublicController.php

require_once __DIR__ . '/../core/Controller.php';

class BlogPublicController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    public function index(): void
    {
        // Fetch settings
        $settingsRow = $this->db->query("SELECT * FROM settings WHERE id=1 LIMIT 1")->fetch_assoc();
        $settings = $settingsRow ?: [];

        // Pagination
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 9;
        $offset = ($page - 1) * $perPage;

        // Fetch featured posts (for hero mosaic)
        $featuredPosts = [];
        $res = $this->db->query("
            SELECT id, title, slug, excerpt, thumbnail, published_at
            FROM posts
            WHERE is_published=1 AND is_featured=1 AND deleted_at IS NULL
            ORDER BY published_at DESC
            LIMIT 4
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $featuredPosts[] = $r;
            }
        }

        // Count total posts
        $countResult = $this->db->query("
            SELECT COUNT(*) as total
            FROM posts
            WHERE is_published=1 AND deleted_at IS NULL
        ");
        $totalPosts = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalPosts / $perPage);

        // Fetch latest posts
        $posts = [];
        $res = $this->db->query("
            SELECT id, title, slug, excerpt, thumbnail, published_at
            FROM posts
            WHERE is_published=1 AND deleted_at IS NULL
            ORDER BY published_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $posts[] = $r;
            }
        }

        $this->renderFrontend('pages/blog', [
            'page' => 'blog',
            'title' => 'Blog & Artikel',
            'settings' => $settings,
            'featuredPosts' => $featuredPosts,
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    public function show($slug): void
    {
        // Fetch settings
        $settingsRow = $this->db->query("SELECT * FROM settings WHERE id=1 LIMIT 1")->fetch_assoc();
        $settings = $settingsRow ?: [];

        // Fetch post by slug
        $stmt = $this->db->prepare("
            SELECT id, title, slug, content, thumbnail, published_at
            FROM posts
            WHERE slug=? AND is_published=1 AND deleted_at IS NULL
        ");
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        $stmt->close();

        if (!$post) {
            http_response_code(404);
            $this->renderFrontend('errors/404', [
                'settings' => $settings,
                'title' => 'Article Not Found'
            ]);
            return;
        }

        // Fetch related posts (same category or random)
        $relatedPosts = [];
        $res = $this->db->query("
            SELECT id, title, slug, thumbnail, published_at
            FROM posts
            WHERE is_published=1 AND deleted_at IS NULL AND id != {$post['id']}
            ORDER BY published_at DESC
            LIMIT 3
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $relatedPosts[] = $r;
            }
        }

        $this->renderFrontend('pages/blog_detail', [
            'page' => 'blog_detail',
            'title' => e($post['title']) . ' - Blog',
            'settings' => $settings,
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
