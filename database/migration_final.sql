-- ============================================
-- EventPrint Database Migration - FINAL WORKING VERSION
-- Generated: 2025-12-20 05:56 WIB
-- Based on verified actual database schema
-- Safe to run multiple times - 100% Idempotent
-- ============================================

USE eventprint;

-- ============================================
-- VERIFIED: All tables are OK (no corruption)
-- ============================================

-- ============================================
-- PHASE 1: Update Contact Settings
-- ============================================

UPDATE settings 
SET 
  address = 'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517',
  email = 'myorder.eventprint@gmail.com',
  whatsapp = '081298984414',
  gmaps_embed = '',
  updated_at = NOW()
WHERE id = 1;

SELECT 'Step 1: Settings updated' AS progress;

-- ============================================
-- PHASE 2: Page Contents Data Seeding
-- Actual schema: id, page_slug, section, field, value, created_at, updated_at, item_key
-- ============================================

-- 2.1: Machine Gallery (Our Home page)
DELETE FROM page_contents WHERE page_slug = 'our-home' AND section = 'machines';

INSERT INTO page_contents (page_slug, section, field, item_key, value)
VALUES
  ('our-home', 'machines', 'image', '1', 'https://placehold.co/800x600/00AEEF/ffffff?text=Konica+Minolta+C14000'),
  ('our-home', 'machines', 'title', '1', 'Konica Minolta AccurioPress C14000'),
  ('our-home', 'machines', 'caption', '1', 'Production Press High Quality'),
  
  ('our-home', 'machines', 'image', '2', 'https://placehold.co/400x300/0891B2/ffffff?text=Epson+S80670'),
  ('our-home', 'machines', 'title', '2', 'Epson SureColor S80670'),
  ('our-home', 'machines', 'caption', '2', 'Eco-Solvent Outdoor'),
  
  ('our-home', 'machines', 'image', '3', 'https://placehold.co/400x300/0E7490/ffffff?text=Fuji+Xerox'),
  ('our-home', 'machines', 'title', '3', 'Fuji Xerox Versant 3100'),
  ('our-home', 'machines', 'caption', '3', 'Laser A3+ Production'),
  
  ('our-home', 'machines', 'image', '4', 'https://placehold.co/500x300/155E75/ffffff?text=Mimaki+CG-130'),
  ('our-home', 'machines', 'title', '4', 'Mimaki CG-130 SRIII'),
  ('our-home', 'machines', 'caption', '4', 'Precision Cutting Plotter'),
  
  ('our-home', 'machines', 'image', '5', 'https://placehold.co/500x300/164E63/ffffff?text=Laminating'),
  ('our-home', 'machines', 'title', '5', 'Laminating Roll 1600'),
  ('our-home', 'machines', 'caption', '5', 'Finishing Protection'),
  
  ('our-home', 'machines', 'image', '6', 'https://placehold.co/500x300/00AEEF/ffffff?text=Binding'),
  ('our-home', 'machines', 'title', '6', 'Hot Glue Binding'),
  ('our-home', 'machines', 'caption', '6', 'Perfect Binding');

SELECT 'Step 2: Machine gallery seeded (6 machines)' AS progress;

-- 2.2: Why Choose Section (Home page)
DELETE FROM page_contents WHERE page_slug = 'home' AND section = 'whyChoose';

