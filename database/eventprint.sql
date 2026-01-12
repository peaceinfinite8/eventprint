-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 08:04 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ep_guard_db` ()   BEGIN
  IF DATABASE() <> 'eventprint' THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'ABORTED: Please select database `eventprint` first.';
  END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `pc_upsert` (IN `p_page` VARCHAR(100), IN `p_section` VARCHAR(100), IN `p_field` VARCHAR(100), IN `p_item_key` VARCHAR(50), IN `p_value` TEXT)   BEGIN
  DECLARE v_id INT;

  SELECT id INTO v_id
  FROM page_contents
  WHERE page_slug = p_page
    AND section   = p_section
    AND field     = p_field
    AND (item_key <=> p_item_key)   -- NULL-safe equality
  ORDER BY id ASC
  LIMIT 1;

  IF v_id IS NULL THEN
    INSERT INTO page_contents (page_slug, section, field, item_key, value)
    VALUES (p_page, p_section, p_field, p_item_key, p_value);
  ELSE
    UPDATE page_contents
    SET value = p_value, updated_at = CURRENT_TIMESTAMP()
    WHERE id = v_id;
  END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `level` enum('info','warning','error') NOT NULL,
  `source` enum('api','admin','system') NOT NULL,
  `message` varchar(255) NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `level`, `source`, `message`, `context`, `created_at`) VALUES
(1, 'info', 'system', 'System Logs feature initialized', '{\"version\": \"1.0\", \"admin\": \"superadmin\"}', '2025-12-21 13:06:09'),
(2, 'info', 'admin', 'TEST_ACTION: Testing from verification script', '{\"test_id\":123,\"user_id\":999,\"username\":\"test_verifier\"}', '2025-12-22 12:23:27'),
(3, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 280gsm (Dummy)', '{\"entity\":\"product\",\"id\":94,\"name\":\"Spanduk Flexi 280gsm (Dummy)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 12:24:25'),
(4, 'info', 'admin', 'Update Store: Updated store ID 6', '{\"id\":6,\"name\":\"kantor cabang1\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 12:49:05'),
(5, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 12:52:50'),
(6, 'info', 'admin', 'UPDATE: Mengubah produk: Amplop Custom (Dummy)', '{\"entity\":\"product\",\"id\":107,\"name\":\"Amplop Custom (Dummy)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:08:12'),
(7, 'info', 'admin', 'Create Tier Price: Created tier for Product #107: 10-15', '{\"id\":1,\"product_id\":107,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:10:13'),
(8, 'info', 'admin', 'Create Tier Price: Created tier for Product #107: 10-15', '{\"id\":2,\"product_id\":107,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:13:16'),
(9, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 107', '{\"id\":\"107\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:16:31'),
(10, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 107', '{\"id\":\"107\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:20:39'),
(11, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 1', '{\"id\":\"1\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:24:05'),
(12, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 2', '{\"id\":\"2\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:24:26'),
(13, 'info', 'admin', 'Create Tier Price: Created tier for Product #107: 10-20', '{\"id\":3,\"product_id\":107,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:25:03'),
(14, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 13:31:05'),
(15, 'info', 'system', 'User logged out: Super admin', '{\"user_id\":1}', '2025-12-22 13:57:10'),
(16, 'info', 'system', 'User logged in: admin', '{\"user_id\":2,\"email\":\"admin@example.com\"}', '2025-12-22 13:57:15'),
(17, 'info', 'admin', 'CREATE: Menambah artikel: testing', '{\"entity\":\"post\",\"title\":\"testing\",\"user_id\":2,\"username\":\"unknown\"}', '2025-12-22 13:58:20'),
(18, 'info', 'system', 'User logged out: admin', '{\"user_id\":2}', '2025-12-22 13:59:49'),
(19, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-22 13:59:55'),
(20, 'info', 'admin', 'UPDATE: Mengubah produk: Poster Band KuburanBand', '{\"entity\":\"product\",\"id\":144,\"name\":\"Poster Band KuburanBand\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 14:04:32'),
(21, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-22 14:51:10'),
(22, 'info', 'admin', 'Update Our Home Content: Updated Our Home page content headers', '{\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 14:58:14'),
(23, 'info', 'admin', 'Update Our Home Content: Updated Our Home page content headers', '{\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 15:11:02'),
(24, 'info', 'admin', 'Update Our Home Content: Updated Our Home page content headers', '{\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 15:11:10'),
(25, 'info', 'admin', 'Update Why Choose: Updated Why Choose Us section content', '{\"title\":\"Mengapa memilih kami\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 15:15:20'),
(26, 'info', 'admin', 'Update Why Choose: Updated Why Choose Us section content', '{\"title\":\"Mengapa memilih kami\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 15:15:46'),
(27, 'info', 'admin', 'Update Home Map: Updated homepage category mapping', '{\"print_id\":39,\"media_id\":41,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 15:20:26'),
(28, 'info', 'admin', 'DELETE: Menghapus produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":1,\"name\":\"Kartu Nama Standard\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 15:56:03'),
(29, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:02:11'),
(30, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Premium', '{\"entity\":\"product\",\"id\":23,\"name\":\"Kartu Nama Premium\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:02:22'),
(31, 'info', 'admin', 'UPDATE: Mengubah produk: Flyer A5', '{\"entity\":\"product\",\"id\":24,\"name\":\"Flyer A5\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:02:31'),
(32, 'info', 'admin', 'UPDATE: Mengubah produk: Brosur Lipat 2 (A4 jadi A5)', '{\"entity\":\"product\",\"id\":25,\"name\":\"Brosur Lipat 2 (A4 jadi A5)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:02:41'),
(33, 'info', 'admin', 'UPDATE: Mengubah produk: Sticker Label A3 (Kiss Cut)', '{\"entity\":\"product\",\"id\":26,\"name\":\"Sticker Label A3 (Kiss Cut)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:02:58'),
(34, 'info', 'admin', 'UPDATE: Mengubah produk: Sticker Vinyl Outdoor', '{\"entity\":\"product\",\"id\":27,\"name\":\"Sticker Vinyl Outdoor\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:03:11'),
(35, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 280gsm', '{\"entity\":\"product\",\"id\":28,\"name\":\"Spanduk Flexi 280gsm\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:03:20'),
(36, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 340gsm', '{\"entity\":\"product\",\"id\":29,\"name\":\"Spanduk Flexi 340gsm\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:03:30'),
(37, 'info', 'admin', 'UPDATE: Mengubah produk: Undangan Pernikahan Standard', '{\"entity\":\"product\",\"id\":30,\"name\":\"Undangan Pernikahan Standard\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:03:37'),
(38, 'info', 'admin', 'UPDATE: Mengubah produk: Box Packaging Small', '{\"entity\":\"product\",\"id\":31,\"name\":\"Box Packaging Small\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:03:45'),
(39, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:04:35'),
(40, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Premium', '{\"entity\":\"product\",\"id\":23,\"name\":\"Kartu Nama Premium\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:04:47'),
(41, 'info', 'admin', 'UPDATE: Mengubah produk: Flyer A5', '{\"entity\":\"product\",\"id\":24,\"name\":\"Flyer A5\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:05:04'),
(42, 'info', 'admin', 'UPDATE: Mengubah produk: Brosur Lipat 2 (A4 jadi A5)', '{\"entity\":\"product\",\"id\":25,\"name\":\"Brosur Lipat 2 (A4 jadi A5)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:05:15'),
(43, 'info', 'admin', 'UPDATE: Mengubah produk: Sticker Label A3 (Kiss Cut)', '{\"entity\":\"product\",\"id\":26,\"name\":\"Sticker Label A3 (Kiss Cut)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:05:26'),
(44, 'info', 'admin', 'UPDATE: Mengubah produk: Sticker Vinyl Outdoor', '{\"entity\":\"product\",\"id\":27,\"name\":\"Sticker Vinyl Outdoor\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:05:38'),
(45, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 280gsm', '{\"entity\":\"product\",\"id\":28,\"name\":\"Spanduk Flexi 280gsm\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:05:51'),
(46, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 340gsm', '{\"entity\":\"product\",\"id\":29,\"name\":\"Spanduk Flexi 340gsm\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:06:01'),
(47, 'info', 'admin', 'UPDATE: Mengubah produk: Undangan Pernikahan Standard', '{\"entity\":\"product\",\"id\":30,\"name\":\"Undangan Pernikahan Standard\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:06:12'),
(48, 'info', 'admin', 'UPDATE: Mengubah produk: Box Packaging Small', '{\"entity\":\"product\",\"id\":31,\"name\":\"Box Packaging Small\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:06:23'),
(49, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 1-100', '{\"id\":4,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:14:51'),
(50, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 101-200', '{\"id\":5,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:15:11'),
(51, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 201-500', '{\"id\":6,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:15:31'),
(52, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 501-1000', '{\"id\":7,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:15:46'),
(53, 'info', 'admin', 'Create Tier Price: Created tier for Product #23: 1-100', '{\"id\":8,\"product_id\":23,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:16:08'),
(54, 'info', 'admin', 'Create Tier Price: Created tier for Product #23: 101-200', '{\"id\":9,\"product_id\":23,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:16:25'),
(55, 'info', 'admin', 'Create Tier Price: Created tier for Product #23: 201-500', '{\"id\":10,\"product_id\":23,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:16:55'),
(56, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 8', '{\"id\":\"8\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:16:59'),
(57, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 9', '{\"id\":\"9\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:17:06'),
(58, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 10', '{\"id\":\"10\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:17:08'),
(59, 'info', 'admin', 'Create Tier Price: Created tier for Product #23: 1-99', '{\"id\":11,\"product_id\":23,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:17:19'),
(60, 'info', 'admin', 'Create Tier Price: Created tier for Product #23: 100-199', '{\"id\":12,\"product_id\":23,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:17:34'),
(61, 'info', 'admin', 'Create Tier Price: Created tier for Product #23: 200-499', '{\"id\":13,\"product_id\":23,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:17:59'),
(62, 'info', 'admin', 'Create Tier Price: Created tier for Product #23: 500-1000', '{\"id\":14,\"product_id\":23,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:18:13'),
(63, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 4', '{\"id\":\"4\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:18:28'),
(64, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 1-99', '{\"id\":15,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:18:37'),
(65, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 5', '{\"id\":\"5\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:18:45'),
(66, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 100-199', '{\"id\":16,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:18:54'),
(67, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 6', '{\"id\":\"6\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:18:59'),
(68, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 200-499', '{\"id\":17,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:19:10'),
(69, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 7', '{\"id\":\"7\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:19:12'),
(70, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 500-1000', '{\"id\":18,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:19:21'),
(71, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 1-40', '{\"id\":19,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:20:44'),
(72, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 19', '{\"id\":\"19\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:20:52'),
(73, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 1-49', '{\"id\":20,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:21:02'),
(74, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 50-99', '{\"id\":21,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:22:16'),
(75, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 20', '{\"id\":\"20\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:23:16'),
(76, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 21', '{\"id\":\"21\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:23:18'),
(77, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 50-99', '{\"id\":22,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:23:40'),
(78, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 100-199', '{\"id\":23,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:24:01'),
(79, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 200-249', '{\"id\":24,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:24:30'),
(80, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 250-500', '{\"id\":25,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:24:48'),
(81, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 15', '{\"id\":\"15\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:25:02'),
(82, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 16', '{\"id\":\"16\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:25:05'),
(83, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 17', '{\"id\":\"17\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:25:08'),
(84, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 18', '{\"id\":\"18\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:25:12'),
(85, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 100-199', '{\"id\":26,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:25:31'),
(86, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 200-499', '{\"id\":27,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:25:50'),
(87, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 26', '{\"id\":\"26\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:26:54'),
(88, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 27', '{\"id\":\"27\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:26:57'),
(89, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 1-99', '{\"id\":28,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:27:30'),
(90, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 100-199', '{\"id\":29,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:27:45'),
(91, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 200-499', '{\"id\":30,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:28:08'),
(92, 'info', 'admin', 'Create Tier Price: Created tier for Product #22: 500-1000', '{\"id\":31,\"product_id\":22,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:28:26'),
(93, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:28:29'),
(94, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 25', '{\"id\":\"25\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:29:57'),
(95, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 24', '{\"id\":\"24\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:29:59'),
(96, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 23', '{\"id\":\"23\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:30:02'),
(97, 'info', 'admin', 'Delete Tier Price: Deleted tier ID 22', '{\"id\":\"22\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:30:27'),
(98, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 1-49', '{\"id\":32,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:30:38'),
(99, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 50-99', '{\"id\":33,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:30:51'),
(100, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 100-249', '{\"id\":34,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:31:09'),
(101, 'info', 'admin', 'Create Tier Price: Created tier for Product #24: 250-500', '{\"id\":35,\"product_id\":24,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:31:28'),
(102, 'info', 'admin', 'Create Tier Price: Created tier for Product #25: 1-49', '{\"id\":36,\"product_id\":25,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:33:02'),
(103, 'info', 'admin', 'Create Tier Price: Created tier for Product #25: 50-99', '{\"id\":37,\"product_id\":25,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:33:20'),
(104, 'info', 'admin', 'Create Tier Price: Created tier for Product #25: 100-249', '{\"id\":38,\"product_id\":25,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:33:39'),
(105, 'info', 'admin', 'Create Tier Price: Created tier for Product #25: 250-1000', '{\"id\":39,\"product_id\":25,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:33:57'),
(106, 'info', 'admin', 'Create Tier Price: Created tier for Product #26: 1-1', '{\"id\":40,\"product_id\":26,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:35:45'),
(107, 'info', 'admin', 'Create Tier Price: Created tier for Product #26: 2-9', '{\"id\":41,\"product_id\":26,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:36:07'),
(108, 'info', 'admin', 'Create Tier Price: Created tier for Product #26: 10-24', '{\"id\":42,\"product_id\":26,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:36:34'),
(109, 'info', 'admin', 'Create Tier Price: Created tier for Product #26: 25-50', '{\"id\":43,\"product_id\":26,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:40:28'),
(110, 'info', 'admin', 'UPDATE: Mengubah produk: Sticker Label A3 (Kiss Cut)', '{\"entity\":\"product\",\"id\":26,\"name\":\"Sticker Label A3 (Kiss Cut)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:40:55'),
(111, 'info', 'admin', 'Create Tier Price: Created tier for Product #30: 1-49', '{\"id\":44,\"product_id\":30,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:41:24'),
(112, 'info', 'admin', 'Create Tier Price: Created tier for Product #30: 50-99', '{\"id\":45,\"product_id\":30,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:41:41'),
(113, 'info', 'admin', 'Create Tier Price: Created tier for Product #30: 100-199', '{\"id\":46,\"product_id\":30,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:42:04'),
(114, 'info', 'admin', 'Create Tier Price: Created tier for Product #30: 200-1000', '{\"id\":47,\"product_id\":30,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:42:28'),
(115, 'info', 'admin', 'UPDATE: Mengubah produk: Box Packaging Small', '{\"entity\":\"product\",\"id\":31,\"name\":\"Box Packaging Small\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:43:15'),
(116, 'info', 'admin', 'UPDATE: Mengubah produk: Box Packaging Small', '{\"entity\":\"product\",\"id\":31,\"name\":\"Box Packaging Small\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 16:43:59'),
(117, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-22 17:17:52'),
(118, 'info', 'admin', 'CREATE: Menambah produk: Debug Product Test', '{\"entity\":\"product\",\"name\":\"Debug Product Test\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 17:23:01'),
(119, 'info', 'admin', 'UPDATE: Mengubah produk: Debug Product Test Edited', '{\"entity\":\"product\",\"id\":32,\"name\":\"Debug Product Test Edited\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-22 17:24:13'),
(120, 'info', 'admin', 'DELETE: Menghapus produk: Debug Product Test Edited', '{\"entity\":\"product\",\"id\":32,\"name\":\"Debug Product Test Edited\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:52:45'),
(121, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:53:17'),
(122, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Premium', '{\"entity\":\"product\",\"id\":23,\"name\":\"Kartu Nama Premium\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:53:46'),
(123, 'info', 'admin', 'UPDATE: Mengubah produk: Flyer A5', '{\"entity\":\"product\",\"id\":24,\"name\":\"Flyer A5\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:54:12'),
(124, 'info', 'admin', 'UPDATE: Mengubah produk: Flyer A5', '{\"entity\":\"product\",\"id\":24,\"name\":\"Flyer A5\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:54:22'),
(125, 'info', 'admin', 'UPDATE: Mengubah produk: Brosur Lipat 2 (A4 jadi A5)', '{\"entity\":\"product\",\"id\":25,\"name\":\"Brosur Lipat 2 (A4 jadi A5)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:55:10'),
(126, 'info', 'admin', 'UPDATE: Mengubah produk: Sticker Label A3 (Kiss Cut)', '{\"entity\":\"product\",\"id\":26,\"name\":\"Sticker Label A3 (Kiss Cut)\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:55:48'),
(127, 'info', 'admin', 'UPDATE: Mengubah produk: Sticker Vinyl Outdoor', '{\"entity\":\"product\",\"id\":27,\"name\":\"Sticker Vinyl Outdoor\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:56:25'),
(128, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 280gsm', '{\"entity\":\"product\",\"id\":28,\"name\":\"Spanduk Flexi 280gsm\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:57:13'),
(129, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 340gsm', '{\"entity\":\"product\",\"id\":29,\"name\":\"Spanduk Flexi 340gsm\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:57:51'),
(130, 'info', 'admin', 'UPDATE: Mengubah produk: Undangan Pernikahan Standard', '{\"entity\":\"product\",\"id\":30,\"name\":\"Undangan Pernikahan Standard\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:58:26'),
(131, 'info', 'admin', 'UPDATE: Mengubah produk: Box Packaging Small', '{\"entity\":\"product\",\"id\":31,\"name\":\"Box Packaging Small\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 08:59:48'),
(132, 'info', 'admin', 'CREATE: Menambah produk: Deleted Test Product', '{\"entity\":\"product\",\"name\":\"Deleted Test Product\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:10:53'),
(133, 'info', 'admin', 'DELETE: Menghapus produk: Deleted Test Product', '{\"entity\":\"product\",\"id\":33,\"name\":\"Deleted Test Product\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:12:53'),
(134, 'info', 'admin', 'Update Why Choose: Updated Why Choose Us section content', '{\"title\":\"Mengapa memilih kami?\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:17:14'),
(135, 'info', 'admin', 'Delete Small Banner: Deleted small banner ID 9', '{\"id\":9,\"title\":\"hello\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:17:55'),
(136, 'info', 'admin', 'Create Small Banner: Created new small banner \'lihat ini!!\'', '{\"id\":10,\"title\":\"lihat ini!!\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:18:46'),
(137, 'info', 'admin', 'Create Small Banner: Created new small banner \'lihat ini\'', '{\"id\":11,\"title\":\"lihat ini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:19:20'),
(138, 'info', 'admin', 'Update Small Banner: Updated small banner ID 11', '{\"id\":11,\"title\":\"lihat ini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:19:36'),
(139, 'info', 'admin', 'Update Small Banner: Updated small banner ID 11', '{\"id\":11,\"title\":\"lihat ini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:19:59'),
(140, 'info', 'admin', 'Delete Small Banner: Deleted small banner ID 10', '{\"id\":10,\"title\":\"lihat ini!!\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:20:18'),
(141, 'info', 'admin', 'Create Small Banner: Created new small banner \'lihat ini\'', '{\"id\":12,\"title\":\"lihat ini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:20:45'),
(142, 'info', 'admin', 'Update Small Banner: Updated small banner ID 12', '{\"id\":12,\"title\":\"lihat ini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:21:11'),
(143, 'info', 'admin', 'Update Hero Slide: Updated hero slide ID 6', '{\"id\":6,\"title\":\"Cetak Online Cepat & Rapi\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:22:34'),
(144, 'info', 'admin', 'Update Hero Slide: Updated hero slide ID 6', '{\"id\":6,\"title\":\"Cetak Online Cepat & Rapi\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:24:08'),
(145, 'info', 'admin', 'Update Hero Slide: Updated hero slide ID 7', '{\"id\":7,\"title\":\"Harga Kompetitif untuk UMKM\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:28:01'),
(146, 'info', 'admin', 'Update Hero Slide: Updated hero slide ID 8', '{\"id\":8,\"title\":\"Kualitas Warna Tajam\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:28:45'),
(147, 'info', 'admin', 'Update Small Banner: Updated small banner ID 11', '{\"id\":11,\"title\":\"lihat ini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:42:16'),
(148, 'info', 'admin', 'Delete Small Banner: Deleted small banner ID 12', '{\"id\":12,\"title\":\"lihat ini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:42:34'),
(149, 'info', 'admin', 'Delete Small Banner: Deleted small banner ID 11', '{\"id\":11,\"title\":\"lihat ini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:42:37'),
(150, 'info', 'admin', 'Create Small Banner: Created new small banner \'ini mesin kami\'', '{\"id\":13,\"title\":\"ini mesin kami\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:43:11'),
(151, 'info', 'admin', 'Create Small Banner: Created new small banner \'2\'', '{\"id\":14,\"title\":\"2\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:43:35'),
(152, 'info', 'admin', 'Delete Small Banner: Deleted small banner ID 14', '{\"id\":14,\"title\":\"2\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:43:38'),
(153, 'info', 'admin', 'Create Small Banner: Created new small banner \'2\'', '{\"id\":15,\"title\":\"2\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:43:53'),
(154, 'info', 'admin', 'Create Small Banner: Created new small banner \'3\'', '{\"id\":16,\"title\":\"3\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:44:06'),
(155, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:45:28'),
(156, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:47:40'),
(157, 'info', 'admin', 'UPDATE: Updated footer settings', '{\"page\":\"footer\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 09:58:39'),
(158, 'info', 'admin', 'UPDATE: Updated footer settings', '{\"page\":\"footer\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 10:10:13'),
(159, 'info', 'admin', 'UPDATE: Updated footer settings', '{\"page\":\"footer\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 10:11:00'),
(160, 'info', 'admin', 'Update Store: Updated store ID 5', '{\"id\":5,\"name\":\"EventPrint Depok\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 10:12:33'),
(161, 'info', 'admin', 'CREATE: Menambah artikel: Berita terkini', '{\"entity\":\"post\",\"title\":\"Berita terkini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 10:42:50'),
(162, 'info', 'admin', 'UPDATE: Mengubah artikel: Berita terkini', '{\"entity\":\"post\",\"id\":12,\"title\":\"Berita terkini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 10:49:17'),
(163, 'info', 'admin', 'UPDATE: Mengubah artikel: Berita terkini', '{\"entity\":\"post\",\"id\":12,\"title\":\"Berita terkini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 10:49:23'),
(164, 'info', 'admin', 'UPDATE: Mengubah artikel: artikel tes via postman', '{\"entity\":\"post\",\"id\":7,\"title\":\"artikel tes via postman\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 10:54:33'),
(165, 'info', 'admin', 'UPDATE: Mengubah artikel: portfilio kami', '{\"entity\":\"post\",\"id\":10,\"title\":\"portfilio kami\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 10:55:43'),
(166, 'info', 'admin', 'DELETE: Menghapus artikel: Projek 5M', '{\"entity\":\"post\",\"id\":1,\"title\":\"Projek 5M\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:06:09'),
(167, 'info', 'admin', 'DELETE: Menghapus artikel: testing', '{\"entity\":\"post\",\"id\":11,\"title\":\"testing\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:06:13'),
(168, 'info', 'admin', 'DELETE: Menghapus artikel: artikel tes via postman', '{\"entity\":\"post\",\"id\":7,\"title\":\"artikel tes via postman\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:06:17'),
(169, 'info', 'admin', 'DELETE: Menghapus artikel: Projek Sawit', '{\"entity\":\"post\",\"id\":2,\"title\":\"Projek Sawit\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:06:20'),
(170, 'info', 'admin', 'UPDATE: Mengubah artikel: Berita terkini', '{\"entity\":\"post\",\"id\":12,\"title\":\"Berita terkini\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:07:03'),
(171, 'info', 'admin', 'UPDATE: Mengubah produk: Flyer A5', '{\"entity\":\"product\",\"id\":24,\"name\":\"Flyer A5\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:10:19'),
(172, 'info', 'admin', 'CREATE: Menambah group opsi \'jumlah dan ukuran\'', '{\"entity\":\"option_group\",\"product_id\":24,\"name\":\"jumlah dan ukuran\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:10:50'),
(173, 'info', 'admin', 'CREATE: Menambah nilai opsi \'100\' pada group ID 7', '{\"entity\":\"option_value\",\"group_id\":7,\"label\":\"100\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:11:10'),
(174, 'info', 'admin', 'DELETE: Menghapus group opsi \'jumlah dan ukuran\'', '{\"entity\":\"option_group\",\"id\":7,\"name\":\"jumlah dan ukuran\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:11:54'),
(175, 'info', 'admin', 'CREATE: Menambah group opsi \'Ukuran\'', '{\"entity\":\"option_group\",\"product_id\":24,\"name\":\"Ukuran\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:12:04'),
(176, 'info', 'admin', 'CREATE: Menambah group opsi \'Jumlah\'', '{\"entity\":\"option_group\",\"product_id\":24,\"name\":\"Jumlah\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:12:16'),
(177, 'info', 'admin', 'CREATE: Menambah nilai opsi \'50\' pada group ID 9', '{\"entity\":\"option_value\",\"group_id\":9,\"label\":\"50\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:13:23'),
(178, 'info', 'admin', 'CREATE: Menambah nilai opsi \'50\' pada group ID 8', '{\"entity\":\"option_value\",\"group_id\":8,\"label\":\"50\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:13:41'),
(179, 'info', 'admin', 'UPDATE: Mengubah nilai opsi \'50\' menjadi \'50\'', '{\"entity\":\"option_value\",\"id\":12,\"old_label\":\"50\",\"new_label\":\"50\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:14:04'),
(180, 'info', 'admin', 'UPDATE: Mengubah artikel: portfolio kami', '{\"entity\":\"post\",\"id\":10,\"title\":\"portfolio kami\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:15:03'),
(181, 'info', 'admin', 'UPDATE: Mengubah group opsi \'Ukuran\' menjadi \'Ukuran\'', '{\"entity\":\"option_group\",\"id\":8,\"old_name\":\"Ukuran\",\"new_name\":\"Ukuran\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:29:05'),
(182, 'info', 'admin', 'UPDATE: Mengubah group opsi \'Jumlah\' menjadi \'Jumlah\'', '{\"entity\":\"option_group\",\"id\":9,\"old_name\":\"Jumlah\",\"new_name\":\"Jumlah\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:29:10'),
(183, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-23 11:46:32'),
(184, 'info', 'system', 'User logged in: admin', '{\"user_id\":2,\"email\":\"admin@example.com\"}', '2025-12-23 11:47:37'),
(185, 'info', 'system', 'User logged out: admin', '{\"user_id\":2}', '2025-12-23 11:47:50'),
(186, 'info', 'admin', 'DELETE: Menghapus user: Super Test', '{\"entity\":\"user\",\"id\":5,\"name\":\"Super Test\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:48:33'),
(187, 'info', 'admin', 'DELETE: Menghapus user: Steve', '{\"entity\":\"user\",\"id\":4,\"name\":\"Steve\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:48:35'),
(188, 'info', 'admin', 'CREATE: Menambah user: ucup (uucp@example.com)', '{\"entity\":\"user\",\"name\":\"ucup\",\"email\":\"uucp@example.com\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:55:50'),
(189, 'info', 'system', 'User logged in: admin', '{\"user_id\":2,\"email\":\"admin@example.com\"}', '2025-12-23 11:55:59'),
(190, 'info', 'system', 'User logged out: admin', '{\"user_id\":2}', '2025-12-23 11:56:04'),
(191, 'info', 'admin', 'UPDATE: Mengubah user: ucup (uucp@example.com)', '{\"entity\":\"user\",\"id\":6,\"name\":\"ucup\",\"email\":\"uucp@example.com\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:56:42'),
(192, 'info', 'admin', 'UPDATE: Mengubah user: ucup (ucup@example.com)', '{\"entity\":\"user\",\"id\":6,\"name\":\"ucup\",\"email\":\"ucup@example.com\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:56:51'),
(193, 'info', 'system', 'User logged in: ucup', '{\"user_id\":6,\"email\":\"ucup@example.com\"}', '2025-12-23 11:57:00'),
(194, 'info', 'system', 'User logged out: ucup', '{\"user_id\":6}', '2025-12-23 11:57:06'),
(195, 'info', 'admin', 'UPDATE: Mengubah user: ucup (ucup@example.com)', '{\"entity\":\"user\",\"id\":6,\"name\":\"ucup\",\"email\":\"ucup@example.com\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 11:57:40'),
(196, 'info', 'system', 'User logged in: ucup', '{\"user_id\":6,\"email\":\"ucup@example.com\"}', '2025-12-23 11:57:48'),
(197, 'info', 'system', 'User logged out: ucup', '{\"user_id\":6}', '2025-12-23 11:57:55'),
(198, 'info', 'admin', 'UPDATE: Mengubah produk: Flyer A5', '{\"entity\":\"product\",\"id\":24,\"name\":\"Flyer A5\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 12:36:41'),
(199, 'info', 'admin', 'UPDATE: Mengubah kategori: Kartu Nama', '{\"entity\":\"category\",\"id\":37,\"name\":\"Kartu Nama\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 13:00:52'),
(200, 'info', 'admin', 'UPDATE: Mengubah kategori: Kartu Nama', '{\"entity\":\"category\",\"id\":37,\"name\":\"Kartu Nama\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 13:02:31'),
(201, 'info', 'admin', 'UPDATE: Mengubah kategori: Kartu Nama', '{\"entity\":\"category\",\"id\":37,\"name\":\"Kartu Nama\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 13:02:49'),
(202, 'info', 'admin', 'UPDATE: Mengubah kategori: Kartu Nama', '{\"entity\":\"category\",\"id\":37,\"name\":\"Kartu Nama\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 13:03:40'),
(203, 'info', 'admin', 'UPDATE: Mengubah kategori: Kartu Nama', '{\"entity\":\"category\",\"id\":37,\"name\":\"Kartu Nama\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 13:04:17'),
(204, 'info', 'system', 'User logged in: admin', '{\"user_id\":2,\"email\":\"admin@example.com\"}', '2025-12-23 16:17:17'),
(205, 'info', 'admin', 'CREATE: Menambah artikel: show preview', '{\"entity\":\"post\",\"title\":\"show preview\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-23 16:20:14'),
(206, 'info', 'system', 'User logged out: admin', '{\"user_id\":2}', '2025-12-23 16:42:13'),
(207, 'info', 'system', 'User logged in: ucup', '{\"user_id\":6,\"email\":\"ucup@example.com\"}', '2025-12-23 16:42:23'),
(208, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 16:44:19'),
(209, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 16:44:39'),
(210, 'info', 'admin', 'Update Home Content: Updated homepage content settings', '{\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 16:49:26'),
(211, 'info', 'admin', 'Update Home Content: Updated homepage content settings', '{\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 16:49:54'),
(212, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 16:50:05'),
(213, 'info', 'admin', 'Update Home Content: Updated homepage content settings', '{\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 16:54:28'),
(214, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 17:08:43'),
(215, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 17:19:57'),
(216, 'info', 'admin', 'Update Home Map: Updated homepage category mapping', '{\"print_id\":39,\"media_id\":42,\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 17:24:51'),
(217, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 17:33:23'),
(218, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 17:34:54'),
(219, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 17:35:39'),
(220, 'info', 'admin', 'UPDATE: Updated footer settings', '{\"page\":\"footer\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 17:39:27'),
(221, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 17:48:00'),
(222, 'info', 'admin', 'UPDATE: Updated footer settings', '{\"page\":\"footer\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 18:01:53'),
(223, 'info', 'admin', 'Delete Store: Deleted store ID 6', '{\"id\":6,\"name\":\"kantor cabang1\",\"user_id\":1,\"username\":\"unknown\"}', '2025-12-23 19:53:17'),
(224, 'info', 'system', 'User logged out: Super admin', '{\"user_id\":1}', '2025-12-23 20:02:07'),
(225, 'info', 'system', 'User logged out: ucup', '{\"user_id\":6}', '2025-12-23 20:05:34'),
(226, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-23 20:08:48'),
(227, 'info', 'system', 'User logged in: admin', '{\"user_id\":2,\"email\":\"admin@example.com\"}', '2025-12-23 20:27:24'),
(228, 'info', 'system', 'User logged out: Super admin', '{\"user_id\":1}', '2025-12-23 20:41:52'),
(229, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-23 20:42:04'),
(230, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-23 22:33:22'),
(231, 'info', 'system', 'User logged in: ucup', '{\"user_id\":6,\"email\":\"ucup@example.com\"}', '2025-12-24 03:30:46'),
(232, 'info', 'system', 'User logged out: ucup', '{\"user_id\":6}', '2025-12-24 03:33:20'),
(233, 'info', 'system', 'User logged in: ucup', '{\"user_id\":6,\"email\":\"ucup@example.com\"}', '2025-12-24 03:38:31'),
(234, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 04:01:35'),
(235, 'info', 'admin', 'View Message: Viewed and auto-marked message #6 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 04:42:54'),
(236, 'info', 'admin', 'View Message: Viewed and auto-marked message #5 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 04:46:15'),
(237, 'info', 'admin', 'Toggle Message Status: Marked message #5 as unread', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 04:46:20'),
(238, 'info', 'admin', 'View Message: Viewed and auto-marked message #5 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 04:46:20'),
(239, 'info', 'admin', 'View Message: Viewed and auto-marked message #7 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 04:46:23'),
(240, 'info', 'system', 'User logged out: ucup', '{\"user_id\":6}', '2025-12-24 04:49:17'),
(241, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 340gsm', '{\"entity\":\"product\",\"id\":29,\"name\":\"Spanduk Flexi 340gsm\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 04:51:10'),
(242, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-24 05:11:11'),
(243, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-24 05:20:24'),
(244, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:37:09'),
(245, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:39:46'),
(246, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:40:50'),
(247, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:41:35'),
(248, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 280gsm', '{\"entity\":\"product\",\"id\":28,\"name\":\"Spanduk Flexi 280gsm\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:50:10'),
(249, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 280gsm', '{\"entity\":\"product\",\"id\":28,\"name\":\"Spanduk Flexi 280gsm\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:50:56'),
(250, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 280gsm', '{\"entity\":\"product\",\"id\":28,\"name\":\"Spanduk Flexi 280gsm\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:51:31'),
(251, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:54:31'),
(252, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:58:04'),
(253, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 05:59:34'),
(254, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 06:04:21'),
(255, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 06:06:07'),
(256, 'info', 'admin', 'UPDATE: Mengubah produk: Spanduk Flexi 340gsm', '{\"entity\":\"product\",\"id\":29,\"name\":\"Spanduk Flexi 340gsm\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 06:09:36'),
(257, 'info', 'admin', 'UPDATE: Mengubah produk: Kartu Nama Standard', '{\"entity\":\"product\",\"id\":22,\"name\":\"Kartu Nama Standard\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 06:14:06'),
(258, 'info', 'admin', 'Update Home Map: Updated homepage category mapping', '{\"print_id\":39,\"media_id\":42,\"merch_id\":49,\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 06:31:01'),
(259, 'info', 'admin', 'CREATE: Menambah produk: Gantungan Kunci', '{\"entity\":\"product\",\"name\":\"Gantungan Kunci\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 06:43:26'),
(260, 'info', 'system', 'User logged out: Super admin', '{\"user_id\":1}', '2025-12-24 07:24:55'),
(261, 'info', 'system', 'User logged in: ucup', '{\"user_id\":6,\"email\":\"ucup@example.com\"}', '2025-12-24 07:25:04'),
(262, 'info', 'system', 'User logged out: ucup', '{\"user_id\":6}', '2025-12-24 07:25:49'),
(263, 'info', 'system', 'User logged in: Super admin', '{\"user_id\":1,\"email\":\"superadmin1@example.com\"}', '2025-12-24 07:26:00'),
(264, 'info', 'admin', 'Create Hero Slide: Created new hero slide \'judul\'', '{\"id\":17,\"title\":\"judul\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 08:45:20'),
(265, 'info', 'admin', 'Delete Hero Slide: Deleted hero slide ID 17', '{\"id\":17,\"title\":\"judul\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 08:45:41'),
(266, 'info', 'admin', 'DELETE: Menghapus produk: Gantungan Kunci Akrilik', '{\"entity\":\"product\",\"id\":40,\"name\":\"Gantungan Kunci Akrilik\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 13:56:39'),
(267, 'info', 'admin', 'DELETE: Menghapus produk: Lanyard Custom', '{\"entity\":\"product\",\"id\":38,\"name\":\"Lanyard Custom\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 13:57:22'),
(268, 'info', 'admin', 'UPDATE: Mengubah produk: Gantungan Kunci', '{\"entity\":\"product\",\"id\":44,\"name\":\"Gantungan Kunci\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:21:53'),
(269, 'info', 'admin', 'UPDATE: Mengubah produk: Gantungan Kunci', '{\"entity\":\"product\",\"id\":44,\"name\":\"Gantungan Kunci\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:22:13'),
(270, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:26:29'),
(271, 'info', 'admin', 'UPDATE: Memperbarui pengaturan umum website', '{\"entity\":\"settings\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:28:15'),
(272, 'info', 'admin', 'View Message: Viewed and auto-marked message #8 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:29:05'),
(273, 'info', 'admin', 'Toggle Message Status: Marked message #8 as unread', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:29:08'),
(274, 'info', 'admin', 'View Message: Viewed and auto-marked message #8 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:29:09'),
(275, 'info', 'admin', 'View Message: Viewed and auto-marked message #9 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:29:14'),
(276, 'info', 'admin', 'UPDATE: Mengubah produk: Gantungan Kunci', '{\"entity\":\"product\",\"id\":44,\"name\":\"Gantungan Kunci\",\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 14:32:00'),
(277, 'info', 'admin', 'View Message: Viewed and auto-marked message #13 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 15:27:45'),
(278, 'info', 'admin', 'View Message: Viewed and auto-marked message #12 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 15:27:48'),
(279, 'info', 'admin', 'View Message: Viewed and auto-marked message #11 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 15:27:50'),
(280, 'info', 'admin', 'View Message: Viewed and auto-marked message #10 as read', '{\"user_id\":null,\"username\":\"unknown\"}', '2025-12-24 15:27:51');

-- --------------------------------------------------------

--
-- Table structure for table `category_laminations`
--

CREATE TABLE `category_laminations` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to product_categories.id',
  `lamination_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to laminations.id',
  `price_delta_override` decimal(10,2) DEFAULT NULL COMMENT 'Category-specific price override (NULL = use master)',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Maps which laminations are available for each product category';

