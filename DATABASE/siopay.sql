-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 27, 2023 at 02:50 AM
-- Server version: 10.5.20-MariaDB-cll-lve-log
-- PHP Version: 7.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siopay_logistics`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

CREATE TABLE `beneficiaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `country_code` varchar(45) NOT NULL,
  `relationship` varchar(45) NOT NULL,
  `gender` varchar(45) NOT NULL,
  `deleted` tinyint(4) DEFAULT 0,
  `extra_datas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beneficiaries`
--

INSERT INTO `beneficiaries` (`id`, `user_id`, `name`, `phone`, `country_code`, `relationship`, `gender`, `deleted`, `extra_datas`, `created_at`, `updated_at`) VALUES
(3, 3, 'Dev Ben', '+301234567864', 'GR', 'Father-in-law', 'Male', 0, NULL, '2023-12-24 17:15:03', '2023-12-25 05:12:47'),
(4, 3, 'agsh hrj', '+411234564896645', 'IT', 'Grand Daughter', 'Female', 0, NULL, '2023-12-26 22:10:31', '2023-12-26 22:10:44'),
(6, 3, 'Wal Tim', '+416655555584455', 'IT', 'Son-in-law', 'Male', 0, NULL, '2023-12-26 22:54:41', '2023-12-27 11:57:22'),
(7, 3, 'fffff ggt', '+39', 'IT', 'Father-in-law', 'Male', 0, NULL, '2023-12-27 11:56:17', '2023-12-27 11:56:17');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary_accounts`
--

CREATE TABLE `beneficiary_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `beneficiary_id` bigint(20) UNSIGNED NOT NULL,
  `beneficiary_account_number` varchar(255) NOT NULL,
  `beneficiary_account_name` varchar(255) NOT NULL,
  `beneficiary_bank_name` varchar(255) NOT NULL,
  `beneficiary_bank_code` varchar(255) NOT NULL,
  `extra_datas` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beneficiary_accounts`
--

INSERT INTO `beneficiary_accounts` (`id`, `user_id`, `beneficiary_id`, `beneficiary_account_number`, `beneficiary_account_name`, `beneficiary_bank_name`, `beneficiary_bank_code`, `extra_datas`, `created_at`, `updated_at`) VALUES
(2, 3, 3, '12369887085', 'Dev Ben', 'European Bank 1', '174068', NULL, '2023-12-24 18:51:55', '2023-12-24 18:51:55'),
(7, 3, 4, '2666494946', 'agsh hrj', 'European Bank 2', '1874057', NULL, '2023-12-26 22:10:41', '2023-12-26 22:10:41'),
(8, 3, 6, '2858888698', 'Wal Tim', 'European Bank 2', '1874057', NULL, '2023-12-26 22:55:37', '2023-12-26 22:55:37');

-- --------------------------------------------------------

--
-- Table structure for table `couriers`
--

CREATE TABLE `couriers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `phone_alt` varchar(255) DEFAULT NULL,
  `current_location` bigint(20) UNSIGNED DEFAULT NULL,
  `vehicle_type` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','decommisioned') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `phone_alt` varchar(255) DEFAULT NULL,
  `address1` text DEFAULT NULL,
  `address2` text DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dispatchers`
--

CREATE TABLE `dispatchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `phone_alt` varchar(255) DEFAULT NULL,
  `address1` text DEFAULT NULL,
  `address2` text DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `e_u_funds_transfer_rates`
--

CREATE TABLE `e_u_funds_transfer_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `s_country_eu` varchar(255) NOT NULL,
  `rx_country_eu` varchar(255) NOT NULL,
  `calc` enum('perc','fixed') NOT NULL,
  `commision` double NOT NULL DEFAULT 1,
  `ex_rate` double NOT NULL DEFAULT 1,
  `min_amt` double NOT NULL DEFAULT 0,
  `max_amt` double NOT NULL DEFAULT 999,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `e_u_funds_transfer_rates`
--

INSERT INTO `e_u_funds_transfer_rates` (`id`, `name`, `s_country_eu`, `rx_country_eu`, `calc`, `commision`, `ex_rate`, `min_amt`, `max_amt`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Premium-momo mobile money', 'Italy', 'Nigeria', 'perc', 2, 1200, 1, 999, 1, '2023-10-25 08:39:56', '2023-10-25 08:39:56'),
(2, 'Bronze pickup', 'Italy', 'Nigeria', 'perc', 5, 1400, 10, 970, 1, '2023-10-28 13:46:52', '2023-10-28 13:46:52'),
(3, 'Bronze pickup', 'Italy', 'Greece', 'perc', 2, 1, 10, 970, 1, '2023-10-28 13:46:52', '2023-10-28 13:46:52');

-- --------------------------------------------------------

--
-- Table structure for table `e_u_fund_transfer_orders`
--

CREATE TABLE `e_u_fund_transfer_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `walk_in_customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dispatcher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `e_u_funds_transfer_rate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tracking_id` varchar(255) DEFAULT NULL,
  `rx_surname` varchar(255) DEFAULT NULL,
  `rx_name` varchar(255) DEFAULT NULL,
  `rx_phone` varchar(255) DEFAULT NULL,
  `rx_email` varchar(255) DEFAULT NULL,
  `rx_bank_name` varchar(255) DEFAULT NULL,
  `rx_bank_routing_no` varchar(255) DEFAULT NULL,
  `rx_bank_swift_code` varchar(255) DEFAULT NULL,
  `rx_bank_account_name` varchar(255) DEFAULT NULL,
  `rx_bank_account_number` varchar(255) DEFAULT NULL,
  `rx_country` varchar(255) DEFAULT NULL,
  `s_country` varchar(255) DEFAULT NULL,
  `s_amount` double DEFAULT NULL,
  `rx_amount` double DEFAULT NULL,
  `tx_status` enum('unpaid','pending','done','rejected') NOT NULL DEFAULT 'unpaid',
  `tx_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intl_funds_transfer_rates`
--

CREATE TABLE `intl_funds_transfer_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `s_country` varchar(255) NOT NULL,
  `rx_country` varchar(255) NOT NULL,
  `s_currency` varchar(255) NOT NULL,
  `rx_currency` varchar(255) NOT NULL,
  `ex_rate` double NOT NULL DEFAULT 1,
  `calc` enum('perc','fixed') NOT NULL,
  `commision` double NOT NULL DEFAULT 1,
  `min_amt` double NOT NULL DEFAULT 0,
  `max_amt` double NOT NULL DEFAULT 999,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `intl_funds_transfer_rates`
--

