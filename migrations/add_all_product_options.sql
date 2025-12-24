-- ============================================
-- Add Material & Lamination Options for ALL Products
-- Based on actual product IDs from database (94-118)
-- ============================================

-- Step 1: Clear any existing options (optional - uncomment if needed)
-- DELETE FROM product_options WHERE product_id IN (94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118);

-- ============================================
-- CATEGORY: Spanduk & Banner (IDs 94-98)
-- Options: Banner Materials only (no lamination for outdoor banners)
-- ============================================

INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Product 94: Spanduk Flexi 280gsm
(94, 'material', 'Flexi China 280gsm', 'flexi-china-280', 0, 1),
(94, 'material', 'Flexi Korea 340gsm', 'flexi-korea-340', 10000, 2),
(94, 'material', 'Flexi German 440gsm', 'flexi-german-440', 20000, 3),

-- Product 95: Banner Roll Up 60x160
(95, 'material', 'Albatross 260gsm', 'albatross-260', 0, 1),
(95, 'material', 'Flexi Korea 340gsm', 'flexi-korea-340', 15000, 2),

-- Product 96: X-Banner 60x160
(96, 'material', 'Albatross 260gsm', 'albatross-260', 0, 1),
(96, 'material', 'Flexi Korea 340gsm', 'flexi-korea-340', 10000, 2),

-- Product 97: Backwall Stand 3x2
(97, 'material', 'Flexi Korea 340gsm', 'flexi-korea-340', 0, 1),
(97, 'material', 'Flexi German 440gsm', 'flexi-german-440', 50000, 2),

-- Product 98: Flag Banner
(98, 'material', 'Satin Flag Material', 'satin-flag', 0, 1),
(98, 'material', 'Polyester Premium', 'polyester-premium', 15000, 2);

-- ============================================
-- CATEGORY: Poster & Flyer (IDs 99-103)
-- Options: Art Paper + Lamination
-- ============================================

INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Product 99: Poster A3 Full Color
(99, 'material', 'Art Paper 150gsm', 'art-paper-150', 0, 1),
(99, 'material', 'Art Paper 260gsm', 'art-paper-260', 2000, 2),
(99, 'material', 'Ivory 230gsm', 'ivory-230', 3000, 3),
(99, 'lamination', 'Doff (Matte)', 'doff', 2500, 1),
(99, 'lamination', 'Glossy', 'glossy', 2500, 2),
(99, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 100: Poster A2 Full Color
(100, 'material', 'Art Paper 150gsm', 'art-paper-150', 0, 1),
(100, 'material', 'Art Paper 260gsm', 'art-paper-260', 5000, 2),
(100, 'material', 'Ivory 230gsm', 'ivory-230', 7000, 3),
(100, 'lamination', 'Doff (Matte)', 'doff', 5000, 1),
(100, 'lamination', 'Glossy', 'glossy', 5000, 2),
(100, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 101: Flyer A6 Full Color
(101, 'material', 'Art Paper 120gsm', 'art-paper-120', 0, 1),
(101, 'material', 'Art Paper 150gsm', 'art-paper-150', 50, 2),
(101, 'lamination', 'Doff (Matte)', 'doff', 100, 1),
(101, 'lamination', 'Glossy', 'glossy', 100, 2),
(101, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 102: Flyer A5 Full Color
(102, 'material', 'Art Paper 120gsm', 'art-paper-120', 0, 1),
(102, 'material', 'Art Paper 150gsm', 'art-paper-150', 100, 2),
(102, 'lamination', 'Doff (Matte)', 'doff', 150, 1),
(102, 'lamination', 'Glossy', 'glossy', 150, 2),
(102, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 103: Leaflet 3 Lipat
(103, 'material', 'Art Paper 120gsm', 'art-paper-120', 0, 1),
(103, 'material', 'Art Paper 150gsm', 'art-paper-150', 200, 2),
(103, 'lamination', 'Doff (Matte)', 'doff', 300, 1),
(103, 'lamination', 'Glossy', 'glossy', 300, 2),
(103, 'lamination', 'No Lamination', 'none', 0, 3);

-- ============================================
-- CATEGORY: Kartu Nama & Stationery (IDs 104-108)
-- Options: Premium Papers + Lamination
-- ============================================

INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Product 104: Kartu Nama Premium 1 Sisi
(104, 'material', 'Art Carton 260gsm', 'art-carton-260', 0, 1),
(104, 'material', 'Art Carton 310gsm', 'art-carton-310', 5000, 2),
(104, 'material', 'Ivory 230gsm', 'ivory-230', 7000, 3),
(104, 'lamination', 'Doff (Matte)', 'doff', 5000, 1),
(104, 'lamination', 'Glossy', 'glossy', 5000, 2),
(104, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 105: Kartu Nama Premium 2 Sisi
(105, 'material', 'Art Carton 260gsm', 'art-carton-260', 0, 1),
(105, 'material', 'Art Carton 310gsm', 'art-carton-310', 5000, 2),
(105, 'material', 'Ivory 230gsm', 'ivory-230', 7000, 3),
(105, 'lamination', 'Doff (Matte)', 'doff', 8000, 1),
(105, 'lamination', 'Glossy', 'glossy', 8000, 2),
(105, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 106: Kop Surat A4
(106, 'material', 'HVS 70gsm', 'hvs-70', 0, 1),
(106, 'material', 'HVS 80gsm', 'hvs-80', 200, 2),
(106, 'material', 'Art Paper 120gsm', 'art-paper-120', 500, 3),

-- Product 107: Amplop Custom
(107, 'material', 'Kertas Samson 80gsm', 'samson-80', 0, 1),
(107, 'material', 'Kertas Jasmine 100gsm', 'jasmine-100', 300, 2),

-- Product 108: Nota NCR 2 Ply
(108, 'material', 'NCR Standard', 'ncr-standard', 0, 1),
(108, 'material', 'NCR Premium', 'ncr-premium', 500, 2);

-- ============================================
-- CATEGORY: Sticker & Label (IDs 109-113)
-- Options: Vinyl Types + Lamination
-- ============================================

INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Product 109: Sticker Vinyl Outdoor
(109, 'material', 'Vinyl Standard', 'vinyl-standard', 0, 1),
(109, 'material', 'Vinyl Premium', 'vinyl-premium', 300, 2),
(109, 'lamination', 'Doff', 'doff', 200, 1),
(109, 'lamination', 'Glossy', 'glossy', 200, 2),
(109, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 110: Sticker Chromo Label
(110, 'material', 'Chromo Paper 80gsm', 'chromo-80', 0, 1),
(110, 'material', 'Chromo Paper 100gsm', 'chromo-100', 150, 2),
(110, 'lamination', 'Glossy', 'glossy', 150, 1),
(110, 'lamination', 'No Lamination', 'none', 0, 2),

-- Product 111: Label Stiker Produk Custom
(111, 'material', 'Chromo Paper 80gsm', 'chromo-80', 0, 1),
(111, 'material', 'Vinyl', 'vinyl', 200, 2),
(111, 'lamination', 'Glossy', 'glossy', 150, 1),
(111, 'lamination', 'No Lamination', 'none', 0, 2),

-- Product 112: Sticker Transparan
(112, 'material', 'Vinyl Transparan Standard', 'vinyl-transparent-std', 0, 1),
(112, 'material', 'Vinyl Transparan Premium', 'vinyl-transparent-premium', 300, 2),
(112, 'lamination', 'Glossy', 'glossy', 250, 1),
(112, 'lamination', 'No Lamination', 'none', 0, 2),

-- Product 113: Sticker Hologram
(113, 'material', 'Hologram Standar', 'hologram-standard', 0, 1),
(113, 'material', 'Hologram Premium', 'hologram-premium', 1000, 2);

-- ============================================
-- CATEGORY: Brosur & Katalog (IDs 114-118)
-- Options: Book/Catalog Papers + Lamination
-- ============================================

INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
-- Product 114: Brosur A5 Full Color
(114, 'material', 'Art Paper 120gsm', 'art-paper-120', 0, 1),
(114, 'material', 'Art Paper 150gsm', 'art-paper-150', 100, 2),
(114, 'lamination', 'Doff (Matte)', 'doff', 150, 1),
(114, 'lamination', 'Glossy', 'glossy', 150, 2),
(114, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 115: Brosur Lipat 2
(115, 'material', 'Art Paper 120gsm', 'art-paper-120', 0, 1),
(115, 'material', 'Art Paper 150gsm', 'art-paper-150', 200, 2),
(115, 'lamination', 'Doff (Matte)', 'doff', 300, 1),
(115, 'lamination', 'Glossy', 'glossy', 300, 2),
(115, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 116: Katalog Produk A4 Staples
(116, 'material', 'HVS 70gsm', 'hvs-70', 0, 1),
(116, 'material', 'HVS 80gsm', 'hvs-80', 1000, 2),
(116, 'material', 'Art Paper 120gsm', 'art-paper-120', 2000, 3),
(116, 'lamination', 'Cover Doff', 'cover-doff', 2000, 1),
(116, 'lamination', 'Cover Glossy', 'cover-glossy', 2000, 2),
(116, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 117: Booklet Company Profile
(117, 'material', 'Art Paper 120gsm', 'art-paper-120', 0, 1),
(117, 'material', 'Art Paper 150gsm', 'art-paper-150', 2000, 2),
(117, 'lamination', 'Cover Doff', 'cover-doff', 3000, 1),
(117, 'lamination', 'Cover Glossy', 'cover-glossy', 3000, 2),
(117, 'lamination', 'No Lamination', 'none', 0, 3),

-- Product 118: Menu Restoran A4
(118, 'material', 'Art Paper 150gsm', 'art-paper-150', 0, 1),
(118, 'material', 'Art Paper 260gsm', 'art-paper-260', 2000, 2),
(118, 'material', 'Art Carton 260gsm', 'art-carton-260', 3000, 3),
(118, 'lamination', 'Doff (Matte)', 'doff', 2500, 1),
(118, 'lamination', 'Glossy', 'glossy', 2500, 2),
(118, 'lamination', 'No Lamination', 'none', 0, 3);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

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
ORDER BY p.id;

-- Check specific product options
-- SELECT * FROM product_options WHERE product_id = 99 ORDER BY option_type, sort_order;
