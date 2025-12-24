<?php
require 'app/config/db.php';
$db = db();

echo "Products:\n";
$r = $db->query('SELECT id, name, category_id FROM products LIMIT 15');
while ($row = $r->fetch_assoc()) {
    printf("%d | %-30s | Cat:%s\n", $row['id'], substr($row['name'], 0, 30), $row['category_id'] ?? 'NULL');
}

echo "\nCategories with product counts:\n";
$r = $db->query('SELECT pc.id, pc.name, pc.slug, COUNT(p.id) as cnt FROM product_categories pc LEFT JOIN products p ON p.category_id = pc.id WHERE pc.is_active = 1 GROUP BY pc.id ORDER BY pc.id');
while ($row = $r->fetch_assoc()) {
    printf("ID:%d | %-25s | Slug:%-20s | Count:%d\n", $row['id'], substr($row['name'], 0, 25), $row['slug'], $row['cnt']);
}
