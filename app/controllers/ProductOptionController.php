<?php
// app/controllers/ProductOptionController.php

require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/ProductOption.php';

class ProductOptionController extends Controller
{
    protected Product $product;
    protected ProductOption $opt;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->product = new Product();
        $this->opt     = new ProductOption();
    }

    private function role(): string
    {
        return (string)($_SESSION['user']['role'] ?? '');
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


    private function normalizeInputType(string $t): string
    {
        $t = strtolower(trim($t));
        return in_array($t, ['select','radio','checkbox'], true) ? $t : 'checkbox';
    }

    public function index($productId)
    {
        if (!$this->isAdminOrSuper()) { http_response_code(403); echo "Forbidden"; return; }

        $productId = (int)$productId;
        $p = $this->product->find($productId);
        if (!$p) { http_response_code(404); echo "Produk tidak ditemukan"; return; }

        $groups = $this->opt->getOptionsForProduct($productId);

        $this->renderAdmin('product_options/index', [
            'product' => $p,
            'groups'  => $groups,
            'isSuper' => $this->isSuper(),
        ], 'Opsi Harga Produk');
    }

    /* ===== GROUP (superadmin only) ===== */

    public function storeGroup($productId)
    {
        if (!$this->isSuper()) { http_response_code(403); echo "Forbidden"; return; }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/products/$productId/options", "CSRF tidak valid"); }

        $productId = (int)$productId;
        $p = $this->product->find($productId);
        if (!$p) { http_response_code(404); echo "Produk tidak ditemukan"; return; }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') $this->redirectWithError("admin/products/$productId/options", "Nama group wajib diisi.");

        $inputType  = $this->normalizeInputType($_POST['input_type'] ?? 'checkbox');
        $isRequired = isset($_POST['is_required']) ? 1 : 0;
        $minSelect  = max(0, (int)($_POST['min_select'] ?? 0));
        $maxSelect  = ($_POST['max_select'] ?? '') === '' ? 0 : max(0, (int)$_POST['max_select']);
        $sortOrder  = max(0, (int)($_POST['sort_order'] ?? 0));
        $isActive   = isset($_POST['is_active']) ? 1 : 0;

        if ($inputType === 'select' || $inputType === 'radio') {
            $maxSelect = 1;
            $minSelect = $isRequired ? 1 : 0;
        } else {
            if ($isRequired && $minSelect < 1) $minSelect = 1;
            if ($maxSelect > 0 && $maxSelect < $minSelect) $maxSelect = $minSelect;
        }

        $data = [
            'product_id'  => $productId,
            'name'        => $name,
            'input_type'  => $inputType,
            'min_select'  => $minSelect,
            'max_select'  => $maxSelect,
            'is_required' => $isRequired,
            'sort_order'  => $sortOrder,
            'is_active'   => $isActive,
        ];

        try { $this->opt->createGroup($data); }
        catch (Exception $e) { $this->redirectWithError("admin/products/$productId/options", $e->getMessage()); }

        $this->redirectWithSuccess("admin/products/$productId/options", "Group opsi berhasil dibuat.");
    }

    public function updateGroup($groupId)
    {
        if (!$this->isSuper()) { http_response_code(403); echo "Forbidden"; return; }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/products", "CSRF tidak valid"); }

        $groupId = (int)$groupId;

        $db = db();
        $stmt = $db->prepare("SELECT product_id FROM product_option_groups WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $res = $stmt->get_result();
        $g = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        $pid = (int)($g['product_id'] ?? 0);
        if ($pid <= 0) { http_response_code(404); echo "Group tidak ditemukan"; return; }

        $name = trim($_POST['name'] ?? '');
        if ($name === '') $this->redirectWithError("admin/products/$pid/options", "Nama group wajib diisi.");

        $inputType  = $this->normalizeInputType($_POST['input_type'] ?? 'checkbox');
        $isRequired = isset($_POST['is_required']) ? 1 : 0;
        $minSelect  = max(0, (int)($_POST['min_select'] ?? 0));
        $maxSelect  = ($_POST['max_select'] ?? '') === '' ? 0 : max(0, (int)$_POST['max_select']);
        $sortOrder  = max(0, (int)($_POST['sort_order'] ?? 0));
        $isActive   = isset($_POST['is_active']) ? 1 : 0;

        if ($inputType === 'select' || $inputType === 'radio') {
            $maxSelect = 1;
            $minSelect = $isRequired ? 1 : 0;
        } else {
            if ($isRequired && $minSelect < 1) $minSelect = 1;
            if ($maxSelect > 0 && $maxSelect < $minSelect) $maxSelect = $minSelect;
        }

        $data = [
            'name'        => $name,
            'input_type'  => $inputType,
            'min_select'  => $minSelect,
            'max_select'  => $maxSelect,
            'is_required' => $isRequired,
            'sort_order'  => $sortOrder,
            'is_active'   => $isActive,
        ];

        try { $this->opt->updateGroup($groupId, $data); }
        catch (Exception $e) { $this->redirectWithError("admin/products/$pid/options", $e->getMessage()); }

        $this->redirectWithSuccess("admin/products/$pid/options", "Group opsi berhasil diupdate.");
    }

    public function deleteGroup($groupId)
    {
        if (!$this->isSuper()) { http_response_code(403); echo "Forbidden"; return; }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/products", "CSRF tidak valid"); }

        $groupId = (int)$groupId;

        $db = db();
        $stmt = $db->prepare("SELECT product_id FROM product_option_groups WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $res = $stmt->get_result();
        $g = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        $pid = (int)($g['product_id'] ?? 0);
        if ($pid <= 0) { http_response_code(404); echo "Group tidak ditemukan"; return; }

        $this->opt->deleteGroup($groupId);
        $this->redirectWithSuccess("admin/products/$pid/options", "Group opsi berhasil dihapus.");
    }

    /* ===== VALUE (superadmin only) ===== */

    public function storeValue($groupId)
    {
        if (!$this->isSuper()) { http_response_code(403); echo "Forbidden"; return; }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/products", "CSRF tidak valid"); }

        $groupId = (int)$groupId;

        $db = db();
        $stmt = $db->prepare("SELECT product_id FROM product_option_groups WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $groupId);
        $stmt->execute();
        $res = $stmt->get_result();
        $g = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        $pid = (int)($g['product_id'] ?? 0);
        if ($pid <= 0) { http_response_code(404); echo "Group tidak ditemukan"; return; }

        $data = [
            'group_id'    => $groupId,
            'label'       => trim($_POST['label'] ?? ''),
            'price_type'  => $_POST['price_type'] ?? 'fixed',
            'price_value' => (float)($_POST['price_value'] ?? 0),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ];

        try { $this->opt->createValue($data); }
        catch (Exception $e) { $this->redirectWithError("admin/products/$pid/options", $e->getMessage()); }

        $this->redirectWithSuccess("admin/products/$pid/options", "Opsi berhasil ditambahkan.");
    }

    public function updateValue($valueId)
    {
        if (!$this->isSuper()) { http_response_code(403); echo "Forbidden"; return; }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/products", "CSRF tidak valid"); }

        $valueId = (int)$valueId;

        $db = db();
        $sql = "SELECT g.product_id
                FROM product_option_values v
                JOIN product_option_groups g ON g.id = v.group_id
                WHERE v.id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $valueId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        $pid = (int)($row['product_id'] ?? 0);
        if ($pid <= 0) { http_response_code(404); echo "Opsi tidak ditemukan"; return; }

        $data = [
            'label'       => trim($_POST['label'] ?? ''),
            'price_type'  => $_POST['price_type'] ?? 'fixed',
            'price_value' => (float)($_POST['price_value'] ?? 0),
            'sort_order'  => (int)($_POST['sort_order'] ?? 0),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ];

        try { $this->opt->updateValue($valueId, $data); }
        catch (Exception $e) { $this->redirectWithError("admin/products/$pid/options", $e->getMessage()); }

        $this->redirectWithSuccess("admin/products/$pid/options", "Opsi berhasil diupdate.");
    }

    public function deleteValue($valueId)
    {
        if (!$this->isSuper()) { http_response_code(403); echo "Forbidden"; return; }

        try { Security::requireCsrfToken(); }
        catch (Exception $e) { $this->redirectWithError("admin/products", "CSRF tidak valid"); }

        $valueId = (int)$valueId;

        $db = db();
        $sql = "SELECT g.product_id
                FROM product_option_values v
                JOIN product_option_groups g ON g.id = v.group_id
                WHERE v.id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $valueId);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        $pid = (int)($row['product_id'] ?? 0);
        if ($pid <= 0) { http_response_code(404); echo "Opsi tidak ditemukan"; return; }

        $this->opt->deleteValue($valueId);
        $this->redirectWithSuccess("admin/products/$pid/options", "Opsi berhasil dihapus.");
    }
}
