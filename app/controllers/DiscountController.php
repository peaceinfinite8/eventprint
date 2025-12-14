<?php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../models/ProductDiscount.php';

class DiscountController extends Controller
{
    protected ProductDiscount $discount;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->discount = new ProductDiscount();
    }

    private function role(): string
    {
        return strtolower((string)($_SESSION['user']['role'] ?? ''));
    }

    private function isAdminOrSuper(): bool
    {
        $r = $this->role();
        return $r === 'admin' || $r === 'super_admin';
    }

    private function isSuper(): bool
    {
        return $this->role() === 'super_admin';
    }

    private function requireSuper(string $redirectTo): void
    {
        if (!$this->isSuper()) {
            $this->redirectWithError($redirectTo, 'Forbidden: hanya Super Admin yang boleh mengubah data diskon.');
        }
    }

    private function v(?string $s): ?string
    {
        $s = $s !== null ? trim($s) : null;
        return ($s === '') ? null : $s;
    }

    // datetime-local "2025-12-13T10:30" -> "2025-12-13 10:30:00"
    private function dt(?string $s): ?string
    {
        $s = $this->v($s);
        if ($s === null) return null;
        $s = str_replace('T', ' ', $s);
        if (strlen($s) === 16) $s .= ':00';
        return $s;
    }

    private function getProductsOptions(): array
    {
        $db = db();
        $sql = "SELECT id, name, stock
                FROM products
                WHERE deleted_at IS NULL
                ORDER BY name ASC";

        $res = $db->query($sql);
        $rows = [];
        if ($res) while ($r = $res->fetch_assoc()) $rows[] = $r;
        return $rows;
    }

    // ===================== INDEX (admin boleh lihat) =====================

    public function index()
    {
        if (!$this->isAdminOrSuper()) {
            http_response_code(403);
            echo "Forbidden";
            return;
        }

        $q       = trim($_GET['q'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $result = $this->discount->paginate($q !== '' ? $q : null, $page, $perPage);

        $this->renderAdmin('discounts/index', [
            'items'      => $result['items'],
            'filter_q'   => $q,
            'pagination' => [
                'total'    => $result['total'],
                'page'     => $result['page'],
                'per_page' => $result['per_page'],
            ],
            'isSuper'    => $this->isSuper(), // buat view: tombol CRUD hanya super
        ], 'Diskon Produk');
    }

    // ===================== CREATE/STORE (super only) =====================

    public function create()
    {
        $this->requireSuper('admin/discounts');

        $this->renderAdmin('discounts/create', [
            'products' => $this->getProductsOptions(),
        ], 'Tambah Diskon');
    }

    public function store()
    {
        $this->requireSuper('admin/discounts');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError('admin/discounts/create', 'CSRF tidak valid.'); }

        $productId = (int)($_POST['product_id'] ?? 0);
        $type      = $_POST['discount_type'] ?? 'percent';
        $value     = (float)($_POST['discount_value'] ?? 0);
        $qtyTotal  = (int)($_POST['qty_total'] ?? 0);

        $startAt   = $this->dt($_POST['start_at'] ?? null);
        $endAt     = $this->dt($_POST['end_at'] ?? null);
        $isActive  = isset($_POST['is_active']) ? 1 : 0;

        if ($productId <= 0) $this->redirectWithError('admin/discounts/create', 'Produk wajib dipilih.');
        if ($type !== 'percent' && $type !== 'fixed') $this->redirectWithError('admin/discounts/create', 'Tipe diskon tidak valid.');
        if ($value <= 0) $this->redirectWithError('admin/discounts/create', 'Nilai diskon wajib > 0.');
        if ($type === 'percent' && $value > 100) $this->redirectWithError('admin/discounts/create', 'Diskon persen maksimal 100.');
        if ($qtyTotal <= 0) $this->redirectWithError('admin/discounts/create', 'Kuota diskon wajib > 0.');

        if ($startAt !== null && !strtotime($startAt)) $this->redirectWithError('admin/discounts/create', 'Start time tidak valid.');
        if ($endAt !== null && !strtotime($endAt)) $this->redirectWithError('admin/discounts/create', 'End time tidak valid.');
        if ($startAt !== null && $endAt !== null && strtotime($startAt) > strtotime($endAt)) {
            $this->redirectWithError('admin/discounts/create', 'Start time tidak boleh lebih besar dari end time.');
        }

        // cek stock produk
        $db = db();
        $stmt = $db->prepare("SELECT stock FROM products WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $res = $stmt->get_result();
        $p = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        if (!$p) $this->redirectWithError('admin/discounts/create', 'Produk tidak ditemukan.');
        $stock = (int)($p['stock'] ?? 0);
        if ($qtyTotal > $stock) $this->redirectWithError('admin/discounts/create', 'Kuota diskon tidak boleh melebihi stok.');

        // cegah overlap diskon aktif (kalau diaktifkan)
        if ($isActive === 1 && $this->discount->hasOverlappingActive($productId, $startAt, $endAt, null)) {
            $this->redirectWithError('admin/discounts/create', 'Sudah ada diskon aktif yang overlap untuk produk ini.');
        }

        $createdBy = (int)($_SESSION['user']['id'] ?? 0);

        try {
            $this->discount->create([
                'product_id'      => $productId,
                'discount_type'   => $type,
                'discount_value'  => $value,
                'qty_total'       => $qtyTotal,
                'start_at'        => $startAt,
                'end_at'          => $endAt,
                'is_active'       => $isActive,
                'created_by'      => $createdBy,
            ]);
        } catch (Exception $e) {
            $this->redirectWithError('admin/discounts/create', 'Gagal menyimpan: ' . $e->getMessage());
        }

        $this->redirectWithSuccess('admin/discounts', 'Diskon berhasil dibuat.');
    }

    // ===================== EDIT/UPDATE (super only) =====================

    public function edit($id)
    {
        $this->requireSuper('admin/discounts');

        $id = (int)$id;
        $item = $this->discount->find($id);
        if (!$item) { http_response_code(404); echo "Diskon tidak ditemukan."; return; }

        $this->renderAdmin('discounts/edit', [
            'item'     => $item,
            'products' => $this->getProductsOptions(),
        ], 'Edit Diskon');
    }

    public function update($id)
    {
        $this->requireSuper('admin/discounts');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/discounts/edit/$id", 'CSRF tidak valid.'); }

        $id = (int)$id;
        $current = $this->discount->find($id);
        if (!$current) { http_response_code(404); echo "Diskon tidak ditemukan."; return; }

        $productId = (int)($_POST['product_id'] ?? 0);
        $type      = $_POST['discount_type'] ?? 'percent';
        $value     = (float)($_POST['discount_value'] ?? 0);
        $qtyTotal  = (int)($_POST['qty_total'] ?? 0);

        $startAt   = $this->dt($_POST['start_at'] ?? null);
        $endAt     = $this->dt($_POST['end_at'] ?? null);
        $isActive  = isset($_POST['is_active']) ? 1 : 0;

        if ($productId <= 0) $this->redirectWithError("admin/discounts/edit/$id", 'Produk wajib dipilih.');
        if ($type !== 'percent' && $type !== 'fixed') $this->redirectWithError("admin/discounts/edit/$id", 'Tipe diskon tidak valid.');
        if ($value <= 0) $this->redirectWithError("admin/discounts/edit/$id", 'Nilai diskon wajib > 0.');
        if ($type === 'percent' && $value > 100) $this->redirectWithError("admin/discounts/edit/$id", 'Diskon persen maksimal 100.');
        if ($qtyTotal <= 0) $this->redirectWithError("admin/discounts/edit/$id", 'Kuota diskon wajib > 0.');

        if ((int)($current['qty_used'] ?? 0) > $qtyTotal) {
            $this->redirectWithError("admin/discounts/edit/$id", 'qty_total tidak boleh lebih kecil dari qty_used.');
        }

        if ($startAt !== null && !strtotime($startAt)) $this->redirectWithError("admin/discounts/edit/$id", 'Start time tidak valid.');
        if ($endAt !== null && !strtotime($endAt)) $this->redirectWithError("admin/discounts/edit/$id", 'End time tidak valid.');
        if ($startAt !== null && $endAt !== null && strtotime($startAt) > strtotime($endAt)) {
            $this->redirectWithError("admin/discounts/edit/$id", 'Start time tidak boleh lebih besar dari end time.');
        }

        // cek stock produk
        $db = db();
        $stmt = $db->prepare("SELECT stock FROM products WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $res = $stmt->get_result();
        $p = $res ? $res->fetch_assoc() : null;
        $stmt->close();
        if (!$p) $this->redirectWithError("admin/discounts/edit/$id", 'Produk tidak ditemukan.');

        $stock = (int)($p['stock'] ?? 0);
        if ($qtyTotal > $stock) $this->redirectWithError("admin/discounts/edit/$id", 'Kuota diskon tidak boleh melebihi stok.');

        // overlap
        if ($isActive === 1 && $this->discount->hasOverlappingActive($productId, $startAt, $endAt, $id)) {
            $this->redirectWithError("admin/discounts/edit/$id", 'Sudah ada diskon aktif yang overlap untuk produk ini.');
        }

        try {
            $this->discount->update($id, [
                'product_id'     => $productId,
                'discount_type'  => $type,
                'discount_value' => $value,
                'qty_total'      => $qtyTotal,
                'start_at'       => $startAt,
                'end_at'         => $endAt,
                'is_active'      => $isActive,
            ]);
        } catch (Exception $e) {
            $this->redirectWithError("admin/discounts/edit/$id", 'Gagal update: ' . $e->getMessage());
        }

        $this->redirectWithSuccess('admin/discounts', 'Diskon berhasil diperbarui.');
    }

    // ===================== DELETE (super only) =====================

    public function delete($id)
    {
        $this->requireSuper('admin/discounts');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/discounts", 'CSRF tidak valid.'); }

        $id = (int)$id;

        try {
            $this->discount->delete($id);
        } catch (Exception $e) {
            $this->redirectWithError("admin/discounts", 'Gagal hapus: ' . $e->getMessage());
        }

        $this->redirectWithSuccess("admin/discounts", "Diskon berhasil dihapus.");
    }
}
