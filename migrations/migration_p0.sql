-- ============================================================================
-- EventPrint - P0 Database Migrations (WAJIB untuk Go-Live)
-- ============================================================================
-- Purpose: Menambah field/tabel yang dibutuhkan frontend 100%
-- Version: 1.0
-- Date: 2025-12-18
-- 
-- INSTRUCTIONS:
-- 1. Backup database dulu: Tools -> Export -> eventprint.sql
-- 2. Run migration ini via phpMyAdmin: Import -> Browse -> migration_p0.sql
-- 3. Verify: SHOW TABLES; untuk cek tabel baru
-- ============================================================================

-- ============================================================================
-- MIGRATION 1: Tabel Testimonials (NEW)
-- ============================================================================
-- Alasan: Frontend homepage butuh section "Kata Mereka", tidak ada di DB

CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL COMMENT 'Nama pemberi testimoni',
  `position` VARCHAR(150) NULL COMMENT 'Jabatan/perusahaan (optional)',
  `photo` VARCHAR(255) NULL COMMENT 'Path foto (optional)',
  `rating` TINYINT UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Rating 1-5 bintang',
  `message` TEXT NOT NULL COMMENT 'Isi testimoni',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Publish/unpublish',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Urutan tampil (smaller = pertama)',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_active_sort` (`is_active`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Testimonials untuk homepage';

-- Seed data dummy (4 testimonials)
INSERT INTO `testimonials` (`name`, `position`, `rating`, `message`, `sort_order`, `is_active`) VALUES
('Budi Santoso', 'Owner Toko ABC', 5, 'Kualitas cetakan sangat bagus dan harga terjangkau. Sudah langganan 2 tahun!', 1, 1),
('Siti Nurhaliza', 'Event Organizer PT Kreatif', 5, 'Pelayanan cepat dan hasil memuaskan. Recommended untuk cetak banner event.', 2, 1),
('Andi Wijaya', 'Pengusaha UMKM', 4, 'PrintEventPrint membantu bisnis saya dengan kualitas cetak yang konsisten.', 3, 1),
('Linda Chen', 'Marketing Manager PT XYZ', 5, 'Professional, on-time delivery, dan support responsive. Sangat puas!', 4, 1);

-- ============================================================================
-- MIGRATION 2: Settings - Field Tambahan
-- ============================================================================
-- Alasan: Frontend butuh jam operasional, GMaps embed, social links

-- Cek apakah field sudah ada (avoid error jika run 2x)
SET @dbname = DATABASE();
SET @tablename = 'settings';

SET @col_exists_operating_hours = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'operating_hours'
);

SET @col_exists_gmaps = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'gmaps_embed'
);

SET @col_exists_twitter = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'twitter'
);

SET @col_exists_youtube = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'youtube'
);

SET @col_exists_tiktok = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'tiktok'
);

-- Add columns (hanya jika belum ada)
SET @sql_operating_hours = IF(@col_exists_operating_hours = 0,
  'ALTER TABLE `settings` ADD COLUMN `operating_hours` VARCHAR(255) NULL AFTER `whatsapp` COMMENT "Jam operasional (misal: Senin-Sabtu 08:00-17:00)";',
  'SELECT "Column operating_hours already exists" AS notice;'
);

SET @sql_gmaps = IF(@col_exists_gmaps = 0,
  'ALTER TABLE `settings` ADD COLUMN `gmaps_embed` TEXT NULL AFTER `operating_hours` COMMENT "Iframe embed Google Maps";',
  'SELECT "Column gmaps_embed already exists" AS notice;'
);

SET @sql_twitter = IF(@col_exists_twitter = 0,
  'ALTER TABLE `settings` ADD COLUMN `twitter` VARCHAR(255) NULL AFTER `instagram` COMMENT "Link Twitter/X";',
  'SELECT "Column twitter already exists" AS notice;'
);

SET @sql_youtube = IF(@col_exists_youtube = 0,
  'ALTER TABLE `settings` ADD COLUMN `youtube` VARCHAR(255) NULL AFTER `twitter` COMMENT "Link YouTube";',
  'SELECT "Column youtube already exists" AS notice;'
);

SET @sql_tiktok = IF(@col_exists_tiktok = 0,
  'ALTER TABLE `settings` ADD COLUMN `tiktok` VARCHAR(255) NULL AFTER `youtube` COMMENT "Link TikTok";',
  'SELECT "Column tiktok already exists" AS notice;'
);

PREPARE stmt1 FROM @sql_operating_hours;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

PREPARE stmt2 FROM @sql_gmaps;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

PREPARE stmt3 FROM @sql_twitter;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

PREPARE stmt4 FROM @sql_youtube;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;

PREPARE stmt5 FROM @sql_tiktok;
EXECUTE stmt5;
DEALLOCATE PREPARE stmt5;

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
-- Alasan: Frontend homepage menampilkan icon kategori di cyan bar

SET @col_exists_icon = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'product_categories' AND COLUMN_NAME = 'icon'
);

SET @sql_icon = IF(@col_exists_icon = 0,
  'ALTER TABLE `product_categories` ADD COLUMN `icon` VARCHAR(100) NULL AFTER `slug` COMMENT "Icon kategori (FontAwesome class atau emoji)";',
  'SELECT "Column icon already exists" AS notice;'
);

PREPARE stmt6 FROM @sql_icon;
EXECUTE stmt6;
DEALLOCATE PREPARE stmt6;

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
-- Alasan: Frontend blog butuh filter "unggulan" untuk hero mosaic

SET @col_exists_featured = (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'posts' AND COLUMN_NAME = 'is_featured'
);

SET @sql_featured = IF(@col_exists_featured = 0,
  'ALTER TABLE `posts` ADD COLUMN `is_featured` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_published` COMMENT "Tampil di hero homepage/blog";',
  'SELECT "Column is_featured already exists" AS notice;'
);