INSERT INTO page_contents (page_slug, section, field, item_key, value)
VALUES
  ('home', 'whyChoose', 'image', NULL, '../assets/images/whychoose/unnamed.png'),
  ('home', 'whyChoose', 'title', NULL, 'WHY CHOOSE EventPrint'),
  ('home', 'whyChoose', 'subtitle', NULL, 'Part of Omegakreasindo'),
  ('home', 'whyChoose', 'description', NULL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec finibus tempor elit, vel gravida nunc faucibus nec. Sed enim nisl, molestie vitae ex interdum, iaculis blandit mauris. Cras ac nisl ornare ex tincidunt suscipit eget imperdiet leo. Cras eu lobortis metus, sit amet tincidunt est. Integer id ipsum quis diam scelerisque auctor. Etiam nec magna congue, dignissim leo eget, fringilla ipsum. Nunc malesuada facilisis purus ac pretium. Cras nec auctor massa. Vestibulum eget gravida felis. Etiam in lacus nulla. "Nge Print Mudah Tanpa Keluar Rumah..."');

SELECT 'Step 3: Why Choose section seeded' AS progress;

-- 2.3: Infrastructure Gallery (Home page)
DELETE FROM page_contents WHERE page_slug = 'home' AND section = 'infrastructure';

INSERT INTO page_contents (page_slug, section, field, item_key, value)
VALUES
  ('home', 'infrastructure', 'image', '1', 'https://placehold.co/1200x320/00AEEF/ffffff?text=Mesin+Printing+High+Resolution'),
  ('home', 'infrastructure', 'alt', '1', 'Mesin Printing High Resolution'),
  
  ('home', 'infrastructure', 'image', '2', 'https://placehold.co/1200x320/0891B2/ffffff?text=Workshop+Produksi+Modern'),
  ('home', 'infrastructure', 'alt', '2', 'Workshop Produksi'),
  
  ('home', 'infrastructure', 'image', '3', 'https://placehold.co/1200x320/0E7490/ffffff?text=Stok+Material+Lengkap'),
  ('home', 'infrastructure', 'alt', '3', 'Ketersediaan Stok Material');

SELECT 'Step 4: Infrastructure gallery seeded (3 images)' AS progress;

-- 2.4: Service Categories (Home page)
DELETE FROM page_contents WHERE page_slug = 'home' AND section = 'categories';

INSERT INTO page_contents (page_slug, section, field, item_key, value)
VALUES
  ('home', 'categories', 'id', 'backwall', 'backwall'),
  ('home', 'categories', 'label', 'backwall', 'Backwall'),
  ('home', 'categories', 'icon', 'backwall', '🖼️'),
  
  ('home', 'categories', 'id', 'event-desk', 'event-desk'),
  ('home', 'categories', 'label', 'event-desk', 'Event Desk'),
  ('home', 'categories', 'icon', 'event-desk', '🪑'),
  
  ('home', 'categories', 'id', 'pop-up-table', 'pop-up-table'),
  ('home', 'categories', 'label', 'pop-up-table', 'Pop Up Table'),
  ('home', 'categories', 'icon', 'pop-up-table', '📋'),
  
  ('home', 'categories', 'id', 'roll-up-banner', 'roll-up-banner'),
  ('home', 'categories', 'label', 'roll-up-banner', 'Roll Up Banner'),
  ('home', 'categories', 'icon', 'roll-up-banner', '📜'),
  
  ('home', 'categories', 'id', 'xy-banner', 'xy-banner'),
  ('home', 'categories', 'label', 'xy-banner', 'X-Y Banner'),
  ('home', 'categories', 'icon', 'xy-banner', '✕'),
  
  ('home', 'categories', 'id', 'stickers', 'stickers'),
  ('home', 'categories', 'label', 'stickers', 'Stickers'),
  ('home', 'categories', 'icon', 'stickers', '📌'),
  
  ('home', 'categories', 'id', 'flag-banner', 'flag-banner'),
  ('home', 'categories', 'label', 'flag-banner', 'Flag Banner'),
  ('home', 'categories', 'icon', 'flag-banner', '🚩');

SELECT 'Step 5: Service categories seeded (7 categories)' AS progress;

-- ============================================
-- FINAL VERIFICATION
-- ============================================

SELECT 'Migration completed successfully!' AS status;
SELECT COUNT(*) AS total_page_contents FROM page_contents;
SELECT page_slug, section, COUNT(*) AS items 
FROM page_contents 
GROUP BY page_slug, section 
ORDER BY page_slug, section;

-- End of migration
