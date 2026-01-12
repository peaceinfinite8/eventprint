-- ============================================
-- HOTFIX: Fix Existing Testimonials Table
-- Description: Rename columns to match code expectations
-- Run this if testimonials table already exists
-- ============================================

-- Drop table and recreate with correct schema
DROP TABLE IF EXISTS `testimonials`;

CREATE TABLE `testimonials` (
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

SELECT '✅ Testimonials table fixed!' AS status;