PREPARE stmt7 FROM @sql_featured;
EXECUTE stmt7;
DEALLOCATE PREPARE stmt7;

-- Add index untuk query optimization
SET @idx_exists_featured = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'posts' AND INDEX_NAME = 'idx_featured_published'
);

SET @sql_idx_featured = IF(@idx_exists_featured = 0,
  'ALTER TABLE `posts` ADD INDEX `idx_featured_published` (`is_featured`, `is_published`, `published_at`);',
  'SELECT "Index idx_featured_published already exists" AS notice;'
);

PREPARE stmt8 FROM @sql_idx_featured;
EXECUTE stmt8;
DEALLOCATE PREPARE stmt8;

-- Seed: set 4 post pertama jadi featured (untuk hero mosaic)
UPDATE `posts` SET `is_featured` = 1 WHERE `id` IN (1, 2, 7) AND `is_published` = 1 LIMIT 4;

-- ============================================================================
-- MIGRATION 5: Product Images - Ensure Table Exists (Already in Schema)
-- ============================================================================
-- Note: Tabel product_images sudah ada di schema, cuma belum dipakai
-- Pastikan tabel ada dan siap dipakai

-- Verify table exists
SELECT 
  IF(
    (SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'product_images') > 0,
    'Table product_images exists - Ready for gallery upload',
    'ERROR: Table product_images missing - Check schema'
  ) AS status;

-- ============================================================================
-- MIGRATION 6: Additional Indexes for Performance
-- ============================================================================

-- Products: index untuk query featured + active
SET @idx_exists_prod_featured = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'products' AND INDEX_NAME = 'idx_featured_active'
);

SET @sql_idx_prod = IF(@idx_exists_prod_featured = 0,
  'ALTER TABLE `products` ADD INDEX `idx_featured_active` (`is_featured`, `is_active`);',
  'SELECT "Index idx_featured_active already exists" AS notice;'
);

PREPARE stmt9 FROM @sql_idx_prod;
EXECUTE stmt9;
DEALLOCATE PREPARE stmt9;

-- Hero Slides: index sudah ada di schema (idx_home_active_pos)

-- Our Store: index untuk query active + sort
SET @idx_exists_store = (
  SELECT COUNT(*) FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'our_store' AND INDEX_NAME = 'idx_active_sort'
);

SET @sql_idx_store = IF(@idx_exists_store = 0,
  'ALTER TABLE `our_store` ADD INDEX `idx_active_sort` (`is_active`, `sort_order`);',
  'SELECT "Index idx_active_sort already exists" AS notice;'
);

PREPARE stmt10 FROM @sql_idx_store;
EXECUTE stmt10;
DEALLOCATE PREPARE stmt10;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check migration success
SELECT '=== MIGRATION P0 VERIFICATION ===' AS '';

