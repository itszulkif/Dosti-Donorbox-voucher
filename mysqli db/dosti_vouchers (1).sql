-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2026 at 11:01 AM
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
-- Database: `dosti_vouchers`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','voucher_editor','box_editor') DEFAULT 'admin',
  `restaurant_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `full_name`, `designation`, `username`, `email`, `password_hash`, `role`, `restaurant_id`, `created_at`) VALUES
(9, NULL, NULL, 'spot', 'spot@partner.local', '$2y$10$nTk0Px0nLz6mcpx9kLN4Ke27ZDrdEZ6TeaBdFJ6UdqEOzakXPjZKK', 'admin', 14, '2026-02-05 07:02:24'),
(17, 'Muhammad Zulkif', 'IT & System Manager', 'zulkif', 'zulkif@dostiwelfare.org', '$2y$10$V3AXQ52pSyXOW.g6Cs855.HnqaKLwm8HRNdUlDaX5Zumcqeq16fqq', 'super_admin', NULL, '2026-02-06 09:12:32'),
(18, 'KFC', NULL, 'kfc', 'kfc@partner.local', '$2y$10$u5Au4igMCIEF0Z4rXNpmgueIKU4ppJ/na9RdJZJgzMA/POwlx7QCC', 'admin', 16, '2026-02-06 09:48:54'),
(19, 'District 9', NULL, 'district', 'district@partner.local', '$2y$10$eD5xXj8diim6n73gEjjAd.yWcF1rm94NqSH67dBZ77AE2dlT7syOe', 'admin', 17, '2026-02-06 11:04:33');

-- --------------------------------------------------------

--
-- Table structure for table `campaign_clicks`
--

CREATE TABLE `campaign_clicks` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `clicked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_opens`
--

CREATE TABLE `campaign_opens` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `campaign_templates`
--

CREATE TABLE `campaign_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `audience_type` enum('donor','shopkeeper') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `campaign_templates`
--

