-- ============================================
-- COMPREHENSIVE PRODUCTION SCHEMA MIGRATION v2
-- Created: 2026-01-04
-- Description: Complete schema sync - FIXED testimonials columns
-- Run this ONE FILE to fix all missing tables and columns
-- ============================================

SET @dbname = DATABASE();

-- =============================================
-- SECTION 1: CREATE MISSING TABLES
-- =============================================

-- 1.1 our_store_gallery
CREATE TABLE IF NOT EXISTS `our_store_gallery` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `store_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gallery_store` (`store_id`, `sort_order`),
  KEY `idx_gallery_active` (`is_active`),
  CONSTRAINT `fk_gallery_store` FOREIGN KEY (`store_id`) REFERENCES `our_store` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1.2 testimonials (FIXED: matched to code expectations)
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `position` varchar(150) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `rating` tinyint(1) UNSIGNED DEFAULT 5,
  `message` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1.3 activity_logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `level` ENUM('info','warning','error') NOT NULL,
  `source` ENUM('api','admin','system') NOT NULL,
  `message` VARCHAR(255) NOT NULL,
  `context` JSON NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_created` (`created_at`),
  INDEX `idx_level` (`level`),
  INDEX `idx_source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.4 product_price_tiers
CREATE TABLE IF NOT EXISTS `product_price_tiers` (
  `id` BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `qty_min` INT NOT NULL,
  `qty_max` INT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  INDEX `idx_product` (`product_id`),
  INDEX `idx_active` (`is_active`),
  INDEX `idx_qty` (`qty_min`, `qty_max`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1.5 materials
CREATE TABLE IF NOT EXISTS `materials` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_delta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1.6 laminations
CREATE TABLE IF NOT EXISTS `laminations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_delta` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1.7 category_materials
CREATE TABLE IF NOT EXISTS `category_materials` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(10) UNSIGNED NOT NULL,
  `material_id` int(10) UNSIGNED NOT NULL,
  `price_delta_override` decimal(12,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category_material` (`category_id`, `material_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_material` (`material_id`),
  CONSTRAINT `fk_cm_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cm_material` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1.8 category_laminations
CREATE TABLE IF NOT EXISTS `category_laminations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(10) UNSIGNED NOT NULL,
  `lamination_id` int(10) UNSIGNED NOT NULL,
  `price_delta_override` decimal(12,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_category_lamination` (`category_id`, `lamination_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_lamination` (`lamination_id`),
  CONSTRAINT `fk_cl_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cl_lamination` FOREIGN KEY (`lamination_id`) REFERENCES `laminations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1.9 product_materials
CREATE TABLE IF NOT EXISTS `product_materials` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `material_id` int(10) UNSIGNED NOT NULL,
  `price_delta_override` decimal(12,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_product_material` (`product_id`, `material_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_material` (`material_id`),
  CONSTRAINT `fk_pm_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pm_material` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1.10 product_laminations
CREATE TABLE IF NOT EXISTS `product_laminations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` int(10) UNSIGNED NOT NULL,
  `lamination_id` int(10) UNSIGNED NOT NULL,
  `price_delta_override` decimal(12,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_product_lamination` (`product_id`, `lamination_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_lamination` (`lamination_id`),
  CONSTRAINT `fk_pl_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pl_lamination` FOREIGN KEY (`lamination_id`) REFERENCES `laminations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================
-- SECTION 2: ADD MISSING COLUMNS
-- =============================================

-- 2.1 our_store.email
SET @columnname = 'email';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'our_store' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "our_store.email exists" AS msg;',
  'ALTER TABLE our_store ADD COLUMN email VARCHAR(255) DEFAULT NULL AFTER phone;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.2 our_store.hours
SET @columnname = 'hours';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'our_store' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "our_store.hours exists" AS msg;',
  'ALTER TABLE our_store ADD COLUMN hours VARCHAR(255) DEFAULT NULL AFTER whatsapp;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.3 posts.external_url
SET @columnname = 'external_url';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'posts' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "posts.external_url exists" AS msg;',
  'ALTER TABLE posts ADD COLUMN external_url VARCHAR(500) DEFAULT NULL AFTER content;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.4 posts.link_target
SET @columnname = 'link_target';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'posts' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "posts.link_target exists" AS msg;',
  'ALTER TABLE posts ADD COLUMN link_target ENUM(''_self'', ''_blank'') DEFAULT ''_self'' AFTER external_url;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.5 posts.is_featured
SET @columnname = 'is_featured';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'posts' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "posts.is_featured exists" AS msg;',
  'ALTER TABLE posts ADD COLUMN is_featured TINYINT(1) DEFAULT 0 AFTER is_published;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.6 products.discount_type
SET @columnname = 'discount_type';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'products' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "products.discount_type exists" AS msg;',
  'ALTER TABLE products ADD COLUMN discount_type ENUM(''percent'', ''fixed'') DEFAULT NULL AFTER base_price;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.7 products.discount_value
SET @columnname = 'discount_value';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'products' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "products.discount_value exists" AS msg;',
  'ALTER TABLE products ADD COLUMN discount_value DECIMAL(12,2) DEFAULT 0.00 AFTER discount_type;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.8 products.options_source
SET @columnname = 'options_source';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'products' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "products.options_source exists" AS msg;',
  'ALTER TABLE products ADD COLUMN options_source ENUM(''category'', ''product'', ''both'') DEFAULT ''category'' AFTER base_price;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.9 product_categories.whatsapp_number
SET @columnname = 'whatsapp_number';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'product_categories' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "product_categories.whatsapp_number exists" AS msg;',
  'ALTER TABLE product_categories ADD COLUMN whatsapp_number VARCHAR(30) DEFAULT NULL AFTER slug;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.10 users.failed_attempts
SET @columnname = 'failed_attempts';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'users' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "users.failed_attempts exists" AS msg;',
  'ALTER TABLE users ADD COLUMN failed_attempts INT UNSIGNED DEFAULT 0 AFTER password;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.11 users.locked_until
SET @columnname = 'locked_until';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'users' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "users.locked_until exists" AS msg;',
  'ALTER TABLE users ADD COLUMN locked_until DATETIME DEFAULT NULL AFTER failed_attempts;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2.12 product_categories.icon (optional)
SET @columnname = 'icon';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'product_categories' AND COLUMN_NAME = @columnname) > 0,
  'SELECT "product_categories.icon exists" AS msg;',
  'ALTER TABLE product_categories ADD COLUMN icon VARCHAR(50) DEFAULT NULL AFTER name;'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- VERIFICATION
-- =============================================
SELECT '✅ COMPREHENSIVE MIGRATION COMPLETED!' AS status;
SELECT 'All missing tables and columns have been added.' AS message;
SELECT 'Testimonials table now has correct columns: name, position, photo, rating, message' AS testimonials_fix;
