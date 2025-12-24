-- ============================================
-- Migration: Per-Product Material & Lamination Options
-- Date: 2025-12-22
-- Purpose: Allow products to have their own materials/laminations
--          in addition to or instead of category-level options
-- ============================================

-- ============================================
-- STEP 1: Create Product-Material Mapping Table
-- ============================================
CREATE TABLE IF NOT EXISTS product_materials (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL COMMENT 'FK to products.id',
    material_id INT UNSIGNED NOT NULL COMMENT 'FK to materials.id',
    price_delta_override DECIMAL(10,2) NULL COMMENT 'Product-specific price override (NULL = use master)',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materials(id) ON DELETE CASCADE,
    UNIQUE KEY uk_product_material (product_id, material_id),
    INDEX idx_product (product_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Maps which materials are available for a specific product (overrides category)';

-- ============================================
-- STEP 2: Create Product-Lamination Mapping Table
-- ============================================
CREATE TABLE IF NOT EXISTS product_laminations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL COMMENT 'FK to products.id',
    lamination_id INT UNSIGNED NOT NULL COMMENT 'FK to laminations.id',
    price_delta_override DECIMAL(10,2) NULL COMMENT 'Product-specific price override (NULL = use master)',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (lamination_id) REFERENCES laminations(id) ON DELETE CASCADE,
    UNIQUE KEY uk_product_lamination (product_id, lamination_id),
    INDEX idx_product (product_id),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Maps which laminations are available for a specific product (overrides category)';

-- ============================================
-- STEP 3: Add column to products table to control option source
-- ============================================
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS options_source ENUM('category', 'product', 'both') DEFAULT 'category'
COMMENT 'category=use category options, product=use product-specific only, both=merge category+product';

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Check tables created
-- SHOW TABLES LIKE 'product_%';

-- Check column added
-- SHOW COLUMNS FROM products LIKE 'options_source';
