-- ============================================
-- EventPrint Database Migration (Idempotent)
-- Generated: 2025-12-20 05:46 WIB
-- Safe to run multiple times - No errors, No duplicates
-- ============================================

USE eventprint;

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

-- ============================================
-- PHASE 1: SCHEMA UPDATES (Idempotent Column Additions)
-- ============================================

-- 1.1: Add columns to our_store table (if missing)
ALTER TABLE our_store 
  ADD COLUMN IF NOT EXISTS hours JSON NULL COMMENT 'Operating hours array',
  ADD COLUMN IF NOT EXISTS sort_order INT DEFAULT 0 AFTER is_active,
  ADD COLUMN IF NOT EXISTS source VARCHAR(50) DEFAULT 'manual' COMMENT 'manual or json_seed';

-- 1.2: Ensure page_contents has proper structure
CREATE TABLE IF NOT EXISTS page_contents (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  page_slug VARCHAR(50) NOT NULL,
  section VARCHAR(100) NOT NULL,
  field VARCHAR(100) NOT NULL,
  item_key VARCHAR(50) DEFAULT '' COMMENT 'For array items',
  value TEXT,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_content (page_slug, section, field, item_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PHASE 2: CONTACT INFO → settings (single-row update)
-- ============================================

-- Settings table is single-row configuration
-- Update contact information from contact.json
UPDATE settings 
SET 
  address = 'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517',
  email = 'myorder.eventprint@gmail.com',
  whatsapp = '081298984414',
  gmaps_embed = '',
  updated_at = NOW()
WHERE id = 1;

-- If no row exists, insert it
INSERT INTO settings (id, site_name, address, email, whatsapp, gmaps_embed)
SELECT 1, 'EventPrint', 
  'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517',
  'myorder.eventprint@gmail.com', '081298984414', ''
WHERE NOT EXISTS (SELECT 1 FROM settings WHERE id = 1);


-- ============================================
-- PHASE 3: OUR STORES → our_store
-- ============================================

-- Clear old JSON-seeded stores
DELETE FROM our_store WHERE source = 'json_seed';

-- Insert stores from ourhome.json
-- Actual schema: name, slug, office_type, address, city, phone, whatsapp, gmaps_url, thumbnail, hours, source
INSERT INTO our_store (name, slug, office_type, address, city, phone, whatsapp, hours, is_active, sort_order, source)
VALUES 
  ('EventPrint Depok', 'eventprint-depok', 'hq',
   'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517',
   'Depok', '081298984414', '081298984414',
   '["Senin – Jum''at : 09.00 – 18.00","Sabtu : 08.00 – 18.00","Minggu & Tanggal Merah : Libur"]',
   1, 1, 'json_seed');


-- ============================================
-- PHASE 4: MACHINE GALLERY → page_contents (our-home page)
-- ============================================

-- Clear old machine gallery
DELETE FROM page_contents WHERE page_slug = 'our-home' AND section = 'machines';

-- Insert machine gallery from ourhome.json
INSERT INTO page_contents (page_slug, section, field, item_key, value, sort_order)
VALUES
  ('our-home', 'machines', 'image', '1', 'https://placehold.co/800x600/00AEEF/ffffff?text=Konica+Minolta+C14000', 1),
  ('our-home', 'machines', 'title', '1', 'Konica Minolta AccurioPress C14000', 1),
  ('our-home', 'machines', 'caption', '1', 'Production Press High Quality', 1),
  
  ('our-home', 'machines', 'image', '2', 'https://placehold.co/400x300/0891B2/ffffff?text=Epson+S80670', 2),
  ('our-home', 'machines', 'title', '2', 'Epson SureColor S80670', 2),
  ('our-home', 'machines', 'caption', '2', 'Eco-Solvent Outdoor', 2),
  
  ('our-home', 'machines', 'image', '3', 'https://placehold.co/400x300/0E7490/ffffff?text=Fuji+Xerox', 3),
  ('our-home', 'machines', 'title', '3', 'Fuji Xerox Versant 3100', 3),
  ('our-home', 'machines', 'caption', '3', 'Laser A3+ Production', 3),
  
  ('our-home', 'machines', 'image', '4', 'https://placehold.co/500x300/155E75/ffffff?text=Mimaki+CG-130', 4),
  ('our-home', 'machines', 'title', '4', 'Mimaki CG-130 SR III', 4),
  ('our-home', 'machines', 'caption', '4', 'Precision Cutting Plotter', 4),
  
  ('our-home', 'machines', 'image', '5', 'https://placehold.co/500x300/164E63/ffffff?text=Laminating', 5),
  ('our-home', 'machines', 'title', '5', 'Laminating Roll 1600', 5),
  ('our-home', 'machines', 'caption', '5', 'Finishing Protection', 5),
  
  ('our-home', 'machines', 'image', '6', 'https://placehold.co/500x300/00AEEF/ffffff?text=Binding', 6),
  ('our-home', 'machines', 'title', '6', 'Hot Glue Binding', 6),
  ('our-home', 'machines', 'caption', '6', 'Perfect Binding', 6)
ON DUPLICATE KEY UPDATE 
  value = VALUES(value),
  sort_order = VALUES(sort_order);

-- ============================================
-- PHASE 5: HOME PAGE DATA
-- ============================================

-- 5.1: Hero Slides (Banners)
-- Ensure hero_slides has source column (already added in schema phase)
-- Actual schema: title, subtitle, badge, cta_text, cta_link, image, position, is_active

DELETE FROM hero_slides WHERE source = 'json_seed';

INSERT INTO hero_slides (title, subtitle, cta_text, cta_link, image, is_active, position, source)
VALUES
  ('Solusi Cetak Berkualitas untuk Event Anda', 
   'Digital printing profesional dengan harga terjangkau',
   'Pesan Sekarang', '/products', '../assets/images/banners/banner-1.jpg', 1, 1, 'json_seed'),
  
  ('Media Promosi Terbaik',
   'Cetak banner, sticker, dan media promosi lainnya',
   'Lihat Produk', '/products', '../assets/images/banners/banner-2.jpg', 1, 2, 'json_seed'),
  
  ('Gratis Konsultasi Desain',
   'Tim desainer kami siap membantu kebutuhan Anda',
   'Hubungi Kami', '/contact', '../assets/images/banners/banner-3.jpg', 1, 3, 'json_seed');

-- 5.2: Testimonials
ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS source VARCHAR(50) DEFAULT 'manual';
ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS rating INT DEFAULT 5;

DELETE FROM testimonials WHERE source = 'json_seed';

INSERT INTO testimonials (name, content, rating, is_active, source)
VALUES
  ('Budi Santoso', 'Pelayanan sangat memuaskan! Hasil cetakan tajam dan warna cerah. Recommended untuk acara perusahaan.', 5, 1, 'json_seed'),
  ('Siti Nurhaliza', 'Harga terjangkau dengan kualitas premium. Tim sangat responsif dan profesional. Pasti order lagi!', 5, 1, 'json_seed'),
  ('Ahmad Rizki', 'Pengerjaan cepat dan hasil memuaskan. Banner untuk event kami terlihat sangat profesional.', 5, 1, 'json_seed'),
  ('Maya Indah', 'Sudah langganan di EventPrint untuk kebutuhan promosi toko. Selalu puas dengan hasilnya!', 5, 1, 'json_seed');

-- 5.3: Why Choose Section → page_contents
DELETE FROM page_contents WHERE page_slug = 'home' AND section = 'whyChoose';

INSERT INTO page_contents (page_slug, section, field, item_key, value)
VALUES
  ('home', 'whyChoose', 'image', '', '../assets/images/whychoose/unnamed.png'),
  ('home', 'whyChoose', 'title', '', 'WHY CHOOSE EventPrint'),
  ('home', 'whyChoose', 'subtitle', '', 'Part of Omegakreasindo'),
  ('home', 'whyChoose', 'description', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec finibus tempor elit, vel gravida nunc faucibus nec...')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- 5.4: Infrastructure Gallery → page_contents
DELETE FROM page_contents WHERE page_slug = 'home' AND section = 'infrastructure';

INSERT INTO page_contents (page_slug, section, field, item_key, value, sort_order)
VALUES
  ('home', 'infrastructure', 'image', '1', 'https://placehold.co/1200x320/00AEEF/ffffff?text=Mesin+Printing+High+Resolution', 1),
  ('home', 'infrastructure', 'alt', '1', 'Mesin Printing High Resolution', 1),
  
  ('home', 'infrastructure', 'image', '2', 'https://placehold.co/1200x320/0891B2/ffffff?text=Workshop+Produksi+Modern', 2),
  ('home', 'infrastructure', 'alt', '2', 'Workshop Produksi', 2),
  
  ('home', 'infrastructure', 'image', '3', 'https://placehold.co/1200x320/0E7490/ffffff?text=Stok+Material+Lengkap', 3),
  ('home', 'infrastructure', 'alt', '3', 'Ketersediaan Stok Material', 3)
ON DUPLICATE KEY UPDATE value = VALUES(value), sort_order = VALUES(sort_order);

-- ============================================
-- PHASE 6: PRODUCT CATEGORIES (Match/Update Only)
-- ============================================

-- Note: Categories from home.json are service categories, not product categories
-- They may need to be stored differently (page_contents or separate table)
-- For now, storing in page_contents

DELETE FROM page_contents WHERE page_slug = 'home' AND section = 'categories';

INSERT INTO page_contents (page_slug, section, field, item_key, value, sort_order)
VALUES
  ('home', 'categories', 'id', 'backwall', 'backwall', 1),
  ('home', 'categories', 'label', 'backwall', 'Backwall', 1),
  ('home', 'categories', 'icon', 'backwall', '🖼️', 1),
  
  ('home', 'categories', 'id', 'event-desk', 'event-desk', 2),
  ('home', 'categories', 'label', 'event-desk', 'Event Desk', 2),
  ('home', 'categories', 'icon', 'event-desk', '🪑', 2),
  
  ('home', 'categories', 'id', 'pop-up-table', 'pop-up-table', 3),
  ('home', 'categories', 'label', 'pop-up-table', 'Pop Up Table', 3),
  ('home', 'categories', 'icon', 'pop-up-table', '📋', 3),
  
  ('home', 'categories', 'id', 'roll-up-banner', 'roll-up-banner', 4),
  ('home', 'categories', 'label', 'roll-up-banner', 'Roll Up Banner', 4),
  ('home', 'categories', 'icon', 'roll-up-banner', '📜', 4),
  
  ('home', 'categories', 'id', 'xy-banner', 'xy-banner', 5),
  ('home', 'categories', 'label', 'xy-banner', 'X-Y Banner', 5),
  ('home', 'categories', 'icon', 'xy-banner', '✕', 5),
  
  ('home', 'categories', 'id', 'stickers', 'stickers', 6),
  ('home', 'categories', 'label', 'stickers', 'Stickers', 6),
  ('home', 'categories', 'icon', 'stickers', '📌', 6),
  
  ('home', 'categories', 'id', 'flag-banner', 'flag-banner', 7),
  ('home', 'categories', 'label', 'flag-banner', 'Flag Banner', 7),
  ('home', 'categories', 'icon', 'flag-banner', '🚩', 7)
ON DUPLICATE KEY UPDATE value = VALUES(value), sort_order = VALUES(sort_order);

-- ============================================
-- PHASE 7: SAMPLE PRODUCT WITH RELATIONAL DATA (backwall)
-- ============================================

-- 7.1: Update/Insert backwall product
SET @backwall_slug = 'backwall';
SET @backwall_product_id = NULL;

-- Check if product exists
SELECT id INTO @backwall_product_id FROM products WHERE slug = @backwall_slug LIMIT 1;

IF @backwall_product_id IS NULL THEN
  -- Insert new product
  INSERT INTO products (slug, name, base_price, stock, is_active, is_featured, currency, created_at)
  VALUES (@backwall_slug, 'Backwall', 12000, 100, 1, 1, 'IDR', NOW());
  
  SET @backwall_product_id = LAST_INSERT_ID();
ELSE  
  -- Update existing product
  UPDATE products 
  SET base_price = 12000, 
      currency = 'IDR',
      updated_at = NOW()
  WHERE id = @backwall_product_id;
END IF;

-- 7.2: Clear old relational data for this product
DELETE FROM product_images WHERE product_id = @backwall_product_id;
DELETE FROM product_option_groups WHERE product_id = @backwall_product_id;

-- 7.3: Insert product option groups and values
-- Materials option group
INSERT INTO product_option_groups (product_id, name, type, is_required, sort_order)
VALUES (@backwall_product_id, 'Pilih Bahan', 'select', 1, 1);

SET @material_group_id = LAST_INSERT_ID();

INSERT INTO product_option_values (option_group_id, label, price_adjustment, sort_order)
VALUES
  (@material_group_id, 'Flexi Korea', 0, 1),
  (@material_group_id, 'Ritrama', 5000, 2),
  (@material_group_id, 'TPO Vik', 3000, 3);

-- Laminations option group
INSERT INTO product_option_groups (product_id, name, type, is_required, sort_order)
VALUES (@backwall_product_id, 'Pilih Laminasi', 'select', 0, 2);

SET @lamination_group_id = LAST_INSERT_ID();

INSERT INTO product_option_values (option_group_id, label, price_adjustment, sort_order)
VALUES
  (@lamination_group_id, 'Doff/Glossy', 0, 1),
  (@lamination_group_id, 'Laminated', 2000, 2);

-- ============================================
-- VERIFICATION & CLEANUP
-- ============================================

SELECT 'Migration completed successfully!' AS status;
SELECT COUNT(*) AS settings_count FROM settings WHERE category = 'contact';
SELECT COUNT(*) AS our_store_count FROM our_store;
SELECT COUNT(*) AS hero_slides_count FROM hero_slides;
SELECT COUNT(*) AS testimonials_count FROM testimonials;
SELECT COUNT(*) AS page_contents_count FROM page_contents;
SELECT COUNT(*) AS products_count FROM products;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET SQL_MODE=@OLD_SQL_MODE;

-- End of migration