INSERT INTO `intl_funds_transfer_rates` (`id`, `name`, `s_country`, `rx_country`, `s_currency`, `rx_currency`, `ex_rate`, `calc`, `commision`, `min_amt`, `max_amt`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Premium-momo mobile money', 'Italy', 'Nigeria', 'EUR - Euro', 'NGN - Nigerian Naira', 1200, 'perc', 2, 1, 999, 1, '2023-10-25 08:39:56', '2023-10-25 08:39:56'),
(2, 'Bronze pickup', 'Italy', 'Nigeria', 'EUR - Euro', 'NGN - Nigerian Naira', 1400, 'perc', 5, 10, 970, 1, '2023-10-28 13:46:52', '2023-10-28 13:46:52'),
(3, 'Bronze pickup', 'Italy', 'Greece', 'EUR - Euro', 'EUR - Euro', 1, 'perc', 2, 10, 970, 1, '2023-10-28 13:46:52', '2023-10-28 13:46:52');

-- --------------------------------------------------------

--
-- Table structure for table `intl_fund_transfer_orders`
--

CREATE TABLE `intl_fund_transfer_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `walk_in_customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dispatcher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `e_u_funds_transfer_rate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tracking_id` varchar(255) DEFAULT NULL,
  `rx_surname` varchar(255) DEFAULT NULL,
  `rx_name` varchar(255) DEFAULT NULL,
  `rx_phone` varchar(255) DEFAULT NULL,
  `rx_email` varchar(255) DEFAULT NULL,
  `rx_bank_name` varchar(255) DEFAULT NULL,
  `rx_bank_routing_no` varchar(255) DEFAULT NULL,
  `rx_bank_swift_code` varchar(255) DEFAULT NULL,
  `rx_bank_account_name` varchar(255) DEFAULT NULL,
  `rx_bank_account_number` varchar(255) DEFAULT NULL,
  `rx_country` varchar(255) DEFAULT NULL,
  `s_country` varchar(255) DEFAULT NULL,
  `rx_currency` varchar(255) DEFAULT NULL,
  `s_currency` varchar(255) DEFAULT NULL,
  `s_amount` double DEFAULT NULL,
  `rx_amount` double DEFAULT NULL,
  `tx_status` enum('unpaid','pending','done','rejected') NOT NULL DEFAULT 'unpaid',
  `tx_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc`
--

CREATE TABLE `kyc` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `document_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_front` varchar(255) DEFAULT NULL,
  `document_back` varchar(255) DEFAULT NULL,
  `selfie` text DEFAULT NULL,
  `proof_of_address_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `proof_of_address` text DEFAULT NULL,
  `kyc_level` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `personal_information` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kyc`
--

INSERT INTO `kyc` (`id`, `user_id`, `document_type_id`, `document_front`, `document_back`, `selfie`, `proof_of_address_type_id`, `proof_of_address`, `kyc_level`, `status`, `rejection_reason`, `created_at`, `updated_at`, `personal_information`) VALUES
(2, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'pending', NULL, '2023-12-15 09:37:06', '2023-12-15 09:37:06', '{\"firstname\":\"Kvng\",\"lastname\":\"Kvng\",\"othername\":\"KC\",\"email\":\"kvng656@gmail.com\",\"phone\":\"+2348038752155\",\"address\":\"Somewhere on earth, Jos\",\"nationality\":\"Nigeria\",\"place_of_birth\":\"Jos\",\"occupation\":\"Software Engineer\",\"fiscal_code\":\"KAY SAN87D15 A00\",\"gender\":\"Male\"}'),
(3, 3, 1, 'documents/gThXbWz4uNAuRUHivCCESb4qvHKbhLC647FHGXMp.jpg', 'documents/76fp3c8aGTFKTNLdkDj7pOusgVdn3LY1kboW2Jzj.jpg', 'selfies/m0AZz59f9EokokI7kg0pwlD4D9q6R5mAnyl9IG8p.jpg', 1, 'proofs_of_address/Jde3eD8LPE57nStIDUozFyCCTD7yUf5Qv46QouZE.jpg', 2, 'approved', NULL, '2023-12-23 20:46:03', '2023-12-23 22:21:39', '{\"firstname\":\"Samson\",\"lastname\":\"Johnson\",\"email\":\"johnsonsamson23@gmail.com\",\"phone\":\"+412345767889464\",\"address\":\"FCT, Jos\",\"nationality\":\"Nigeria\",\"place_of_birth\":\"Nigeria\",\"occupation\":\"Farmer\",\"fiscal_code\":\"2368939373738383\",\"gender\":\"Male\"}'),
(4, 5, 1, 'documents/yPtFrPyHRFVmsA4E5mvqLfzyO0BWCvpCeyM2CbLo.jpg', 'documents/kempuzuV4RhR05GSsdx5lZID0AmzJA1oQ7HOlZ5T.jpg', 'selfies/bIqLbvhTBJjSaO3EgzJiGqajcb61KDVkOT9yOkCZ.jpg', 2, 'proofs_of_address/Gi3qiZfyzg8dT2SpDcIKwxm3sipytdAkLLWdv3xI.jpg', 4, 'pending', NULL, '2023-12-26 22:30:21', '2023-12-26 22:31:32', '{\"firstname\":\"djdj\",\"lastname\":\"hshs\",\"othername\":null,\"email\":\"1@a.com\",\"phone\":\"+411223649494949\",\"address\":\"hshsh\",\"nationality\":\"\\u00c5land Islands\",\"place_of_birth\":\"\\u00c5land Islands\",\"occupation\":\"bdhshs\",\"fiscal_code\":\"1234567890385567\",\"gender\":\"Male\"}');

-- --------------------------------------------------------

--
-- Table structure for table `kyc_address_proof_types`
--

CREATE TABLE `kyc_address_proof_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kyc_address_proof_types`
--

INSERT INTO `kyc_address_proof_types` (`id`, `document_name`, `created_at`, `updated_at`) VALUES
(1, 'Utility Bill (Electricity bill, Water bill, etc.)', NULL, NULL),
(2, 'Credit Card Bill or Statement', NULL, NULL),
(3, 'Bank Statement', NULL, NULL),
(4, 'Bank Letter', NULL, NULL),
(5, 'Social Insurance Statement', NULL, NULL),
(6, 'Paycheck', NULL, NULL),
(7, 'Letter from Public Authority (Court, etc.)', NULL, NULL),
(8, 'Insurance Policy (for your car or home)', NULL, NULL),
(9, 'Rental or Mortgage Contract/Statement', NULL, NULL),
(10, 'Car\'s Registration', NULL, NULL),
(11, 'Change of Address Form', NULL, NULL),
(12, 'DD214', NULL, NULL),
(13, 'Official Letter from Employer or School', NULL, NULL),
(14, 'Voter Registration Confirmation', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kyc_document_type`
--

CREATE TABLE `kyc_document_type` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kyc_document_type`
--

INSERT INTO `kyc_document_type` (`id`, `document_name`, `created_at`, `updated_at`) VALUES
(1, 'EU National ID Card / Carta d\'Identita\' Europea', NULL, NULL),
(2, 'ID Card / Carta Di Identità', NULL, NULL),
(3, 'EU Driving License / Patente Di Guida', NULL, NULL),
(4, 'Others / Altro', NULL, NULL),
(5, 'Passport / Passaporto', NULL, NULL),
(6, 'EU Resident Card / Permesso Di Soggiorno', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) NOT NULL,
  `longitude` varchar(255) NOT NULL,
  `postcode` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_08_19_000000_create_failed_jobs_table', 1),
(9, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(10, '2023_10_01_175034_create_locations_table', 1),
(11, '2023_10_01_175530_create_customers_table', 1),
(12, '2023_10_01_181748_create_couriers_table', 1),
(13, '2023_10_01_181821_create_dispatchers_table', 1),
(14, '2023_10_01_184316_create_admins_table', 1),
(15, '2023_10_01_185552_create_shipping_rates_table', 1),
(16, '2023_10_01_185553_create_orders_table', 1),
(17, '2023_10_01_190628_create_vehicles_table', 1),
(18, '2023_10_01_213229_create_order_batches_table', 1),
(19, '2023_10_01_213929_add_batch_id_col_to_orders_table', 1),
(20, '2023_10_05_182532_add_postcode_col_to_locations_table', 1),
(21, '2023_10_06_190015_create_order_packages_table', 1),
(22, '2023_10_08_153417_create_payments_table', 1),
(23, '2023_10_09_092606_add_pickup_cost_to_shipping_rates_table', 1),
(24, '2023_10_10_063141_add_blocked_col_to_users_table', 1),
(25, '2023_10_10_073114_add_customer_id_col_to_payments_table', 1),
(26, '2023_10_10_081718_add_email_sent_col_to_payments_table', 1),
(27, '2023_10_25_071312_create_e_u_funds_transfer_rates_table', 1),
(28, '2023_10_25_071325_create_intl_funds_transfer_rates_table', 1),
(29, '2023_10_26_133444_create_walk_in_customers_table', 1),
(30, '2023_10_26_211532_add_walkincustomercol_to_orders_table', 1),
(31, '2023_10_27_080804_create_e_u_fund_transfer_orders_table', 1),
(32, '2023_10_27_080819_create_intl_fund_transfer_orders_table', 1),
(33, '2023_10_28_075741_add_contact_to_walk_in_customers_table', 1),
(34, '2023_11_04_182410_add_otp_to_users_table', 1),
(35, '2023_11_23_184011_create_kyc_document_type_table', 2),
(36, '2023_11_23_185038_create_kyc_address_proof_types_table', 3),
(40, '2023_12_03_104414_create_transactions_table', 6),
(41, '2023_12_12_075509_create_operation_countries_table', 7),
(44, '2023_12_14_181545_add_phone_to_users_table', 9),
(45, '2023_12_14_190559_add_receiveing_and_sending_to_operation_countries_table', 10),
(46, '2023_11_23_190245_create_kyc_table', 11),
(47, '2023_12_15_091949_add_personal_information_col_to_kyc_table', 12),
(48, '2023_12_15_200626_create_transfer_intent_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `secret` varchar(100) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `redirect` text NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) NOT NULL,
  `access_token_id` varchar(100) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `operation_countries`
--

CREATE TABLE `operation_countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `country_alpha2code` varchar(255) NOT NULL,
  `country_alpha3code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `receiving` varchar(255) NOT NULL DEFAULT 'no',
  `sending` varchar(255) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `operation_countries`
--

INSERT INTO `operation_countries` (`id`, `country_name`, `country_alpha2code`, `country_alpha3code`, `created_at`, `updated_at`, `receiving`, `sending`) VALUES
(1, 'Australia', 'AU', 'AUS', NULL, NULL, 'yes', 'yes'),
(2, 'Austria', 'AT', 'AUT', NULL, NULL, 'yes', 'yes'),
(3, 'Italy', 'IT', 'ITA', NULL, NULL, 'yes', 'yes'),
(4, 'Greece', 'GR', 'GRC', NULL, NULL, 'yes', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `walk_in_customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `courier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dispatcher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `batch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shipping_rate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('unpaid','placed','assigned','in_transit','delivered','cancelled') NOT NULL DEFAULT 'unpaid',
  `pickup_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `delivery_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tracking_id` varchar(255) NOT NULL,
  `pickup_time` timestamp NULL DEFAULT NULL,
  `delivery_time` timestamp NULL DEFAULT NULL,
  `delivery_name` varchar(255) DEFAULT NULL,
  `delivery_email` varchar(255) DEFAULT NULL,
  `delivery_phone` varchar(255) DEFAULT NULL,
  `delivery_phone_alt` varchar(255) DEFAULT NULL,
  `delivery_address1` text DEFAULT NULL,
  `delivery_address2` text DEFAULT NULL,
  `delivery_zip` varchar(255) DEFAULT NULL,
  `delivery_city` varchar(255) DEFAULT NULL,
  `delivery_state` varchar(255) DEFAULT NULL,
  `delivery_country` varchar(255) DEFAULT NULL,
  `pickup_name` varchar(255) DEFAULT NULL,
  `pickup_phone` varchar(255) DEFAULT NULL,
  `pickup_email` varchar(255) DEFAULT NULL,
  `pickup_phone_alt` varchar(255) DEFAULT NULL,
  `pickup_address1` text DEFAULT NULL,
  `pickup_address2` text DEFAULT NULL,
  `pickup_zip` varchar(255) DEFAULT NULL,
  `pickup_city` varchar(255) DEFAULT NULL,
  `pickup_state` varchar(255) DEFAULT NULL,
  `pickup_country` varchar(255) DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `cond_of_goods` varchar(255) DEFAULT NULL,
  `val_of_goods` double(8,2) DEFAULT NULL,
  `val_cur` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_batches`
--

CREATE TABLE `order_batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `dispatcher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('assigned','in_transit','delivered','cancelled') NOT NULL DEFAULT 'assigned',
  `location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_packages`
--

CREATE TABLE `order_packages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `length` double(8,2) DEFAULT NULL,
  `width` double(8,2) DEFAULT NULL,
  `height` double(8,2) DEFAULT NULL,
  `weight` double(8,2) DEFAULT NULL,
  `qty` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ref` varchar(255) DEFAULT NULL,
  `amt_expected` double(8,2) NOT NULL DEFAULT 0.00,
  `amt_paid` double(8,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','done','failed') NOT NULL DEFAULT 'pending',
  `misc` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_rates`
--

CREATE TABLE `shipping_rates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `origin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `destination_id` bigint(20) UNSIGNED DEFAULT NULL,
  `weight_start` double(8,2) DEFAULT NULL,
  `weight_end` double(8,2) DEFAULT NULL,
  `price` double(8,2) DEFAULT NULL,
  `length` double(8,2) DEFAULT NULL,
  `width` double(8,2) DEFAULT NULL,
  `height` double(8,2) DEFAULT NULL,
  `pickup_cost_per_km` double(8,2) DEFAULT 0.00,
  `delivery_cost_per_km` double(8,2) DEFAULT 0.00,
  `desc` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supported_banks`
--

CREATE TABLE `supported_banks` (
  `id` int(10) UNSIGNED NOT NULL,
  `bank_code` varchar(45) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `country_code` varchar(45) NOT NULL,
  `extra_info` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supported_banks`
--

INSERT INTO `supported_banks` (`id`, `bank_code`, `bank_name`, `country_code`, `extra_info`, `created_at`, `updated_at`) VALUES
(1, '123456', 'African Bank 1', 'ZA', NULL, '2023-12-24 20:15:40', '2023-12-24 20:15:45'),
(2, '264905', 'African Bank 2', 'NG', NULL, '2023-12-24 20:15:41', '2023-12-24 20:15:47'),
(3, '174068', 'European Bank 1', 'GR', NULL, '2023-12-24 20:15:43', '2023-12-24 20:15:48'),
(4, '1874057', 'European Bank 2', 'IT', NULL, '2023-12-24 20:15:44', '2023-12-24 20:15:49');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_type` enum('transfer') NOT NULL DEFAULT 'transfer',
  `beneficiary_id` bigint(20) UNSIGNED NOT NULL,
  `beneficiary_account_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` varchar(255) NOT NULL,
  `transaction_amount` double NOT NULL,
  `beneficiary_account_number` varchar(255) NOT NULL,
  `beneficiary_account_name` varchar(255) NOT NULL,
  `beneficiary_bank_name` varchar(255) NOT NULL,
  `beneficiary_bank_code` varchar(255) NOT NULL,
  `payment_provider` varchar(255) NOT NULL,
  `transaction_status` enum('success','pending','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_reference` varchar(255) DEFAULT NULL,
  `payment_intent` varchar(255) DEFAULT NULL,
  `payment_intent_data` text DEFAULT NULL,
  `webhook_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transfer_intent`
--

CREATE TABLE `transfer_intent` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `intent_id` varchar(255) NOT NULL,
  `payment_intent_data` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transfer_intent`
--

INSERT INTO `transfer_intent` (`id`, `user_id`, `intent_id`, `payment_intent_data`, `created_at`, `updated_at`) VALUES
(1, 3, 'pi_3OREXWCPuHcGXdFK17gV2KvC_secret_NDAgpvbLw0PhPWX4J74dGEYSM', 'Stripe\\PaymentIntent JSON: {\n    \"id\": \"pi_3OREXWCPuHcGXdFK17gV2KvC\",\n    \"object\": \"payment_intent\",\n    \"amount\": 20400,\n    \"amount_capturable\": 0,\n    \"amount_details\": {\n        \"tip\": []\n    },\n    \"amount_received\": 0,\n    \"application\": null,\n    \"application_fee_amount\": null,\n    \"automatic_payment_methods\": {\n        \"allow_redirects\": \"always\",\n        \"enabled\": true\n    },\n    \"canceled_at\": null,\n    \"cancellation_reason\": null,\n    \"capture_method\": \"automatic\",\n    \"client_secret\": \"pi_3OREXWCPuHcGXdFK17gV2KvC_secret_NDAgpvbLw0PhPWX4J74dGEYSM\",\n    \"confirmation_method\": \"automatic\",\n    \"created\": 1703512194,\n    \"currency\": \"eur\",\n    \"customer\": null,\n    \"description\": null,\n    \"invoice\": null,\n    \"last_payment_error\": null,\n    \"latest_charge\": null,\n    \"livemode\": false,\n    \"metadata\": {\n        \"beneficiary_account_id\": \"1\",\n        \"beneficiary_account_name\": \"Dev Ben\",\n        \"beneficiary_account_number\": \"12369887085\",\n        \"beneficiary_bank_code\": \"174068\",\n        \"beneficiary_bank_name\": \"European Bank 1\",\n        \"beneficiary_id\": \"3\"\n    },\n    \"next_action\": null,\n    \"on_behalf_of\": null,\n    \"payment_method\": null,\n    \"payment_method_configuration_details\": {\n        \"id\": \"pmc_1LRcNhCPuHcGXdFKlIjueJUS\",\n        \"parent\": null\n    },\n    \"payment_method_options\": {\n        \"card\": {\n            \"installments\": null,\n            \"mandate_options\": null,\n            \"network\": null,\n            \"request_three_d_secure\": \"automatic\"\n        }\n    },\n    \"payment_method_types\": [\n        \"card\"\n    ],\n    \"processing\": null,\n    \"receipt_email\": null,\n    \"review\": null,\n    \"setup_future_usage\": null,\n    \"shipping\": null,\n    \"source\": null,\n    \"statement_descriptor\": null,\n    \"statement_descriptor_suffix\": null,\n    \"status\": \"requires_payment_method\",\n    \"transfer_data\": null,\n    \"transfer_group\": null\n}', '2023-12-25 12:49:56', '2023-12-25 12:49:56'),
(2, 3, 'pi_3ORpkpCPuHcGXdFK0ag2ZUyK_secret_WYG8ReiRuqZXcgAUl0kF6UlbY', 'Stripe\\PaymentIntent JSON: {\n    \"id\": \"pi_3ORpkpCPuHcGXdFK0ag2ZUyK\",\n    \"object\": \"payment_intent\",\n    \"amount\": 1019,\n    \"amount_capturable\": 0,\n    \"amount_details\": {\n        \"tip\": []\n    },\n    \"amount_received\": 0,\n    \"application\": null,\n    \"application_fee_amount\": null,\n    \"automatic_payment_methods\": {\n        \"allow_redirects\": \"always\",\n        \"enabled\": true\n    },\n    \"canceled_at\": null,\n    \"cancellation_reason\": null,\n    \"capture_method\": \"automatic\",\n    \"client_secret\": \"pi_3ORpkpCPuHcGXdFK0ag2ZUyK_secret_WYG8ReiRuqZXcgAUl0kF6UlbY\",\n    \"confirmation_method\": \"automatic\",\n    \"created\": 1703655247,\n    \"currency\": \"eur\",\n    \"customer\": null,\n    \"description\": null,\n    \"invoice\": null,\n    \"last_payment_error\": null,\n    \"latest_charge\": null,\n    \"livemode\": false,\n    \"metadata\": {\n        \"beneficiary_account_id\": \"2\",\n        \"beneficiary_account_name\": \"Dev Ben\",\n        \"beneficiary_account_number\": \"12369887085\",\n        \"beneficiary_bank_code\": \"174068\",\n        \"beneficiary_bank_name\": \"European Bank 1\",\n        \"beneficiary_id\": \"3\"\n    },\n    \"next_action\": null,\n    \"on_behalf_of\": null,\n    \"payment_method\": null,\n    \"payment_method_configuration_details\": {\n        \"id\": \"pmc_1LRcNhCPuHcGXdFKlIjueJUS\",\n        \"parent\": null\n    },\n    \"payment_method_options\": {\n        \"card\": {\n            \"installments\": null,\n            \"mandate_options\": null,\n            \"network\": null,\n            \"request_three_d_secure\": \"automatic\"\n        }\n    },\n    \"payment_method_types\": [\n        \"card\"\n    ],\n    \"processing\": null,\n    \"receipt_email\": null,\n    \"review\": null,\n    \"setup_future_usage\": null,\n    \"shipping\": null,\n    \"source\": null,\n    \"statement_descriptor\": null,\n    \"statement_descriptor_suffix\": null,\n    \"status\": \"requires_payment_method\",\n    \"transfer_data\": null,\n    \"transfer_group\": null\n}', '2023-12-27 10:34:08', '2023-12-27 10:34:08'),
(3, 3, 'pi_3ORqJbCPuHcGXdFK0evqLXqJ_secret_1k8bdH8muEVCm1kXtZZiY9JFC', 'Stripe\\PaymentIntent JSON: {\n    \"id\": \"pi_3ORqJbCPuHcGXdFK0evqLXqJ\",\n    \"object\": \"payment_intent\",\n    \"amount\": 5100,\n    \"amount_capturable\": 0,\n    \"amount_details\": {\n        \"tip\": []\n    },\n    \"amount_received\": 0,\n    \"application\": null,\n    \"application_fee_amount\": null,\n    \"automatic_payment_methods\": {\n        \"allow_redirects\": \"always\",\n        \"enabled\": true\n    },\n    \"canceled_at\": null,\n    \"cancellation_reason\": null,\n    \"capture_method\": \"automatic\",\n    \"client_secret\": \"pi_3ORqJbCPuHcGXdFK0evqLXqJ_secret_1k8bdH8muEVCm1kXtZZiY9JFC\",\n    \"confirmation_method\": \"automatic\",\n    \"created\": 1703657403,\n    \"currency\": \"eur\",\n    \"customer\": null,\n    \"description\": null,\n    \"invoice\": null,\n    \"last_payment_error\": null,\n    \"latest_charge\": null,\n    \"livemode\": false,\n    \"metadata\": {\n        \"beneficiary_account_id\": \"2\",\n        \"beneficiary_account_name\": \"Dev Ben\",\n        \"beneficiary_account_number\": \"12369887085\",\n        \"beneficiary_bank_code\": \"174068\",\n        \"beneficiary_bank_name\": \"European Bank 1\",\n        \"beneficiary_id\": \"3\"\n    },\n    \"next_action\": null,\n    \"on_behalf_of\": null,\n    \"payment_method\": null,\n    \"payment_method_configuration_details\": {\n        \"id\": \"pmc_1LRcNhCPuHcGXdFKlIjueJUS\",\n        \"parent\": null\n    },\n    \"payment_method_options\": {\n        \"card\": {\n            \"installments\": null,\n            \"mandate_options\": null,\n            \"network\": null,\n            \"request_three_d_secure\": \"automatic\"\n        }\n    },\n    \"payment_method_types\": [\n        \"card\"\n    ],\n    \"processing\": null,\n    \"receipt_email\": null,\n    \"review\": null,\n    \"setup_future_usage\": null,\n    \"shipping\": null,\n    \"source\": null,\n    \"statement_descriptor\": null,\n    \"statement_descriptor_suffix\": null,\n    \"status\": \"requires_payment_method\",\n    \"transfer_data\": null,\n    \"transfer_group\": null\n}', '2023-12-27 11:10:03', '2023-12-27 11:10:03'),
(4, 3, 'pi_3ORqL1CPuHcGXdFK1GK0yWHQ_secret_Qp2YFjQpEILPJMjzYKfpcOM39', 'Stripe\\PaymentIntent JSON: {\n    \"id\": \"pi_3ORqL1CPuHcGXdFK1GK0yWHQ\",\n    \"object\": \"payment_intent\",\n    \"amount\": 5100,\n    \"amount_capturable\": 0,\n    \"amount_details\": {\n        \"tip\": []\n    },\n    \"amount_received\": 0,\n    \"application\": null,\n    \"application_fee_amount\": null,\n    \"automatic_payment_methods\": {\n        \"allow_redirects\": \"always\",\n        \"enabled\": true\n    },\n    \"canceled_at\": null,\n    \"cancellation_reason\": null,\n    \"capture_method\": \"automatic\",\n    \"client_secret\": \"pi_3ORqL1CPuHcGXdFK1GK0yWHQ_secret_Qp2YFjQpEILPJMjzYKfpcOM39\",\n    \"confirmation_method\": \"automatic\",\n    \"created\": 1703657491,\n    \"currency\": \"eur\",\n    \"customer\": null,\n    \"description\": null,\n    \"invoice\": null,\n    \"last_payment_error\": null,\n    \"latest_charge\": null,\n    \"livemode\": false,\n    \"metadata\": {\n        \"beneficiary_account_id\": \"2\",\n        \"beneficiary_account_name\": \"Dev Ben\",\n        \"beneficiary_account_number\": \"12369887085\",\n        \"beneficiary_bank_code\": \"174068\",\n        \"beneficiary_bank_name\": \"European Bank 1\",\n        \"beneficiary_id\": \"3\"\n    },\n    \"next_action\": null,\n    \"on_behalf_of\": null,\n    \"payment_method\": null,\n    \"payment_method_configuration_details\": {\n        \"id\": \"pmc_1LRcNhCPuHcGXdFKlIjueJUS\",\n        \"parent\": null\n    },\n    \"payment_method_options\": {\n        \"card\": {\n            \"installments\": null,\n            \"mandate_options\": null,\n            \"network\": null,\n            \"request_three_d_secure\": \"automatic\"\n        }\n    },\n    \"payment_method_types\": [\n        \"card\"\n    ],\n    \"processing\": null,\n    \"receipt_email\": null,\n    \"review\": null,\n    \"setup_future_usage\": null,\n    \"shipping\": null,\n    \"source\": null,\n    \"statement_descriptor\": null,\n    \"statement_descriptor_suffix\": null,\n    \"status\": \"requires_payment_method\",\n    \"transfer_data\": null,\n    \"transfer_group\": null\n}', '2023-12-27 11:11:31', '2023-12-27 11:11:31'),
(5, 3, 'pi_3ORqPXCPuHcGXdFK1h5HqkNl_secret_Pzpt20JbGhtkkhqPu3RYp4E3T', 'Stripe\\PaymentIntent JSON: {\n    \"id\": \"pi_3ORqPXCPuHcGXdFK1h5HqkNl\",\n    \"object\": \"payment_intent\",\n    \"amount\": 5100,\n    \"amount_capturable\": 0,\n    \"amount_details\": {\n        \"tip\": []\n    },\n    \"amount_received\": 0,\n    \"application\": null,\n    \"application_fee_amount\": null,\n    \"automatic_payment_methods\": {\n        \"allow_redirects\": \"always\",\n        \"enabled\": true\n    },\n    \"canceled_at\": null,\n    \"cancellation_reason\": null,\n    \"capture_method\": \"automatic\",\n    \"client_secret\": \"pi_3ORqPXCPuHcGXdFK1h5HqkNl_secret_Pzpt20JbGhtkkhqPu3RYp4E3T\",\n    \"confirmation_method\": \"automatic\",\n    \"created\": 1703657771,\n    \"currency\": \"eur\",\n    \"customer\": null,\n    \"description\": null,\n    \"invoice\": null,\n    \"last_payment_error\": null,\n    \"latest_charge\": null,\n    \"livemode\": false,\n    \"metadata\": {\n        \"beneficiary_account_id\": \"2\",\n        \"beneficiary_account_name\": \"Dev Ben\",\n        \"beneficiary_account_number\": \"12369887085\",\n        \"beneficiary_bank_code\": \"174068\",\n        \"beneficiary_bank_name\": \"European Bank 1\",\n        \"beneficiary_id\": \"3\"\n    },\n    \"next_action\": null,\n    \"on_behalf_of\": null,\n    \"payment_method\": null,\n    \"payment_method_configuration_details\": {\n        \"id\": \"pmc_1LRcNhCPuHcGXdFKlIjueJUS\",\n        \"parent\": null\n    },\n    \"payment_method_options\": {\n        \"card\": {\n            \"installments\": null,\n            \"mandate_options\": null,\n            \"network\": null,\n            \"request_three_d_secure\": \"automatic\"\n        }\n    },\n    \"payment_method_types\": [\n        \"card\"\n    ],\n    \"processing\": null,\n    \"receipt_email\": null,\n    \"review\": null,\n    \"setup_future_usage\": null,\n    \"shipping\": null,\n    \"source\": null,\n    \"statement_descriptor\": null,\n    \"statement_descriptor_suffix\": null,\n    \"status\": \"requires_payment_method\",\n    \"transfer_data\": null,\n    \"transfer_group\": null\n}', '2023-12-27 11:16:11', '2023-12-27 11:16:11'),
(6, 3, 'pi_3ORqXCCPuHcGXdFK1qFW4YOl_secret_jo9ARrnOiypm4jqDPHgWolyHk', 'Stripe\\PaymentIntent JSON: {\n    \"id\": \"pi_3ORqXCCPuHcGXdFK1qFW4YOl\",\n    \"object\": \"payment_intent\",\n    \"amount\": 5100,\n    \"amount_capturable\": 0,\n    \"amount_details\": {\n        \"tip\": []\n    },\n    \"amount_received\": 0,\n    \"application\": null,\n    \"application_fee_amount\": null,\n    \"automatic_payment_methods\": {\n        \"allow_redirects\": \"always\",\n        \"enabled\": true\n    },\n    \"canceled_at\": null,\n    \"cancellation_reason\": null,\n    \"capture_method\": \"automatic\",\n    \"client_secret\": \"pi_3ORqXCCPuHcGXdFK1qFW4YOl_secret_jo9ARrnOiypm4jqDPHgWolyHk\",\n    \"confirmation_method\": \"automatic\",\n    \"created\": 1703658246,\n    \"currency\": \"eur\",\n    \"customer\": null,\n    \"description\": null,\n    \"invoice\": null,\n    \"last_payment_error\": null,\n    \"latest_charge\": null,\n    \"livemode\": false,\n    \"metadata\": {\n        \"beneficiary_account_id\": \"2\",\n        \"beneficiary_account_name\": \"Dev Ben\",\n        \"beneficiary_account_number\": \"12369887085\",\n        \"beneficiary_bank_code\": \"174068\",\n        \"beneficiary_bank_name\": \"European Bank 1\",\n        \"beneficiary_id\": \"3\"\n    },\n    \"next_action\": null,\n    \"on_behalf_of\": null,\n    \"payment_method\": null,\n    \"payment_method_configuration_details\": {\n        \"id\": \"pmc_1LRcNhCPuHcGXdFKlIjueJUS\",\n        \"parent\": null\n    },\n    \"payment_method_options\": {\n        \"card\": {\n            \"installments\": null,\n            \"mandate_options\": null,\n            \"network\": null,\n            \"request_three_d_secure\": \"automatic\"\n        }\n    },\n    \"payment_method_types\": [\n        \"card\"\n    ],\n    \"processing\": null,\n    \"receipt_email\": null,\n    \"review\": null,\n    \"setup_future_usage\": null,\n    \"shipping\": null,\n    \"source\": null,\n    \"statement_descriptor\": null,\n    \"statement_descriptor_suffix\": null,\n    \"status\": \"requires_payment_method\",\n    \"transfer_data\": null,\n    \"transfer_group\": null\n}', '2023-12-27 11:24:06', '2023-12-27 11:24:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `user_type` enum('user','courier','dispatcher','admin','mobile') NOT NULL DEFAULT 'user',
  `blocked` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `country` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `country_code` varchar(45) NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `status`, `user_type`, `blocked`, `remember_token`, `created_at`, `updated_at`, `otp`, `country`, `phone`, `country_code`, `photo`) VALUES
(1, 'Kefas Kingsley', 'kvng656@gmail.com', NULL, '$2y$10$vof22O0ZRXn9fDWby9QP1.9UzEqh3ZC3ff5Fzywedf30FiHYDz7GK', 1, 'mobile', 0, NULL, NULL, '2023-11-23 18:57:29', NULL, 0, NULL, '', NULL),
(3, 'Samson Johnson', 'johnsonsamson23@gmail.com', NULL, '$2y$10$Xwl2Hx6WyUelDCnE0rjADOaKfuB6.9M660AidRWvnfjxGO2bhMGpC', 1, 'user', 0, NULL, '2023-12-23 15:50:45', '2023-12-23 15:50:45', NULL, NULL, '+412345767889464', 'IT', NULL),
(4, 'Acc ount', 's@a.con', NULL, '$2y$10$q6BgcappPdoPU.SZQspKQu9I9pLQbJIJRIcUh5GTXVn9xgr8.veqG', 1, 'user', 0, NULL, '2023-12-26 20:09:45', '2023-12-26 20:09:45', NULL, NULL, '+411234546784946', 'IT', NULL),
(5, 'djdj hshs', '1@a.com', NULL, '$2y$10$TlbpBPE/Z.QK9Rvyq61Igu0yUgszXXC1FJvIUEc/f10sFR.Vgqtya', 1, 'user', 0, NULL, '2023-12-26 22:29:35', '2023-12-26 22:29:35', NULL, NULL, '+411223649494949', 'IT', NULL),
(6, 'Walshak Apollos', 'admin@bigbosscrypto.site', NULL, '$2y$10$jV17rHVW4bdYfkWLwYBUWeVWXojjf/mXolhq/FW.5BiX1MhGEXg0q', 1, 'user', 0, NULL, '2023-12-26 22:47:18', '2023-12-26 22:47:18', NULL, NULL, '+413589497979999', 'IT', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `courier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive','decommisioned') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `walk_in_customers`
--

CREATE TABLE `walk_in_customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `surname` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birthDate` date DEFAULT NULL,
  `birthPlace` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `belfioreCode` varchar(255) DEFAULT NULL,
  `doc_type` varchar(255) DEFAULT NULL,
  `doc_num` varchar(255) DEFAULT NULL,
  `doc_front` varchar(255) DEFAULT NULL,
  `doc_back` varchar(255) DEFAULT NULL,
  `tax_code` varchar(255) DEFAULT NULL,
  `kyc_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admins_user_id_foreign` (`user_id`);

--
-- Indexes for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beneficiaries_user_id_foreign` (`user_id`);

--
-- Indexes for table `beneficiary_accounts`
--
ALTER TABLE `beneficiary_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `beneficiary_accounts_user_id_foreign` (`user_id`),
  ADD KEY `beneficiary_accounts_beneficiary_id_foreign` (`beneficiary_id`);

--
-- Indexes for table `couriers`
--
ALTER TABLE `couriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `couriers_user_id_foreign` (`user_id`),
  ADD KEY `couriers_current_location_foreign` (`current_location`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_user_id_foreign` (`user_id`);

--
-- Indexes for table `dispatchers`
--
ALTER TABLE `dispatchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dispatchers_user_id_foreign` (`user_id`),
  ADD KEY `dispatchers_location_id_foreign` (`location_id`);

--
-- Indexes for table `e_u_funds_transfer_rates`
--
ALTER TABLE `e_u_funds_transfer_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `e_u_fund_transfer_orders`
--
ALTER TABLE `e_u_fund_transfer_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `e_u_fund_transfer_orders_walk_in_customer_id_foreign` (`walk_in_customer_id`),
  ADD KEY `e_u_fund_transfer_orders_customer_id_foreign` (`customer_id`),
  ADD KEY `e_u_fund_transfer_orders_dispatcher_id_foreign` (`dispatcher_id`),
  ADD KEY `e_u_fund_transfer_orders_e_u_funds_transfer_rate_id_foreign` (`e_u_funds_transfer_rate_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `intl_funds_transfer_rates`
--
ALTER TABLE `intl_funds_transfer_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `intl_fund_transfer_orders`
--
ALTER TABLE `intl_fund_transfer_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intl_fund_transfer_orders_walk_in_customer_id_foreign` (`walk_in_customer_id`),
  ADD KEY `intl_fund_transfer_orders_customer_id_foreign` (`customer_id`),
  ADD KEY `intl_fund_transfer_orders_dispatcher_id_foreign` (`dispatcher_id`),
  ADD KEY `intl_fund_transfer_orders_e_u_funds_transfer_rate_id_foreign` (`e_u_funds_transfer_rate_id`);

--
-- Indexes for table `kyc`
--
ALTER TABLE `kyc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kyc_user_id_foreign` (`user_id`),
  ADD KEY `kyc_document_type_id_foreign` (`document_type_id`),
  ADD KEY `kyc_proof_of_address_type_id_foreign` (`proof_of_address_type_id`);

--
-- Indexes for table `kyc_address_proof_types`
--
ALTER TABLE `kyc_address_proof_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kyc_document_type`
--
ALTER TABLE `kyc_document_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `operation_countries`
--
ALTER TABLE `operation_countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_tracking_id_unique` (`tracking_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`),
  ADD KEY `orders_delivery_customer_id_foreign` (`delivery_customer_id`),
  ADD KEY `orders_courier_id_foreign` (`courier_id`),
  ADD KEY `orders_dispatcher_id_foreign` (`dispatcher_id`),
  ADD KEY `orders_shipping_rate_id_foreign` (`shipping_rate_id`),
  ADD KEY `orders_pickup_location_id_foreign` (`pickup_location_id`),
  ADD KEY `orders_delivery_location_id_foreign` (`delivery_location_id`),
  ADD KEY `orders_current_location_id_foreign` (`current_location_id`),
  ADD KEY `orders_batch_id_foreign` (`batch_id`),
  ADD KEY `orders_walk_in_customer_id_foreign` (`walk_in_customer_id`);

--
-- Indexes for table `order_batches`
--
ALTER TABLE `order_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_batches_dispatcher_id_foreign` (`dispatcher_id`),
  ADD KEY `order_batches_location_id_foreign` (`location_id`);

--
-- Indexes for table `order_packages`
--
ALTER TABLE `order_packages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_packages_order_id_foreign` (`order_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_order_id_foreign` (`order_id`),
  ADD KEY `payments_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `shipping_rates`
--
ALTER TABLE `shipping_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipping_rates_origin_id_foreign` (`origin_id`),
  ADD KEY `shipping_rates_destination_id_foreign` (`destination_id`);

--
-- Indexes for table `supported_banks`
--
ALTER TABLE `supported_banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_foreign` (`user_id`),
  ADD KEY `transactions_beneficiary_id_foreign` (`beneficiary_id`),
  ADD KEY `transactions_beneficiary_account_id_foreign` (`beneficiary_account_id`);

--
-- Indexes for table `transfer_intent`
--
ALTER TABLE `transfer_intent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfer_intent_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicles_courier_id_foreign` (`courier_id`);

--
-- Indexes for table `walk_in_customers`
--
ALTER TABLE `walk_in_customers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `beneficiary_accounts`
--
ALTER TABLE `beneficiary_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `couriers`
--
ALTER TABLE `couriers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dispatchers`
--
ALTER TABLE `dispatchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `e_u_funds_transfer_rates`
--
ALTER TABLE `e_u_funds_transfer_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `e_u_fund_transfer_orders`
--
ALTER TABLE `e_u_fund_transfer_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `intl_funds_transfer_rates`
--
ALTER TABLE `intl_funds_transfer_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `intl_fund_transfer_orders`
--
ALTER TABLE `intl_fund_transfer_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kyc`
--
ALTER TABLE `kyc`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `kyc_address_proof_types`
--
ALTER TABLE `kyc_address_proof_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `kyc_document_type`
--
ALTER TABLE `kyc_document_type`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operation_countries`
--
ALTER TABLE `operation_countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_batches`
--
ALTER TABLE `order_batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_packages`
--
ALTER TABLE `order_packages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipping_rates`
--
ALTER TABLE `shipping_rates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supported_banks`
--
ALTER TABLE `supported_banks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transfer_intent`
--
ALTER TABLE `transfer_intent`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `walk_in_customers`
--
ALTER TABLE `walk_in_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `beneficiaries`
--
ALTER TABLE `beneficiaries`
  ADD CONSTRAINT `beneficiaries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `beneficiary_accounts`
--
ALTER TABLE `beneficiary_accounts`
  ADD CONSTRAINT `beneficiary_accounts_beneficiary_id_foreign` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`),
  ADD CONSTRAINT `beneficiary_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `couriers`
--
ALTER TABLE `couriers`
  ADD CONSTRAINT `couriers_current_location_foreign` FOREIGN KEY (`current_location`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `couriers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `dispatchers`
--
ALTER TABLE `dispatchers`
  ADD CONSTRAINT `dispatchers_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `dispatchers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `e_u_fund_transfer_orders`
--
ALTER TABLE `e_u_fund_transfer_orders`
  ADD CONSTRAINT `e_u_fund_transfer_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `e_u_fund_transfer_orders_dispatcher_id_foreign` FOREIGN KEY (`dispatcher_id`) REFERENCES `dispatchers` (`id`),
  ADD CONSTRAINT `e_u_fund_transfer_orders_e_u_funds_transfer_rate_id_foreign` FOREIGN KEY (`e_u_funds_transfer_rate_id`) REFERENCES `e_u_funds_transfer_rates` (`id`),
  ADD CONSTRAINT `e_u_fund_transfer_orders_walk_in_customer_id_foreign` FOREIGN KEY (`walk_in_customer_id`) REFERENCES `walk_in_customers` (`id`);

--
-- Constraints for table `intl_fund_transfer_orders`
--
ALTER TABLE `intl_fund_transfer_orders`
  ADD CONSTRAINT `intl_fund_transfer_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `intl_fund_transfer_orders_dispatcher_id_foreign` FOREIGN KEY (`dispatcher_id`) REFERENCES `dispatchers` (`id`),
  ADD CONSTRAINT `intl_fund_transfer_orders_e_u_funds_transfer_rate_id_foreign` FOREIGN KEY (`e_u_funds_transfer_rate_id`) REFERENCES `e_u_funds_transfer_rates` (`id`),
  ADD CONSTRAINT `intl_fund_transfer_orders_walk_in_customer_id_foreign` FOREIGN KEY (`walk_in_customer_id`) REFERENCES `walk_in_customers` (`id`);

--
-- Constraints for table `kyc`
--
ALTER TABLE `kyc`
  ADD CONSTRAINT `kyc_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `kyc_document_type` (`id`),
  ADD CONSTRAINT `kyc_proof_of_address_type_id_foreign` FOREIGN KEY (`proof_of_address_type_id`) REFERENCES `kyc_address_proof_types` (`id`),
  ADD CONSTRAINT `kyc_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `order_batches` (`id`),
  ADD CONSTRAINT `orders_courier_id_foreign` FOREIGN KEY (`courier_id`) REFERENCES `couriers` (`id`),
  ADD CONSTRAINT `orders_current_location_id_foreign` FOREIGN KEY (`current_location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_delivery_customer_id_foreign` FOREIGN KEY (`delivery_customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `orders_delivery_location_id_foreign` FOREIGN KEY (`delivery_location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `orders_dispatcher_id_foreign` FOREIGN KEY (`dispatcher_id`) REFERENCES `dispatchers` (`id`),
  ADD CONSTRAINT `orders_pickup_location_id_foreign` FOREIGN KEY (`pickup_location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `orders_shipping_rate_id_foreign` FOREIGN KEY (`shipping_rate_id`) REFERENCES `shipping_rates` (`id`),
  ADD CONSTRAINT `orders_walk_in_customer_id_foreign` FOREIGN KEY (`walk_in_customer_id`) REFERENCES `walk_in_customers` (`id`);

--
-- Constraints for table `order_batches`
--
ALTER TABLE `order_batches`
  ADD CONSTRAINT `order_batches_dispatcher_id_foreign` FOREIGN KEY (`dispatcher_id`) REFERENCES `dispatchers` (`id`),
  ADD CONSTRAINT `order_batches_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `order_packages`
--
ALTER TABLE `order_packages`
  ADD CONSTRAINT `order_packages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `shipping_rates`
--
ALTER TABLE `shipping_rates`
  ADD CONSTRAINT `shipping_rates_destination_id_foreign` FOREIGN KEY (`destination_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `shipping_rates_origin_id_foreign` FOREIGN KEY (`origin_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_beneficiary_account_id_foreign` FOREIGN KEY (`beneficiary_account_id`) REFERENCES `beneficiary_accounts` (`id`),
  ADD CONSTRAINT `transactions_beneficiary_id_foreign` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`),
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `transfer_intent`
--
ALTER TABLE `transfer_intent`
  ADD CONSTRAINT `transfer_intent_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_courier_id_foreign` FOREIGN KEY (`courier_id`) REFERENCES `couriers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