--
-- Dumping data for table `category_laminations`
--

INSERT INTO `category_laminations` (`id`, `category_id`, `lamination_id`, `price_delta_override`, `is_active`, `created_at`) VALUES
(55, 37, 6, NULL, 1, '2025-12-22 16:10:27'),
(56, 37, 7, NULL, 1, '2025-12-22 16:10:27'),
(57, 37, 8, NULL, 1, '2025-12-22 16:10:27'),
(58, 37, 9, NULL, 1, '2025-12-22 16:10:27'),
(59, 38, 7, NULL, 1, '2025-12-22 16:11:01'),
(60, 38, 8, NULL, 1, '2025-12-22 16:11:01'),
(61, 38, 9, NULL, 1, '2025-12-22 16:11:01'),
(62, 39, 8, NULL, 1, '2025-12-22 16:11:15'),
(63, 41, 6, NULL, 1, '2025-12-22 16:12:01'),
(64, 41, 7, NULL, 1, '2025-12-22 16:12:01'),
(65, 41, 8, NULL, 1, '2025-12-22 16:12:01'),
(66, 41, 9, NULL, 1, '2025-12-22 16:12:01'),
(67, 42, 6, NULL, 1, '2025-12-22 16:12:31'),
(68, 42, 7, NULL, 1, '2025-12-22 16:12:31'),
(69, 42, 8, NULL, 1, '2025-12-22 16:12:31');

