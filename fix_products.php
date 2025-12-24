<?php
// Script to fix product images and verify data
require 'app/config/db.php';
$db = db();

echo "=== FIXING PRODUCT IMAGES ===\n\n";

// Update products with proper image paths
$updates = [
    ["pattern" => "%Spanduk Flexi%", "image" => "uploads/products/spanduk_flexi.jpg"],
    ["pattern" => "%Roll Up%", "image" => "uploads/products/rollup_60x160.jpg"],
    ["pattern" => "%Banner%", "image" => "uploads/products/rollup_60x160.jpg"],
    ["pattern" => "%Brosur%", "image" => "uploads/products/brosur_a5.jpg"],
    ["pattern" => "%Kartu Nama%", "image" => "uploads/products/kartu_nama.jpg"],
    ["pattern" => "%Sticker%", "image" => "uploads/products/sticker_vinyl.jpg"],
    ["pattern" => "%Stiker%", "image" => "uploads/products/sticker_vinyl.jpg"],
    ["pattern" => "%Poster%", "image" => "uploads/products/poster_a3.jpg"],
    ["pattern" => "%X-Banner%", "image" => "uploads/products/xbanner_60x160.jpg"],
    ["pattern" => "%Flyer%", "image" => "uploads/products/flyer_a6.jpg"],
    ["pattern" => "%Kop Surat%", "image" => "uploads/products/kop_surat_a4.jpg"],
    ["pattern" => "%Label%", "image" => "uploads/products/label_produk.jpg"]
];

foreach ($updates as $update) {
    $stmt = $db->prepare("UPDATE products SET thumbnail = ? WHERE name LIKE ?");
    $stmt->bind_param("ss", $update['image'], $update['pattern']);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        echo "✓ Updated {$affected} products matching '{$update['pattern']}' with image: {$update['image']}\n";
    }
    $stmt->close();
}

echo "\n=== VERIFICATION ===\n\n";

// Show updated products
$result = $db->query("SELECT id, name, category_id, thumbnail FROM products WHERE id >= 20 ORDER BY id LIMIT 20");
echo "Products 20-40:\n";
while ($row = $result->fetch_assoc()) {
    printf(
        "ID:%-3d | %-35s | Cat:%-3d | %s\n",
        $row['id'],
        substr($row['name'], 0, 35),
        $row['category_id'],
        $row['thumbnail'] ?? '(no image)'
    );
}

echo "\n=== CATEGORY PRODUCT COUNTS ===\n";
$result = $db->query("
    SELECT pc.id, pc.name, pc.slug, COUNT(p.id) as cnt 
    FROM product_categories pc
    LEFT JOIN products p ON p.category_id = pc.id
    WHERE pc.is_active = 1
    GROUP BY pc.id
    ORDER BY pc.id
");
while ($row = $result->fetch_assoc()) {
    printf(
        "Cat %-2d | %-30s | %-25s | Products: %d\n",
        $row['id'],
        substr($row['name'], 0, 30),
        $row['slug'],
        $row['cnt']
    );
}

echo "\n✅ Product images updated successfully!\n";