INSERT INTO `campaign_templates` (`id`, `name`, `subject`, `body`, `audience_type`, `created_at`) VALUES
(1, 'Scheduled: 2026-02-13 08:44', 'testing campaign', '<p>asffasdfdfaf</p><p>asd</p><p>sdsdf</p><p>f</p><p>fdf</p><p>sdf</p><p>f{{phone}}{{email}}{{box_number}}</p>', 'shopkeeper', '2026-02-13 07:44:46'),
(2, 'Test Temp', 'Test Schedule 1771226338', 'This is a test scheduled email body.', 'donor', '2026-02-16 07:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `donation_shops`
--

CREATE TABLE `donation_shops` (
  `id` int(11) NOT NULL,
  `box_number` varchar(50) NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `installation_date` date DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_shops`
--

INSERT INTO `donation_shops` (`id`, `box_number`, `shop_name`, `email`, `installation_date`, `contact_person`, `phone`, `address`, `created_at`) VALUES
(6, '11', 'dry fruit khan', NULL, '2026-02-02', 'kamil', '03334243', 'askldfjkls', '2026-02-02 10:25:20'),
(7, '250', 'zulkif model school', NULL, '2026-02-05', 'zulkif', '0234234', 'asdfkljsfsdkl', '2026-02-04 05:38:16'),
(8, '8', 'M DOT IS COLLECTION', NULL, '2026-02-04', 'MOHABAT', '923115520289', '4 FEIT CITY TOWER UNI ROAD PSHAWAR', '2026-02-04 08:02:12'),
(9, '10', 'OUT LOOK FASHION', NULL, '2026-02-04', 'ARSHAID IQBAL ', '923009364510', 'TOWN TOWER PLAZA PESHAWAR', '2026-02-04 08:02:46'),
(10, '32', 'HAROUN GARMENTS ', NULL, '2026-02-04', 'HAROON ', '923009594377', 'TOWN TOWER PLAZA PESHAWAR', '2026-02-04 08:03:14'),
(11, '34', 'MISS CLASS ', NULL, '2026-02-04', 'SHARIF ULLHA', '923018150614', 'TOWN TOWER PLAZA PESHAWAR', '2026-02-04 08:03:43'),
(12, '3', 'OSAMMA HALL', NULL, '2026-02-04', 'Kamran', '923429097179', 'KHUSSA MEHAL PESHAWAR', '2026-02-04 08:04:37'),
(13, '51', 'STITCH ZAY', NULL, '2026-02-04', 'SHAYAR KHAN ', '923018881888', 'JAWAD TOWER UIVERSTY ROAD PESHAWAR', '2026-02-04 08:05:06'),
(14, '28', 'MOHD IJAZ FOODWEAR SHOOP#2 NUM BLOCK', NULL, '2026-02-04', 'MUHAMMAD IJAZ', '923149146937', 'SHAKEL PLAZA  OPP ISLIMA COLLEGE PESHAWAR', '2026-02-04 08:05:45'),
(15, '39', 'BRIDAL FASHON ', NULL, '2026-02-04', 'UMAIR KHAN', '923163576385', 'AL SAYED PLAZA UNIVERSTY ROAD PESHAWAR', '2026-02-04 08:06:11'),
(16, '42', 'RIWACH BRIDAL BOU', NULL, '2026-02-04', 'YASIRTIQUE', '923219166923', 'ALL SAYED PLAZA  UNIVERSTY ROAD PESHAWAR', '2026-02-04 08:06:57'),
(18, '129', 'INSAF CLOTH', NULL, '2026-02-04', 'GUL JAN', '923155250200', 'shop no 4 board bazar', '2026-02-04 08:07:46'),
(21, '12', 'hahijsafjkl', NULL, '2026-02-05', 'asklfjdk', '234423', 'sasdf', '2026-02-05 07:54:14'),
(27, '100', 'book fair', 'bookg2@gmail.com', '2026-02-06', 'amir', '0234234234', 'asmfkdjl', '2026-02-06 09:46:20'),
(33, 'TEST-3367', 'Test Shop 757', 'test@example.com', '2026-02-06', '', '', '', '2026-02-06 10:33:21'),
(34, '21', 'asfsdf', 'muhammadzulkif2001@gmail.com', '2026-02-06', 'Muhammad Zulkif', '23423', 'saf', '2026-02-06 10:37:07'),
(35, '15', 'Faisal General Store', '', '2026-02-06', 'Faisal ', '02342323', 'Board bazar', '2026-02-06 11:00:41'),
(36, '101', 'AK Mart', 'kzulfi968@gmail.com', '2026-02-13', 'Muhammad Zulkif', '03339298', 'asffdaf', '2026-02-13 05:35:28'),
(37, '211', 'aman', 'dostiwelorg@gmail.com', '2026-02-13', 'Muhammad Zulkif', '03339298', 'asfsdf', '2026-02-13 05:47:11'),
(38, 'ansar', 'janu', 'globaleducationcampaign4@gmail.com', '2026-02-13', 'Muhammad Zulkif', '03338332929', 'fsdfd', '2026-02-13 06:23:13');

-- --------------------------------------------------------

--
-- Table structure for table `donation_visits`
--

CREATE TABLE `donation_visits` (
  `id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `visit_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `amount_1` decimal(10,2) DEFAULT 0.00,
  `amount_2` decimal(10,2) DEFAULT 0.00,
  `received_from` varchar(255) DEFAULT NULL,
  `received_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donation_visits`
--

INSERT INTO `donation_visits` (`id`, `shop_id`, `visit_date`, `amount`, `amount_1`, `amount_2`, `received_from`, `received_by`, `created_at`) VALUES
(7, 6, '2026-02-02', 500.00, 0.00, 0.00, 'amran', 'khalil', '2026-02-02 11:04:50'),
(8, 6, '2026-02-04', 5200.00, 0.00, 0.00, 'amran', 'khalil', '2026-02-03 09:58:42'),
(9, 6, '2026-02-04', 500.00, 0.00, 0.00, 'kamran', 'khalil', '2026-02-03 10:10:35'),
(10, 6, '2026-02-04', 400.00, 0.00, 0.00, 'amran', 'khalil', '2026-02-03 10:10:44'),
(11, 6, '2026-02-10', 4000.00, 0.00, 0.00, 'amran', 'khalil', '2026-02-03 10:10:53'),
(12, 6, '2026-02-18', 400.00, 0.00, 0.00, 'amran', 'khalil', '2026-02-03 10:11:02'),
(13, 7, '2026-02-05', 5000.00, 0.00, 0.00, 'shahid', 'khalil', '2026-02-04 05:39:46'),
(14, 7, '2026-02-05', 1500.00, 1500.00, 1500.00, 'shahid', 'khalil', '2026-02-04 06:16:17'),
(15, 7, '2026-02-09', 110.00, 110.00, 110.00, 'jawad', 'amir', '2026-02-04 08:00:50'),
(16, 18, '2026-02-06', 1500.00, 1500.00, 1500.00, 'hamza', 'khalil', '2026-02-04 08:08:52'),
(17, 12, '2026-02-05', 1500.00, 1500.00, 1500.00, 'janii', 'khalil', '2026-02-04 08:33:20'),
(18, 8, '2026-02-05', 10.00, 10.00, 10.00, 'amran', 'khalil', '2026-02-04 08:36:52'),
(19, 9, '2026-02-05', 1500.00, 1500.00, 1500.00, 'asif', 'arif', '2026-02-04 08:37:13'),
(20, 18, '2026-02-05', 1500.00, 1500.00, 1500.00, 'amran', 'khalil', '2026-02-04 09:27:35'),
(21, 21, '2026-02-05', 10.00, 10.00, 10.00, 'amran', 'khalil', '2026-02-05 07:54:56'),
(22, 9, '2026-02-05', 10000.00, 10000.00, 10000.00, 'amran', 'khalil', '2026-02-05 09:18:33'),
(23, 9, '2026-02-06', 15000.00, 15000.00, 15000.00, 'amran', 'khalil', '2026-02-05 09:28:03'),
(24, 13, '2026-02-05', 1000.00, 1000.00, 1000.00, 'shahid', 'khalil', '2026-02-05 09:28:54'),
(25, 11, '2026-02-05', 120.00, 120.00, 120.00, 'kamran', 'khalil', '2026-02-05 09:29:23'),
(26, 9, '2026-02-06', 110.00, 110.00, 110.00, 'amran', 'khalil', '2026-02-06 09:35:57'),
(27, 9, '2026-02-07', 1110.00, 1110.00, 1110.00, '', '', '2026-02-06 09:36:50'),
(28, 27, '2026-02-06', 550.00, 550.00, 550.00, 'amran', 'khalil', '2026-02-06 09:46:54'),
(29, 27, '2026-02-09', 550.00, 550.00, 550.00, '', '', '2026-02-06 09:47:49'),
(30, 35, '2026-02-06', 1550.00, 1550.00, 1550.00, 'amran', 'faisal', '2026-02-06 11:01:28');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `voucher_id` varchar(50) NOT NULL,
  `voucher_value` decimal(10,2) DEFAULT 500.00,
  `status` enum('Active','Inactive','Redeemed') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`id`, `name`, `email`, `phone`, `voucher_id`, `voucher_value`, `status`, `created_at`) VALUES
(21, 'Ahmed', 'ahme@gmail.com', '154156451', '1', 500.00, 'Active', '2026-02-05 07:02:40'),
(22, 'asif', 'aisfs@gmail.com', '23423234', '100', 500.00, 'Active', '2026-02-05 10:17:10'),
(23, 'amir', '', '023423', '11', 500.00, 'Active', '2026-02-06 06:15:35'),
(24, 'zou', '', '033333', '12', 500.00, 'Active', '2026-02-06 06:23:26'),
(25, 'ansdu', 'asa@gmail.com', '023333333333', '5000', 500.00, 'Active', '2026-02-06 07:04:35'),
(26, 'jabar', 'jabar@gmail.com', '2342343', '200', 500.00, 'Active', '2026-02-06 09:27:26'),
(27, 'faisal ', '', '', '499', 500.00, 'Active', '2026-02-06 11:05:46'),
(36, 'airf kawla', 'muhammadzulkif2001@gmail.com', '0333832929', '1200', 500.00, 'Active', '2026-02-13 05:34:18'),
(37, 'jajslk2w', '', '033332', '12000', 500.00, 'Active', '2026-02-13 05:34:57'),
(48, 'amar jhon', 'geminidosit@gmail.com', '5903458934590', '102', 500.00, 'Active', '2026-02-13 05:48:56'),
(50, 'amar jj', 'geminidosti@gmail.com', '', '101', 500.00, 'Active', '2026-02-13 05:50:16'),
(52, 'aman', 'trumpaivideo2246@gmail.com', '', '103', 500.00, 'Active', '2026-02-13 06:20:25'),
(53, 'anuun', 'riminig324@2insp.com', '', '1211', 500.00, 'Active', '2026-02-13 06:29:13'),
(60, 'weruioweru', 'riminig324@2insp.com', '', '5001', 500.00, 'Active', '2026-02-13 06:35:10'),
(61, 'asdfsa', 'sewik57617@2insp.com', '', '1212', 500.00, 'Active', '2026-02-13 06:36:59'),
(62, 'mayar', 'sewik57617@2insp.com', '', '1500', 500.00, 'Active', '2026-02-16 06:59:35');

-- --------------------------------------------------------

--
-- Table structure for table `donor_offers`
--

CREATE TABLE `donor_offers` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `restaurant_name` varchar(255) NOT NULL,
  `restaurant_address` text DEFAULT NULL,
  `offer_type` enum('percentage','fixed') NOT NULL,
  `offer_value` decimal(10,2) NOT NULL,
  `status` enum('Pending','Redeemed') DEFAULT 'Pending',
  `redeemed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donor_offers`
--

INSERT INTO `donor_offers` (`id`, `donor_id`, `restaurant_name`, `restaurant_address`, `offer_type`, `offer_value`, `status`, `redeemed_at`) VALUES
(28, 21, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Redeemed', NULL),
(29, 22, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Redeemed', NULL),
(30, 23, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Redeemed', '2026-02-06 06:15:53'),
(32, 24, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Redeemed', '2026-02-06 06:24:04'),
(34, 25, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(35, 26, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(36, 27, 'District 9', 'peshawar', 'percentage', 15.00, 'Redeemed', '2026-02-06 11:05:58'),
(37, 27, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(38, 27, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(39, 36, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(40, 36, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(41, 36, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(42, 37, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(43, 37, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(44, 37, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(45, 48, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(46, 48, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(47, 48, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(48, 50, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(49, 50, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(50, 50, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Redeemed', '2026-02-13 05:51:27'),
(51, 52, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(52, 52, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(53, 52, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(54, 53, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(55, 53, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(56, 60, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(57, 60, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(58, 60, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(59, 61, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(60, 61, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL),
(61, 61, 'Melting Spots', 'Peshawar', 'percentage', 15.00, 'Pending', NULL),
(62, 62, 'District 9', 'peshawar', 'percentage', 15.00, 'Pending', NULL),
(63, 62, 'KFC', 'pesahwar', 'percentage', 50.00, 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `email_campaigns`
--

CREATE TABLE `email_campaigns` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `audience_type` varchar(50) NOT NULL,
  `sent_count` int(11) DEFAULT 0,
  `fail_count` int(11) DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_campaigns`
--

INSERT INTO `email_campaigns` (`id`, `subject`, `body`, `audience_type`, `sent_count`, `fail_count`, `sent_at`) VALUES
(3, 'thank you for purchase donor', '<p>new voucher comoing soon{{phone}}{{email}}{{name}}{{voucher_id}}</p>', 'donor', 11, 0, '2026-02-13 07:15:01'),
(7, 'testing campaign', '<p>asffasdfdfaf</p><p>asd</p><p>sdsdf</p><p>f</p><p>fdf</p><p>sdf</p><p>f{{phone}}{{email}}{{box_number}}</p>', 'shopkeeper', 6, 0, '2026-02-16 07:18:59'),
(8, 'Test Schedule 1771226338', 'This is a test scheduled email body.', 'donor', 12, 0, '2026-02-16 07:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `status` enum('sent','failed') NOT NULL,
  `error_message` text DEFAULT NULL,
  `email_type` varchar(50) NOT NULL,
  `campaign_id` int(11) DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_logs`
--

INSERT INTO `email_logs` (`id`, `recipient_email`, `recipient_name`, `subject`, `body`, `status`, `error_message`, `email_type`, `campaign_id`, `sent_at`) VALUES
(1, 'trumpaivideo2246@gmail.com', 'aman', 'voucher active 103', 'Hello aman,\r\nThis is voucher id 103\r\nthank you for your support', 'sent', NULL, 'voucher', NULL, '2026-02-13 06:20:27'),
(2, 'trumpaivideo2246@gmail.com', 'aman', 'voucher active 103', 'Hello aman,\r\nThis is voucher id 103\r\nthank you for your support', 'sent', NULL, 'voucher', NULL, '2026-02-13 06:20:30'),
(3, 'globaleducationcampaign4@gmail.com', 'Muhammad Zulkif', 'collection scedule for ansar', 'Hello janu,\r\nour team will visit janu and possible soon', 'sent', NULL, 'box', NULL, '2026-02-13 06:23:15'),
(5, 'riminig324@2insp.com', 'anuun', 'voucher active 1211', 'Hello anuun,\r\nThis is voucher id 1211\r\nthank you for your support', 'sent', NULL, 'voucher', NULL, '2026-02-13 06:29:16'),
(6, 'riminig324@2insp.com', 'weruioweru', 'voucher active 5001', 'Hello weruioweru,\r\nThis is voucher id 5001\r\nthank you for your support', 'sent', NULL, 'voucher', NULL, '2026-02-13 06:35:13'),
(7, 'sewik57617@2insp.com', 'asdfsa', 'voucher active 1212', 'Hello asdfsa,\r\nThis is voucher id 1212\r\nthank you for your support', 'sent', NULL, 'voucher', NULL, '2026-02-13 06:37:02'),
(8, 'test_verify_1771225812@example.com', 'Test User', 'Verification Test 1771225812', 'This is a test body', 'sent', NULL, 'manual', NULL, '2026-02-16 07:10:12'),
(9, 'bookg2@gmail.com', 'amir', 'testing campaign', '<p>asffasdfdfaf</p><p>asd</p><p>sdsdf</p><p>f</p><p>fdf</p><p>sdf</p><p>f0234234234bookg2@gmail.com100</p><img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=7&e=bookg2%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 7, '2026-02-16 07:19:01'),
(10, 'test@example.com', '', 'testing campaign', '<p>asffasdfdfaf</p><p>asd</p><p>sdsdf</p><p>f</p><p>fdf</p><p>sdf</p><p>ftest@example.comTEST-3367</p><img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=7&e=test%40example.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 7, '2026-02-16 07:19:04'),
(11, 'muhammadzulkif2001@gmail.com', 'Muhammad Zulkif', 'testing campaign', '<p>asffasdfdfaf</p><p>asd</p><p>sdsdf</p><p>f</p><p>fdf</p><p>sdf</p><p>f23423muhammadzulkif2001@gmail.com21</p><img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=7&e=muhammadzulkif2001%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 7, '2026-02-16 07:19:06'),
(12, 'kzulfi968@gmail.com', 'Muhammad Zulkif', 'testing campaign', '<p>asffasdfdfaf</p><p>asd</p><p>sdsdf</p><p>f</p><p>fdf</p><p>sdf</p><p>f03339298kzulfi968@gmail.com101</p><img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=7&e=kzulfi968%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 7, '2026-02-16 07:19:09'),
(13, 'dostiwelorg@gmail.com', 'Muhammad Zulkif', 'testing campaign', '<p>asffasdfdfaf</p><p>asd</p><p>sdsdf</p><p>f</p><p>fdf</p><p>sdf</p><p>f03339298dostiwelorg@gmail.com211</p><img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=7&e=dostiwelorg%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 7, '2026-02-16 07:19:11'),
(14, 'globaleducationcampaign4@gmail.com', 'Muhammad Zulkif', 'testing campaign', '<p>asffasdfdfaf</p><p>asd</p><p>sdsdf</p><p>f</p><p>fdf</p><p>sdf</p><p>f03338332929globaleducationcampaign4@gmail.comansar</p><img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=7&e=globaleducationcampaign4%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 7, '2026-02-16 07:19:14'),
(15, 'ahme@gmail.com', 'Ahmed', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=ahme%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:16'),
(16, 'aisfs@gmail.com', 'asif', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=aisfs%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:19'),
(17, 'asa@gmail.com', 'ansdu', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=asa%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:21'),
(18, 'jabar@gmail.com', 'jabar', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=jabar%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:24'),
(19, 'muhammadzulkif2001@gmail.com', 'airf kawla', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=muhammadzulkif2001%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:26'),
(20, 'geminidosit@gmail.com', 'amar jhon', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=geminidosit%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:29'),
(21, 'geminidosti@gmail.com', 'amar jj', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=geminidosti%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:31'),
(22, 'trumpaivideo2246@gmail.com', 'aman', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=trumpaivideo2246%40gmail.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:34'),
(23, 'riminig324@2insp.com', 'anuun', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=riminig324%402insp.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:36'),
(24, 'sewik57617@2insp.com', 'asdfsa', 'Test Schedule 1771226338', 'This is a test scheduled email body.<img src=\'http://localhost/Dosti_Voucher_Donors/api/track_open.php?c=8&e=sewik57617%402insp.com\' width=\'1\' height=\'1\' style=\'display:none\' />', 'sent', NULL, 'campaign', 8, '2026-02-16 07:19:41');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `discount_percentage` int(11) DEFAULT 0,
  `custom_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `address`, `discount_percentage`, `custom_price`, `created_at`) VALUES
(14, 'Melting Spots', 'Peshawar', 15, 0.00, '2026-02-05 07:02:24'),
(16, 'KFC', 'pesahwar', 50, 0.00, '2026-02-06 09:48:54'),
(17, 'District 9', 'peshawar', 15, 0.00, '2026-02-06 11:04:33');

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_campaigns`
--

CREATE TABLE `scheduled_campaigns` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `audience_type` enum('donor','shopkeeper') NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduled_campaigns`
--

INSERT INTO `scheduled_campaigns` (`id`, `template_id`, `audience_type`, `scheduled_at`, `status`, `created_at`) VALUES
(1, 1, 'shopkeeper', '2026-02-13 12:45:00', 'sent', '2026-02-13 07:44:46'),
(2, 2, 'donor', '2026-02-16 08:17:58', 'sent', '2026-02-16 07:18:58');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'voucher_price', '500', '2026-02-04 05:18:18', '2026-02-04 05:18:18'),
(2, 'smtp_port', '465', '2026-02-13 05:20:50', '2026-02-13 05:20:50'),
(3, 'smtp_user', 'smtp@campaign2025.org', '2026-02-13 05:20:50', '2026-02-13 05:20:50'),
(4, 'smtp_encryption', 'ssl', '2026-02-13 05:20:50', '2026-02-13 05:27:39'),
(5, 'smtp_pass', '1[1nk3r:@{4_', '2026-02-13 05:20:50', '2026-02-13 05:20:50'),
(6, 'smtp_host', 'mail.campaign2025.org', '2026-02-13 05:20:50', '2026-02-13 05:20:50'),
(7, 'smtp_from_name', 'Dosti Voucher', '2026-02-13 05:20:50', '2026-02-16 06:39:53'),
(8, 'smtp_from_email', 'smtp@campaign2025.org', '2026-02-13 05:20:50', '2026-02-13 05:20:50'),
(30, 'email_template_voucher_body', 'Hello {name},\r\nThis is voucher id {voucher_id}\r\nthank you for your support', '2026-02-13 05:44:14', '2026-02-13 05:44:14'),
(31, 'email_template_voucher_subject', 'voucher active {voucher_id}', '2026-02-13 05:44:14', '2026-02-13 05:44:14'),
(32, 'email_template_box_subject', 'collection scedule for {box_number}', '2026-02-13 05:45:08', '2026-02-13 05:45:08'),
(33, 'email_template_box_body', 'Hello {shop_name},\r\nour team will visit {shop_name} and possible soon', '2026-02-13 05:45:08', '2026-02-13 05:45:08');

-- --------------------------------------------------------

--
-- Table structure for table `voucher_usage`
--

CREATE TABLE `voucher_usage` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `restaurant` enum('MS','D9','GO') NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voucher_usage`
--

INSERT INTO `voucher_usage` (`id`, `donor_id`, `restaurant`, `used_at`) VALUES
(16, 21, 'MS', '2026-02-05 07:03:04'),
(17, 22, 'MS', '2026-02-05 10:17:22'),
(18, 23, 'MS', '2026-02-06 06:15:53'),
(19, 24, 'MS', '2026-02-06 06:24:04'),
(20, 24, 'GO', '2026-02-06 06:25:00'),
(21, 27, 'D9', '2026-02-06 11:05:58'),
(22, 50, 'MS', '2026-02-13 05:51:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_admin_restaurant` (`restaurant_id`);

--
-- Indexes for table `campaign_clicks`
--
ALTER TABLE `campaign_clicks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_id` (`campaign_id`);

--
-- Indexes for table `campaign_opens`
--
ALTER TABLE `campaign_opens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_id` (`campaign_id`);

--
-- Indexes for table `campaign_templates`
--
ALTER TABLE `campaign_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donation_shops`
--
ALTER TABLE `donation_shops`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `box_number` (`box_number`);

--
-- Indexes for table `donation_visits`
--
ALTER TABLE `donation_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shop_id` (`shop_id`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voucher_id` (`voucher_id`);

--
-- Indexes for table `donor_offers`
--
ALTER TABLE `donor_offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `email_campaigns`
--
ALTER TABLE `email_campaigns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email_type` (`email_type`),
  ADD KEY `fk_email_logs_campaign` (`campaign_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scheduled_campaigns`
--
ALTER TABLE `scheduled_campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `voucher_usage`
--
ALTER TABLE `voucher_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `campaign_clicks`
--
ALTER TABLE `campaign_clicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign_opens`
--
ALTER TABLE `campaign_opens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign_templates`
--
ALTER TABLE `campaign_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `donation_shops`
--
ALTER TABLE `donation_shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `donation_visits`
--
ALTER TABLE `donation_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `donor_offers`
--
ALTER TABLE `donor_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `email_campaigns`
--
ALTER TABLE `email_campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `scheduled_campaigns`
--
ALTER TABLE `scheduled_campaigns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `voucher_usage`
--
ALTER TABLE `voucher_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_admin_restaurant` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `campaign_clicks`
--
ALTER TABLE `campaign_clicks`
  ADD CONSTRAINT `campaign_clicks_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `email_campaigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `campaign_opens`
--
ALTER TABLE `campaign_opens`
  ADD CONSTRAINT `campaign_opens_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `email_campaigns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donation_visits`
--
ALTER TABLE `donation_visits`
  ADD CONSTRAINT `donation_visits_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `donation_shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donor_offers`
--
ALTER TABLE `donor_offers`
  ADD CONSTRAINT `donor_offers_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_logs`
--
ALTER TABLE `email_logs`
  ADD CONSTRAINT `fk_email_logs_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `email_campaigns` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `scheduled_campaigns`
--
ALTER TABLE `scheduled_campaigns`
  ADD CONSTRAINT `scheduled_campaigns_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `campaign_templates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `voucher_usage`
--
ALTER TABLE `voucher_usage`
  ADD CONSTRAINT `voucher_usage_ibfk_1` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
