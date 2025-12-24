<?php
// Direct PDO connection to get slugs
try {
    $pdo = new PDO('mysql:host=localhost;dbname=eventprint', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get product slug
    $stmt = $pdo->query("SELECT slug FROM products WHERE is_active=1 ORDER BY id DESC LIMIT 1");
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $productSlug = $product['slug'] ?? 'NO_PRODUCT';

    // Get blog slug  
    $stmt = $pdo->query("SELECT slug FROM posts ORDER BY id DESC LIMIT 1");
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    $blogSlug = $post['slug'] ?? 'NO_POST';

    echo "PRODUCT_SLUG=$productSlug\n";
    echo "BLOG_SLUG=$blogSlug\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>