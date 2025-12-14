<?php
// app/controllers/PricingController.php

require_once __DIR__ . '/../models/ProductOption.php';

class PricingController extends Controller
{
    protected mysqli $db;
    protected ProductOption $opt;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->db  = db();
        $this->opt = new ProductOption();
    }

    private function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function calc()
    {
        $body = json_decode(file_get_contents('php://input'), true);
        if (!is_array($body)) $body = $_POST;

        $productId = (int)($body['product_id'] ?? 0);
        $qty       = (int)($body['qty'] ?? 1);
        if ($qty < 1) $qty = 1;

        $selectedIds = $body['option_value_ids'] ?? $body['selected_value_ids'] ?? [];
        if (!is_array($selectedIds)) $selectedIds = [];
        $selectedIds = array_values(array_unique(array_map('intval', $selectedIds)));

        if ($productId <= 0) $this->json(['ok'=>false,'message'=>'product_id wajib'], 422);

        // product must active & not deleted
        $stmt = $this->db->prepare(
            "SELECT id, name, base_price
             FROM products
             WHERE id = ? AND is_active = 1 AND deleted_at IS NULL
             LIMIT 1"
        );
        if (!$stmt) $this->json(['ok'=>false,'message'=>'DB error: '.$this->db->error], 500);

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $res = $stmt->get_result();
        $product = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        if (!$product) $this->json(['ok'=>false,'message'=>'Produk tidak ditemukan / tidak aktif'], 404);

        $basePrice = (float)($product['base_price'] ?? 0);

        // VALIDATION via DB (robust)
        $val = $this->opt->validateSelectionsForProduct($productId, $selectedIds);

        $fixedAdd = 0.0;
        $percentAddTotal = 0.0;
        $selectedOptions = [];

        foreach ($selectedIds as $vid) {
            if (!isset($val['value_detail'][$vid])) continue;
            $v = $val['value_detail'][$vid];

            $pt = strtolower(trim((string)($v['price_type'] ?? 'fixed')));
            $pv = (float)($v['price_value'] ?? 0);

            if ($pt === 'percent') $percentAddTotal += $pv;
            else $fixedAdd += $pv;

            $selectedOptions[] = [
                'id'          => (int)$v['id'],
                'group_id'    => (int)$v['group_id'],
                'label'       => (string)$v['label'],
                'price_type'  => $pt,
                'price_value' => (float)$pv,
            ];
        }

        $unitPercentValue = ($percentAddTotal > 0)
            ? ($basePrice * ($percentAddTotal / 100.0))
            : 0.0;

        $unitPrice = $basePrice + $fixedAdd + $unitPercentValue;
        $subtotal  = $unitPrice * $qty;

        $this->json([
            'ok' => true,
            'product' => [
                'id' => (int)$product['id'],
                'name' => (string)$product['name'],
                'base_price' => $basePrice,
            ],
            'qty' => $qty,
            'breakdown' => [
                'base' => $basePrice,
                'fixed_add' => $fixedAdd,
                'percent_add_total' => $percentAddTotal,
                'unit_percent_value' => $unitPercentValue,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ],
            'selected_options' => $selectedOptions
        ], 200);
    }

    public function options()
    {
        $productId = (int)($_GET['product_id'] ?? 0);
        if ($productId <= 0) $this->json(['ok'=>false,'message'=>'product_id wajib'], 422);

        // product must active & not deleted
        $stmt = $this->db->prepare(
            "SELECT id, name, base_price
            FROM products
            WHERE id = ? AND is_active = 1 AND deleted_at IS NULL
            LIMIT 1"
        );
        if (!$stmt) $this->json(['ok'=>false,'message'=>'DB error: '.$this->db->error], 500);

        $stmt->bind_param('i', $productId);
        $stmt->execute();
        $res = $stmt->get_result();
        $product = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        if (!$product) $this->json(['ok'=>false,'message'=>'Produk tidak ditemukan / tidak aktif'], 404);

        $groups = $this->opt->getGroupsWithValues($productId);

        $this->json([
            'ok' => true,
            'product' => [
                'id' => (int)$product['id'],
                'name' => (string)$product['name'],
                'base_price' => (float)$product['base_price'],
            ],
            'groups' => $groups
        ], 200);
    }

}
