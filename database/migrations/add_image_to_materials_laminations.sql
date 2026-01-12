-- Add image_path column to materials and laminations tables
ALTER TABLE materials ADD COLUMN image_path VARCHAR(255) NULL AFTER sort_order;
ALTER TABLE laminations ADD COLUMN image_path VARCHAR(255) NULL AFTER sort_order;
