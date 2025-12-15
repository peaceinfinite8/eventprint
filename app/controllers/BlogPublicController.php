<?php
// app/controllers/BlogPublicController.php

class BlogPublicController extends Controller
{
    protected mysqli $db;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db = db();
    }

    public function index()
    {
        $items = [];

        $sql = "SELECT id, title, slug, excerpt, thumbnail, published_at
                FROM posts
                WHERE is_published = 1 AND (deleted_at IS NULL)
                ORDER BY published_at DESC, id DESC
                LIMIT 30";
        $res = $this->db->query($sql);
        if ($res) while ($r = $res->fetch_assoc()) $items[] = $r;

        $this->renderFrontend('blog/index', [
            'page'  => 'articles',
            'posts' => $items,
        ], 'EventPrint — Artikel');
    }

    // /articles/{slug}
    public function show($slug)
    {
        $slug = trim((string)$slug);
        if ($slug === '') {
            http_response_code(404);
            echo "Artikel tidak ditemukan.";
            return;
        }

        $stmt = $this->db->prepare(
            "SELECT id, title, slug, content, thumbnail, published_at
             FROM posts
             WHERE slug = ? AND is_published = 1 AND (deleted_at IS NULL)
             LIMIT 1"
        );
        $post = null;

        if ($stmt) {
            $stmt->bind_param('s', $slug);
            $stmt->execute();
            $res = $stmt->get_result();
            $post = $res ? $res->fetch_assoc() : null;
            $stmt->close();
        }

        if (!$post) {
            http_response_code(404);
            echo "Artikel tidak ditemukan.";
            return;
        }

        $this->renderFrontend('blog/show', [
            'page' => 'articles',
            'post' => $post,
        ], 'EventPrint — ' . ($post['title'] ?? 'Artikel'));
    }
}
