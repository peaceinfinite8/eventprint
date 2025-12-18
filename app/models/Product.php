<?php
// app/models/Product.php

class Product
{
    protected function db(): mysqli
    {
        return db();
    }


    protected mysqli $db;

    public function __construct()
    {
        $this->db = db(); // pastikan helper db() mengembalikan mysqli
    }
    
    public function getPublicCategories(): array
    {
        $sql = "SELECT id, name, slug, icon, sort_order
            FROM product_categories
            WHERE is_active=1
            ORDER BY sort_order ASC, id ASC";
        $res = $this->db->query($sql);
        $cats = [];

        // "all" pertama biar sidebar kamu tetap cocok
        $cats[] = ['id' => 'all', 'name' => 'Semua Produk', 'subcategories' => []];

        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $cats[] = [
                    'id' => $r['slug'],
                    'name' => $r['name'],
                    'subcategories' => [] // belum ada subcategory di DB
                ];
            }
        }

        return $cats;
    }



    public function countAll(): int
    {
        $db  = $this->db();
        $sql = "SELECT COUNT(*) AS total FROM products WHERE deleted_at IS NULL";
        $res = $db->query($sql);
        $row = $res ? $res->fetch_assoc() : null;
        return $row ? (int)$row['total'] : 0;
    }

    public function getLatest(int $limit = 5): array
    {
        $db    = $this->db();
        $limit = max(1, (int)$limit);

        $sql  = "SELECT p.*
                 FROM products p
                 WHERE p.deleted_at IS NULL
                 ORDER BY p.created_at DESC
                 LIMIT ?";
        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res  = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    public function getLatestWithDiscountsPaginated(int $page = 1, int $perPage = 10): array
    {
        $db      = $this->db();
        $page    = max(1, (int)$page);
        $perPage = max(1, (int)$perPage);
        $offset  = ($page - 1) * $perPage;

        // total produk (untuk pagination)
        $resTotal = $db->query("SELECT COUNT(*) AS total FROM products WHERE deleted_at IS NULL");
        $rowTotal = $resTotal ? $resTotal->fetch_assoc() : null;
        $total    = $rowTotal ? (int)$rowTotal['total'] : 0;

        // diskon aktif terbaru per product (kalau ada)
        $sql = "
            SELECT
                p.id, p.name, p.thumbnail, p.short_description, p.base_price, p.is_featured, p.created_at,
                d.discount_type, d.discount_value, d.start_at, d.end_at, d.qty_total, d.qty_used
            FROM products p
            LEFT JOIN (
                SELECT dd.*
                FROM product_discounts dd
                INNER JOIN (
                    SELECT product_id, MAX(id) AS max_id
                    FROM product_discounts
                    WHERE is_active = 1
                    AND (start_at IS NULL OR start_at <= NOW())
                    AND (end_at   IS NULL OR end_at   >= NOW())
                    AND (qty_used < qty_total)
                    GROUP BY product_id
                ) x ON x.max_id = dd.id
            ) d ON d.product_id = p.id
            WHERE p.deleted_at IS NULL
            ORDER BY p.created_at DESC
            LIMIT ?, ?
        ";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param('ii', $offset, $perPage);
        $stmt->execute();
        $res   = $stmt->get_result();
        $items = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }


    public function getPublicList(int $limit = 50): array
    {
        $db = db();
        $limit = max(1, (int)$limit);

        $sql = "SELECT id, name, slug, thumbnail, short_description, base_price
                FROM products
                WHERE is_active = 1 AND deleted_at IS NULL
                ORDER BY id DESC
                LIMIT $limit";

        $res = $db->query($sql);
        $rows = [];
        if ($res) while ($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }

    // ===============================
// PUBLIC DETAIL (BY SLUG) + ALIAS
// ===============================
public function findPublicBySlug(string $slug): ?array
{
    $db   = $this->db();
    $slug = trim($slug);
    if ($slug === '') return null;

    $sql = "SELECT
                id, category_id, name, slug,
                short_description, description,
                thumbnail, base_price, stock,
                is_featured, is_active
            FROM products
            WHERE slug = ?
              AND is_active = 1
              AND deleted_at IS NULL
            LIMIT 1";

    $stmt = $db->prepare($sql);
    if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

public function findIdBySlug(string $slug): ?int
{
    $db = db(); // asumsi helper db() return mysqli

    $sql = "SELECT id FROM products WHERE slug = ? AND is_active = 1 AND deleted_at IS NULL LIMIT 1";
    $stmt = $db->prepare($sql);
    if (!$stmt) return null;

    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    return $row ? (int)$row['id'] : null;
}


/**
 * Alias biar typo lama nggak bikin fatal error lagi.
 * (Kalau ada file lain yang masih manggil method typo)
 */
public function findPubliBySlug(string $slug): ?array
{
    return $this->findPublicBySlug($slug);
}

public function findPublicbyIdPublic(int $id): ?array
{
    return $this->findPublicById((int)$id);
}

public function getPublicAllWithCategory(int $limit = 200): array
{
    $db = $this->db();
    $limit = max(1, (int)$limit);

    $sql = "SELECT p.id, p.name, p.slug, p.thumbnail, p.short_description, p.base_price,
                   c.name AS category_name, c.slug AS category_slug
            FROM products p
            LEFT JOIN product_categories c ON c.id = p.category_id
            WHERE p.is_active = 1 AND p.deleted_at IS NULL
            ORDER BY p.is_featured DESC, p.id DESC
            LIMIT ?";

    $stmt = $db->prepare($sql);
    if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    return $rows;
}

protected function renderFrontend(string $viewName, array $vars = []): void
{
    // viewName WAJIB: "product/index" (tanpa "frontend/" dan tanpa ".php")
    $baseUrl = $vars['baseUrl'] ?? '/eventprint/public';
    $vars['baseUrl'] = $baseUrl;

    // app/ sebagai root view
    $appPath = realpath(__DIR__ . '/..'); // app/
    if (!$appPath) die("App path tidak valid.");

    $viewName = trim($viewName, '/');
    $viewFile = $appPath . '/views/frontend/' . $viewName . '.php';
    $layout   = $appPath . '/views/frontend/layout/main.php';

    if (!file_exists($layout)) die("Layout frontend tidak ditemukan: " . $layout);
    if (!file_exists($viewFile)) die("View frontend tidak ditemukan: " . $viewFile);

    $view = $viewFile; // layout akan include $view
    $vars = $vars;
    include $layout;
}


    public function findPublicById(int $id): ?array
    {
        $db = db();
        $stmt = $db->prepare(
            "SELECT id, name, slug, thumbnail, description, short_description, base_price
            FROM products
            WHERE id = ? AND is_active = 1 AND deleted_at IS NULL
            LIMIT 1"
        );
        if (!$stmt) return null;

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function getPublicAll(): array
    {
        $db = db();

        $sql = "SELECT id, name, slug, short_description, base_price, thumbnail
                FROM products
                WHERE is_active = 1 AND deleted_at IS NULL
                ORDER BY id DESC";

        $res = $db->query($sql);
        $rows = [];
        if ($res) {
            while ($r = $res->fetch_assoc()) $rows[] = $r;
        }
        return $rows;
    }


    public function getPublicServices(int $limit = 8, bool $featuredFirst = true): array
    {
        $db    = $this->db();
        $limit = max(1, (int)$limit);

        // tampilkan produk aktif dan tidak dihapus
        if ($featuredFirst) {
            $sql = "SELECT p.*, c.name AS category_name
                    FROM products p
                    LEFT JOIN product_categories c ON c.id = p.category_id
                    WHERE p.deleted_at IS NULL AND p.is_active = 1
                    ORDER BY p.is_featured DESC, p.created_at DESC
                    LIMIT ?";
        } else {
            $sql = "SELECT p.*, c.name AS category_name
                    FROM products p
                    LEFT JOIN product_categories c ON c.id = p.category_id
                    WHERE p.deleted_at IS NULL AND p.is_active = 1
                    ORDER BY p.created_at DESC
                    LIMIT ?";
        }

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $res  = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }


    public function searchWithPagination(?string $keyword, ?int $categoryId, int $page, int $perPage): array
    {
        $db      = $this->db();
        $page    = max(1, $page);
        $perPage = max(1, $perPage);
        $offset  = ($page - 1) * $perPage;

        $keyword = $keyword !== null ? trim($keyword) : null;

        $where  = "WHERE p.deleted_at IS NULL";
        $params = [];
        $types  = '';

        if ($categoryId !== null) {
            $where   .= " AND p.category_id = ?";
            $params[] = $categoryId;
            $types   .= 'i';
        }

        if ($keyword !== null && $keyword !== '') {
            $where   .= " AND (p.name LIKE ? OR p.short_description LIKE ? OR p.slug LIKE ?)";
            $like     = '%' . $keyword . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
            $types   .= 'sss';
        }

        $sqlCount = "SELECT COUNT(*) AS total FROM products p $where";
        $stmt = $db->prepare($sqlCount);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res   = $stmt->get_result();
        $row   = $res ? $res->fetch_assoc() : null;
        $total = $row ? (int)$row['total'] : 0;
        $stmt->close();

        $sqlData = "SELECT p.*, c.name AS category_name
                    FROM products p
                    LEFT JOIN product_categories c ON c.id = p.category_id
                    $where
                    ORDER BY p.created_at DESC
                    LIMIT ?, ?";

        $stmt = $db->prepare($sqlData);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $params2 = $params;
        $types2  = $types . 'ii';
        $params2[] = $offset;
        $params2[] = $perPage;

        if (!empty($params)) $stmt->bind_param($types2, ...$params2);
        else $stmt->bind_param('ii', $offset, $perPage);

        $stmt->execute();
        $res   = $stmt->get_result();
        $items = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return [
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    public function find(int $id): ?array
    {
        $db  = $this->db();
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN product_categories c ON c.id = p.category_id
                WHERE p.id = ? AND p.deleted_at IS NULL
                LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        return $row ?: null;
    }

    public function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $db   = $this->db();
        $slug = trim($slug);
        if ($slug === '') return false;

        $sql = "SELECT id FROM products WHERE slug = ? AND deleted_at IS NULL";
        if ($ignoreId !== null) $sql .= " AND id <> ?";
        $sql .= " LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        if ($ignoreId !== null) $stmt->bind_param('si', $slug, $ignoreId);
        else $stmt->bind_param('s', $slug);

        $stmt->execute();
        $res = $stmt->get_result();
        $ok  = $res && $res->num_rows > 0;
        $stmt->close();

        return $ok;
    }

    public function create(array $data): int
    {
        $db = $this->db();

        $categoryId       = !empty($data['category_id']) ? (int)$data['category_id'] : null;
        $name             = trim($data['name'] ?? '');
        $slug             = trim($data['slug'] ?? '');
        $shortDescription = $data['short_description'] ?? null;
        $description      = $data['description'] ?? null;
        $thumbnail        = $data['thumbnail'] ?? null;
        $basePrice        = (float)($data['base_price'] ?? 0);
        $stock            = (int)($data['stock'] ?? 0);
        $isFeatured       = (int)($data['is_featured'] ?? 0);
        $isActive         = (int)($data['is_active'] ?? 0);

        if ($name === '') throw new Exception("Nama produk wajib diisi.");
        if ($stock < 0) $stock = 0;
        if ($slug === '') $slug = $name;

        $shortDescription = ($shortDescription !== null && $shortDescription !== '') ? $shortDescription : null;
        $description      = ($description !== null && $description !== '') ? $description : null;
        $thumbnail        = ($thumbnail !== null && $thumbnail !== '') ? $thumbnail : null;

        $sql = "INSERT INTO products
                (category_id, name, slug, short_description, description,
                 thumbnail, base_price, stock, is_featured, is_active,
                 created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param(
            'isssssdiii',
            $categoryId,
            $name,
            $slug,
            $shortDescription,
            $description,
            $thumbnail,
            $basePrice,
            $stock,
            $isFeatured,
            $isActive
        );

        $stmt->execute();
        $id = (int)$stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $db = $this->db();

        $categoryId       = !empty($data['category_id']) ? (int)$data['category_id'] : null;
        $name             = trim($data['name'] ?? '');
        $slug             = trim($data['slug'] ?? '');
        $shortDescription = $data['short_description'] ?? null;
        $description      = $data['description'] ?? null;
        $thumbnail        = $data['thumbnail'] ?? null;
        $basePrice        = (float)($data['base_price'] ?? 0);
        $stock            = (int)($data['stock'] ?? 0);
        $isFeatured       = (int)($data['is_featured'] ?? 0);
        $isActive         = (int)($data['is_active'] ?? 0);

        if ($name === '') throw new Exception("Nama produk wajib diisi.");
        if ($stock < 0) $stock = 0;
        if ($slug === '') $slug = $name;

        $shortDescription = ($shortDescription !== null && $shortDescription !== '') ? $shortDescription : null;
        $description      = ($description !== null && $description !== '') ? $description : null;
        $thumbnail        = ($thumbnail !== null && $thumbnail !== '') ? $thumbnail : null;

        $sql = "UPDATE products
                SET category_id       = ?,
                    name              = ?,
                    slug              = ?,
                    short_description = ?,
                    description       = ?,
                    thumbnail         = ?,
                    base_price        = ?,
                    stock             = ?,
                    is_featured       = ?,
                    is_active         = ?,
                    updated_at        = NOW()
                WHERE id = ? AND deleted_at IS NULL
                LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param(
            'isssssdiiii',
            $categoryId,
            $name,
            $slug,
            $shortDescription,
            $description,
            $thumbnail,
            $basePrice,
            $stock,
            $isFeatured,
            $isActive,
            $id
        );

        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();

        return $ok;
    }

    public function delete(int $id): bool
    {
        $db = $this->db();
        $sql = "UPDATE products SET deleted_at = NOW() WHERE id = ? LIMIT 1";

        $stmt = $db->prepare($sql);
        if (!$stmt) throw new Exception("Prepare failed: " . $db->error);

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();

        return $ok;
    }
}
