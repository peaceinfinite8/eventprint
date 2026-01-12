<?php
// app/models/Post.php

class Post
{
    protected function db()
    {
        return db();
    }

    /* ========== STAT & LIST ========== */

    public function countAll(): int
    {
        $db = $this->db();
        $res = $db->query("SELECT COUNT(*) AS total FROM posts");
        if ($res && $row = $res->fetch_assoc()) {
            return (int) $row['total'];
        }
        return 0;
    }

    public function countPublished(): int
    {
        $db = $this->db();
        $res = $db->query("SELECT COUNT(*) AS total FROM posts WHERE is_published = 1");
        if ($res && $row = $res->fetch_assoc()) {
            return (int) $row['total'];
        }
        return 0;
    }

    public function countDraft(): int
    {
        $db = $this->db();
        $res = $db->query("SELECT COUNT(*) AS total FROM posts WHERE is_published = 0");
        if ($res && $row = $res->fetch_assoc()) {
            return (int) $row['total'];
        }
        return 0;
    }

    public function getLatest(int $limit = 5): array
    {
        $db = $this->db();
        $limit = max(1, $limit);

        $sql = "SELECT *
                 FROM posts
                 ORDER BY COALESCE(published_at, created_at) DESC
                 LIMIT ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    public function searchWithPagination(?string $keyword, int $page, int $perPage): array
    {
        $db = $this->db();
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $keyword = $keyword !== null ? trim($keyword) : null;
        $where = '';
        $params = [];
        $types = '';

        if ($keyword !== null && $keyword !== '') {
            $where = "WHERE title LIKE ? OR excerpt LIKE ?";
            $like = '%' . $keyword . '%';
            $params = [$like, $like];
            $types = 'ss';
        }

        // total
        $sqlCount = "SELECT COUNT(*) AS total FROM posts " . $where;
        $stmt = $db->prepare($sqlCount);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $total = $row ? (int) $row['total'] : 0;
        $stmt->close();

        // data
        $sqlData = "SELECT *
                    FROM posts
                    $where
                    ORDER BY COALESCE(published_at, created_at) DESC
                    LIMIT ?, ?";
        $stmt = $db->prepare($sqlData);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        if ($params) {
            $mergedParams = [...$params, $offset, $perPage];
            $mergedTypes = $types . 'ii';
            $stmt->bind_param($mergedTypes, ...$mergedParams);
        } else {
            $stmt->bind_param('ii', $offset, $perPage);
        }

        $stmt->execute();
        $res = $stmt->get_result();
        $items = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    /* ========== FIND ========== */

    public function find(int $id): ?array
    {
        $db = $this->db();

        $sql = "SELECT * FROM posts WHERE id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $db = $this->db();

        $sql = "SELECT * FROM posts WHERE slug = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    /* ========== CREATE / UPDATE ========== */

    public function create(array $data): int
    {
        $db = $this->db();

        $title = trim($data['title'] ?? '');
        $slug = trim($data['slug'] ?? '');
        $excerpt = trim($data['excerpt'] ?? '');
        $content = trim($data['content'] ?? '');
        $thumb = trim($data['thumbnail'] ?? '');
        $isPublished = (int) ($data['is_published'] ?? 0);
        $postType = $data['post_type'] ?? 'normal';
        $category = $data['post_category'] ?? '';
        $externalUrl = $data['external_url'] ?? '';
        $linkTarget = $data['link_target'] ?? '_self';

        // NEW COLUMNS FIX
        $isFeatured = (int) ($data['is_featured'] ?? 0);
        $bgColor = $data['bg_color'] ?? null;

        if ($title === '' || $content === '') {
            throw new Exception("Title dan content wajib diisi.");
        }

        if ($slug === '') {
            $slug = $this->generateUniqueSlug($title, null);
        } else {
            $slug = $this->generateUniqueSlug($slug, null, true);
        }

        if ($excerpt === '') {
            $excerpt = mb_substr(strip_tags($content), 0, 200);
        }

        $publishedAt = $isPublished ? date('Y-m-d H:i:s') : null;
        $now = date('Y-m-d H:i:s');

        // Insert including IS_FEATURED and BG_COLOR
        $sql = "INSERT INTO posts
                 (title, slug, excerpt, content, thumbnail, is_published, published_at, created_at, updated_at, post_type, post_category, external_url, link_target, is_featured, bg_color)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param(
            'sssssisssssssis',
            $title,
            $slug,
            $excerpt,
            $content,
            $thumb,
            $isPublished,
            $publishedAt,
            $now, // created_at
            $now, // updated_at
            $postType,
            $category,
            $externalUrl,
            $linkTarget,
            $isFeatured, // Added
            $bgColor     // Added
        );
        $stmt->execute();
        $id = (int) $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function update(int $id, array $data): void
    {
        $db = $this->db();
        $post = $this->find($id);
        if (!$post) {
            throw new Exception("Post tidak ditemukan.");
        }

        $title = trim($data['title'] ?? '');
        $slug = trim($data['slug'] ?? '');
        $excerpt = trim($data['excerpt'] ?? '');
        $content = trim($data['content'] ?? '');
        $thumb = trim($data['thumbnail'] ?? '');
        $isPublished = (int) ($data['is_published'] ?? 0);
        $postType = $data['post_type'] ?? 'normal';
        $category = $data['post_category'] ?? '';
        $externalUrl = $data['external_url'] ?? '';
        $linkTarget = $data['link_target'] ?? '_self';

        // NEW COLUMNS FIX
        $isFeatured = (int) ($data['is_featured'] ?? 0);
        $bgColor = $data['bg_color'] ?? null;

        if ($title === '' || $content === '') {
            throw new Exception("Title dan content wajib diisi.");
        }

        if ($slug === '') {
            $slug = $this->generateUniqueSlug($title, $id);
        } else {
            $slug = $this->generateUniqueSlug($slug, $id, true);
        }

        if ($excerpt === '') {
            $excerpt = mb_substr(strip_tags($content), 0, 200);
        }

        $publishedAt = $post['published_at'];

        // draft → published
        if ($isPublished && !$post['is_published']) {
            $publishedAt = date('Y-m-d H:i:s');
        }

        // published → draft
        if (!$isPublished) {
            $publishedAt = null;
        }

        $updatedAt = date('Y-m-d H:i:s');

        // Update including IS_FEATURED and BG_COLOR
        $sql = "UPDATE posts
                SET title        = ?,
                    slug         = ?,
                    excerpt      = ?,
                    content      = ?,
                    thumbnail    = ?,
                    is_published = ?,
                    published_at = ?,
                    updated_at   = ?,
                    post_type    = ?,
                    post_category = ?,
                    external_url = ?,
                    link_target  = ?,
                    is_featured  = ?,
                    bg_color     = ?
                WHERE id = ?";

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param(
            'sssssissssssisi',
            $title,
            $slug,
            $excerpt,
            $content,
            $thumb,
            $isPublished,
            $publishedAt,
            $updatedAt,
            $postType,
            $category,
            $externalUrl,
            $linkTarget,
            $isFeatured, // Added
            $bgColor,    // Added
            $id
        );
        $stmt->execute();
        $stmt->close();
    }

    /* ========== SLUG & DELETE ========== */

    private function generateUniqueSlug(string $slug, ?int $ignoreId = null, bool $isCustom = false): string
    {
        $db = $this->db();

        if (!$isCustom) {
            $slug = strtolower(trim($slug));
            $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
            $slug = preg_replace('/\s+/', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
        }

        $baseSlug = $slug;
        $i = 1;

        while (true) {
            $sql = "SELECT id FROM posts WHERE slug = ?";
            $params = [$slug];

            if ($ignoreId !== null) {
                $sql .= " AND id != ?";
                $params[] = $ignoreId;
            }

            $stmt = $db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $db->error);
            }

            if ($ignoreId !== null) {
                $stmt->bind_param("si", ...$params);
            } else {
                $stmt->bind_param("s", $slug);
            }

            $stmt->execute();
            $res = $stmt->get_result();

            if ($res->num_rows === 0) {
                $stmt->close();
                break;
            }

            $slug = $baseSlug . '-' . $i;
            $i++;
            $stmt->close();
        }

        return $slug;
    }

    public function delete(int $id): bool
    {
        $db = $this->db();

        $sql = "DELETE FROM posts WHERE id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $db->error);
        }

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected > 0;
    }

}
