-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2025 at 06:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventprint`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'Asrul', 'asrul@example.com', '787465254', 'banner', 'saya ingin memesan banner dengan ukuran 5km x 5km', 1, '2025-12-10 10:58:57'),
(3, 'lana', 'lana@gmai.com', '123443', 'lana keren', 'Halo lana', 1, '2025-12-14 14:10:04');

-- --------------------------------------------------------

--
-- Table structure for table `hero_slides`
--

CREATE TABLE `hero_slides` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_slug` varchar(100) NOT NULL DEFAULT 'home',
  `title` varchar(150) NOT NULL,
  `subtitle` varchar(500) DEFAULT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `cta_text` varchar(100) DEFAULT NULL,
  `cta_link` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `position` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_slides`
--

INSERT INTO `hero_slides` (`id`, `page_slug`, `title`, `subtitle`, `badge`, `cta_text`, `cta_link`, `image`, `position`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'home', 'Harga Kompetitif', 'Paket & finishing mudah di-update dari CMS.', 'Cetak Online Terpercaya', 'Order Sekarang', '/contact#order', NULL, 1, 1, '2025-12-15 13:20:31', '2025-12-15 13:20:31');

-- --------------------------------------------------------

--
-- Table structure for table `our_store`
--

CREATE TABLE `our_store` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `office_type` enum('hq','branch') NOT NULL DEFAULT 'branch',
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `gmaps_url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `our_store`
--

