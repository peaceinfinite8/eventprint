-- Migration 006: Product Enhancements
-- Date: 2025-12-21
-- Purpose: Add product options, tier pricing, and discount support

-- 1. Add discount columns to products table
ALTER TABLE products
ADD COLUMN discount_type ENUM('none','percent','fixed') NOT NULL DEFAULT 'none' AFTER base_price,
ADD COLUMN discount_value DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER discount_type;

-- 2. Create product_options table
CREATE TABLE IF NOT EXISTS product_options (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  name VARCHAR(100) NOT NULL,
  price_delta DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  sort_order INT NOT NULL DEFAULT 1,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_product (product_id),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create product_price_tiers table
CREATE TABLE IF NOT EXISTS product_price_tiers (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  qty_min INT NOT NULL,
  qty_max INT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  INDEX idx_product (product_id),
  INDEX idx_active (is_active),
  INDEX idx_qty (qty_min, qty_max)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
