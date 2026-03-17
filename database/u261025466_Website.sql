-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 17, 2026 at 12:49 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u261025466_Website`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `task_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `automation_logs`
--

CREATE TABLE `automation_logs` (
  `id` int(11) NOT NULL,
  `job_type` enum('invoice_generation','payment_reminder','overdue_check') NOT NULL,
  `status` enum('success','error','partial') NOT NULL,
  `details` text DEFAULT NULL,
  `records_processed` int(11) DEFAULT 0,
  `execution_time` decimal(8,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `title`, `subtitle`, `description`, `image_url`, `button_text`, `button_link`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 'Engineering The Digital Future.', 'Welcome to SCL Digital Agency', '', 'https://i.postimg.cc/Jzs75VN7/118081.jpg', 'Download Now', 'https://softwarecreativelabs.com/contactus.html', 1, 0, '2025-10-20 20:46:33', '2026-02-09 21:45:56');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `package_name` varchar(100) DEFAULT NULL,
  `monthly_amount` decimal(10,2) DEFAULT 0.00,
  `next_due_date` date DEFAULT NULL,
  `billing_cycle` enum('monthly','quarterly','annually') DEFAULT 'monthly',
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `password`, `company`, `website`, `phone`, `address`, `package_name`, `monthly_amount`, `next_due_date`, `billing_cycle`, `status`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'client@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Example Corp', NULL, NULL, NULL, 'Premium Package', 5000.00, '2024-10-01', 'monthly', 'active', '2025-09-16 14:41:52', '2025-09-16 14:41:52'),
(2, 'Trevor', 'Trevor@gmail.com', 'Lomboto', 'Lomboto', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-16 14:53:43', '2025-09-16 14:53:43'),
(3, 'Kevin', 'Kevin@gmail.com', 'Kevin', 'Kevin Cars', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-16 14:56:33', '2025-09-16 14:56:33'),
(4, 'David Ayowa', 'davidayowa@gmail.com', 'David', 'Payments 24', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-18 08:30:30', '2025-09-18 08:30:30'),
(5, 'Khanya M', 'Khanya@gmail.com', 'Khanya', 'Khanya M', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-18 12:55:18', '2025-09-18 12:55:18'),
(6, 'Keitumetse', 'klelaka22@gmail.com', 'Keitumetse', 'Koin22studio', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-18 16:34:43', '2025-09-18 16:34:43'),
(7, 'Ukewenza Team', 'adventure@ukwenzavr.com', 'Ukewenza', 'Ukewenza', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-19 12:34:39', '2025-09-19 12:34:39'),
(8, 'Njeri', 'njeri.ndonga@ukwenzavr.com', 'Njeri', 'Njeri', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-19 12:37:10', '2025-09-19 12:37:10'),
(9, 'Jean Pierre Lomboto Lyonga', 'jlomboto@gmail.com', 'Lomboto', 'Lomboto', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-19 12:39:12', '2025-09-19 12:39:12'),
(10, 'Njeri', 'Njeri@gmail.com', 'Njeri', 'Njeri', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-19 12:41:27', '2025-09-19 12:41:27'),
(11, 'Shinsa Lyonga Lomboto', 'shinsalomboto45@gmail.com', 'Shinsa', 'Shinsa\'s Beats', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-19 14:05:35', '2025-09-27 15:54:24'),
(12, 'Ignacius', 'muhammed@rennenauto.co.za', 'muhammed', 'rennenauto', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-23 11:04:35', '2025-09-30 15:03:32'),
(13, 'Lawrence Matjila', 'lawrence@pyralinkaerospace.co.za', 'Lawrence', 'Pyralink Aerospace', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-23 15:49:05', '2025-10-09 15:34:55'),
(14, 'Einari Peura', 'einari.peura@valoa.fi', 'Einari', 'Einari', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-24 07:07:15', '2025-09-24 07:07:15'),
(15, 'Chichi', 'chichi@gmail.com', 'chichi', 'chichi', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-09-26 14:10:07', '2025-09-26 14:10:07'),
(16, 'Jonathan', 'Jonathankabeya04@gmail.com', 'Jonathan', 'Kdeux Clothing', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-02 12:59:02', '2025-10-02 12:59:02'),
(17, 'Dr Benjamin', 'mbksurgery20@gmail.com', 'Benji', 'ActiveCare Family Medical Centre (GP)', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-03 11:15:15', '2025-10-03 11:15:15'),
(18, 'Bianca', 'thebrandbiancah@gmail.com', 'Bianca', 'Maison Bianca', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-04 11:35:24', '2025-10-04 11:35:24'),
(19, 'Marlon Mpoyo', 'mpoyo2016@gmail.com', 'Marlon', 'Marlon Mpoyo', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-09 09:56:24', '2025-10-09 09:56:24'),
(20, 'Carmen Shone', 'Jewelleryartbysimon@gmail.com', 'Carmen', 'Jewelleryartbysimon', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-09 10:53:00', '2025-10-09 10:53:00'),
(21, 'The Best Client Ever', 'TheBestClientEver@gmail.com', 'TheBestClientEver', 'TheBestClientEver', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-11 19:10:09', '2025-10-11 19:10:09'),
(22, 'Thato Styles', '+27738673560@gmail.com', 'Thato', 'Thato Styles', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-17 13:10:14', '2025-10-17 13:10:14'),
(23, 'Thomas Nzenzo', 'tnzenzo@gmail.com', 'Thomas', 'Africa Gateway Tour', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-19 15:00:23', '2025-10-19 15:00:23'),
(24, 'Dr Lengo', 'Lengo@gmail.com', 'Lengo', 'albamcenter@gmail.com', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-19 15:10:16', '2025-10-19 15:10:16'),
(25, 'Trevor Lee', 'Trevakins385@gmail.com', 'Trevor', 'Professional Solar Installers', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-20 11:17:42', '2025-10-20 11:17:42'),
(26, 'Nellz Aliu', 'dapodamos@yahoo.com', 'Nellz', 'NELLZ', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-23 12:42:10', '2025-10-23 12:42:10'),
(27, 'King Mubby', 'mubbyglobalventure@gmail.com', 'King', 'KING', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-23 12:44:27', '2025-10-23 12:44:27'),
(28, 'Mila Mqaga', 'milamqoga@gmail.com', 'Mila', 'Mila', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-23 13:39:31', '2025-10-23 13:39:31'),
(29, 'Hlomla Sidiya', 'hlomlasidiya2@gmail.com', 'Hlomla', 'Hlomla', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-23 13:40:42', '2025-10-23 13:40:42'),
(30, 'Modisha', 'modishamokwenyama@gmail.com', 'Modisha', 'Modisha', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-24 10:22:26', '2025-10-24 10:22:26'),
(31, 'Bernice Patience.', 'Formulatorsdepot@gmail.com', 'Bernice', 'Bernice', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-24 23:09:49', '2025-10-24 23:09:49'),
(32, 'Harvey', 'Harvey@softwarecreativelabs.com', 'Harvey', 'Harvey', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-28 16:58:32', '2025-10-28 16:58:32'),
(33, 'Cape Town Peace Ballers', 'capetownpeaceballers@gmail.com', 'capetown', 'Cape Town Peace Ballers', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-10-31 13:23:55', '2025-10-31 13:23:55'),
(34, 'Dr Betty', 'bolabolioo@gmail.com', 'Betty', 'Dr Betty', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-11-05 16:03:36', '2025-11-05 16:03:36'),
(35, 'Nathan Azba', 'azbabotuli12@gmail.com', 'Nathan', 'Nathan Azba', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-11-06 08:05:18', '2025-11-06 08:05:18'),
(36, 'Constance Chipila', 'constance.chipila@outlook.com', 'Constance', 'Elydez.co.za', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-11-28 18:53:23', '2025-11-28 18:53:23'),
(37, 'Simon Shone', 'Simonsays@hbic.co.za', 'Simon', 'jewellryartbysimon.com', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-01 15:15:38', '2025-12-01 15:15:38'),
(38, 'Benedict Lomboto', 'benelomboto4@gmail.com', 'Ben', 'benelomboto4@gmail.com', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-03 13:19:00', '2025-12-03 13:19:00'),
(39, 'jackpot bt', 'jackjack@gmail.com', 'Lomboto@2', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-03 20:39:11', '2025-12-03 20:39:11'),
(40, 'Mwamba Feza', 'soundclickteam@gmail.com', 'Lomboto@2', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-07 17:54:47', '2025-12-07 17:54:47'),
(41, 'Shinsa Lomboto', 'shinsalombo3to45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-07 18:45:07', '2025-12-07 18:45:07'),
(42, 'Shinsa Lomboto', 'shinsalombweoto45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-07 19:00:38', '2025-12-07 19:00:38'),
(43, 'Shinsa Lomboto', 'soundclissckteam@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-07 19:12:33', '2025-12-07 19:12:33'),
(44, 'Shinsa Lomboto', 'shinsalomboto45f@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 09:33:04', '2025-12-08 09:33:04'),
(45, 'Shinsa Lomboto', 'shinsalombdfdfdfoto45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 16:45:18', '2025-12-08 16:45:18'),
(46, 'Shinsa Lomboto', 'shinsalomboerererto45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 16:49:08', '2025-12-08 16:49:08'),
(47, 'Shinsa Lomboto', 'shinsalo2323mboto45@gmail.com', 'Lomboto@24', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 17:12:34', '2025-12-08 17:12:34'),
(48, 'Shinsa Lomboto', 'shinsalo3434mboto45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 17:28:24', '2025-12-08 17:28:24'),
(49, 'Shinsa Lomboto', 'shins34alomboto45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 17:34:10', '2025-12-08 17:34:10'),
(50, 'Shinsa Lomboto', 'shinsalomweweweboto45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 18:00:08', '2025-12-08 18:00:08'),
(51, 'Shinsa Lomboto', 'shinsalomboto2e45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 18:07:31', '2025-12-08 18:07:31'),
(52, 'Shinsa Lomboto', 'shinsa2323lomboto45@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 18:13:16', '2025-12-08 18:13:16'),
(53, 'Shinsa Lomboto', 'shinsalombotoe45@gmail.com', 'Lomboto@24', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 18:20:12', '2025-12-08 18:20:12'),
(54, 'Shinsa Lomboto', 'shinsalomboto4ddd5@gmail.com', 'Lomboto@25', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-08 18:52:52', '2025-12-08 18:52:52'),
(55, 'Moletlanyi Makatong', 'mozarmakatong@gmail.com', 'Moletlanyi', 'Moletlanyi', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-31 15:55:35', '2025-12-31 15:55:35'),
(56, 'Dr Nezingu Djoni Lengo', 'albamcenter@gmail.com', 'albamcenter', 'Albam Medical Center', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2025-12-31 16:17:29', '2025-12-31 16:17:29'),
(57, 'Blessing', '+27631250268', '+27631250268', '+27631250268', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2026-01-07 15:10:58', '2026-01-07 15:10:58'),
(59, 'Tinashe', 'Tinashe@gmail.com', 'Tinashe@gmail.com', 'Tinashe@gmail.com', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2026-01-13 20:34:39', '2026-01-13 20:34:39'),
(60, 'Lawrence Matjila', 'Pyramiddept@gmail.com', 'Lomboto@2', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2026-01-27 08:26:30', '2026-01-27 08:26:30'),
(61, 'Chadwin Dodo', 'chadwing41@gmail.com', 'chadwing41@gmail.com', 'Chadwin Dodo', NULL, NULL, NULL, NULL, 0.00, NULL, 'monthly', 'active', '2026-02-23 16:32:12', '2026-02-23 16:32:12');

-- --------------------------------------------------------

--
-- Table structure for table `client_files`
--

CREATE TABLE `client_files` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by_type` enum('client','staff') DEFAULT 'client',
  `uploaded_by_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post` varchar(255) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL DEFAULT 'SCL Digital Agency',
  `company_email` varchar(200) NOT NULL DEFAULT 'info@softwarecreativelabs.com',
  `company_phone` varchar(50) DEFAULT '+27 21 123 4567',
  `company_address` text DEFAULT 'Cape Town, South Africa',
  `company_website` varchar(200) DEFAULT 'https://softwarecreativelabs.com',
  `logo_url` varchar(500) DEFAULT '/images/SCL_Blue.png',
  `tax_number` varchar(100) DEFAULT '',
  `registration_number` varchar(100) DEFAULT '',
  `bank_details` text DEFAULT '',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `paypal_client_id` varchar(500) DEFAULT '',
  `paypal_client_secret` varchar(500) DEFAULT '',
  `paypal_mode` enum('sandbox','live') DEFAULT 'sandbox',
  `paypal_enabled` tinyint(1) DEFAULT 0,
  `currency` varchar(10) DEFAULT 'ZAR'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `company_email`, `company_phone`, `company_address`, `company_website`, `logo_url`, `tax_number`, `registration_number`, `bank_details`, `created_at`, `updated_at`, `paypal_client_id`, `paypal_client_secret`, `paypal_mode`, `paypal_enabled`, `currency`) VALUES
(1, 'SCL Digital Agency (Pty) Ltd', 'info@softwarecreativelabs.com', '+27 21 123 4567', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', '', '2025-10-11 23:33:31', '2025-10-11 23:33:31', '', '', 'sandbox', 0, 'ZAR'),
(2, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-10-11 23:35:17', '2025-10-11 23:35:17', '', '', 'sandbox', 0, 'ZAR'),
(3, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-10-11 23:35:22', '2025-10-11 23:35:22', '', '', 'sandbox', 0, 'ZAR'),
(4, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-11-22 19:05:33', '2025-11-22 19:05:33', '', '', 'sandbox', 0, 'ZAR'),
(5, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-07 19:06:14', '2025-12-07 19:06:14', 'AR8HvXVovw6J8Z1NOnhn_GdJxj6ZUEoYMZuNhdMypq06NbZrWekzO6P0M7rq3QUhTJrwE7Ou-5Mkjwbm', 'EIIRotCa8A5JucN9ateD2tt3olvP-lbVUf-HPSesqTeU-UbgFhEam-8qz3wTozxKIZ7MmYGNb_DS4iF_', 'sandbox', 0, 'ZAR'),
(6, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-07 19:20:42', '2025-12-07 19:20:42', 'AbiazvGhTHXe7Hlww5PbvF8I-Mp_0B36vU-vjAJt5jzeRFH4BUrA9PUi0nKn5HC3KRToQPIwbz7uhXLg', 'AbiazvGhTHXe7Hlww5PbvF8I-Mp_0B36vU-vjAJt5jzeRFH4BUrA9PUi0nKn5HC3KRToQPIwbz7uhXLg', 'sandbox', 0, 'ZAR'),
(7, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-08 09:31:43', '2025-12-08 09:31:43', 'AbiazvGhTHXe7Hlww5PbvF8I-Mp_0B36vU-vjAJt5jzeRFH4BUrA9PUi0nKn5HC3KRToQPIwbz7uhXLg', 'AbiazvGhTHXe7Hlww5PbvF8I-Mp_0B36vU-vjAJt5jzeRFH4BUrA9PUi0nKn5HC3KRToQPIwbz7uhXLg', 'sandbox', 1, 'ZAR'),
(8, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-08 09:31:52', '2025-12-08 09:31:52', 'AbiazvGhTHXe7Hlww5PbvF8I-Mp_0B36vU-vjAJt5jzeRFH4BUrA9PUi0nKn5HC3KRToQPIwbz7uhXLg', 'AbiazvGhTHXe7Hlww5PbvF8I-Mp_0B36vU-vjAJt5jzeRFH4BUrA9PUi0nKn5HC3KRToQPIwbz7uhXLg', 'sandbox', 1, 'USD'),
(9, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-08 16:57:39', '2025-12-08 16:57:39', 'AbiazvGhTHXe7Hlww5PbvF8I-Mp_0B36vU-vjAJt5jzeRFH4BUrA9PUi0nKn5HC3KRToQPIwbz7uhXLg', 'EPK8PDhusjGjqweMcYEJ69xvgJea7Ng5VMtj3tBHxcfmLX3iGbKUqztOGCFAupaYBaAEhuXakrhcUu2Y', 'sandbox', 1, 'USD'),
(10, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-08 17:24:58', '2025-12-08 17:24:58', 'AfWwW4y2LhZhURu-IOwN9GC0y_F4oJZws0Gyj_CUYSj3osxEbAxq076hVu2f7d-Acm01x5VcNvJNRFjD', 'EPufHp4E84ofvqiOYrNOyGZGDK5tjV8S44MpsWdMVZrI7yOy0G_7R99JIg93RrwQxRinCxeKwItjI8Au', 'live', 1, 'USD'),
(11, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-08 18:06:13', '2025-12-08 18:06:13', 'AfWwW4y2LhZhURu-IOwN9GC0y_F4oJZws0Gyj_CUYSj3osxEbAxq076hVu2f7d-Acm01x5VcNvJNRFjD', 'EPufHp4E84ofvqiOYrNOyGZGDK5tjV8S44MpsWdMVZrI7yOy0G_7R99JIg93RrwQxRinCxeKwItjI8Au', 'sandbox', 1, 'USD'),
(12, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-08 18:09:52', '2025-12-08 18:09:52', 'AfWwW4y2LhZhURu-IOwN9GC0y_F4oJZws0Gyj_CUYSj3osxEbAxq076hVu2f7d-Acm01x5VcNvJNRFjD', 'EPufHp4E84ofvqiOYrNOyGZGDK5tjV8S44MpsWdMVZrI7yOy0G_7R99JIg93RrwQxRinCxeKwItjI8Au', 'live', 1, 'USD'),
(13, 'SCL Digital Agency (Pty) Ltd', 'contact@softwarecreativelabs.com', '+27 74 019 4690', 'Cape Town, South Africa', 'https://softwarecreativelabs.com', '/images/SCL_Blue.png', '', '', 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', '2025-12-10 21:05:52', '2025-12-10 21:05:52', 'AR8HvXVovw6J8Z1NOnhn_GdJxj6ZUEoYMZuNhdMypq06NbZrWekzO6P0M7rq3QUhTJrwE7Ou-5Mkjwbm', 'EIIRotCa8A5JucN9ateD2tt3olvP-lbVUf-HPSesqTeU-UbgFhEam-8qz3wTozxKIZ7MmYGNb_DS4iF_', 'sandbox', 1, 'USD');

-- --------------------------------------------------------

--
-- Table structure for table `domains`
--

CREATE TABLE `domains` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `status` enum('Active','Pending','Expired','Suspended') DEFAULT 'Pending',
  `expiry_date` date DEFAULT NULL,
  `auto_renewal` tinyint(1) DEFAULT 0,
  `registrar` varchar(255) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `renewal_price` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `domains`
--

INSERT INTO `domains` (`id`, `client_id`, `domain_name`, `status`, `expiry_date`, `auto_renewal`, `registrar`, `registration_date`, `renewal_price`, `notes`, `created_at`, `updated_at`) VALUES
(1, 9, 'tongilmedicare.co.za', 'Active', '2025-10-18', 0, 'Hostinger', '2025-10-08', 300.00, 'testing', '2025-10-20 21:03:24', '2025-10-20 21:03:24'),
(2, 13, 'pyralinkaerospace.co.za', 'Pending', '2025-10-31', 1, 'Godaddy.com', '2025-10-15', 100.00, '', '2025-10-21 00:15:02', '2025-10-21 00:15:02');

-- --------------------------------------------------------

--
-- Table structure for table `email_notifications`
--

CREATE TABLE `email_notifications` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `type` enum('invoice_created','payment_reminder','payment_received','overdue_notice') NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `sent_at` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_settings`
--

CREATE TABLE `email_settings` (
  `id` int(11) NOT NULL,
  `smtp_host` varchar(255) NOT NULL,
  `smtp_port` int(11) NOT NULL DEFAULT 587,
  `smtp_username` varchar(255) NOT NULL,
  `smtp_password` varchar(500) NOT NULL,
  `smtp_encryption` varchar(10) DEFAULT 'tls',
  `from_email` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_settings`
--

INSERT INTO `email_settings` (`id`, `smtp_host`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_encryption`, `from_email`, `from_name`, `updated_at`) VALUES
(1, 'smtp.gmail.com', 587, 'your-email@gmail.com', '', 'tls', 'your-email@gmail.com', 'SCL Digital Agency', '2025-12-10 21:19:16');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `event_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `event_type` varchar(100) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `pdf_url` varchar(500) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `agenda` text DEFAULT NULL,
  `overview` text DEFAULT NULL,
  `map_embed` text DEFAULT NULL,
  `ad_left_url` varchar(500) DEFAULT NULL,
  `ad_left_link` varchar(500) DEFAULT NULL,
  `ad_right_url` varchar(500) DEFAULT NULL,
  `ad_right_link` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `client_id`, `title`, `event_date`, `location`, `status`, `description`, `created_at`, `event_type`, `image_url`, `pdf_url`, `video_url`, `agenda`, `overview`, `map_embed`, `ad_left_url`, `ad_left_link`, `ad_right_url`, `ad_right_link`) VALUES
(1, 11, 'VisionForge 2025: Build. Brand. Scale.', '2026-01-31 06:00:00', 'CTICC', 'published', 'VisionForge 2025 is a creative, tech, and business fusion event designed to bring together\nentrepreneurs, startups, designers, and innovators to explore how powerful branding, design,\nand digital strategy can scale ideas into successful ventures.', '2025-09-25 17:49:09', '', 'https://i.postimg.cc/rmwvMF2D/Abstract-Tech.jpg', 'https://drive.google.com/file/d/1hw7mfmuVI5emz-VlPhOGbWfFiAXPOXDg/view?usp=sharing', 'https://www.youtube.com/embed/75lyN-_bezM?si=MvRp06_hUTgmE2m4', '08:00 – 09:00 | Registration & Welcome Coffee\n\nAttendee check-in\n\nNetworking and refreshments\n\n09:00 – 09:15 | Opening Remarks\n\nWelcome address by [Host/CEO]\n\nOverview of the day’s objectives\n\n09:15 – 10:00 | Keynote: The Future of Digital Innovation\n\nSpeaker: [Industry Leader/Guest Speaker]\n\nFocus on trends in technology, branding, and scaling businesses\n\n10:00 – 10:45 | Panel Discussion: Building Brands That Last\n\nExperts from marketing, design, and tech\n\nQ&A session with attendees\n\n10:45 – 11:00 | Coffee Break & Networking\n11:00 – 12:00 | Workshop: Digital Strategies for Business Growth\n\nInteractive session with case studies\n\nTools and frameworks for building a scalable brand\n\n12:00 – 13:00 | Lunch Break & Networking\n13:00 – 14:00 | Fireside Chat: Entrepreneurial Lessons from Visionary Leaders\n\nConversation with successful founders\n\nSharing insights on challenges, failures, and scaling success\n\n14:00 – 15:00 | Interactive Breakout Sessions\n\nTrack 1: Building Tech Solutions for Modern Businesses\n\nTrack 2: Branding & Marketing in the Digital Era\n\nTrack 3: Scaling Operations and Processes\n\n15:00 – 15:15 | Coffee Break\n15:15 – 16:00 | Case Study Showcase: Real-World Success Stories\n\nSCL DA client projects and results\n\nLessons learned and best practices\n\n16:00 – 16:45 | Panel: The Future of Entrepreneurship & Innovation\n\nIndustry thought leaders discuss emerging opportunities\n\nAudience Q&A\n\n16:45 – 17:00 | Closing Remarks & Takeaways\n\nKey insights from the day\n\nNext steps for attendees\n\n17:00 – 18:00 | Networking & Cocktails\n\nInformal networking\n\nOpportunity to connect with speakers and participants', 'VisionForge 2025: Overview\n\nVisionForge 2025 is a premier event designed for entrepreneurs, innovators, and business leaders looking to build, brand, and scale their ventures in the modern digital landscape. This immersive conference brings together industry experts, visionary speakers, and hands-on workshops to provide actionable insights, strategies, and tools for business growth.\n\nAttendees will gain exposure to the latest trends in technology, branding, and operations, explore real-world case studies, and engage in interactive sessions that foster innovation and collaboration. VisionForge 2025 is not just a conference — it’s a platform to connect, learn, and transform ideas into impactful business outcomes.', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d423817.96640452865!2d18.032263996301417!3d-33.913395563198335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1dcc500f8826eed7%3A0x687fe1fc2828aa87!2sCape%20Town!5e0!3m2!1sen!2sza!4v1759432045582!5m2!1sen!2sza\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 'https://i.postimg.cc/50rkJB3c/search.png', 'https://i.postimg.cc/50rkJB3c/search.png', 'https://i.postimg.cc/50rkJB3c/search.png', 'https://i.postimg.cc/50rkJB3c/search.png');

-- --------------------------------------------------------

--
-- Table structure for table `event_bookings`
--

CREATE TABLE `event_bookings` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `booking_reference` varchar(50) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `ticket_count` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'ZAR',
  `paypal_order_id` varchar(255) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'completed',
  `qr_code_data` text DEFAULT NULL,
  `ticket_pdf_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `event_id`, `booking_reference`, `customer_name`, `customer_email`, `organization`, `ticket_count`, `total_amount`, `currency`, `paypal_order_id`, `payment_status`, `qr_code_data`, `ticket_pdf_path`, `created_at`) VALUES
(1, 1, 'EVT-2025-566967', 'Shinsa Lomboto', 'shinsalomboto45@gmail.com', 'Shinsa\'s Beats', 1, 500.00, 'USD', '6H376812XA7568116', 'completed', 'EVT-2025-566967', NULL, '2025-12-10 21:27:07'),
(2, 1, 'EVT-2025-683223', 'Test User', 'test@example.com', 'Test Org', 1, 500.00, 'ZAR', 'TEST-ORDER-123', 'completed', 'EVT-2025-683223', NULL, '2025-12-10 21:27:43'),
(3, 1, 'EVT-2025-728281', 'Test User', 'test@example.com', 'Test Org', 1, 500.00, 'ZAR', 'TEST-ORDER-123', 'completed', 'EVT-2025-728281', NULL, '2025-12-10 21:28:04'),
(4, 1, 'EVT-2025-661872', 'Test User', 'test@example.com', 'Test Org', 1, 500.00, 'ZAR', 'TEST-ORDER-123', 'completed', 'EVT-2025-661872', NULL, '2025-12-10 21:28:48'),
(5, 1, 'EVT-2025-764390', 'Shinsa Lomboto', 'shinsalomboto45@gmail.com', 'Shinsa\'s Beats', 1, 500.00, 'USD', '8CP730859A827804C', 'completed', 'EVT-2025-764390', NULL, '2025-12-10 21:30:50'),
(6, 1, 'EVT-2025-990769', 'Shinsa Lomboto', 'shinsalomboto45@gmail.com', 'Shinsa\'s Beats', 1, 500.00, 'USD', '40C68475EN6093920', 'completed', 'EVT-2025-990769', 'C:\\xampp\\htdocs\\mostrecent.softwarecreativelabs.com\\api/../tickets/ticket_EVT-2025-990769.html', '2025-12-10 21:38:45'),
(7, 1, 'EVT-2025-473116', 'Shinsa Lomboto', 'soundclickteam@gmail.com', 'www.soundclick.co.za', 1, 500.00, 'USD', '62C1912306603445T', 'completed', 'EVT-2025-473116', 'C:\\xampp\\htdocs\\mostrecent.softwarecreativelabs.com\\api/../tickets/ticket_EVT-2025-473116.html', '2025-12-10 21:46:19'),
(8, 1, 'EVT-2025-404223', 'Shinsa Lomboto', 'shinsalomboto45@gmail.com', 'Shinsa\'s Beats', 1, 500.00, 'USD', '18Y79648B1364045F', 'completed', 'EVT-2025-404223', 'C:\\xampp\\htdocs\\mostrecent.softwarecreativelabs.com\\api/../tickets/ticket_EVT-2025-404223.pdf', '2025-12-10 21:53:21');

-- --------------------------------------------------------

--
-- Table structure for table `event_partners`
--

CREATE TABLE `event_partners` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_speakers`
--

CREATE TABLE `event_speakers` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `position` varchar(200) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('draft','sent','paid','overdue','cancelled') DEFAULT 'draft',
  `due_date` date NOT NULL,
  `issue_date` date NOT NULL,
  `paid_date` datetime DEFAULT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `client_id`, `invoice_number`, `amount`, `tax_amount`, `total_amount`, `status`, `due_date`, `issue_date`, `paid_date`, `description`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 'INV-2025-CA9F49', 2500.00, 0.00, 2500.00, 'sent', '2025-09-16', '2025-09-16', NULL, 'Pay before 09/17/2025', NULL, '2025-09-16 14:54:36', '2025-09-16 14:54:36'),
(2, 3, 'INV-2025-E088D7', 6415000.00, 0.00, 6415000.00, 'paid', '2025-09-16', '2025-09-16', NULL, 'Please make payments before 9/17/2025', NULL, '2025-09-16 14:57:23', '2025-09-16 18:20:00'),
(3, 4, 'INV-2025-942785', 1500.00, 0.00, 1500.00, 'paid', '2025-09-19', '2025-09-18', NULL, 'This is a invoice for a Website Development Project', NULL, '2025-09-18 08:31:40', '2025-09-18 08:34:12'),
(4, 5, 'INV-2025-3B94DB', 50.00, 0.00, 50.00, 'paid', '2025-09-18', '2025-09-18', NULL, 'This is a Invoice , please make payments on due date', NULL, '2025-09-18 12:56:46', '2025-09-18 12:57:57'),
(5, 6, 'INV-2025-67D253', 500.00, 0.00, 500.00, 'sent', '2025-09-19', '2025-09-18', NULL, 'Deposit payment for the development of a custom private booking website (including design, development, calendar sync, secure payment integration, and host dashboard).', NULL, '2025-09-18 16:54:16', '2025-09-18 16:54:16'),
(6, 6, 'INV-2025-F7FDEF', 1000.00, 0.00, 1000.00, 'sent', '2025-09-30', '2025-09-18', NULL, 'Final payment for the development of a custom private booking website (including design, development, calendar sync, secure payment integration, host dashboard, and final deployment).', NULL, '2025-09-18 16:56:38', '2025-09-18 16:56:38'),
(7, 9, 'INV-2025-7AA969', 500.00, 0.00, 500.00, 'sent', '2025-09-21', '2025-09-21', NULL, 'Production of Tongil Medicare Promotional Video, including:\n\nFull-Length Video: A polished promotional advertisement highlighting Oasis Pharmacy and Tongil Pharmacy, their 24/7 services, and community care, based on the approved script.\n\nShort-Form Video: A 30–60 second, social-media-optimized version tailored for platforms such as Facebook, Instagram, TikTok, and WhatsApp Status.\n\nDeliverables include: Scripted narration, editing, visuals, branding integration, and final delivery in requested formats.\n\nProject Timeline: Draft delivery on 21 September 2025, final delivery by 22 September 2025. ', NULL, '2025-09-21 14:49:47', '2025-09-21 14:49:47'),
(8, 9, 'INV-2025-722D34', 500.00, 0.00, 500.00, 'sent', '2025-09-21', '2025-09-21', NULL, 'Production of Tongil Medicare Promotional Video\n\nBank Account Details\n\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', NULL, '2025-09-21 14:50:56', '2025-09-21 14:50:56'),
(9, 14, 'INV-2025-D0F6AC', 258.85, 0.00, 258.85, 'sent', '2025-09-24', '2025-09-24', NULL, 'Domain setup & development services', NULL, '2025-09-24 07:07:55', '2025-09-24 07:07:55'),
(10, 9, 'INV-2025-E28302', 350.00, 0.00, 350.00, 'sent', '2025-09-24', '2025-09-24', NULL, 'Service: Social Media Account Setup (Tongil Medicare)\n\nPlatforms: Facebook, Instagram, LinkedIn, X (Twitter)\n\nIncludes: Account creation, branding (logos, cover images, bios, contact details), optimization of profile settings, and delivery of login details.\n\nTotal: ZAR 350 (Paid in Full)', NULL, '2025-09-24 19:30:53', '2025-09-24 19:30:53'),
(11, 15, 'INV-2025-BB4C92', 30000.00, 0.00, 30000.00, 'sent', '2026-02-26', '2025-09-26', NULL, 'Website Development', NULL, '2025-09-26 14:11:45', '2025-09-26 14:11:45'),
(12, 12, 'INV-2025-E51727', 295.00, 0.00, 295.00, 'sent', '2025-10-01', '2025-09-30', NULL, 'Support Plan / Monthly', NULL, '2025-09-30 15:04:38', '2025-09-30 15:04:38'),
(13, 9, 'INV-2025-C98019', 300.00, 0.00, 300.00, 'sent', '2025-10-02', '2025-10-02', NULL, 'Invoice Description\nProject: Video Production – \"Unité, Vérité et Mission : Le Mouvement de l’Unification face à son destin\"\n\nScope of Work:\n\nPre-production planning (review of script & structure)\n\nVideo recording setup & guidance\n\nEditing of multiple interview sections (7 parts)\n\nIntro & conclusion with text overlays and background music\n\nAddition of visuals/graphics (archival images, titles, subtitles if needed)\n\nFinal export in HD format, ready for distribution', NULL, '2025-10-02 06:20:07', '2025-10-02 06:20:07'),
(14, 16, 'INV-2025-98B344', 100.00, 0.00, 100.00, 'sent', '2025-10-01', '2025-10-02', NULL, 'SCL DA Business Hosting', NULL, '2025-10-02 13:00:13', '2025-10-02 13:00:13'),
(15, 17, 'INV-2025-F6D995', 400.00, 0.00, 400.00, 'sent', '2025-10-03', '2025-10-03', NULL, 'Google Business Profile Optimization\nWe will optimize your Google Business Profile (Maps listing) so that patients can easily find you when they search for terms like “GP Sunningdale”, “doctor Table Bay Mall”, or “family medical centre near me”.\n\nThis includes:\n\nClaiming or updating your Google listing with the correct name, address, phone number, email, and opening hours.\n\nAdding your services and practice details so patients know what you offer.\n\nUploading professional photos of your clinic, team, and location to build trust and attract attention.\n\nAdding keywords that match what local patients are searching for.\n\nShowing you how to collect and manage patient reviews, which helps improve your visibility and reputation online.\n\nWith this setup, you’ll rank higher on Google Maps and search results, making it easier for new patients to find and contact your practice.', NULL, '2025-10-03 11:18:53', '2025-10-03 11:18:53'),
(16, 2, 'INV-2025-551080', 6415000.00, 0.00, 6415000.00, 'sent', '2025-10-03', '2025-10-03', NULL, 'Creative and strategic services provided by SCL Digital Agency, including brand identity development, digital product design (FutaFunds prototype), user experience strategy, technology consulting, and comprehensive brand guidelines. Services also cover creative direction, communication strategy, and integration support, ensuring alignment with financial sector standards and long-term scalability.', NULL, '2025-10-03 11:24:46', '2025-10-03 11:24:46'),
(17, 19, 'INV-2025-06474A', 250.00, 0.00, 250.00, 'sent', '2025-10-09', '2025-10-09', NULL, 'Investment Funding — AI Infrastructure Development (6-Month Period)\n\nThis invoice represents a funding contribution toward SCL Digital Agency’s AI Infrastructure Investment Plan. The funds will be allocated to the development and implementation of advanced artificial intelligence systems, including data processing tools, automation frameworks, and machine learning integrations.\n\nThe investment will support research, software acquisition, cloud infrastructure, and prototype deployment over the next six months to enhance the company’s digital capabilities and scalability.\n\nPurpose: Strategic investment in SCL DA’s AI ecosystem for long-term innovation and operational efficiency.', NULL, '2025-10-09 09:56:55', '2025-10-09 09:56:55'),
(18, 13, 'INV-2025-ACF64A', 100.00, 0.00, 100.00, 'sent', '2025-11-01', '2025-10-09', NULL, 'Web Hosting', NULL, '2025-10-09 15:35:46', '2025-10-09 15:35:46'),
(19, 21, 'INV-2025-3A7365', 10000.00, 0.00, 10000.00, 'sent', '2025-10-11', '2025-10-11', NULL, 'Web Development Services', NULL, '2025-10-11 19:11:07', '2025-10-11 19:11:07'),
(20, 1, 'INV-2025-63AC17', 333.00, 0.00, 333.00, 'sent', '2025-10-11', '2025-10-11', NULL, '333', NULL, '2025-10-11 23:24:46', '2025-10-11 23:24:46'),
(21, 1, 'INV-2025-40D411', 898.00, 0.00, 898.00, 'sent', '2025-10-11', '2025-10-11', NULL, '9', NULL, '2025-10-11 23:26:20', '2025-10-11 23:26:20'),
(22, 4, 'INV-2025-4107F8', 5555.00, 0.00, 5555.00, 'sent', '2025-10-11', '2025-10-11', NULL, '55', NULL, '2025-10-11 23:27:04', '2025-10-11 23:27:04'),
(23, 2, 'INV-2025-62BE9B', 15000.00, 0.00, 15000.00, 'sent', '2025-10-11', '2025-10-11', NULL, 'dwewe', NULL, '2025-10-11 23:30:25', '2025-10-11 23:30:25'),
(24, 1, 'INV-2025-1FAC66', 15000.00, 0.00, 15000.00, 'sent', '2025-10-11', '2025-10-11', NULL, 'wewe', NULL, '2025-10-11 23:30:35', '2025-10-11 23:30:35'),
(25, 1, 'INV-2025-DDADE2', 15000.00, 0.00, 15000.00, 'sent', '2025-10-11', '2025-10-11', NULL, 'bnbn', NULL, '2025-10-11 23:33:56', '2025-10-11 23:33:56'),
(26, 1, 'INV-2025-F4698B', 15000.00, 0.00, 15000.00, 'sent', '2025-10-11', '2025-10-11', NULL, 'bnbn', NULL, '2025-10-11 23:33:57', '2025-10-11 23:33:57'),
(28, 3, 'INV-2025-E6145D', 15000.00, 0.00, 15000.00, 'sent', '2025-10-18', '2025-10-11', NULL, 'f', NULL, '2025-10-11 23:37:07', '2025-10-11 23:37:07'),
(30, 1, 'INV-2025-483384', 30000.00, 0.00, 30000.00, 'sent', '2025-10-11', '2025-10-11', NULL, '5', NULL, '2025-10-11 23:40:08', '2025-10-11 23:40:08'),
(31, 1, 'INV-2025-331BF7', 15000.00, 0.00, 15000.00, 'sent', '2025-10-11', '2025-10-11', NULL, 'd', NULL, '2025-10-11 23:41:12', '2025-10-11 23:41:12'),
(33, 1, 'INV-2025-8E0AFF', 15000.00, 0.00, 15000.00, 'sent', '2025-10-11', '2025-10-11', NULL, 'Please make payments asap', NULL, '2025-10-11 23:45:32', '2025-10-11 23:45:32'),
(36, 9, 'INV-2025-3EFCE8', 500.00, 0.00, 500.00, 'sent', '2025-10-14', '2025-10-14', NULL, 'Video production', NULL, '2025-10-14 11:32:31', '2025-10-14 11:32:31'),
(37, 10, 'INV-2025-87E7F8', 15000.00, 0.00, 15000.00, 'sent', '2025-10-14', '2025-10-14', NULL, 'hjhj', NULL, '2025-10-14 17:11:27', '2025-10-14 17:11:27'),
(38, 7, 'INV-2025-963383', 35000.00, 0.00, 35000.00, 'paid', '2025-10-14', '2025-10-14', NULL, 'fff', NULL, '2025-10-14 17:12:48', '2025-10-14 17:12:48'),
(39, 1, 'INV-2025-E10477', 35000.00, 0.00, 35000.00, '', '2025-10-14', '2025-10-14', NULL, '', NULL, '2025-10-14 17:15:40', '2025-10-14 17:15:40'),
(41, 1, 'INV-2025-AA890B', 15000.00, 0.00, 15000.00, 'sent', '2025-10-14', '2025-10-14', NULL, 'f', NULL, '2025-10-14 17:17:52', '2025-10-14 17:17:52'),
(42, 5, 'INV-2025-35DE89', 35000.00, 0.00, 35000.00, 'sent', '2025-10-14', '2025-10-14', NULL, 'd', NULL, '2025-10-14 17:18:58', '2025-10-14 17:18:58'),
(44, 22, 'INV-2025-454B0F', 399.00, 0.00, 399.00, 'sent', '2025-10-17', '2025-10-17', NULL, 'Domain Registration\nDomain Name: thatostyles.co.za\nR 399.00', NULL, '2025-10-17 13:14:53', '2025-10-17 13:14:53'),
(45, 23, 'INV-2025-7AB7D3', 499.00, 0.00, 499.00, 'sent', '2025-10-19', '2025-10-19', NULL, 'Bank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Domain renewal\n\nDescription:\nThis invoice covers the renewal and continued maintenance of the africagatewaytour.com domain and its associated hosting services for one year. This includes:\n\nDomain renewal through Namecheap to ensure uninterrupted access and ownership.\n\n\n', NULL, '2025-10-19 15:01:58', '2025-10-19 15:01:58'),
(46, 24, 'INV-2025-A8319C', 250.00, 0.00, 250.00, 'sent', '2025-10-19', '2025-10-19', NULL, 'Bank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992\nBranch Code: 250655\nSwift Code: FIRNZAJJ\nReference: Domain renewal\nDescription:\nThis invoice covers the renewal and continued maintenance of the africag', NULL, '2025-10-19 15:11:03', '2025-10-19 15:11:03'),
(47, 24, 'INV-2025-825872', 250.00, 0.00, 250.00, 'sent', '2025-10-19', '2025-10-19', NULL, 'Bank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992\nBranch Code: 250655\nSwift Code: FIRNZAJJ\nReference: Domain renewal\nDescription:\nThis invoice covers the renewal and continued maintenance of the albam.co.za domain', NULL, '2025-10-19 15:11:38', '2025-10-19 15:11:38'),
(48, 25, 'INV-2025-D7B613', 1000.00, 0.00, 1000.00, 'sent', '2025-10-21', '2025-10-20', NULL, 'Invoice Description (Deposit Payment)\n\nItem: Website Development Deposit – Course Platform\nDescription:\nDeposit payment for the design and development of a custom online course platform, including:\n\nAdmin panel for course, user, and payment management\n\nUser registration and login functionality\n\nPayment gateway integration (PayFast / PayPal / Stripe)\n\nResponsive front-end design (HTML, CSS, JavaScript, PHP)\n\nThis deposit secures the project start date and covers the initial design and setup phase.\n\nAmount: R1,000.00\nTotal Project Value: R5,000.00\nBalance Remaining: R4,000.00\n\nPayment Terms: Due upon acceptance of project agreement.', NULL, '2025-10-20 11:18:32', '2025-10-20 11:18:32'),
(49, 7, 'INV-2025-C40587', 599.00, 0.00, 599.00, 'sent', '2025-10-20', '2025-10-20', NULL, 'Transfer of client’s domain (www.nexainternationalschool.com) to SCL Digital Agency (Pty) Ltd for renewal, DNS management, and continued hosting support for the 2025–2026 period.', NULL, '2025-10-20 15:57:10', '2025-10-20 15:57:10'),
(50, 7, 'INV-2025-563F3A', 225.00, 0.00, 225.00, 'sent', '2025-10-20', '2025-10-21', NULL, 'Transfer of client’s domain (www.nexainternationalschool.com) to SCL Digital Agency (Pty) Ltd for renewal, DNS management, and continued hosting support for the 2025–2026 period.', NULL, '2025-10-21 12:07:46', '2025-10-21 12:07:46'),
(51, 30, 'INV-2025-7B9BE5', 3500.00, 0.00, 3500.00, 'sent', '2025-10-20', '2025-10-24', NULL, 'Web Development', NULL, '2025-10-24 10:24:31', '2025-10-24 10:24:31'),
(52, 32, 'INV-2025-F5125C', 150.00, 0.00, 150.00, 'sent', '2025-10-28', '2025-10-28', NULL, 'Project Testing and Deployment', NULL, '2025-10-28 17:00:02', '2025-10-28 17:00:02'),
(53, 16, 'INV-2025-78725E', 100.00, 0.00, 100.00, 'sent', '2025-10-31', '2025-10-31', NULL, 'SCL DA E-Commerce Hosting', NULL, '2025-10-31 13:13:58', '2025-10-31 13:13:58'),
(54, 16, 'INV-2025-3CEE90', 100.00, 0.00, 100.00, 'sent', '2025-10-31', '2025-10-31', NULL, 'SCL DA E-Commerce Hosting - September 2025', NULL, '2025-10-31 13:15:03', '2025-10-31 13:15:03'),
(55, 16, 'INV-2025-5557BB', 100.00, 0.00, 100.00, 'sent', '2025-10-31', '2025-10-31', NULL, 'SCL DA E-commerce Hosting - 01 - 30 November 2025', NULL, '2025-10-31 13:16:29', '2025-10-31 13:16:29'),
(56, 13, 'INV-2025-437020', 100.00, 0.00, 100.00, 'sent', '2025-10-31', '2025-10-31', NULL, 'SCL DA Web Hosting - 01 - 30 November 2025', NULL, '2025-10-31 13:19:55', '2025-10-31 13:19:55'),
(57, 33, 'INV-2025-B48271', 100.00, 0.00, 100.00, 'sent', '2025-10-31', '2025-10-31', NULL, 'Social Media Management Agreement', NULL, '2025-10-31 13:24:20', '2025-10-31 13:24:20'),
(58, 34, 'INV-2025-A06346', 1000.00, 0.00, 1000.00, 'sent', '2025-11-05', '2025-11-05', NULL, 'Creative concept and full billboard design development — from visual direction to final artwork. Includes layout design, branding alignment, and delivery of print-ready files for outdoor advertising.', NULL, '2025-11-05 16:07:11', '2025-11-05 16:07:11'),
(59, 1, 'INV-2025-781F2B', 600.00, 0.00, 600.00, 'sent', '2025-11-06', '2025-11-06', NULL, 'Invoice Description\n\nService: Annual Music Distribution Plan (Unlimited Artist Plan)\nClient: Nathan Azba (Artist Name: Azba Love)\nProvider: SCL DA / SCL DIGITAL AGENCY (PTY) LTD\nDate of Service: 2025/11/06\nAmount Due: R600.00 ZAR (Once-off annual fee)\n\nService Overview:\nThis invoice covers a one-year subscription to SCL Digital Agency’s Music Distribution Service — a premium plan that allows the artist Azba Love to release unlimited music to over 150+ global streaming platforms including Spotify, Apple Music, YouTube Music, Deezer, and more.\n\nPlan Includes:\n\nUnlimited music uploads for 1 artist (Azba Love)\n\n100% artist royalty retention\n\nGlobal distribution to 150+ digital platforms\n\nFree Pre-Save Smartlinks for every release\n\nAutomatic split royalty payments\n\nIn-depth analytics and fan data reports\n\nInstant artist verification on Spotify\n\nPlaylist submission opportunities\n\nFast royalty payouts through Ditto integration\n\nFull access via the Ditto App\n\nAdditional Notes:\nThis service is facilitated by SCL DA / SCL DIGITAL AGENCY (PTY) LTD, a Cape Town–based creative technology company founded by Shinsa Lyonga Lomboto. Established on January 3, 2025, SCL DA specializes in creative digital solutions, including web development, branding, content management, and music technology services.\n\nThe agency’s growth from a small startup to a thriving corporate entity reflects its dedication to innovation, quality, and excellence. Through partnerships with talented artists such as Azba Love, SCL DA continues to expand its influence in the digital entertainment and creative technology sectors.\n\nTotal Amount Payable: R600.00\nPayment Terms: Due upon receipt\nAccepted Payment Methods: \nBank: FNB/RMB\nAccount Holder: *Scl Digital Agency (pty) Ltd\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\nBranch Code: 250655\n', NULL, '2025-11-06 08:10:01', '2025-11-06 08:10:01'),
(60, 35, 'INV-2025-8D5838', 600.00, 0.00, 600.00, 'sent', '2025-11-06', '2025-11-06', NULL, 'Invoice Description\n\nService: Annual Music Distribution Plan (Unlimited Artist Plan)\nClient: Nathan Azba (Artist Name: Azba Love)\nProvider: SCL DA / SCL DIGITAL AGENCY (PTY) LTD\nDate of Service: 2025/11/06\nAmount Due: R600.00 ZAR (Once-off annual fee)\n\nService Overview:\nThis invoice covers a one-year subscription to SCL Digital Agency’s Music Distribution Service — a premium plan that allows the artist Azba Love to release unlimited music to over 150+ global streaming platforms including Spotify, Apple Music, YouTube Music, Deezer, and more.\n\nPlan Includes:\n\nUnlimited music uploads for 1 artist (Azba Love)\n\n100% artist royalty retention\n\nGlobal distribution to 150+ digital platforms\n\nFree Pre-Save Smartlinks for every release\n\nAutomatic split royalty payments\n\nIn-depth analytics and fan data reports\n\nInstant artist verification on Spotify\n\nPlaylist submission opportunities\n\nFast royalty payouts through Ditto integration\n\nFull access via the Ditto App\n\nAdditional Notes:\nThis service is facilitated by SCL DA / SCL DIGITAL AGENCY (PTY) LTD, a Cape Town–based creative technology company founded by Shinsa Lyonga Lomboto. Established on January 3, 2025, SCL DA specializes in creative digital solutions, including web development, branding, content management, and music technology services.\n\nThe agency’s growth from a small startup to a thriving corporate entity reflects its dedication to innovation, quality, and excellence. Through partnerships with talented artists such as Azba Love, SCL DA continues to expand its influence in the digital entertainment and creative technology sectors.\n\nTotal Amount Payable: R600.00\nPayment Terms: Due upon receipt\nAccepted Payment Methods:\nBank: FNB/RMB\nAccount Holder: *Scl Digital Agency (pty) Ltd\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\nBranch Code: 250655', NULL, '2025-11-06 08:10:46', '2025-11-06 08:10:46'),
(61, 6, 'INV-2025-B25A75', 300.00, 0.00, 300.00, 'sent', '2025-11-18', '2025-11-15', NULL, 'fgg', NULL, '2025-11-15 09:38:36', '2025-11-15 09:38:36'),
(62, 35, 'INV-2025-A7E55B', 200.00, 0.00, 200.00, 'sent', '2025-11-18', '2025-11-18', NULL, '', NULL, '2025-11-18 09:55:35', '2025-11-18 09:55:35'),
(63, 35, 'INV-2025-4D0A91', 200.00, 0.00, 200.00, 'sent', '2025-11-18', '2025-11-18', NULL, 'Design services for song promotion graphics', NULL, '2025-11-18 09:56:42', '2025-11-18 09:56:42'),
(64, 9, 'INV-2025-491C83', 765.00, 0.00, 765.00, 'sent', '2025-11-19', '2025-11-19', NULL, 'Banner Design, PDF Preparation & Printing – Full banner design, creation of print-ready PDF, and assistance with getting the banner professionally printed.', NULL, '2025-11-19 10:17:28', '2025-11-19 10:17:28'),
(65, 9, 'INV-2025-4D1C4C', 850.00, 0.00, 850.00, 'sent', '2025-11-26', '2025-11-26', NULL, 'Email Account Configuration for Tongil Medicare & UDP DRC', NULL, '2025-11-26 14:17:04', '2025-11-26 14:17:04'),
(66, 9, 'INV-2025-DE12E1', 850.00, 0.00, 850.00, 'sent', '2025-11-26', '2025-11-26', NULL, 'Email Account Configuration for Tongil Medicare & UDP DRC\n \nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number\n', NULL, '2025-11-26 14:18:47', '2025-11-26 14:18:47'),
(67, 6, 'INV-2025-3C25AE', 300.00, 0.00, 300.00, 'sent', '2025-11-28', '2025-11-28', NULL, 'Project: Testing Phase – Host Dashboard & QA\nDetails:\nPayment received for partial completion of the Testing Phase, which includes:\n\nFunctional Testing & QA\n\nBug Fixes and Mobile/Browser Compatibility Checks\n\nClient Review & Feedback Round\n\nFinal Walkthrough and Revisions\n\nGo-Live Preparation, Training Session & Documentation\n\nAmount:\n\nR300 – Already Paid\n\nR1,000 – Remaining Balance Due', NULL, '2025-11-28 18:33:30', '2025-11-28 18:33:30'),
(68, 13, 'INV-2025-3DB4FD', 100.00, 0.00, 100.00, 'sent', '2025-11-30', '2025-11-28', NULL, 'Web Hosting – December 2025\nMonthly hosting fee: R100', NULL, '2025-11-28 18:43:36', '2025-11-28 18:43:36'),
(69, 16, 'INV-2025-A26A64', 150.00, 0.00, 150.00, 'sent', '2025-11-30', '2025-11-28', NULL, 'Web Hosting – December 2025\nMonthly hosting fee: R100\nWebsite updates fee: R50', NULL, '2025-11-28 18:45:33', '2025-11-28 18:45:33'),
(70, 16, 'INV-2025-DB4DC5', 150.00, 0.00, 150.00, 'sent', '2025-11-30', '2025-11-28', NULL, 'Web Hosting – December 2025\nMonthly hosting fee: R100\nWebsite updates fee: R50\n\nBank Account Details\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', NULL, '2025-11-28 18:46:46', '2025-11-28 18:46:46'),
(71, 36, 'INV-2025-557085', 150.00, 0.00, 150.00, 'sent', '2025-11-30', '2025-11-28', NULL, 'Web Hosting Services (December 2025)\nIncludes standard monthly hosting for elydez.co.za (R100) and account reactivation fee (R50).', NULL, '2025-11-28 18:53:50', '2025-11-28 18:53:50'),
(72, 36, 'INV-2025-19A8ED', 150.00, 0.00, 150.00, 'sent', '2025-11-30', '2025-11-28', NULL, 'Web Hosting Services (December 2025)\nIncludes standard monthly hosting for elydez.co.za (R100) and account reactivation fee (R50).\n\nBank Account Details\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', NULL, '2025-11-28 18:54:12', '2025-11-28 18:54:12'),
(73, 20, 'INV-2025-DD3B4D', 100.00, 0.00, 100.00, 'sent', '2025-11-30', '2025-11-29', NULL, 'Web Hosting services - December 2025\nAmount: ZAR 100\n\n\nBank Account Details\n\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number\n\n', NULL, '2025-11-29 12:10:16', '2025-11-29 12:10:16'),
(74, 37, 'INV-2025-53D63C', 100.00, 0.00, 100.00, 'sent', '2025-12-01', '2025-12-01', NULL, 'Web Hosting % Maintenance for - jewellryartbysimon.com', NULL, '2025-12-01 15:16:19', '2025-12-01 15:16:19'),
(75, 37, 'INV-2025-01FEC4', 100.00, 0.00, 100.00, 'sent', '2025-12-01', '2025-12-01', NULL, 'Web Hosting & Maintenance for - jewellryartbysimon.com\n\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number\n\n', NULL, '2025-12-01 15:20:03', '2025-12-01 15:20:03'),
(76, 38, 'INV-2025-461FF9', 299.00, 0.00, 299.00, 'sent', '2025-12-03', '2025-12-03', NULL, 'Professional Business Card Design', NULL, '2025-12-03 13:19:56', '2025-12-03 13:19:56'),
(77, 38, 'INV-2025-273FB2', 299.00, 0.00, 299.00, 'sent', '2025-12-03', '2025-12-03', NULL, 'Professional Business Card Design\n\n\nBank Account Details\n\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number\n', NULL, '2025-12-03 13:21:17', '2025-12-03 13:21:17'),
(78, 13, 'INV-2025-BD13C2', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-30', NULL, 'Web Hosting & Maintenance for - pyralinkaerospace.co.za ( 1 - 31 January 2025\nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992\nBranch Code: 250655\nSwift Code: FIRNZAJJ\nReference: Your Invoice Number', NULL, '2025-12-30 12:44:54', '2025-12-30 12:44:54'),
(79, 16, 'INV-2025-7024B2', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-30', NULL, 'Web Hosting & Maintenance for - kdeuxclothing.com ( 1 - 31 January 2025 )\nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992\nBranch Code: 250655\nSwift Code: FIRNZAJJ\nReference: Your Invoice Number\n\n', NULL, '2025-12-30 12:47:46', '2025-12-30 12:47:46'),
(80, 37, 'INV-2025-E655E7', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-30', NULL, 'Web Hosting & Maintenance for - jewellryartbysimon.com ( 1 - 31 January 2025 ) Bank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 Branch Code: 250655\nSwift Code: FIRNZAJJ Reference: Your Invoice Number', NULL, '2025-12-30 12:53:49', '2025-12-30 12:53:49'),
(81, 12, 'INV-2025-F1B5D5', 295.00, 0.00, 295.00, 'sent', '2025-12-31', '2025-12-30', NULL, 'Web Hosting & Maintenance for - rennenauto.co.za ( 1 - 31 January 2025 ) \nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 Branch Code: 250655 Swift Code: FIRNZAJJ Reference: Your Invoice Number', NULL, '2025-12-30 13:05:37', '2025-12-30 13:05:37'),
(82, 36, 'INV-2025-DD98DF', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-30', NULL, 'Web Hosting & Maintenance for - elydez.co.za ( 1 - 31 January 2025 ) \nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 Branch Code: 250655\nSwift Code: FIRNZAJJ Reference: Your Invoice Number', NULL, '2025-12-30 19:47:19', '2025-12-30 19:47:19'),
(83, 55, 'INV-2025-D1CB48', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-31', NULL, 'Web Hosting & Maintenance for - www.kagopele.co.za ( 1 - 31 January 2026 ) Bank\nName: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 Branch Code: 250655\nSwift Code: FIRNZAJJ Reference: Your Invoice Number', NULL, '2025-12-31 15:57:07', '2025-12-31 15:57:07'),
(84, 23, 'INV-2025-2EE2C2', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-31', NULL, 'Web Hosting & Maintenance for - africagatewaytour.com ( 1 - 31 January 2025 ) \nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 \nBranch Code: 250655\nSwift Code: FIRNZAJJ \nReference: Your Invoice Number', NULL, '2025-12-31 16:05:30', '2025-12-31 16:05:30'),
(85, 23, 'INV-2025-CC43CF', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-31', NULL, 'Web Hosting & Maintenance for - africagatewaytour.com ( 1 - 31 January 2026 ) \nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 \nBranch Code: 250655\nSwift Code: FIRNZAJJ \nReference: Your Invoice Number', NULL, '2025-12-31 16:08:15', '2025-12-31 16:08:15'),
(86, 23, 'INV-2025-BAC72D', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-31', NULL, 'Web Hosting & Maintenance for - africagatewaytour.com ( 1 - 31 January 2026 ) \nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 \nBranch Code: 250655\nSwift Code: FIRNZAJJ \nReference: Your Invoice Number', NULL, '2025-12-31 16:10:27', '2025-12-31 16:10:27'),
(87, 56, 'INV-2025-5B627E', 100.00, 0.00, 100.00, 'sent', '2026-01-01', '2025-12-31', NULL, 'Web Hosting & Maintenance for - albam.co.za ( 1 - 31 January 2026 ) \nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 \nBranch Code: 250655\nSwift Code: FIRNZAJJ \nReference: Your Invoice Number', NULL, '2025-12-31 16:19:10', '2025-12-31 16:19:10'),
(88, 9, 'INV-2025-94C8FB', 295.00, 0.00, 295.00, 'sent', '2026-01-01', '2025-12-31', NULL, 'Web Hosting & Maintenance for - tongilmedicare.co.za | familypeaceassociation.co.za | udp-drc.africa ( 1 - 31 January 2026 ) \nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 \nBranch Code: 250655\nSwift Code: FIRNZAJJ \nReference: Your Invoice Number', NULL, '2025-12-31 16:23:20', '2025-12-31 16:23:20'),
(89, 57, 'INV-2026-4CB587', 100.00, 0.00, 100.00, 'sent', '2026-01-07', '2026-01-07', NULL, 'Blessing - Pictures at Arderne Gardens , Cape Town - R100 Package', NULL, '2026-01-07 15:17:01', '2026-01-07 15:17:01'),
(90, 59, 'INV-2026-728567', 2500.00, 0.00, 2500.00, 'sent', '2026-01-31', '2026-01-13', NULL, 'Professional Web Development', NULL, '2026-01-13 20:36:04', '2026-01-13 20:36:04'),
(91, 12, 'INV-2026-679107', 3000.00, 0.00, 3000.00, 'sent', '2026-02-02', '2026-02-02', NULL, 'Renne Autoteile site to high-conversion platform. Includes UI/UX overhaul, mobile responsiveness, and SEO infrastructure.\n	\nTotal Project Value	:	R3,000.00\n\nDeposit Due (30%)	\nRequired to secure start date and commence Phase I.	R900.00\nBalance Due	\nPayable upon final review & site handover.	R2,100.00', NULL, '2026-02-02 14:22:28', '2026-02-02 14:22:28'),
(92, 1, 'INV-2026-5B32BF', 3000.00, 0.00, 3000.00, 'sent', '2026-02-02', '2026-02-02', NULL, 'Renne Autoteile site  [ rennenauto.co.za ] to high-conversion platform. \n\nIncludes UI/UX overhaul, mobile responsiveness, and SEO infrastructure.\",\"R3,000.00\"\n,,\nTotal Project Value,,\"R3,000.00\"\n\nDeposit Due (30%),\n\nRequired to secure start date and commence Phase I.,R900.00\n\nBalance Due,\nPayable upon final review & site handover.,\"R2,100.00\"\n\n\nBank Account Details \nBank Name: FNB / RMB \nAccount Holder: SCL Digital Agency (Pty) Ltd \nAccount Type: Gold Business Account \nAccount Number: 63134146992 \nBranch Code: 250655 \nSwift Code: FIRNZAJJ \nReference: Your Invoice Number', NULL, '2026-02-02 14:32:58', '2026-02-02 14:32:58'),
(93, 12, 'INV-2026-7D4BAA', 3000.00, 0.00, 3000.00, 'sent', '2026-02-02', '2026-02-02', NULL, 'Renne Autoteile site [ rennenauto.co.za ] to high-conversion platform.\n\nIncludes UI/UX overhaul, mobile responsiveness, and SEO infrastructure.\",\"R3,000.00\"\n,,\nTotal Project Value,,\"R3,000.00\"\n\nDeposit Due (30%),\n\nRequired to secure start date and commence Phase I.,R900.00\n\nBalance Due,\nPayable upon final review & site handover.,\"R2,100.00\"\n\n\nBank Account Details\nBank Name: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992\nBranch Code: 250655\nSwift Code: FIRNZAJJ\nReference: Your Invoice Numbe', NULL, '2026-02-02 14:33:23', '2026-02-02 14:33:23'),
(94, 12, 'INV-2026-B82E3B', 3000.00, 0.00, 3000.00, 'sent', '2026-02-03', '2026-02-03', NULL, 'Project Scope & Payment Terms\nDescription: Renne Autoteile site [rennenauto.co.za] to high-conversion platform. Includes UI/UX overhaul, mobile responsiveness, and SEO infrastructure. \n\n\nTotal Project Value: R3,000.00 \n\n\nDeposit Due (30%): R900.00 (Required to secure start date and commence Phase I) \n\n\nBalance Due: R2,100.00 (Payable upon final review & site handover)\n\n\n\nRennen Autoteile Website features.\n1. Implementation of all desktop features on the mobile version and clean up. THAT IS:\n- Video in hero banner.\n- Call now and Shop banner\n- Fully functional and clearly visible “find your car” menu\n- Search function with “see more” button at the bottom the search\nsuggestions list that leads to filtered search results page\n- Gallery: Picture in Picture gallery. (Each picture acts as a clickable folder that\ntakes has more images in it)\n- Contact us (every query should be sent to two emails that work – this\ncurrently does not work.) New emails have been updated in the back end,\nbut the query form still sends to the old email pre update\n- Instagram posts embedded at the bottom of the page. Containers centred\nwith each automatic scroll. 2. DesktopFeatures\n- Search function does not work on about us page and gallery page. (Fix)\n- Drop down search suggestions don’t show in search bar on the “Search\nresults” page (Please enable and add “see more” button)\n- Add “see more” button to the bottom of search suggestions list that takes us\nto filtered “Search results” page with the keyword in it.\n- Gallery: Create a Picture in Picture gallery. (Each picture acts as a clickable\nfolder that takes has more images in it)\n- About us page. Enable more Pictures and Text. I want to have more pictures\nof the team in action.\n- Contact us (every query should be sent to two emails that work – this\ncurrently does not work.) New emails have been updated in the back end, but the query form still sends to the old email pre-update.\n- Backend product linking to car is very slow. Please optimize\n- The YT shorts video container is covered by the images of the car. Can we trip\nthe container or just restrict the images to the gallery part of this rather than under image and video and gallery.\n\n\nBank Account Details\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', NULL, '2026-02-03 08:01:20', '2026-02-03 08:01:20'),
(95, 37, 'INV-2026-BE6F59', 100.00, 0.00, 100.00, 'sent', '2026-02-01', '2026-02-05', NULL, 'Web Hosting & Maintenance for - jewellryartbysimon.com ( 1 - 28 February 2026 )\nBank\nName: FNB / RMB\nAccount Holder: SCL Digital Agency (Pty) Ltd\nAccount Type: Gold Business Account\nAccount Number: 63134146992 Branch Code: 250655\nSwift Code: FIRNZAJJ Reference: Your Invoice Number', NULL, '2026-02-05 14:02:34', '2026-02-05 14:02:34'),
(96, 35, 'INV-2026-CC9EC0', 250.00, 0.00, 250.00, 'sent', '2026-02-05', '2026-02-05', NULL, 'R250 Payment – YouTube Song Release Package\nIncludes full song release on the YouTube platform with complete video production services.\n\n\nBank Account Details\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', NULL, '2026-02-05 14:12:03', '2026-02-05 14:12:03'),
(97, 60, 'INV-2026-958CE7', 200.00, 0.00, 200.00, 'sent', '2026-02-05', '2026-02-05', NULL, 'invoice  for the January & February 2026 web hosting & maintenance for https://pyralinkaerospace.co.za.', NULL, '2026-02-05 14:15:29', '2026-02-05 14:15:29'),
(98, 6, 'INV-2026-FF7ADF', 1000.00, 0.00, 1000.00, 'sent', '2026-02-13', '2026-02-11', NULL, 'Project: Website Development – luxuryapartments.kopanoyarona.com Description:\n\nFinal Milestone Payment: Completion of website development and functional testing phase.\n\nDeployment: Migration to live environment and configuration of the Admin Panel.\n\nHandover: Delivery of final source files, access credentials, and project documentation.\n\nBalance Due: ZAR 1,000.00', NULL, '2026-02-11 19:01:33', '2026-02-11 19:01:33'),
(99, 12, 'INV-2026-C27F2F', 2100.00, 0.00, 2100.00, 'sent', '2026-02-27', '2026-02-27', NULL, 'Full and final settlement – Website feature upgrade & optimization (rennenauto.co.za)', NULL, '2026-02-27 10:00:08', '2026-02-27 10:00:08'),
(100, 12, 'INV-2026-09FBCE', 2100.00, 0.00, 2100.00, 'sent', '2026-02-27', '2026-02-27', NULL, 'Full and final settlement – Website feature upgrade & optimization (rennenauto.co.za)\n\nBank Account Details\nBank Name: FNB / RMB\n\nAccount Holder: SCL Digital Agency (Pty) Ltd\n\nAccount Type: Gold Business Account\n\nAccount Number: 63134146992\n\nBranch Code: 250655\n\nSwift Code: FIRNZAJJ\n\nReference: Your Invoice Number', NULL, '2026-02-27 10:01:04', '2026-02-27 10:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(8,2) DEFAULT 1.00,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `description`, `quantity`, `unit_price`, `total_price`, `created_at`) VALUES
(2, 33, '\n                                    SecurePay Gateway', 1.00, 15000.00, 15000.00, '2025-10-11 23:45:32'),
(6, 37, '\n                                    SCL DA eCommerce Development', 1.00, 15000.00, 15000.00, '2025-10-14 17:11:27'),
(7, 38, '\n                                    SCL DA Web Development', 1.00, 35000.00, 35000.00, '2025-10-14 17:12:48'),
(8, 39, '\n                                    SCL DA Web Development', 1.00, 35000.00, 35000.00, '2025-10-14 17:15:40'),
(10, 41, '\n                                    SCL DA eCommerce Development', 1.00, 15000.00, 15000.00, '2025-10-14 17:17:52'),
(11, 42, '\n                                    SCL DA Web Development', 1.00, 35000.00, 35000.00, '2025-10-14 17:18:58'),
(12, 74, '\n                                    Web Hosting & Maintenance', 1.00, 449.00, 449.00, '2025-12-01 15:16:19'),
(13, 75, '\n                                    Web Hosting & Maintenance', 1.00, 449.00, 449.00, '2025-12-01 15:20:03'),
(14, 81, '\n                                    Digiboost', 1.00, 295.00, 295.00, '2025-12-30 13:05:37'),
(15, 88, '\n                                    Digiboost', 1.00, 295.00, 295.00, '2025-12-31 16:23:20');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  `from_type` enum('client','staff') NOT NULL,
  `to_type` enum('client','staff') NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE `milestones` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `status` enum('pending','in_progress','completed','overdue','on_hold') DEFAULT 'pending',
  `progress` int(11) DEFAULT 0 CHECK (`progress` >= 0 and `progress` <= 100),
  `due_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `gateway` enum('payfast','stripe','paypal','manual','bank_transfer') NOT NULL,
  `gateway_transaction_id` varchar(255) DEFAULT NULL,
  `gateway_reference` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `paypal_orders`
--

CREATE TABLE `paypal_orders` (
  `id` int(11) NOT NULL,
  `paypal_order_id` varchar(255) NOT NULL,
  `capture_id` varchar(255) DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `payer_email` varchar(200) DEFAULT NULL,
  `raw_response` longtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `paypal_orders`
--

INSERT INTO `paypal_orders` (`id`, `paypal_order_id`, `capture_id`, `amount`, `currency`, `status`, `payer_email`, `raw_response`, `created_at`) VALUES
(1, '1LD999132H299194R', '6KN74521B8358933U', 0.01, 'USD', 'COMPLETED', 'soundclickteam@gmail.com', '{\"id\":\"1LD999132H299194R\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"soundclickteam@gmail.com\",\"account_id\":\"KKE5CNF4R5NT8\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Digiboost - Custom Platform\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Digiboost - Custom Platform\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"6KN74521B8358933U\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/6KN74521B8358933U\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/6KN74521B8358933U\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/1LD999132H299194R\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-08T17:03:27Z\",\"update_time\":\"2025-12-08T17:03:27Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"soundclickteam@gmail.com\",\"payer_id\":\"KKE5CNF4R5NT8\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-08T17:02:07Z\",\"update_time\":\"2025-12-08T17:03:27Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/1LD999132H299194R\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-08 17:03:29'),
(2, '8DS332439N778403F', '92514867A8802373H', 0.01, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"8DS332439N778403F\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"3LZGATMER5TML\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Digiboost - Site\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Digiboost - Site\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"address_line_2\":\"2\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"92514867A8802373H\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/92514867A8802373H\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/92514867A8802373H\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/8DS332439N778403F\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-08T17:14:12Z\",\"update_time\":\"2025-12-08T17:14:12Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"3LZGATMER5TML\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-08T17:13:14Z\",\"update_time\":\"2025-12-08T17:14:12Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/8DS332439N778403F\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-08 17:14:13'),
(3, '3GR10793F5094390V', '2EP621682P3106903', 0.01, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"3GR10793F5094390V\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"M4C5HGL3WZDWE\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Digiboost - Custom Platform\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Digiboost - Custom Platform\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"address_line_2\":\"plumstead\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"2EP621682P3106903\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/2EP621682P3106903\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/2EP621682P3106903\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/3GR10793F5094390V\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-08T17:17:51Z\",\"update_time\":\"2025-12-08T17:17:51Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"M4C5HGL3WZDWE\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-08T17:16:50Z\",\"update_time\":\"2025-12-08T17:17:51Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/3GR10793F5094390V\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-08 17:17:52'),
(4, '3JR0368114171941V', '8AY085948K144070F', 0.01, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"3JR0368114171941V\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"AKXVLKVEEHH7L\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"shinsa@softwarecreativelabs.com\",\"merchant_id\":\"E8VN9PH59TUHC\"},\"description\":\"Digiboost - Site\",\"soft_descriptor\":\"PAYPAL *SCLDIGITALA\",\"items\":[{\"name\":\"Digiboost - Site\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve, 3\",\"address_line_2\":\"g\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"8AY085948K144070F\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/8AY085948K144070F\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/8AY085948K144070F\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/3JR0368114171941V\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-08T18:22:34Z\",\"update_time\":\"2025-12-08T18:22:34Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"AKXVLKVEEHH7L\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-08T18:20:20Z\",\"update_time\":\"2025-12-08T18:22:34Z\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/3JR0368114171941V\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-08 18:22:36'),
(5, '6R484214HW587531S', '8VB073473W8709104', 0.01, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"6R484214HW587531S\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"8Z8NB3AXSBZ68\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"shinsa@softwarecreativelabs.com\",\"merchant_id\":\"E8VN9PH59TUHC\"},\"description\":\"Digiboost - E-Commerce\",\"soft_descriptor\":\"PAYPAL *SCLDIGITALA\",\"items\":[{\"name\":\"Digiboost - E-Commerce\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve, 3\",\"address_line_2\":\"e\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"8VB073473W8709104\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"0.01\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/8VB073473W8709104\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/8VB073473W8709104\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/6R484214HW587531S\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-08T18:54:30Z\",\"update_time\":\"2025-12-08T18:54:30Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"8Z8NB3AXSBZ68\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-08T18:53:11Z\",\"update_time\":\"2025-12-08T18:54:30Z\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/6R484214HW587531S\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-08 18:54:31'),
(6, '6MP385273H044341R', '7U257547FM5437316', 500.00, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"6MP385273H044341R\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"NGH8Y7YUEDB9A\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Event Ticket\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Event Ticket\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"address_line_2\":\"f\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"7U257547FM5437316\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"17.30\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"482.70\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/7U257547FM5437316\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/7U257547FM5437316\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/6MP385273H044341R\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-10T21:21:16Z\",\"update_time\":\"2025-12-10T21:21:16Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"NGH8Y7YUEDB9A\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-10T21:20:12Z\",\"update_time\":\"2025-12-10T21:21:16Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/6MP385273H044341R\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-10 21:21:18'),
(7, '6H376812XA7568116', '3D22360857660135N', 500.00, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"6H376812XA7568116\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"7MPEWEJY38NTS\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Event Ticket\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Event Ticket\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"address_line_2\":\"x\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"3D22360857660135N\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"17.30\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"482.70\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/3D22360857660135N\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/3D22360857660135N\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/6H376812XA7568116\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-10T21:27:05Z\",\"update_time\":\"2025-12-10T21:27:05Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"7MPEWEJY38NTS\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-10T21:26:16Z\",\"update_time\":\"2025-12-10T21:27:05Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/6H376812XA7568116\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-10 21:27:07'),
(8, '8CP730859A827804C', '0YN35479VS828143L', 500.00, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"8CP730859A827804C\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"4XK78ALPAGKSW\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Event Ticket\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Event Ticket\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"address_line_2\":\"t\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"0YN35479VS828143L\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"17.30\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"482.70\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/0YN35479VS828143L\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/0YN35479VS828143L\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/8CP730859A827804C\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-10T21:30:48Z\",\"update_time\":\"2025-12-10T21:30:48Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"4XK78ALPAGKSW\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-10T21:29:54Z\",\"update_time\":\"2025-12-10T21:30:48Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/8CP730859A827804C\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-10 21:30:49'),
(9, '40C68475EN6093920', '03R11550NS209063M', 500.00, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"40C68475EN6093920\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"D7H674ZHXUQRU\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Event Ticket\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Event Ticket\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"address_line_2\":\"z\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"03R11550NS209063M\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"17.30\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"482.70\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/03R11550NS209063M\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/03R11550NS209063M\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/40C68475EN6093920\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-10T21:38:43Z\",\"update_time\":\"2025-12-10T21:38:43Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"D7H674ZHXUQRU\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-10T21:37:45Z\",\"update_time\":\"2025-12-10T21:38:43Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/40C68475EN6093920\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-10 21:38:45'),
(10, '62C1912306603445T', '71M86906C9822511M', 500.00, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"62C1912306603445T\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"9VDTZUSES7BHU\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Event Ticket\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Event Ticket\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"address_line_2\":\"r\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"71M86906C9822511M\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"17.30\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"482.70\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/71M86906C9822511M\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/71M86906C9822511M\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/62C1912306603445T\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-10T21:46:17Z\",\"update_time\":\"2025-12-10T21:46:17Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"9VDTZUSES7BHU\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-10T21:45:17Z\",\"update_time\":\"2025-12-10T21:46:17Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/62C1912306603445T\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-10 21:46:19'),
(11, '18Y79648B1364045F', '3VP854176D5284830', 500.00, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"18Y79648B1364045F\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"ULE2HG7PLC4BC\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Event Ticket\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Event Ticket\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve\",\"address_line_2\":\"f\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"3VP854176D5284830\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"500.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"17.30\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"482.70\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/3VP854176D5284830\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/3VP854176D5284830\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/18Y79648B1364045F\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-12-10T21:53:20Z\",\"update_time\":\"2025-12-10T21:53:20Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"ULE2HG7PLC4BC\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2025-12-10T21:52:29Z\",\"update_time\":\"2025-12-10T21:53:20Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/18Y79648B1364045F\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-12-10 21:53:21'),
(12, '6YP16901JW2236109', '2XH25680Y9615080T', 1.00, 'USD', 'COMPLETED', 'shinsalomboto45@gmail.com', '{\"id\":\"6YP16901JW2236109\",\"intent\":\"CAPTURE\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shinsalomboto45@gmail.com\",\"account_id\":\"6UR3QB4UEV2FY\",\"account_status\":\"UNVERIFIED\",\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"address\":{\"country_code\":\"ZA\"}}},\"purchase_units\":[{\"reference_id\":\"default\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"1.00\",\"breakdown\":{\"item_total\":{\"currency_code\":\"USD\",\"value\":\"1.00\"},\"shipping\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"handling\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"insurance\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"shipping_discount\":{\"currency_code\":\"USD\",\"value\":\"0.00\"}}},\"payee\":{\"email_address\":\"sb-a6ynb27809301@business.example.com\",\"merchant_id\":\"U45UJJXTXEEH2\"},\"description\":\"Digiboost - Lite\",\"soft_descriptor\":\"PAYPAL *TEST STORE\",\"items\":[{\"name\":\"Digiboost - Lite\",\"unit_amount\":{\"currency_code\":\"USD\",\"value\":\"1.00\"},\"tax\":{\"currency_code\":\"USD\",\"value\":\"0.00\"},\"quantity\":\"1\"}],\"shipping\":{\"name\":{\"full_name\":\"Shinsa Lomboto\"},\"address\":{\"address_line_1\":\"Muizenburg, Villa D\'Algarve, 3\",\"admin_area_2\":\"Cape Town\",\"admin_area_1\":\"Western Cape\",\"postal_code\":\"7945\",\"country_code\":\"ZA\"}},\"supplementary_data\":{\"tax_nexus\":[]},\"payments\":{\"captures\":[{\"id\":\"2XH25680Y9615080T\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"1.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"NOT_ELIGIBLE\"},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"1.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"0.33\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"0.67\"}},\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/2XH25680Y9615080T\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/payments\\/captures\\/2XH25680Y9615080T\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/6YP16901JW2236109\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2026-01-02T12:43:51Z\",\"update_time\":\"2026-01-02T12:43:51Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Shinsa\",\"surname\":\"Lomboto\"},\"email_address\":\"shinsalomboto45@gmail.com\",\"payer_id\":\"6UR3QB4UEV2FY\",\"address\":{\"country_code\":\"ZA\"}},\"create_time\":\"2026-01-02T12:41:45Z\",\"update_time\":\"2026-01-02T12:43:51Z\",\"links\":[{\"href\":\"https:\\/\\/api.sandbox.paypal.com\\/v2\\/checkout\\/orders\\/6YP16901JW2236109\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2026-01-02 12:43:50');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price_amount` decimal(10,2) DEFAULT 0.00,
  `price_period` varchar(50) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `reviews_count` int(11) DEFAULT 0,
  `image_url` varchar(500) DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `background_url` varchar(500) DEFAULT NULL,
  `badge` varchar(50) DEFAULT NULL,
  `cta_primary_text` varchar(100) DEFAULT NULL,
  `cta_primary_link` varchar(500) DEFAULT NULL,
  `cta_secondary_text` varchar(100) DEFAULT NULL,
  `cta_secondary_link` varchar(500) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `category`, `description`, `price_amount`, `price_period`, `rating`, `reviews_count`, `image_url`, `icon_class`, `background_url`, `badge`, `cta_primary_text`, `cta_primary_link`, `cta_secondary_text`, `cta_secondary_link`, `is_featured`, `created_at`) VALUES
(5, 'Solo7', 'Solo7', 'Solo7 powered by SCL DA.', 9.99, 'month', 0.00, 0, 'https://i.postimg.cc/pL39xjQQ/Solo-Icon.jpg', 'fa-solid fa-wallet', 'https://i.postimg.cc/pL39xjQQ/Solo-Icon.jpg', 'Featured', 'Download Now', 'https://softwarecreativelabs.com/applications/solo_lite.exe', 'View Details', '/applications/landing/index.html', 1, '2025-12-02 15:32:21'),
(8, 'VERITAS RHYTHM ENGINE (VRE)', 'VERITAS RHYTHM ENGINE', '', 9.99, '', NULL, NULL, '', '', '', '', 'Learn More', '\\applications\\Application\\VeritasWebpage\\index.html', 'Purchase a Copy', '\\applications\\Application\\VeritasWebpage\\index.html', 0, '2026-02-06 20:37:57'),
(9, 'ThinkBridge AI', '', NULL, 9.99, '', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-07 18:19:39'),
(10, 'FutaFunds', '', NULL, 9.99, '', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-07 18:19:58');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `status` enum('active','pending','completed','on_hold') DEFAULT 'pending',
  `progress` int(11) DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `amount` decimal(12,2) DEFAULT 0.00,
  `notes` longtext DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '[]' CHECK (json_valid(`attachments`)),
  `created_by` int(11) DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `client_id`, `service_id`, `title`, `status`, `progress`, `start_date`, `deadline`, `description`, `budget`, `created_at`, `amount`, `notes`, `attachments`, `created_by`, `completed_date`, `updated_at`) VALUES
(21, 1, NULL, 'Project Agreement – Private Booking Website', 'pending', 75, '2025-12-29', '2026-01-20', 'Luxury Apartments: \n\n\n No notification after booking \n Booking not on admin \n Contact us form activation  \n\n Homepage amenities(edit images and text ) \n\n Sms / notication solution', NULL, '2025-12-29 12:23:00', 1000.00, 'Luxury Apartments: \n Yoco is working when you make a booking \n Successful booking screen works \n No notification after booking \n Booking not on admin \n Add download as pdf \n Add apartment + add multiple images (slider ) \n Contact us form activation  \n Add email + hosting fee SLA \n Homepage amenities(edit images and text ) \n Add section way to add admin panel icon ) \n Admin login ui touch up \n Sms / notication solution  \n Afternoon Shinsa, I just got a call from the client. They would like you to put the \nwelcome at the top, above the booking form.  \n Also it should be “welcome to the Kopano Ya Rona  Luxury Apartments”', '[]', 0, NULL, '2026-02-10 10:52:27'),
(23, 1, NULL, 'Rennen Autoteile Overhaul', 'pending', 0, '2026-02-03', '2026-02-17', '', NULL, '2026-02-10 10:53:30', 2100.00, '', '[]', 0, NULL, '2026-02-10 10:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `billing_cycle` enum('one_time','monthly','quarterly','annually') DEFAULT 'monthly',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `slug` varchar(200) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price_amount` decimal(10,2) DEFAULT 0.00,
  `price_period` varchar(50) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `details_url` varchar(500) DEFAULT NULL,
  `billing_period` varchar(50) DEFAULT 'month',
  `features` longtext DEFAULT NULL,
  `notes` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`, `billing_cycle`, `is_active`, `created_at`, `slug`, `category`, `price_amount`, `price_period`, `image_url`, `details_url`, `billing_period`, `features`, `notes`) VALUES
(223, 'E-Com Power', NULL, 0.00, 'monthly', 1, '2025-12-01 07:13:02', 'E-Com Power', 'E-Com Power', 899.00, 'month', 'https://i.postimg.cc/W146ghV3/web-hosting.png', '', NULL, '[\"1 High-Traffic Store\",\"50GB NVMe Storage\",\"Unlimited Emails\",\"Dedicated IP & SSL\",\"Real-time Backups\",\"Advanced Firewall +  DDoS\",\"WhatsApp/Call Support\",\"Monthly Performance  Audit\"]', '[]'),
(224, 'Business Boost', NULL, 0.00, 'monthly', 1, '2025-12-01 07:18:20', 'Business Boost', 'Business Boost', 499.00, 'Month', 'https://i.postimg.cc/BZwTTJhj/Untitled-1.png', '', NULL, '[\"1 Custom Platform Hosted\",\"20GB SSD Storage\",\"20 Professional Emails\",\"Free SSL Certificate\",\"Daily Backups\",\"Malware Monitoring\",\"Priority Email Support\",\"Core Updates (CMS)\"]', '[]'),
(225, 'Standard Care', NULL, 0.00, 'monthly', 1, '2025-12-01 07:23:59', 'Standard Care', 'Standard Care', 249.00, '', 'https://i.postimg.cc/9Fn771Q5/red-points-blog-domain-management-1.png', '', NULL, '[\"1 Website Hosted\",\"5GB SSD Storage\",\"5 Professional Emails\",\"Free SSL Certificate\",\"Weekly Backups\",\"Standard Security\",\"Email Support\"]', '[\"Note: Additional setup may be required\",\"Note: Custom terms available on request\"]');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','accountant') DEFAULT 'staff',
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `password`, `role`, `permissions`, `created_at`) VALUES
(1, 'Admin User', 'admin@scl.com', 'password', 'admin', NULL, '2025-09-16 14:41:52');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `milestone_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `status` enum('pending','in_progress','completed','overdue','on_hold','cancelled') DEFAULT 'pending',
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `progress` int(11) DEFAULT 0 CHECK (`progress` >= 0 and `progress` <= 100),
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `order_index` int(11) DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `file_path` (`file_path`),
  ADD KEY `idx_project_id` (`project_id`),
  ADD KEY `idx_task_id` (`task_id`);

--
-- Indexes for table `automation_logs`
--
ALTER TABLE `automation_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `client_files`
--
ALTER TABLE `client_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domains`
--
ALTER TABLE `domains`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_domain_name` (`domain_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expiry_date` (`expiry_date`);

--
-- Indexes for table `email_notifications`
--
ALTER TABLE `email_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `email_settings`
--
ALTER TABLE `email_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_date` (`event_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_reference` (`booking_reference`),
  ADD KEY `idx_event_id` (`event_id`),
  ADD KEY `idx_booking_reference` (`booking_reference`),
  ADD KEY `idx_customer_email` (`customer_email`);

--
-- Indexes for table `event_partners`
--
ALTER TABLE `event_partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event` (`event_id`);

--
-- Indexes for table `event_speakers`
--
ALTER TABLE `event_speakers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_event` (`event_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `idx_client_status` (`client_id`,`status`),
  ADD KEY `idx_due_date` (`due_date`),
  ADD KEY `idx_invoice_number` (`invoice_number`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recipient` (`to_id`,`to_type`,`is_read`);

--
-- Indexes for table `milestones`
--
ALTER TABLE `milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project_id` (`project_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `idx_client_date` (`client_id`,`payment_date`),
  ADD KEY `idx_gateway_ref` (`gateway_transaction_id`);

--
-- Indexes for table `paypal_orders`
--
ALTER TABLE `paypal_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_featured` (`is_featured`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_milestone_id` (`milestone_id`),
  ADD KEY `idx_project_id` (`project_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `automation_logs`
--
ALTER TABLE `automation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `client_files`
--
ALTER TABLE `client_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `domains`
--
ALTER TABLE `domains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `email_notifications`
--
ALTER TABLE `email_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_settings`
--
ALTER TABLE `email_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `event_bookings`
--
ALTER TABLE `event_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `event_partners`
--
ALTER TABLE `event_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_speakers`
--
ALTER TABLE `event_speakers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `milestones`
--
ALTER TABLE `milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paypal_orders`
--
ALTER TABLE `paypal_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attachments_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_files`
--
ALTER TABLE `client_files`
  ADD CONSTRAINT `client_files_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `email_notifications`
--
ALTER TABLE `email_notifications`
  ADD CONSTRAINT `email_notifications_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `email_notifications_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_partners`
--
ALTER TABLE `event_partners`
  ADD CONSTRAINT `fk_event_partners_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_speakers`
--
ALTER TABLE `event_speakers`
  ADD CONSTRAINT `fk_event_speakers_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `milestones`
--
ALTER TABLE `milestones`
  ADD CONSTRAINT `milestones_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`milestone_id`) REFERENCES `milestones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
