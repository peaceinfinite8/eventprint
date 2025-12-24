-- ============================================
-- EventPrint Database Migration (Simplified & Idempotent)
-- Generated: 2025-12-20 05:52 WIB
-- Safe to run multiple times
-- ============================================

USE eventprint;

-- ============================================
-- PHASE 1: Update Contact Info in Settings Table
-- ============================================

UPDATE settings 
SET 
  address = 'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517',
  email = 'myorder.eventprint@gmail.com',
  whatsapp = '081298984414',
  gmaps_embed = '',
  updated_at = NOW()
WHERE id = 1;

-- ============================================
-- PHASE 2: Ensure page_contents Table Exists
-- ============================================

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
-- PHASE 3: Machine Gallery Data → page_contents
-- ============================================

DELETE FROM page_contents WHERE page_slug = 'our-home' AND section = 'machines';

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
  ('our-home', 'machines', 'title', '4', 'Mimaki CG-130 SRIII', 4),
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
-- PHASE 4: Why Choose Section → page_contents
-- ============================================

DELETE FROM page_contents WHERE page_slug = 'home' AND section = 'whyChoose';

INSERT INTO page_contents (page_slug, section, field, item_key, value)
VALUES
  ('home', 'whyChoose', 'image', '', '../assets/images/whychoose/unnamed.png'),
  ('home', 'whyChoose', 'title', '', 'WHY CHOOSE EventPrint'),
  ('home', 'whyChoose', 'subtitle', '', 'Part of Omegakreasindo'),
  ('home', 'whyChoose', 'description', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec finibus tempor elit, vel gravida nunc faucibus nec. Sed enim nisl, molestie vitae ex interdum, iaculis blandit mauris. Cras ac nisl ornare ex tincidunt suscipit eget imperdiet leo. Cras eu lobortis metus, sit amet tincidunt est. Integer id ipsum quis diam scelerisque auctor. Etiam nec magna congue, dignissim leo eget, fringilla ipsum. Nunc malesuada facilisis purus ac pretium. Cras nec auctor massa. Vestibulum eget gravida felis. Etiam in lacus nulla. "Nge Print Mudah Tanpa Keluar Rumah..."')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- ============================================
-- PHASE 5: Infrastructure Gallery → page_contents
-- ============================================

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
-- VERIFICATION
-- ============================================

SELECT 'Migration completed successfully!' AS status;
SELECT COUNT(*) AS page_contents_count FROM page_contents;

-- End of simplified migration
