-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2026 at 11:15 AM
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
-- Database: `go_clear`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('go-clear-cache-app_settings_cache', 'a:0:{}', 1768300448);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_availabilities`
--

CREATE TABLE `driver_availabilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`days`)),
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `service_radius` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_availabilities`
--

INSERT INTO `driver_availabilities` (`id`, `driver_id`, `days`, `start_time`, `end_time`, `service_radius`, `created_at`, `updated_at`) VALUES
(1, 6, '\"[\\\"Monday\\\",\\\"Tuesday\\\",\\\"Wednesday\\\"]\"', '08:00:00', '18:00:00', 50, '2026-01-06 02:27:02', '2026-01-06 02:27:02'),
(2, 7, '\"[\\\"Monday\\\",\\\"Tuesday\\\"]\"', '08:00:00', '19:00:00', 80, '2026-01-06 07:35:56', '2026-01-06 07:35:56'),
(5, 13, '\"[\\\"Monday\\\",\\\"Tuesday\\\",\\\"Wednesday\\\",\\\"Thursday\\\",\\\"Friday\\\"]\"', '08:00:00', '18:00:00', 90, '2026-01-12 04:24:59', '2026-01-12 04:24:59');

-- --------------------------------------------------------

--
-- Table structure for table `driver_earnings`
--

CREATE TABLE `driver_earnings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `total_earnings` decimal(10,2) NOT NULL DEFAULT 0.00,
  `available_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `withdrawn_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_earnings`
--

INSERT INTO `driver_earnings` (`id`, `driver_id`, `total_earnings`, `available_balance`, `withdrawn_amount`, `created_at`, `updated_at`) VALUES
(1, 7, 1281.31, 1281.31, 0.00, '2026-01-07 05:57:48', '2026-01-12 02:49:37'),
(2, 6, 12.33, 12.33, 0.00, '2026-01-09 07:02:36', '2026-01-09 08:48:04'),
(3, 13, 425.00, 425.00, 0.00, '2026-01-12 06:10:27', '2026-01-12 06:10:27');

-- --------------------------------------------------------

--
-- Table structure for table `driver_locations`
--

CREATE TABLE `driver_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_profile_id` bigint(20) UNSIGNED NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `heading` smallint(5) UNSIGNED DEFAULT NULL,
  `speed` smallint(5) UNSIGNED DEFAULT NULL,
  `recorded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_locations`
--

INSERT INTO `driver_locations` (`id`, `driver_profile_id`, `lat`, `lng`, `heading`, `speed`, `recorded_at`, `created_at`, `updated_at`) VALUES
(1, 1, 33.6050385, 73.2181414, NULL, NULL, '2026-01-09 08:48:01', '2026-01-06 02:34:37', '2026-01-09 08:48:01'),
(2, 2, 33.5992546, 73.2089877, NULL, NULL, '2026-01-12 04:01:57', '2026-01-06 13:32:21', '2026-01-12 04:01:57'),
(3, 6, 33.5994035, 73.2136583, NULL, NULL, '2026-01-13 04:29:26', '2026-01-12 05:58:45', '2026-01-13 04:29:26');

-- --------------------------------------------------------

--
-- Table structure for table `driver_pricings`
--

CREATE TABLE `driver_pricings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `fixed_price` decimal(10,2) DEFAULT NULL,
  `price_per_mile` decimal(10,2) DEFAULT NULL,
  `price_per_hour` decimal(10,2) DEFAULT NULL,
  `price_per_load` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_pricings`
--

INSERT INTO `driver_pricings` (`id`, `driver_id`, `fixed_price`, `price_per_mile`, `price_per_hour`, `price_per_load`, `created_at`, `updated_at`) VALUES
(1, 6, 50.00, 2.00, 25.00, 30.00, '2026-01-06 02:26:36', '2026-01-06 02:26:36'),
(2, 7, 50.00, 3.00, 30.00, 40.00, '2026-01-06 07:35:28', '2026-01-06 07:35:28'),
(6, 13, 50.00, 2.00, 30.00, 40.00, '2026-01-12 04:24:45', '2026-01-12 04:24:45');

-- --------------------------------------------------------

--
-- Table structure for table `driver_profiles`
--

CREATE TABLE `driver_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `driving_license_front` varchar(191) DEFAULT NULL,
  `driving_license_back` varchar(191) DEFAULT NULL,
  `vehicle_photo` varchar(191) DEFAULT NULL,
  `number_plate` varchar(191) DEFAULT NULL,
  `insurance_proof` varchar(191) DEFAULT NULL,
  `waste_carrier_license` varchar(191) DEFAULT NULL,
  `vehicle_type` varchar(191) DEFAULT NULL,
  `vehicle_make_model` varchar(191) DEFAULT NULL,
  `registration_number` varchar(191) DEFAULT NULL,
  `manufacture_year` varchar(191) DEFAULT NULL,
  `fuel_type` varchar(191) DEFAULT NULL,
  `verification_status` enum('pending','in_progress','verified','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `is_online` tinyint(1) NOT NULL DEFAULT 0,
  `rating` decimal(3,2) NOT NULL DEFAULT 0.00,
  `total_jobs` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `current_latitude` decimal(10,8) DEFAULT NULL,
  `current_longitude` decimal(11,8) DEFAULT NULL,
  `last_location_update` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_profiles`
--

INSERT INTO `driver_profiles` (`id`, `user_id`, `driving_license_front`, `driving_license_back`, `vehicle_photo`, `number_plate`, `insurance_proof`, `waste_carrier_license`, `vehicle_type`, `vehicle_make_model`, `registration_number`, `manufacture_year`, `fuel_type`, `verification_status`, `rejection_reason`, `verified_at`, `is_online`, `rating`, `total_jobs`, `created_at`, `updated_at`, `current_latitude`, `current_longitude`, `last_location_update`) VALUES
(1, 6, 'wivBo0QmHpbHbT9bd7WOQves1nsPFYwJD3zY0Szq.jpg', 'OlIe7Skmm77uwDH6HZMFw8erB76KDfHoxsNOSXzk.jpg', 'y7aMMJKzYlQbA2dEuLSh1VNX5iIWrsNHRM9lqWkh.jpg', '4ZoUhwlEVSncbpaIHxyPffMcnZLpKKxGNHEpIk0i.png', 'BbflL7DLAdzmVYymRRSLaEhsYatIfxSsd5lVSVL4.png', 'wcJhRo8TCJHaCSKxEnVQzVXF8iiqx1K50r6cNvdb.png', 'van', 'Shehzore', '1234', '2012', 'petrol', 'rejected', 'hjkh', NULL, 1, 0.00, 3, '2026-01-06 02:23:10', '2026-01-09 08:57:12', 33.60503846, 73.21814138, '2026-01-09 08:48:01'),
(2, 7, 'QLW4woLrVbtHmg8I3dG1SdSKUaYRDprfjUPu75Af.png', 'nlMsHBawvJyH2qPnlgALLWe5UADFSehtxdyyuaKf.png', 'qwW3lCZzg0aS6P8jTGce7PUGX6vapNMN1UbvSGfN.png', '76YcmdQf720nFunwJnCmBpJ2ayuAdRa8s71VVORQ.png', 'zNLKhdkjBVvdwL4UPygI8NNaOwCoonLvvVvv1KPR.png', 'PF8tzIWZukf3GHqNcxdXIGosKm9qj0OuiujNLffy.jpg', 'pick up', 'Suzuki', '1231', '2024', 'petrol', 'verified', NULL, NULL, 1, 4.00, 2, '2026-01-06 07:34:06', '2026-01-12 02:49:37', 33.59925460, 73.20898768, '2026-01-12 04:01:57'),
(6, 13, 'Yxa2dRR3c0uYdNw9yG1cPgz5tMdmeL9thxJB5wmQ.jpg', 'IxX6BUzGklWbXEW4oKtLMruCx6KXUgroWwyVRn8T.jpg', 'Jb1xWFns7XNlt6rSSbBE76gYZJVZj0puuYlpVWev.jpg', 'FTYT8gRWq4iVNqcixaUmiLP9avXBXtXFSlW4zgTa.jpg', '8ceWbx2KpuVyJPQc5R5cMfD45z3dLC1Oey3g84Yr.jpg', 'ntUxh5FHerfhvQvt43J3w4887FsE8uzs8eRydMpQ.jpg', 'truck', 'ford', '4567', '2023', 'diesel', 'verified', NULL, NULL, 0, 0.00, 1, '2026-01-12 04:23:52', '2026-01-13 03:03:00', 33.59940350, 73.21365830, '2026-01-13 04:29:26');

-- --------------------------------------------------------

--
-- Table structure for table `driver_services`
--

CREATE TABLE `driver_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `pricing_type` enum('fixed','per_mile','per_hour','per_load','per_ton_kg') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `driver_services`
--

INSERT INTO `driver_services` (`id`, `driver_id`, `service_id`, `pricing_type`, `price`, `is_available`, `created_at`, `updated_at`) VALUES
(30, 7, 1, 'per_hour', 2.00, 1, '2026-01-07 03:24:23', '2026-01-07 03:24:23'),
(31, 7, 2, 'per_mile', 30.00, 1, '2026-01-07 03:24:23', '2026-01-07 03:24:23'),
(32, 7, 4, 'per_ton_kg', 10.00, 1, '2026-01-07 03:24:23', '2026-01-07 03:24:23'),
(33, 6, 1, 'per_hour', 25.00, 1, '2026-01-07 08:59:50', '2026-01-07 08:59:50'),
(43, 13, 1, 'per_hour', 0.00, 1, '2026-01-12 04:24:34', '2026-01-12 04:24:34'),
(44, 13, 2, 'per_mile', 0.00, 1, '2026-01-12 04:24:34', '2026-01-12 04:24:34'),
(45, 13, 3, 'per_mile', 0.00, 1, '2026-01-12 04:24:34', '2026-01-12 04:24:34'),
(46, 13, 4, 'per_ton_kg', 0.00, 1, '2026-01-12 04:24:34', '2026-01-12 04:24:34');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_number` varchar(191) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `pickup_address` varchar(191) NOT NULL,
  `pickup_postcode` varchar(191) DEFAULT NULL,
  `pickup_latitude` decimal(10,8) DEFAULT NULL,
  `pickup_longitude` decimal(11,8) DEFAULT NULL,
  `delivery_address` varchar(191) DEFAULT NULL,
  `delivery_postcode` varchar(191) DEFAULT NULL,
  `delivery_latitude` decimal(10,8) DEFAULT NULL,
  `delivery_longitude` decimal(11,8) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `scheduled_at` datetime DEFAULT NULL,
  `status` enum('pending','accepted','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `final_price` decimal(10,2) DEFAULT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `job_number`, `customer_id`, `driver_id`, `service_id`, `pickup_address`, `pickup_postcode`, `pickup_latitude`, `pickup_longitude`, `delivery_address`, `delivery_postcode`, `delivery_latitude`, `delivery_longitude`, `description`, `scheduled_at`, `status`, `estimated_price`, `final_price`, `distance`, `duration`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(1, 'GC-20260107-00001', 5, 6, 1, 'Clifton Karachi', '5544', 24.86070000, 67.00110000, NULL, NULL, NULL, NULL, NULL, NULL, 'completed', NULL, 1500.00, 12.50, 45, NULL, '2026-01-07 05:45:59', '2026-01-09 08:58:01'),
(2, 'GC-20260107-00002', 7, 7, 1, 'Test Pickup', 'AB12', 33.59000000, 73.21000000, 'Test Drop', 'CD34', NULL, NULL, NULL, NULL, 'accepted', 5.93, NULL, 3.24, 12, NULL, '2026-01-07 07:03:04', '2026-01-08 08:28:49'),
(5, 'GC-20260108-00005', 5, NULL, 1, 'Gulberg Residencia, Islamabad', '44000', 33.59938000, 73.21367000, 'F-10 Markaz, Islamabad', '44100', 33.69250000, 73.01820000, '2 sofas and 5 boxes', '2026-01-08 14:30:00', 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-08 03:32:33', '2026-01-08 03:32:33'),
(10, 'GC-20260108-00006', 7, NULL, 1, 'Test Pickup', NULL, 33.59000000, 73.21000000, 'Test Drop', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-08 03:58:37', '2026-01-08 03:58:37'),
(11, 'GC-20260108-00011', 5, NULL, 1, 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.59938600, 73.21367910, '22, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.60626628, 73.22101135, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-08 03:58:49', '2026-01-08 03:58:49'),
(12, 'GC-20260108-00012', 5, NULL, 1, 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.59938600, 73.21367910, '22, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.60626628, 73.22101135, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-08 04:01:22', '2026-01-08 04:01:22'),
(13, 'GC-20260108-00013', 5, 7, 1, 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.59938240, 73.21367540, '22, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.60675299, 73.22147705, NULL, NULL, 'cancelled', NULL, NULL, NULL, NULL, NULL, '2026-01-08 05:34:57', '2026-01-08 06:15:19'),
(14, 'GC-20260108-00014', 7, NULL, 1, 'Test Pickup', NULL, 33.59000000, 73.21000000, 'Test Drop', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-08 07:32:50', '2026-01-08 07:32:50'),
(15, 'GC-20260108-00015', 5, 6, 1, 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.59939460, 73.21367130, 'Gulberg Recidencia, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.60972263, 73.22995953, NULL, NULL, 'accepted', NULL, NULL, NULL, NULL, NULL, '2026-01-08 08:32:52', '2026-01-08 08:34:41'),
(16, 'GC-20260109-00016', 5, NULL, 1, 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.59937520, 73.21368290, 'J66C+HQQ, Darwala, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61027326, 73.22164401, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-09 01:57:56', '2026-01-09 01:57:56'),
(17, 'GC-20260109-00017', 5, NULL, 1, 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.59939550, 73.21366220, '41, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.60539896, 73.21968332, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-09 02:33:52', '2026-01-09 02:33:52'),
(18, 'GC-20260109-00018', 5, NULL, 1, 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.59939280, 73.21366520, '22, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.60556623, 73.22083130, NULL, NULL, 'pending', 2.00, NULL, 1.00, 3, NULL, '2026-01-09 03:15:48', '2026-01-09 03:15:55'),
(30, 'GC-20260112-00030', 5, NULL, 1, 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.59918758, 73.21279574, 'Shop no 55 alhajj market DHA 2 2, DHA Phase II, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.53049103, 73.16707682, NULL, NULL, 'in_progress', 13.32, NULL, 1.00, 3, NULL, '2026-01-12 02:22:50', '2026-01-12 02:31:05'),
(31, 'GC-20260112-00031', 5, 7, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 3.00, 9, NULL, '2026-01-12 02:39:29', '2026-01-12 02:41:24'),
(32, 'GC-20260112-00032', 8, 7, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'completed', 7.42, 7.42, NULL, NULL, NULL, '2026-01-12 02:47:50', '2026-01-12 02:49:37'),
(33, 'GC-20260112-00033', 8, 7, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Rawal Park, Chah Sultan, Rawalpindi, Punjab, Pakistan', NULL, 33.62718093, 73.07451613, NULL, NULL, 'in_progress', 12.39, NULL, 3.00, 9, NULL, '2026-01-12 02:50:26', '2026-01-12 02:50:44'),
(34, 'GC-20260112-00034', 8, 7, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 02:54:03', '2026-01-12 02:54:23'),
(35, 'GC-20260112-00035', 8, 7, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 3.00, 9, NULL, '2026-01-12 02:56:34', '2026-01-12 02:56:53'),
(36, 'GC-20260112-00036', 8, 7, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'accepted', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 03:02:09', '2026-01-12 03:02:25'),
(37, 'GC-20260112-00037', 8, 7, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'accepted', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 03:05:49', '2026-01-12 03:06:13'),
(38, 'GC-20260112-00038', 8, 7, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'accepted', 7.42, NULL, 3.00, 9, NULL, '2026-01-12 03:09:34', '2026-01-12 03:09:46'),
(40, 'GC-20260112-00039', 8, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'completed', 7.42, 500.00, NULL, NULL, NULL, '2026-01-12 06:03:09', '2026-01-12 06:10:27'),
(41, 'GC-20260112-00041', 8, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Shop no 55 alhajj market DHA 2 2, DHA Phase II, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.53049103, 73.16707682, NULL, NULL, 'in_progress', 10.98, NULL, 2.00, 8, NULL, '2026-01-12 06:12:26', '2026-01-12 06:13:01'),
(42, 'GC-20260112-00042', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 06:51:13', '2026-01-12 06:51:30'),
(43, 'GC-20260112-00043', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'accepted', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 07:03:48', '2026-01-12 07:03:56'),
(44, 'GC-20260112-00044', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 07:06:40', '2026-01-12 07:09:55'),
(45, 'GC-20260112-00045', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'accepted', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 07:48:13', '2026-01-12 07:48:20'),
(46, 'GC-20260112-00046', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 07:56:44', '2026-01-12 07:57:09'),
(47, 'GC-20260112-00047', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 08:00:28', '2026-01-12 08:00:41'),
(48, 'GC-20260112-00048', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 08:12:14', '2026-01-12 08:12:33'),
(49, 'GC-20260112-00049', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'accepted', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 08:14:52', '2026-01-12 08:14:59'),
(50, 'GC-20260112-00050', 5, NULL, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'pending', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 08:26:48', '2026-01-12 08:26:50'),
(51, 'GC-20260112-00051', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'in_progress', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 08:36:18', '2026-01-12 08:36:44'),
(52, 'GC-20260112-00052', 5, 13, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'accepted', 7.42, NULL, 2.00, 8, NULL, '2026-01-12 08:45:14', '2026-01-12 08:45:21'),
(53, 'GC-20260113-00053', 5, 13, 1, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, NULL, NULL, 'accepted', 6.78, NULL, 6.00, 20, NULL, '2026-01-13 01:51:41', '2026-01-13 01:51:52'),
(54, 'GC-20260113-00054', 5, NULL, 1, 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', NULL, 33.61147476, 73.17331262, 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', NULL, 33.62913259, 73.11355535, NULL, NULL, 'pending', 7.42, NULL, 2.00, 8, NULL, '2026-01-13 02:05:13', '2026-01-13 02:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `job_requests`
--

CREATE TABLE `job_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('sent','accepted','rejected','expired','cancelled') NOT NULL DEFAULT 'sent',
  `sent_at` datetime DEFAULT NULL,
  `responded_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_requests`
--

INSERT INTO `job_requests` (`id`, `job_id`, `driver_id`, `status`, `sent_at`, `responded_at`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 'rejected', '2026-01-07 12:13:47', '2026-01-08 13:29:30', '2026-01-07 07:13:47', '2026-01-08 08:29:30'),
(2, 2, 7, 'accepted', '2026-01-07 12:28:52', '2026-01-08 13:28:49', '2026-01-07 07:28:52', '2026-01-08 08:28:49'),
(3, 1, 6, 'accepted', '2026-01-07 13:24:13', '2026-01-09 10:50:08', '2026-01-07 08:24:13', '2026-01-09 05:50:08'),
(4, 1, 5, 'expired', '2026-01-07 13:45:28', '2026-01-09 10:50:08', '2026-01-07 08:45:28', '2026-01-09 05:50:08'),
(5, 13, 7, 'rejected', '2026-01-08 10:37:18', '2026-01-08 13:28:23', '2026-01-08 05:37:18', '2026-01-08 08:28:23'),
(6, 15, 6, 'accepted', '2026-01-08 13:32:58', '2026-01-08 13:34:41', '2026-01-08 08:32:58', '2026-01-08 08:34:41'),
(7, 16, 7, 'rejected', '2026-01-09 07:00:26', '2026-01-12 07:47:40', '2026-01-09 02:00:26', '2026-01-12 02:47:40'),
(8, 17, 7, 'rejected', '2026-01-09 07:33:56', '2026-01-12 07:47:39', '2026-01-09 02:33:56', '2026-01-12 02:47:39'),
(9, 18, 7, 'rejected', '2026-01-09 08:15:55', '2026-01-12 07:47:36', '2026-01-09 03:15:55', '2026-01-12 02:47:36'),
(21, 30, 7, 'rejected', '2026-01-12 07:22:57', '2026-01-12 07:47:27', '2026-01-12 02:22:57', '2026-01-12 02:47:27'),
(22, 31, 7, 'accepted', '2026-01-12 07:39:33', '2026-01-12 07:41:10', '2026-01-12 02:39:33', '2026-01-12 02:41:10'),
(23, 32, 7, 'accepted', '2026-01-12 07:47:58', '2026-01-12 07:48:12', '2026-01-12 02:47:58', '2026-01-12 02:48:12'),
(24, 33, 7, 'accepted', '2026-01-12 07:50:28', '2026-01-12 07:50:35', '2026-01-12 02:50:28', '2026-01-12 02:50:35'),
(25, 34, 7, 'accepted', '2026-01-12 07:54:05', '2026-01-12 07:54:12', '2026-01-12 02:54:05', '2026-01-12 02:54:12'),
(26, 35, 7, 'accepted', '2026-01-12 07:56:37', '2026-01-12 07:56:42', '2026-01-12 02:56:37', '2026-01-12 02:56:42'),
(27, 36, 7, 'accepted', '2026-01-12 08:02:17', '2026-01-12 08:02:25', '2026-01-12 03:02:17', '2026-01-12 03:02:25'),
(28, 37, 7, 'accepted', '2026-01-12 08:05:56', '2026-01-12 08:06:13', '2026-01-12 03:05:56', '2026-01-12 03:06:13'),
(29, 38, 7, 'accepted', '2026-01-12 08:09:37', '2026-01-12 08:09:46', '2026-01-12 03:09:37', '2026-01-12 03:09:46'),
(31, 40, 13, 'accepted', '2026-01-12 11:03:12', '2026-01-12 11:03:19', '2026-01-12 06:03:12', '2026-01-12 06:03:19'),
(32, 41, 13, 'accepted', '2026-01-12 11:12:36', '2026-01-12 11:12:47', '2026-01-12 06:12:36', '2026-01-12 06:12:47'),
(33, 42, 13, 'accepted', '2026-01-12 11:51:16', '2026-01-12 11:51:22', '2026-01-12 06:51:16', '2026-01-12 06:51:22'),
(34, 43, 13, 'accepted', '2026-01-12 12:03:50', '2026-01-12 12:03:56', '2026-01-12 07:03:50', '2026-01-12 07:03:56'),
(35, 44, 13, 'accepted', '2026-01-12 12:06:42', '2026-01-12 12:06:51', '2026-01-12 07:06:42', '2026-01-12 07:06:51'),
(36, 45, 13, 'accepted', '2026-01-12 12:48:15', '2026-01-12 12:48:20', '2026-01-12 07:48:15', '2026-01-12 07:48:20'),
(37, 46, 13, 'accepted', '2026-01-12 12:56:47', '2026-01-12 12:56:51', '2026-01-12 07:56:47', '2026-01-12 07:56:51'),
(38, 47, 13, 'accepted', '2026-01-12 13:00:31', '2026-01-12 13:00:35', '2026-01-12 08:00:31', '2026-01-12 08:00:35'),
(39, 48, 13, 'accepted', '2026-01-12 13:12:16', '2026-01-12 13:12:22', '2026-01-12 08:12:16', '2026-01-12 08:12:22'),
(40, 49, 13, 'accepted', '2026-01-12 13:14:53', '2026-01-12 13:14:59', '2026-01-12 08:14:53', '2026-01-12 08:14:59'),
(41, 50, 13, 'rejected', '2026-01-12 13:26:50', '2026-01-12 13:35:50', '2026-01-12 08:26:50', '2026-01-12 08:35:50'),
(42, 51, 13, 'accepted', '2026-01-12 13:36:24', '2026-01-12 13:36:28', '2026-01-12 08:36:24', '2026-01-12 08:36:28'),
(43, 52, 13, 'accepted', '2026-01-12 13:45:16', '2026-01-12 13:45:21', '2026-01-12 08:45:16', '2026-01-12 08:45:21'),
(44, 53, 13, 'accepted', '2026-01-13 06:51:46', '2026-01-13 06:51:52', '2026-01-13 01:51:46', '2026-01-13 01:51:52'),
(45, 54, 13, 'rejected', '2026-01-13 07:05:15', '2026-01-13 08:01:22', '2026-01-13 02:05:15', '2026-01-13 03:01:22');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_services_table', 1),
(4, '0001_01_01_000003_create_jobs_table', 1),
(5, '2025_11_21_071849_create_driver_profiles_table', 1),
(6, '2025_11_21_072051_create_driver_services_table', 1),
(7, '2025_11_21_072221_create_transactions_table', 1),
(8, '2025_11_21_072338_create_driver_earnings_table', 1),
(9, '2025_11_21_072444_create_ratings_table', 1),
(10, '2025_11_21_072543_create_settings_table', 1),
(11, '2025_11_21_080108_create_personal_access_tokens_table', 1),
(12, '2025_11_24_075834_add_location_to_driver_profiles', 1),
(13, '2025_12_10_065506_create_driver_pricings_table', 1),
(14, '2025_12_10_065518_create_driver_availabilities_table', 1),
(16, '2025_12_18_113330_add_admin_commission_to_settings_table', 1),
(17, '2025_12_19_081123_create_notifications_table', 1),
(18, '2025_12_22_113339_update_type_enum_in_notifications_table', 1),
(19, '2026_01_01_072008_add_vehicle_details_to_driver_profiles_table', 1),
(20, '2026_01_05_065050_create_driver_locations_table', 1),
(23, '2026_01_05_121313_create_saved_addresses_table', 2),
(24, '2026_01_06_095040_add_pricing_type_to_services_table', 3),
(25, '2026_01_07_072226_update_pricing_type_enum_in_driver_services', 4),
(26, '2026_01_07_115222_create_job_requests_table', 5),
(27, '2026_01_07_121723_alter_notifications_type_column', 6),
(28, '2026_01_08_085712_make_pickup_postcode_nullable_in_jobs_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(191) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `is_read`, `data`, `created_at`, `updated_at`) VALUES
(1, 6, 'GENERAL', '', 'Your documents have been submitted successfully and are currently under review.', 0, '{\"verification_status\":\"pending\"}', '2026-01-06 02:25:52', '2026-01-06 02:25:52'),
(2, 4, 'ADMIN', 'New Verification Request', 'Driver \"Driver 1\" has submitted documents for verification.', 1, '{\"driver_profile_id\":1,\"verification_status\":\"pending\"}', '2026-01-06 02:25:52', '2026-01-12 08:22:08'),
(3, 7, 'GENERAL', '', 'Your documents have been submitted successfully and are currently under review.', 0, '{\"verification_status\":\"pending\"}', '2026-01-06 07:34:52', '2026-01-06 07:34:52'),
(4, 4, 'ADMIN', 'New Verification Request', 'Driver \"Driver 2\" has submitted documents for verification.', 1, '{\"driver_profile_id\":2,\"verification_status\":\"pending\"}', '2026-01-06 07:34:52', '2026-01-12 08:22:03'),
(5, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":1}', '2026-01-07 05:46:00', '2026-01-07 05:46:00'),
(6, 7, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":1,\"driver_name\":\"Driver 2\"}', '2026-01-07 05:52:55', '2026-01-07 05:52:55'),
(7, 7, 'JOB_UPDATE', 'Job Started', 'Driver 2 has started your waste collection service.', 0, '{\"job_id\":1,\"driver_name\":\"Driver 2\"}', '2026-01-07 05:55:53', '2026-01-07 05:55:53'),
(8, 7, 'JOB_UPDATE', 'Job Completed', 'Your waste collection service has been completed.', 0, '{\"job_id\":1}', '2026-01-07 05:57:48', '2026-01-07 05:57:48'),
(9, 7, 'JOB_UPDATE', 'Payment Received', 'Payment of £1500 has been received successfully.', 0, '{\"job_id\":1,\"amount\":1500}', '2026-01-07 05:57:48', '2026-01-07 05:57:48'),
(10, 7, 'JOB_UPDATE', 'New Rating Received', 'You have received a new rating from the customer.', 0, '{\"job_id\":1}', '2026-01-07 06:01:31', '2026-01-07 06:01:31'),
(11, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":2}', '2026-01-07 07:03:04', '2026-01-07 07:03:04'),
(12, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 07:26:55', '2026-01-07 07:26:55'),
(13, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":2}', '2026-01-07 07:28:52', '2026-01-07 07:28:52'),
(14, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 07:29:35', '2026-01-07 07:29:35'),
(15, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:22:12', '2026-01-07 08:22:12'),
(16, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:24:12', '2026-01-07 08:24:12'),
(17, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:24:13', '2026-01-07 08:24:13'),
(18, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:26:14', '2026-01-07 08:26:14'),
(19, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:26:26', '2026-01-07 08:26:26'),
(20, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:26:27', '2026-01-07 08:26:27'),
(21, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:26:29', '2026-01-07 08:26:29'),
(22, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:26:31', '2026-01-07 08:26:31'),
(23, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:26:37', '2026-01-07 08:26:37'),
(24, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:28:15', '2026-01-07 08:28:15'),
(25, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:39:59', '2026-01-07 08:39:59'),
(26, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:41:52', '2026-01-07 08:41:52'),
(27, 5, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:45:28', '2026-01-07 08:45:28'),
(28, 5, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-07 08:45:51', '2026-01-07 08:45:51'),
(29, 5, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-08 02:07:04', '2026-01-08 02:07:04'),
(30, 5, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-08 02:12:02', '2026-01-08 02:12:02'),
(31, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-08 02:39:16', '2026-01-08 02:39:16'),
(32, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-08 02:39:28', '2026-01-08 02:39:28'),
(33, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-08 02:40:54', '2026-01-08 02:40:54'),
(34, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-08 02:47:08', '2026-01-08 02:47:08'),
(35, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-08 02:49:51', '2026-01-08 02:49:51'),
(36, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-08 02:53:34', '2026-01-08 02:53:34'),
(37, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":3}', '2026-01-08 03:19:56', '2026-01-08 03:19:56'),
(38, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":4}', '2026-01-08 03:32:16', '2026-01-08 03:32:16'),
(39, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":5}', '2026-01-08 03:32:33', '2026-01-08 03:32:33'),
(40, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":6}', '2026-01-08 03:42:16', '2026-01-08 03:42:16'),
(41, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":7}', '2026-01-08 03:42:52', '2026-01-08 03:42:52'),
(42, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":8}', '2026-01-08 03:45:22', '2026-01-08 03:45:22'),
(43, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":9}', '2026-01-08 03:46:51', '2026-01-08 03:46:51'),
(44, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":10}', '2026-01-08 03:58:37', '2026-01-08 03:58:37'),
(45, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":11}', '2026-01-08 03:58:49', '2026-01-08 03:58:49'),
(46, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":12}', '2026-01-08 04:01:22', '2026-01-08 04:01:22'),
(47, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":13}', '2026-01-08 05:34:57', '2026-01-08 05:34:57'),
(48, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":13}', '2026-01-08 05:37:18', '2026-01-08 05:37:18'),
(49, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":13,\"driver_name\":\"Driver 2\"}', '2026-01-08 06:15:19', '2026-01-08 06:15:19'),
(50, 7, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":14}', '2026-01-08 07:32:50', '2026-01-08 07:32:50'),
(51, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":2}', '2026-01-08 07:36:39', '2026-01-08 07:36:39'),
(52, 7, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":2,\"driver_name\":\"Driver 2\"}', '2026-01-08 08:28:49', '2026-01-08 08:28:49'),
(53, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":15}', '2026-01-08 08:32:52', '2026-01-08 08:32:52'),
(54, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":15}', '2026-01-08 08:32:58', '2026-01-08 08:32:58'),
(55, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 1 has been assigned to your booking.', 0, '{\"job_id\":15,\"driver_name\":\"Driver 1\"}', '2026-01-08 08:34:41', '2026-01-08 08:34:41'),
(56, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":16}', '2026-01-09 01:57:56', '2026-01-09 01:57:56'),
(57, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":16}', '2026-01-09 02:00:26', '2026-01-09 02:00:26'),
(58, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":17}', '2026-01-09 02:33:52', '2026-01-09 02:33:52'),
(59, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":17}', '2026-01-09 02:33:56', '2026-01-09 02:33:56'),
(60, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-09 03:03:34', '2026-01-09 03:03:34'),
(61, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":18}', '2026-01-09 03:15:48', '2026-01-09 03:15:48'),
(62, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":18}', '2026-01-09 03:15:55', '2026-01-09 03:15:55'),
(63, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-09 05:25:50', '2026-01-09 05:25:50'),
(64, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":1}', '2026-01-09 05:39:53', '2026-01-09 05:39:53'),
(65, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 1 has been assigned to your booking.', 0, '{\"job_id\":1,\"driver_name\":\"Driver 1\"}', '2026-01-09 05:50:08', '2026-01-09 05:50:08'),
(67, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":19}', '2026-01-09 06:01:25', '2026-01-09 06:01:25'),
(71, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":20}', '2026-01-09 06:44:14', '2026-01-09 06:44:14'),
(75, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":21}', '2026-01-09 07:01:55', '2026-01-09 07:01:55'),
(81, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":22}', '2026-01-09 07:06:54', '2026-01-09 07:06:54'),
(83, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":23}', '2026-01-09 07:12:05', '2026-01-09 07:12:05'),
(89, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":24}', '2026-01-09 07:44:19', '2026-01-09 07:44:19'),
(93, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":25}', '2026-01-09 07:48:03', '2026-01-09 07:48:03'),
(96, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":26}', '2026-01-09 08:03:23', '2026-01-09 08:03:23'),
(100, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":27}', '2026-01-09 08:12:48', '2026-01-09 08:12:48'),
(103, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":28}', '2026-01-09 08:39:36', '2026-01-09 08:39:36'),
(107, 6, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":29}', '2026-01-09 08:47:25', '2026-01-09 08:47:25'),
(112, 6, 'GENERAL', 'Verification Approved', 'Congratulations! Your driver account has been verified. You can now start accepting jobs.', 0, '{\"verification_status\":\"verified\"}', '2026-01-09 08:56:48', '2026-01-09 08:56:48'),
(113, 6, 'GENERAL', 'Verification In Progress', 'Your documents are currently being reviewed. We will notify you once the verification is complete.', 0, '{\"verification_status\":\"in_progress\"}', '2026-01-09 08:56:52', '2026-01-09 08:56:52'),
(114, 6, 'GENERAL', 'Verification Rejected', 'Your verification was rejected. Reason: hjkh. Please update your documents and resubmit.', 0, '{\"verification_status\":\"rejected\",\"rejection_reason\":\"hjkh\"}', '2026-01-09 08:57:12', '2026-01-09 08:57:12'),
(115, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":30}', '2026-01-12 02:22:50', '2026-01-12 02:22:50'),
(116, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":30}', '2026-01-12 02:22:57', '2026-01-12 02:22:57'),
(117, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":31}', '2026-01-12 02:39:29', '2026-01-12 02:39:29'),
(118, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":31}', '2026-01-12 02:39:33', '2026-01-12 02:39:33'),
(119, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":31,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:41:10', '2026-01-12 02:41:10'),
(120, 5, 'JOB_UPDATE', 'Job Started', 'Driver 2 has started your waste collection service.', 0, '{\"job_id\":31,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:41:24', '2026-01-12 02:41:24'),
(121, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":32}', '2026-01-12 02:47:50', '2026-01-12 02:47:50'),
(122, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":32}', '2026-01-12 02:47:58', '2026-01-12 02:47:58'),
(123, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":32,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:48:12', '2026-01-12 02:48:12'),
(124, 8, 'JOB_UPDATE', 'Job Started', 'Driver 2 has started your waste collection service.', 0, '{\"job_id\":32,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:48:24', '2026-01-12 02:48:24'),
(125, 8, 'JOB_UPDATE', 'Job Completed', 'Your waste collection service has been completed.', 0, '{\"job_id\":32}', '2026-01-12 02:49:37', '2026-01-12 02:49:37'),
(126, 8, 'JOB_UPDATE', 'Payment Received', 'Payment of £7.42 has been received successfully.', 0, '{\"job_id\":32,\"amount\":7.42}', '2026-01-12 02:49:37', '2026-01-12 02:49:37'),
(127, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":33}', '2026-01-12 02:50:26', '2026-01-12 02:50:26'),
(128, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":33}', '2026-01-12 02:50:28', '2026-01-12 02:50:28'),
(129, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":33,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:50:35', '2026-01-12 02:50:35'),
(130, 8, 'JOB_UPDATE', 'Job Started', 'Driver 2 has started your waste collection service.', 0, '{\"job_id\":33,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:50:44', '2026-01-12 02:50:44'),
(131, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":34}', '2026-01-12 02:54:03', '2026-01-12 02:54:03'),
(132, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":34}', '2026-01-12 02:54:05', '2026-01-12 02:54:05'),
(133, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":34,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:54:12', '2026-01-12 02:54:12'),
(134, 8, 'JOB_UPDATE', 'Job Started', 'Driver 2 has started your waste collection service.', 0, '{\"job_id\":34,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:54:23', '2026-01-12 02:54:23'),
(135, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":35}', '2026-01-12 02:56:34', '2026-01-12 02:56:34'),
(136, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":35}', '2026-01-12 02:56:37', '2026-01-12 02:56:37'),
(137, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":35,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:56:42', '2026-01-12 02:56:42'),
(138, 8, 'JOB_UPDATE', 'Job Started', 'Driver 2 has started your waste collection service.', 0, '{\"job_id\":35,\"driver_name\":\"Driver 2\"}', '2026-01-12 02:56:53', '2026-01-12 02:56:53'),
(139, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":36}', '2026-01-12 03:02:09', '2026-01-12 03:02:09'),
(140, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":36}', '2026-01-12 03:02:17', '2026-01-12 03:02:17'),
(141, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":36,\"driver_name\":\"Driver 2\"}', '2026-01-12 03:02:25', '2026-01-12 03:02:25'),
(142, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":37}', '2026-01-12 03:05:49', '2026-01-12 03:05:49'),
(143, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":37}', '2026-01-12 03:05:56', '2026-01-12 03:05:56'),
(144, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":37,\"driver_name\":\"Driver 2\"}', '2026-01-12 03:06:13', '2026-01-12 03:06:13'),
(145, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":38}', '2026-01-12 03:09:34', '2026-01-12 03:09:34'),
(146, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":38}', '2026-01-12 03:09:37', '2026-01-12 03:09:37'),
(147, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 2 has been assigned to your booking.', 0, '{\"job_id\":38,\"driver_name\":\"Driver 2\"}', '2026-01-12 03:09:46', '2026-01-12 03:09:46'),
(149, 4, 'ADMIN', 'New Verification Request', 'Driver \"Driver 3\" has submitted documents for verification.', 1, '{\"driver_profile_id\":3,\"verification_status\":\"pending\"}', '2026-01-12 03:17:02', '2026-01-12 08:21:55'),
(152, 4, 'ADMIN', 'New Verification Request', 'Driver \"Driver 3\" has submitted documents for verification.', 1, '{\"driver_profile_id\":4,\"verification_status\":\"pending\"}', '2026-01-12 03:43:44', '2026-01-12 08:21:46'),
(155, 7, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":39}', '2026-01-12 04:01:03', '2026-01-12 04:01:03'),
(158, 4, 'ADMIN', 'New Verification Request', 'Driver \"Driver 3\" has submitted documents for verification.', 1, '{\"driver_profile_id\":5,\"verification_status\":\"pending\"}', '2026-01-12 04:09:15', '2026-01-12 08:21:25'),
(160, 13, 'GENERAL', '', 'Your documents have been submitted successfully and are currently under review.', 0, '{\"verification_status\":\"pending\"}', '2026-01-12 04:24:23', '2026-01-12 04:24:23'),
(161, 4, 'ADMIN', 'New Verification Request', 'Driver \"Driver 3\" has submitted documents for verification.', 1, '{\"driver_profile_id\":6,\"verification_status\":\"pending\"}', '2026-01-12 04:24:23', '2026-01-12 07:16:00'),
(162, 13, 'GENERAL', 'Verification Approved', 'Congratulations! Your driver account has been verified. You can now start accepting jobs.', 0, '{\"verification_status\":\"verified\"}', '2026-01-12 04:24:26', '2026-01-12 04:24:26'),
(163, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":40}', '2026-01-12 06:03:09', '2026-01-12 06:03:09'),
(164, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":40}', '2026-01-12 06:03:12', '2026-01-12 06:03:12'),
(165, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":40,\"driver_name\":\"Driver 3\"}', '2026-01-12 06:03:19', '2026-01-12 06:03:19'),
(166, 8, 'JOB_UPDATE', 'Job Started', 'Driver 3 has started your waste collection service.', 0, '{\"job_id\":40,\"driver_name\":\"Driver 3\"}', '2026-01-12 06:04:28', '2026-01-12 06:04:28'),
(167, 8, 'JOB_UPDATE', 'Job Completed', 'Your waste collection service has been completed.', 0, '{\"job_id\":40}', '2026-01-12 06:10:27', '2026-01-12 06:10:27'),
(168, 8, 'JOB_UPDATE', 'Payment Received', 'Payment of £500 has been received successfully.', 0, '{\"job_id\":40,\"amount\":500}', '2026-01-12 06:10:27', '2026-01-12 06:10:27'),
(169, 8, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":41}', '2026-01-12 06:12:26', '2026-01-12 06:12:26'),
(170, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":41}', '2026-01-12 06:12:36', '2026-01-12 06:12:36'),
(171, 8, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":41,\"driver_name\":\"Driver 3\"}', '2026-01-12 06:12:47', '2026-01-12 06:12:47'),
(172, 8, 'JOB_UPDATE', 'Job Started', 'Driver 3 has started your waste collection service.', 0, '{\"job_id\":41,\"driver_name\":\"Driver 3\"}', '2026-01-12 06:13:01', '2026-01-12 06:13:01'),
(173, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":42}', '2026-01-12 06:51:13', '2026-01-12 06:51:13'),
(174, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":42}', '2026-01-12 06:51:16', '2026-01-12 06:51:16'),
(175, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":42,\"driver_name\":\"Driver 3\"}', '2026-01-12 06:51:22', '2026-01-12 06:51:22'),
(176, 5, 'JOB_UPDATE', 'Job Started', 'Driver 3 has started your waste collection service.', 0, '{\"job_id\":42,\"driver_name\":\"Driver 3\"}', '2026-01-12 06:51:30', '2026-01-12 06:51:30'),
(177, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":43}', '2026-01-12 07:03:48', '2026-01-12 07:03:48'),
(178, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":43}', '2026-01-12 07:03:50', '2026-01-12 07:03:50'),
(179, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":43,\"driver_name\":\"Driver 3\"}', '2026-01-12 07:03:56', '2026-01-12 07:03:56'),
(180, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":44}', '2026-01-12 07:06:40', '2026-01-12 07:06:40'),
(181, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":44}', '2026-01-12 07:06:42', '2026-01-12 07:06:42'),
(182, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":44,\"driver_name\":\"Driver 3\"}', '2026-01-12 07:06:52', '2026-01-12 07:06:52'),
(183, 5, 'JOB_UPDATE', 'Job Started', 'Driver 3 has started your waste collection service.', 0, '{\"job_id\":44,\"driver_name\":\"Driver 3\"}', '2026-01-12 07:09:55', '2026-01-12 07:09:55'),
(184, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":45}', '2026-01-12 07:48:13', '2026-01-12 07:48:13'),
(185, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":45}', '2026-01-12 07:48:15', '2026-01-12 07:48:15'),
(186, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":45,\"driver_name\":\"Driver 3\"}', '2026-01-12 07:48:20', '2026-01-12 07:48:20'),
(187, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":46}', '2026-01-12 07:56:45', '2026-01-12 07:56:45'),
(188, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":46}', '2026-01-12 07:56:47', '2026-01-12 07:56:47'),
(189, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":46,\"driver_name\":\"Driver 3\"}', '2026-01-12 07:56:51', '2026-01-12 07:56:51'),
(190, 5, 'JOB_UPDATE', 'Job Started', 'Driver 3 has started your waste collection service.', 0, '{\"job_id\":46,\"driver_name\":\"Driver 3\"}', '2026-01-12 07:57:09', '2026-01-12 07:57:09'),
(191, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":47}', '2026-01-12 08:00:28', '2026-01-12 08:00:28'),
(192, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":47}', '2026-01-12 08:00:31', '2026-01-12 08:00:31'),
(193, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":47,\"driver_name\":\"Driver 3\"}', '2026-01-12 08:00:35', '2026-01-12 08:00:35'),
(194, 5, 'JOB_UPDATE', 'Job Started', 'Driver 3 has started your waste collection service.', 0, '{\"job_id\":47,\"driver_name\":\"Driver 3\"}', '2026-01-12 08:00:41', '2026-01-12 08:00:41'),
(195, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":48}', '2026-01-12 08:12:14', '2026-01-12 08:12:14'),
(196, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":48}', '2026-01-12 08:12:16', '2026-01-12 08:12:16'),
(197, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":48,\"driver_name\":\"Driver 3\"}', '2026-01-12 08:12:22', '2026-01-12 08:12:22'),
(198, 5, 'JOB_UPDATE', 'Job Started', 'Driver 3 has started your waste collection service.', 0, '{\"job_id\":48,\"driver_name\":\"Driver 3\"}', '2026-01-12 08:12:33', '2026-01-12 08:12:33'),
(199, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":49}', '2026-01-12 08:14:52', '2026-01-12 08:14:52'),
(200, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":49}', '2026-01-12 08:14:53', '2026-01-12 08:14:53'),
(201, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":49,\"driver_name\":\"Driver 3\"}', '2026-01-12 08:14:59', '2026-01-12 08:14:59'),
(202, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":50}', '2026-01-12 08:26:48', '2026-01-12 08:26:48'),
(203, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":50}', '2026-01-12 08:26:50', '2026-01-12 08:26:50'),
(204, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":51}', '2026-01-12 08:36:18', '2026-01-12 08:36:18'),
(205, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":51}', '2026-01-12 08:36:24', '2026-01-12 08:36:24'),
(206, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":51,\"driver_name\":\"Driver 3\"}', '2026-01-12 08:36:28', '2026-01-12 08:36:28'),
(207, 5, 'JOB_UPDATE', 'Job Started', 'Driver 3 has started your waste collection service.', 0, '{\"job_id\":51,\"driver_name\":\"Driver 3\"}', '2026-01-12 08:36:44', '2026-01-12 08:36:44'),
(208, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":52}', '2026-01-12 08:45:14', '2026-01-12 08:45:14'),
(209, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":52}', '2026-01-12 08:45:16', '2026-01-12 08:45:16'),
(210, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":52,\"driver_name\":\"Driver 3\"}', '2026-01-12 08:45:21', '2026-01-12 08:45:21'),
(211, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":53}', '2026-01-13 01:51:41', '2026-01-13 01:51:41'),
(212, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":53}', '2026-01-13 01:51:46', '2026-01-13 01:51:46'),
(213, 5, 'JOB_UPDATE', 'Driver Assigned', 'Driver 3 has been assigned to your booking.', 0, '{\"job_id\":53,\"driver_name\":\"Driver 3\"}', '2026-01-13 01:51:52', '2026-01-13 01:51:52'),
(214, 5, 'JOB_UPDATE', 'Booking Created', 'Your booking has been created successfully. We will assign a driver soon.', 0, '{\"job_id\":54}', '2026-01-13 02:05:13', '2026-01-13 02:05:13'),
(215, 13, 'JOB_UPDATE', 'New Job Request', 'You have received a new job request.', 0, '{\"job_id\":54}', '2026-01-13 02:05:15', '2026-01-13 02:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', 'a9663c4ca15184b56a94b82e490dc54c253f8918a49b593c378432e92685a288', '[\"*\"]', NULL, NULL, '2026-01-05 06:33:32', '2026-01-05 06:33:32'),
(2, 'App\\Models\\User', 2, 'auth_token', '21d9fe615a902986810b8a7ac7dc7ed22a5bd77a6381059719e2dca2dbf0e852', '[\"*\"]', '2026-01-12 03:07:21', NULL, '2026-01-05 06:35:35', '2026-01-12 03:07:21'),
(3, 'App\\Models\\User', 2, 'auth_token', '986297a0dcd8f81aa506264c19b7480b4ddf3b0d15c98d1abcf221e8b19cf2a1', '[\"*\"]', '2026-01-13 02:53:27', NULL, '2026-01-05 06:36:58', '2026-01-13 02:53:27'),
(4, 'App\\Models\\User', 5, 'auth_token', '8847a9fbb6c26785a3ded51058d47d62221ab59f087c188336ccc9264a1bf590', '[\"*\"]', '2026-01-06 02:22:12', NULL, '2026-01-05 07:00:21', '2026-01-06 02:22:12'),
(5, 'App\\Models\\User', 6, 'auth_token', 'eefda53739ea8ac853c0035568efb72620692c7bc04249d84ee19f80b6152aa4', '[\"*\"]', '2026-01-06 02:41:14', NULL, '2026-01-06 02:23:11', '2026-01-06 02:41:14'),
(6, 'App\\Models\\User', 6, 'auth_token', '0e1fa2f0a02de9bd0b0b554554f2181448fa912d1d7d75d34b863a463ed4cd64', '[\"*\"]', '2026-01-09 08:18:47', NULL, '2026-01-06 02:34:05', '2026-01-09 08:18:47'),
(7, 'App\\Models\\User', 5, 'auth_token', '54e61893e75f25bef42a5c7d5839845cbc995d15c8a3f068051b7b53d28314b4', '[\"*\"]', '2026-01-07 08:07:50', NULL, '2026-01-06 02:41:32', '2026-01-07 08:07:50'),
(8, 'App\\Models\\User', 7, 'auth_token', 'bc2ee88fe19b82fd9ecc84dab15043f5e8c776ec3cbd0e1de15156eb8e9660f6', '[\"*\"]', '2026-01-06 08:16:56', NULL, '2026-01-06 07:34:06', '2026-01-06 08:16:56'),
(9, 'App\\Models\\User', 5, 'auth_token', '05faa3dc6fb82341b0564ddf418517f3d65e173a04b817d1f408ab3a3a169eec', '[\"*\"]', '2026-01-07 07:59:28', NULL, '2026-01-06 08:17:14', '2026-01-07 07:59:28'),
(10, 'App\\Models\\User', 7, 'auth_token', '1ac8b05ab460b119ac74994e912ef6f367ff3eb196d2ae689c79faa56eaa52f9', '[\"*\"]', '2026-01-09 05:58:00', NULL, '2026-01-07 02:31:42', '2026-01-09 05:58:00'),
(11, 'App\\Models\\User', 6, 'auth_token', 'd0a12afe8da22c435d9db8529d68d5ab801558740c4f2b00f9c4d653ad7352f8', '[\"*\"]', NULL, NULL, '2026-01-07 03:32:24', '2026-01-07 03:32:24'),
(12, 'App\\Models\\User', 6, 'auth_token', '0ae425fe37b5afcbe4d513c2b95847d54486b13327d84d20ae30e1b2f322a2e9', '[\"*\"]', '2026-01-07 03:37:35', NULL, '2026-01-07 03:32:34', '2026-01-07 03:37:35'),
(13, 'App\\Models\\User', 7, 'auth_token', 'd1197aee9c593abd4583e939816dee38290cc4eb447c5043256e7a994b226517', '[\"*\"]', '2026-01-12 03:06:48', NULL, '2026-01-07 05:50:10', '2026-01-12 03:06:48'),
(14, 'App\\Models\\User', 5, 'auth_token', 'b701ad8afee13e0a93de1b8a593a752cb2cdb3dc64ddc23af9adc24f416905da', '[\"*\"]', '2026-01-07 08:18:33', NULL, '2026-01-07 08:00:05', '2026-01-07 08:18:33'),
(15, 'App\\Models\\User', 6, 'auth_token', 'c7a33fbed46a6b18529faf5590135e165d723460488d77cc467aeec875f82ad0', '[\"*\"]', '2026-01-07 08:25:33', NULL, '2026-01-07 08:20:06', '2026-01-07 08:25:33'),
(16, 'App\\Models\\User', 7, 'auth_token', '91c98a0c2efb0c6cbfe92b9dbe531b6c163b2861d10ee16d491b264655f57bd1', '[\"*\"]', '2026-01-07 08:45:34', NULL, '2026-01-07 08:26:02', '2026-01-07 08:45:34'),
(17, 'App\\Models\\User', 5, 'auth_token', '32355191f3afe7f158052e542a7f50e5fef156a1a1770b4cdd4a1eacadc31eab', '[\"*\"]', '2026-01-08 02:05:40', NULL, '2026-01-07 08:27:54', '2026-01-08 02:05:40'),
(18, 'App\\Models\\User', 5, 'auth_token', 'cba0dbca79e0e4f51c8a8713e822370287877216cf2aa4f70d6df7e75b71a3e6', '[\"*\"]', '2026-01-09 05:39:53', NULL, '2026-01-08 02:06:10', '2026-01-09 05:39:53'),
(19, 'App\\Models\\User', 7, 'auth_token', '879ab626755e91392ddde1b1c84b934e2239572eb41a821ab76342de767eb975', '[\"*\"]', '2026-01-08 05:37:09', NULL, '2026-01-08 05:34:48', '2026-01-08 05:37:09'),
(20, 'App\\Models\\User', 6, 'auth_token', '08c06308673a930a6ae2fd5572bf5822a49b6ec0e4800cef8cfaa3562bc99603', '[\"*\"]', '2026-01-08 05:43:39', NULL, '2026-01-08 05:39:02', '2026-01-08 05:43:39'),
(21, 'App\\Models\\User', 7, 'auth_token', '2557e31dd9af65ecc418825627db6c59598be20fc292539756e5eeded7a89fbb', '[\"*\"]', '2026-01-13 02:21:35', NULL, '2026-01-08 05:48:51', '2026-01-13 02:21:35'),
(22, 'App\\Models\\User', 7, 'auth_token', '204552e418d47a50df000a4038cdfe05ce4fa59f98360b2631bf8df81345888d', '[\"*\"]', '2026-01-08 08:31:45', NULL, '2026-01-08 06:05:24', '2026-01-08 08:31:45'),
(23, 'App\\Models\\User', 5, 'auth_token', 'aec0a7e72b2725fafb64f8c75f05ce305b6eafe017321ac6d29e960204db0199', '[\"*\"]', '2026-01-08 08:33:35', NULL, '2026-01-08 08:32:13', '2026-01-08 08:33:35'),
(24, 'App\\Models\\User', 6, 'auth_token', '4782081b1ddce9179c41fba6fb2cf319a611f54bc67d260fd0f2480fcb9a069d', '[\"*\"]', '2026-01-09 01:54:51', NULL, '2026-01-08 08:33:53', '2026-01-09 01:54:51'),
(25, 'App\\Models\\User', 5, 'auth_token', 'd5296241d8ebb438e693034e32a4b4f7c5d6c915ea4c3a8326339b5fecdebb89', '[\"*\"]', '2026-01-09 02:54:02', NULL, '2026-01-09 01:57:17', '2026-01-09 02:54:02'),
(26, 'App\\Models\\User', 6, 'auth_token', '9f7c8ad77eadfee695ed90ca4f6b37b470f414c265c0430087f74e5547ef8f98', '[\"*\"]', '2026-01-09 02:54:52', NULL, '2026-01-09 02:54:17', '2026-01-09 02:54:52'),
(27, 'App\\Models\\User', 7, 'auth_token', '8b03768314907d98e2a8efb4e4bc61180cc8a30a303493801b0fae1819d20b33', '[\"*\"]', '2026-01-09 03:06:46', NULL, '2026-01-09 02:55:07', '2026-01-09 03:06:46'),
(28, 'App\\Models\\User', 5, 'auth_token', '7132bc1362910d9d29211a13e269015410e9e0b798d339127b43dbb566e3d3e7', '[\"*\"]', '2026-01-09 03:18:49', NULL, '2026-01-09 03:07:05', '2026-01-09 03:18:49'),
(29, 'App\\Models\\User', 7, 'auth_token', 'f1d81243084be2cc8f29b28a390d2aad6b7296f20b39e91ca3deb6fbbfd1a3c8', '[\"*\"]', '2026-01-09 04:54:09', NULL, '2026-01-09 03:19:08', '2026-01-09 04:54:09'),
(30, 'App\\Models\\User', 5, 'auth_token', 'db5d44b2558450140d7e33c992b1b76e3e793c1a11dab2ea356a931067d662ef', '[\"*\"]', '2026-01-09 05:05:54', NULL, '2026-01-09 04:55:37', '2026-01-09 05:05:54'),
(31, 'App\\Models\\User', 8, 'auth_token', '2804ec21f199fa8fed17845d6e3ad6697349f79b8bed287deec80da93fd5d903', '[\"*\"]', '2026-01-09 05:42:41', NULL, '2026-01-09 05:06:48', '2026-01-09 05:42:41'),
(32, 'App\\Models\\User', 8, 'auth_token', 'e411f6c94b38e69ec647f35074952172aae5d73452b7d4d1140e6aa39b1b5e4c', '[\"*\"]', '2026-01-09 05:43:16', NULL, '2026-01-09 05:27:50', '2026-01-09 05:43:16'),
(33, 'App\\Models\\User', 9, 'auth_token', '1f3e1974df1362f98325a0bc4e81314b786bdf8f7a32221fa26a988f8dda4cc4', '[\"*\"]', '2026-01-09 05:49:12', NULL, '2026-01-09 05:43:27', '2026-01-09 05:49:12'),
(34, 'App\\Models\\User', 6, 'auth_token', '1c0fee1b88a535b5722059af1b4746c609229772911aa858be23ca54fc49aba1', '[\"*\"]', '2026-01-09 06:26:21', NULL, '2026-01-09 05:44:03', '2026-01-09 06:26:21'),
(35, 'App\\Models\\User', 9, 'auth_token', '888e6389b5affc89e9daeb4b602af590b4e5ef5ccab1fb11b779f5bc706c6cc5', '[\"*\"]', '2026-01-09 08:48:06', NULL, '2026-01-09 05:49:25', '2026-01-09 08:48:06'),
(36, 'App\\Models\\User', 6, 'auth_token', 'a24b56c0bacb782b5f8e051408bf022a1fe440f37ae0f472560db6b0354bb996', '[\"*\"]', '2026-01-12 02:40:07', NULL, '2026-01-09 06:31:27', '2026-01-12 02:40:07'),
(37, 'App\\Models\\User', 5, 'auth_token', '0463ff1843b4611111d6b7b08ba6c96e3fc5ad1a2800a4ce0a5e9f3e976b2411', '[\"*\"]', '2026-01-13 02:20:38', NULL, '2026-01-09 07:32:00', '2026-01-13 02:20:38'),
(38, 'App\\Models\\User', 5, 'auth_token', '889f4f2d01931ab1471d23e9d05f31854f0882ae2b2ebd90505e0f04be7de759', '[\"*\"]', '2026-01-12 02:45:45', NULL, '2026-01-12 02:15:32', '2026-01-12 02:45:45'),
(39, 'App\\Models\\User', 7, 'auth_token', '41d6f1696ad42f51ea944c52a9055b71e861e82952d527eb9a304c7190d32514', '[\"*\"]', '2026-01-12 02:45:14', NULL, '2026-01-12 02:40:31', '2026-01-12 02:45:14'),
(40, 'App\\Models\\User', 8, 'auth_token', '9bc195533681af1f52b98d075961265a58d21f87202330796e1ee86aa2165931', '[\"*\"]', '2026-01-12 03:12:49', NULL, '2026-01-12 02:46:16', '2026-01-12 03:12:49'),
(41, 'App\\Models\\User', 7, 'auth_token', '364599f61ff5c07d05e1e5c8ebfe641fcb65d9b14dc421f1b8ddb6a712ba2c18', '[\"*\"]', '2026-01-12 03:12:51', NULL, '2026-01-12 02:47:08', '2026-01-12 03:12:51'),
(42, 'App\\Models\\User', 10, 'auth_token', '596b8f83c70766d7c59f3f673c756eac62c060daa54512403f98900c90cc4642', '[\"*\"]', '2026-01-12 03:32:28', NULL, '2026-01-12 03:16:07', '2026-01-12 03:32:28'),
(43, 'App\\Models\\User', 9, 'auth_token', '253e2d4199ac0f98c11a2c986e26ebc9ec08b4e77fd428ae4b3503863388e736', '[\"*\"]', '2026-01-12 04:03:04', NULL, '2026-01-12 03:24:21', '2026-01-12 04:03:04'),
(44, 'App\\Models\\User', 10, 'auth_token', '42e8a6bfd4a57c563ce2adacd6364b591c468ffae614681113609e607b37e338', '[\"*\"]', '2026-01-12 03:42:33', NULL, '2026-01-12 03:32:46', '2026-01-12 03:42:33'),
(45, 'App\\Models\\User', 10, 'auth_token', '7766d36cc62d3f396f641051d9cbee5a40564ea835f375d78fbf3409cf280db9', '[\"*\"]', '2026-01-12 03:38:44', NULL, '2026-01-12 03:36:13', '2026-01-12 03:38:44'),
(46, 'App\\Models\\User', 11, 'auth_token', 'b15dd351d79d2738cadf949426124721390ed9b91d7553ede99244d72c8cb142', '[\"*\"]', '2026-01-12 04:00:41', NULL, '2026-01-12 03:43:10', '2026-01-12 04:00:41'),
(47, 'App\\Models\\User', 7, 'auth_token', 'dac56b2454733319952db537582fd34b559c5f4548e05914764de6273767bcaa', '[\"*\"]', '2026-01-12 04:02:51', NULL, '2026-01-12 04:00:55', '2026-01-12 04:02:51'),
(48, 'App\\Models\\User', 12, 'auth_token', '825b9476bdac2e5733e3c174e0507e47d9018c20488e115d4d2113fcc35ff1dd', '[\"*\"]', '2026-01-12 04:17:28', NULL, '2026-01-12 04:08:42', '2026-01-12 04:17:28'),
(49, 'App\\Models\\User', 13, 'auth_token', '5672cde8a122f24df2ed82a58c75bd453bda11dfe952cf51a92cdcdb7cd3db27', '[\"*\"]', '2026-01-12 04:24:59', NULL, '2026-01-12 04:23:52', '2026-01-12 04:24:59'),
(50, 'App\\Models\\User', 8, 'auth_token', 'fad67c53b24a1e093e82d44739a162b56a8480c25ef11c1c8706e8f314e800c0', '[\"*\"]', '2026-01-12 06:10:05', NULL, '2026-01-12 05:30:49', '2026-01-12 06:10:05'),
(51, 'App\\Models\\User', 13, 'auth_token', '79ca1acfc7df03f105e4c557257c8b8073c3ce1429dba2898a398633405a0283', '[\"*\"]', '2026-01-12 05:58:28', NULL, '2026-01-12 05:39:17', '2026-01-12 05:58:28'),
(52, 'App\\Models\\User', 13, 'auth_token', '5274d7d287c836a6a437a78779e6019e499ec998f73786c6ddd4119bedee183d', '[\"*\"]', '2026-01-12 06:09:59', NULL, '2026-01-12 05:58:41', '2026-01-12 06:09:59'),
(53, 'App\\Models\\User', 13, 'auth_token', 'cedfbf843286c32c4a70f594772bf4ed9e7715f3862521be68941a7671051bcf', '[\"*\"]', '2026-01-12 06:10:27', NULL, '2026-01-12 06:02:50', '2026-01-12 06:10:27'),
(54, 'App\\Models\\User', 13, 'auth_token', 'e12523f276bfaf0a26c40e7f45dbc7445e5069b132e7904ae397bad6c4c3f260', '[\"*\"]', '2026-01-12 06:27:02', NULL, '2026-01-12 06:10:15', '2026-01-12 06:27:02'),
(55, 'App\\Models\\User', 8, 'auth_token', 'd5f834a1477da342f81f84da817542adb07b9ef5cde9402f852e8743c6e2835b', '[\"*\"]', '2026-01-12 06:27:02', NULL, '2026-01-12 06:10:34', '2026-01-12 06:27:02'),
(56, 'App\\Models\\User', 5, 'auth_token', 'e856b23c87cfbd20f184846c0c45a26e6fc4ab6439f6f122e28cb5b0e4084552', '[\"*\"]', '2026-01-12 06:49:38', NULL, '2026-01-12 06:48:35', '2026-01-12 06:49:38'),
(57, 'App\\Models\\User', 13, 'auth_token', '4e08f0504ad2f6b30c5c587e5e0e892e5700dcd5129b4ffea31dc6d7ff36ec14', '[\"*\"]', '2026-01-13 04:29:25', NULL, '2026-01-12 06:48:51', '2026-01-13 04:29:25'),
(58, 'App\\Models\\User', 5, 'auth_token', '5067ebf35e8e887d6160c79e36f65ea855cbc38ff127ede8841eec14a36a5119', '[\"*\"]', '2026-01-13 05:04:38', NULL, '2026-01-12 06:49:57', '2026-01-13 05:04:38'),
(59, 'App\\Models\\User', 13, 'auth_token', 'a92c2f323cdcc9c489d2ea6704e563064e0d99a9b81dcefbb6833f01bde12c59', '[\"*\"]', '2026-01-13 05:01:04', NULL, '2026-01-13 02:12:57', '2026-01-13 05:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `job_id`, `customer_id`, `driver_id`, `rating`, `review`, `created_at`, `updated_at`) VALUES
(1, 1, 7, 7, 4, NULL, '2026-01-07 06:01:31', '2026-01-07 06:01:31');

-- --------------------------------------------------------

--
-- Table structure for table `saved_addresses`
--

CREATE TABLE `saved_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `label` varchar(191) NOT NULL,
  `address` varchar(191) NOT NULL,
  `postcode` varchar(191) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `distance_meters` int(10) UNSIGNED DEFAULT NULL,
  `duration_seconds` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saved_addresses`
--

INSERT INTO `saved_addresses` (`id`, `user_id`, `booking_id`, `label`, `address`, `postcode`, `latitude`, `longitude`, `distance_meters`, `duration_seconds`, `created_at`, `updated_at`) VALUES
(11, 5, 11, 'Wast Collection Pickup', 'H6X6+CXC, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.59918758, 73.21279574, 14942, 1598, '2026-01-05 08:05:05', '2026-01-12 02:22:04'),
(12, 5, 11, 'HOme Removal Delivery', 'Arif Karyana store, Azizabad, Rawalpindi, Punjab, Pakistan', '', 33.58468879, 73.03536225, 23030, 2399, '2026-01-05 08:05:05', '2026-01-05 08:07:37'),
(13, 5, 11, 'HOme Removal Delivery', 'H2MP+P27, Tench Bhata, Rawalpindi, Punjab, Pakistan', '', 33.58434915, 73.03513359, 23013, 2394, '2026-01-05 08:08:56', '2026-01-05 08:08:56'),
(14, 5, 11, 'Wast Collection Delivery', 'Shop no 55 alhajj market DHA 2 2, DHA Phase II, Rawalpindi, Islamabad Capital Territory, Pakistan', '', 33.53049103, 73.16707682, 14942, 1598, '2026-01-05 08:26:26', '2026-01-12 02:22:04'),
(15, 6, 15, 'Wast Collection Pickup', 'House', '75500', 24.80460000, 67.07240000, 5400, 780, '2026-01-06 03:31:45', '2026-01-06 03:31:45'),
(16, 6, 15, 'Wast Collection Delivery', 'Clifton', '75600', 24.81380000, 67.03170000, 5400, 780, '2026-01-06 03:31:45', '2026-01-06 03:31:45'),
(17, 5, 11, 'Wast Collection Delivery', 'Japan Road, Al Hamra Avenue Housing Scheme, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.62148734, 73.24069005, 5216, 711, '2026-01-06 06:51:47', '2026-01-06 06:51:47'),
(18, 6, 18, 'Wast Collection Pickup', 'H6X6+CXC, Gulberg Residencia, Islamabad', '44000', 33.59938000, 73.21367000, 5216, 711, '2026-01-06 07:16:39', '2026-01-07 03:35:52'),
(19, 6, 18, 'Wast Collection Delivery', 'Arif Karyana Store, Azizabad, Rawalpindi', '46000', 33.58468000, 73.03536000, 5216, 711, '2026-01-06 07:16:39', '2026-01-07 03:35:52'),
(20, 5, 11, 'Wast Collection Delivery', 'J62C+M6R, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60282516, 73.21977485, 1086, 190, '2026-01-07 02:08:25', '2026-01-07 02:08:25'),
(21, 5, 11, 'Wast Collection Delivery', '22, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60556623, 73.22083130, 1953, 275, '2026-01-07 02:16:58', '2026-01-09 03:15:43'),
(22, 5, 11, 'Wast Collection Delivery', 'J64H+J49, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60623696, 73.22775073, 2666, 380, '2026-01-07 02:33:10', '2026-01-07 02:33:10'),
(23, 5, 11, 'Wast Collection Delivery', 'E-11, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60681666, 73.21242157, 1758, 253, '2026-01-07 02:35:30', '2026-01-07 02:35:30'),
(24, 7, 24, 'Wast Collection Pickup', 'H6X6+CXC, Gulberg Residencia, Islamabad', '44000', 33.59938000, 73.21367000, 5216, 711, '2026-01-07 02:35:39', '2026-01-07 03:34:30'),
(25, 7, 24, 'Wast Collection Delivery', 'Arif Karyana Store, Azizabad, Rawalpindi', '46000', 33.58468000, 73.03536000, 5216, 711, '2026-01-07 02:35:39', '2026-01-07 03:34:30'),
(26, 5, 11, 'Wast Collection Delivery', 'J784+32, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60570892, 73.22366070, 2218, 313, '2026-01-07 02:43:06', '2026-01-08 02:10:36'),
(27, 5, 11, 'Wast Collection Delivery', '21, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60452270, 73.22158936, 2473, 363, '2026-01-07 03:10:29', '2026-01-07 03:45:42'),
(28, 5, 11, 'Wast Collection Delivery', 'E Block Park, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60120745, 73.21602948, 367, 65, '2026-01-07 03:46:29', '2026-01-07 03:46:29'),
(29, 5, 11, 'Wast Collection Delivery', 'F198, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60419375, 73.21748994, 964, 212, '2026-01-07 03:50:24', '2026-01-07 03:51:52'),
(30, 5, 11, 'Wast Collection Delivery', '559, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60417644, 73.22164468, 1278, 251, '2026-01-07 03:52:43', '2026-01-07 03:52:43'),
(31, 5, 11, 'Wast Collection Delivery', '142, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60264672, 73.21918879, 2315, 302, '2026-01-07 03:56:24', '2026-01-07 03:58:27'),
(32, 5, 11, 'Wast Collection Delivery', 'International Airport, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.58759187, 73.21921963, 3548, 524, '2026-01-07 03:59:08', '2026-01-07 04:05:52'),
(33, 5, 11, 'Wast Collection Delivery', 'J639+MX9, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60333088, 73.21998037, 1052, 179, '2026-01-07 04:06:49', '2026-01-07 04:06:49'),
(34, 5, 11, 'Wast Collection Delivery', '438, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60441519, 73.22071865, 1230, 224, '2026-01-07 06:02:41', '2026-01-07 06:02:41'),
(35, 5, 11, 'Wast Collection Delivery', 'J64C+9GF, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60590886, 73.22232798, 2095, 300, '2026-01-08 02:31:15', '2026-01-08 02:31:15'),
(36, 5, 11, 'Wast Collection Delivery', 'Gulberg Recidencia, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60972263, 73.22995953, 2788, 376, '2026-01-08 08:32:39', '2026-01-08 08:32:39'),
(37, 5, 11, 'Wast Collection Delivery', 'J66C+HQQ, Darwala, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.61027326, 73.22164401, 2678, 368, '2026-01-09 01:57:40', '2026-01-09 01:57:40'),
(38, 5, 11, 'Wast Collection Delivery', '41, Gulberg Residencia, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.60539896, 73.21968332, 1846, 261, '2026-01-09 02:33:44', '2026-01-09 02:33:44'),
(39, 5, 39, 'Wast Collection Pickup', 'Moryal, Napier Quarter, Karachi, Sindh, Pakistan', '', 24.86069992, 67.00110007, 1473, 389, '2026-01-09 04:57:55', '2026-01-09 04:57:55'),
(40, 5, 39, 'Wast Collection Delivery', 'V274+MG5, Old Haji Camp, Karachi, Sindh, Pakistan', '', 24.86407687, 67.00630590, 1473, 389, '2026-01-09 04:57:55', '2026-01-09 04:57:55'),
(41, 8, 41, 'Wast Collection Pickup', 'Moryal, Napier Quarter, Karachi, Sindh, Pakistan', '', 24.86069992, 67.00110007, 2526, 718, '2026-01-09 05:33:25', '2026-01-09 05:40:58'),
(42, 8, 41, 'Wast Collection Delivery', 'V293+6GW, Rexer Lanes, Karachi, Sindh, Pakistan', '', 24.86807611, 67.00359084, 1421, 438, '2026-01-09 05:33:25', '2026-01-09 05:33:25'),
(43, 8, 41, 'Wast Collection Delivery', 'V283+WMJ, Jinnahabad 1, Karachi, Sindh, Pakistan', '', 24.86730742, 67.00438444, 2526, 718, '2026-01-09 05:40:58', '2026-01-09 05:40:58'),
(58, 5, 58, 'Wast Collection Pickup', 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.61147476, 73.17331262, 10550, 890, '2026-01-12 02:39:15', '2026-01-13 02:05:08'),
(59, 5, 58, 'Wast Collection Delivery', 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', '', 33.62913259, 73.11355535, 10550, 890, '2026-01-12 02:39:15', '2026-01-13 02:05:08'),
(60, 8, 60, 'Wast Collection Pickup', 'J56F+H8P, Gulberg Greens, Islamabad, Islamabad Capital Territory, Pakistan', '', 33.61147476, 73.17331262, 15061, 1317, '2026-01-12 02:46:56', '2026-01-12 06:12:04'),
(61, 8, 60, 'Wast Collection Delivery', 'Al noor colony KRL Road, Rawalpindi, Islamabad Capital Territory, Pakistan', '', 33.62913259, 73.11355535, 10550, 890, '2026-01-12 02:46:56', '2026-01-12 05:31:19'),
(62, 8, 60, 'Wast Collection Delivery', 'Rawal Park, Chah Sultan, Rawalpindi, Punjab, Pakistan', '', 33.62718093, 73.07451613, 17816, 1487, '2026-01-12 02:50:23', '2026-01-12 02:50:23'),
(63, 8, 60, 'Wast Collection Delivery', 'Shop no 55 alhajj market DHA 2 2, DHA Phase II, Rawalpindi, Islamabad Capital Territory, Pakistan', '', 33.53049103, 73.16707682, 15061, 1317, '2026-01-12 06:12:04', '2026-01-12 06:12:04');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `pricing_type` enum('per_mile','per_hour','per_ton_kg') NOT NULL DEFAULT 'per_mile',
  `icon` varchar(191) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `slug`, `description`, `pricing_type`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Wast Collection', 'wast-collection', 'Wast Collection Desc', 'per_hour', NULL, 1, '2026-01-05 06:47:32', '2026-01-05 06:49:34'),
(2, 'Skip Hire', 'skip-hire', 'Skip Hire desc', 'per_mile', NULL, 1, '2026-01-05 06:47:58', '2026-01-05 06:47:58'),
(3, 'HOme Removal', 'home-removal', 'Home Removal Desc', 'per_mile', NULL, 1, '2026-01-05 06:48:23', '2026-01-05 06:48:23'),
(4, 'Muck Away', 'muck-away', 'Muck Away Desc', 'per_ton_kg', NULL, 1, '2026-01-06 05:04:18', '2026-01-06 05:04:18'),
(5, 'Cement Wasty', 'cement-wasty', 'Cement Wasty Desc', 'per_ton_kg', NULL, 0, '2026-01-06 07:30:42', '2026-01-09 08:57:32');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('n9t4MqxulPSP6G8IXxXDFhMc2ferjcqwQHf1p3fb', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRGdxOWZiZGhySHYwUTlBOXI0b3U1amlqSUYyN2VSZjhqNkNkeHNkZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9ub3RpZmljYXRpb25zL2JlbGwiO3M6NToicm91dGUiO3M6MjQ6ImFkbWluLm5vdGlmaWNhdGlvbnMuYmVsbCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7fQ==', 1768299278);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(191) NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_commission` decimal(5,2) NOT NULL DEFAULT 15.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` varchar(191) NOT NULL,
  `job_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `driver_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `admin_commission` decimal(10,2) NOT NULL DEFAULT 0.00,
  `driver_earning` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','wallet') NOT NULL DEFAULT 'card',
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `transaction_id`, `job_id`, `customer_id`, `driver_id`, `amount`, `admin_commission`, `driver_earning`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(1, 'TXN-1767783468-9956', 1, 7, 7, 1500.00, 225.00, 1275.00, 'card', 'completed', '2026-01-07 05:57:48', '2026-01-07 05:57:48'),
(5, 'TXN-1768204177-3087', 32, 8, 7, 7.42, 1.11, 6.31, 'card', 'completed', '2026-01-12 02:49:37', '2026-01-12 02:49:37'),
(6, 'TXN-1768216227-1796', 40, 8, 13, 500.00, 75.00, 425.00, 'cash', 'completed', '2026-01-12 06:10:27', '2026-01-12 06:10:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `password` varchar(191) NOT NULL,
  `role` enum('admin','customer','driver') NOT NULL DEFAULT 'customer',
  `address` varchar(191) DEFAULT NULL,
  `postcode` varchar(191) DEFAULT NULL,
  `profile_image` varchar(191) DEFAULT NULL,
  `status` enum('active','blocked') NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `address`, `postcode`, `profile_image`, `status`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Shazad khan', 'shazad@example.com', '03441234566', '$2y$12$UjJiYhjzr2GVfNxoAUOLJ.o9JqUZSQciMgQNjQn7PQEwzylHObJE6', 'customer', '123 Main Street, City', '12345', NULL, 'active', NULL, NULL, '2026-01-05 06:33:32', '2026-01-12 02:03:18'),
(2, 'alikhan', 'ali@example.com', '03441237566', '$2y$12$ps8U6b1V6k6qf2lnZ81BtOWsLotUg8EEzpJ7cJttKtDOanDEdrwbO', 'customer', '123 Main Street, City', '12345', NULL, 'active', NULL, NULL, '2026-01-05 06:35:35', '2026-01-05 06:35:35'),
(4, 'Admin', 'admin@goclear.com', '03000000000', '$2y$12$EA97ddZJXAwIPOwnF63HGO2bR8xkyEnGsjyDyDUONdahe.IOPajA2', 'admin', NULL, NULL, 'admins/E6aZsk9VCZoN5WBqujVUn1xwE2tNQ4gTdVoVPtvs.jpg', 'active', NULL, '2O2FuArPS8Iu9vgj8X14H1obDhYfa1fIWicRUblBHdkZJPhSweGQykPM561N', '2026-01-05 06:45:08', '2026-01-12 07:30:17'),
(5, 'Customer1', 'customer1@gmail.com', '8767076', '$2y$12$9kigAvC7FAgqpg5WTlJzgu/5/lSxlsqdVb6/o.4LT5x4wf6aoAOv6', 'customer', NULL, NULL, NULL, 'active', NULL, NULL, '2026-01-05 07:00:21', '2026-01-05 07:00:21'),
(6, 'Driver 1', 'driver1@gmail.com', '875784570', '$2y$12$COov4roVPxKRP0Kerfh2r.ZLe1iBkF0WZSXyiNhoW3ODLcEmXXaxO', 'driver', 'testing', '1122', NULL, 'active', NULL, NULL, '2026-01-06 02:23:10', '2026-01-06 02:23:10'),
(7, 'Driver 2', 'driver2@gmail.com', '8457876', '$2y$12$eh2LAWALgq7/M6QI6SGYMuLIOdOVd8ScewYNCf3zplOEhjSeSgDB6', 'driver', 'test', '234', NULL, 'active', NULL, NULL, '2026-01-06 07:34:06', '2026-01-10 04:22:40'),
(8, 'customer2', 'customer2@gmail.com', '875787', '$2y$12$G8JX1es/EqFhCq4LUMpVBuWveJ6YJdnT28pO4Gx37.b03ee0zRR/u', 'customer', NULL, NULL, NULL, 'active', NULL, NULL, '2026-01-09 05:06:48', '2026-01-09 05:06:48'),
(13, 'Driver 3', 'driver3@gmail.com', '5437876', '$2y$12$tshnS0br7vp4vD14QzflHuXK68A5lYA40S05.iw2/PvHbkwzEvIIi', 'driver', 'testing', '4567', NULL, 'active', NULL, NULL, '2026-01-12 04:23:52', '2026-01-12 04:23:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `driver_availabilities`
--
ALTER TABLE `driver_availabilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_availabilities_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `driver_earnings`
--
ALTER TABLE `driver_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_earnings_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `driver_locations`
--
ALTER TABLE `driver_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `driver_locations_driver_profile_id_unique` (`driver_profile_id`),
  ADD KEY `driver_locations_lat_lng_index` (`lat`,`lng`);

--
-- Indexes for table `driver_pricings`
--
ALTER TABLE `driver_pricings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_pricings_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `driver_profiles`
--
ALTER TABLE `driver_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `driver_services`
--
ALTER TABLE `driver_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_services_driver_id_foreign` (`driver_id`),
  ADD KEY `driver_services_service_id_foreign` (`service_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jobs_job_number_unique` (`job_number`),
  ADD KEY `jobs_customer_id_foreign` (`customer_id`),
  ADD KEY `jobs_driver_id_foreign` (`driver_id`),
  ADD KEY `jobs_service_id_foreign` (`service_id`);

--
-- Indexes for table `job_requests`
--
ALTER TABLE `job_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_requests_job_id_driver_id_unique` (`job_id`,`driver_id`),
  ADD KEY `job_requests_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `notifications_user_id_type_index` (`user_id`,`type`),
  ADD KEY `notifications_created_at_index` (`created_at`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ratings_job_id_foreign` (`job_id`),
  ADD KEY `ratings_customer_id_foreign` (`customer_id`),
  ADD KEY `ratings_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `saved_addresses`
--
ALTER TABLE `saved_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saved_addresses_user_id_booking_id_index` (`user_id`,`booking_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `services_slug_unique` (`slug`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_transaction_id_unique` (`transaction_id`),
  ADD KEY `transactions_job_id_foreign` (`job_id`),
  ADD KEY `transactions_customer_id_foreign` (`customer_id`),
  ADD KEY `transactions_driver_id_foreign` (`driver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `driver_availabilities`
--
ALTER TABLE `driver_availabilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `driver_earnings`
--
ALTER TABLE `driver_earnings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `driver_locations`
--
ALTER TABLE `driver_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `driver_pricings`
--
ALTER TABLE `driver_pricings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `driver_profiles`
--
ALTER TABLE `driver_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `driver_services`
--
ALTER TABLE `driver_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `job_requests`
--
ALTER TABLE `job_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `saved_addresses`
--
ALTER TABLE `saved_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `driver_availabilities`
--
ALTER TABLE `driver_availabilities`
  ADD CONSTRAINT `driver_availabilities_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_earnings`
--
ALTER TABLE `driver_earnings`
  ADD CONSTRAINT `driver_earnings_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_locations`
--
ALTER TABLE `driver_locations`
  ADD CONSTRAINT `driver_locations_driver_profile_id_foreign` FOREIGN KEY (`driver_profile_id`) REFERENCES `driver_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_pricings`
--
ALTER TABLE `driver_pricings`
  ADD CONSTRAINT `driver_pricings_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_profiles`
--
ALTER TABLE `driver_profiles`
  ADD CONSTRAINT `driver_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_services`
--
ALTER TABLE `driver_services`
  ADD CONSTRAINT `driver_services_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `driver_services_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `jobs_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jobs_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_requests`
--
ALTER TABLE `job_requests`
  ADD CONSTRAINT `job_requests_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_requests_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `saved_addresses`
--
ALTER TABLE `saved_addresses`
  ADD CONSTRAINT `saved_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