-- --------------------------------------------------------

--
-- Table structure for table `category_materials`
--

CREATE TABLE `category_materials` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to product_categories.id',
  `material_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to materials.id',
  `price_delta_override` decimal(10,2) DEFAULT NULL COMMENT 'Category-specific price override (NULL = use master)',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Maps which materials are available for each product category';

--
-- Dumping data for table `category_materials`
--

INSERT INTO `category_materials` (`id`, `category_id`, `material_id`, `price_delta_override`, `is_active`, `created_at`) VALUES
(41, 37, 28, NULL, 1, '2025-12-22 16:10:27'),
(42, 37, 29, NULL, 1, '2025-12-22 16:10:27'),
(43, 38, 25, NULL, 1, '2025-12-22 16:11:01'),
(44, 38, 26, NULL, 1, '2025-12-22 16:11:01'),
(45, 38, 27, NULL, 1, '2025-12-22 16:11:01'),
(46, 39, 30, NULL, 1, '2025-12-22 16:11:15'),
(47, 39, 31, NULL, 1, '2025-12-22 16:11:15'),
(48, 40, 32, NULL, 1, '2025-12-22 16:11:34'),
(49, 40, 33, NULL, 1, '2025-12-22 16:11:34'),
(50, 41, 28, NULL, 1, '2025-12-22 16:12:01'),
(51, 41, 29, NULL, 1, '2025-12-22 16:12:01'),
(52, 42, 29, NULL, 1, '2025-12-22 16:12:31'),
(53, 42, 34, NULL, 1, '2025-12-22 16:12:31');

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
(3, 'lana', 'lana@gmai.com', '123443', 'lana keren', 'Halo lana', 1, '2025-12-14 14:10:04'),
(4, 'Test User', 'test@example.com', '', 'Test Subject', 'This is a test message from verification agent.', 1, '2025-12-19 19:31:16'),
(5, 'Test User', '', '081234567890', '', 'This is a test message to verify the contact form submission.', 1, '2025-12-24 04:41:13'),
(6, 'Test User', '', '081234567890', '', 'This is a test message to verify the contact form submission.', 1, '2025-12-24 04:42:00'),
(7, 'Verification Test', '', '089999999999', '', 'Verification message content.', 1, '2025-12-24 04:43:29'),
(8, 'Flyer A5', '', '287382', '', 'akusdbiuadb', 1, '2025-12-24 07:22:40'),
(9, 'ucup', '', '+62 851-7982-9543', '', 'mass', 1, '2025-12-24 14:27:07'),
(10, 'asd', '', '34235', '', 'ksdfowf', 1, '2025-12-24 15:21:22'),
(11, 'ucup', '', '628123456789', '', 'asdawdaede', 1, '2025-12-24 15:25:43'),
(12, 'sadasd', '', '08812329239', '', 'asdewfwe', 1, '2025-12-24 15:25:59'),
(13, 'Test User\"', '', '081234567890', '', 'est message dari contact form', 1, '2025-12-24 15:27:15');

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
(6, 'home', 'Cetak Online Cepat & Rapi', 'Spanduk, banner, stiker, brosur, kartu nama. Upload desain, pilih bahan, selesai.', 'Cetak Online Terpercaya', 'Order Sekarang', '/contact', 'uploads/hero/hero_20251223_102408_c7fddd11.jpg', 1, 1, '2025-12-18 13:36:23', '2025-12-23 09:24:08'),
(7, 'home', 'Harga Kompetitif untuk UMKM', 'Mulai dari qty kecil sampai partai besar. Konsultasi gratis sebelum produksi.', 'Promo Mingguan', 'Lihat Produk', '/products', 'uploads/hero/hero_20251223_102801_9bf24392.jpg', 2, 1, '2025-12-18 13:36:23', '2025-12-23 09:28:01'),
(8, 'home', 'Kualitas Warna Tajam', 'Finishing lengkap: laminasi, potong, lipat, mata ayam, dan lainnya.', 'Quality First', 'Konsultasi', '/contact', 'uploads/hero/hero_20251223_102845_7d03eb85.png', 3, 1, '2025-12-18 13:36:23', '2025-12-23 09:28:45'),
(13, 'home_small', 'ini mesin kami', '', '', '', '', 'uploads/hero/hero_20251223_104311_c516a464.jpg', 1, 1, '2025-12-23 09:43:11', '2025-12-23 09:43:11'),
(15, 'home_small', '2', '', '', '', '', 'uploads/hero/hero_20251223_104353_23238016.jpg', 1, 1, '2025-12-23 09:43:53', '2025-12-23 09:43:53'),
(16, 'home_small', '3', '', '', '', '', 'uploads/hero/hero_20251223_104406_cfd47232.jpg', 1, 1, '2025-12-23 09:44:06', '2025-12-23 09:44:06');

-- --------------------------------------------------------

--
-- Table structure for table `laminations`
--

CREATE TABLE `laminations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Display name (e.g., "Doff", "Glossy")',
  `slug` varchar(100) NOT NULL COMMENT 'URL-safe identifier',
  `price_delta` decimal(10,2) DEFAULT 0.00 COMMENT 'Price adjustment from base',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Display order (lower = first)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 = active, 0 = hidden',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master list of available laminations (laminasi)';

