-- ============================================
-- Migration: Category-Based Product Options
-- Date: 2025-12-22
-- Purpose: Move from per-product options to category-based options
-- ============================================

-- ============================================
-- STEP 1: Create Materials Master Table
-- ============================================
CREATE TABLE IF NOT EXISTS materials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Display name (e.g., "Art Paper 150gsm")',
    slug VARCHAR(100) NOT NULL COMMENT 'URL-safe identifier',
    price_delta DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Price adjustment from base',
    sort_order INT DEFAULT 0 COMMENT 'Display order (lower = first)',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = hidden',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Master list of available materials (bahan)';

-- ============================================
-- STEP 2: Create Laminations Master Table
-- ============================================
CREATE TABLE IF NOT EXISTS laminations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL COMMENT 'Display name (e.g., "Doff", "Glossy")',
    slug VARCHAR(100) NOT NULL COMMENT 'URL-safe identifier',
    price_delta DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Price adjustment from base',
    sort_order INT DEFAULT 0 COMMENT 'Display order (lower = first)',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1 = active, 0 = hidden',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Master list of available laminations (laminasi)';

-- ============================================
-- STEP 3: Create Category-Material Mapping Table
-- ============================================
CREATE TABLE IF NOT EXISTS category_materials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL COMMENT 'FK to product_categories.id',
    material_id INT UNSIGNED NOT NULL COMMENT 'FK to materials.id',
    price_delta_override DECIMAL(10,2) NULL COMMENT 'Category-specific price override (NULL = use master)',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE,
    UNIQUE KEY uk_category_material (category_id, material_id),
    INDEX idx_category (category_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Maps which materials are available for each product category';

-- ============================================
-- STEP 4: Create Category-Lamination Mapping Table
-- ============================================
CREATE TABLE IF NOT EXISTS category_laminations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL COMMENT 'FK to product_categories.id',
    lamination_id INT UNSIGNED NOT NULL COMMENT 'FK to laminations.id',
    price_delta_override DECIMAL(10,2) NULL COMMENT 'Category-specific price override (NULL = use master)',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (lamination_id) REFERENCES laminations(id) ON DELETE CASCADE,
    UNIQUE KEY uk_category_lamination (category_id, lamination_id),
    INDEX idx_category (category_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Maps which laminations are available for each product category';

-- ============================================
-- STEP 5: Seed Master Materials Data
-- ============================================
INSERT INTO materials (name, slug, price_delta, sort_order) VALUES
('Art Paper 150gsm', 'art-paper-150', 0.00, 1),
('Art Paper 260gsm', 'art-paper-260', 15000.00, 2),
('Art Carton 310gsm', 'art-carton-310', 25000.00, 3),
('Ivory 230gsm', 'ivory-230', 20000.00, 4),
('Ivory 310gsm', 'ivory-310', 30000.00, 5),
('HVS 80gsm', 'hvs-80', 0.00, 6),
('HVS 100gsm', 'hvs-100', 5000.00, 7),
('Albatros', 'albatros', 35000.00, 8),
('Vinyl', 'vinyl', 40000.00, 9),
('Flexi China', 'flexi-china', 25000.00, 10),
('Flexi Korea', 'flexi-korea', 45000.00, 11),
('Sticker Vinyl', 'sticker-vinyl', 30000.00, 12),
('Sticker Chromo', 'sticker-chromo', 20000.00, 13),
('Sticker Transparan', 'sticker-transparan', 35000.00, 14)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- ============================================
-- STEP 6: Seed Master Laminations Data
-- ============================================
INSERT INTO laminations (name, slug, price_delta, sort_order) VALUES
('Tanpa Laminasi', 'none', 0.00, 1),
('Doff (Matte)', 'doff', 15000.00, 2),
('Glossy', 'glossy', 18000.00, 3),
('Doff UV', 'doff-uv', 25000.00, 4),
('Glossy UV', 'glossy-uv', 28000.00, 5)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- ============================================
-- STEP 7: Map Materials to Categories
-- ============================================

-- Category 27: Spanduk & Banner (uses Flexi materials, no paper)
INSERT INTO category_materials (category_id, material_id) 
SELECT 27, id FROM materials WHERE slug IN ('flexi-china', 'flexi-korea', 'vinyl', 'albatros')
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 28: Poster & Flyer (uses Art Paper, Ivory)
INSERT INTO category_materials (category_id, material_id) 
SELECT 28, id FROM materials WHERE slug IN ('art-paper-150', 'art-paper-260', 'art-carton-310', 'ivory-230', 'ivory-310')
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 29: Kartu Nama & Stationery (uses thicker papers)
INSERT INTO category_materials (category_id, material_id) 
SELECT 29, id FROM materials WHERE slug IN ('art-carton-310', 'ivory-230', 'ivory-310')
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 30: Sticker & Label (uses sticker materials)
INSERT INTO category_materials (category_id, material_id) 
SELECT 30, id FROM materials WHERE slug IN ('sticker-vinyl', 'sticker-chromo', 'sticker-transparan')
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 31: Brosur & Katalog (uses Art Paper)
INSERT INTO category_materials (category_id, material_id) 
SELECT 31, id FROM materials WHERE slug IN ('art-paper-150', 'art-paper-260', 'art-carton-310')
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 32: Kalender (uses Art Carton)
INSERT INTO category_materials (category_id, material_id) 
SELECT 32, id FROM materials WHERE slug IN ('art-carton-310', 'ivory-310')
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 33: Undangan & Kartu Ucapan (uses Ivory, Art Carton)
INSERT INTO category_materials (category_id, material_id) 
SELECT 33, id FROM materials WHERE slug IN ('ivory-230', 'ivory-310', 'art-carton-310')
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 34: ID Card & Lanyard (no material selection - skip)

-- Category 35: Packaging & Box (Art Carton)
INSERT INTO category_materials (category_id, material_id) 
SELECT 35, id FROM materials WHERE slug IN ('art-carton-310', 'ivory-310')
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 36: Akrilik & Signage (no material options - uses specific acrylics)

-- ============================================
-- STEP 8: Map Laminations to Categories
-- ============================================

-- Paper-based categories get lamination options
-- Category 28: Poster & Flyer
INSERT INTO category_laminations (category_id, lamination_id) 
SELECT 28, id FROM laminations WHERE is_active=1
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 29: Kartu Nama & Stationery
INSERT INTO category_laminations (category_id, lamination_id) 
SELECT 29, id FROM laminations WHERE is_active=1
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 31: Brosur & Katalog
INSERT INTO category_laminations (category_id, lamination_id) 
SELECT 31, id FROM laminations WHERE is_active=1
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 32: Kalender
INSERT INTO category_laminations (category_id, lamination_id) 
SELECT 32, id FROM laminations WHERE is_active=1
ON DUPLICATE KEY UPDATE is_active=1;

-- Category 33: Undangan
INSERT INTO category_laminations (category_id, lamination_id) 
SELECT 33, id FROM laminations WHERE is_active=1
ON DUPLICATE KEY UPDATE is_active=1;

-- Sticker category gets limited laminations
INSERT INTO category_laminations (category_id, lamination_id) 
SELECT 30, id FROM laminations WHERE slug IN ('none', 'doff', 'glossy')
ON DUPLICATE KEY UPDATE is_active=1;

-- Spanduk & Banner - no lamination (already outdoor)
-- Akrilik - no lamination
-- ID Card - no lamination
-- Packaging - optional
INSERT INTO category_laminations (category_id, lamination_id) 
SELECT 35, id FROM laminations WHERE slug IN ('none', 'doff', 'glossy')
ON DUPLICATE KEY UPDATE is_active=1;

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check materials count
-- SELECT COUNT(*) AS material_count FROM materials WHERE is_active=1;

-- Check laminations count
-- SELECT COUNT(*) AS lamination_count FROM laminations WHERE is_active=1;

-- Check category mappings
-- SELECT pc.name AS category, COUNT(cm.id) AS materials_count
-- FROM product_categories pc
-- LEFT JOIN category_materials cm ON pc.id = cm.category_id
-- WHERE pc.is_active=1
-- GROUP BY pc.id ORDER BY pc.sort_order;

-- SELECT pc.name AS category, COUNT(cl.id) AS laminations_count
-- FROM product_categories pc
-- LEFT JOIN category_laminations cl ON pc.id = cl.category_id
-- WHERE pc.is_active=1
-- GROUP BY pc.id ORDER BY pc.sort_order;
