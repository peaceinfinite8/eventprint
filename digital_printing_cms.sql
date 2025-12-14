-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 08:06 AM
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
-- Database: `digital_printing_cms`
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
(1, 'Asrul', 'asrul@example.com', '787465254', 'banner', 'saya ingin memesan banner dengan ukuran 5km x 5km', 1, '2025-12-10 10:58:57');

-- --------------------------------------------------------

--
-- Table structure for table `our_home`
--

CREATE TABLE `our_home` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `client_name` varchar(150) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `project_date` date DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `our_store`
--

CREATE TABLE `our_store` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `client_name` varchar(150) DEFAULT NULL,
  `category` varchar(150) DEFAULT NULL,
  `project_date` date DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `our_store`
--

INSERT INTO `our_store` (`id`, `title`, `slug`, `client_name`, `category`, `project_date`, `short_description`, `description`, `thumbnail`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Graha Printing 71', 'graha-printing-71', 'PrimaGraphia Graha Printing', 'Jakarta Pusat', '2020-01-01', 'Layanan digital printing lengkap untuk kebutuhan promosi dan branding perusahaan.', 'Find us: Jl. Kepu Selatan No. 71 Kemayoran - Jakarta Pusat.\r\nCall us: (+62) 21 - 4288 9999.\r\nWhatsApp: 0811-1185-771.\r\nEmail: grahaorder@gmail.co.id.\r\nWorking hours: Senin s/d Jumat 08.00–00.00, Sabtu–Minggu 09.00–19.00, hari libur 09.00–19.00.', 'uploads/portfolio/20251211_161521_9879f2e3.png', 1, 1, '2025-12-10 14:15:18', '2025-12-11 15:15:21'),
(2, 'PrimaGraphia Express', 'primagraphia-express', 'PrimaGraphia Express', 'Jakarta Pusat', '2020-02-15', 'Cabang ekspres untuk kebutuhan cetak cepat dengan kualitas tinggi.', 'Find us: Jl. Kali Baru Timur III No. 22 C Senen - Jakarta Pusat.\r\nCall us: (+62) 21 426 0533.\r\nWhatsApp: 0811-8385-773.\r\nEmail: pgxpress@primagraphia.co.id.\r\nWorking hours: Senin–Jumat 09.00–19.00, Sabtu 09.00–18.00, Minggu & hari merah: Libur.', 'uploads/portfolio/20251211_161543_57083c68.png', 1, 2, '2025-12-10 14:15:18', '2025-12-11 15:15:43'),
(3, 'PrimaGraphia KPS 66', 'primagraphia-kps-66', 'PrimaGraphia KPS 66', 'Jakarta Pusat', '2021-05-10', 'Spesialisasi cetak DTF, sablon, dan kebutuhan apparel.', 'Find us: Jl. Kepu Selatan No. 66 Senen - Jakarta Pusat.\r\nCall us: (+62) 21 424 4114.\r\nWhatsApp: 0877-7231-0250.\r\nEmail: order@primagraphia.co.id.\r\nWorking hours: Senin–Jumat 09.00–18.00, Sabtu 08.00–18.00, Minggu & hari merah: Libur.', 'uploads/portfolio/20251211_161554_16e52783.png', 1, 3, '2025-12-10 14:15:18', '2025-12-11 15:15:54'),
(4, 'EventPrint Workshop BSD', 'eventprint-workshop-bsd', 'EventPrint BSD', 'Tangerang Selatan', '2022-03-01', 'Workshop utama untuk produksi backdrop besar, booth event, dan signage.', 'Alamat: Ruko Golden Boulevard, BSD City, Tangerang Selatan.\r\nTelepon: (021) 555 1234.\r\nWhatsApp: 0812-9000-1234.\r\nEmail: bsd@eventprint.co.id.\r\nJam operasional: Senin–Sabtu 09.00–18.00.', 'uploads/portfolio/store-1765449878-0457e58b.png', 1, 4, '2025-12-10 14:15:18', '2025-12-11 10:44:38'),
(5, 'EventPrint Production Center', 'eventprint-production-center', 'EventPrint Production', 'Jakarta Barat', '2021-08-20', 'Fokus pada produksi massal banner, spanduk, dan materi promosi offline.', 'Alamat: Jl. Raya Daan Mogot KM 14, Jakarta Barat.\r\nTelepon: (021) 567 8910.\r\nWhatsApp: 0813-8000-5678.\r\nEmail: production@eventprint.co.id.\r\nJam operasional: Senin–Sabtu 08.00–20.00.', 'uploads/portfolio/store-1765449836-2daa3b3f.png', 1, 5, '2025-12-10 14:15:18', '2025-12-11 10:43:56'),
(6, 'EventPrint Online Fulfillment', 'eventprint-online-fulfillment', 'EventPrint Online', 'Jakarta Selatan', '2023-01-10', 'Cabang khusus pemrosesan order online dan pengiriman ke seluruh Indonesia.', 'Alamat: Jl. TB Simatupang No. 10, Jakarta Selatan.\r\nTelepon: (021) 7788 9900.\r\nWhatsApp: 0819-9000-7788.\r\nEmail: online@eventprint.co.id.\r\nJam operasional: Senin–Jumat 09.00–17.00.', 'uploads/portfolio/store-1765449851-476b9020.png', 1, 6, '2025-12-10 14:15:18', '2025-12-11 10:44:11'),
(9, 'kjascbkasjcblL', 'kjascbkasjcbll', 'kjbcuibviwue', 'Event', '2025-12-06', 'kascbiubciaub', 'ilubcilusdbclaubdc', 'uploads/portfolio/20251211_193739_4977d934.png', 1, 1, '2025-12-11 17:41:48', '2025-12-11 18:38:29');

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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_contents`
--

INSERT INTO `page_contents` (`id`, `page_slug`, `section`, `field`, `value`, `created_at`, `updated_at`) VALUES
(1, 'home', 'hero', 'title', 'Halaman Princess', '2025-12-10 10:25:58', '2025-12-11 15:19:54'),
(2, 'home', 'hero', 'subtitle', 'Halaman ini Dari Zaman Mediavel', '2025-12-10 10:25:58', '2025-12-11 15:19:54'),
(3, 'home', 'hero', 'button_text', 'Mediaval', '2025-12-10 10:25:58', '2025-12-11 15:19:54'),
(4, 'home', 'hero', 'button_link', '', '2025-12-10 10:25:58', '2025-12-11 15:19:54');

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
(3, 'akljsdh', 'akljsdh', 'aksjdbka', 'jkawudgviauysvcx', 'uploads/blog/post-1765452888-6540242b.png', 1, '2025-12-11 12:34:48', '2025-12-11 11:34:48', '2025-12-11 11:34:48', NULL),
(5, 'as,dm ekwbd', 'asdm-ekwbd', 'loskjcbiesufhi', 'oewihfciowbwLIsjb', 'uploads/blog/20251211_144418_f9f37bd0.jpg', 1, '2025-12-11 15:29:55', '2025-12-11 13:44:18', '2025-12-11 14:29:55', NULL),
(6, 'alsk dldek', 'alsk-dldek', 'pweofmm', 'pofmwpeof', 'uploads/blog/20251211_151301_8bae99e9.png', 1, '2025-12-11 15:29:46', '2025-12-11 14:13:01', '2025-12-11 14:29:46', NULL),
(7, 'artikel tes via postman', 'artikel-tes-via-postman', 'ini konten dummy', 'ini konten dummy', '', 1, '2025-12-11 17:14:12', '2025-12-11 16:14:12', '2025-12-11 16:14:12', NULL),
(8, 'as,jdbk', 'asjdbk', 'kugefiwugbki', 'uglwiuegfwkejbfilweufcbiwu', 'uploads/blog/post-1765476814-9dbba900.png', 0, NULL, '2025-12-11 18:13:34', '2025-12-11 18:35:24', NULL),
(9, 'wlewle', 'wlewle', 'masih', 'konten', '', 0, NULL, '2025-12-12 10:55:10', '2025-12-12 10:55:10', NULL);

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
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `thumbnail`, `base_price`, `is_featured`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'spanduk', 'spanduk', 'spanduk ajaib', 'spanduk gokil', 'uploads/products/product-1765287997-8700.png', 12000.00, 0, 1, '2025-12-09 12:33:37', '2025-12-09 13:46:37', NULL),
(2, 2, 'stiker', 'stiker ajaib', 'stiker tahan banting', 'stiker anti robek', 'uploads/products/product-1765287985-4511.png', 100000.00, 1, 1, '2025-12-09 12:35:57', '2025-12-09 13:46:25', NULL),
(3, 1, 'Spanduk Outdoor 4x1 Meter', 'spanduk-outdoor-4x1', 'Spanduk outdoor bahan flexi 280gr ukuran 4x1 meter.', 'Spanduk outdoor cocok untuk promosi di jalan raya, depan toko, atau area event. Dicetak full color menggunakan mesin outdoor dengan kualitas tahan cuaca.', 'uploads/products/product-1765287885-4046.png', 250000.00, 0, 1, '2025-12-09 12:40:39', '2025-12-09 13:44:45', NULL),
(4, 1, 'Banner Indoor Standing', 'banner-indoor-standing', 'Banner indoor dengan tiang standing, cocok untuk area lobby atau event.', 'Banner indoor bahan albatros dengan kualitas cetak halus, dipasang pada tiang standing yang mudah dibongkar-pasang.', 'uploads/products/product-1765287895-9858.png', 200000.00, 1, 1, '2025-12-09 12:40:39', '2025-12-09 13:44:55', NULL),
(5, 2, 'Poster A3 Full Color', 'poster-a3-full-color', 'Poster ukuran A3, cetak 1 sisi full color.', 'Poster A3 cocok untuk promo di dalam ruangan, informasi event, menu restoran, dan lain-lain. Dicetak di kertas art paper 150gr.', 'uploads/products/product-1765287913-7000.png', 15000.00, 0, 1, '2025-12-09 12:40:39', '2025-12-09 13:45:13', NULL),
(6, 2, 'Flyer A5 2 Sisi', 'flyer-a5-2-sisi', 'Flyer promosi ukuran A5, cetak 2 sisi.', 'Flyer A5 2 sisi untuk kampanye promosi skala besar. Dicetak di kertas art paper 120gr, minimal order 100 lembar.', 'uploads/products/product-1765287922-6177.png', 300000.00, 0, 1, '2025-12-09 12:40:39', '2025-12-09 13:45:22', NULL),
(7, 3, 'X-Banner 60x160 cm', 'x-banner-60x160', 'Paket X-Banner ukuran 60x160 cm lengkap dengan rangka.', 'X-Banner dengan bahan flexi 280gr dan rangka X yang kokoh. Praktis untuk dibawa dan dipasang di berbagai titik promosi.', 'uploads/products/product-1765287937-3503.png', 175000.00, 1, 1, '2025-12-09 12:40:39', '2025-12-09 13:45:37', NULL),
(8, 3, 'Roll Up Banner 80x200 cm', 'roll-up-banner-80x200', 'Roll up banner premium ukuran 80x200 cm.', 'Roll up banner dengan casing aluminium, praktis digulung dan disimpan. Cocok untuk pameran dan event indoor.', 'uploads/products/product-1765287948-6269.png', 450000.00, 1, 1, '2025-12-09 12:40:39', '2025-12-09 13:45:48', NULL),
(9, 4, 'Kartu Nama Art Carton 260gr', 'kartu-nama-art-carton-260', 'Kartu nama profesional, cetak 2 sisi.', 'Kartu nama dicetak di art carton 260gr dengan laminasi doff/glossy opsional. Desain bisa dari klien atau dibuatkan tim desain.', 'uploads/products/product-1765287960-8906.png', 75000.00, 0, 1, '2025-12-09 12:40:39', '2025-12-09 13:46:00', NULL),
(10, 5, 'Totebag Custom Sablon 1 Warna', 'totebag-custom-sablon-1-warna', 'Totebag bahan canvas dengan sablon 1 warna.', 'Totebag custom untuk merchandise event, seminar, atau brand activation. Bahan canvas tebal, area sablon maksimal A4.', 'uploads/products/product-1765287973-6883.png', 35000.00, 0, 1, '2025-12-09 12:40:39', '2025-12-09 13:46:13', NULL),
(11, 4, 'Poster Band KuburanBand', 'poster-band-kuburanband', 'poster', 'poster ini punya kuburan band', 'uploads/products/product-1765287874-7979.png', 1500000.00, 0, 1, '2025-12-09 13:11:05', '2025-12-11 15:17:09', NULL),
(14, 4, 'dakjnn', 'dakjnn', 'qkwdn', 'oqiwdkn', 'uploads/products/20251211_145809_803b0aa7.jpg', 999999999.00, 1, 1, '2025-12-11 13:56:28', '2025-12-11 13:58:09', NULL),
(15, 6, 'g', 'g', 'gg', 'ggg', 'uploads/products/20251211_150542_36d2d4fc.png', 9999999.00, 0, 1, '2025-12-11 14:04:48', '2025-12-11 14:06:35', '2025-12-11 21:06:35'),
(16, 7, 'd', 'd', 'k', 'jwef ', 'uploads/products/20251211_150526_a171c9a5.png', 9999999.00, 0, 1, '2025-12-11 14:05:26', '2025-12-11 14:05:50', '2025-12-11 21:05:50'),
(17, 4, 'uyfouyfouhjvyvuy', 'uyfouyfouhjvyvuy', 'oiuweftiuwb', 'iu7gwefcuygbw', 'uploads/products/20251211_183531_f4819a0c.png', 9999999999.99, 1, 1, '2025-12-11 17:35:31', '2025-12-11 17:35:31', NULL);

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
(2, 'stiker', 'stiker', 'stiker bagus', 10, 1, '2025-12-09 12:34:47', '2025-12-09 12:34:47'),
(3, 'Spanduk & Banner', 'spanduk-banner', 'Produk spanduk dan banner untuk indoor & outdoor.', 1, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(4, 'Poster & Flyer', 'poster-flyer', 'Poster, flyer, brosur untuk promosi event dan brand.', 2, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(5, 'X-Banner & Roll Up', 'x-banner-rollup', 'Media display portabel untuk pameran dan promosi.', 3, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(6, 'Kartu Nama & Stationery', 'kartu-nama-stationery', 'Kartu nama, kop surat, amplop, dan stationery brand.', 4, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(7, 'Merchandise Cetak', 'merchandise-cetak', 'Mug, totebag, t-shirt dan merchandise custom.', 5, 1, '2025-12-09 12:40:00', '2025-12-09 12:40:00'),
(9, 'dgsdgs', 'adsgvvrfs', 'cfsrgesrs', 11, 1, '2025-12-11 17:45:28', '2025-12-11 17:45:28'),
(10, 'as,jb ckwjb olqwidho', 'bwieufgiq-whfbkjwefb-i', 'iuqgfiuwebfiuwebfck', 12, 1, '2025-12-11 18:17:37', '2025-12-11 18:17:37');

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
(1, 'website kamboja', 'web asik bikin nagih', 'uploads/settings/logo_WhatsApp_Image_2025-12-12_at_02_17_48_63a3b86a_1765480709.jpg', '918649857', 'kambojagokin@example.com', 'kamboja barat daya', 'kambojaim', 'kaJdniuqn', 'kudnciwuedbn', '2025-12-11 19:06:58', '2025-12-11 19:18:29');

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
(1, 'Super admin', 'superadmin1@example.com', '$2y$10$K3JWVttfrq3fyscjXsEtve8MtcZEucZC.qwzxjKZclTlB6nbkWUA.', 'super_admin', 1, '2025-12-12 14:12:42', '2025-12-08 10:35:28', '2025-12-12 07:12:42'),
(2, 'admin', 'admin@example.com', '$2y$10$6hm8GoEmVw6T5fGTOztvQeZQP/xmQ5gCasLPF7IKwr6pCr1a1DTXm', 'admin', 1, '2025-12-12 18:12:07', '2025-12-09 14:39:30', '2025-12-12 11:12:07'),
(3, 'admin1', 'admin1@example.com', '$2y$10$H/LjKQdB/e2Uo/RxbxKBf.omeIDNHCLUD/2xr51YVbG3cEM0tOrV6', 'admin', 0, '2025-12-12 13:50:22', '2025-12-12 06:37:13', '2025-12-12 06:54:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `our_home`
--
ALTER TABLE `our_home`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `our_home_slug_unique` (`slug`);

--
-- Indexes for table `our_store`
--
ALTER TABLE `our_store`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `our_store_slug_unique` (`slug`);

--
-- Indexes for table `page_contents`
--
ALTER TABLE `page_contents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_page_section_field` (`page_slug`,`section`,`field`);

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
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_images_product` (`product_id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `our_home`
--
ALTER TABLE `our_home`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `our_store`
--
ALTER TABLE `our_store`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `page_contents`
--
ALTER TABLE `page_contents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_product_variants_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
