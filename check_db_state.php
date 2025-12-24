<?php
// Quick script to check database state
require_once __DIR__ . '/app/config/db.php';

$db = db();

// Check products and their categories
echo "=== PRODUCTS AND CATEGORIES ===\n";
$result = $db->query("SELECT id, name, category_id, thumbnail FROM products LIMIT 25");
while ($row = $result->fetch_assoc()) {
    echo sprintf(
        "ID: %d | Name: %-35s | Cat ID: %-3s | Image: %s\n",
        $row['id'],
        substr($row['name'], 0, 35),
        $row['category_id'] ?? 'NULL',
        $row['thumbnail'] ?? 'NULL'
    );
}

echo "\n=== CATEGORIES ===\n";
$result = $db->query("SELECT id, name, slug FROM product_categories WHERE is_active=1 ORDER BY sort_order, id");
while ($row = $result->fetch_assoc()) {
    echo sprintf("ID: %d | Name: %-30s | Slug: %s\n", $row['id'], $row['name'], $row['slug']);
}

echo "\n=== PRODUCTS PER CATEGORY ===\n";
$result = $db->query("
    SELECT pc.id, pc.name, pc.slug, COUNT(p.id) as cnt 
    FROM product_categories pc
    LEFT JOIN products p ON p.category_id = pc.id
    WHERE pc.is_active = 1
    GROUP BY pc.id, pc.name, pc.slug
    ORDER BY pc.sort_order, pc.id
");
while ($row = $result->fetch_assoc()) {
    echo sprintf(
        "Cat ID: %d | Name: %-30s | Slug: %-20s | Count: %d\n",
        $row['id'],
        $row['name'],
        $row['slug'],
        $row['cnt']
    );
}
