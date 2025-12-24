-- Fix product images and ensure proper category assignments

-- First, let's check current state
SELECT 'Products without thumbnails:' as info;
SELECT id, name, category_id, thumbnail FROM products WHERE thumbnail IS NULL OR thumbnail = '';

SELECT '' as info;
SELECT 'Products with missing category assignments:' as info;
SELECT id, name, category_id FROM products WHERE category_id IS NULL;

SELECT '' as info;
SELECT 'Current category distribution:' as info;
SELECT pc.name, COUNT(p.id) as product_count 
FROM product_categories pc
LEFT JOIN products p ON p.category_id = pc.id
WHERE pc.is_active = 1
GROUP BY pc.id, pc.name
ORDER BY pc.id;

-- Update products to ensure they have proper image paths
-- These match the uploaded files in /public/uploads/products/

UPDATE products SET thumbnail = 'uploads/products/spanduk_flexi.jpg' WHERE name LIKE '%Spanduk Flexi%';
UPDATE products SET thumbnail = 'uploads/products/rollup_60x160.jpg' WHERE name LIKE '%Roll Up%' OR name LIKE '%RollUp%';
UPDATE products SET thumbnail = 'uploads/products/brosur_a5.jpg' WHERE name LIKE '%Brosur%';
UPDATE products SET thumbnail = 'uploads/products/kartu_nama.jpg' WHERE name LIKE '%Kartu Nama%';
UPDATE products SET thumbnail = 'uploads/products/sticker_vinyl.jpg' WHERE name LIKE '%Sticker%' OR name LIKE '%Stiker%';
UPDATE products SET thumbnail = 'uploads/products/poster_a3.jpg' WHERE name LIKE '%Poster%';
UPDATE products SET thumbnail = 'uploads/products/xbanner_60x160.jpg' WHERE name LIKE '%X-Banner%' OR name LIKE '%XBanner%';
UPDATE products SET thumbnail = 'uploads/products/flyer_a6.jpg' WHERE name LIKE '%Flyer%';
UPDATE products SET thumbnail = 'uploads/products/kop_surat_a4.jpg' WHERE name LIKE '%Kop Surat%';
UPDATE products SET thumbnail = 'uploads/products/label_produk.jpg' WHERE name LIKE '%Label%';

-- Show results
SELECT 'Updated products:' as info;
SELECT id, name, category_id, thumbnail FROM products WHERE id BETWEEN 20 AND 35 ORDER BY id;
