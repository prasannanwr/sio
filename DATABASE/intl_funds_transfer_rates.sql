-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 25, 2023 at 09:21 AM
-- Server version: 8.0.35-0ubuntu0.22.04.1
-- PHP Version: 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siopay`
--

-- --------------------------------------------------------

--
-- Table structure for table `intl_funds_transfer_rates`
--

CREATE TABLE `intl_funds_transfer_rates` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rx_country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rx_currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ex_rate` double NOT NULL DEFAULT '1',
  `calc` enum('perc','fixed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `commision` double NOT NULL DEFAULT '1',
  `min_amt` double NOT NULL DEFAULT '0',
  `max_amt` double NOT NULL DEFAULT '999',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `intl_funds_transfer_rates`
--

INSERT INTO `intl_funds_transfer_rates` (`id`, `name`, `s_country`, `rx_country`, `s_currency`, `rx_currency`, `ex_rate`, `calc`, `commision`, `min_amt`, `max_amt`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Premium-momo mobile money', 'Italy', 'Nigeria', 'EUR - Euro', 'NGN - Nigerian Naira', 1200, 'perc', 2, 1, 999, 1, '2023-10-25 09:39:56', '2023-10-25 09:39:56'),
(2, 'Bronze pickup', 'Italy', 'Nigeria', 'EUR - Euro', 'NGN - Nigerian Naira', 1400, 'perc', 5, 10, 970, 1, '2023-10-28 14:46:52', '2023-10-28 14:46:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `intl_funds_transfer_rates`
--
ALTER TABLE `intl_funds_transfer_rates`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `intl_funds_transfer_rates`
--
ALTER TABLE `intl_funds_transfer_rates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
