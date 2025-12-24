-- ============================================
-- Phase 2: Product Options & Pricing Fix
-- Migration Script
-- ============================================

-- ============================================
-- STEP 1: Fix Pricing API (Add Discount Columns)
-- ============================================

ALTER TABLE products 
ADD COLUMN IF NOT EXISTS discount_type ENUM('none', 'percent', 'fixed') DEFAULT 'none' AFTER base_price,
ADD COLUMN IF NOT EXISTS discount_value DECIMAL(10,2) DEFAULT 0.00 AFTER discount_type;

-- ============================================
-- STEP 2: Create Product Options Table
-- ============================================

CREATE TABLE IF NOT EXISTS product_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL COMMENT 'Foreign key to products.id',
    option_type ENUM('material', 'lamination') NOT NULL COMMENT 'Type of option: material or lamination',
    name VARCHAR(100) NOT NULL COMMENT 'Display name (e.g., "Albatros", "Doff")',
    slug VARCHAR(100) NOT NULL COMMENT 'URL-safe identifier',
    price_delta DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Price adjustment (+/-) from base price',
    sort_order INT DEFAULT 0 COMMENT 'Display order (lower = first)',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = hidden',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_type (product_id, option_type),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Product customization options (materials, laminations) with price deltas';

-- ============================================
-- STEP 3: Create Product Price Tiers Table
-- ============================================

CREATE TABLE IF NOT EXISTS product_price_tiers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL COMMENT 'Foreign key to products.id',
    qty_min INT NOT NULL COMMENT 'Minimum quantity for this tier',
    qty_max INT NULL COMMENT 'Maximum quantity (NULL = unlimited)',
    unit_price DECIMAL(10,2) NOT NULL COMMENT 'Price per unit at this tier',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = hidden',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_quantity (qty_min, qty_max),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Quantity-based pricing tiers for bulk discounts';

-- ============================================
-- STEP 4: Sample Data for Testing
-- ============================================

-- Find product IDs for "Poster Band" and "Backwall"
-- Using product_id = 144 for Poster Band KuburanBand (from browser testing)
-- Using product_id = 145 for Backwall Premium (if exists)

-- Sample Material Options for Poster Band (ID: 144)
INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
(144, 'material', 'Art Paper 150gsm', 'art-paper-150', 0.00, 1),
(144, 'material', 'Art Paper 260gsm', 'art-paper-260', 15000.00, 2),
(144, 'material', 'Ivory 230gsm', 'ivory-230', 20000.00, 3)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Sample Lamination Options for Poster Band (ID: 144)
INSERT INTO product_options (product_id, option_type, name, slug, price_delta, sort_order) VALUES
(144, 'lamination', 'Doff (Matte)', 'doff', 15000.00, 1),
(144, 'lamination', 'Glossy', 'glossy', 18000.00, 2),
(144, 'lamination', 'No Lamination', 'none', 0.00, 3)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- ============================================
-- STEP 4: Add Sample Gallery Images for Multi-Image Testing
-- ============================================

-- NOTE: This database uses product_images table for gallery, not a gallery column
-- To add multiple images for testing thumbnails, use product_images table instead:

-- Example: Add 3 more images to product 144 for thumbnail testing
-- INSERT INTO product_images (product_id, image_path, sort_order) VALUES
-- (144, '/uploads/products/poster_thumb_1.jpg', 1),
-- (144, '/uploads/products/poster_thumb_2.jpg', 2),
-- (144, '/uploads/products/poster_thumb_3.jpg', 3)
-- ON DUPLICATE KEY UPDATE image_path=VALUES(image_path);

-- For now, skip this step - can be done manually later if needed

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Verify discount columns were added
-- SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT 
-- FROM INFORMATION_SCHEMA.COLUMNS 
-- WHERE TABLE_SCHEMA = 'eventprint' 
--   AND TABLE_NAME = 'products' 
--   AND COLUMN_NAME LIKE 'discount%';

-- Verify product_options table was created
-- SELECT TABLE_NAME, TABLE_ROWS, CREATE_TIME 
-- FROM INFORMATION_SCHEMA.TABLES 
-- WHERE TABLE_SCHEMA = 'eventprint' 
--   AND TABLE_NAME = 'product_options';

-- Verify sample options were inserted
-- SELECT id, product_id, option_type, name, price_delta 
-- FROM product_options 
-- WHERE product_id = 144 
-- ORDER BY option_type, sort_order;

-- Check products with multiple images
-- SELECT id, name, slug, JSON_LENGTH(gallery) as image_count
-- FROM products
-- WHERE JSON_LENGTH(gallery) > 1
-- LIMIT 5;

-- ============================================
-- ROLLBACK (if needed)
-- ============================================

-- DROP TABLE IF EXISTS product_options;
-- ALTER TABLE products DROP COLUMN IF EXISTS discount_type;
-- ALTER TABLE products DROP COLUMN IF EXISTS discount_value;