--
-- Dumping data for table `laminations`
--

INSERT INTO `laminations` (`id`, `name`, `slug`, `price_delta`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(6, 'Tanpa Laminasi', 'tanpa', 0.00, 10, 1, '2025-12-22 15:14:48', '2025-12-22 15:14:48'),
(7, 'Laminasi Doff', 'doff', 2000.00, 20, 1, '2025-12-22 15:14:48', '2025-12-22 15:14:48'),
(8, 'Laminasi Glossy', 'glossy', 2000.00, 30, 1, '2025-12-22 15:14:48', '2025-12-22 15:14:48'),
(9, 'Laminasi Soft Touch', 'soft-touch', 7000.00, 40, 1, '2025-12-22 15:14:48', '2025-12-22 15:14:48');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Display name (e.g., "Art Paper 150gsm")',
  `slug` varchar(100) NOT NULL COMMENT 'URL-safe identifier',
  `price_delta` decimal(10,2) DEFAULT 0.00 COMMENT 'Price adjustment from base',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Display order (lower = first)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 = active, 0 = hidden',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master list of available materials (bahan)';

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `name`, `slug`, `price_delta`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(25, 'HVS 80gsm', 'hvs-80', -2000.00, 10, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(26, 'Art Paper 120gsm', 'art-paper-120', 0.00, 20, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(27, 'Art Paper 150gsm', 'art-paper-150', 1000.00, 30, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(28, 'Art Carton 260gsm', 'art-carton-260', 2000.00, 40, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(29, 'Art Carton 310gsm', 'art-carton-310', 5000.00, 50, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(30, 'Sticker Chromo', 'sticker-chromo', 0.00, 60, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(31, 'Sticker Vinyl (Outdoor)', 'sticker-vinyl', 8000.00, 70, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(32, 'Flexi 280gsm', 'flexi-280', 0.00, 80, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(33, 'Flexi 340gsm', 'flexi-340', 5000.00, 90, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37'),
(34, 'Duplex 350gsm', 'duplex-350', 7000.00, 100, 1, '2025-12-22 15:14:37', '2025-12-22 15:14:37');

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
  `email` varchar(100) DEFAULT NULL,
  `gmaps_url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hours` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Operating hours array' CHECK (json_valid(`hours`)),
  `source` varchar(50) DEFAULT 'manual' COMMENT 'manual or json_seed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `our_store`
--

INSERT INTO `our_store` (`id`, `name`, `slug`, `office_type`, `address`, `city`, `phone`, `whatsapp`, `email`, `gmaps_url`, `thumbnail`, `is_active`, `sort_order`, `created_at`, `updated_at`, `hours`, `source`) VALUES
(5, 'EventPrint Depok', 'eventprint-depok', 'hq', 'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat 16517', 'Depok', '081298984414', '081298984414', 'myorder.eventprint@gmail.com', 'https://maps.app.goo.gl/adxbzqJriqiJ2DR1A', 'uploads/our_store/20251220_104137_6ba85828.jpg', 1, 1, '2025-12-19 22:52:15', '2025-12-23 10:12:33', '[\"Senin ??? Jum\'at : 09.00 ??? 18.00\",\"Sabtu : 08.00 ??? 18.00\",\"Minggu & Tanggal Merah : Libur\"]', 'json_seed');

-- --------------------------------------------------------

--
-- Table structure for table `our_store_gallery`
--

CREATE TABLE `our_store_gallery` (
  `id` int(10) UNSIGNED NOT NULL,
  `store_id` int(10) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `our_store_gallery`
--

INSERT INTO `our_store_gallery` (`id`, `store_id`, `image_path`, `caption`, `sort_order`, `created_at`) VALUES
(6, 5, 'uploads/our_store/20251222_165126_6bbc9946.webp', '', 1, '2025-12-22 15:51:26'),
(7, 5, 'uploads/our_store/20251222_165144_5772351d.jpeg', '', 2, '2025-12-22 15:51:44'),
(8, 5, 'uploads/our_store/20251222_165203_057e60c7.jpeg', '', 3, '2025-12-22 15:52:03'),
(10, 5, 'uploads/our_store/20251222_165256_3b52010d.jpeg', '', 4, '2025-12-22 15:52:56'),
(15, 5, 'uploads/our_store/20251224_094655_c7a2f071.png', '', 1, '2025-12-24 08:46:55');

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
(70, 'home', 'hero', 'image', '', '2025-12-17 14:30:14', '2025-12-17 14:30:14', 'hero_1'),
(72, 'home', 'hero', 'is_active', '1', '2025-12-17 14:30:14', '2025-12-17 14:30:14', 'hero_1'),
(75, 'home', 'home_content', 'home_print_category_id', '39', '2025-12-17 14:51:45', '2025-12-24 06:31:01', NULL),
(76, 'home', 'home_content', 'home_media_category_id', '42', '2025-12-17 14:51:45', '2025-12-24 06:31:01', NULL),
(85, 'home', 'home_content', 'contact_address', 'Jl. BSD Grand Boulevard No.1, Pagedangan, Kec. Pagedangan, Kabupaten Tangerang, Banten 15339\r\nhttps://maps.app.goo.gl/pJth7WJ3frYsqXme6', '2025-12-17 15:07:11', '2025-12-23 16:54:28', NULL),
(86, 'home', 'home_content', 'contact_email', 'google@gmail.com', '2025-12-17 15:07:11', '2025-12-23 16:54:28', NULL),
(87, 'home', 'home_content', 'contact_whatsapp', '8923649258', '2025-12-17 15:07:11', '2025-12-23 16:54:28', NULL),
(88, 'home', 'home_content', 'cta_left_text', 'Baca Artikel!!?', '2025-12-17 15:07:11', '2025-12-17 19:18:20', NULL),
(89, 'home', 'home_content', 'cta_left_link', 'http://localhost/eventprint/public/blog', '2025-12-17 15:07:11', '2025-12-17 19:18:20', NULL),
(90, 'home', 'home_content', 'cta_right_text', 'Kenapa Pilih Kami??!', '2025-12-17 15:07:11', '2025-12-17 19:18:20', NULL),
(91, 'home', 'home_content', 'cta_right_link', 'http://localhost/eventprint/public/our-home', '2025-12-17 15:07:11', '2025-12-17 19:18:20', NULL),
(92, 'home', 'hero', 'headline', 'Solusi Digital Printing untuk Bisnis Kamu', '2025-12-18 13:36:23', '2025-12-18 13:36:23', NULL),
(93, 'home', 'hero', 'subheadline', 'Pesan online, proses cepat, hasil presisi. Siap kirim untuk kebutuhan promosi, event, dan brand.', '2025-12-18 13:36:23', '2025-12-18 13:36:23', NULL),
(94, 'home', 'value_props', 'title', 'Proses Cepat', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_1'),
(95, 'home', 'value_props', 'desc', 'Estimasi pengerjaan jelas. Cocok untuk kebutuhan urgent.', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_1'),
(96, 'home', 'value_props', 'icon', 'zap', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_1'),
(97, 'home', 'value_props', 'title', 'Kualitas Terjaga', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_2'),
(98, 'home', 'value_props', 'desc', 'Bahan dan finishing sesuai standar, warna tajam, hasil rapi.', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_2'),
(99, 'home', 'value_props', 'icon', 'award', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_2'),
(100, 'home', 'value_props', 'title', 'Mudah Custom', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_3'),
(101, 'home', 'value_props', 'desc', 'Bisa tambah varian, ukuran, dan opsi dari CMS admin.', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_3'),
(102, 'home', 'value_props', 'icon', 'sliders', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_3'),
(103, 'home', 'value_props', 'title', 'Support Responsif', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_4'),
(104, 'home', 'value_props', 'desc', 'Tanya dulu sebelum cetak, biar aman dan sesuai kebutuhan.', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_4'),
(105, 'home', 'value_props', 'icon', 'message-circle', '2025-12-18 13:36:23', '2025-12-18 13:36:23', 'vp_4'),
(106, 'home', 'contact_strip', 'whatsapp', '+6281234567890', '2025-12-18 13:36:23', '2025-12-18 13:36:23', NULL),
(107, 'home', 'contact_strip', 'operating_hours', 'Senin - Sabtu, 08:00 - 18:00', '2025-12-18 13:36:23', '2025-12-18 13:36:23', NULL),
(108, 'home', 'contact_strip', 'address', 'Jl. Contoh No. 123, Jakarta', '2025-12-18 13:36:23', '2025-12-18 13:36:23', NULL),
(176, 'home', 'whyChoose', 'image', '../assets/images/whychoose/unnamed.png', '2025-12-19 23:08:04', '2025-12-19 23:08:04', NULL),
(177, 'home', 'whyChoose', 'title', 'WHY CHOOSE EventPrint', '2025-12-19 23:08:04', '2025-12-19 23:08:04', NULL),
(178, 'home', 'whyChoose', 'subtitle', 'Part of Omegakreasindo', '2025-12-19 23:08:04', '2025-12-19 23:08:04', NULL),
(179, 'home', 'whyChoose', 'description', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec finibus tempor elit, vel gravida nunc faucibus nec. Sed enim nisl, molestie vitae ex interdum, iaculis blandit mauris. Cras ac nisl ornare ex tincidunt suscipit eget imperdiet leo. Cras eu lobortis metus, sit amet tincidunt est. Integer id ipsum quis diam scelerisque auctor. Etiam nec magna congue, dignissim leo eget, fringilla ipsum. Nunc malesuada facilisis purus ac pretium. Cras nec auctor massa. Vestibulum eget gravida felis. Etiam in lacus nulla. \"Nge Print Mudah Tanpa Keluar Rumah...\"', '2025-12-19 23:08:04', '2025-12-19 23:08:04', NULL),
(180, 'home', 'infrastructure', 'image', 'https://placehold.co/1200x320/00AEEF/ffffff?text=Mesin+Printing+High+Resolution', '2025-12-19 23:08:04', '2025-12-19 23:08:04', '1'),
(181, 'home', 'infrastructure', 'alt', 'Mesin Printing High Resolution', '2025-12-19 23:08:04', '2025-12-19 23:08:04', '1'),
(182, 'home', 'infrastructure', 'image', 'https://placehold.co/1200x320/0891B2/ffffff?text=Workshop+Produksi+Modern', '2025-12-19 23:08:04', '2025-12-19 23:08:04', '2'),
(183, 'home', 'infrastructure', 'alt', 'Workshop Produksi', '2025-12-19 23:08:04', '2025-12-19 23:08:04', '2'),
(184, 'home', 'infrastructure', 'image', 'https://placehold.co/1200x320/0E7490/ffffff?text=Stok+Material+Lengkap', '2025-12-19 23:08:04', '2025-12-19 23:08:04', '3'),
(185, 'home', 'infrastructure', 'alt', 'Ketersediaan Stok Material', '2025-12-19 23:08:04', '2025-12-19 23:08:04', '3'),
(186, 'home', 'categories', 'id', 'backwall', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'backwall'),
(187, 'home', 'categories', 'label', 'Backwall', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'backwall'),
(188, 'home', 'categories', 'icon', '🖼️', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'backwall'),
(189, 'home', 'categories', 'id', 'event-desk', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'event-desk'),
(190, 'home', 'categories', 'label', 'Event Desk', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'event-desk'),
(191, 'home', 'categories', 'icon', '🪑', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'event-desk'),
(192, 'home', 'categories', 'id', 'pop-up-table', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'pop-up-table'),
(193, 'home', 'categories', 'label', 'Pop Up Table', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'pop-up-table'),
(194, 'home', 'categories', 'icon', '📋', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'pop-up-table'),
(195, 'home', 'categories', 'id', 'roll-up-banner', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'roll-up-banner'),
(196, 'home', 'categories', 'label', 'Roll Up Banner', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'roll-up-banner'),
(197, 'home', 'categories', 'icon', '📜', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'roll-up-banner'),
(198, 'home', 'categories', 'id', 'xy-banner', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'xy-banner'),
(199, 'home', 'categories', 'label', 'X-Y Banner', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'xy-banner'),
(200, 'home', 'categories', 'icon', '✕', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'xy-banner'),
(201, 'home', 'categories', 'id', 'stickers', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'stickers'),
(202, 'home', 'categories', 'label', 'Stickers', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'stickers'),
(203, 'home', 'categories', 'icon', '📌', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'stickers'),
(204, 'home', 'categories', 'id', 'flag-banner', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'flag-banner'),
(205, 'home', 'categories', 'label', 'Flag Banner', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'flag-banner'),
(206, 'home', 'categories', 'icon', '🚩', '2025-12-19 23:08:04', '2025-12-19 23:08:04', 'flag-banner'),
(207, 'home', 'why_choose', 'title', 'Mengapa memilih kami?', '2025-12-22 12:37:03', '2025-12-23 09:17:14', NULL),
(208, 'home', 'why_choose', 'subtitle', '', '2025-12-22 12:37:03', '2025-12-23 09:17:14', NULL),
(209, 'home', 'why_choose', 'description', 'kami adalah sebuah Event Print yang dimana semua product nya dengan harga yanng sangat terjangkau.', '2025-12-22 12:37:03', '2025-12-23 09:17:14', NULL),
(210, 'home', 'why_choose', 'image', 'uploads/hero/hero_20251222_161546_10d17768.png', '2025-12-22 12:37:03', '2025-12-22 15:15:46', NULL),
(211, 'our-home', 'our_home_content', 'page_title', 'Our Store', '2025-12-22 14:58:14', '2025-12-22 15:11:10', NULL),
(212, 'our-home', 'our_home_content', 'gallery_title', 'Galeri Kami', '2025-12-22 14:58:14', '2025-12-22 15:11:10', NULL),
(213, 'our-home', 'our_home_content', 'gallery_subtitle', 'Lihat mesin yang kami gunakan untuk menjaga kualitas & kecepatan produksi', '2025-12-22 14:58:14', '2025-12-22 15:11:10', NULL),
(214, 'footer', 'main', 'copyright', '© 2025 EventPrint. Verified by Subagent.', '2025-12-23 09:58:39', '2025-12-23 18:01:53', '0'),
(215, 'footer', 'main', 'product_links', '[{\"label\":\"Kartu Nama\",\"url\":\"http:\\/\\/localhost\\/eventprint\\/public\\/products?category=kartu-nama\"},{\"label\":\"Brosur & Flyer\",\"url\":\"http:\\/\\/localhost\\/eventprint\\/public\\/products?category=brosur-flyer\"},{\"label\":\"Banner & Spanduk\",\"url\":\"http:\\/\\/localhost\\/eventprint\\/public\\/products?category=banner-spanduk\"},{\"label\":\"Undangan\",\"url\":\"http:\\/\\/localhost\\/eventprint\\/public\\/products?category=undangan\"},{\"label\":\"Packaging\",\"url\":\"http:\\/\\/localhost\\/eventprint\\/public\\/products?category=packaging\"},{\"label\":\"Sticker & Label\",\"url\":\"http:\\/\\/localhost\\/eventprint\\/public\\/products?category=sticker-label\"}]', '2025-12-23 09:58:39', '2025-12-23 18:01:53', '0'),
(216, 'footer', 'main', 'payment_methods', '[{\"label\":\"BCA\",\"image\":\"uploads\\/payment\\/payment_1766511567_223.png\"}]', '2025-12-23 09:58:39', '2025-12-23 18:01:53', '0'),
(217, 'home', 'home_content', 'home_merch_category_id', '49', '2025-12-24 06:31:01', '2025-12-24 06:31:01', NULL);

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
(36, 'home', 'hero', 'banners', '[\r\n    {\r\n      \"title\":\"Harga Kompetitif\",\r\n      \"subtitle\":\"Paket & finishing mudah di-update\",\r\n      \"badge\":\"Cetak Online Terpercaya\",\r\n      \"cta_link\":\"/contact#order\"\r\n    }\r\n  ]', '2025-12-15 12:09:24', '2025-12-15 12:09:24', NULL),
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
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `post_type` varchar(20) DEFAULT 'normal' COMMENT 'large/small/normal',
  `bg_color` varchar(50) DEFAULT NULL COMMENT 'Background color for carousel',
  `post_category` varchar(50) DEFAULT NULL COMMENT 'featured/unggulan/tren',
  `external_url` varchar(255) DEFAULT NULL,
  `link_target` varchar(20) DEFAULT '_self'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `slug`, `excerpt`, `content`, `thumbnail`, `is_published`, `is_featured`, `published_at`, `created_at`, `updated_at`, `deleted_at`, `post_type`, `bg_color`, `post_category`, `external_url`, `link_target`) VALUES
(10, 'portfolio kami', 'portfolio-kami', 'ini adalah portfolio peace infinite', 'ini adalah portfolio peace infinite', 'uploads/blog/post-1766394926-18858ec2.png', 1, 0, '2025-12-22 10:15:26', '2025-12-22 09:15:26', '2025-12-23 11:15:03', NULL, 'normal', NULL, 'unggulan', 'https://peaceinfinite.com/', '_blank'),
(12, 'Berita terkini', 'berita-terkini', 'ini adalah berita hari ini', 'ini adalah berita hari ini', '', 1, 0, '2025-12-23 11:42:50', '2025-12-23 10:42:50', '2025-12-23 11:07:03', NULL, 'large', NULL, 'featured', 'https://news.detik.com/berita/d-8273077/larangan-kembang-api-tahun-baru-di-jakarta-tahun-ini', '_self'),
(13, 'show preview', 'show-preview', 'preview', 'preview', 'uploads/blog/post-1766506814-f7844da3.jpeg', 1, 0, '2025-12-23 17:20:14', '2025-12-23 16:20:14', '2025-12-23 16:20:14', NULL, 'large', 'black', 'unggulan', 'http://peaceinfinite.com', '_blank');

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
  `discount_type` enum('none','percent','fixed') DEFAULT 'none',
  `discount_value` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'IDR',
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `work_time` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Production time array' CHECK (json_valid(`work_time`)),
  `product_notes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Product notes array' CHECK (json_valid(`product_notes`)),
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Specifications array' CHECK (json_valid(`specs`)),
  `upload_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'File upload configuration' CHECK (json_valid(`upload_rules`)),
  `shopee_url` varchar(500) DEFAULT NULL,
  `tokopedia_url` varchar(500) DEFAULT NULL,
  `options_source` enum('category','product','both') DEFAULT 'category' COMMENT 'category=use category options, product=use product-specific only, both=merge category+product'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `thumbnail`, `base_price`, `discount_type`, `discount_value`, `currency`, `stock`, `is_featured`, `is_active`, `created_at`, `updated_at`, `deleted_at`, `work_time`, `product_notes`, `specs`, `upload_rules`, `shopee_url`, `tokopedia_url`, `options_source`) VALUES
(22, 37, 'Kartu Nama Standard', 'kartu-nama-standard', 'Kartu nama tajam untuk kebutuhan bisnis. Pilih bahan & laminasi.', 'Cocok untuk kebutuhan kantor dan usaha. Bisa 1 sisi/2 sisi sesuai desain.', 'uploads/products/20251223_095317_0a4adf0a.jpeg', 25000.00, 'percent', 15.00, 'IDR', 1000, 1, 1, '2025-12-22 15:57:24', '2025-12-24 06:14:06', NULL, '{\"min_days\":1,\"max_days\":2,\"unit\":\"hari\"}', '[\"Harga dasar belum termasuk opsi tertentu.\",\"Teks aman 5mm dari tepi.\",\"Revisi minor sebelum cetak bila diperlukan.\"]', '{\"ukuran\":\"9x5.5 cm\",\"cetak\":\"Full color\",\"sisi\":\"1/2 sisi\",\"warna\":\"CMYK\"}', '{\"bleed_mm\":3,\"safe_margin_mm\":5,\"min_dpi\":300,\"format\":[\"PDF\",\"AI\",\"CDR\",\"PNG\"],\"note\":\"Font di-outline untuk menghindari berubah.\"}', NULL, NULL, 'both'),
(23, 37, 'Kartu Nama Premium', 'kartu-nama-premium', 'Kartu nama tebal + finishing premium untuk kesan eksklusif.', 'Cocok untuk owner/sales. Rekomendasi bahan tebal + laminasi doff/soft touch.', 'uploads/products/20251223_095346_ada987bc.jpeg', 45000.00, 'none', 0.00, 'IDR', 1000, 1, 1, '2025-12-22 15:57:24', '2025-12-23 08:53:46', NULL, '{\"min_days\":2,\"max_days\":3,\"unit\":\"hari\"}', '[\"Rekomendasi: Art Carton 310gsm.\",\"Soft touch memberi feel premium.\",\"Pastikan desain siap cetak (CMYK).\"]', '{\"ukuran\":\"9x5.5 cm\",\"cetak\":\"Full color\",\"sisi\":\"2 sisi\",\"warna\":\"CMYK\"}', '{\"bleed_mm\":3,\"safe_margin_mm\":5,\"min_dpi\":300,\"format\":[\"PDF\",\"AI\",\"CDR\"],\"note\":\"Hindari teks terlalu kecil (<7pt).\"}', NULL, NULL, 'both'),
(24, 38, 'Flyer A5', 'flyer-a5', 'Flyer A5 cepat jadi untuk promosi. Pilih kertas sesuai budget.', 'Cocok untuk promo toko, event, menu, dan selebaran informasi.', 'uploads/products/20251223_095422_5627a380.jpeg', 30000.00, 'none', 0.00, 'IDR', 1000, 1, 1, '2025-12-22 15:57:24', '2025-12-23 12:36:41', NULL, '{\"min_days\":1,\"max_days\":2,\"unit\":\"hari\"}', '[\"Harga dasar untuk paket standar.\",\"Laminasi opsional untuk hasil lebih awet.\",\"Bisa 1 sisi atau 2 sisi sesuai desain.\"]', '{\"ukuran\":\"A5 (14.8x21 cm)\",\"cetak\":\"Full color\",\"sisi\":\"1/2 sisi\",\"warna\":\"CMYK\"}', '{\"bleed_mm\":3,\"safe_margin_mm\":5,\"min_dpi\":300,\"format\":[\"PDF\",\"PNG\",\"JPG\"],\"note\":\"Gunakan gambar tajam, hindari blur.\"}', NULL, NULL, 'category'),
(25, 38, 'Brosur Lipat 2 (A4 jadi A5)', 'brosur-lipat-2-a4-a5', 'Brosur lipat 2 untuk informasi lebih lengkap dan rapi.', 'Cocok untuk profil usaha, menu, katalog mini, dan program event.', 'uploads/products/20251223_095510_8d30f59d.jpeg', 80000.00, 'none', 0.00, 'IDR', 1000, 0, 1, '2025-12-22 15:57:24', '2025-12-23 08:55:10', NULL, '{\"min_days\":2,\"max_days\":3,\"unit\":\"hari\"}', '[\"Perhatikan area lipatan agar tidak memotong teks.\",\"Konsisten margin tiap panel.\",\"Proofread sebelum cetak.\"]', '{\"ukuran\":\"A4 dilipat 2 (jadi A5)\",\"cetak\":\"Full color\",\"sisi\":\"2 sisi\",\"warna\":\"CMYK\",\"finishing\":\"Lipat 2\"}', '{\"bleed_mm\":3,\"safe_margin_mm\":5,\"min_dpi\":300,\"format\":[\"PDF\",\"AI\",\"CDR\"],\"note\":\"Tandai garis lipat di desain.\"}', NULL, NULL, 'category'),
(26, 39, 'Sticker Label A3 (Kiss Cut)', 'sticker-label-a3-kisscut', 'Sticker label A3 kiss cut untuk packaging dan branding.', 'Bisa banyak desain dalam 1 lembar A3. Cocok untuk label produk.', 'uploads/products/20251223_095548_c3d7a97f.jpeg', 65000.00, 'none', 0.00, 'IDR', 100, 1, 1, '2025-12-22 15:57:24', '2025-12-23 08:55:48', NULL, '{\"min_days\":2,\"max_days\":4,\"unit\":\"hari\"}', '[\"Untuk bentuk custom, sertakan cutline.\",\"Jarak antar desain disarankan 2-3mm.\",\"Vinyl cocok untuk tahan air.\"]', '{\"ukuran\":\"A3\",\"cetak\":\"Full color\",\"tipe\":\"Kiss cut\",\"warna\":\"CMYK\"}', '{\"bleed_mm\":2,\"safe_margin_mm\":2,\"min_dpi\":300,\"format\":[\"PDF\",\"AI\",\"CDR\",\"PNG\"],\"note\":\"Sertakan cutline terpisah jika bentuk custom.\"}', NULL, NULL, 'both'),
(27, 39, 'Sticker Vinyl Outdoor', 'sticker-vinyl-outdoor', 'Sticker vinyl tahan air untuk outdoor dan kendaraan.', 'Cocok untuk branding yang butuh ketahanan. Laminasi glossy disarankan.', 'uploads/products/20251223_095625_e0827d03.jpeg', 90000.00, 'none', 0.00, 'IDR', 2000, 0, 1, '2025-12-22 15:57:24', '2025-12-23 08:56:25', NULL, '{\"min_days\":2,\"max_days\":4,\"unit\":\"hari\"}', '[\"Vinyl lebih kuat dibanding chromo.\",\"Laminasi menambah ketahanan gores & air.\",\"Cocok untuk outdoor/kendaraan.\"]', '{\"bahan\":\"Vinyl\",\"cetak\":\"Full color\",\"warna\":\"CMYK\",\"ketahanan\":\"Outdoor\"}', '{\"bleed_mm\":2,\"safe_margin_mm\":2,\"min_dpi\":300,\"format\":[\"PDF\",\"PNG\"],\"note\":\"Hindari detail sangat kecil agar tetap terbaca.\"}', NULL, NULL, 'both'),
(28, 40, 'Spanduk Flexi 280gsm', 'spanduk-flexi-280', 'Spanduk flexi ekonomis untuk event dan promosi.', 'Cocok untuk kebutuhan promosi outdoor standar. Ukuran custom tersedia.', 'uploads/products/20251223_095713_d4702eff.jpeg', 120000.00, 'none', 0.00, 'IDR', 500, 1, 1, '2025-12-22 15:57:24', '2025-12-24 05:51:31', NULL, '{\"min_days\":1,\"max_days\":2,\"unit\":\"hari\"}', '[\"Ukuran custom mengikuti kebutuhan.\",\"Finishing mata ayam opsional.\",\"File besar cukup 150-200dpi.\"]', '{\"bahan\":\"Flexi 280gsm\",\"cetak\":\"Full color\",\"penggunaan\":\"Outdoor standar\"}', '{\"bleed_mm\":0,\"safe_margin_mm\":30,\"min_dpi\":150,\"format\":[\"PDF\"],\"note\":\"Untuk banner besar, 150-200dpi cukup.\"}', NULL, NULL, 'category'),
(29, 40, 'Spanduk Flexi 340gsm', 'spanduk-flexi-340', 'Spanduk lebih tebal untuk outdoor jangka panjang.', 'Cocok untuk pemasangan lebih lama dan area berangin.', 'uploads/products/20251223_095751_09a115d7.jpeg', 170000.00, 'percent', 60.00, 'IDR', 500, 1, 1, '2025-12-22 15:57:24', '2025-12-24 06:09:36', NULL, '{\"min_days\":1,\"max_days\":3,\"unit\":\"hari\"}', '[\"Lebih tebal & kuat dari 280gsm.\",\"Rekomendasi untuk pemasangan lama.\",\"Finishing mata ayam opsional.\"]', '{\"bahan\":\"Flexi 340gsm\",\"cetak\":\"Full color\",\"penggunaan\":\"Outdoor jangka panjang\"}', '{\"bleed_mm\":0,\"safe_margin_mm\":30,\"min_dpi\":150,\"format\":[\"PDF\"],\"note\":\"Pastikan teks besar dan kontras agar terbaca dari jauh.\"}', NULL, NULL, 'category'),
(30, 41, 'Undangan Pernikahan Standard', 'undangan-pernikahan-standard', 'Undangan simple elegan dengan opsi finishing.', 'Cocok untuk undangan pernikahan, ulang tahun, dan acara keluarga.', 'uploads/products/20251223_095826_086130c6.jpeg', 150000.00, 'none', 0.00, 'IDR', 1200, 0, 1, '2025-12-22 15:57:24', '2025-12-23 08:58:26', NULL, '{\"min_days\":3,\"max_days\":5,\"unit\":\"hari\"}', '[\"Harga tergantung jumlah & finishing.\",\"Proofread nama/tanggal sebelum cetak.\",\"Laminasi menambah kesan premium.\"]', '{\"ukuran\":\"10x15 cm (custom tersedia)\",\"cetak\":\"Full color\",\"warna\":\"CMYK\"}', '{\"bleed_mm\":3,\"safe_margin_mm\":5,\"min_dpi\":300,\"format\":[\"PDF\",\"AI\",\"CDR\"],\"note\":\"Pastikan ejaan dan tanggal benar.\"}', NULL, NULL, 'both'),
(31, 42, 'Box Packaging Small', 'box-packaging-small', 'Box kemasan kecil untuk UMKM dan produk retail.', 'Box packaging ukuran kecil. Cocok untuk makanan ringan, kosmetik, dan produk UMKM.', 'uploads/products/20251223_095948_71755a40.jpeg', 250000.00, 'none', 0.00, 'IDR', 500, 0, 1, '2025-12-22 15:57:24', '2025-12-23 08:59:48', NULL, '{\"min_days\":5,\"max_days\":7,\"unit\":\"hari\"}', '[\"Custom die-cut butuh ukuran pasti (P x L x T).\",\"Disarankan mockup sebelum produksi massal.\",\"Harga tergantung ukuran & finishing.\"]', '{\"jenis\":\"Box packaging\",\"ukuran\":\"Custom (P x L x T)\",\"cetak\":\"Full color\",\"warna\":\"CMYK\"}', '{\"bleed_mm\":3,\"safe_margin_mm\":5,\"min_dpi\":300,\"format\":[\"PDF\",\"AI\",\"CDR\"],\"note\":\"Wajib sertakan ukuran dan template dieline jika ada.\"}', NULL, NULL, 'product'),
(32, 37, 'Debug Product Test Edited', 'debug-product-test', NULL, NULL, NULL, 10000.00, 'none', 0.00, 'IDR', 10, 1, 1, '2025-12-22 17:23:01', '2025-12-23 08:52:45', '2025-12-23 15:52:45', NULL, NULL, NULL, NULL, NULL, NULL, 'category'),
(33, 37, 'Deleted Test Product', 'deleted-test-product', NULL, NULL, NULL, 50000.00, 'none', 0.00, 'IDR', 10, 0, 1, '2025-12-23 09:10:53', '2025-12-23 09:12:53', '2025-12-23 16:12:53', NULL, NULL, NULL, NULL, NULL, NULL, 'category'),
(34, 49, 'Mug Custom', 'mug-custom', 'Mug putih custom untuk souvenir kantor/event.', 'Cocok untuk hadiah, branding komunitas, dan merchandise event. Cetak full color.', 'uploads/products/mug-custom.jpg', 35000.00, 'none', 0.00, 'IDR', 9999, 1, 1, '2025-12-24 06:30:34', '2025-12-24 06:30:34', NULL, '{\"min_days\":2,\"max_days\":4,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Warna hasil cetak mengikuti file (CMYK/RGB bisa berbeda).\",\"Disarankan desain kontras agar terbaca.\"]', '{\"jenis\":\"Mug keramik\",\"kapasitas\":\"11 oz\",\"area_cetak\":\"±20x9 cm\",\"cetak\":\"Full color\"}', '{\"min_dpi\":300,\"format\":[\"PNG\",\"JPG\",\"PDF\"],\"note\":\"Gunakan background transparan jika perlu. Hindari teks terlalu kecil.\"}', NULL, NULL, 'category'),
(35, 49, 'Tumbler Custom', 'tumbler-custom', 'Tumbler custom untuk souvenir premium.', 'Cocok untuk corporate gift. Bisa nama personal atau logo perusahaan.', 'uploads/products/tumbler-custom.jpg', 85000.00, 'none', 0.00, 'IDR', 9999, 0, 1, '2025-12-24 06:30:34', '2025-12-24 06:30:34', NULL, '{\"min_days\":3,\"max_days\":6,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Untuk nama personal, kirim list nama dalam file terpisah.\",\"Hasil cetak tergantung warna bahan.\"]', '{\"jenis\":\"Tumbler\",\"kapasitas\":\"500-600 ml\",\"area_cetak\":\"Menyesuaikan model\",\"cetak\":\"1-2 sisi\"}', '{\"min_dpi\":300,\"format\":[\"PDF\",\"AI\",\"CDR\",\"PNG\"],\"note\":\"Jika ada banyak nama, lampirkan Excel/CSV.\"}', NULL, NULL, 'category'),
(36, 49, 'Totebag Kanvas Custom', 'totebag-kanvas-custom', 'Totebag kanvas custom untuk event dan komunitas.', 'Totebag kanvas cocok untuk seminar kit, goodie bag, dan merch brand.', 'uploads/products/totebag-kanvas.jpg', 28000.00, 'none', 0.00, 'IDR', 9999, 1, 1, '2025-12-24 06:30:34', '2025-12-24 06:30:34', NULL, '{\"min_days\":3,\"max_days\":7,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Ukuran totebag bisa bervariasi tergantung stok.\",\"Desain 1 sisi default (2 sisi opsional).\"]', '{\"bahan\":\"Kanvas\",\"ukuran\":\"±35x40 cm\",\"area_cetak\":\"±25x30 cm\",\"cetak\":\"1 sisi (opsional 2 sisi)\"}', '{\"min_dpi\":300,\"format\":[\"PNG\",\"PDF\",\"AI\"],\"note\":\"Hindari detail kecil. Gunakan desain high-contrast.\"}', NULL, NULL, 'category'),
(37, 49, 'Kaos Custom', 'kaos-custom', 'Kaos custom untuk komunitas/event/perusahaan.', 'Kaos custom dengan sablon/printing sesuai kebutuhan. Tersedia size S-XXL.', 'uploads/products/kaos-custom.jpg', 65000.00, 'none', 0.00, 'IDR', 9999, 1, 1, '2025-12-24 06:30:34', '2025-12-24 06:30:34', NULL, '{\"min_days\":5,\"max_days\":10,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Size mix boleh, kirim breakdown size.\",\"Warna kaos mempengaruhi hasil warna desain.\"]', '{\"jenis\":\"Kaos\",\"size\":\"S-XXL\",\"area_cetak\":\"Depan/Belakang\",\"cetak\":\"1-2 sisi\"}', '{\"min_dpi\":300,\"format\":[\"AI\",\"PDF\",\"PNG\"],\"note\":\"Jika banyak varian size/nama, lampirkan list. Hindari gradasi ekstrem untuk sablon tertentu.\"}', NULL, NULL, 'category'),
(38, 49, 'Lanyard Custom', 'lanyard-custom', 'Lanyard custom untuk ID card dan event.', 'Cocok untuk panitia, peserta, dan identitas kantor. Bisa cetak satu sisi/dua sisi.', 'uploads/products/lanyard-custom.jpg', 12000.00, 'none', 0.00, 'IDR', 9999, 0, 1, '2025-12-24 06:30:34', '2025-12-24 13:57:22', '2025-12-24 20:57:22', '{\"min_days\":4,\"max_days\":8,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Minimal order biasanya berlaku (sesuaikan kebijakan).\",\"Logo harus tajam agar hasil maksimal.\"]', '{\"jenis\":\"Lanyard\",\"lebar\":\"±2 cm\",\"panjang\":\"±90 cm\",\"cetak\":\"1-2 sisi\",\"aksesoris\":\"Hook standar\"}', '{\"min_dpi\":300,\"format\":[\"AI\",\"PDF\",\"CDR\"],\"note\":\"Desain pola repetitif disarankan. Sertakan warna Pantone jika perlu.\"}', NULL, NULL, 'category'),
(39, 49, 'Pin/Badge Custom', 'pin-badge-custom', 'Pin badge custom untuk komunitas dan acara.', 'Pin custom untuk branding kecil tapi nendang. Cocok untuk event, sekolah, organisasi.', 'uploads/products/pin-badge.jpg', 5000.00, 'none', 0.00, 'IDR', 9999, 0, 1, '2025-12-24 06:30:34', '2025-12-24 06:30:34', NULL, '{\"min_days\":3,\"max_days\":6,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Bentuk bisa bulat/persegi (sesuai alat).\",\"Detail kecil berisiko tidak terbaca.\"]', '{\"jenis\":\"Pin/Badge\",\"ukuran\":\"±4.4 cm (standar)\",\"cetak\":\"Full color\",\"finishing\":\"Glossy\"}', '{\"min_dpi\":300,\"format\":[\"PNG\",\"PDF\",\"AI\"],\"note\":\"Sertakan cutline jika bentuk custom. Hindari teks < 6pt.\"}', NULL, NULL, 'category'),
(40, 49, 'Gantungan Kunci Akrilik', 'gantungan-kunci-akrilik', 'Akrilik custom untuk souvenir dan merch.', 'Gantungan kunci akrilik custom bentuk bebas (pakai cutline).', 'uploads/products/gantungan-kunci-akrilik.jpg', 9000.00, 'none', 0.00, 'IDR', 9999, 1, 1, '2025-12-24 06:30:34', '2025-12-24 13:56:39', '2025-12-24 20:56:39', '{\"min_days\":4,\"max_days\":9,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Wajib cutline untuk bentuk custom.\",\"Disarankan tambah outline agar desain tidak kepotong.\"]', '{\"bahan\":\"Akrilik\",\"ketebalan\":\"±3 mm\",\"ukuran\":\"Custom\",\"cetak\":\"Full color\"}', '{\"min_dpi\":300,\"format\":[\"AI\",\"PDF\",\"PNG\"],\"note\":\"Sertakan cutline (stroke) terpisah. Minimal jarak detail ke tepi 2mm.\"}', NULL, NULL, 'category'),
(41, 49, 'Notebook Custom', 'notebook-custom', 'Notebook custom untuk seminar kit dan corporate gift.', 'Notebook custom cover full color, bisa tambah nama perusahaan/event.', 'uploads/products/notebook-custom.jpg', 22000.00, 'none', 0.00, 'IDR', 9999, 0, 1, '2025-12-24 06:30:34', '2025-12-24 06:30:34', NULL, '{\"min_days\":5,\"max_days\":10,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Jumlah halaman bisa berbeda sesuai paket.\",\"Pastikan desain cover resolusi tinggi.\"]', '{\"jenis\":\"Notebook\",\"ukuran\":\"A5\",\"cover\":\"Full color\",\"isi\":\"Garis/kosong (opsional)\"}', '{\"min_dpi\":300,\"format\":[\"PDF\",\"AI\",\"CDR\"],\"note\":\"Bleed 3mm untuk cover. Pastikan margin aman 5mm.\"}', NULL, NULL, 'category'),
(42, 49, 'Kalender Meja Custom', 'kalender-meja-custom', 'Kalender meja custom untuk branding tahunan.', 'Kalender meja custom cocok untuk promosi brand sepanjang tahun.', 'uploads/products/kalender-meja.jpg', 18000.00, 'none', 0.00, 'IDR', 9999, 0, 1, '2025-12-24 06:30:34', '2025-12-24 06:30:34', NULL, '{\"min_days\":7,\"max_days\":14,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Tema dan layout harus konsisten untuk 12 bulan.\",\"Perhatikan tanggal merah jika ingin custom.\"]', '{\"jenis\":\"Kalender meja\",\"ukuran\":\"±20x15 cm\",\"jumlah_halaman\":\"13 (cover + 12 bulan)\",\"finishing\":\"Spiral\"}', '{\"min_dpi\":300,\"format\":[\"PDF\"],\"note\":\"Gunakan template grid tanggal. Pastikan foto tidak pecah.\"}', NULL, NULL, 'category'),
(43, 49, 'Name Tag / ID Card Custom', 'name-tag-idcard-custom', 'Name tag custom untuk panitia, kantor, dan event.', 'Name tag custom lengkap dengan desain identitas. Cocok untuk event dan kantor.', 'uploads/products/name-tag.jpg', 15000.00, 'none', 0.00, 'IDR', 9999, 0, 1, '2025-12-24 06:30:34', '2025-12-24 06:30:34', NULL, '{\"min_days\":2,\"max_days\":5,\"unit\":\"hari\"}', '[\"Harga per pcs.\",\"Jika ada banyak nama, lampirkan list data.\",\"Bisa tambah slot/jepit sesuai stok.\"]', '{\"jenis\":\"Name Tag/ID Card\",\"ukuran\":\"±9x5.5 cm (standar)\",\"cetak\":\"Full color\",\"finishing\":\"Laminasi opsional\"}', '{\"min_dpi\":300,\"format\":[\"PDF\",\"PNG\"],\"note\":\"Jika variabel data (nama/jabatan), kirim file Excel/CSV.\"}', NULL, NULL, 'category'),
(44, 49, 'Gantungan Kunci', 'gantungan-kunci', 'ini adalah gantungan kunci anime', 'ini adalah gantungan kunci anime one piece', NULL, 10000.00, 'none', 0.00, 'IDR', 10, 1, 1, '2025-12-24 06:43:26', '2025-12-24 14:32:00', NULL, NULL, NULL, NULL, NULL, 'https://shopee.co.id/', 'htpp://tokopedia.com/', 'category');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `whatsapp_number` varchar(50) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `name`, `slug`, `description`, `icon`, `whatsapp_number`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(37, 'Kartu Nama', 'kartu-nama', 'Cetak kartu nama profesional berbagai finishing.', '🪪', '+6285185318501', 1, 1, '2025-12-22 14:53:30', '2025-12-23 13:04:17'),
(38, 'Brosur & Flyer', 'brosur-flyer', 'Promosi cepat dengan brosur dan flyer berkualitas.', '📄', NULL, 2, 1, '2025-12-22 14:53:30', '2025-12-22 14:53:30'),
(39, 'Sticker & Label', 'sticker-label', 'Sticker custom untuk branding dan kemasan.', '🏷️', NULL, 3, 1, '2025-12-22 14:53:30', '2025-12-22 14:53:30'),
(40, 'Banner & Spanduk', 'banner-spanduk', 'Media promosi ukuran besar indoor/outdoor.', '🖼️', NULL, 4, 1, '2025-12-22 14:53:30', '2025-12-22 14:53:30'),
(41, 'Undangan', 'undangan', 'Undangan acara dengan desain elegan & finishing premium.', '💌', NULL, 5, 1, '2025-12-22 14:53:30', '2025-12-22 14:53:30'),
(42, 'Packaging', 'packaging', 'Kemasan produk dengan material tebal dan finishing rapi.', '📦', NULL, 6, 1, '2025-12-22 14:53:30', '2025-12-22 14:53:30'),
(49, 'Merchandise & Souvenir', 'merchandise-souvenir', 'Produk merchandise untuk event, kantor, komunitas, dan hadiah.', '🎁', NULL, 7, 1, '2025-12-24 06:30:07', '2025-12-24 06:30:07');

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
(8, 24, 'fixed', 5000.00, 0, 0, NULL, NULL, 1, 1, '2025-12-23 19:59:29', '2025-12-23 19:59:29');

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
-- Table structure for table `product_laminations`
--

CREATE TABLE `product_laminations` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to products.id',
  `lamination_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to laminations.id',
  `price_delta_override` decimal(10,2) DEFAULT NULL COMMENT 'Product-specific price override (NULL = use master)',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Maps which laminations are available for a specific product (overrides category)';

-- --------------------------------------------------------

--
-- Table structure for table `product_materials`
--

CREATE TABLE `product_materials` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to products.id',
  `material_id` int(10) UNSIGNED NOT NULL COMMENT 'FK to materials.id',
  `price_delta_override` decimal(10,2) DEFAULT NULL COMMENT 'Product-specific price override (NULL = use master)',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Maps which materials are available for a specific product (overrides category)';

-- --------------------------------------------------------

--
-- Table structure for table `product_options`
--

CREATE TABLE `product_options` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL COMMENT 'Foreign key to products.id',
  `option_type` enum('material','lamination') NOT NULL COMMENT 'Type of option: material or lamination',
  `name` varchar(100) NOT NULL COMMENT 'Display name (e.g., "Albatros", "Doff")',
  `slug` varchar(100) NOT NULL COMMENT 'URL-safe identifier',
  `price_delta` decimal(10,2) DEFAULT 0.00 COMMENT 'Price adjustment (+/-) from base price',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Display order (lower = first)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 = active, 0 = hidden',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product customization options (materials, laminations) with price deltas';

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
(8, 24, 'Ukuran', 'checkbox', 0, 0, 0, 1, 1, '2025-12-23 11:12:04', '2025-12-23 11:29:05'),
(9, 24, 'Jumlah', 'checkbox', 0, 0, 0, 2, 1, '2025-12-23 11:12:16', '2025-12-23 11:29:10');

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
(12, 9, '50', 'percent', 5.00, 2, 1, '2025-12-23 11:13:23', '2025-12-23 11:14:04'),
(13, 8, '50', 'percent', 5.00, 1, 1, '2025-12-23 11:13:41', '2025-12-23 11:13:41');

-- --------------------------------------------------------

--
-- Table structure for table `product_price_tiers`
--

CREATE TABLE `product_price_tiers` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL COMMENT 'Foreign key to products.id',
  `qty_min` int(11) NOT NULL COMMENT 'Minimum quantity for this tier',
  `qty_max` int(11) DEFAULT NULL COMMENT 'Maximum quantity (NULL = unlimited)',
  `unit_price` decimal(10,2) NOT NULL COMMENT 'Price per unit at this tier',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 = active, 0 = hidden',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_price_tiers`
--

INSERT INTO `product_price_tiers` (`id`, `product_id`, `qty_min`, `qty_max`, `unit_price`, `is_active`, `created_at`, `updated_at`) VALUES
(11, 23, 1, 99, 1200.00, 1, '2025-12-22 16:17:19', '2025-12-22 16:17:19'),
(12, 23, 100, 199, 950.00, 1, '2025-12-22 16:17:34', '2025-12-22 16:17:34'),
(13, 23, 200, 499, 800.00, 1, '2025-12-22 16:17:59', '2025-12-22 16:17:59'),
(14, 23, 500, 1000, 650.00, 1, '2025-12-22 16:18:13', '2025-12-22 16:18:13'),
(28, 22, 1, 99, 800.00, 1, '2025-12-22 16:27:30', '2025-12-22 16:27:30'),
(29, 22, 100, 199, 650.00, 1, '2025-12-22 16:27:45', '2025-12-22 16:27:45'),
(30, 22, 200, 499, 500.00, 1, '2025-12-22 16:28:08', '2025-12-22 16:28:08'),
(31, 22, 500, 1000, 400.00, 1, '2025-12-22 16:28:26', '2025-12-22 16:28:26'),
(32, 24, 1, 49, 1500.00, 1, '2025-12-22 16:30:38', '2025-12-22 16:30:38'),
(33, 24, 50, 99, 1200.00, 1, '2025-12-22 16:30:51', '2025-12-22 16:30:51'),
(34, 24, 100, 249, 950.00, 1, '2025-12-22 16:31:09', '2025-12-22 16:31:09'),
(35, 24, 250, 500, 800.00, 1, '2025-12-22 16:31:28', '2025-12-22 16:31:28'),
(36, 25, 1, 49, 4500.00, 1, '2025-12-22 16:33:02', '2025-12-22 16:33:02'),
(37, 25, 50, 99, 3800.00, 1, '2025-12-22 16:33:20', '2025-12-22 16:33:20'),
(38, 25, 100, 249, 3200.00, 1, '2025-12-22 16:33:39', '2025-12-22 16:33:39'),
(39, 25, 250, 1000, 2800.00, 1, '2025-12-22 16:33:57', '2025-12-22 16:33:57'),
(40, 26, 1, 1, 65000.00, 1, '2025-12-22 16:35:45', '2025-12-22 16:35:45'),
(41, 26, 2, 9, 60000.00, 1, '2025-12-22 16:36:07', '2025-12-22 16:36:07'),
(42, 26, 10, 24, 55000.00, 1, '2025-12-22 16:36:34', '2025-12-22 16:36:34'),
(43, 26, 25, 50, 50000.00, 1, '2025-12-22 16:40:28', '2025-12-22 16:40:28'),
(44, 30, 1, 49, 3500.00, 1, '2025-12-22 16:41:24', '2025-12-22 16:41:24'),
(45, 30, 50, 99, 3000.00, 1, '2025-12-22 16:41:41', '2025-12-22 16:41:41'),
(46, 30, 100, 199, 2600.00, 1, '2025-12-22 16:42:04', '2025-12-22 16:42:04'),
(47, 30, 200, 1000, 2200.00, 1, '2025-12-22 16:42:28', '2025-12-22 16:42:28');

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
  `maps_link` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `sales_contacts` text DEFAULT NULL,
  `operating_hours` varchar(255) DEFAULT NULL,
  `gmaps_embed` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `site_tagline`, `logo`, `phone`, `email`, `address`, `maps_link`, `facebook`, `instagram`, `twitter`, `youtube`, `linkedin`, `tiktok`, `whatsapp`, `sales_contacts`, `operating_hours`, `gmaps_embed`, `created_at`, `updated_at`) VALUES
(1, 'EventPrint', 'Layanan cetak event, pameran, dan promosi brand', 'uploads/settings/logo_download_1766509723.png', '081511929326', 'myorder.eventprint@gmail.com', 'Jl. Serua Raya No.46, Serua, Kec. Bojongsari, Kota Depok, Jawa Barat', 'https://maps.app.goo.gl/ChYDqWneg3ULuaNz8', '', 'https://www.instagram.com/event.print_/', '', 'https://youtube.com/@eventprint', '', 'https://tiktok.com/@eventprint', '081511929326', '[{\"name\":\"cs 1\",\"number\":\"089653436221\"},{\"name\":\"cs 2\",\"number\":\"0895353304264\"}]', 'Senin - Sabtu: 08:00 - 19:00', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.197816369866!2d106.73157667462885!3d-6.368440893621703!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ef5ca6d4baa7%3A0xc5e4b1467c239c12!2sJl.%20Serua%20Raya%20No.46%2C%20Serua%2C%20Kec.%20Bojongsari%2C%20Kota%20Depok%2C%20Jawa%20Barat%2016517!5e0!3m2!1sid!2sid!4v1766510234523!5m2!1sid!2sid\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', '2025-12-11 19:06:58', '2025-12-24 14:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Nama pemberi testimoni',
  `position` varchar(150) DEFAULT NULL COMMENT 'Jabatan/perusahaan (optional)',
  `photo` varchar(255) DEFAULT NULL COMMENT 'Path foto (optional)',
  `rating` tinyint(3) UNSIGNED NOT NULL DEFAULT 5 COMMENT 'Rating 1-5 bintang',
  `message` text NOT NULL COMMENT 'Isi testimoni',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Publish/unpublish',
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Urutan tampil (smaller = pertama)',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Testimonials untuk homepage';

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `position`, `photo`, `rating`, `message`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(9, 'Budi Santoso', 'Owner Toko ABC', NULL, 5, 'Kualitas cetakan sangat bagus dan harga terjangkau. Sudah langganan 2 tahun!', 1, 1, '2025-12-18 11:36:41', '2025-12-18 11:36:41'),
(18, 'Siti Nurhaliza', 'Event Organizer PT Kreatif', NULL, 5, 'Pelayanan cepat dan hasil memuaskan. Recommended untuk cetak banner event.', 1, 2, '2025-12-18 12:05:33', '2025-12-18 12:05:33'),
(19, 'Andi Wijaya', 'Pengusaha UMKM', NULL, 4, 'PrintEventPrint membantu bisnis saya dengan kualitas cetak yang konsisten.', 1, 3, '2025-12-18 12:05:33', '2025-12-18 12:05:33'),
(20, 'Linda Chen', 'Marketing Manager PT XYZ', NULL, 5, 'Professional, on-time delivery, dan support responsive. Sangat puas!', 1, 4, '2025-12-18 12:05:33', '2025-12-18 12:05:33'),
(21, 'Hekal Latief', 'Founder', NULL, 5, 'Ini Web bagus banget sumpah!!!!!!', 1, 1, '2025-12-20 04:46:06', '2025-12-20 04:46:06');

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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `is_active`, `last_login_at`, `created_at`, `updated_at`, `failed_attempts`, `locked_until`) VALUES
(1, 'Super admin', 'superadmin1@example.com', '$2y$10$K3JWVttfrq3fyscjXsEtve8MtcZEucZC.qwzxjKZclTlB6nbkWUA.', 'super_admin', 1, '2025-12-24 14:26:00', '2025-12-08 10:35:28', '2025-12-24 07:26:00', 0, NULL),
(2, 'admin', 'admin@example.com', '$2y$10$6hm8GoEmVw6T5fGTOztvQeZQP/xmQ5gCasLPF7IKwr6pCr1a1DTXm', 'admin', 1, '2025-12-24 03:27:24', '2025-12-09 14:39:30', '2025-12-24 04:54:45', 2, NULL),
(6, 'ucup', 'ucup@example.com', '$2y$10$S5648Qtq2ZsxL1QBVU5sUeE9TO7vp7mpja4FmXEh532mLL9pjcD/K', 'admin', 1, '2025-12-24 14:25:04', '2025-12-23 11:55:50', '2025-12-24 07:25:04', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_level` (`level`),
  ADD KEY `idx_source` (`source`);

--
-- Indexes for table `category_laminations`
--
ALTER TABLE `category_laminations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_category_lamination` (`category_id`,`lamination_id`),
  ADD KEY `lamination_id` (`lamination_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `category_materials`
--
ALTER TABLE `category_materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_category_material` (`category_id`,`material_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `hero_slides`
--
ALTER TABLE `hero_slides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_home_active_pos` (`page_slug`,`is_active`,`position`);

--
-- Indexes for table `laminations`
--
ALTER TABLE `laminations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `our_store`
--
ALTER TABLE `our_store`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_active_sort` (`is_active`,`sort_order`);

--
-- Indexes for table `our_store_gallery`
--
ALTER TABLE `our_store_gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_store_id` (`store_id`);

--
-- Indexes for table `page_contents`
--
ALTER TABLE `page_contents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_page_section_item_field` (`page_slug`,`section`,`item_key`,`field`),
  ADD UNIQUE KEY `uniq_page_section_field_item` (`page_slug`,`section`,`field`,`item_key`),
  ADD KEY `idx_page_section_item` (`page_slug`,`section`,`item_key`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_featured_published` (`is_featured`,`is_published`,`published_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_products_category` (`category_id`),
  ADD KEY `idx_featured_active` (`is_featured`,`is_active`),
  ADD KEY `idx_deleted_at` (`deleted_at`);

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
-- Indexes for table `product_laminations`
--
ALTER TABLE `product_laminations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_product_lamination` (`product_id`,`lamination_id`),
  ADD KEY `lamination_id` (`lamination_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `product_materials`
--
ALTER TABLE `product_materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_product_material` (`product_id`,`material_id`),
  ADD KEY `material_id` (`material_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `product_options`
--
ALTER TABLE `product_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_type` (`product_id`,`option_type`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

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
-- Indexes for table `product_price_tiers`
--
ALTER TABLE `product_price_tiers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_quantity` (`qty_min`,`qty_max`),
  ADD KEY `idx_active` (`is_active`);

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
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active_sort` (`is_active`,`sort_order`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=281;

--
-- AUTO_INCREMENT for table `category_laminations`
--
ALTER TABLE `category_laminations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `category_materials`
--
ALTER TABLE `category_materials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `hero_slides`
--
ALTER TABLE `hero_slides`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `laminations`
--
ALTER TABLE `laminations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `our_store`
--
ALTER TABLE `our_store`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `our_store_gallery`
--
ALTER TABLE `our_store_gallery`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `page_contents`
--
ALTER TABLE `page_contents`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=218;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

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
-- AUTO_INCREMENT for table `product_laminations`
--
ALTER TABLE `product_laminations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_materials`
--
ALTER TABLE `product_materials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product_options`
--
ALTER TABLE `product_options`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `product_option_groups`
--
ALTER TABLE `product_option_groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_option_values`
--
ALTER TABLE `product_option_values`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_price_tiers`
--
ALTER TABLE `product_price_tiers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

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
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `category_laminations`
--
ALTER TABLE `category_laminations`
  ADD CONSTRAINT `category_laminations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_laminations_ibfk_2` FOREIGN KEY (`lamination_id`) REFERENCES `laminations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_materials`
--
ALTER TABLE `category_materials`
  ADD CONSTRAINT `category_materials_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_materials_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `our_store_gallery`
--
ALTER TABLE `our_store_gallery`
  ADD CONSTRAINT `fk_gallery_store` FOREIGN KEY (`store_id`) REFERENCES `our_store` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `product_laminations`
--
ALTER TABLE `product_laminations`
  ADD CONSTRAINT `product_laminations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_laminations_ibfk_2` FOREIGN KEY (`lamination_id`) REFERENCES `laminations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_materials`
--
ALTER TABLE `product_materials`
  ADD CONSTRAINT `product_materials_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_materials_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_options`
--
ALTER TABLE `product_options`
  ADD CONSTRAINT `product_options_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `product_price_tiers`
--
ALTER TABLE `product_price_tiers`
  ADD CONSTRAINT `product_price_tiers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

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
