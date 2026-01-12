-- ============================================
-- Migration: Add icon column to product_categories
-- Created: 2026-01-04
-- Description: Adds icon column for category icons/emojis
-- ============================================

-- Check if column exists before adding
SET @dbname = DATABASE();
SET @tablename = 'product_categories';
SET @columnname = 'icon';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  'SELECT "Column already exists" AS message;',
  'ALTER TABLE product_categories ADD COLUMN icon VARCHAR(50) DEFAULT NULL AFTER slug;'
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Optional: Set default icons for existing categories
UPDATE product_categories SET icon = '🖨️' WHERE slug = 'digital-printing' AND icon IS NULL;
UPDATE product_categories SET icon = '📢' WHERE slug = 'media-promosi' AND icon IS NULL;
UPDATE product_categories SET icon = '🎁' WHERE slug = 'merchandise' AND icon IS NULL;
UPDATE product_categories SET icon = '🏷️' WHERE slug LIKE '%stiker%' AND icon IS NULL;
UPDATE product_categories SET icon = '📦' WHERE icon IS NULL; -- Default for others

-- Verify migration
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    CHARACTER_MAXIMUM_LENGTH,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'product_categories' 
  AND COLUMN_NAME = 'icon';

SELECT '✅ Migration completed successfully!' AS status;