INSERT INTO `our_store` (`id`, `name`, `slug`, `office_type`, `address`, `city`, `phone`, `whatsapp`, `gmaps_url`, `thumbnail`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Kantor Pusat', 'kantor-pusat', 'hq', 'Jl. BSD Grand Boulevard No.1, Pagedangan, Kec. Pagedangan, Kabupaten Tangerang, Banten 15339', 'tangerang selatan', '918649857', '81723t8712358', 'https://maps.app.goo.gl/pJth7WJ3frYsqXme6', 'uploads/our_store/20251213_112941_33fc1f17.png', 1, 1, '2025-12-13 10:29:41', '2025-12-13 10:29:41'),
(2, 'cibagedur', 'tengah-kota', 'hq', 'jl.penghulu', 'jakarta', NULL, NULL, 'https://maps.app.goo.gl/9f3VUFRvncwcy9ZR6', NULL, 1, 7, '2025-12-14 14:07:00', '2025-12-14 14:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `page_contents`
--

CREATE TABLE `page_contents` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_slug` varchar(100) NOT NULL,
  `section` varchar(100) NOT NULL,
  `field` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `item_key` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_contents`
--

INSERT INTO `page_contents` (`id`, `page_slug`, `section`, `field`, `value`, `created_at`, `updated_at`, `item_key`) VALUES
(1, 'home', 'hero', 'title', 'Harga Kompetitif', '2025-12-10 10:25:58', '2025-12-15 12:18:07', 'default'),
(2, 'home', 'hero', 'subtitle', 'Paket & finishing mudah di-update dari CMS.', '2025-12-10 10:25:58', '2025-12-15 12:18:07', 'default'),
(3, 'home', 'hero', 'button_text', 'Mediaval', '2025-12-10 10:25:58', '2025-12-15 12:18:07', 'default'),
(4, 'home', 'hero', 'button_link', '', '2025-12-10 10:25:58', '2025-12-15 12:18:07', 'default'),
(36, 'home', 'hero', 'banners', '[\r\n    {\r\n      \"title\":\"Harga Kompetitif\",\r\n      \"subtitle\":\"Paket & finishing mudah di-update\",\r\n      \"badge\":\"Cetak Online Terpercaya\",\r\n      \"cta_link\":\"/contact#order\"\r\n    }\r\n  ]', '2025-12-15 12:09:24', '2025-12-15 12:18:07', 'default'),
(37, 'home', 'hero', 'title', 'Harga Kompetitif', '2025-12-15 12:19:42', '2025-12-15 12:19:42', 'hero_1'),
(38, 'home', 'hero', 'subtitle', 'Paket & finishing mudah di-update dari CMS.', '2025-12-15 12:19:42', '2025-12-15 12:19:42', 'hero_1'),
(39, 'home', 'hero', 'badge', 'Cetak Online Terpercaya', '2025-12-15 12:19:42', '2025-12-15 12:19:42', 'hero_1'),
(40, 'home', 'hero', 'cta_text', 'Order Sekarang', '2025-12-15 12:19:42', '2025-12-15 12:19:42', 'hero_1'),
(41, 'home', 'hero', 'cta_link', '/contact#order', '2025-12-15 12:19:42', '2025-12-15 12:19:42', 'hero_1'),
(42, 'home', 'hero', 'position', '1', '2025-12-15 12:19:42', '2025-12-15 12:19:42', 'hero_1'),
(43, 'home', 'hero', 'title', ',sacbn', '2025-12-16 09:46:33', '2025-12-16 09:46:33', 'slide_20251216_104633_a7e1ac'),
(44, 'home', 'hero', 'subtitle', 'ksudvbkie', '2025-12-16 09:46:33', '2025-12-16 09:46:33', 'slide_20251216_104633_a7e1ac'),
(45, 'home', 'hero', 'badge', 'ikvneieo', '2025-12-16 09:46:33', '2025-12-16 09:46:33', 'slide_20251216_104633_a7e1ac'),
(46, 'home', 'hero', 'cta_text', 'ckibeukb', '2025-12-16 09:46:33', '2025-12-16 09:46:33', 'slide_20251216_104633_a7e1ac'),
(47, 'home', 'hero', 'cta_link', 'kbfkwefi', '2025-12-16 09:46:33', '2025-12-16 09:46:33', 'slide_20251216_104633_a7e1ac'),
(48, 'home', 'hero', 'image', '', '2025-12-16 09:46:33', '2025-12-16 09:46:33', 'slide_20251216_104633_a7e1ac'),
(49, 'home', 'hero', 'position', '1', '2025-12-16 09:46:33', '2025-12-16 09:46:33', 'slide_20251216_104633_a7e1ac'),
(50, 'home', 'hero', 'is_active', '1', '2025-12-16 09:46:33', '2025-12-16 09:46:33', 'slide_20251216_104633_a7e1ac');

-- --------------------------------------------------------

--
-- Table structure for table `page_contents_backup_20251215`
--

CREATE TABLE `page_contents_backup_20251215` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `page_slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `section` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `field` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `item_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page_contents_backup_20251215`
--

INSERT INTO `page_contents_backup_20251215` (`id`, `page_slug`, `section`, `field`, `value`, `created_at`, `updated_at`, `item_key`) VALUES
(1, 'home', 'hero', 'title', 'Harga Kompetitif', '2025-12-10 10:25:58', '2025-12-15 12:08:28', NULL),
(2, 'home', 'hero', 'subtitle', 'Paket & finishing mudah di-update dari CMS.', '2025-12-10 10:25:58', '2025-12-15 12:08:28', NULL),
(3, 'home', 'hero', 'button_text', 'Mediaval', '2025-12-10 10:25:58', '2025-12-11 15:19:54', NULL),
(4, 'home', 'hero', 'button_link', '', '2025-12-10 10:25:58', '2025-12-11 15:19:54', NULL),
(36, 'home', 'hero', 'banners', '[\r\n    {\r\n      \"title\":\"Harga Kompetitif\",\r\n      \"subtitle\":\"Paket & finishing mudah di-update\",\r\n      \"badge\":\"Cetak Online Terpercaya\",\r\n      \"cta_link\":\"/contact#order\"\r\n    }\r\n  ]', '2025-12-15 12:09:24', '2025-12-15 12:09:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `excerpt`, `content`, `thumbnail`, `is_published`, `published_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Projek 5M', 'projek-5m', 'projek gokil dah pokoknya', 'ini adalah projek sekret dari elit dunia', 'uploads/blog/post-1765358457-e0f0351c.png', 1, '2025-12-10 12:28:42', '2025-12-10 09:20:57', '2025-12-10 11:28:42', NULL),
(2, 'Projek Sawit', 'projek-sawit', 'Projek Sawit kamboja', 'dalam projek ini kami ingin membuat sebuah lahan sawit seluas 170hektar di desa kamboja', 'uploads/blog/post-1765365868-2d4f08fa.png', 1, '2025-12-11 15:30:18', '2025-12-10 11:24:28', '2025-12-11 14:30:18', NULL),
(7, 'artikel tes via postman', 'artikel-tes-via-postman', 'ini konten dummy', 'ini konten dummy', '', 1, '2025-12-11 17:14:12', '2025-12-11 16:14:12', '2025-12-11 16:14:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `base_price` decimal(12,2) DEFAULT 0.00,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `thumbnail`, `base_price`, `stock`, `is_featured`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'spanduk', 'spanduk', 'spanduk ajaib', 'spanduk gokil', 'uploads/products/product-1765287997-8700.png', 12000.00, 0, 0, 1, '2025-12-09 12:33:37', '2025-12-14 12:46:40', '2025-12-14 19:46:40'),
(2, 2, 'stiker', 'stiker ajaib', 'stiker tahan banting', 'stiker anti robek', 'uploads/products/product-1765287985-4511.png', 100000.00, 0, 1, 1, '2025-12-09 12:35:57', '2025-12-09 13:46:25', NULL),
(3, 1, 'Spanduk Outdoor 4x1 Meter', 'spanduk-outdoor-4x1', 'Spanduk outdoor bahan flexi 280gr ukuran 4x1 meter.', 'Spanduk outdoor cocok untuk promosi di jalan raya, depan toko, atau area event. Dicetak full color menggunakan mesin outdoor dengan kualitas tahan cuaca.', 'uploads/products/product-1765287885-4046.png', 250000.00, 100, 0, 1, '2025-12-09 12:40:39', '2025-12-13 11:47:11', NULL),
(4, 1, 'Banner Indoor Standing', 'banner-indoor-standing', 'Banner indoor dengan tiang standing, cocok untuk area lobby atau event.', 'Banner indoor bahan albatros dengan kualitas cetak halus, dipasang pada tiang standing yang mudah dibongkar-pasang.', 'uploads/products/product-1765287895-9858.png', 200000.00, 10000, 1, 1, '2025-12-09 12:40:39', '2025-12-14 11:33:09', NULL),
(5, 2, 'Poster A3 Full Color', 'poster-a3-full-color', 'Poster ukuran A3, cetak 1 sisi full color.', 'Poster A3 cocok untuk promo di dalam ruangan, informasi event, menu restoran, dan lain-lain. Dicetak di kertas art paper 150gr.', 'uploads/products/product-1765287913-7000.png', 15000.00, 100, 0, 0, '2025-12-09 12:40:39', '2025-12-14 12:47:29', NULL),
(6, 2, 'Flyer A5 2 Sisi', 'flyer-a5-2-sisi', 'Flyer promosi ukuran A5, cetak 2 sisi.', 'Flyer A5 2 sisi untuk kampanye promosi skala besar. Dicetak di kertas art paper 120gr, minimal order 100 lembar.', 'uploads/products/product-1765287922-6177.png', 300000.00, 0, 0, 1, '2025-12-09 12:40:39', '2025-12-09 13:45:22', NULL),
(7, 3, 'X-Banner 60x160 cm', 'x-banner-60x160', 'Paket X-Banner ukuran 60x160 cm lengkap dengan rangka.', 'X-Banner dengan bahan flexi 280gr dan rangka X yang kokoh. Praktis untuk dibawa dan dipasang di berbagai titik promosi.', 'uploads/products/product-1765287937-3503.png', 175000.00, 0, 1, 1, '2025-12-09 12:40:39', '2025-12-09 13:45:37', NULL),
(8, 3, 'Roll Up Banner 80x200 cm', 'roll-up-banner-80x200', 'Roll up banner premium ukuran 80x200 cm.', 'Roll up banner dengan casing aluminium, praktis digulung dan disimpan. Cocok untuk pameran dan event indoor.', 'uploads/products/product-1765287948-6269.png', 450000.00, 10000, 1, 1, '2025-12-09 12:40:39', '2025-12-14 16:17:15', NULL),
(9, 4, 'Kartu Nama Art Carton 260gr', 'kartu-nama-art-carton-260', 'Kartu nama profesional, cetak 2 sisi.', 'Kartu nama dicetak di art carton 260gr dengan laminasi doff/glossy opsional. Desain bisa dari klien atau dibuatkan tim desain.', 'uploads/products/product-1765287960-8906.png', 75000.00, 1000, 0, 1, '2025-12-09 12:40:39', '2025-12-14 16:18:13', NULL),
(10, 5, 'Totebag Custom Sablon 1 Warna', 'totebag-custom-sablon-1-warna', 'Totebag bahan canvas dengan sablon 1 warna.', 'Totebag custom untuk merchandise event, seminar, atau brand activation. Bahan canvas tebal, area sablon maksimal A4.', 'uploads/products/20251214_130543_f8f1ba64.jpg', 35000.00, 0, 0, 1, '2025-12-09 12:40:39', '2025-12-14 12:05:43', NULL),
(11, 4, 'Poster Band KuburanBand sejati', 'poster-band-kuburanband', 'poster', 'poster ini punya kuburan band', 'uploads/products/product-1765287874-7979.png', 1500000.00, 1999, 0, 1, '2025-12-09 13:11:05', '2025-12-14 11:25:35', NULL),
(18, 11, 'Kertas Ajaib', 'kertas-ajaib', 'kertas', 'kertas', 'uploads/products/20251214_123558_075b7153.jpg', 5000.00, 1000, 0, 1, '2025-12-14 11:35:58', '2025-12-14 11:36:16', NULL),
(19, 2, 'stiker oasis', 'stiker-oasis', 'stiker band', 'stiker ini asli dari official oasis', 'uploads/products/20251214_163737_7249dde7.png', 15000.00, 100, 1, 1, '2025-12-14 15:37:37', '2025-12-14 15:37:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'spanduk', 'spanduk', 'spanduk bagus', 3, 1, '2025-12-09 12:20:36', '2025-12-09 12:20:36'),
(2, 'stiker', 'stiker', 'stiker bagus', 7, 1, '2025-12-09 12:34:47', '2025-12-14 11:35:02'),
(3, 'Spanduk & Banner', 'spanduk-banner', 'Produk spanduk dan banner untuk indoor & outdoor.', 1, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(4, 'Poster & Flyer', 'poster-flyer', 'Poster, flyer, brosur untuk promosi event dan brand.', 2, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(5, 'X-Banner & Roll Up', 'x-banner-rollup', 'Media display portabel untuk pameran dan promosi.', 6, 1, '2025-12-09 12:40:00', '2025-12-14 11:34:56'),
(6, 'Kartu Nama & Stationery', 'kartu-nama-stationery', 'Kartu nama, kop surat, amplop, dan stationery brand.', 4, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(7, 'Merchandise Cetak', 'merchandise-cetak', 'Mug, totebag, t-shirt dan merchandise custom.', 5, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(11, 'Kertas Printer', 'kertas-printer', NULL, 8, 1, '2025-12-14 11:35:23', '2025-12-14 11:35:23');

-- --------------------------------------------------------

--
-- Table structure for table `product_discounts`
--

CREATE TABLE `product_discounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `discount_type` enum('percent','fixed') NOT NULL DEFAULT 'percent',
  `discount_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `qty_total` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `qty_used` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `start_at` datetime DEFAULT NULL,
  `end_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_discounts`
--

INSERT INTO `product_discounts` (`id`, `product_id`, `discount_type`, `discount_value`, `qty_total`, `qty_used`, `start_at`, `end_at`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 3, 'percent', 0.50, 10, 0, NULL, '2025-12-16 23:00:00', 1, 1, '2025-12-13 12:30:55', '2025-12-13 12:30:55'),
(2, 11, 'percent', 20.00, 999, 0, NULL, NULL, 1, 1, '2025-12-14 11:33:36', '2025-12-14 11:33:36'),
(3, 4, 'fixed', 9999.00, 500, 0, NULL, NULL, 1, 1, '2025-12-14 11:34:08', '2025-12-14 11:44:45'),
(4, 18, 'percent', 1.00, 10, 0, '2025-12-14 18:36:00', '2025-12-16 18:36:00', 1, 1, '2025-12-14 11:36:58', '2025-12-14 11:36:58'),
(5, 5, 'percent', 15.00, 50, 0, NULL, NULL, 1, 1, '2025-12-14 12:54:53', '2025-12-14 12:54:53'),
(6, 19, 'percent', 15.00, 5, 0, NULL, NULL, 1, 1, '2025-12-14 15:38:14', '2025-12-14 15:38:14'),
(7, 8, 'percent', 99.00, 100, 0, NULL, NULL, 1, 1, '2025-12-14 16:17:40', '2025-12-14 16:17:40'),
(8, 9, 'fixed', 10000.00, 100, 0, NULL, NULL, 1, 1, '2025-12-14 16:18:30', '2025-12-14 16:18:30');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_option_groups`
--

CREATE TABLE `product_option_groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `input_type` enum('select','radio','checkbox') NOT NULL DEFAULT 'checkbox',
  `min_select` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `max_select` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_option_groups`
--

INSERT INTO `product_option_groups` (`id`, `product_id`, `name`, `input_type`, `min_select`, `max_select`, `is_required`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'Ukuran', 'select', 1, 1, 1, 10, 1, '2025-12-13 20:57:42', '2025-12-14 13:32:24'),
(2, 3, 'Bahan', 'select', 1, 1, 1, 20, 1, '2025-12-13 20:57:42', '2025-12-14 13:32:24'),
(3, 3, 'Finishing', 'checkbox', 0, 0, 0, 30, 1, '2025-12-13 20:57:42', '2025-12-14 13:32:24'),
(4, 11, 'A4', 'checkbox', 100, 0, 1, 1, 1, '2025-12-14 11:28:22', '2025-12-14 11:28:22');

-- --------------------------------------------------------

--
-- Table structure for table `product_option_values`
--

CREATE TABLE `product_option_values` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `label` varchar(150) NOT NULL,
  `price_type` enum('fixed','percent') NOT NULL DEFAULT 'fixed',
  `price_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_option_values`
--

INSERT INTO `product_option_values` (`id`, `group_id`, `label`, `price_type`, `price_value`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'A4', 'fixed', 0.00, 10, 1, '2025-12-13 20:57:42', '2025-12-13 20:57:42'),
(2, 1, 'A3', 'fixed', 5000.00, 20, 1, '2025-12-13 20:57:42', '2025-12-13 20:57:42'),
(3, 1, 'A2', 'fixed', 15000.00, 30, 1, '2025-12-13 20:57:42', '2025-12-13 20:57:42'),
(4, 2, 'Flexi 280gsm', 'fixed', 0.00, 10, 1, '2025-12-13 20:57:42', '2025-12-13 20:57:42'),
(5, 2, 'Flexi 340gsm', 'fixed', 3000.00, 20, 1, '2025-12-13 20:57:42', '2025-12-13 20:57:42'),
(6, 3, 'Laminasi Doff', 'fixed', 10000.00, 10, 1, '2025-12-13 20:57:42', '2025-12-13 20:57:42'),
(7, 3, 'Laminasi Glossy', 'fixed', 10000.00, 20, 1, '2025-12-13 20:57:42', '2025-12-13 20:57:42'),
(8, 3, 'Spot UV', 'fixed', 15000.00, 30, 1, '2025-12-13 20:57:42', '2025-12-13 20:57:42');

-- --------------------------------------------------------

--
-- Table structure for table `product_quantity_prices`
--

CREATE TABLE `product_quantity_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `min_qty` int(10) UNSIGNED NOT NULL,
  `max_qty` int(10) UNSIGNED DEFAULT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_name` varchar(150) NOT NULL,
  `size` varchar(100) DEFAULT NULL,
  `material` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_order` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `site_name` varchar(150) NOT NULL,
  `site_tagline` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `site_tagline`, `logo`, `phone`, `email`, `address`, `facebook`, `instagram`, `whatsapp`, `created_at`, `updated_at`) VALUES
(1, 'Event Print', 'web asik bikin nagih', 'uploads/settings/logo_logo_perpus_1765715138.png', '0878-****-****', 'peaceinfinite@gmail.com', 'Peace Infinite', 'infinite@peace', 'peace', 'infinite', '2025-12-11 19:06:58', '2025-12-14 16:25:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Super admin', 'superadmin1@example.com', '$2y$10$K3JWVttfrq3fyscjXsEtve8MtcZEucZC.qwzxjKZclTlB6nbkWUA.', 'super_admin', 1, '2025-12-15 19:34:15', '2025-12-08 10:35:28', '2025-12-15 12:34:15'),
(2, 'admin', 'admin@example.com', '$2y$10$6hm8GoEmVw6T5fGTOztvQeZQP/xmQ5gCasLPF7IKwr6pCr1a1DTXm', 'admin', 1, '2025-12-14 19:52:47', '2025-12-09 14:39:30', '2025-12-14 12:52:47'),
(4, 'Steve', 'steve@gmail.com', '70a3a8848b092a658537b9b0ea4e9d5fbc8c5b24', 'super_admin', 1, NULL, '2025-12-14 16:26:18', '2025-12-14 16:28:31'),
(5, 'Super Test', 'supertest@gmail.com', '$2y$10$K3JWVttfrq3fyscjXsEtve8MtcZEucZC.qwzxjKZclTlB6nbkWUA.', 'super_admin', 1, NULL, '2025-12-14 16:35:19', '2025-12-14 16:35:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_home_active_pos` (`page_slug`,`is_active`,`position`);

--
-- Indexes for table `our_store`
--
ALTER TABLE `our_store`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `page_contents`
--
ALTER TABLE `page_contents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_page_section_item_field` (`page_slug`,`section`,`item_key`,`field`),
  ADD KEY `idx_page_section_item` (`page_slug`,`section`,`item_key`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_products_category` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `product_discounts`
--
ALTER TABLE `product_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pd_product` (`product_id`),
  ADD KEY `idx_pd_active_time` (`is_active`,`start_at`,`end_at`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_images_product` (`product_id`);

--
-- Indexes for table `product_option_groups`
--
ALTER TABLE `product_option_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pog_product_name` (`product_id`,`name`),
  ADD KEY `idx_pog_product_sort` (`product_id`,`sort_order`,`id`),
  ADD KEY `idx_pog_active` (`product_id`,`is_active`);

--
-- Indexes for table `product_option_values`
--
ALTER TABLE `product_option_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pov_group_sort` (`group_id`,`sort_order`,`id`),
  ADD KEY `idx_pov_active` (`group_id`,`is_active`);

--
-- Indexes for table `product_quantity_prices`
--
ALTER TABLE `product_quantity_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pqp_product` (`product_id`,`is_active`,`min_qty`,`max_qty`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_variants_product` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `our_store`
--
ALTER TABLE `our_store`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `page_contents`
--
ALTER TABLE `page_contents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_discounts`
--
ALTER TABLE `product_discounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_option_groups`
--
ALTER TABLE `product_option_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_option_values`
--
ALTER TABLE `product_option_values`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_quantity_prices`
--
ALTER TABLE `product_quantity_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_discounts`
--
ALTER TABLE `product_discounts`
  ADD CONSTRAINT `fk_pd_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_option_groups`
--
ALTER TABLE `product_option_groups`
  ADD CONSTRAINT `fk_pog_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_option_values`
--
ALTER TABLE `product_option_values`
  ADD CONSTRAINT `fk_pov_group` FOREIGN KEY (`group_id`) REFERENCES `product_option_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_quantity_prices`
--
ALTER TABLE `product_quantity_prices`
  ADD CONSTRAINT `fk_pqp_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_product_variants_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
