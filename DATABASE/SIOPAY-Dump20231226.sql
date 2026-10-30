-- MySQL dump 10.13  Distrib 8.0.32, for Win64 (x86_64)
--
-- Host: localhost    Database: siopay
-- ------------------------------------------------------
-- Server version	8.0.32

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `admins_user_id_foreign` (`user_id`),
  CONSTRAINT `admins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiaries`
--

DROP TABLE IF EXISTS `beneficiaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficiaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint DEFAULT '0',
  `extra_datas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `beneficiaries_user_id_foreign` (`user_id`),
  CONSTRAINT `beneficiaries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiaries`
--

LOCK TABLES `beneficiaries` WRITE;
/*!40000 ALTER TABLE `beneficiaries` DISABLE KEYS */;
INSERT INTO `beneficiaries` VALUES (3,3,'Dev Ben','+301234567864','GR','Father-in-law','Male',0,NULL,'2023-12-24 17:15:03','2023-12-25 05:12:47');
/*!40000 ALTER TABLE `beneficiaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `beneficiary_accounts`
--

DROP TABLE IF EXISTS `beneficiary_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficiary_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `beneficiary_id` bigint unsigned NOT NULL,
  `beneficiary_account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_bank_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra_datas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `beneficiary_accounts_user_id_foreign` (`user_id`),
  KEY `beneficiary_accounts_beneficiary_id_foreign` (`beneficiary_id`),
  CONSTRAINT `beneficiary_accounts_beneficiary_id_foreign` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`),
  CONSTRAINT `beneficiary_accounts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `beneficiary_accounts`
--

LOCK TABLES `beneficiary_accounts` WRITE;
/*!40000 ALTER TABLE `beneficiary_accounts` DISABLE KEYS */;
INSERT INTO `beneficiary_accounts` VALUES (1,3,3,'12369887085','Dev Ben','European Bank 1','174068',NULL,'2023-12-24 18:51:28','2023-12-24 18:51:28'),(2,3,3,'12369887085','Dev Ben','European Bank 1','174068',NULL,'2023-12-24 18:51:55','2023-12-24 18:51:55');
/*!40000 ALTER TABLE `beneficiary_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `couriers`
--

DROP TABLE IF EXISTS `couriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `couriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_alt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_location` bigint unsigned DEFAULT NULL,
  `vehicle_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','decommisioned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `couriers_user_id_foreign` (`user_id`),
  KEY `couriers_current_location_foreign` (`current_location`),
  CONSTRAINT `couriers_current_location_foreign` FOREIGN KEY (`current_location`) REFERENCES `locations` (`id`),
  CONSTRAINT `couriers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `couriers`
--

LOCK TABLES `couriers` WRITE;
/*!40000 ALTER TABLE `couriers` DISABLE KEYS */;
/*!40000 ALTER TABLE `couriers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_alt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `address2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customers_user_id_foreign` (`user_id`),
  CONSTRAINT `customers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dispatchers`
--

DROP TABLE IF EXISTS `dispatchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dispatchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_alt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `address2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dispatchers_user_id_foreign` (`user_id`),
  KEY `dispatchers_location_id_foreign` (`location_id`),
  CONSTRAINT `dispatchers_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `dispatchers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dispatchers`
--

LOCK TABLES `dispatchers` WRITE;
/*!40000 ALTER TABLE `dispatchers` DISABLE KEYS */;
/*!40000 ALTER TABLE `dispatchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `e_u_fund_transfer_orders`
--

DROP TABLE IF EXISTS `e_u_fund_transfer_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `e_u_fund_transfer_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `walk_in_customer_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `dispatcher_id` bigint unsigned DEFAULT NULL,
  `e_u_funds_transfer_rate_id` bigint unsigned DEFAULT NULL,
  `tracking_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_routing_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_swift_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_amount` double DEFAULT NULL,
  `rx_amount` double DEFAULT NULL,
  `tx_status` enum('unpaid','pending','done','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `tx_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `e_u_fund_transfer_orders_walk_in_customer_id_foreign` (`walk_in_customer_id`),
  KEY `e_u_fund_transfer_orders_customer_id_foreign` (`customer_id`),
  KEY `e_u_fund_transfer_orders_dispatcher_id_foreign` (`dispatcher_id`),
  KEY `e_u_fund_transfer_orders_e_u_funds_transfer_rate_id_foreign` (`e_u_funds_transfer_rate_id`),
  CONSTRAINT `e_u_fund_transfer_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `e_u_fund_transfer_orders_dispatcher_id_foreign` FOREIGN KEY (`dispatcher_id`) REFERENCES `dispatchers` (`id`),
  CONSTRAINT `e_u_fund_transfer_orders_e_u_funds_transfer_rate_id_foreign` FOREIGN KEY (`e_u_funds_transfer_rate_id`) REFERENCES `e_u_funds_transfer_rates` (`id`),
  CONSTRAINT `e_u_fund_transfer_orders_walk_in_customer_id_foreign` FOREIGN KEY (`walk_in_customer_id`) REFERENCES `walk_in_customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `e_u_fund_transfer_orders`
--

LOCK TABLES `e_u_fund_transfer_orders` WRITE;
/*!40000 ALTER TABLE `e_u_fund_transfer_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `e_u_fund_transfer_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `e_u_funds_transfer_rates`
--

DROP TABLE IF EXISTS `e_u_funds_transfer_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `e_u_funds_transfer_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_country_eu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rx_country_eu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `calc` enum('perc','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commision` double NOT NULL DEFAULT '1',
  `ex_rate` double NOT NULL DEFAULT '1',
  `min_amt` double NOT NULL DEFAULT '0',
  `max_amt` double NOT NULL DEFAULT '999',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `e_u_funds_transfer_rates`
--

LOCK TABLES `e_u_funds_transfer_rates` WRITE;
/*!40000 ALTER TABLE `e_u_funds_transfer_rates` DISABLE KEYS */;
INSERT INTO `e_u_funds_transfer_rates` VALUES (1,'Premium-momo mobile money','Italy','Nigeria','perc',2,1200,1,999,1,'2023-10-25 08:39:56','2023-10-25 08:39:56'),(2,'Bronze pickup','Italy','Nigeria','perc',5,1400,10,970,1,'2023-10-28 13:46:52','2023-10-28 13:46:52'),(3,'Bronze pickup','Italy','Greece','perc',2,1,10,970,1,'2023-10-28 13:46:52','2023-10-28 13:46:52');
/*!40000 ALTER TABLE `e_u_funds_transfer_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intl_fund_transfer_orders`
--

DROP TABLE IF EXISTS `intl_fund_transfer_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intl_fund_transfer_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `walk_in_customer_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `dispatcher_id` bigint unsigned DEFAULT NULL,
  `e_u_funds_transfer_rate_id` bigint unsigned DEFAULT NULL,
  `tracking_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_routing_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_swift_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bank_account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_amount` double DEFAULT NULL,
  `rx_amount` double DEFAULT NULL,
  `tx_status` enum('unpaid','pending','done','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `tx_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `intl_fund_transfer_orders_walk_in_customer_id_foreign` (`walk_in_customer_id`),
  KEY `intl_fund_transfer_orders_customer_id_foreign` (`customer_id`),
  KEY `intl_fund_transfer_orders_dispatcher_id_foreign` (`dispatcher_id`),
  KEY `intl_fund_transfer_orders_e_u_funds_transfer_rate_id_foreign` (`e_u_funds_transfer_rate_id`),
  CONSTRAINT `intl_fund_transfer_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `intl_fund_transfer_orders_dispatcher_id_foreign` FOREIGN KEY (`dispatcher_id`) REFERENCES `dispatchers` (`id`),
  CONSTRAINT `intl_fund_transfer_orders_e_u_funds_transfer_rate_id_foreign` FOREIGN KEY (`e_u_funds_transfer_rate_id`) REFERENCES `e_u_funds_transfer_rates` (`id`),
  CONSTRAINT `intl_fund_transfer_orders_walk_in_customer_id_foreign` FOREIGN KEY (`walk_in_customer_id`) REFERENCES `walk_in_customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intl_fund_transfer_orders`
--

LOCK TABLES `intl_fund_transfer_orders` WRITE;
/*!40000 ALTER TABLE `intl_fund_transfer_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `intl_fund_transfer_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `intl_funds_transfer_rates`
--

DROP TABLE IF EXISTS `intl_funds_transfer_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `intl_funds_transfer_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rx_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rx_currency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ex_rate` double NOT NULL DEFAULT '1',
  `calc` enum('perc','fixed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `commision` double NOT NULL DEFAULT '1',
  `min_amt` double NOT NULL DEFAULT '0',
  `max_amt` double NOT NULL DEFAULT '999',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `intl_funds_transfer_rates`
--

LOCK TABLES `intl_funds_transfer_rates` WRITE;
/*!40000 ALTER TABLE `intl_funds_transfer_rates` DISABLE KEYS */;
INSERT INTO `intl_funds_transfer_rates` VALUES (1,'Premium-momo mobile money','Italy','Nigeria','EUR - Euro','NGN - Nigerian Naira',1200,'perc',2,1,999,1,'2023-10-25 08:39:56','2023-10-25 08:39:56'),(2,'Bronze pickup','Italy','Nigeria','EUR - Euro','NGN - Nigerian Naira',1400,'perc',5,10,970,1,'2023-10-28 13:46:52','2023-10-28 13:46:52'),(3,'Bronze pickup','Italy','Greece','EUR - Euro','EUR - Euro',1,'perc',2,10,970,1,'2023-10-28 13:46:52','2023-10-28 13:46:52');
/*!40000 ALTER TABLE `intl_funds_transfer_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kyc`
--

DROP TABLE IF EXISTS `kyc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kyc` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `document_type_id` bigint unsigned DEFAULT NULL,
  `document_front` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_back` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selfie` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `proof_of_address_type_id` bigint unsigned DEFAULT NULL,
  `proof_of_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kyc_level` int NOT NULL DEFAULT '0',
  `status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `personal_information` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `kyc_user_id_foreign` (`user_id`),
  KEY `kyc_document_type_id_foreign` (`document_type_id`),
  KEY `kyc_proof_of_address_type_id_foreign` (`proof_of_address_type_id`),
  CONSTRAINT `kyc_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `kyc_document_type` (`id`),
  CONSTRAINT `kyc_proof_of_address_type_id_foreign` FOREIGN KEY (`proof_of_address_type_id`) REFERENCES `kyc_address_proof_types` (`id`),
  CONSTRAINT `kyc_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kyc`
--

LOCK TABLES `kyc` WRITE;
/*!40000 ALTER TABLE `kyc` DISABLE KEYS */;
INSERT INTO `kyc` VALUES (2,1,NULL,NULL,NULL,NULL,NULL,NULL,1,'pending',NULL,'2023-12-15 09:37:06','2023-12-15 09:37:06','{\"firstname\":\"Kvng\",\"lastname\":\"Kvng\",\"othername\":\"KC\",\"email\":\"kvng656@gmail.com\",\"phone\":\"+2348038752155\",\"address\":\"Somewhere on earth, Jos\",\"nationality\":\"Nigeria\",\"place_of_birth\":\"Jos\",\"occupation\":\"Software Engineer\",\"fiscal_code\":\"KAY SAN87D15 A00\",\"gender\":\"Male\"}'),(3,3,1,'documents/gThXbWz4uNAuRUHivCCESb4qvHKbhLC647FHGXMp.jpg','documents/76fp3c8aGTFKTNLdkDj7pOusgVdn3LY1kboW2Jzj.jpg','selfies/m0AZz59f9EokokI7kg0pwlD4D9q6R5mAnyl9IG8p.jpg',1,'proofs_of_address/Jde3eD8LPE57nStIDUozFyCCTD7yUf5Qv46QouZE.jpg',2,'approved',NULL,'2023-12-23 20:46:03','2023-12-23 22:21:39','{\"firstname\":\"Samson\",\"lastname\":\"Johnson\",\"email\":\"johnsonsamson23@gmail.com\",\"phone\":\"+412345767889464\",\"address\":\"FCT, Jos\",\"nationality\":\"Nigeria\",\"place_of_birth\":\"Nigeria\",\"occupation\":\"Farmer\",\"fiscal_code\":\"2368939373738383\",\"gender\":\"Male\"}');
/*!40000 ALTER TABLE `kyc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kyc_address_proof_types`
--

DROP TABLE IF EXISTS `kyc_address_proof_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kyc_address_proof_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kyc_address_proof_types`
--

LOCK TABLES `kyc_address_proof_types` WRITE;
/*!40000 ALTER TABLE `kyc_address_proof_types` DISABLE KEYS */;
INSERT INTO `kyc_address_proof_types` VALUES (1,'Utility Bill (Electricity bill, Water bill, etc.)',NULL,NULL),(2,'Credit Card Bill or Statement',NULL,NULL),(3,'Bank Statement',NULL,NULL),(4,'Bank Letter',NULL,NULL),(5,'Social Insurance Statement',NULL,NULL),(6,'Paycheck',NULL,NULL),(7,'Letter from Public Authority (Court, etc.)',NULL,NULL),(8,'Insurance Policy (for your car or home)',NULL,NULL),(9,'Rental or Mortgage Contract/Statement',NULL,NULL),(10,'Car\'s Registration',NULL,NULL),(11,'Change of Address Form',NULL,NULL),(12,'DD214',NULL,NULL),(13,'Official Letter from Employer or School',NULL,NULL),(14,'Voter Registration Confirmation',NULL,NULL);
/*!40000 ALTER TABLE `kyc_address_proof_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kyc_document_type`
--

DROP TABLE IF EXISTS `kyc_document_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kyc_document_type` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kyc_document_type`
--

LOCK TABLES `kyc_document_type` WRITE;
/*!40000 ALTER TABLE `kyc_document_type` DISABLE KEYS */;
INSERT INTO `kyc_document_type` VALUES (1,'EU National ID Card / Carta d\'Identita\' Europea',NULL,NULL),(2,'ID Card / Carta Di Identità',NULL,NULL),(3,'EU Driving License / Patente Di Guida',NULL,NULL),(4,'Others / Altro',NULL,NULL),(5,'Passport / Passaporto',NULL,NULL),(6,'EU Resident Card / Permesso Di Soggiorno',NULL,NULL);
/*!40000 ALTER TABLE `kyc_document_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `postcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2016_06_01_000001_create_oauth_auth_codes_table',1),(4,'2016_06_01_000002_create_oauth_access_tokens_table',1),(5,'2016_06_01_000003_create_oauth_refresh_tokens_table',1),(6,'2016_06_01_000004_create_oauth_clients_table',1),(7,'2016_06_01_000005_create_oauth_personal_access_clients_table',1),(8,'2019_08_19_000000_create_failed_jobs_table',1),(9,'2019_12_14_000001_create_personal_access_tokens_table',1),(10,'2023_10_01_175034_create_locations_table',1),(11,'2023_10_01_175530_create_customers_table',1),(12,'2023_10_01_181748_create_couriers_table',1),(13,'2023_10_01_181821_create_dispatchers_table',1),(14,'2023_10_01_184316_create_admins_table',1),(15,'2023_10_01_185552_create_shipping_rates_table',1),(16,'2023_10_01_185553_create_orders_table',1),(17,'2023_10_01_190628_create_vehicles_table',1),(18,'2023_10_01_213229_create_order_batches_table',1),(19,'2023_10_01_213929_add_batch_id_col_to_orders_table',1),(20,'2023_10_05_182532_add_postcode_col_to_locations_table',1),(21,'2023_10_06_190015_create_order_packages_table',1),(22,'2023_10_08_153417_create_payments_table',1),(23,'2023_10_09_092606_add_pickup_cost_to_shipping_rates_table',1),(24,'2023_10_10_063141_add_blocked_col_to_users_table',1),(25,'2023_10_10_073114_add_customer_id_col_to_payments_table',1),(26,'2023_10_10_081718_add_email_sent_col_to_payments_table',1),(27,'2023_10_25_071312_create_e_u_funds_transfer_rates_table',1),(28,'2023_10_25_071325_create_intl_funds_transfer_rates_table',1),(29,'2023_10_26_133444_create_walk_in_customers_table',1),(30,'2023_10_26_211532_add_walkincustomercol_to_orders_table',1),(31,'2023_10_27_080804_create_e_u_fund_transfer_orders_table',1),(32,'2023_10_27_080819_create_intl_fund_transfer_orders_table',1),(33,'2023_10_28_075741_add_contact_to_walk_in_customers_table',1),(34,'2023_11_04_182410_add_otp_to_users_table',1),(35,'2023_11_23_184011_create_kyc_document_type_table',2),(36,'2023_11_23_185038_create_kyc_address_proof_types_table',3),(40,'2023_12_03_104414_create_transactions_table',6),(41,'2023_12_12_075509_create_operation_countries_table',7),(44,'2023_12_14_181545_add_phone_to_users_table',9),(45,'2023_12_14_190559_add_receiveing_and_sending_to_operation_countries_table',10),(46,'2023_11_23_190245_create_kyc_table',11),(47,'2023_12_15_091949_add_personal_information_col_to_kyc_table',12),(48,'2023_12_15_200626_create_transfer_intent_table',13);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_access_tokens`
--

LOCK TABLES `oauth_access_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `scopes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_auth_codes`
--

LOCK TABLES `oauth_auth_codes` WRITE;
/*!40000 ALTER TABLE `oauth_auth_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_auth_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_clients`
--

LOCK TABLES `oauth_clients` WRITE;
/*!40000 ALTER TABLE `oauth_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_personal_access_clients`
--

LOCK TABLES `oauth_personal_access_clients` WRITE;
/*!40000 ALTER TABLE `oauth_personal_access_clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_personal_access_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oauth_refresh_tokens`
--

LOCK TABLES `oauth_refresh_tokens` WRITE;
/*!40000 ALTER TABLE `oauth_refresh_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `oauth_refresh_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operation_countries`
--

DROP TABLE IF EXISTS `operation_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operation_countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_alpha2code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_alpha3code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `receiving` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `sending` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operation_countries`
--

LOCK TABLES `operation_countries` WRITE;
/*!40000 ALTER TABLE `operation_countries` DISABLE KEYS */;
INSERT INTO `operation_countries` VALUES (1,'Australia','AU','AUS',NULL,NULL,'yes','yes'),(2,'Austria','AT','AUT',NULL,NULL,'yes','yes'),(3,'Italy','IT','ITA',NULL,NULL,'yes','yes'),(4,'Greece','GR','GRC',NULL,NULL,'yes','yes');
/*!40000 ALTER TABLE `operation_countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_batches`
--

DROP TABLE IF EXISTS `order_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dispatcher_id` bigint unsigned DEFAULT NULL,
  `status` enum('assigned','in_transit','delivered','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assigned',
  `location_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_batches_dispatcher_id_foreign` (`dispatcher_id`),
  KEY `order_batches_location_id_foreign` (`location_id`),
  CONSTRAINT `order_batches_dispatcher_id_foreign` FOREIGN KEY (`dispatcher_id`) REFERENCES `dispatchers` (`id`),
  CONSTRAINT `order_batches_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_batches`
--

LOCK TABLES `order_batches` WRITE;
/*!40000 ALTER TABLE `order_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_packages`
--

DROP TABLE IF EXISTS `order_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `length` double(8,2) DEFAULT NULL,
  `width` double(8,2) DEFAULT NULL,
  `height` double(8,2) DEFAULT NULL,
  `weight` double(8,2) DEFAULT NULL,
  `qty` double(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_packages_order_id_foreign` (`order_id`),
  CONSTRAINT `order_packages_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_packages`
--

LOCK TABLES `order_packages` WRITE;
/*!40000 ALTER TABLE `order_packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned DEFAULT NULL,
  `walk_in_customer_id` bigint unsigned DEFAULT NULL,
  `delivery_customer_id` bigint unsigned DEFAULT NULL,
  `courier_id` bigint unsigned DEFAULT NULL,
  `dispatcher_id` bigint unsigned DEFAULT NULL,
  `batch_id` bigint unsigned DEFAULT NULL,
  `shipping_rate_id` bigint unsigned DEFAULT NULL,
  `status` enum('unpaid','placed','assigned','in_transit','delivered','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `pickup_location_id` bigint unsigned DEFAULT NULL,
  `delivery_location_id` bigint unsigned DEFAULT NULL,
  `current_location_id` bigint unsigned DEFAULT NULL,
  `tracking_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pickup_time` timestamp NULL DEFAULT NULL,
  `delivery_time` timestamp NULL DEFAULT NULL,
  `delivery_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_phone_alt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_address1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `delivery_address2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `delivery_zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_phone_alt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_address1` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pickup_address2` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pickup_zip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pickup_country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `cond_of_goods` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `val_of_goods` double(8,2) DEFAULT NULL,
  `val_cur` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_tracking_id_unique` (`tracking_id`),
  KEY `orders_customer_id_foreign` (`customer_id`),
  KEY `orders_delivery_customer_id_foreign` (`delivery_customer_id`),
  KEY `orders_courier_id_foreign` (`courier_id`),
  KEY `orders_dispatcher_id_foreign` (`dispatcher_id`),
  KEY `orders_shipping_rate_id_foreign` (`shipping_rate_id`),
  KEY `orders_pickup_location_id_foreign` (`pickup_location_id`),
  KEY `orders_delivery_location_id_foreign` (`delivery_location_id`),
  KEY `orders_current_location_id_foreign` (`current_location_id`),
  KEY `orders_batch_id_foreign` (`batch_id`),
  KEY `orders_walk_in_customer_id_foreign` (`walk_in_customer_id`),
  CONSTRAINT `orders_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `order_batches` (`id`),
  CONSTRAINT `orders_courier_id_foreign` FOREIGN KEY (`courier_id`) REFERENCES `couriers` (`id`),
  CONSTRAINT `orders_current_location_id_foreign` FOREIGN KEY (`current_location_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `orders_delivery_customer_id_foreign` FOREIGN KEY (`delivery_customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `orders_delivery_location_id_foreign` FOREIGN KEY (`delivery_location_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `orders_dispatcher_id_foreign` FOREIGN KEY (`dispatcher_id`) REFERENCES `dispatchers` (`id`),
  CONSTRAINT `orders_pickup_location_id_foreign` FOREIGN KEY (`pickup_location_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `orders_shipping_rate_id_foreign` FOREIGN KEY (`shipping_rate_id`) REFERENCES `shipping_rates` (`id`),
  CONSTRAINT `orders_walk_in_customer_id_foreign` FOREIGN KEY (`walk_in_customer_id`) REFERENCES `walk_in_customers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  `customer_id` bigint unsigned DEFAULT NULL,
  `ref` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amt_expected` double(8,2) NOT NULL DEFAULT '0.00',
  `amt_paid` double(8,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','done','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `misc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_order_id_foreign` (`order_id`),
  KEY `payments_customer_id_foreign` (`customer_id`),
  CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_rates`
--

DROP TABLE IF EXISTS `shipping_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shipping_rates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_id` bigint unsigned DEFAULT NULL,
  `destination_id` bigint unsigned DEFAULT NULL,
  `weight_start` double(8,2) DEFAULT NULL,
  `weight_end` double(8,2) DEFAULT NULL,
  `price` double(8,2) DEFAULT NULL,
  `length` double(8,2) DEFAULT NULL,
  `width` double(8,2) DEFAULT NULL,
  `height` double(8,2) DEFAULT NULL,
  `pickup_cost_per_km` double(8,2) DEFAULT '0.00',
  `delivery_cost_per_km` double(8,2) DEFAULT '0.00',
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shipping_rates_origin_id_foreign` (`origin_id`),
  KEY `shipping_rates_destination_id_foreign` (`destination_id`),
  CONSTRAINT `shipping_rates_destination_id_foreign` FOREIGN KEY (`destination_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `shipping_rates_origin_id_foreign` FOREIGN KEY (`origin_id`) REFERENCES `locations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_rates`
--

LOCK TABLES `shipping_rates` WRITE;
/*!40000 ALTER TABLE `shipping_rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `shipping_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supported_banks`
--

DROP TABLE IF EXISTS `supported_banks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supported_banks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `bank_code` varchar(45) NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `country_code` varchar(45) NOT NULL,
  `extra_info` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supported_banks`
--

LOCK TABLES `supported_banks` WRITE;
/*!40000 ALTER TABLE `supported_banks` DISABLE KEYS */;
INSERT INTO `supported_banks` VALUES (1,'123456','African Bank 1','ZA',NULL,'2023-12-24 20:15:40','2023-12-24 20:15:45'),(2,'264905','African Bank 2','NG',NULL,'2023-12-24 20:15:41','2023-12-24 20:15:47'),(3,'174068','European Bank 1','GR',NULL,'2023-12-24 20:15:43','2023-12-24 20:15:48'),(4,'1874057','European Bank 2','IT',NULL,'2023-12-24 20:15:44','2023-12-24 20:15:49');
/*!40000 ALTER TABLE `supported_banks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `transaction_type` enum('transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'transfer',
  `beneficiary_id` bigint unsigned NOT NULL,
  `beneficiary_account_id` bigint unsigned NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_amount` double NOT NULL,
  `beneficiary_account_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_account_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_bank_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_bank_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_status` enum('success','pending','failed','refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `transaction_reference` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  KEY `transactions_beneficiary_id_foreign` (`beneficiary_id`),
  KEY `transactions_beneficiary_account_id_foreign` (`beneficiary_account_id`),
  CONSTRAINT `transactions_beneficiary_account_id_foreign` FOREIGN KEY (`beneficiary_account_id`) REFERENCES `beneficiary_accounts` (`id`),
  CONSTRAINT `transactions_beneficiary_id_foreign` FOREIGN KEY (`beneficiary_id`) REFERENCES `beneficiaries` (`id`),
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transfer_intent`
--

DROP TABLE IF EXISTS `transfer_intent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transfer_intent` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `intent_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_intent_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfer_intent_user_id_foreign` (`user_id`),
  CONSTRAINT `transfer_intent_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transfer_intent`
--

LOCK TABLES `transfer_intent` WRITE;
/*!40000 ALTER TABLE `transfer_intent` DISABLE KEYS */;
INSERT INTO `transfer_intent` VALUES (1,3,'pi_3OREXWCPuHcGXdFK17gV2KvC_secret_NDAgpvbLw0PhPWX4J74dGEYSM','Stripe\\PaymentIntent JSON: {\n    \"id\": \"pi_3OREXWCPuHcGXdFK17gV2KvC\",\n    \"object\": \"payment_intent\",\n    \"amount\": 20400,\n    \"amount_capturable\": 0,\n    \"amount_details\": {\n        \"tip\": []\n    },\n    \"amount_received\": 0,\n    \"application\": null,\n    \"application_fee_amount\": null,\n    \"automatic_payment_methods\": {\n        \"allow_redirects\": \"always\",\n        \"enabled\": true\n    },\n    \"canceled_at\": null,\n    \"cancellation_reason\": null,\n    \"capture_method\": \"automatic\",\n    \"client_secret\": \"pi_3OREXWCPuHcGXdFK17gV2KvC_secret_NDAgpvbLw0PhPWX4J74dGEYSM\",\n    \"confirmation_method\": \"automatic\",\n    \"created\": 1703512194,\n    \"currency\": \"eur\",\n    \"customer\": null,\n    \"description\": null,\n    \"invoice\": null,\n    \"last_payment_error\": null,\n    \"latest_charge\": null,\n    \"livemode\": false,\n    \"metadata\": {\n        \"beneficiary_account_id\": \"1\",\n        \"beneficiary_account_name\": \"Dev Ben\",\n        \"beneficiary_account_number\": \"12369887085\",\n        \"beneficiary_bank_code\": \"174068\",\n        \"beneficiary_bank_name\": \"European Bank 1\",\n        \"beneficiary_id\": \"3\"\n    },\n    \"next_action\": null,\n    \"on_behalf_of\": null,\n    \"payment_method\": null,\n    \"payment_method_configuration_details\": {\n        \"id\": \"pmc_1LRcNhCPuHcGXdFKlIjueJUS\",\n        \"parent\": null\n    },\n    \"payment_method_options\": {\n        \"card\": {\n            \"installments\": null,\n            \"mandate_options\": null,\n            \"network\": null,\n            \"request_three_d_secure\": \"automatic\"\n        }\n    },\n    \"payment_method_types\": [\n        \"card\"\n    ],\n    \"processing\": null,\n    \"receipt_email\": null,\n    \"review\": null,\n    \"setup_future_usage\": null,\n    \"shipping\": null,\n    \"source\": null,\n    \"statement_descriptor\": null,\n    \"statement_descriptor_suffix\": null,\n    \"status\": \"requires_payment_method\",\n    \"transfer_data\": null,\n    \"transfer_group\": null\n}','2023-12-25 12:49:56','2023-12-25 12:49:56');
/*!40000 ALTER TABLE `transfer_intent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `user_type` enum('user','courier','dispatcher','admin','mobile') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `blocked` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` bigint unsigned DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Kefas Kingsley','kvng656@gmail.com',NULL,'$2y$10$vof22O0ZRXn9fDWby9QP1.9UzEqh3ZC3ff5Fzywedf30FiHYDz7GK',1,'mobile',0,NULL,NULL,'2023-11-23 18:57:29',NULL,0,NULL,'',NULL),(3,'Samson Johnson','johnsonsamson23@gmail.com',NULL,'$2y$10$Xwl2Hx6WyUelDCnE0rjADOaKfuB6.9M660AidRWvnfjxGO2bhMGpC',1,'user',0,NULL,'2023-12-23 15:50:45','2023-12-23 15:50:45',NULL,NULL,'+412345767889464','IT',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `courier_id` bigint unsigned DEFAULT NULL,
  `status` enum('active','inactive','decommisioned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vehicles_courier_id_foreign` (`courier_id`),
  CONSTRAINT `vehicles_courier_id_foreign` FOREIGN KEY (`courier_id`) REFERENCES `couriers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `walk_in_customers`
--

DROP TABLE IF EXISTS `walk_in_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `walk_in_customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `surname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthDate` date DEFAULT NULL,
  `birthPlace` date DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `belfioreCode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_num` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_front` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_back` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kyc_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `walk_in_customers`
--

LOCK TABLES `walk_in_customers` WRITE;
/*!40000 ALTER TABLE `walk_in_customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `walk_in_customers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-12-26 14:23:08
