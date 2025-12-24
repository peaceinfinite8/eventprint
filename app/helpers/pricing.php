<?php
// app/helpers/pricing.php
// Product pricing calculation helpers

/**
 * Calculate product price with options, tiers, and discount
 * 
 * @param array $product Product data with base_price, discount_type, discount_value
 * @param int $quantity Order quantity
 * @param int|null $optionId Selected option ID (optional)
 * @return array ['unit_price' => float, 'subtotal' => float, 'discount' => float, 'final_total' => float, 'option' => array|null, 'tier' => array|null]
 */
function calculate_product_price(array $product, int $quantity, ?int $optionId = null): array
{
    $db = db();
    $productId = (int) $product['id'];

    // 1. Get base unit price from tier or product
    $unitPrice = (float) $product['base_price'];
    $tier = null;

    // Check for price tiers
    $tierStmt = $db->prepare("
        SELECT * FROM product_price_tiers
        WHERE product_id = ? AND is_active = 1
        AND qty_min <= ?
        AND (qty_max IS NULL OR qty_max >= ?)
        ORDER BY qty_min DESC
        LIMIT 1
    ");
    $tierStmt->bind_param('iii', $productId, $quantity, $quantity);
    $tierStmt->execute();
    $tierResult = $tierStmt->get_result();

    if ($tierResult->num_rows > 0) {
        $tier = $tierResult->fetch_assoc();
        $unitPrice = (float) $tier['unit_price'];
    }
    $tierStmt->close();

    // 2. Apply option price delta
    $option = null;
    if ($optionId !== null) {
        $optionStmt = $db->prepare("
            SELECT * FROM product_options
            WHERE id = ? AND product_id = ? AND is_active = 1
        ");
        $optionStmt->bind_param('ii', $optionId, $productId);
        $optionStmt->execute();
        $optionResult = $optionStmt->get_result();

        if ($optionResult->num_rows > 0) {
            $option = $optionResult->fetch_assoc();
            $unitPrice += (float) $option['price_delta'];
        }
        $optionStmt->close();
    }

    // 3. Calculate subtotal
    $subtotal = $unitPrice * $quantity;

    // 4. Apply discount
    $discount = 0;
    $discountType = $product['discount_type'] ?? 'none';
    $discountValue = (float) ($product['discount_value'] ?? 0);

    if ($discountType === 'percent' && $discountValue > 0) {
        $discount = $subtotal * ($discountValue / 100);
    } elseif ($discountType === 'fixed' && $discountValue > 0) {
        $discount = $discountValue;
    }

    // Ensure discount doesn't exceed subtotal
    $discount = min($discount, $subtotal);

    $finalTotal = max(0, $subtotal - $discount);

    return [
        'unit_price' => $unitPrice,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'final_total' => $finalTotal,
        'option' => $option,
        'tier' => $tier
    ];
}

/**
 * Get all active options for a product
 */
function get_product_options(int $productId): array
{
    $db = db();
    $options = [];

    $stmt = $db->prepare("
        SELECT * FROM product_options
        WHERE product_id = ? AND is_active = 1
        ORDER BY sort_order ASC, name ASC
    ");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }

    $stmt->close();
    return $options;
}

/**
 * Get all active price tiers for a product
 */
function get_product_tiers(int $productId): array
{
    $db = db();
    $tiers = [];

    $stmt = $db->prepare("
        SELECT * FROM product_price_tiers
        WHERE product_id = ? AND is_active = 1
        ORDER BY qty_min ASC
    ");
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $row['qty_min'] = (int) $row['qty_min'];
        $row['qty_max'] = $row['qty_max'] !== null ? (int) $row['qty_max'] : null;
        $row['unit_price'] = (float) $row['unit_price'];
        $tiers[] = $row;
    }

    $stmt->close();
    return $tiers;
}

/**
 * Generate WhatsApp order message
 */
function generate_whatsapp_order_message(array $product, array $pricing, int $quantity, ?string $notes = null): string
{
    $productName = $product['name'] ?? 'Produk';
    $optionName = $pricing['option']['name'] ?? null;
    $unitPrice = format_rupiah($pricing['unit_price']);
    $finalTotal = format_rupiah($pricing['final_total']);

    $message = "*Pesanan Baru*\n\n";
    $message .= "Produk: {$productName}\n";

    if ($optionName) {
        $message .= "Pilihan: {$optionName}\n";
    }

    $message .= "Jumlah: {$quantity}\n";
    $message .= "Harga Satuan: {$unitPrice}\n";

    if ($pricing['discount'] > 0) {
        $message .= "Diskon: " . format_rupiah($pricing['discount']) . "\n";
    }

    $message .= "Total: {$finalTotal}\n";

    if ($notes) {
        $message .= "\nCatatan:\n{$notes}\n";
    }

    $message .= "\n_File desain akan dikirim terpisah jika ada_";

    return $message;
}
