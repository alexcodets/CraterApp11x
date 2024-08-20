/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `add_ons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `add_ons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `slug` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `module_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `add_ons_company_id_foreign` (`company_id`),
  KEY `add_ons_creator_id_foreign` (`creator_id`),
  KEY `add_ons_module_id_foreign` (`module_id`),
  CONSTRAINT `add_ons_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `add_ons_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `add_ons_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `addresses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street_1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `county` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_exempt` tinyint(1) DEFAULT NULL,
  `delivery_method` enum('Email','Paper','InterFax','PostalMethods','SMS') COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_notices` tinyint(1) DEFAULT NULL,
  `tax_id_vatin` varchar(160) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `pcode` int(11) DEFAULT NULL,
  `tax_agency_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_country_id_foreign` (`country_id`),
  KEY `addresses_user_id_foreign` (`user_id`),
  KEY `addresses_company_id_foreign` (`company_id`),
  CONSTRAINT `addresses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `addresses_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aditional_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aditional_charges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `profile_extension_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `profile_did_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aditional_charges_profile_did_id_foreign` (`profile_did_id`),
  CONSTRAINT `aditional_charges_profile_did_id_foreign` FOREIGN KEY (`profile_did_id`) REFERENCES `profile_did` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `authorize`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `authorize` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payer_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `credit_card_full` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_card` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expiration_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street_1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_street_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `payment_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `ACH_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_on_account` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_check` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `routing_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `authorize_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `authorize_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `transaction_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_API` enum('CIM','AIM') COLLATE utf8_unicode_ci NOT NULL,
  `payment_account_validation_mode` enum('none','test','live') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `test_mode` tinyint(4) NOT NULL,
  `developer_mode` tinyint(4) NOT NULL,
  `currency` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `creator_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_default` tinyint(4) DEFAULT NULL,
  `enable_identification_verification` tinyint(4) DEFAULT '0',
  `enable_fee_charges` tinyint(4) DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aux_vault_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aux_vault_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `endpoint` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `api_key` text COLLATE utf8_unicode_ci NOT NULL,
  `merchant_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `production` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `enable_identification_verification` tinyint(4) DEFAULT '0',
  `enable_fee_charges` tinyint(4) DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `aux_vaults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aux_vaults` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `base_amount` decimal(8,2) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `fees` decimal(8,2) DEFAULT NULL,
  `card_number` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_code` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expiry_date` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cvv` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ach_routing_number` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ach_account_number` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transaction_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `authorize_setting_id` bigint(20) unsigned DEFAULT NULL,
  `aux_vault_setting_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aux_vaults_aux_vault_setting_id_foreign` (`aux_vault_setting_id`),
  CONSTRAINT `aux_vaults_aux_vault_setting_id_foreign` FOREIGN KEY (`aux_vault_setting_id`) REFERENCES `aux_vault_settings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_bundles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_bundles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `service` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` bigint(20) unsigned NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `no_taxable` tinyint(1) DEFAULT NULL,
  `allow_taxes` tinyint(1) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_configs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `conexion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `invm` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'invoice_mode',
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `host` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bscl` enum('0','1') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Business Class',
  `svcl` enum('0','1') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Service Class',
  `fclt` tinyint(1) DEFAULT NULL COMMENT 'Has Facilities',
  `reg` tinyint(1) DEFAULT NULL COMMENT 'Is Regulated',
  `frch` tinyint(1) DEFAULT NULL COMMENT 'Is Franchise',
  `lfln` tinyint(1) DEFAULT NULL COMMENT 'The customer is a Lifeline participant',
  `dtl` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Return/Response Detail',
  `summ` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Return/Response Summary',
  `retnb` tinyint(1) DEFAULT NULL COMMENT 'Return Non-Billable Taxes',
  `retext` tinyint(1) DEFAULT NULL COMMENT 'Return Extended Data',
  `incrf` tinyint(1) DEFAULT NULL COMMENT 'Return Reporting Information',
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `item_did_id` int(10) unsigned DEFAULT NULL,
  `item_cdr_id` int(10) unsigned DEFAULT NULL,
  `item_extension_id` int(10) unsigned DEFAULT NULL,
  `item_international_id` int(10) unsigned DEFAULT NULL,
  `item_custom_id` int(10) unsigned DEFAULT NULL,
  `item_toll_free_id` int(10) unsigned DEFAULT NULL,
  `custom_app_rate_item_id` bigint(20) unsigned DEFAULT NULL,
  `services_price_item_id` bigint(20) unsigned DEFAULT NULL,
  `additional_charges_item_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `profile_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_identifier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_reference` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `intDefault` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `avalara_configs_user_name_unique` (`user_name`),
  KEY `avalara_configs_company_id_foreign` (`company_id`),
  CONSTRAINT `avalara_configs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_exemptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_exemptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `exemption_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `frc` tinyint(1) DEFAULT NULL COMMENT 'Force: Override level exempt flag on tax type wildcard exemptions',
  `tpe` smallint(5) unsigned DEFAULT NULL COMMENT 'Tax Type ID',
  `cat` smallint(5) unsigned DEFAULT NULL COMMENT 'Tax Category Id: Tax Category to exempt.',
  `dom` smallint(5) unsigned DEFAULT NULL COMMENT 'Exemption Domain',
  `scp` smallint(5) unsigned DEFAULT NULL COMMENT 'Exemption Exemption Scope',
  `exnb` tinyint(1) DEFAULT NULL COMMENT 'Exempt Non-billable: Determines if non-billable taxes are to be considered as candidates for exemption',
  `pbx_services_id` int(10) unsigned DEFAULT NULL,
  `avalara_locations_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `avalara_exemptions_pbx_services_id_foreign` (`pbx_services_id`),
  KEY `avalara_exemptions_avalara_locations_id_foreign` (`avalara_locations_id`),
  CONSTRAINT `avalara_exemptions_avalara_locations_id_foreign` FOREIGN KEY (`avalara_locations_id`) REFERENCES `avalara_locations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `avalara_exemptions_pbx_services_id_foreign` FOREIGN KEY (`pbx_services_id`) REFERENCES `pbx_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '0 => draft, 1 => active, 2 => void, 3 => reversed, 4 => edited',
  `pbx_service_id` int(10) unsigned DEFAULT NULL,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `avalara_invoice_number` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `document_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_locationables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_locationables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `locationable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `avalara_location_id` bigint(20) unsigned NOT NULL,
  `small_locationable_id` int(10) unsigned DEFAULT NULL,
  `locationable_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `county` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `zip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `incorporated` tinyint(1) DEFAULT NULL,
  `pcd` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'PCode',
  `fips` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'FIPS',
  `npa` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'NPANXX',
  `geo` tinyint(1) DEFAULT NULL COMMENT 'should be geocoded in order to obtain taxing jurisdiction',
  `type` tinyint(3) unsigned DEFAULT NULL COMMENT 'Loc Type: [0 => pcd, 1 => fips, 2 => npa, 3 => geo]',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  `company_id` int(10) unsigned DEFAULT NULL,
  `addresses_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned DEFAULT NULL,
  `commit` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `operation_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `request` json DEFAULT NULL COMMENT 'data sent to the endpoint',
  `response` json DEFAULT NULL,
  `procesing_time` smallint(5) unsigned DEFAULT NULL COMMENT 'procesing time in miliseconds',
  `invoice_id` int(10) unsigned NOT NULL,
  `pbx_service_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `avalara_log_id` bigint(20) unsigned DEFAULT NULL,
  `note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_service_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_service_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `service_type` int(11) NOT NULL,
  `avalara_transaction_types` int(11) NOT NULL,
  `service_type_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `taxable_amount` tinyint(1) DEFAULT '0',
  `lines` tinyint(1) DEFAULT '0',
  `minutes` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_taxe_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_taxe_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cid` smallint(5) unsigned NOT NULL COMMENT 'category_id from avalara',
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `avalara_taxe_categories_company_id_foreign` (`company_id`),
  KEY `avalara_taxe_categories_creator_id_foreign` (`creator_id`),
  CONSTRAINT `avalara_taxe_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `avalara_taxe_categories_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_taxe_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_taxe_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tid` smallint(5) unsigned NOT NULL COMMENT 'tax_id from avalara',
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `avalara_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avalara_taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bill` tinyint(1) NOT NULL COMMENT 'Billable Indicates if the tax is billable to your customer',
  `cmpl` tinyint(1) NOT NULL COMMENT 'Compliance Indicates if the tax is to be reported to the jurisdiction',
  `tm` double NOT NULL COMMENT 'Taxable Measure The basis for calculation of rate-based taxes',
  `calc` smallint(5) unsigned NOT NULL COMMENT 'Calculation Type Indicates how the tax is calculated',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `exm` double NOT NULL COMMENT 'Exempt Sale Amount',
  `lns` smallint(5) unsigned NOT NULL COMMENT 'Number of lines taxed',
  `min` double NOT NULL COMMENT 'Amount of minutes taxed',
  `pcd` int(10) unsigned NOT NULL COMMENT 'Reporting PCode PCode representing reporting tax jurisdiction',
  `taxpcd` int(10) unsigned DEFAULT NULL COMMENT 'Taxing PCode PCode representing taxing jurisdiction. Only returned when retext in RequestConfig is set to true',
  `rate` double NOT NULL COMMENT 'Applicable tax rate',
  `sur` tinyint(1) NOT NULL COMMENT 'Surcharge Indicates if this tax is a surcharge',
  `tax` double NOT NULL COMMENT ' Tax Amount For rate-based taxes, Tax Amount = Taxable Measure * Rate',
  `lvl` smallint(5) unsigned NOT NULL COMMENT 'Tax Level Indicates the jurisdiction level of the tax',
  `usexm` tinyint(1) DEFAULT NULL COMMENT 'User Exempt Flag indicating if the tax has been exempted by the user via Exemptions (exms)',
  `notax` tinyint(1) DEFAULT NULL COMMENT 'Is No Tax Transaction Flag indicating that the transaction processed successfully but returned no taxes.',
  `trans` int(10) unsigned DEFAULT NULL COMMENT 'Transaction Type Transaction type use to calculate tax',
  `svc` int(10) unsigned DEFAULT NULL COMMENT 'Service Type Service type use to calculate tax',
  `chg` double DEFAULT NULL COMMENT 'Charge Charge used to calculate tax.',
  `avalara_type_id` bigint(20) unsigned NOT NULL,
  `avalara_category_id` bigint(20) unsigned NOT NULL,
  `package_id` int(10) unsigned DEFAULT NULL,
  `invoice_item_id` int(10) unsigned DEFAULT NULL,
  `estimate_item_id` int(10) unsigned DEFAULT NULL,
  `package_item_id` int(10) unsigned DEFAULT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `is_adj` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `avalara_invoice_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `avalara_taxes_avalara_type_id_foreign` (`avalara_type_id`),
  CONSTRAINT `avalara_taxes_avalara_type_id_foreign` FOREIGN KEY (`avalara_type_id`) REFERENCES `avalara_taxe_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `balance_customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `balance_customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` enum('A','D') COLLATE utf8_unicode_ci NOT NULL,
  `present_balance` double unsigned DEFAULT '0',
  `amount_op` double unsigned DEFAULT '0',
  `amount_final` double unsigned DEFAULT '0',
  `payment_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bandwidth_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bandwidth_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `accountid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '0',
  `selected` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bw_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bw_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `account_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bw_configs_user_unique` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  UNIQUE KEY `cache_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `call_detail_register_totals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_detail_register_totals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `duration` int(11) NOT NULL,
  `total_duration` int(11) NOT NULL COMMENT 'The duration applying the minutes increments',
  `rate` decimal(20,5) DEFAULT NULL,
  `calls` int(11) NOT NULL COMMENT 'The total calls made',
  `cost` double unsigned DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT 'inbound(0) / outbound(1)',
  `exclusive_seconds` int(10) unsigned NOT NULL DEFAULT '0',
  `exclusive_cost` double(9,5) unsigned DEFAULT '0.00000',
  `number` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_did_id` int(10) unsigned DEFAULT NULL,
  `pbx_extension_id` int(10) unsigned DEFAULT NULL,
  `international_rate_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `pbx_services_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `prepaid_check` int(11) NOT NULL DEFAULT '0',
  `exclusive_cost_paid` double(9,5) unsigned NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `call_detail_register_totals_pbx_did_id_foreign` (`pbx_did_id`),
  KEY `call_detail_register_totals_pbx_services_id_foreign` (`pbx_services_id`),
  KEY `cdr_totals_ext_inv_numb` (`pbx_extension_id`,`invoice_id`,`number`),
  KEY `call_detail_register_totals_international_rate_id_foreign` (`international_rate_id`),
  CONSTRAINT `call_detail_register_totals_international_rate_id_foreign` FOREIGN KEY (`international_rate_id`) REFERENCES `international_rate` (`id`) ON DELETE CASCADE,
  CONSTRAINT `call_detail_register_totals_pbx_did_id_foreign` FOREIGN KEY (`pbx_did_id`) REFERENCES `pbx_did` (`id`) ON DELETE CASCADE,
  CONSTRAINT `call_detail_register_totals_pbx_extension_id_foreign` FOREIGN KEY (`pbx_extension_id`) REFERENCES `pbx_extensions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `call_detail_register_totals_pbx_services_id_foreign` FOREIGN KEY (`pbx_services_id`) REFERENCES `pbx_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `call_detail_register_totals_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_detail_register_totals_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `call_detail_register_totals_id` bigint(20) unsigned NOT NULL,
  `amount` double unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `taxamount` double(9,5) unsigned DEFAULT '0.00000',
  `amountbruto` double(8,5) DEFAULT '0.00000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `call_detail_registers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_detail_registers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` int(10) unsigned NOT NULL,
  `duration` int(10) unsigned NOT NULL,
  `billing_duration` int(10) unsigned DEFAULT NULL,
  `round_duration` int(10) unsigned DEFAULT NULL,
  `cost` double(9,5) unsigned DEFAULT NULL,
  `exclusive_seconds` int(10) unsigned NOT NULL DEFAULT '0',
  `exclusive_cost` double(9,5) unsigned DEFAULT '0.00000',
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `unique_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT 'inbound(0) / outbound(1)',
  `trunk_id` int(11) DEFAULT NULL,
  `billed_at` date DEFAULT NULL,
  `pbx_did_id` int(10) unsigned DEFAULT NULL,
  `pbx_extension_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `call_detail_registers_unique_id_start_date_index` (`unique_id`,`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cash_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cash_register_id` bigint(20) unsigned NOT NULL,
  `cash_received` decimal(8,2) NOT NULL DEFAULT '0.00',
  `initial_amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `final_amount` decimal(8,2) DEFAULT '0.00',
  `open` tinyint(1) NOT NULL DEFAULT '1',
  `open_note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `close_note` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `open_date` timestamp NULL DEFAULT NULL,
  `close_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `other_income` decimal(8,2) NOT NULL DEFAULT '0.00',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_histories_ref_unique` (`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cash_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_register` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint(20) unsigned NOT NULL,
  `store_id` bigint(20) unsigned NOT NULL,
  `device` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `open_cash` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cash_register_assign_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_register_assign_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `cash_register_id` bigint(20) unsigned NOT NULL,
  `cash_history_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cash_register_cash_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_register_cash_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `amount` bigint(20) NOT NULL DEFAULT '0',
  `type` enum('I','R') COLLATE utf8_unicode_ci NOT NULL,
  `cash_histories_id` bigint(20) unsigned NOT NULL,
  `cash_register_id` bigint(20) unsigned NOT NULL,
  `creator_id` bigint(20) unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cash_register_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_register_invoice` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `cash_register_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cash_history_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cash_register_table_table_pivot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_register_table_table_pivot` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cash_register_id` bigint(20) unsigned NOT NULL,
  `table_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cash_register_table_table_pivot_cash_register_id_foreign` (`cash_register_id`),
  KEY `cash_register_table_table_pivot_table_id_foreign` (`table_id`),
  CONSTRAINT `cash_register_table_table_pivot_cash_register_id_foreign` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_register` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_register_table_table_pivot_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cash_register_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_register_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cash_register_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unique_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_identifier` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title_header` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subtitle_header` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_wallpaper_login` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avalara_location_id` bigint(20) unsigned DEFAULT NULL COMMENT 'General location for Avalara Invoice',
  PRIMARY KEY (`id`),
  KEY `companies_avalara_location_id_foreign` (`avalara_location_id`),
  CONSTRAINT `companies_avalara_location_id_foreign` FOREIGN KEY (`avalara_location_id`) REFERENCES `avalara_locations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_settings_company_id_foreign` (`company_id`),
  CONSTRAINT `company_settings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companydefaultsetting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companydefaultsetting` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contact_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_invoice` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `identification` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `second_phone` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `allow_receive_emails` tinyint(1) DEFAULT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `position` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reports` tinyint(1) DEFAULT NULL,
  `payments_accounts` tinyint(1) DEFAULT NULL,
  `tickets` tinyint(1) DEFAULT NULL,
  `payments` tinyint(1) DEFAULT NULL,
  `estimates` tinyint(1) DEFAULT NULL,
  `invoices` tinyint(1) DEFAULT NULL,
  `repeat_password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_in_credentials` tinyint(1) DEFAULT NULL,
  `type` enum('B','S','R') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'B',
  `email` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `email_estimates` tinyint(1) DEFAULT '0',
  `email_invoices` tinyint(1) DEFAULT '0',
  `email_payments` tinyint(1) DEFAULT '0',
  `email_services` tinyint(1) DEFAULT '0',
  `email_pbx_services` tinyint(1) DEFAULT '0',
  `email_tickets` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `core_pos_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `core_pos_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `document_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `payment_id` int(10) unsigned DEFAULT NULL,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `hold_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `item_price` bigint(20) DEFAULT NULL,
  `item_total` bigint(20) DEFAULT NULL,
  `item_quantity` bigint(20) DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount_amount` bigint(20) DEFAULT NULL,
  `tax_id` int(10) unsigned DEFAULT NULL,
  `tax_type_percent` decimal(8,2) DEFAULT NULL,
  `tax_type_amount` bigint(20) DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `cash_register_id` int(10) unsigned DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tables` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `qty_persons` bigint(20) DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT '1',
  `tip_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tip_amount` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `corebill_logs_dev`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `corebill_logs_dev` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `controller` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `method` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `request` longtext COLLATE utf8_unicode_ci,
  `headers` text COLLATE utf8_unicode_ci,
  `data_in` longtext COLLATE utf8_unicode_ci,
  `data_out` text COLLATE utf8_unicode_ci,
  `message` text COLLATE utf8_unicode_ci,
  `time` double DEFAULT NULL,
  `type` enum('D','E') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'D',
  `date_reg` datetime NOT NULL,
  `company` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phonecode` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `countries_id_index` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `precision` int(11) NOT NULL,
  `thousand_separator` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `decimal_separator` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `swap_currency_symbol` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_app_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_app_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `office` tinyint(1) NOT NULL DEFAULT '1',
  `office_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `bussiness` tinyint(1) NOT NULL DEFAULT '1',
  `bussiness_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `web` tinyint(1) NOT NULL DEFAULT '1',
  `web_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `agent` tinyint(1) NOT NULL DEFAULT '1',
  `agent_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `supervisor` tinyint(1) NOT NULL DEFAULT '1',
  `supervisor_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `mobile` tinyint(1) NOT NULL DEFAULT '1',
  `mobile_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `crm` tinyint(1) NOT NULL DEFAULT '1',
  `crm_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `call_pop_up` tinyint(1) NOT NULL DEFAULT '1',
  `call_pop_up_price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_did_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_did_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('TF','IN','LO') COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_field_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_field_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custom_field_valuable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `custom_field_valuable_id` int(10) unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `boolean_answer` tinyint(1) DEFAULT NULL,
  `date_answer` date DEFAULT NULL,
  `time_answer` time DEFAULT NULL,
  `string_answer` text COLLATE utf8_unicode_ci,
  `number_answer` bigint(20) unsigned DEFAULT NULL,
  `date_time_answer` datetime DEFAULT NULL,
  `custom_field_id` bigint(20) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_field_values_custom_field_id_foreign` (`custom_field_id`),
  KEY `custom_field_values_company_id_foreign` (`company_id`),
  CONSTRAINT `custom_field_values_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `custom_field_values_custom_field_id_foreign` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_fields` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `placeholder` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `options` json DEFAULT NULL,
  `boolean_answer` tinyint(1) DEFAULT NULL,
  `date_answer` date DEFAULT NULL,
  `time_answer` time DEFAULT NULL,
  `string_answer` text COLLATE utf8_unicode_ci,
  `number_answer` bigint(20) unsigned DEFAULT NULL,
  `date_time_answer` datetime DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `order` bigint(20) unsigned NOT NULL DEFAULT '1',
  `company_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_fields_company_id_foreign` (`company_id`),
  CONSTRAINT `custom_fields_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `custom_searches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custom_searches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_tenant_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_configs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `customer_id` int(10) unsigned NOT NULL,
  `invoice_days_before_renewal` int(11) NOT NULL,
  `auto_debit_days_before_due` int(11) NOT NULL,
  `suspend_services_days_after_due` int(11) NOT NULL,
  `auto_debit_attempts` int(11) NOT NULL,
  `cancel_service_changes_days` int(11) NOT NULL,
  `apply_invoice_late_fees` int(11) NOT NULL,
  `enable_auto_debit` tinyint(1) NOT NULL,
  `set_invoice_method` tinyint(1) NOT NULL,
  `invoice_suspended_services` tinyint(1) NOT NULL,
  `invoice_service_together` tinyint(1) NOT NULL,
  `display_range_date` tinyint(1) NOT NULL,
  `cancel_services` tinyint(1) NOT NULL,
  `synchronize_addons` tinyint(1) NOT NULL,
  `client_create_addons` tinyint(1) NOT NULL,
  `client_change_service_term` tinyint(1) NOT NULL,
  `client_change_service_package` tinyint(1) NOT NULL,
  `client_prorate_credits` tinyint(1) NOT NULL,
  `auto_apply_credits` tinyint(1) NOT NULL,
  `auto_paid_pending_services` tinyint(1) NOT NULL,
  `void_invoice_canceled_service` tinyint(1) NOT NULL,
  `void_invoice_canceled_service_days` int(11) NOT NULL,
  `show_client_tax_id` tinyint(1) NOT NULL,
  `queue_service_changes` tinyint(1) NOT NULL,
  `send_cancellation_notice` tinyint(1) NOT NULL,
  `send_payment_notices` tinyint(1) NOT NULL,
  `notice_1` int(11) NOT NULL,
  `notice_1_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notice_2` int(11) NOT NULL,
  `notice_2_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notice_3` int(11) NOT NULL,
  `notice_3_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auto_debit_pending_notice` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `summary` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `stiky` tinyint(1) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_package_discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_package_discounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_package_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `discount_type` enum('fixed','percentage') COLLATE utf8_unicode_ci NOT NULL,
  `discount` decimal(15,2) NOT NULL,
  `discount_val` bigint(20) unsigned NOT NULL,
  `term_type` enum('D','U') COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `time_unit_number` int(11) DEFAULT NULL,
  `term` enum('days','weeks','months','years') COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_package_discounts_customer_package_id_foreign` (`customer_package_id`),
  CONSTRAINT `customer_package_discounts_customer_package_id_foreign` FOREIGN KEY (`customer_package_id`) REFERENCES `customer_packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_package_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_package_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_package_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `price` bigint(20) unsigned NOT NULL,
  `discount_type` enum('fixed','percentage') COLLATE utf8_unicode_ci NOT NULL,
  `discount` decimal(15,2) NOT NULL,
  `discount_val` bigint(20) unsigned NOT NULL,
  `tax` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `end_period_act` tinyint(1) NOT NULL DEFAULT '0',
  `end_period_number` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `customer_package_items_customer_package_id_foreign` (`customer_package_id`),
  KEY `customer_package_items_item_id_foreign` (`item_id`),
  CONSTRAINT `customer_package_items_customer_package_id_foreign` FOREIGN KEY (`customer_package_id`) REFERENCES `customer_packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_package_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_package_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_package_taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tax_type_id` int(10) unsigned NOT NULL,
  `customer_package_id` int(10) unsigned DEFAULT NULL,
  `customer_package_item_id` bigint(20) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(5,2) NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `compound_tax` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_package_taxes_customer_package_id_foreign` (`customer_package_id`),
  KEY `customer_package_taxes_customer_package_item_id_foreign` (`customer_package_item_id`),
  CONSTRAINT `customer_package_taxes_customer_package_id_foreign` FOREIGN KEY (`customer_package_id`) REFERENCES `customer_packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_package_taxes_customer_package_item_id_foreign` FOREIGN KEY (`customer_package_item_id`) REFERENCES `customer_package_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `package_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_by` enum('N','G','I') COLLATE utf8_unicode_ci NOT NULL,
  `allow_discount` tinyint(1) NOT NULL,
  `discount_by` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `discount_type` enum('fixed','percentage') COLLATE utf8_unicode_ci NOT NULL,
  `discount` decimal(15,2) NOT NULL,
  `discount_val` bigint(20) unsigned NOT NULL,
  `sub_total` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `tax` bigint(20) unsigned NOT NULL,
  `status` enum('A','P','S','C') COLLATE utf8_unicode_ci NOT NULL,
  `term` enum('daily','weekly','monthly','bimonthly','quarterly','biannual','yearly','one time') COLLATE utf8_unicode_ci NOT NULL,
  `activation_date` date DEFAULT NULL,
  `renewal_date` date DEFAULT NULL,
  `service_auto_suspension` tinyint(1) NOT NULL,
  `addresses_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `date_prev` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_packages_customer_id_foreign` (`customer_id`),
  KEY `customer_packages_package_id_foreign` (`package_id`),
  CONSTRAINT `customer_packages_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_packages_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_ticket_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_ticket_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL,
  `item_user_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `summary` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `dep_id` int(10) unsigned NOT NULL,
  `assigned_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `priority` enum('E','C','H','M','L') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'H',
  `status` enum('S','C','I','O','M') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'S',
  `company_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ticket_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `send_notification_customer` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'booleano que indica si se le envia correo de notificacion al cliente',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `departament_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departament_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dep_group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `mailable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mailable_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_logs_company_id_foreign` (`company_id`),
  KEY `email_logs_creator_id_foreign` (`creator_id`),
  CONSTRAINT `email_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `email_logs_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `estimate_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estimate_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount` decimal(15,2) DEFAULT NULL,
  `discount_val` bigint(20) unsigned DEFAULT NULL,
  `price` bigint(20) unsigned NOT NULL,
  `tax` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `estimate_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimate_items_item_id_foreign` (`item_id`),
  KEY `estimate_items_estimate_id_foreign` (`estimate_id`),
  KEY `estimate_items_company_id_foreign` (`company_id`),
  CONSTRAINT `estimate_items_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estimate_items_estimate_id_foreign` FOREIGN KEY (`estimate_id`) REFERENCES `estimates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estimate_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `estimate_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estimate_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `view` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `estimates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estimates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `estimate_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `estimate_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reference_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tax_per_item` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `discount_per_item` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `discount` decimal(15,2) DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount_val` bigint(20) unsigned DEFAULT NULL,
  `sub_total` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `tax` bigint(20) unsigned NOT NULL,
  `unique_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `assigne_user_id` int(10) unsigned DEFAULT NULL,
  `estimate_template_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `template_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimates_user_id_foreign` (`user_id`),
  KEY `estimates_estimate_template_id_foreign` (`estimate_template_id`),
  KEY `estimates_company_id_foreign` (`company_id`),
  KEY `estimates_creator_id_foreign` (`creator_id`),
  CONSTRAINT `estimates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estimates_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estimates_estimate_template_id_foreign` FOREIGN KEY (`estimate_template_id`) REFERENCES `estimate_templates` (`id`),
  CONSTRAINT `estimates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `for_payments` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `expense_categories_company_id_foreign` (`company_id`),
  CONSTRAINT `expense_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expense_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_id` int(10) unsigned NOT NULL,
  `provider_id` int(10) unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subtotal` bigint(20) unsigned NOT NULL,
  `total_tax` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expense_invoices_tax_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_invoices_tax_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `expense_invoice_id` int(10) unsigned NOT NULL,
  `tax_type_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expense_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_templates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `payment_method_id` int(10) unsigned DEFAULT NULL,
  `items_id` int(11) DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `providers_id` int(11) DEFAULT NULL,
  `expense_category_id` int(10) unsigned NOT NULL,
  `notification` tinyint(1) DEFAULT '0',
  `days_after_payment_date` int(11) DEFAULT NULL,
  `initial_status` enum('Active','Pending') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Active',
  `term` enum('daily','weekly','monthly','bimonthly','quarterly','biannual','yearly') COLLATE utf8_unicode_ci NOT NULL,
  `last_date` date DEFAULT NULL,
  `expense_date` date NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Active',
  `template_expense_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(999) COLLATE utf8_unicode_ci NOT NULL,
  `expense_date` date NOT NULL,
  `attachment_receipt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `expense_category_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `providers_id` int(11) DEFAULT NULL,
  `items_id` int(11) DEFAULT NULL,
  `payment_id` int(10) unsigned DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method_id` int(10) unsigned DEFAULT NULL,
  `status` enum('Active','Pending') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Active',
  `notification` tinyint(1) DEFAULT '0',
  `expense_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `store_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  KEY `expenses_company_id_foreign` (`company_id`),
  KEY `expenses_user_id_foreign` (`user_id`),
  KEY `expenses_creator_id_foreign` (`creator_id`),
  CONSTRAINT `expenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_payment_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_payment_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_gateway` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transaction_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'payment',
  `date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `payment_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `error_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `file_disks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `file_disks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'REMOTE',
  `driver` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `set_as_default` tinyint(1) NOT NULL DEFAULT '0',
  `credentials` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `general_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `general_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `setting_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `setting_id` bigint(20) unsigned NOT NULL,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` json DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `general_settings_setting_type_setting_id_index` (`setting_type`,`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `history_call_indi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history_call_indi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `call_detail_register_totals_id` bigint(20) unsigned NOT NULL,
  `amout` double(9,5) unsigned DEFAULT '0.00000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tax_type_id` bigint(20) unsigned DEFAULT NULL,
  `taxamount` double(9,5) unsigned DEFAULT '0.00000',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0 => no impuesto, 1 => Tax cdr, 2 => all taxes, 3 => error',
  `percent` decimal(5,2) DEFAULT '0.00',
  `amoutbruto` double(9,5) unsigned DEFAULT '0.00000',
  `invoice_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `history_call_indi_tax_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history_call_indi_tax_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `percent` decimal(8,2) NOT NULL,
  `compound_tax` tinyint(4) NOT NULL,
  `amount` double(9,5) unsigned DEFAULT '0.00000',
  `tax` double(8,5) unsigned DEFAULT '0.00000',
  `taxable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `taxable_id` bigint(20) unsigned NOT NULL,
  `pbx_services_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `history_call_indi_tax_types_taxable_type_taxable_id_index` (`taxable_type`,`taxable_id`),
  KEY `history_call_indi_tax_types_pbx_services_id_foreign` (`pbx_services_id`),
  CONSTRAINT `history_call_indi_tax_types_pbx_services_id_foreign` FOREIGN KEY (`pbx_services_id`) REFERENCES `pbx_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hold_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hold_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `identification` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `second_phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hold_invoice_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hold_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hold_invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `total` bigint(20) NOT NULL,
  `due_amount` bigint(20) NOT NULL,
  `sub_total` bigint(20) NOT NULL,
  `tax` bigint(20) NOT NULL,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `discount` decimal(8,2) NOT NULL,
  `discount_val` bigint(20) NOT NULL,
  `cash_register_id` bigint(20) unsigned NOT NULL,
  `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tip_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tip` decimal(8,2) DEFAULT NULL,
  `tip_val` bigint(20) DEFAULT NULL,
  `store_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hold_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hold_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` bigint(20) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `unit_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount` decimal(8,2) NOT NULL,
  `discount_val` bigint(20) NOT NULL,
  `tax` bigint(20) NOT NULL,
  `total` bigint(20) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `item_id` bigint(20) unsigned NOT NULL,
  `retentions_id` bigint(20) unsigned DEFAULT NULL,
  `retention_concept` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `retention_percentage` decimal(8,2) DEFAULT NULL,
  `retention_amount` bigint(20) DEFAULT NULL,
  `hold_invoice_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hold_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hold_tables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hold_invoice_id` bigint(20) unsigned NOT NULL,
  `table_id` bigint(20) unsigned NOT NULL,
  `quantity` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `hold_tables_hold_invoice_id_foreign` (`hold_invoice_id`),
  CONSTRAINT `hold_tables_hold_invoice_id_foreign` FOREIGN KEY (`hold_invoice_id`) REFERENCES `hold_invoices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hold_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hold_taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hold_invoice_id` bigint(20) unsigned NOT NULL,
  `tax_type_id` bigint(20) unsigned NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `compound_tax` tinyint(4) NOT NULL DEFAULT '0',
  `percent` decimal(8,2) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `international_prefixrate_destination`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `international_prefixrate_destination` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `int_rate_id` bigint(20) unsigned NOT NULL,
  `prefixrate_id` bigint(20) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `international_rate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `international_rate` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci DEFAULT NULL,
  `rate_per_minute` decimal(20,5) DEFAULT NULL,
  `prefix` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `prefixrate_groups_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `category` enum('T','I','C') COLLATE utf8_unicode_ci DEFAULT NULL,
  `from` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `typecustom` enum('P','FT') COLLATE utf8_unicode_ci DEFAULT 'P',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `international_rate_prefixrate_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `international_rate_prefixrate_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL DEFAULT '0',
  `int_rate_id` bigint(20) unsigned NOT NULL,
  `prefixrate_id` bigint(20) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_additional_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_additional_charges` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `additional_charge_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `additional_charge_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `additional_charge_amount` decimal(8,2) NOT NULL,
  `template_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `additional_charge_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `qty` int(11) NOT NULL,
  `total` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `profile_extension_id` int(10) unsigned DEFAULT NULL,
  `profile_did_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_app_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_app_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `pbx_package_id` int(10) unsigned NOT NULL,
  `pbx_service_id` int(10) unsigned NOT NULL,
  `invoice_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_customer_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_customer_packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `package_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `customer_id` int(11) NOT NULL,
  `status` enum('A','D') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_dids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_dids` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `pbx_did_id` int(10) unsigned DEFAULT NULL,
  `template_did_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `pbx_did_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_did_server` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_did_trunk` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_did_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_did_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_did_rate` decimal(8,2) DEFAULT '0.00',
  `custom_did_id` bigint(20) unsigned DEFAULT NULL,
  `custom_did_rate` decimal(8,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name_prefix` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_extensions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `pbx_extension_id` int(10) unsigned NOT NULL,
  `template_extension_id` int(10) unsigned DEFAULT NULL,
  `pbx_extension_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_extension_ext` int(10) unsigned DEFAULT NULL,
  `pbx_extension_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_extension_ua_fullname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_extension_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template_extension_rate` decimal(8,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount` decimal(15,2) DEFAULT NULL,
  `discount_val` bigint(20) unsigned NOT NULL,
  `tax` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `invoice_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `retention_id` bigint(20) unsigned DEFAULT NULL,
  `retention_concept` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `retention_percentage` decimal(15,2) DEFAULT NULL,
  `retention_amount` bigint(20) unsigned NOT NULL DEFAULT '0',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_items_item_id_foreign` (`item_id`),
  KEY `invoice_items_company_id_foreign` (`company_id`),
  CONSTRAINT `invoice_items_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_late_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_late_fees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `type` tinyint(3) unsigned NOT NULL,
  `notice` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subtotal` bigint(20) unsigned NOT NULL,
  `tax_amount` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `invoice_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_pbx_did_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_pbx_did_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_pbx_extension_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_pbx_extension_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `view` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_bool` tinyint(1) DEFAULT NULL,
  `customer_packages_id` int(11) DEFAULT NULL,
  `pbx_service_id` int(10) unsigned DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `reference_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paid_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `save_as_draft` tinyint(1) NOT NULL DEFAULT '0',
  `not_charge_automatically` tinyint(1) NOT NULL DEFAULT '0',
  `tax_per_item` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `discount_per_item` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount` decimal(15,2) DEFAULT NULL,
  `discount_val` bigint(20) unsigned DEFAULT NULL,
  `sub_total` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `late_fee_amount` bigint(20) unsigned NOT NULL DEFAULT '0',
  `late_fee_taxes` bigint(20) unsigned NOT NULL DEFAULT '0',
  `pbx_service_price` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tax` bigint(20) unsigned NOT NULL,
  `due_amount` bigint(20) unsigned NOT NULL,
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  `unique_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `invoice_template_id` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `template_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `pbx_total_items` bigint(20) unsigned NOT NULL DEFAULT '0',
  `pbx_total_extension` bigint(20) unsigned NOT NULL DEFAULT '0',
  `pbx_total_did` bigint(20) unsigned NOT NULL DEFAULT '0',
  `pbx_total_aditional_charges` bigint(20) unsigned NOT NULL DEFAULT '0',
  `pbx_total_cdr` bigint(20) unsigned NOT NULL DEFAULT '0',
  `avalara_total_tax` bigint(20) unsigned DEFAULT NULL,
  `inv_avalara_bool` tinyint(1) DEFAULT '0',
  `avalara_invm` tinyint(1) DEFAULT NULL COMMENT 'invoice_mode',
  `pbxservice_date_prev` date DEFAULT NULL,
  `pbxservice_date_renewal` date DEFAULT NULL,
  `end_period_services` date DEFAULT NULL,
  `attempts` int(11) NOT NULL DEFAULT '0',
  `autodebit_notices_check` int(11) NOT NULL DEFAULT '0',
  `sended_credentials` int(11) NOT NULL DEFAULT '0',
  `invoice_pbx_modify` tinyint(1) NOT NULL DEFAULT '0',
  `invoiceprorate` int(11) DEFAULT '0',
  `prepaid_amount` decimal(15,5) NOT NULL,
  `tax_prepaid_amount` decimal(15,5) NOT NULL,
  `pbx_total_apprate` bigint(20) unsigned NOT NULL DEFAULT '0',
  `retention_total` bigint(20) unsigned NOT NULL DEFAULT '0',
  `retention` enum('YES','NO') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'NO',
  `pbx_packprice` bigint(20) unsigned NOT NULL DEFAULT '0',
  `noeditable` int(10) unsigned DEFAULT '0',
  `addresses_id` int(10) unsigned DEFAULT NULL,
  `pbx_extension_price` double(8,2) DEFAULT '0.00',
  `count_extension` int(10) unsigned DEFAULT '0',
  `count_did` int(10) unsigned DEFAULT '0',
  `edited_at` timestamp NULL DEFAULT NULL,
  `is_edited` tinyint(1) NOT NULL DEFAULT '0',
  `is_pdf_pos` tinyint(1) DEFAULT '0',
  `is_invoice_pos` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `invoices_invoice_template_id_foreign` (`invoice_template_id`),
  KEY `invoices_user_id_foreign` (`user_id`),
  KEY `invoices_company_id_foreign` (`company_id`),
  KEY `invoices_creator_id_foreign` (`creator_id`),
  CONSTRAINT `invoices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_invoice_template_id_foreign` FOREIGN KEY (`invoice_template_id`) REFERENCES `invoice_templates` (`id`),
  CONSTRAINT `invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoices_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices_tables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity_persons` bigint(20) unsigned NOT NULL,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_group` tinyint(1) NOT NULL DEFAULT '0',
  `is_item` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_group_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_group_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_group_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_group_items_item_group_id_foreign` (`item_group_id`),
  KEY `item_group_items_item_id_foreign` (`item_id`),
  KEY `item_group_items_company_id_foreign` (`company_id`),
  CONSTRAINT `item_group_items_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_group_items_item_group_id_foreign` FOREIGN KEY (`item_group_id`) REFERENCES `item_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_group_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_group_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_group_store` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_group_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `no_taxable` tinyint(4) NOT NULL DEFAULT '0',
  `company_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `item_category_id` int(10) unsigned DEFAULT NULL,
  `allow_pos` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `item_groups_company_id_foreign` (`company_id`),
  CONSTRAINT `item_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_groups_item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_groups_item_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_group_id` int(10) unsigned NOT NULL,
  `item_category_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_section` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `item_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_store` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL,
  `store_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `price` bigint(20) unsigned NOT NULL,
  `allow_taxes` tinyint(1) DEFAULT NULL,
  `no_taxable` tinyint(1) DEFAULT NULL,
  `avalara_bool` tinyint(1) DEFAULT NULL,
  `avalara_type` enum('0','1','2','3','4','5','6','7','8','9','10','11','13','14','15','16','18','19','20','21','24','25','32','34','36','42','44','47','48','50','57','58','59','60','61','64','65','66') COLLATE utf8_unicode_ci DEFAULT NULL,
  `avalara_service_type` int(11) DEFAULT NULL,
  `avalara_payment_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `retentions_bool` tinyint(1) NOT NULL DEFAULT '0',
  `retentions_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `item_number` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `item_category_id` int(10) unsigned DEFAULT NULL,
  `allow_pos` tinyint(1) NOT NULL DEFAULT '0',
  `avalara_sale_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Retail',
  `avalara_discount_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `avalara_tax_inclusion` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `items_unit_id_foreign` (`unit_id`),
  KEY `items_company_id_foreign` (`company_id`),
  KEY `items_creator_id_foreign` (`creator_id`),
  CONSTRAINT `items_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `items_item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items_item_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_category_id` int(10) unsigned NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` text COLLATE utf8_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lead_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` text COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `creator_id` int(11) NOT NULL,
  `lead_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `leadnote_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_type` enum('N','B','R') COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('A','C') COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `primary_contact_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `last_contacted_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `logsmodule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logsmodule` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `task` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `task_id` int(10) unsigned DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `useremail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `deletemessage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `collection_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `manipulations` text COLLATE utf8_unicode_ci NOT NULL,
  `custom_properties` text COLLATE utf8_unicode_ci NOT NULL,
  `responsive_images` text COLLATE utf8_unicode_ci NOT NULL,
  `order_column` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `uuid` char(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `conversions_disk` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mobile_login_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mobile_login_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `session_start` timestamp NULL DEFAULT NULL,
  `firebase_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `system_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `system_version` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_tablet` tinyint(1) DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unique_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manufacturer` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_level` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mac_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mobile_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mobile_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `color_palette` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dark_color_palette` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vertical_menu` json DEFAULT NULL,
  `horizontal_menu` json DEFAULT NULL,
  `dashboard` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `firebase_token_notification` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `slug` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modules_company_id_foreign` (`company_id`),
  KEY `modules_creator_id_foreign` (`creator_id`),
  CONSTRAINT `modules_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `modules_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `note_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `note_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `customer_ticket_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `public` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notes` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_descriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_descriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `html` mediumtext COLLATE utf8_unicode_ci,
  `text` mediumtext COLLATE utf8_unicode_ci,
  `lang` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_descriptions_package_id_foreign` (`package_id`),
  KEY `package_descriptions_company_id_foreign` (`company_id`),
  CONSTRAINT `package_descriptions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_descriptions_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_groups_id` int(10) unsigned NOT NULL,
  `packages_id` int(10) unsigned NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci DEFAULT NULL,
  `order` smallint(6) NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_group_package_groups_id_foreign` (`package_groups_id`),
  KEY `package_group_packages_id_foreign` (`packages_id`),
  KEY `package_group_company_id_foreign` (`company_id`),
  CONSTRAINT `package_group_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `package_group_package_groups_id_foreign` FOREIGN KEY (`package_groups_id`) REFERENCES `package_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_group_packages_id_foreign` FOREIGN KEY (`packages_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `allow_upgrades` tinyint(4) NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_groups_company_id_foreign` (`company_id`),
  CONSTRAINT `package_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_item_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_item_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `item_group_id` int(10) unsigned NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_item_groups_package_id_foreign` (`package_id`),
  KEY `package_item_groups_item_group_id_foreign` (`item_group_id`),
  KEY `package_item_groups_company_id_foreign` (`company_id`),
  CONSTRAINT `package_item_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_item_groups_item_group_id_foreign` FOREIGN KEY (`item_group_id`) REFERENCES `item_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_item_groups_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `item_group_id` int(11) DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT NULL,
  `discount_val` bigint(20) unsigned DEFAULT NULL,
  `price` bigint(20) unsigned NOT NULL,
  `tax` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `items_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `end_period_act` tinyint(1) NOT NULL DEFAULT '0',
  `end_period_number` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `package_items_package_id_foreign` (`package_id`),
  KEY `package_items_items_id_foreign` (`items_id`),
  KEY `package_items_company_id_foreign` (`company_id`),
  CONSTRAINT `package_items_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_items_items_id_foreign` FOREIGN KEY (`items_id`) REFERENCES `items` (`id`),
  CONSTRAINT `package_items_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_names` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `lang` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `package_names_name_unique` (`name`),
  KEY `package_names_package_id_foreign` (`package_id`),
  KEY `package_names_company_id_foreign` (`company_id`),
  CONSTRAINT `package_names_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_names_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_tax_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_tax_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned DEFAULT NULL,
  `pbx_package_id` int(10) unsigned DEFAULT NULL,
  `tax_group_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'A',
  PRIMARY KEY (`id`),
  KEY `package_tax_groups_package_id_foreign` (`package_id`),
  KEY `package_tax_groups_tax_group_id_foreign` (`tax_group_id`),
  KEY `package_tax_groups_pbx_package_id_foreign` (`pbx_package_id`),
  CONSTRAINT `package_tax_groups_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`),
  CONSTRAINT `package_tax_groups_pbx_package_id_foreign` FOREIGN KEY (`pbx_package_id`) REFERENCES `pbx_packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_tax_groups_tax_group_id_foreign` FOREIGN KEY (`tax_group_id`) REFERENCES `tax_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `package_tax_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `package_tax_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` int(10) unsigned NOT NULL,
  `tax_types_id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(5,2) NOT NULL,
  `compound_tax` tinyint(4) NOT NULL DEFAULT '0',
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_tax_types_package_id_foreign` (`package_id`),
  KEY `package_tax_types_tax_types_id_foreign` (`tax_types_id`),
  KEY `package_tax_types_company_id_foreign` (`company_id`),
  CONSTRAINT `package_tax_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `package_tax_types_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`),
  CONSTRAINT `package_tax_types_tax_types_id_foreign` FOREIGN KEY (`tax_types_id`) REFERENCES `tax_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `package_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `html` mediumtext COLLATE utf8_unicode_ci,
  `apply_tax_type` enum('none','item','general') COLLATE utf8_unicode_ci DEFAULT 'general',
  `discount_general_type` enum('percentage','fixed') COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount_general` decimal(15,2) DEFAULT NULL,
  `packages_discount` tinyint(1) NOT NULL DEFAULT '0',
  `packages_discount_none` tinyint(1) DEFAULT '0',
  `text` mediumtext COLLATE utf8_unicode_ci,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `client_qty` int(11) DEFAULT NULL,
  `taxable` int(11) DEFAULT NULL,
  `single_term` int(11) DEFAULT NULL,
  `prorata_day` tinyint(4) DEFAULT NULL,
  `prorata_cuoff` tinyint(4) DEFAULT NULL,
  `upgrades_use_renewal` tinyint(4) DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `status_payment` enum('R','O') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'R',
  PRIMARY KEY (`id`),
  UNIQUE KEY `packages_name_unique` (`name`),
  UNIQUE KEY `packages_package_number_unique` (`package_number`),
  KEY `packages_company_id_foreign` (`company_id`),
  CONSTRAINT `packages_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(11) NOT NULL,
  `state_id` int(11) NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address_1` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `address_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_account_type` enum('CC','ACH') COLLATE utf8_unicode_ci NOT NULL,
  `card_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `credit_card` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cvv` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expiration_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ACH_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `routing_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_check` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `client_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `main_account` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_devolutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_devolutions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `transaction_id` int(10) unsigned DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8_unicode_ci,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_gateways`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_gateways` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` enum('Authorize','Paypal','AuxVault','Stripe') COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `default` tinyint(1) NOT NULL,
  `slug` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_img` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_gateways_company_id_foreign` (`company_id`),
  KEY `payment_gateways_creator_id_foreign` (`creator_id`),
  CONSTRAINT `payment_gateways_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_gateways_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_gateways_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_gateways_fees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` bigint(20) unsigned NOT NULL DEFAULT '0',
  `payment_gateway` enum('Authorize','Paypal','AuxVault','Stripe') COLLATE utf8_unicode_ci NOT NULL,
  `authorize_setting_id` bigint(20) unsigned DEFAULT NULL,
  `aux_vault_setting_id` bigint(20) unsigned DEFAULT NULL,
  `paypal_settings_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `only_cash` tinyint(4) NOT NULL DEFAULT '0',
  `add_payment_gateway` tinyint(1) DEFAULT '0',
  `paypal_button` tinyint(4) NOT NULL DEFAULT '0',
  `stripe_button` tinyint(1) NOT NULL DEFAULT '0',
  `company_id` int(10) unsigned DEFAULT NULL,
  `account_accepted` enum('N','A','C') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `payment_gateways_id` int(10) unsigned DEFAULT NULL,
  `for_customer_use` tinyint(1) NOT NULL,
  `generate_expense` tinyint(1) NOT NULL DEFAULT '0',
  `void_refund` tinyint(1) DEFAULT NULL,
  `generate_expense_id` int(10) unsigned DEFAULT NULL,
  `void_refund_expense_id` int(10) unsigned DEFAULT NULL,
  `expense_import` tinyint(1) NOT NULL DEFAULT '0',
  `is_multiple` tinyint(1) DEFAULT '0',
  `show_notes_table` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `payment_methods_company_id_foreign` (`company_id`),
  KEY `payment_methods_generate_expense_id_foreign` (`generate_expense_id`),
  KEY `payment_methods_void_refund_expense_id_foreign` (`void_refund_expense_id`),
  CONSTRAINT `payment_methods_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_methods_generate_expense_id_foreign` FOREIGN KEY (`generate_expense_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_methods_void_refund_expense_id_foreign` FOREIGN KEY (`void_refund_expense_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payment_payment_gateways_fee`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_payment_gateways_fee` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned NOT NULL,
  `payment_gateways_fee_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL COMMENT 'amount es el monto de la tabla payment gateways fees',
  `total` bigint(20) unsigned NOT NULL COMMENT 'total es lo que representa el amount respecto al payment',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payment_date` date NOT NULL,
  `notes` text COLLATE utf8_unicode_ci,
  `amount` bigint(20) unsigned NOT NULL,
  `unique_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `payment_method_id` int(10) unsigned DEFAULT NULL,
  `authorize_id` int(10) unsigned DEFAULT NULL,
  `aux_vault_id` bigint(20) unsigned DEFAULT NULL,
  `payment_paypal_id` int(11) DEFAULT NULL,
  `credit_card` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `transaction_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Approved',
  `payment_prepaid` tinyint(1) NOT NULL DEFAULT '0',
  `inv_expense_credit` tinyint(1) NOT NULL DEFAULT '0',
  `applied_credit_customer` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_invoice_id_foreign` (`invoice_id`),
  KEY `payments_company_id_foreign` (`company_id`),
  KEY `payments_payment_method_id_foreign` (`payment_method_id`),
  KEY `payments_creator_id_foreign` (`creator_id`),
  KEY `payments_aux_vault_id_foreign` (`aux_vault_id`),
  CONSTRAINT `payments_aux_vault_id_foreign` FOREIGN KEY (`aux_vault_id`) REFERENCES `aux_vaults` (`id`),
  CONSTRAINT `payments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments_payment_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(10) unsigned NOT NULL,
  `payment_method_id` int(10) unsigned NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `received` bigint(20) unsigned NOT NULL,
  `returned` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payments_paypals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments_paypals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double(10,2) DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `card_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `card_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `create_time` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `paypal_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paypal_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paypal_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paypal_secret` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `paypal_signature` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `currency` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `test_mode` tinyint(4) NOT NULL,
  `developer_mode` tinyint(4) NOT NULL,
  `creator_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `merchant_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `public_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `private_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enviroment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sandbox',
  `enable_identification_verification` tinyint(4) DEFAULT '0',
  `enable_fee_charges` tinyint(4) DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_additional_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_additional_charges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `profile_extension_id` int(10) unsigned DEFAULT NULL,
  `profile_did_id` int(10) unsigned DEFAULT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `pbx_service_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `additional_charge_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_cdr_tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_cdr_tenants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pbx_server_id` int(10) unsigned NOT NULL,
  `tenantid` smallint(5) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `date_begin` date DEFAULT NULL,
  `last_update` datetime DEFAULT NULL,
  `job_active_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pbx_cdr_tenants_pbx_server_id_foreign` (`pbx_server_id`),
  CONSTRAINT `pbx_cdr_tenants_pbx_server_id_foreign` FOREIGN KEY (`pbx_server_id`) REFERENCES `pbx_servers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_cdrs_1_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_cdrs_1_1` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` int(10) unsigned NOT NULL,
  `duration` int(10) unsigned NOT NULL,
  `billing_duration` int(10) unsigned DEFAULT NULL,
  `round_duration` int(10) unsigned DEFAULT NULL,
  `cost` double(9,5) unsigned DEFAULT NULL,
  `exclusive_seconds` int(10) unsigned NOT NULL DEFAULT '0',
  `exclusive_cost` double(9,5) unsigned DEFAULT '0.00000',
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `unique_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT 'inbound(0) / outbound(1)',
  `trunk_id` int(11) DEFAULT NULL,
  `billed_at` date DEFAULT NULL,
  `pbx_did_id` int(10) unsigned DEFAULT NULL,
  `pbx_extension_id` int(10) unsigned DEFAULT NULL,
  `international_rate_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pbx_cdrs_1_1_pbx_did_id_foreign` (`pbx_did_id`),
  KEY `pbx_cdrs_1_1_pbx_extension_id_foreign` (`pbx_extension_id`),
  KEY `pbx_cdrs_1_1_international_rate_id_foreign` (`international_rate_id`),
  KEY `pbx_cdrs_1_1_unique_id_start_date_index` (`unique_id`,`start_date`),
  CONSTRAINT `pbx_cdrs_1_1_international_rate_id_foreign` FOREIGN KEY (`international_rate_id`) REFERENCES `international_rate` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pbx_cdrs_1_1_pbx_did_id_foreign` FOREIGN KEY (`pbx_did_id`) REFERENCES `pbx_did` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pbx_cdrs_1_1_pbx_extension_id_foreign` FOREIGN KEY (`pbx_extension_id`) REFERENCES `pbx_extensions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_cdrs_4_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_cdrs_4_1` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` int(10) unsigned NOT NULL,
  `duration` int(10) unsigned NOT NULL,
  `billing_duration` int(10) unsigned DEFAULT NULL,
  `round_duration` int(10) unsigned DEFAULT NULL,
  `cost` double(9,5) unsigned DEFAULT NULL,
  `exclusive_seconds` int(10) unsigned NOT NULL DEFAULT '0',
  `exclusive_cost` double(9,5) unsigned DEFAULT '0.00000',
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `unique_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT 'inbound(0) / outbound(1)',
  `trunk_id` int(11) DEFAULT NULL,
  `billed_at` date DEFAULT NULL,
  `pbx_did_id` int(10) unsigned DEFAULT NULL,
  `pbx_extension_id` int(10) unsigned DEFAULT NULL,
  `international_rate_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pbx_cdrs_4_1_pbx_did_id_foreign` (`pbx_did_id`),
  KEY `pbx_cdrs_4_1_pbx_extension_id_foreign` (`pbx_extension_id`),
  KEY `pbx_cdrs_4_1_international_rate_id_foreign` (`international_rate_id`),
  KEY `pbx_cdrs_4_1_unique_id_start_date_index` (`unique_id`,`start_date`),
  CONSTRAINT `pbx_cdrs_4_1_international_rate_id_foreign` FOREIGN KEY (`international_rate_id`) REFERENCES `international_rate` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pbx_cdrs_4_1_pbx_did_id_foreign` FOREIGN KEY (`pbx_did_id`) REFERENCES `pbx_did` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pbx_cdrs_4_1_pbx_extension_id_foreign` FOREIGN KEY (`pbx_extension_id`) REFERENCES `pbx_extensions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_did`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_did` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `didid` smallint(5) unsigned DEFAULT NULL COMMENT 'Value from pbxSystem, required for the api',
  `server` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_tenant_id` int(10) unsigned DEFAULT NULL,
  `trunk` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ext` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `e164_2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `e164` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `cost_per_day` bigint(20) DEFAULT NULL,
  `prorate` bigint(20) DEFAULT NULL,
  `date_prorate` date DEFAULT NULL,
  `pbxdid_id` int(11) DEFAULT NULL,
  `pbx_tenant_code` int(11) DEFAULT NULL,
  `pbx_server_id` int(11) DEFAULT NULL,
  `deleted_in_server` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_extension_custom_searches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_extension_custom_searches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `custom_search_id` bigint(20) unsigned NOT NULL,
  `pbx_extension_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_extensions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ext` int(10) unsigned DEFAULT NULL,
  `extensionid` smallint(5) unsigned DEFAULT NULL COMMENT 'Value from pbxSystem, required for the api',
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `pbx_tenant_id` int(10) unsigned DEFAULT NULL,
  `api_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `linenum` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auto_provisioning` tinyint(1) DEFAULT NULL,
  `macaddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `protocol` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ua_fullname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ua_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ua_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cost_per_day` bigint(20) DEFAULT NULL,
  `prorate` bigint(20) DEFAULT NULL,
  `date_prorate` date DEFAULT NULL,
  `pbxext_id` int(11) DEFAULT NULL,
  `pbx_tenant_code` int(11) DEFAULT NULL,
  `pbx_server_id` int(11) DEFAULT NULL,
  `deleted_in_server` tinyint(1) NOT NULL DEFAULT '0',
  `dhcp` int(11) DEFAULT '1',
  `static_ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time_zone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_job_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_job_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'job name',
  `lvl` tinyint(3) unsigned NOT NULL COMMENT '0 => debug, 1 => info, 2 => warning, 3 => error',
  `response` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'job response, usually error msg',
  `data` json NOT NULL COMMENT 'extra data concerning the job',
  `note` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pbx_service_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pbx_job_logs_pbx_service_id_foreign` (`pbx_service_id`),
  CONSTRAINT `pbx_job_logs_pbx_service_id_foreign` FOREIGN KEY (`pbx_service_id`) REFERENCES `pbx_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_package_item_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_package_item_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pbx_package_id` int(10) unsigned NOT NULL,
  `item_group_id` int(10) unsigned NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_package_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_package_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pbx_package_id` int(10) unsigned NOT NULL,
  `item_group_id` int(11) DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT NULL,
  `discount_val` bigint(20) unsigned DEFAULT NULL,
  `price` bigint(20) unsigned NOT NULL,
  `tax` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `items_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `end_period_act` tinyint(1) NOT NULL DEFAULT '0',
  `end_period_number` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_package_tax_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_package_tax_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `pbx_package_id` int(10) unsigned DEFAULT NULL,
  `tax_types_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(8,2) NOT NULL,
  `compound_tax` int(11) NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_package_tax_types_cdrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_package_tax_types_cdrs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `pbx_package_id` int(10) unsigned DEFAULT NULL,
  `tax_types_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(8,2) NOT NULL,
  `compound_tax` int(11) NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_packages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `taxes_id` int(10) unsigned DEFAULT NULL,
  `package_tax_groups_id` int(10) unsigned DEFAULT NULL,
  `item_group_id` int(10) unsigned DEFAULT NULL,
  `apply_tax_type` enum('item','general') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'general',
  `pbx_package_name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `html` mediumtext COLLATE utf8_unicode_ci,
  `text` mediumtext COLLATE utf8_unicode_ci,
  `status` enum('A','I','R') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `qty_available` int(11) DEFAULT '0',
  `client_limit` int(11) DEFAULT '0',
  `extensions` tinyint(1) NOT NULL,
  `did` tinyint(1) NOT NULL,
  `call_ratings` tinyint(1) NOT NULL,
  `package_discount` tinyint(1) NOT NULL,
  `type` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `discount` int(11) NOT NULL,
  `modify_server` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `pbx_server_id` int(10) unsigned DEFAULT NULL,
  `rate` double(8,2) NOT NULL DEFAULT '0.00',
  `national_dialing_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `international_dialing_code` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inclusive_minutes` int(11) DEFAULT '0',
  `international_inclusive_minutes` int(11) DEFAULT '0',
  `local_inclusive_minutes` int(11) DEFAULT '0',
  `status_payment` enum('prepaid','postpaid') COLLATE utf8_unicode_ci NOT NULL,
  `type_time_increment` enum('sec','min') COLLATE utf8_unicode_ci NOT NULL,
  `minutes_increments` int(11) NOT NULL DEFAULT '1',
  `rate_per_minutes` decimal(20,5) DEFAULT '0.00000',
  `value_discount` double NOT NULL DEFAULT '0',
  `packages_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unmetered` tinyint(1) NOT NULL,
  `template_did_id` int(11) DEFAULT NULL,
  `template_extension_id` int(11) DEFAULT NULL,
  `discount_term_type` enum('D','U') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'D',
  `discount_start_date` date DEFAULT NULL,
  `discount_end_date` date DEFAULT NULL,
  `discount_time_units` int(11) DEFAULT NULL,
  `discount_term` enum('days','weeks','months','years') COLLATE utf8_unicode_ci DEFAULT NULL,
  `inclusive_minutes_seconds` int(10) unsigned NOT NULL DEFAULT '0',
  `prefixrate_groups_id` int(10) unsigned DEFAULT NULL,
  `automatic_suspension` tinyint(1) NOT NULL,
  `prefixrate_groups_outbound_id` int(10) unsigned DEFAULT NULL,
  `avalara_options` tinyint(1) NOT NULL DEFAULT '0',
  `avalara_price` tinyint(1) NOT NULL DEFAULT '1',
  `avalara_items` tinyint(1) NOT NULL DEFAULT '0',
  `avalara_extension` tinyint(1) NOT NULL DEFAULT '0',
  `avalara_did` tinyint(1) NOT NULL DEFAULT '0',
  `avalara_callrating` tinyint(1) NOT NULL DEFAULT '0',
  `avalara_custom_app_rate_items` tinyint(1) NOT NULL DEFAULT '0',
  `all_cdrs` tinyint(1) DEFAULT NULL,
  `custom_app_rate_id` bigint(20) unsigned DEFAULT NULL,
  `custom_destinations_item_id` bigint(20) unsigned DEFAULT NULL,
  `inter_custom_destinations_item_id` int(10) unsigned DEFAULT NULL,
  `toll_free_custom_destinations_item_id` int(10) unsigned DEFAULT NULL,
  `cdr_items_id` bigint(20) unsigned DEFAULT NULL,
  `avalara_did_item_id` bigint(20) unsigned DEFAULT NULL,
  `avalara_extension_item_id` bigint(20) unsigned DEFAULT NULL,
  `avalara_custom_app_rate_item_id` bigint(20) unsigned DEFAULT NULL,
  `avalara_services_price_item` tinyint(1) NOT NULL DEFAULT '0',
  `avalara_additional_charges_item` tinyint(1) NOT NULL DEFAULT '0',
  `avalara_services_price_item_id` bigint(20) unsigned DEFAULT NULL,
  `avalara_additional_charges_item_id` bigint(20) unsigned DEFAULT NULL,
  `suspension_type` enum('T','E') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'T',
  `avalaraBundle` tinyint(1) NOT NULL DEFAULT '0',
  `bundleTransaction` int(11) NOT NULL DEFAULT '0',
  `bundleService` int(11) NOT NULL DEFAULT '0',
  `update_child_services` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pbx_packages_pbx_server_id_foreign` (`pbx_server_id`),
  KEY `pbx_packages_custom_app_rate_id_foreign` (`custom_app_rate_id`),
  CONSTRAINT `pbx_packages_custom_app_rate_id_foreign` FOREIGN KEY (`custom_app_rate_id`) REFERENCES `custom_app_rates` (`id`),
  CONSTRAINT `pbx_packages_pbx_server_id_foreign` FOREIGN KEY (`pbx_server_id`) REFERENCES `pbx_servers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_packages_cdr_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_packages_cdr_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pbx_packages_id` int(10) unsigned NOT NULL,
  `status` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_packages_prefixrate_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_packages_prefixrate_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pbx_package_id` int(10) unsigned NOT NULL,
  `prefixrate_group_id` int(10) unsigned NOT NULL,
  `type` enum('Inbound','Outbound') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_server_cdr_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_server_cdr_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pbx_servers_id` int(10) unsigned NOT NULL,
  `status` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_server_tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_server_tenants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `tenant_code` smallint(5) unsigned NOT NULL,
  `tenant_id` smallint(5) unsigned DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pbx_server_id` bigint(20) unsigned NOT NULL,
  `creator_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_servers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `server_label` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `hostname` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `ssl_port` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `api_key` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `national_dialing_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `international_dialing_code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `pbx_package_id` int(10) unsigned DEFAULT NULL,
  `pbx_tenant_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','P','S','C') COLLATE utf8_unicode_ci DEFAULT NULL,
  `apply_tax_type` enum('G','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'G',
  `term` enum('daily','weekly','monthly','bimonthly','quarterly','biannual','yearly') COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_begin` date DEFAULT NULL,
  `allow_discount` tinyint(1) NOT NULL,
  `auto_suspension` tinyint(1) NOT NULL,
  `only_callrating` tinyint(1) NOT NULL DEFAULT '0',
  `allow_discount_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `discount_term_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `allow_discount_type` enum('fixed','percentage') COLLATE utf8_unicode_ci NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `time_period` int(11) DEFAULT NULL,
  `time_period_value` enum('Days','Weeks','Months','Years') COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `addresses_id` int(10) unsigned DEFAULT NULL,
  `renewal_date` date DEFAULT NULL,
  `pbx_services_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbxpackages_price` bigint(20) unsigned DEFAULT NULL,
  `discount_val` bigint(20) unsigned DEFAULT NULL,
  `sub_total` bigint(20) unsigned DEFAULT NULL,
  `total` bigint(20) unsigned DEFAULT NULL,
  `tax` bigint(20) unsigned DEFAULT NULL,
  `call_rating_preview` bigint(20) unsigned DEFAULT NULL,
  `inclusive_minutes_seconds_consumed` int(10) unsigned NOT NULL DEFAULT '0',
  `time_period_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_prev` date DEFAULT NULL,
  `prefixrate_groups_id` int(10) unsigned DEFAULT NULL,
  `prefixrate_groups_outbound_id` int(10) unsigned DEFAULT NULL,
  `allow_pbxpackages` tinyint(1) NOT NULL DEFAULT '0',
  `allow_items` tinyint(1) NOT NULL DEFAULT '0',
  `allow_extensions` tinyint(1) NOT NULL DEFAULT '0',
  `allow_did` tinyint(1) NOT NULL DEFAULT '0',
  `allow_aditionalcharges` tinyint(1) NOT NULL DEFAULT '0',
  `allow_usagesummary` tinyint(1) NOT NULL DEFAULT '0',
  `cap_extension` bigint(20) unsigned DEFAULT NULL,
  `cap_total` bigint(20) unsigned DEFAULT NULL,
  `is_importing` tinyint(1) NOT NULL DEFAULT '0',
  `is_calculating` tinyint(1) NOT NULL DEFAULT '0',
  `total_prorate` bigint(20) NOT NULL DEFAULT '0',
  `tax_type_id` int(10) unsigned DEFAULT NULL,
  `suspension_type` enum('T','E') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'T',
  `custom_app_rate_id` bigint(20) unsigned DEFAULT NULL,
  `allow_customapp` tinyint(1) NOT NULL DEFAULT '0',
  `main_update` tinyint(1) NOT NULL DEFAULT '0',
  `allow_pbx_packages_update` tinyint(1) NOT NULL DEFAULT '0',
  `first_time_import` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `pbx_services_custom_app_rate_id_foreign` (`custom_app_rate_id`),
  CONSTRAINT `pbx_services_custom_app_rate_id_foreign` FOREIGN KEY (`custom_app_rate_id`) REFERENCES `custom_app_rates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services_app_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services_app_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `pbx_package_id` int(10) unsigned NOT NULL,
  `pbx_service_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned NOT NULL,
  `count_extension` int(10) unsigned NOT NULL,
  `count_did` int(10) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `price_did` decimal(15,2) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services_did`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services_did` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `pbx_service_id` int(10) unsigned DEFAULT NULL,
  `pbx_did_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `custom_did_id` int(10) unsigned DEFAULT NULL,
  `cost_per_day` bigint(20) DEFAULT NULL,
  `prorate` bigint(20) DEFAULT NULL,
  `date_prorate` date DEFAULT NULL,
  `invoiced_prorate` tinyint(1) NOT NULL DEFAULT '0',
  `old_prorate` bigint(20) DEFAULT NULL,
  `old_date_prorate` date DEFAULT NULL,
  `name_prefix` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services_extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services_extensions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `pbx_service_id` int(10) unsigned DEFAULT NULL,
  `pbx_extension_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `cost_per_day` bigint(20) DEFAULT NULL,
  `prorate` bigint(20) DEFAULT NULL,
  `date_prorate` date DEFAULT NULL,
  `old_prorate` bigint(20) DEFAULT NULL,
  `old_date_prorate` date DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `invoiced_prorate` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pbx_services_id` int(10) unsigned NOT NULL,
  `item_group_id` int(10) unsigned NOT NULL,
  `items_id` int(11) DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `discount_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) DEFAULT NULL,
  `discount_val` bigint(20) unsigned DEFAULT NULL,
  `price` bigint(20) unsigned NOT NULL,
  `tax` bigint(20) unsigned NOT NULL,
  `total` bigint(20) unsigned NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `end_period_act` tinyint(1) NOT NULL DEFAULT '0',
  `end_period_number` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services_prefixrate_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services_prefixrate_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pbx_service_id` int(10) unsigned NOT NULL,
  `prefixrate_group_id` int(10) unsigned NOT NULL,
  `type` enum('Inbound','Outbound') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services_tax_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services_tax_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `pbx_services_id` int(10) unsigned DEFAULT NULL,
  `tax_types_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(8,2) NOT NULL,
  `compound_tax` tinyint(4) NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `amount` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_services_tax_types_cdr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_services_tax_types_cdr` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `pbx_services_id` int(10) unsigned DEFAULT NULL,
  `tax_types_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(8,2) NOT NULL,
  `compound_tax` tinyint(4) NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_tenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_tenant` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tenantid` smallint(5) unsigned DEFAULT NULL COMMENT 'Value from pbxSystem, required for the api',
  `config` json DEFAULT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `details` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `pbx_server_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pbx_tenant_cdrs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pbx_tenant_cdrs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `to` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` int(10) unsigned NOT NULL,
  `duration` int(10) unsigned NOT NULL,
  `billing_duration` int(10) unsigned DEFAULT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `unique_id` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT 'inbound(0) / outbound(1) / internatl(3)',
  `trunk_id` int(11) DEFAULT NULL,
  `pbx_cdr_tenant_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pbx_tenant_cdrs_pbx_cdr_tenant_id_foreign` (`pbx_cdr_tenant_id`),
  KEY `pbx_tenant_cdrs_unique_id_start_date_index` (`unique_id`,`start_date`),
  CONSTRAINT `pbx_tenant_cdrs_pbx_cdr_tenant_id_foreign` FOREIGN KEY (`pbx_cdr_tenant_id`) REFERENCES `pbx_cdr_tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pos_item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_item_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_category_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pos_money`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_money` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `is_coin` tinyint(4) NOT NULL DEFAULT '0',
  `currency_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pos_money_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_money_payment_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pos_money_id` int(10) unsigned NOT NULL,
  `payment_method_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pos_payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_payment_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_method_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pos_sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pos_stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_stores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pos_tip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_tip` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tip_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tip` decimal(8,2) NOT NULL,
  `tip_val` bigint(20) NOT NULL,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prefixes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prefixes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `from` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prefixrate_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prefixrate_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `status` enum('A','T') COLLATE utf8_unicode_ci NOT NULL,
  `type` enum('Inbound','Outbound') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profile_did`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_did` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `did_rate` double DEFAULT NULL,
  `toll_free_did_rate` int(11) DEFAULT NULL,
  `international_did_rate` int(11) DEFAULT NULL,
  `international_inbound_per_minute_rate` int(11) DEFAULT NULL,
  `inbound_per_minute_rate` int(11) DEFAULT NULL,
  `emergency_services_rate` int(11) DEFAULT NULL,
  `emergency_services_address` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cnam_rate` int(11) DEFAULT NULL,
  `cnam_name` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cnam_price` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `inbound_per_minute_rate_value` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emergency_services_rate_value` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emergency_services_city` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emergency_services_state` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `emergency_services_zip` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `did_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unmetered` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_did_company_id_foreign` (`company_id`),
  KEY `profile_did_creator_id_foreign` (`creator_id`),
  CONSTRAINT `profile_did_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `profile_did_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profile_did_custom_did_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_did_custom_did_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profile_did_id` int(10) unsigned NOT NULL,
  `custom_did_group_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profile_did_toll_frees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_did_toll_frees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `prefijo` bigint(20) DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `toll_free_category_id` int(10) unsigned NOT NULL,
  `rate_per_minute` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profile_extensions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_extensions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `rate` double(8,2) DEFAULT NULL,
  `minutes_cap` int(11) DEFAULT NULL,
  `minutes_increments` int(11) DEFAULT NULL,
  `outbound_per_minute_rate` decimal(8,2) DEFAULT NULL,
  `inbound_per_minute_rate` decimal(8,2) DEFAULT NULL,
  `extension_balance` double(8,2) DEFAULT NULL,
  `minimum_extension_balance` double(8,2) DEFAULT NULL,
  `status_payment` enum('prepaid','postpaid') COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I','R') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `type_time_increment` enum('sec','min') COLLATE utf8_unicode_ci NOT NULL,
  `extensions_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unmetered` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `providers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `middle_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `suffix` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name_check` tinyint(4) DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `state_id` int(11) DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` int(11) DEFAULT NULL,
  `fax` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `webside` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `terms` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `opening_balance` int(11) DEFAULT NULL,
  `as_of` date DEFAULT NULL,
  `account_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `business_no` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `track_payments` tinyint(4) DEFAULT NULL,
  `default_expense_account` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `status` enum('A','T') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `providers_number` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `push_notifications_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `push_notifications_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notification_sent` tinyint(1) DEFAULT NULL,
  `log_message` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notification_data` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `retentions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `retentions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `concept` text COLLATE utf8_unicode_ci NOT NULL,
  `minimium_base_per_unit` bigint(20) unsigned NOT NULL,
  `minimium_base_in_currency` bigint(20) NOT NULL,
  `type_of_minimium_base_in_currency` enum('percentage','fixed') COLLATE utf8_unicode_ci NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `foreing` tinyint(1) NOT NULL DEFAULT '0',
  `country_id` int(10) unsigned NOT NULL,
  `simple_tax_regime` tinyint(4) NOT NULL DEFAULT '0',
  `vat_withholding_agent` tinyint(4) NOT NULL DEFAULT '0',
  `self_retaining` tinyint(4) NOT NULL DEFAULT '0',
  `great_contributor` tinyint(4) NOT NULL DEFAULT '0',
  `type_vat_regime` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `retentions_country_id_foreign` (`country_id`),
  CONSTRAINT `retentions_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rol_permisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol_permisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rol_id` int(10) unsigned NOT NULL,
  `module` enum('lead','lead_notes','customers','providers','estimates','invoices','payments','items','expenses','packages','corepbx','tickets','users','reports','settings','account_settings','company_info','preferences','customizations','notifications','tax_Groups','tax_types','payment_modes','custom_fields','notes','expense_categories','mail_configuration','file_disk','backup','logs','Modules','PBXware','Avalara','BillPay','roles','payment_gateways','Authorize','Paypal','services','pbx_services','services_normal','retentions','pbx_packages','pbx_extension','pbx_did','pbx_app_rate','pbx_custom_did','pbx_custom_destination','pbx_customization','pbx_report','pbx_tenant','tickets_depa','tickets_email_temp','cust_address','cust_contacts','cust_payment_acc','cust_mnotes','corePOS_module','corePOS_index','corePOS_dashboard','open_close_cash_register','income_withdrawal_cash','assign_user_cash_register') COLLATE utf8_unicode_ci DEFAULT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '1',
  `create` tinyint(1) NOT NULL DEFAULT '1',
  `read` tinyint(1) NOT NULL DEFAULT '1',
  `update` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `guard_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `schedule_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lvl` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0 => debug, 1 => info, 2 => warning, 3 => error',
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `extra_data` json DEFAULT NULL COMMENT 'extra data concerning the module',
  `model_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedule_logs_model_type_model_id_index` (`model_type`,`model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `service_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `service_id` int(10) unsigned NOT NULL,
  `customer_ticket_id` bigint(20) unsigned NOT NULL,
  `service_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `states` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_alpha2` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stripe_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stripe_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `secret_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `currency` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'USD',
  `environment` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'sandbox',
  `creator_id` bigint(20) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `table_email_period`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `table_email_period` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `table_payment_devolutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `table_payment_devolutions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `transaction_id` int(10) unsigned DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payload` text COLLATE utf8_unicode_ci,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tax_agencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_agencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tax_agencies_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tax_agency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_agency` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `number` int(11) DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phonenumber` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tax_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tax_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tax_groups_id` bigint(20) unsigned NOT NULL,
  `taxes_id` int(10) unsigned NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_group_tax_groups_id_foreign` (`tax_groups_id`),
  KEY `tax_group_taxes_id_foreign` (`taxes_id`),
  CONSTRAINT `tax_group_tax_groups_id_foreign` FOREIGN KEY (`tax_groups_id`) REFERENCES `tax_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tax_group_taxes_id_foreign` FOREIGN KEY (`taxes_id`) REFERENCES `tax_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tax_group_taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_group_taxes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax_group_id` bigint(20) unsigned NOT NULL,
  `tax_types_id` int(10) unsigned NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_group_taxes_tax_group_id_foreign` (`tax_group_id`),
  KEY `tax_group_taxes_tax_types_id_foreign` (`tax_types_id`),
  CONSTRAINT `tax_group_taxes_tax_group_id_foreign` FOREIGN KEY (`tax_group_id`) REFERENCES `tax_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tax_group_taxes_tax_types_id_foreign` FOREIGN KEY (`tax_types_id`) REFERENCES `tax_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tax_group_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_group_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tax_groups_id` bigint(20) unsigned NOT NULL,
  `tax_types_id` int(10) unsigned NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_group_types_tax_groups_id_foreign` (`tax_groups_id`),
  KEY `tax_group_types_tax_types_id_foreign` (`tax_types_id`),
  CONSTRAINT `tax_group_types_tax_groups_id_foreign` FOREIGN KEY (`tax_groups_id`) REFERENCES `tax_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tax_group_types_tax_types_id_foreign` FOREIGN KEY (`tax_types_id`) REFERENCES `tax_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tax_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `state_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tax_agency_id` bigint(20) unsigned DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `county` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_groups_country_id_foreign` (`country_id`),
  KEY `tax_groups_state_id_foreign` (`state_id`),
  KEY `tax_groups_company_id_foreign` (`company_id`),
  CONSTRAINT `tax_groups_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `tax_groups_country_id_foreign` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`),
  CONSTRAINT `tax_groups_state_id_foreign` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tax_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `percent` decimal(5,2) NOT NULL,
  `compound_tax` tinyint(4) NOT NULL DEFAULT '0',
  `collective_tax` tinyint(4) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8_unicode_ci,
  `company_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `state_id` bigint(20) unsigned DEFAULT NULL,
  `country_id` bigint(20) unsigned DEFAULT NULL,
  `tax_agency_id` bigint(20) unsigned DEFAULT NULL,
  `tax_category_id` bigint(20) unsigned DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `county` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `for_cdr` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_types_company_id_foreign` (`company_id`),
  CONSTRAINT `tax_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taxes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax_type_id` int(10) unsigned NOT NULL,
  `invoice_id` int(10) unsigned DEFAULT NULL,
  `estimate_id` int(10) unsigned DEFAULT NULL,
  `package_id` int(10) unsigned DEFAULT NULL,
  `invoice_item_id` int(10) unsigned DEFAULT NULL,
  `estimate_item_id` int(10) unsigned DEFAULT NULL,
  `package_item_id` int(10) unsigned DEFAULT NULL,
  `item_id` int(10) unsigned DEFAULT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `amount` bigint(20) unsigned NOT NULL,
  `percent` decimal(5,2) NOT NULL,
  `compound_tax` tinyint(4) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `pbx_package_id` int(10) unsigned DEFAULT NULL,
  `pbx_package_item_id` int(10) unsigned DEFAULT NULL,
  `pbx_service_item_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `taxes_tax_type_id_foreign` (`tax_type_id`),
  KEY `taxes_invoice_id_foreign` (`invoice_id`),
  KEY `taxes_estimate_id_foreign` (`estimate_id`),
  KEY `taxes_invoice_item_id_foreign` (`invoice_item_id`),
  KEY `taxes_estimate_item_id_foreign` (`estimate_item_id`),
  KEY `taxes_item_id_foreign` (`item_id`),
  KEY `taxes_company_id_foreign` (`company_id`),
  KEY `taxes_pbx_package_id_foreign` (`pbx_package_id`),
  CONSTRAINT `taxes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `taxes_estimate_id_foreign` FOREIGN KEY (`estimate_id`) REFERENCES `estimates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxes_estimate_item_id_foreign` FOREIGN KEY (`estimate_item_id`) REFERENCES `estimate_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxes_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxes_invoice_item_id_foreign` FOREIGN KEY (`invoice_item_id`) REFERENCES `invoice_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxes_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxes_pbx_package_id_foreign` FOREIGN KEY (`pbx_package_id`) REFERENCES `pbx_packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `taxes_tax_type_id_foreign` FOREIGN KEY (`tax_type_id`) REFERENCES `tax_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_departaments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_departaments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `client_permission` tinyint(1) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sender_override` tinyint(1) DEFAULT NULL,
  `send_emails` tinyint(1) DEFAULT NULL,
  `automatically_transition_admin` tinyint(1) DEFAULT NULL,
  `default_priority` enum('E','C','H','M','L') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'E',
  `email_handling` enum('N','P','O','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `automatically_close` int(10) unsigned NOT NULL,
  `automatically_delete` int(10) unsigned NOT NULL,
  `status` enum('A','I') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `company_id` int(10) unsigned NOT NULL,
  `creator_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `schedule_data` longtext COLLATE utf8_unicode_ci,
  `receive_tickets_emails` text COLLATE utf8_unicode_ci,
  `receive_mobile_tickets_emails` text COLLATE utf8_unicode_ci,
  `receive_tickets_messenger_notifications` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `toll_free_custom_did_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `toll_free_custom_did_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `toll_free_did_id` bigint(20) unsigned NOT NULL,
  `custom_did_group_id` bigint(20) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `toll_free_custom_did_group_toll_free_did_id_foreign` (`toll_free_did_id`),
  KEY `toll_free_custom_did_group_custom_did_group_id_foreign` (`custom_did_group_id`),
  CONSTRAINT `toll_free_custom_did_group_custom_did_group_id_foreign` FOREIGN KEY (`custom_did_group_id`) REFERENCES `custom_did_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `toll_free_custom_did_group_toll_free_did_id_foreign` FOREIGN KEY (`toll_free_did_id`) REFERENCES `profile_did_toll_frees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transaction_fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction_fees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Fixed',
  `amount` bigint(20) unsigned NOT NULL DEFAULT '0',
  `total` bigint(20) unsigned NOT NULL DEFAULT '0',
  `payment_gateways_fee_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `authorize_id` bigint(20) unsigned DEFAULT NULL,
  `aux_vault_id` bigint(20) unsigned DEFAULT NULL,
  `payments_paypal_id` bigint(20) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `units_company_id_foreign` (`company_id`),
  CONSTRAINT `units_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_permisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `module` enum('lead','lead_notes','customers','providers','estimates','invoices','payments','items','expenses','packages','corepbx','tickets','users','reports','settings','account_settings','company_info','preferences','customizations','notifications','tax_Groups','tax_types','payment_modes','custom_fields','notes','expense_categories','mail_configuration','file_disk','backup','logs','Modules','PBXware','Avalara','BillPay','roles','payment_gateways','Authorize','Paypal','services','pbx_services','services_normal','retentions','pbx_packages','pbx_extension','pbx_did','pbx_app_rate','pbx_custom_did','pbx_custom_destination','pbx_customization','pbx_report','pbx_tenant','tickets_depa','tickets_email_temp','cust_address','cust_contacts','cust_payment_acc','cust_mnotes','corePOS_module','corePOS_index','corePOS_dashboard','open_close_cash_register','income_withdrawal_cash','assign_user_cash_register') COLLATE utf8_unicode_ci DEFAULT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '1',
  `create` tinyint(1) NOT NULL DEFAULT '1',
  `read` tinyint(1) NOT NULL DEFAULT '1',
  `update` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `company_id` int(10) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_settings_user_id_foreign` (`user_id`),
  CONSTRAINT `user_settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_customer` enum('A','I','F') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_encrypted` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `security_pin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `role2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authentication` tinyint(4) DEFAULT NULL,
  `username_status` tinyint(4) DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `github_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `enable_portal` tinyint(1) DEFAULT NULL,
  `currency_id` int(10) unsigned DEFAULT NULL,
  `company_id` int(10) unsigned DEFAULT NULL,
  `company_orf` enum('bscl','svcl','fclt','reg') COLLATE utf8_unicode_ci DEFAULT NULL,
  `company_orf_type` enum('0','1') COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_type` enum('N','B','R') COLLATE utf8_unicode_ci DEFAULT NULL,
  `avalara_bool` tinyint(1) DEFAULT NULL,
  `avalara_type` enum('0','1','2','3') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Avalara Customer Type.',
  `bscl` enum('0','1') COLLATE utf8_unicode_ci DEFAULT NULL,
  `svcl` enum('0','1') COLLATE utf8_unicode_ci DEFAULT NULL,
  `fclt` tinyint(1) DEFAULT NULL,
  `reg` tinyint(1) DEFAULT NULL,
  `prepaid_option` enum('0','1') COLLATE utf8_unicode_ci DEFAULT '0',
  `auto_debit` enum('0','1') COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_low_balance_notification` int(11) DEFAULT '0',
  `auto_replenish_amount` int(11) DEFAULT '0',
  `negative_balance` int(11) DEFAULT '0',
  `language` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auto_suspension` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `balance` double unsigned DEFAULT '0',
  `add_shipping_addres` tinyint(1) NOT NULL,
  `status_payment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `minimun_balance` decimal(20,5) NOT NULL DEFAULT '0.00000',
  `sendedcredentials` tinyint(1) NOT NULL DEFAULT '0',
  `sale_type` enum('Retail','Wholesale','Consumed','Vendor Use') COLLATE utf8_unicode_ci DEFAULT NULL,
  `avalara_location_id` bigint(20) unsigned DEFAULT NULL COMMENT 'General location for Avalara Invoice',
  `frch` tinyint(4) DEFAULT NULL,
  `lfln` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'avalara life line',
  `type_vat_regime` int(11) DEFAULT NULL,
  `great_contributor` tinyint(1) NOT NULL DEFAULT '0',
  `self_retaining` tinyint(1) NOT NULL DEFAULT '0',
  `vat_withholding_agent` tinyint(1) NOT NULL DEFAULT '0',
  `simple_tax_regime` tinyint(1) NOT NULL DEFAULT '0',
  `not_applicable_others` tinyint(1) NOT NULL DEFAULT '0',
  `firebase_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pbx_notification` tinyint(1) DEFAULT '0',
  `email_estimates` tinyint(1) DEFAULT '0',
  `email_invoices` tinyint(1) DEFAULT '0',
  `email_payments` tinyint(1) DEFAULT '0',
  `email_services` tinyint(1) DEFAULT '0',
  `email_pbx_services` tinyint(1) DEFAULT '0',
  `email_tickets` tinyint(1) DEFAULT '0',
  `email_expenses` tinyint(1) DEFAULT '0',
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `incorporated` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Critical to apply local taxes',
  `lead_id` int(11) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_currency_id_foreign` (`currency_id`),
  KEY `users_company_id_foreign` (`company_id`),
  KEY `users_creator_id_foreign` (`creator_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `users_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users_estimates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_estimates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `estimate_id` int(10) unsigned NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_11_071840_create_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2014_10_11_125754_create_currencies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2016_05_13_060834_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2017_04_11_064308_create_units_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2017_04_11_081227_create_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2017_04_11_140447_create_invoice_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2017_04_12_090759_create_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2017_04_12_091015_create_invoice_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2017_05_04_141701_create_estimate_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2017_05_05_055609_create_estimates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2017_05_05_073927_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2017_05_06_173745_create_countries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2017_10_02_123501_create_estimate_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2018_11_02_133825_create_ expense_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2018_11_02_133956_create_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2019_08_30_072639_create_addresses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2019_09_02_053155_create_payment_methods_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2019_09_03_135234_create_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2019_09_14_120124_create_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2019_09_21_052540_create_tax_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2019_09_21_052548_create_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2019_09_26_145012_create_company_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2020_02_01_063235_create_custom_fields_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2020_02_01_063509_create_custom_field_values_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2020_05_12_154129_add_user_id_to_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2020_09_07_103054_create_file_disks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2020_09_22_153617_add_columns_to_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2020_09_26_100951_create_user_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2020_10_01_102913_add_company_to_addresses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2020_10_17_074745_create_notes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2020_10_24_091934_change_value_column_to_text_on_company_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2020_11_23_050206_add_creator_in_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2020_11_23_050252_add_creator_in_estimates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2020_11_23_050316_add_creator_in_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2020_11_23_050333_add_creator_in_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2020_11_23_050406_add_creator_in_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2020_11_23_065815_add_creator_in_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2020_11_23_074154_create_email_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2020_12_02_064933_update_crater_version_320',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2020_12_02_090527_update_crater_version_400',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2020_12_08_065715_change_description_and_notes_column_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2020_12_08_133131_update_crater_version_401',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2020_12_14_044717_add_template_name_to_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2020_12_14_045310_add_template_name_to_estimates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2020_12_14_051450_remove_template_id_from_invoices_and_estimates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2020_12_23_061302_update_crater_version_402',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2020_12_31_100816_update_crater_version_403',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2021_01_22_085644_update_crater_version_404',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2021_03_03_155223_add_unit_name_to_pdf',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2021_03_23_145012_add_number_length_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2021_05_05_063533_update_crater_version_410',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2021_05_27_152128_corebill_logs_dev',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2021_05_30_124238_create_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2021_05_31_153000_logs_module',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2021_05_31_193531_create_package_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2021_05_31_194546_create_package_group_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2021_06_01_144839_create_package_names_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2021_06_01_150718_create_package_descriptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2021_06_01_160743_create_item_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2021_06_08_004440_create_package_tax_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2021_06_08_131332_update_status_in_package_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2021_06_08_132650_update_status_in_package_group_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2021_06_08_134702_add_order_to_package_group',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2021_06_10_182253_create_package_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2021_06_10_192213_alter_items_table_delete_unit_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2021_06_11_140403_create_item_group_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2021_06_14_150619_alter_table_items_add_taxables_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2021_06_16_134357_create_states_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2021_06_16_161831_alter_item_groups_table_column_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2021_06_16_192126_create_tax_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2021_06_17_142547_create_tax_group_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2021_06_18_204440_create_package_tax_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2021_06_19_121939_update_crater_version_420',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2021_06_22_150912_create_tax_group_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2021_06_23_194435_alter_email_logs_table_add_column_company_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2021_06_23_202419_create_package_item_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2021_06_24_182416_add_deleted_at_to_tax_group',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2021_06_25_213119_create_providers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2021_06_30_142416_alter_users_table_add_columns_for_avalara',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2021_07_01_170932_alter_items_table_add_avalara_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2021_07_01_182711_create_avalara_configs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2021_07_02_175052_create_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2021_07_06_004704_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2021_07_06_145002_create_pbx_servers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2021_07_09_171118_create_customer_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2021_07_12_203421_create_payment_gateways_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2021_07_12_220243_create_tax_group_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2021_07_14_205750_alter_users_table_add_columns_company_for_avalara',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2021_07_15_214758_create_pbx_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2021_07_16_190702_alter_table_payment_gateways_add_default_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2021_07_20_185647_create_avalara_service_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2021_07_20_200529_alter_table_items_add_avalara_service_type_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2021_07_21_151332_alter_table_addresses_state_id_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2021_07_21_151428_add_id_pbx_package_to_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2021_07_21_191459_add_id_pbx_server_to_pbx_package_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2021_07_21_205326_create_profile_extensions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2021_07_22_144006_create_pbx_package_tax_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2021_07_22_171630_create_authorize_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2021_07_22_190233_create_aditional_charges_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2021_07_23_145513_alter_users_table_add_columns_prepaid_options',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2021_07_26_025436_create_authorize_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2021_07_26_133810_alter_table_expenses_add_expense_number_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2021_07_26_150117_create_prefixes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2021_07_26_155702_alter_users_table_add_columns_company_with_select_box_for_avalara',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2021_07_27_160853_alter_table_authorize_add_fields_null_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2021_07_27_172929_alter_table_payment_add_authorize_id_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2021_07_28_213515_alter_table_authorize_add_address_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2021_07_29_153532_create_profile_did_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2021_07_29_161032_add_id_profile_did_to_aditional_charges_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2021_07_29_200606_alter_customer_packages_table_rename_discount_type_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (114,'2021_07_29_201550_alter_customer_packages_table_add_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2021_07_29_202958_alter_users_table_add_columns_first_last_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2021_07_29_211001_add_columns_to_pbx_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2021_07_29_220803_create_customer_package_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2021_07_29_232111_create_customer_package_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2021_07_30_001347_create_customer_package_discount_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2021_07_30_011035_alter_customer_packages_table_add_term_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2021_07_30_163615_create_avalara_taxe_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (122,'2021_07_30_163704_create_avalara_taxe_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (123,'2021_07_30_163715_create_avalara_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (124,'2021_07_30_164054_alter_table_items_add_avalara_service_type_data_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (125,'2021_07_30_184144_alter_packages_table_add_column_packages_discount_none',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (126,'2021_08_02_170306_alter_table_authorize_change_column_address_street_2_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (127,'2021_08_03_133854_add_columns_to_pbx_packages_table_v2',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (128,'2021_08_04_143035_alter_table_payments_add_credit_card_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (129,'2021_08_05_100452_add_columns_to_payment_methods_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (130,'2021_08_05_191322_alter_payment_methods_add_column_account_accepted',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (131,'2021_08_05_203134_packages_table_add_column_discounts_general',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (132,'2021_08_06_163720_alter_table_item_groups_add_no_taxable_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (133,'2021_08_06_203655_add_columns_to_profile_did_v2',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (134,'2021_08_09_161751_add_columns_to_profile_extensions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (135,'2021_08_09_181446_packages_table_add_column_apply_tax_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (136,'2021_08_10_015729_alter_customer_package_discounts_add_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (137,'2021_08_10_132102_create_customer_notes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (138,'2021_08_10_132508_alter_table_users_add_customer_username_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (139,'2021_08_12_162921_create_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (140,'2021_08_13_000750_alter_table_users_add_status_customer_and_deleted_at_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (141,'2021_08_13_133418_create_profile_did_toll_frees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (142,'2021_08_15_215601_create_payment_accounts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (143,'2021_08_16_225436_alter_table_expenses_add_provider_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (144,'2021_08_18_133803_alter_table_payment_methods_add_payment_gateways_id_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (145,'2021_08_18_141204_alter_table_avalara_configs_add_company_data',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (146,'2021_08_18_142727_alter_table_user_remove_company_data',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (147,'2021_08_18_151352_alter_customer_packages_table_add_column_renewal_date',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (148,'2021_08_18_155547_create_call_detail_registers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (149,'2021_08_18_173937_alter_table_payment_accounts_add_status_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (150,'2021_08_18_194137_alter_table_payment_accounts_change_address_2_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (151,'2021_08_19_160036_alter_table_users_add_balance_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (152,'2021_08_19_170733_create_balance_customers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (153,'2021_08_20_163605_alter_users_table_add_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (154,'2021_08_20_173208_alter_addresses_table_add_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (155,'2021_08_22_155627_create_ticket_departaments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (156,'2021_08_24_122120_create_customer_configs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (157,'2021_08_24_131900_alter_table_authorize_add_expiration_date_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (158,'2021_08_24_215257_alter_table_authorize_remove_card_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (159,'2021_08_25_150601_alter_customer_packages_table_add_code_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (160,'2021_08_26_155810_alter_table_pbx_packages_add_packages_number_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (161,'2021_08_26_160521_create_pbx_extensions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (162,'2021_08_26_161004_alter_table_profile_did_add_did_number_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (163,'2021_08_26_161107_alter_table_profile_extensions_add_did_number_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (164,'2021_08_26_205132_alter_table_payments_add_transaction_status_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (165,'2021_08_26_222818_alter_table_balance_customers_remove_transaction_status_and_payment_id_null',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (166,'2021_08_27_135715_create_pbx_did',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (167,'2021_08_27_143445_create_pbx_services_extensions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (168,'2021_08_27_144129_create_pbx_services_did',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (169,'2021_08_27_153131_create_pbx_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (170,'2021_08_27_185420_alter_table_profile_did_toll_frees_add_toll_free_category_id_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (171,'2021_08_30_132352_alter_call_detail_register_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (172,'2021_08_30_180333__alter_table_expenses_add_item_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (173,'2021_08_31_123715_invoice_customer_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (174,'2021_08_31_175840_alter_table_invoice_add_package_bool',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (175,'2021_09_01_134819_alter_table_item_number',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (176,'2021_09_01_135208_alter_table_provider_number',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (177,'2021_09_02_121731_alter_table_users_change_first_and_last_name_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (178,'2021_09_02_131908_alter_table_users_add_security_pin_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (179,'2021_09_02_145521_add_create_at_to_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (180,'2021_09_02_145659_add_delete_at_to_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (181,'2021_09_02_154654_alter_table_ticket_departaments_add_schedule_data_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (182,'2021_09_02_190400_alter_table_authorize_change_phone_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (183,'2021_09_02_193332_create_failed_payment_history',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (184,'2021_09_03_105257_create_departament_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (185,'2021_09_06_200339_create_pbx_tenant_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (186,'2021_09_06_200925_alter_table_users_add_role2_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (187,'2021_09_06_202841_alter_pbx_did_table_add_tentant_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (188,'2021_09_06_202903_alter_pbx_extensions_table_add_tentant_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (189,'2021_09_06_205243_alter_table_pbxservices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (190,'2021_09_06_210654_alter_pbx_services_table_modify_column_status',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (191,'2021_09_06_213947_alter_table_pbx_packages_add_unmetered_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (192,'2021_09_06_214246_alter_table_profile_extensions_add_unmetered_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (193,'2021_09_06_214401_alter_table_profile_did_add_unmetered_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (194,'2021_09_06_235957_alter_invoices_table_add_column_customer_packages_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (195,'2021_09_07_182936_alter_pbx_services_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (196,'2021_09_07_203707_alter_table_users_add_add_shipping_addres_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (197,'2021_09_09_143348_drop_profile_ext_on_pbx_services_extensions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (198,'2021_09_09_144908_drop_profile_did_on_pbx_services_did_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (199,'2021_09_09_234328_alter_table_pbx_packages_add_extension_id_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (200,'2021_09_10_073655_create_customer_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (201,'2021_09_10_133138_create_customer_ticket_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (202,'2021_09_10_152727_alter_cdrs_table_migration',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (203,'2021_09_10_152916_create_call_detail_register_totals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (204,'2021_09_10_215042_alter_table_pbx_packages_add__discount_term_type_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (205,'2021_09_13_144345_create_pbx_services_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (206,'2021_09_13_175902_alter_table_packages_add_status_payment_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (207,'2021_09_13_214029_alter_table_user_add_column_status',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (208,'2021_09_15_213320_alter_table_pbx_packages_modify_rate_per_minutes_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (209,'2021_09_16_150216_delete_fields_pbx_did',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (210,'2021_09_16_150451_delete_fields_pbx_extensions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (211,'2021_09_16_182526_alter_pbx_services_table_add_renewal_date',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (212,'2021_09_17_161024_add_service_id_to_call_detail_register_totals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (213,'2021_09_17_183127_alter_table_authorize_add_ach_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (214,'2021_09_17_222854_add_ext_to_pbx_extensions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (215,'2021_09_20_144145_alter_pbx_services_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (216,'2021_09_20_144846_alter_pbx_did_table_add_all_fields_from_api',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (217,'2021_09_20_163722_alter_pbx_extensions_table_add_all_fields_from_api',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (218,'2021_09_21_155859_alter_table_payment_accounts_add_bank_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (219,'2021_09_22_143917_alter_table_pbx_services_items_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (220,'2021_09_22_145815_create_pbx_package_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (221,'2021_09_22_154609_create_pbx_package_item_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (222,'2021_09_22_160311_alter_table_pbx_packages_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (223,'2021_09_22_201250_create_international_rate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (224,'2021_09_24_155609_alter_pbx_services_table_add_price',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (225,'2021_09_25_182213_create_pbx_services_tax_type_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (226,'2021_09_27_215022_add_auto_suspension_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (227,'2021_10_01_231948_add_invoice_to_cdr_total',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (228,'2021_10_06_142354_alter_invoices_table_add_columns_pbx_service_id_and_pbx_service_price',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (229,'2021_10_06_153208_create_invoice_dids_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (230,'2021_10_06_163153_create_invoice_extensions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (231,'2021_10_06_192010_create_invoice_additional_charges_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (232,'2021_10_06_204925_add_only_callrating_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (233,'2021_10_07_135558_add_allow_discount_type_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (234,'2021_10_07_192503_alter_pbx_packages_inclusive_minutes_seconds',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (235,'2021_10_07_193906_alter_pbx_services_inclusive_minutes_consume',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (236,'2021_10_08_151219_add_inclusive_fields_to_call_detail_register_totals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (237,'2021_10_08_151319_add_inclusive_fields_to_call_detail_registers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (238,'2021_10_08_165537_alter_invoice_extensions_table_add_columns_campany_id_and_creator_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (239,'2021_10_08_201528_alter_invoice_dids_table_add_columns_campany_id_and_creator_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (240,'2021_10_11_141904_add_totales_pbx_services_to_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (241,'2021_10_11_143732_alter_invoice_additional_charges_table_add_columns_campany_id_and_creator_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (242,'2021_10_11_154133_create_paypal_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (243,'2021_10_13_151427_add_index_to_call_detail_register_totals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (244,'2021_10_15_183336_alter_pbx_services_time_period_value',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (245,'2021_10_18_160209_alter_customer_packages_table_add_auto_suspension_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (246,'2021_10_18_212529_alter_table_payment_accounts_add_num_check',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (247,'2021_10_20_133604_altertable_pbx_service_previusdate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (248,'2021_10_20_134231_altertable_invoice_previusdate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (249,'2021_10_21_141147_create_prefix_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (250,'2021_10_21_143551_alter_prefix_groups_pbxpackages_pbxservices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (251,'2021_10_22_152331_alter_table_international_rate_add_prefixrate_groups_id_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (252,'2021_10_25_180451_alter_table_pbx_packages_add_automatic_suspension_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (253,'2021_10_27_151628_alter_rate_call_detail_register_totals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (254,'2021_10_28_025212_add_for_customer_user_payment_methods_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (255,'2021_10_29_170634_alter_prefixrate_groups_table_add_column_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (256,'2021_10_29_175953_alter_pbx_packages_services_outbound',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (257,'2021_10_29_205616_alter_table_authorize_add_column_credit_card',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (258,'2021_10_29_212118_alter_table_payment_accounts_add_comumn_credit_card',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (259,'2021_11_02_192152_create_custom_did_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (260,'2021_11_02_195436_create_toll_free_custom_did_group_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (261,'2021_11_03_154244_alter_table_profile_did_toll_frees_add_rate_per_minute_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (262,'2021_11_08_152756_alter_table_international_rate_add_category_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (263,'2021_11_09_135649_create_profile_did_custom_did_groups_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (264,'2021_11_09_211210_alter_payments_accounts',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (265,'2021_11_10_162511_alter_international_rate_table_modify_prefixrate_groups_id_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (266,'2021_11_10_201035_add_custom_did_id_to_pbx_services_did',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (267,'2021_11_11_152124_alter_authorize',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (268,'2021_11_11_182227_alter_failedpayments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (269,'2021_11_15_151859_alter_table_profile_did',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (270,'2021_11_15_175408_alter_table_invoices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (271,'2021_11_15_212059_alter_invoice_dids_table_add_and_modify_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (272,'2021_11_16_234908_create_international_rate_prefixrate_group_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (273,'2021_11_17_171716_alter_table_invoices_format',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (274,'2021_11_18_151755_create_international_prefixrate_destination_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (275,'2021_11_18_170855_alter_table_custom_destination_groups_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (276,'2021_11_18_190646_alter_table_custom_destination_groups_desnull',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (277,'2021_11_22_144908_alter_paypal_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (278,'2021_11_22_164450_alter_table_users_add_minimun_balance_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (279,'2021_11_22_183759_alter_table_balance_customer',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (280,'2021_11_23_161924_alter_table_calldetailregister_prepaid',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (281,'2021_11_23_224038_alter_table_calldetailregister_old',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (282,'2021_11_29_201609_alter_prefijo_profile_did_toll_frees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (283,'2021_11_30_141247_alter_table_invoice_autodebit_notice',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (284,'2021_12_01_142116_alter_table_invoice_credentials',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (285,'2021_12_03_185246_alter_table_pbx_service_avalara_bool',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (286,'2021_12_03_193242_alter_table_authorize_settings_add_null_columns',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (287,'2021_12_06_144856_alter_table_pbx_service_avalara_bool_default',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (288,'2021_12_06_184509_add_fields_to_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (289,'2021_12_06_214331_create_avalara_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (290,'2021_12_06_214740_add_avalara_invoice_relation_to_avalara_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (291,'2021_12_07_131506_alter_table_pbxpacjage_bool',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (292,'2021_12_07_162414_add_cap_extension_to_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (293,'2021_12_08_152625_add_avalara_invoice_tax_to_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (294,'2021_12_08_161518_add_cap_total_to_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (295,'2021_12_08_200746_create_table_email_period',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (296,'2021_12_10_152043_create_pbx_services_detail_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (297,'2021_12_10_160004_add_did_cdr_ext_int_items_relation_to_avalara_configs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (298,'2021_12_10_161103_alter_table_customer_check',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (299,'2021_12_10_174410_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (300,'2021_12_13_205851_add_all_cdrs_to_pbx_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (301,'2021_12_14_175017_create_payments_paypals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (302,'2021_12_15_151822_alter_table_invoices_add_invoice_pbx_modify_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (303,'2021_12_15_162023_add_assigne_user_to_estimates',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (304,'2021_12_15_163624_create_users_estimates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (305,'2021_12_21_150038_add_job_status_to_pbx_services_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (306,'2021_12_22_163647_create_avalara_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (307,'2021_12_22_180615_add_soft_deletes_to_estimates',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (308,'2021_12_23_174635_add_prorate_fields_to_pbx_services_did',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (309,'2021_12_23_183541_add_prorate_fields_to_pbx_services_extensions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (310,'2021_12_27_183945_add_paypal_id_to_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (311,'2021_12_27_192641_add_fields_to_payments_paypals_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (312,'2021_12_27_194417_alter_payments_prepaid',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (313,'2021_12_28_144434_alter_payment_accounts_zip',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (314,'2021_12_29_162523_create_protate_data',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (315,'2021_12_29_211648_create_cache_locks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (316,'2021_12_30_191018_add_invoiced_prorate_field_to_pbx_services_did',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (317,'2021_12_30_215611_add_timezone_to_pbx_servers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (318,'2022_01_03_141018_create_pbx_server_cdr_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (319,'2022_01_03_145616_add_total_prorate_field_to_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (320,'2022_01_04_152052_add_timezone_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (321,'2022_01_05_165245_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (322,'2022_01_11_194401_create_job_batches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (323,'2022_01_11_210742_create_pbx_job_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (324,'2022_01_21_211236_add_config_and_tenant_id_to_pbx_tenant_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (325,'2022_01_25_164621_alter_call_register_monto_float',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (326,'2022_01_25_173743_create_history_call_indi',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (327,'2022_01_26_183532_alter_corebill_logs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (328,'2022_01_27_160346_add_generate_expense_field_to_payment_methods',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (329,'2022_01_27_213934_add_tax_type_id_to_pbx_services_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (330,'2022_01_31_193801_add_pbx_package_item_id_to_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (331,'2022_01_31_195840_add_id_to_did_and_extension_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (332,'2022_02_01_151959_add_for_payments_field_to_expense_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (333,'2022_02_01_212623_add_field_name_to_pbx_package_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (334,'2022_02_07_114602_alter_table_expenses_payments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (335,'2022_02_08_185818_alter_table_avalara_config_profileid',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (336,'2022_02_08_201029_alter_table_user_customer_typeavalara',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (337,'2022_02_11_125143_alter_table_company_identifier',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (338,'2022_02_11_135857_alter_table_avalara_account',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (339,'2022_02_11_160959_add_request_to_avalara_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (340,'2022_02_15_183717_alter_table_expense_newfields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (341,'2022_02_15_200014_create_avalara_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (342,'2022_02_15_200023_create_avalara_exemptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (343,'2022_02_17_183203_add_pbx_server_id_to_pbx_tenant_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (344,'2022_02_17_184505_add_pbxdid_id_to_pbx_did_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (345,'2022_02_17_184711_add_pbxext_id_to_pbx_extensions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (346,'2022_02_17_204720_add_fields_to_pbx_did_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (347,'2022_02_17_205112_add_fields_to_pbx_extensions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (348,'2022_02_18_221623_alter_payments_table_add_soft_deletes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (349,'2022_02_18_222912_alter_addresses_table_add_soft_deletes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (350,'2022_02_21_214344_add_discount_term_type_to_pbx_services_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (351,'2022_02_22_142932_create_avalara_bundles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (352,'2022_02_28_142125_create_mobile_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (353,'2022_02_28_185033_add_field_pbx_service_item_id_to_taxes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (354,'2022_03_01_220041_alter_table_customer_package',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (355,'2022_03_02_132139_alter_table_call_history_newcharge',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (356,'2022_03_02_201013_alter_table_invoicecustomer',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (357,'2022_03_03_164641_add_street_address_to_avalara_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (358,'2022_03_04_010954_add_pn_to_pbx_extensions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (359,'2022_03_08_171019_create_avalara_locationables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (360,'2022_03_09_165644_add_location_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (361,'2022_03_14_165337_add_life_line_to_avalara_configs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (362,'2022_03_15_201639_alter_table_invoice_validation',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (363,'2022_03_16_154436_alter_table_company_header',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (364,'2022_03_17_170403_alter_table_history_call_indi_tax',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (365,'2022_03_17_172824_alter_table_history_call_indi_tax2',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (366,'2022_03_17_175026_alter_table_history_call_indi_tax3',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (367,'2022_03_22_214537_alter_table_history_call_indi_add_column_invoice_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (368,'2022_03_22_225341_alter_table_invoices_add_columns_prepaid_amount_and_tax_prepaid_amount',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (369,'2022_03_29_164440_edit_balance_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (370,'2022_03_29_165412_edit_balance_to_balance_customers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (371,'2022_03_30_133152_change_name_to_modules',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (372,'2022_03_30_200155_alter_table_invoice_dids_modify_pbx_did_id_to_nullable',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (373,'2022_04_05_204148_add_pcode_to_addresses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (374,'2022_04_08_222952_alter_table_pbx_service_extension_add_column_old_prorate_and_old_date_prorate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (375,'2022_04_11_210458_alter_table_pbx_services_did_add_column_old_prorate_and_old_date_prorate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (376,'2022_04_13_174354_create_custom_app_rates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (377,'2022_04_14_182552_alter_table_expense_status',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (378,'2022_04_14_204216_add_custom_app_rate_id_to_pbx_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (379,'2022_04_15_013845_alter_table_payment_methods_add_columns_generate_expense_id_and_void_refund_expense_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (380,'2022_04_18_165041_add_field_to_custom_app_rates',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (381,'2022_04_20_170205_add_suspension_type_to_pbx_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (382,'2022_04_25_191210_add_county_to_addresses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (383,'2022_04_26_144736_alter_table_pbx_packages_suspension',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (384,'2022_04_26_151729_alter_table_pbx_services_new_suspendionfields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (385,'2022_04_26_205846_alter_table_tax_types_country_state',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (386,'2022_04_28_124745_create_userpermisions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (387,'2022_04_28_145323_create_pbx_services_tax_types_cdr',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (388,'2022_04_28_145612_create_tax_agencies',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (389,'2022_04_28_151602_alter_addresses',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (390,'2022_04_28_153254_alter_internationalrate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (391,'2022_04_28_200846_create_tax_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (392,'2022_04_28_203638_create_pbx_packages_cdr_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (393,'2022_04_29_182111_create_pbx_package_tax_types_cdrs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (394,'2022_05_02_154939_add_tax_category_id_to_tax_types',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (395,'2022_05_03_145750_alter_tax_type_city_county',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (396,'2022_05_03_205021_create_history_call_indi_tax_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (397,'2022_05_03_211934_create_tax_agency_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (398,'2022_05_03_221201_alter_table_addresses_add_tax_agency_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (399,'2022_05_13_222113_create_pbx_services_app_rates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (400,'2022_05_16_193807_create_table_service_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (401,'2022_05_16_205920_add_order_to_international_rate_prefixrate_group',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (402,'2022_05_18_141742_invoice_custom_app_rate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (403,'2022_05_18_151629_alter_invoice_proapprate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (404,'2022_05_18_235907_alter_table_service_tickets_add_column_customer_ticket_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (405,'2022_05_19_141940_add_life_line_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (406,'2022_05_19_210555_add_user_id_to_avalara_exemptions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (407,'2022_05_20_113059_add_enable_to_avalara_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (408,'2022_05_23_220813_add_lfln_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (409,'2022_05_24_131842_create_table_payment_devolutions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (410,'2022_05_24_131948_table_payment_devolutions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (411,'2022_05_27_161243_add_for_cdr_tax_types',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (412,'2022_05_27_212623_alter_table_avalara_configs_add_default',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (413,'2022_05_30_200838_alter_table_authorize_settings_add_is_default',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (414,'2022_05_30_224725_alter_authorize_seetings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (415,'2022_06_01_154103_add_fields_to_avalara_bundles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (416,'2022_06_01_163632_add_credit_card_full_to_authorize',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (417,'2022_06_01_193432_create_bw_configs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (418,'2022_06_02_043129_add_cutom_app_rate_item_id_to_avalara_configs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (419,'2022_06_02_182456_add_fields_to_pbx_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (420,'2022_06_03_161341_add_company_owner_id_to_avalara_locations_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (421,'2022_06_03_180659_add_addresses_id_to_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (422,'2022_06_06_195032_create_retentions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (423,'2022_06_06_205555_add_field_to_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (424,'2022_06_06_215437_change_type_field_to_pbx_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (425,'2022_06_09_131614_alter_avalara_locations_adress_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (426,'2022_06_09_153133_alter_apbx_packages_rate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (427,'2022_06_09_173247_add_field_to_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (428,'2022_06_13_213338_add_retention_total_to_invoices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (429,'2022_06_14_055731_add_field_to_invoice_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (430,'2022_06_20_143255_alter_invoice_pbx_price',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (431,'2022_06_20_213302_create_bandwidth_accounts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (432,'2022_06_21_044713_create_custom_searches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (433,'2022_06_21_171308_create_add_ons_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (434,'2022_06_21_215122_create_pbx_extension_custom_searches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (435,'2022_06_23_221523_add_created_id_to_custom_searches_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (436,'2022_06_28_222729_add_account_name_to_bw_configs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (437,'2022_07_08_161801_alter_table_providers_change_phone_datatype',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (438,'2022_07_12_173151_alter_table_retentions_add_resp_fiscal_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (439,'2022_07_14_181542_add_field_to_avalara_config_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (440,'2022_07_14_221014_add_field_to_pbx_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (441,'2022_07_18_200606_add_fields_to_pbx_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (442,'2022_07_19_201941_add_price_to_pbx_services_extensions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (443,'2022_07_20_224015_create_mobile_login_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (444,'2022_07_21_150612_create_pbx_cdr_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (445,'2022_07_22_151724_create_pbx_tenant_cdrs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (446,'2022_08_04_152458_alter_users_table_add_firebasecode',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (447,'2022_08_04_220827_add_last_date_to_pbx_cdr_tenants_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (448,'2022_08_10_160131_create_push_notifications_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (449,'2022_08_12_232340_change_field_to_providers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (450,'2022_08_15_143824_change_type_business_no_to_providers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (451,'2022_08_17_161802_change_type_zip_code_to_providers',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (452,'2022_08_19_225211_alter_invoice_pbx_price4',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (453,'2022_08_23_160653_create_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (454,'2022_08_26_164827_alter_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (455,'2022_08_30_223637_alter_contacts_table_add_email',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (456,'2022_09_01_193351_alter_contacts_table_add_login_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (457,'2022_09_02_183721_alter_iavalara_service_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (458,'2022_09_06_160958_alter_iavalara_service_type_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (459,'2022_09_06_184828_alter_avalara_config_table_add_item_custom_item_toll_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (460,'2022_09_07_151512_alter_pbx_packages_table_add_item_inter_toll_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (461,'2022_09_09_231428_add_url_wallpaper_login_to_companies',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (462,'2022_09_12_122040_alter_pbx_ser_prorate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (463,'2022_09_13_145023_add_fields_to_avalara_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (464,'2022_09_13_164925_add_reverse_and_void_fields_to_avalara_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (465,'2022_09_15_155750_alter_mobile_settings_table_add_dark_color_palette',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (466,'2022_09_19_142552_add_field_to_pbx_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (467,'2022_09_27_152236_alter_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (468,'2022_09_27_152538_alter_tax_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (469,'2022_09_27_161742_alter_pbx_expense',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (470,'2022_09_30_162350_add_location_id_to_companies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (471,'2022_10_07_121118_alter_users_table_add_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (472,'2022_10_07_122017_alter_invoices_table_add_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (473,'2022_10_07_122852_alter_estimates_table_add_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (474,'2022_10_07_123131_alter_taxes_table_add_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (475,'2022_10_07_123649_alter_items_table_add_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (476,'2022_10_07_131843_validate_empty_estimate_template_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (477,'2022_10_07_134738_validate_empty_invoice_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (478,'2022_10_10_215429_alter_item_softdelte',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (479,'2022_10_11_134540_create_invoice_late_fees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (480,'2022_10_13_132824_add_late_fee_taxes_to_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (481,'2022_10_14_210625_add_original_valure_to_invoice_late_fees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (482,'2022_10_18_202344_alter_invoice_notedit',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (483,'2022_10_19_125547_alter_customer_packages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (484,'2022_10_19_235645_change_type_item_id_to_pbx_package_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (485,'2022_10_21_122531_alter_package_items_delete_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (486,'2022_10_21_123756_alter_package_items_agg_field',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (487,'2022_10_21_184326_alter_package_items',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (488,'2022_10_21_220842_add_apply_tax_type_to_pbx_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (489,'2022_10_25_131427_alter_customer_package_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (490,'2022_10_26_141031_alter_pbx_serviitemnull',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (491,'2022_10_31_172409_alter_user_pbx_notification',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (492,'2022_11_01_180115_alter_paymentgate',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (493,'2022_11_03_203216_alter_invoice_address',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (494,'2022_11_07_212911_change_enviroment_to_paypal_setting',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (495,'2022_11_08_163505_alter_pbx_did_servi',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (496,'2022_11_08_173120_alter_invoice_did_prefix_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (497,'2022_11_09_222549_create_general_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (498,'2022_11_15_144719_add_main_update_to_pbx_services_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (499,'2022_11_16_152239_add_deleted_in_server_to_pbx_extensions_and_did_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (500,'2022_11_16_160040_alter_pbx_services_add_apply_tax_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (501,'2022_11_16_195914_create_schedule_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (502,'2022_11_18_124806_alter_users_table_add_fields_bools',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (503,'2022_11_18_152945_alter_contacts_table_add_fields_bools',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (504,'2022_11_18_213323_add_update_child_services_to_pbx_packages',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (505,'2022_11_22_140735_add_allow_pbx_packages_update_to_pbx_services',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (506,'2022_11_22_183049_alter_contacts_table_change_field_password',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (507,'2022_11_25_141522_add_new_values_enum_user_permissions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (508,'2022_11_28_221520_update_field_enum_added_service_normal',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (509,'2022_11_30_145003_added_value_enum_retentions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (510,'2022_11_30_163943_added_values_enum_core_pbx',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (511,'2022_12_01_160552_delete_value_enum_duplicated_tax_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (512,'2022_12_07_154805_add_values_enum_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (513,'2022_12_13_173112_add_field_avalara_bool',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (514,'2022_12_19_174050_alter_invoice_count_templatrs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (515,'2022_12_20_123639_alter_invoice_addi_char',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (516,'2022_12_20_123640_add_field_expense_bool_payment_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (517,'2022_12_22_204509_update_values_invoice_additional_charges_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (518,'2022_12_22_233333_add_adj_to_avalara_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (519,'2022_12_26_195652_update_change_field_type_retention_amount_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (520,'2022_12_28_161840_alter_invoice_pbx_total_item',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (521,'2022_12_28_201316_alteaddi',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (522,'2022_12_28_210630_alteaddi2',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (523,'2023_01_06_184910_add_error_description_to_failed_payment_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (524,'2023_01_10_171611_create_field_settings_retention_platform_active_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (525,'2023_01_10_180312_alter_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (526,'2023_01_11_125922_invoices_pbx_extension_price',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (527,'2023_01_19_151505_create_field_applied_credit_customer_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (528,'2023_01_23_165138_add_edited_at_to_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (529,'2023_01_25_221924_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (530,'2023_01_31_202327_add_first_time_to_pbx_services_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (531,'2023_02_08_171847_modify_hash_invoices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (532,'2023_02_08_223404_modify_hash_payments',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (533,'2023_02_08_223430_modify_hash_estimates',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (534,'2023_02_14_175836_alter_table_tax_group_foreign_key',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (535,'2023_02_15_141713_add_field_price_pbx_services_did_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (536,'2023_02_17_160205_alter_table_package_tax_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (537,'2023_02_20_170026_alter_tickets_number',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (538,'2023_02_20_171225_alter_add_tickets_number',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (539,'2023_02_21_191323_alter_expense_date',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (540,'2023_02_23_163752_create_note_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (541,'2023_02_27_191617_add_subject_to_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (542,'2023_02_28_172442_create_pbx_additional_charges_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (543,'2023_03_01_222604_add_expense_notification_bool_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (544,'2023_03_03_115925_add_notification_to_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (545,'2023_03_03_152943_create_table_package_prefixrate_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (546,'2023_03_03_211921_create_expense_templates_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (547,'2023_03_06_142320_modify_field_last_date_expense_template_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (548,'2023_03_06_181144_create_table_pbx_services_prefix_rate_groups',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (549,'2023_03_06_200450_add_field_to_payment_method',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (550,'2023_03_07_144319_create_table_invoice_pbx_extension_detail',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (551,'2023_03_08_173306_create_table_invoice_pbx_did_detail',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (552,'2023_03_08_230548_alter_invoice_dids_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (553,'2023_03_09_142108_update_field_price_in_invoice_dids_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (554,'2023_03_09_152001_alter_field_additional_charge_id_in_invoice_additional_charges_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (555,'2023_03_09_194107_add_field_additional_charge_id_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (556,'2023_03_10_193318_add_field_amount_pbx_services_tax_type_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (557,'2023_03_14_142233_create_item_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (558,'2023_03_14_213059_add_field_token_firebass_mobile_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (559,'2023_03_15_140322_add_new_module_corebill_pos_modules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (560,'2023_03_15_193543_add_values_enum_rol_pos_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (561,'2023_03_16_123843_alter_items_table_new_field_item_category_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (562,'2023_03_16_123906_alter_item_groups_table_new_field_item_category_id',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (563,'2023_03_17_181352_add_field_allow_pos_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (564,'2023_03_20_160931_create_pos_money_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (565,'2023_03_21_181030_create_stores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (566,'2023_03_21_195105_create_items_stores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (567,'2023_03_21_195139_create_item_groups_stores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (568,'2023_03_22_124507_create_cash_register_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (569,'2023_03_22_124540_create_cash_register_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (570,'2023_03_24_163656_alter_note_tickets_table_new_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (571,'2023_03_27_172603_add_field_deleted_at_store_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (572,'2023_03_27_182201_alter_customer_tickets_table_new_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (573,'2023_03_29_145934_alter_rol_permisions_table_field_module',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (574,'2023_03_29_165651_add_expensewarnnig',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (575,'2023_03_29_182459_alter_user_permisions_table_field_module',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (576,'2023_04_06_135228_alter_profile_did_toll_frees_field_rate_per_minute',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (577,'2023_04_11_143623_add_options_to_company_settings',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (578,'2023_04_13_185939_update_field_price_pbx_service_extension_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (579,'2023_04_13_185953_update_field_price_pbx_service_did_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (580,'2023_04_13_211358_update_field_price_pbx_service_did_second_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (581,'2023_04_18_153800_alter_invoice_table_add_field_is_edited',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (582,'2023_04_20_193228_assign_value_inv_avalara_bool_in_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (583,'2023_04_21_130830_group_normal_tables_to_detail_tables_in_pbx',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (584,'2023_04_21_152014_search_and_create_bbc_emails',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (585,'2023_04_24_161328_add_field_avatar_in_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (586,'2023_04_26_150601_add_options_company_settings_idatio',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (587,'2023_04_28_153256_add_number_fields_to_avalara_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (588,'2023_04_29_123522_alter_items_servicestype2',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (589,'2023_05_01_153808_alter_items_user_userincorporated',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (590,'2023_05_02_152147_add_new_fields_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (591,'2023_05_02_202151_add_field_exemption_name_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (592,'2023_05_04_150831_add_response_lvl_to_avalara_configs',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (593,'2023_05_08_144156_add_invoice_mode_to_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (594,'2023_05_17_122640_add_field_save_as_draft_in_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (595,'2023_05_23_183745_alter_field_subject_in_expense_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (596,'2023_05_25_134511_create_leads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (597,'2023_05_29_145310_alter_field_type_leads_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (598,'2023_05_30_123809_alter_invoices_table_add_field_not_charge_automatically',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (599,'2023_05_31_223406_add_field_lead_id_customer_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (600,'2023_06_06_142457_add_field_pdf_pos_invoice_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (601,'2023_06_07_215239_insert_template_pos',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (602,'2023_06_13_162008_add_field_paypal_button_in_payment_methods_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (603,'2023_06_14_215516_add_new_field_is_pos_invoice_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (604,'2023_06_20_161242_alter_fields_first_and_last_name_in_providers_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (605,'2023_06_20_212503_create_item_item_category_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (606,'2023_06_21_215908_create_pos_item_categories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (607,'2023_06_28_220122_create_cash_register_invoice_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (608,'2023_06_29_153830_create_table_item_groups_item_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (609,'2023_07_05_203236_create_table_payments_payment_methods',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (610,'2023_07_06_005921_alter_table_payment_methods_add_field_is_multiple',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (611,'2023_07_11_120323_create_table_pos_payment_methods',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (612,'2023_07_12_154454_create_contact_invoice_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (613,'2023_07_14_122642_add_field_only_cash_in_payment_methods_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (614,'2023_07_14_153129_alter_payments_payment_methods_add_fields',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (615,'2023_07_18_143048_alter_table_pos_money_add_field_name',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (616,'2023_07_18_160241_create_table_pos_money_payment_methods',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (617,'2023_07_19_211449_add_fields_cash_register_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (618,'2023_07_26_201916_create_hold_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (619,'2023_07_26_201954_create_hold_items_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (620,'2023_07_26_202010_create_hold_taxes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (621,'2023_07_26_202053_create_hold_contacts_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (622,'2023_07_28_164828_create_table_expense_invoices',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (623,'2023_07_28_165445_create_table_expense_invoices_tax_types',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (624,'2023_08_10_010102_create_tables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (625,'2023_08_11_160034_create_cash_register_table_table_pivot',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (626,'2023_08_15_132601_invoices_tables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (627,'2023_08_17_150831_create_cash_histories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (628,'2023_08_23_205958_add_new_field_cash_register_invoices_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (629,'2023_08_28_173948_create_cash_register_cash_histories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (630,'2023_09_04_140615_add_field_open_cash_cash_register_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (631,'2023_10_03_193935_alter_table_permissions_roles_users_add_permissions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (632,'2023_10_04_162726_cash_register_assign_users',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (633,'2023_10_05_215312_add_new_field_cash_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (634,'2023_10_06_162800_add_new_field_cash_register_cash_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (635,'2023_10_06_171326_add_new_field_cash_histories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (636,'2023_10_18_212955_add_field_hold_invoice_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (637,'2023_10_24_144417_add_field_cash_register_history',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (638,'2023_11_16_203923_core_pos_histories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (639,'2023_12_04_093225_create_aux_vaults_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (640,'2023_12_04_153014_create_aux_vault_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (641,'2023_12_04_171204_update_paymentgateway_table_auxpay',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (642,'2023_12_05_140227_alter_name_for_payment_gateways_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (643,'2023_12_05_170922_update_core_pos_histories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (644,'2023_12_12_200041_add_new_fields_tips_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (645,'2023_12_12_202017_create_pos_tip_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (646,'2023_12_15_141626_create_hold_tables_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (647,'2023_12_21_213306_add_field_store_hold_invoice_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (648,'2023_12_22_152255_create_pos_stores_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (649,'2023_12_22_193316_create_pos_sections_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (650,'2023_12_26_195044_create_item_section_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (651,'2023_12_27_213002_add_field_store_id_expenses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (652,'2024_01_18_134852_add_field_status_payment_tax_group_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (653,'2024_02_01_115137_add_aux_id_to_payments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (654,'2024_02_01_131627_add_settings_invoice_late_fee_retroactive',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (655,'2024_02_08_191106_change_default_add_payment_gateway_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (656,'2024_02_12_170936_add_type_to_failed_payment_history_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (657,'2024_02_27_192219_alter_items_main_category',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (658,'2024_03_14_144821_add_a_c_h_fields_to_aux_vaults_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (659,'2024_03_19_192753_add_invoice_duedate_company_setting',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (660,'2024_03_20_120006_alter_transaction_type_in_aux_vaults_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (661,'2024_03_27_205505_modify_phone_number_in_aux_vaults_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (662,'2024_04_01_193952_update_password_fields_in_contacts_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (663,'2024_04_01_205457_add_paymentmehtodh_alter',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (664,'2024_05_03_190220_add_tickets_alter',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (665,'2024_05_08_173152_create_company_default_setting_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (666,'2024_05_16_150951_create_stripe_settings_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (667,'2025_03_08_145537_modify_pbx_services',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (668,'2024_05_27_205415_create_pbx_server_tenants_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (669,'2024_06_10_135158_add_end_period_fields_to_tables',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (670,'2024_06_20_143310_add_auto_provisioning_to_pbx_extensions_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (671,'2024_06_24_141043_add_dhcp_to_pbx_extensions_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (672,'2024_06_21_190412_add_end_period_services_to_invoices_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (673,'2024_06_24_175444_add_show_notes_table_field_to_payment_methods_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (674,'2024_06_26_165542_update_dhcp_default_value_in_pbx_extensions_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (675,'2024_06_28_015624_update_pg_images',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (676,'2024_06_28_154436_add_stripe_to_payment_gateways_enum',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (677,'2024_07_01_172714_add_verification_and_fee_fields_to_settings_tables',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (678,'2024_07_01_212243_update_dhcp_value_in_pbx_extension_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (679,'2024_07_01_212902_create_payment_gateways_fees_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (680,'2024_07_02_151316_add_company_id_to_payment_gateways_fees_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (681,'2024_07_02_165219_create_payment_payment_gateways_fee_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (682,'2024_07_02_175754_add_stripe_button_to_payment_methods_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (683,'2024_07_08_034058_add_verified_to_users_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (684,'2024_07_08_204548_add_sms_option_to_delivery_method_in_addresses_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (685,'2024_07_08_210254_add_name_column_to_payment_settings_tables',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (686,'2024_07_12_133418_add_dates_to_leads_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (687,'2024_07_12_135749_create_leads_notes_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (688,'2024_07_12_180547_add_leadnote_number_to_lead_notes_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (689,'2024_07_12_191854_add_company_id_to_leads_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (690,'2024_07_12_205635_add_deleted_at_and_timestamps_to_your_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (691,'2024_07_15_191528_add_public_to_note_tickets_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (692,'2024_07_16_144621_update_amount_in_payment_gateways_fees_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (693,'2024_07_16_164353_add_soft_deletes_to_payment_gateways_fees_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (694,'2024_07_18_165024_update_company_settings',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (695,'2024_07_26_203523_add_aux_vault_setting_id_to_aux_vaults_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (696,'2024_07_26_204754_create_transaction_fees_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (697,'2024_07_26_172944_updatecompaningsettingportal',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (698,'2024_07_29_153313_updatecompanysettings_customer_config',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (699,'2024_07_29_171823_add_permission_lead_notes_lead',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (700,'2024_07_29_192613_add_authorize_setting_id_to_authorize_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (701,'2024_08_13_000000_add_expires_at_to_personal_access_tokens_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (702,'2024_08_19_000000_rename_password_resets_table',8);
