<?php
// app/controllers/BlogPublicController.php

require_once __DIR__ . '/../core/controller.php';

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

        // Fetch featured posts (Revisi: Hapus filter is_featured yang tidak ada di DB User)
        // Kita ambil 4 post terbaru saja sebagai pengganti "featured"
        $featuredPosts = [];
        $res = $this->db->query("
            SELECT id, title, slug, excerpt, thumbnail, published_at, created_at
            FROM posts
            WHERE is_published=1
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
            WHERE is_published=1
        ");
        $totalPosts = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalPosts / $perPage);

        // Fetch latest posts
        $posts = [];
        $res = $this->db->query("
            SELECT id, title, slug, excerpt, thumbnail, published_at, created_at
            FROM posts
            WHERE is_published=1
            ORDER BY published_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $posts[] = $r;
            }
        }

        // FIX: Path view yang benar adalah 'pages/blog', bukan 'blog/index'
        $this->renderFrontend('pages/blog', [
            'page' => 'blog',
            'title' => 'Blog & Artikel',
            'settings' => $settings,
            'featuredPosts' => $featuredPosts,
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'additionalJs' => [
                'frontend/js/render/renderBlog.js'
            ]
        ]);
    }

    public function show($slug): void
    {
        // Fetch settings
        $settingsRow = $this->db->query("SELECT * FROM settings WHERE id=1 LIMIT 1")->fetch_assoc();
        $settings = $settingsRow ?: [];

        // Fetch post by slug (Hapus external_url dkk)
        $stmt = $this->db->prepare("
            SELECT id, title, slug, content, thumbnail, published_at
            FROM posts
            WHERE slug=? AND is_published=1
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

        // Fetch related posts
        $relatedPosts = [];
        $res = $this->db->query("
            SELECT id, title, slug, thumbnail, published_at
            FROM posts
            WHERE is_published=1 AND id != {$post['id']}
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
            'additionalJs' => [
                'frontend/js/render/renderBlogDetail.js'
            ]
        ]);
    }

    public function apiBlog(): void
    {
        header('Content-Type: application/json');

        // Fetch featured (fallback ke latest)
        $featuredPosts = [];
        $res = $this->db->query("
            SELECT id, title, slug, excerpt, content, thumbnail, published_at, post_type
            FROM posts
            WHERE is_published=1
            ORDER BY published_at DESC
            LIMIT 4
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $r['thumbnail'] = safeImageUrl($r['thumbnail'] ?? '', 'blog');
                $featuredPosts[] = $r;
            }
        }

        // Fetch recent posts
        $recentPosts = [];
        $res = $this->db->query("
            SELECT id, title, slug, excerpt, content, thumbnail, published_at, post_type
            FROM posts
            WHERE is_published=1
            ORDER BY published_at DESC
            LIMIT 10
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $r['thumbnail'] = safeImageUrl($r['thumbnail'] ?? '', 'blog');
                $recentPosts[] = $r;
            }
        }

        echo json_encode([
            'success' => true,
            'featured' => $featuredPosts,
            'recent' => $recentPosts
        ]);
    }

    public function apiPosts(): void
    {
        header('Content-Type: application/json');

        $posts = [];
        $res = $this->db->query("
            SELECT id, title, slug, excerpt, content, thumbnail, published_at, post_type
            FROM posts
            WHERE is_published=1
            ORDER BY published_at DESC
            LIMIT 20
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $r['thumbnail'] = safeImageUrl($r['thumbnail'] ?? '', 'blog');
                $posts[] = $r;
            }
        }

        echo json_encode(['success' => true, 'data' => $posts]);
    }

    public function apiBlogDetail($slug): void
    {
        header('Content-Type: application/json');

        try {
            // Fetch post by slug (Safe Query)
            $stmt = $this->db->prepare("
                SELECT id, title, slug, content, thumbnail, published_at, excerpt
                FROM posts
                WHERE slug=? AND is_published=1
            ");
            $stmt->bind_param('s', $slug);
            $stmt->execute();
            $result = $stmt->get_result();
            $post = $result->fetch_assoc();
            $stmt->close();

            if (!$post) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Article not found'
                ]);
                return;
            }

            // Safe URL for post thumbnail
            $post['thumbnail'] = safeImageUrl($post['thumbnail'] ?? '', 'blog');

            // Fetch related posts
            $relatedPosts = [];
            $res = $this->db->query("
                SELECT id, title, slug, thumbnail, published_at
                FROM posts
                WHERE is_published=1 AND id != {$post['id']}
                ORDER BY published_at DESC
                LIMIT 3
            ");
            if ($res) {
                while ($r = $res->fetch_assoc()) {
                    $r['thumbnail'] = safeImageUrl($r['thumbnail'] ?? '', 'blog');
                    $relatedPosts[] = $r;
                }
            }

            echo json_encode([
                'success' => true,
                'post' => $post,
                'relatedPosts' => $relatedPosts
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