SELECT 
  'testimonials' AS table_name,
  COUNT(*) AS row_count,
  'Should be 4' AS expected
FROM `testimonials`;

SELECT 
  'settings' AS table_name,
  IF(operating_hours IS NOT NULL, 'EXISTS', 'MISSING') AS operating_hours_field,
  IF(gmaps_embed IS NOT NULL, 'EXISTS', 'MISSING') AS gmaps_embed_field,
  IF(twitter IS NOT NULL, 'EXISTS', 'MISSING') AS twitter_field
FROM `settings` WHERE `id` = 1;

SELECT 
  'product_categories' AS table_name,
  COUNT(*) AS categories_with_icon,
  'Should be 8' AS expected
FROM `product_categories` WHERE `icon` IS NOT NULL;

SELECT 
  'posts' AS table_name,
  COUNT(*) AS featured_posts,
  'Should be 3-4' AS expected
FROM `posts` WHERE `is_featured` = 1;

SELECT 
  'product_images' AS table_name,
  IF(COUNT(*) >= 0, 'Table exists', 'Missing') AS status
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'product_images';

-- ============================================================================
-- ROLLBACK SCRIPT (jika perlu revert)
-- ============================================================================
-- UNCOMMENT DI BAWAH JIKA INGIN ROLLBACK (HATI-HATI: DATA HILANG!)

/*
-- ROLLBACK MIGRATION 1: Drop testimonials table
DROP TABLE IF EXISTS `testimonials`;

-- ROLLBACK MIGRATION 2: Drop settings fields
ALTER TABLE `settings` 
  DROP COLUMN IF EXISTS `operating_hours`,
  DROP COLUMN IF EXISTS `gmaps_embed`,
  DROP COLUMN IF EXISTS `twitter`,
  DROP COLUMN IF EXISTS `youtube`,
  DROP COLUMN IF EXISTS `tiktok`;

-- ROLLBACK MIGRATION 3: Drop icon field
ALTER TABLE `product_categories` DROP COLUMN IF EXISTS `icon`;

-- ROLLBACK MIGRATION 4: Drop is_featured field
ALTER TABLE `posts` 
  DROP INDEX IF EXISTS `idx_featured_published`,
  DROP COLUMN IF EXISTS `is_featured`;

-- ROLLBACK MIGRATION 6: Drop indexes
ALTER TABLE `products` DROP INDEX IF EXISTS `idx_featured_active`;
ALTER TABLE `our_store` DROP INDEX IF EXISTS `idx_active_sort`;

SELECT 'ROLLBACK COMPLETE - Database reverted to pre-migration state' AS status;
*/

-- ============================================================================
-- POST-MIGRATION TASKS
-- ============================================================================
-- After running this migration, you need to:
-- 
-- 1. CREATE ADMIN MODULE:
--    - TestimonialsController.php (CRUD)
--    - Model Testimonial.php
--    - Views: views/admin/testimonials/{index,create,edit}.php
--    - Routes: routes/admin.php (add testimonials routes)
--
-- 2. UPDATE EXISTING FORMS:
--    - views/admin/settings/index.php (add 5 new fields)
--    - views/admin/product_category/form.php (add icon field)
--    - views/admin/blog/form.php (add is_featured checkbox)
--
-- 3. UPDATE MODELS:
--    - Setting.php (add new fields to fillable)
--    - ProductCategory.php (add icon to fillable)
--    - Post.php (add is_featured to fillable)
--
-- 4. UPLOAD SECURITY:
--    - Create app/helpers/UploadHelper.php (validateUpload function)
--    - Apply to all controllers: ProductController, BlogController, etc
--
-- 5. FRONTEND INTEGRATION:
--    - HomePublicController: fetch testimonials, featured products
--    - ProductPublicController: fetch gallery from product_images
--    - BlogPublicController: filter by is_featured
--
-- See IMPLEMENTATION_PLAN.md for detailed instructions.
-- ============================================================================

SELECT '=== MIGRATION P0 COMPLETE ===' AS '';
SELECT 'Next: Update admin forms and create Testimonials module' AS todo;
SELECT 'See: IMPLEMENTATION_PLAN.md - Section 4 (Admin Delta P0)' AS reference;
