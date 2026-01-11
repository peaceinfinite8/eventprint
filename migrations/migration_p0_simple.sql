-- ============================================================================
-- EventPrint - P0 Database Migrations (WAJIB untuk Go-Live)
-- ============================================================================
-- Purpose: Menambah field/tabel yang dibutuhkan frontend 100%
-- Version: 1.1 (Simplified - Compatible with all MySQL/MariaDB versions)
-- Date: 2025-12-18
-- 
-- INSTRUCTIONS:
-- 1. Backup database dulu: Tools -> Export -> eventprint.sql
-- 2. Run migration ini via phpMyAdmin: Import -> Browse -> migration_p0_simple.sql
-- 3. Atau copy-paste per section ke SQL tab
-- ============================================================================

-- ============================================================================
-- MIGRATION 1: Tabel Testimonials (NEW)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL COMMENT 'Nama pemberi testimoni',
  `position` VARCHAR(150) NULL COMMENT 'Jabatan/perusahaan (optional)',
  `photo` VARCHAR(255) NULL COMMENT 'Path foto (optional)',
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Rating 1-5 bintang',
  `message` TEXT NOT NULL COMMENT 'Isi testimoni',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Publish/unpublish',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Urutan tampil',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed data dummy (4 testimonials)
INSERT INTO `testimonials` (`name`, `position`, `rating`, `message`, `sort_order`, `is_active`) VALUES
('Budi Santoso', 'Owner Toko ABC', 5, 'Kualitas cetakan sangat bagus dan harga terjangkau. Sudah langganan 2 tahun!', 1, 1),
('Siti Nurhaliza', 'Event Organizer PT Kreatif', 5, 'Pelayanan cepat dan hasil memuaskan. Recommended untuk cetak banner event.', 2, 1),
('Andi Wijaya', 'Pengusaha UMKM', 4, 'PrintEventPrint membantu bisnis saya dengan kualitas cetak yang konsisten.', 3, 1),
('Linda Chen', 'Marketing Manager PT XYZ', 5, 'Professional, on-time delivery, dan support responsive. Sangat puas!', 4, 1);

-- ============================================================================
-- MIGRATION 2: Settings - Field Tambahan
-- ============================================================================

-- Add operating_hours column (if not exists)
SET @dbname = DATABASE();
SET @tablename = 'settings';
SET @columnname = 'operating_hours';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " VARCHAR(255) NULL AFTER whatsapp")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add gmaps_embed column (if not exists)
SET @columnname = 'gmaps_embed';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " TEXT NULL AFTER operating_hours")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add twitter column (if not exists)
SET @columnname = 'twitter';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " VARCHAR(255) NULL AFTER instagram")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add youtube column (if not exists)
SET @columnname = 'youtube';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " VARCHAR(255) NULL AFTER twitter")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add tiktok column (if not exists)
SET @columnname = 'tiktok';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " VARCHAR(255) NULL AFTER youtube")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Update existing settings row (seed data)
UPDATE `settings` SET
  `operating_hours` = 'Senin - Sabtu: 08:00 - 17:00',
  `gmaps_embed` = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966!2d106.6297!3d-6.2088!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMTInMzEuNyJTIDEwNsKwMzcnNDYuOSJF!5e0!3m2!1sen!2sid!4v1234567890" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
  `twitter` = 'https://twitter.com/eventprint',
  `youtube` = 'https://youtube.com/@eventprint',
  `tiktok` = 'https://tiktok.com/@eventprint'
WHERE `id` = 1;

-- ============================================================================
-- MIGRATION 3: Product Categories - Icon Field
-- ============================================================================

SET @tablename = 'product_categories';
SET @columnname = 'icon';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " VARCHAR(100) NULL AFTER slug")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Update existing categories dengan icon (FontAwesome)
UPDATE `product_categories` SET `icon` = 'fa-flag' WHERE `slug` = 'spanduk';
UPDATE `product_categories` SET `icon` = 'fa-sticky-note' WHERE `slug` = 'stiker';
UPDATE `product_categories` SET `icon` = 'fa-bullhorn' WHERE `slug` = 'spanduk-banner';
UPDATE `product_categories` SET `icon` = 'fa-file' WHERE `slug` = 'poster-flyer';
UPDATE `product_categories` SET `icon` = 'fa-expand' WHERE `slug` = 'x-banner-rollup';
UPDATE `product_categories` SET `icon` = 'fa-id-card' WHERE `slug` = 'kartu-nama-stationery';
UPDATE `product_categories` SET `icon` = 'fa-gift' WHERE `slug` = 'merchandise-cetak';
UPDATE `product_categories` SET `icon` = 'fa-print' WHERE `slug` = 'kertas-printer';

-- ============================================================================
-- MIGRATION 4: Posts - Is Featured Field
-- ============================================================================

SET @tablename = 'posts';
SET @columnname = 'is_featured';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " TINYINT(1) NOT NULL DEFAULT 0 AFTER is_published")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Add index untuk query optimization
SET @indexname = 'idx_featured_published';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD INDEX ", @indexname, " (is_featured, is_published, published_at)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Seed: set 3 post pertama jadi featured (untuk hero mosaic)
UPDATE `posts` SET `is_featured` = 1 WHERE `id` IN (1, 2, 7) AND `is_published` = 1 LIMIT 3;

-- ============================================================================
-- MIGRATION 5: Additional Indexes for Performance
-- ============================================================================

-- Products: index untuk query featured + active
SET @tablename = 'products';
SET @indexname = 'idx_featured_active';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD INDEX ", @indexname, " (is_featured, is_active)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Testimonials: index sudah ada di CREATE TABLE

-- Our Store: index untuk query active + sort
SET @tablename = 'our_store';
SET @indexname = 'idx_active_sort';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  "SELECT 1",
  CONCAT("ALTER TABLE ", @tablename, " ADD INDEX ", @indexname, " (is_active, sort_order)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

SELECT '=== MIGRATION P0 VERIFICATION ===' AS '';

SELECT 
  'testimonials' AS table_name,
  COUNT(*) AS row_count,
  'Should be 4' AS expected
FROM `testimonials`;

SELECT 
  'settings - new fields' AS info,
  IF(operating_hours IS NOT NULL, 'EXISTS', 'MISSING') AS operating_hours_field,
  IF(gmaps_embed IS NOT NULL, 'EXISTS', 'MISSING') AS gmaps_embed_field,
  IF(twitter IS NOT NULL, 'EXISTS', 'MISSING') AS twitter_field,
  IF(youtube IS NOT NULL, 'EXISTS', 'MISSING') AS youtube_field,
  IF(tiktok IS NOT NULL, 'EXISTS', 'MISSING') AS tiktok_field
FROM `settings` WHERE `id` = 1;

SELECT 
  'product_categories - icon field' AS info,
  COUNT(*) AS categories_with_icon,
  'Should be 8' AS expected
FROM `product_categories` WHERE `icon` IS NOT NULL;

SELECT 
  'posts - featured' AS info,
  COUNT(*) AS featured_posts,
  'Should be 3' AS expected
FROM `posts` WHERE `is_featured` = 1;

SELECT '=== MIGRATION P0 COMPLETE ===' AS '';
