-- ============================================
-- Add Material & Lamination Options for Multiple Products
-- ============================================

-- STEP 1: Clear existing options (if needed)
-- DELETE FROM product_options WHERE product_id IN (/* list product IDs */);

-- ============================================
-- TEMPLATE: Poster Products (Art Paper + Lamination)
-- ============================================

-- Product ID 144: Poster Band KuburanBand (ALREADY EXISTS - for reference)
INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
(144, 'material', 'Art Paper 150gsm', 'art-paper-150', 0.00, 1),
(144, 'material', 'Art Paper 260gsm', 'art-paper-260', 15000.00, 2),
(144, 'material', 'Ivory 230gsm', 'ivory-230', 20000.00, 3),
(144, 'lamination', 'Doff (Matte)', 'doff', 15000.00, 1),
(144, 'lamination', 'Glossy', 'glossy', 18000.00, 2),
(144, 'lamination', 'No Lamination', 'none', 0.00, 3);

-- ============================================
-- EXAMPLE: Add options for other poster products
-- ============================================

-- Replace PRODUCT_ID with actual product ID for each poster product
-- Example for product ID 145 (Poster A3)
INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Materials
(145, 'material', 'Art Paper 150gsm', 'art-paper-150', 0.00, 1),
(145, 'material', 'Art Paper 260gsm', 'art-paper-260', 15000.00, 2),
(145, 'material', 'Ivory 230gsm', 'ivory-230', 20000.00, 3),
-- Laminations
(145, 'lamination', 'Doff (Matte)', 'doff', 15000.00, 1),
(145, 'lamination', 'Glossy', 'glossy', 18000.00, 2),
(145, 'lamination', 'No Lamination', 'none', 0.00, 3);

-- ============================================
-- TEMPLATE: Sticker Products (Vinyl + Lamination)
-- ============================================

-- For sticker products, use different materials
INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Materials (Vinyl types)
(STICKER_PRODUCT_ID, 'material', 'Vinyl Standar', 'vinyl-standard', 0.00, 1),
(STICKER_PRODUCT_ID, 'material', 'Vinyl Premium', 'vinyl-premium', 10000.00, 2),
(STICKER_PRODUCT_ID, 'material', 'Vinyl Transparan', 'vinyl-transparent', 12000.00, 3),
-- Laminations
(STICKER_PRODUCT_ID, 'lamination', 'Doff', 'doff', 8000.00, 1),
(STICKER_PRODUCT_ID, 'lamination', 'Glossy', 'glossy', 8000.00, 2),
(STICKER_PRODUCT_ID, 'lamination', 'No Lamination', 'none', 0.00, 3);

-- ============================================
-- TEMPLATE: Banner Products (Banner Material + No Lamination)
-- ============================================

-- Banners typically don't use lamination
INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Materials
(BANNER_PRODUCT_ID, 'material', 'Albatross 280gsm', 'albatross-280', 0.00, 1),
(BANNER_PRODUCT_ID, 'material', 'Flexi China', 'flexi-china', 5000.00, 2),
(BANNER_PRODUCT_ID, 'material', 'Flexi Korea', 'flexi-korea', 15000.00, 3);
-- No lamination options for banners

-- ============================================
-- TEMPLATE: Backwall Products (Specific Materials)
-- ============================================

INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Materials
(BACKWALL_PRODUCT_ID, 'material', 'X-Banner', 'x-banner', 0.00, 1),
(BACKWALL_PRODUCT_ID, 'material', 'Roll Up Banner', 'roll-up', 50000.00, 2),
(BACKWALL_PRODUCT_ID, 'material', 'Pop Up Banner', 'pop-up', 100000.00, 3);

-- ============================================
-- QUERY: Get All Active Products to Add Options
-- ============================================

-- Use this query to see all products and decide which need options
SELECT 
    id,
    name,
    category_id,
    (SELECT name FROM product_categories WHERE id = products.category_id) as category_name,
    base_price
FROM products 
WHERE is_active = 1
ORDER BY category_id, name;

-- ============================================
-- BULK INSERT EXAMPLE: Add same options to multiple products
-- ============================================

-- If multiple products share the same options (e.g., all posters)
-- You can use a loop or multiple INSERT statements

-- Example: Add standard poster options to product IDs 144, 145, 146, 147
INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Product 144
(144, 'material', 'Art Paper 150gsm', 'art-paper-150', 0.00, 1),
(144, 'material', 'Art Paper 260gsm', 'art-paper-260', 15000.00, 2),
(144, 'lamination', 'Doff', 'doff', 15000.00, 1),
(144, 'lamination', 'Glossy', 'glossy', 18000.00, 2),
-- Product 145 (copy same options)
(145, 'material', 'Art Paper 150gsm', 'art-paper-150', 0.00, 1),
(145, 'material', 'Art Paper 260gsm', 'art-paper-260', 15000.00, 2),
(145, 'lamination', 'Doff', 'doff', 15000.00, 1),
(145, 'lamination', 'Glossy', 'glossy', 18000.00, 2);
-- Continue for other product IDs...

-- ============================================
-- VERIFICATION
-- ============================================

-- Check options for specific product
SELECT 
    po.id,
    p.name as product_name,
    po.option_type,
    po.name as option_name,
    po.price_delta
FROM product_options po
JOIN products p ON po.product_id = p.id
WHERE po.product_id = YOUR_PRODUCT_ID
ORDER BY po.option_type, po.sort_order;

-- Count options per product
SELECT 
    p.id,
    p.name,
    COUNT(po.id) as total_options,
    SUM(CASE WHEN po.option_type = 'material' THEN 1 ELSE 0 END) as materials,
    SUM(CASE WHEN po.option_type = 'lamination' THEN 1 ELSE 0 END) as laminations
FROM products p
LEFT JOIN product_options po ON p.id = po.product_id
WHERE p.is_active = 1
GROUP BY p.id, p.name
HAVING total_options > 0
ORDER BY p.name;
