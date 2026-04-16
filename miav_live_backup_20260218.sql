-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: miav_system_dev
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_assignments`
--

DROP TABLE IF EXISTS `asset_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned DEFAULT NULL,
  `employee_id` bigint unsigned DEFAULT NULL,
  `assigned_by` bigint unsigned DEFAULT NULL,
  `assignment_date` date DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'assigned',
  `assigned_at` datetime DEFAULT NULL,
  `expected_return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `returned_to` bigint unsigned DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `assignment_notes` text COLLATE utf8mb4_unicode_ci,
  `return_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `quantity_assigned` int DEFAULT '1',
  `condition_when_assigned` enum('new','good','fair','poor') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'good',
  `condition_when_returned` enum('new','good','fair','poor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_assignments_assigned_by_foreign` (`assigned_by`),
  KEY `asset_assignments_returned_to_foreign` (`returned_to`),
  CONSTRAINT `asset_assignments_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asset_assignments_returned_to_foreign` FOREIGN KEY (`returned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_assignments`
--

LOCK TABLES `asset_assignments` WRITE;
/*!40000 ALTER TABLE `asset_assignments` DISABLE KEYS */;
INSERT INTO `asset_assignments` VALUES (1,2,3,1,'2026-01-20','transferred',NULL,NULL,'2026-01-20',1,NULL,'non','Transferred to System Administrator. Reason: Equipment upgrade. noon','2026-01-20 19:29:08','2026-01-20 19:29:31',1,'good',NULL),(2,2,1,1,'2026-01-20','assigned',NULL,NULL,NULL,NULL,NULL,'Transferred from monah chimwamafuku. Reason: Equipment upgrade. noon',NULL,'2026-01-20 19:29:31','2026-01-20 19:29:31',1,'good',NULL);
/*!40000 ALTER TABLE `asset_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_categories`
--

DROP TABLE IF EXISTS `asset_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2196f3',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `requires_individual_entry` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_categories_sort_order_index` (`sort_order`),
  KEY `asset_categories_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_categories`
--

LOCK TABLES `asset_categories` WRITE;
/*!40000 ALTER TABLE `asset_categories` DISABLE KEYS */;
INSERT INTO `asset_categories` VALUES (1,'Hardware','Computer hardware, laptops, monitors, peripherals','💻','#2196f3',1,1,0,'2026-01-13 09:42:07','2026-01-13 09:42:07'),(2,'Software','Software licenses, applications, subscriptions','⚙️','#4caf50',2,1,0,'2026-01-13 09:42:07','2026-01-13 09:42:07'),(3,'Office Supplies','Stationery, printing supplies, office equipment','📝','#ff9800',3,1,0,'2026-01-13 09:42:07','2026-01-13 09:42:07'),(4,'Mobile Devices','Phones, tablets, mobile accessories','📱','#9c27b0',4,1,0,'2026-01-13 09:42:07','2026-01-13 09:42:07'),(5,'Furniture','Office furniture, chairs, desks','🪑','#795548',5,1,0,'2026-01-13 09:42:07','2026-01-13 09:42:07'),(6,'Networking','Network equipment, cables, routers','🌐','#607d8b',6,1,0,'2026-01-13 09:42:07','2026-01-13 09:42:07'),(7,'Vehicles','Company vehicles, cars, trucks, motorcycles','🚗','#f44336',7,1,1,'2026-01-13 09:42:07','2026-01-13 09:42:07');
/*!40000 ALTER TABLE `asset_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_category_fields`
--

DROP TABLE IF EXISTS `asset_category_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_category_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_category_id` bigint unsigned NOT NULL,
  `field_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_type` enum('text','number','date','select','textarea','email','url','tel') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `validation_rules` json DEFAULT NULL,
  `options` json DEFAULT NULL,
  `placeholder_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `help_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_category_fields_asset_category_id_field_name_unique` (`asset_category_id`,`field_name`),
  KEY `asset_category_fields_asset_category_id_display_order_index` (`asset_category_id`,`display_order`),
  CONSTRAINT `asset_category_fields_asset_category_id_foreign` FOREIGN KEY (`asset_category_id`) REFERENCES `asset_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_category_fields`
--

LOCK TABLES `asset_category_fields` WRITE;
/*!40000 ALTER TABLE `asset_category_fields` DISABLE KEYS */;
INSERT INTO `asset_category_fields` VALUES (1,7,'license_plate','License Plate / Number Plate','text',1,NULL,NULL,'e.g., KAA 123A','Vehicle license plate number',1,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(2,7,'make','Make','text',1,NULL,NULL,'e.g., Toyota, Nissan',NULL,2,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(3,7,'model','Model','text',1,NULL,NULL,'e.g., Hilux, Patrol',NULL,3,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(4,7,'year','Year','number',1,'\"{\\\"min\\\":1900,\\\"max\\\":2030}\"',NULL,'e.g., 2023',NULL,4,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(5,7,'fuel_type','Fuel Type','select',0,NULL,'\"[\\\"Petrol\\\",\\\"Diesel\\\",\\\"Electric\\\",\\\"Hybrid\\\"]\"',NULL,NULL,5,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(6,7,'registration_date','Registration Date','date',0,NULL,NULL,NULL,NULL,6,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(7,7,'insurance_expiry','Insurance Expiry Date','date',0,NULL,NULL,NULL,'When insurance expires',7,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(8,7,'insurance_provider','Insurance Provider','text',0,NULL,NULL,'e.g., Jubilee Insurance',NULL,8,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(9,5,'item_type','Item Type','select',1,NULL,'\"[\\\"Desk\\\",\\\"Chair\\\",\\\"Cabinet\\\",\\\"Table\\\",\\\"Shelf\\\",\\\"Other\\\"]\"',NULL,NULL,1,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(10,5,'purchase_date','Purchase Date','date',0,NULL,NULL,NULL,NULL,2,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(11,5,'warranty_expiry','Warranty Expiry Date','date',0,NULL,NULL,NULL,NULL,3,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(12,5,'condition','Condition','select',0,NULL,'\"[\\\"New\\\",\\\"Good\\\",\\\"Fair\\\",\\\"Poor\\\"]\"',NULL,NULL,4,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(13,2,'license_type','License Type','select',1,NULL,'\"[\\\"Insurance\\\",\\\"Road License\\\",\\\"Operating Permit\\\",\\\"NTSA\\\",\\\"Software License\\\",\\\"Other\\\"]\"',NULL,NULL,1,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(14,2,'license_number','License Number','text',1,NULL,NULL,'e.g., INS-2024-12345',NULL,2,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(15,2,'issuing_authority','Issuing Authority','text',0,NULL,NULL,'e.g., Jubilee Insurance, NTSA',NULL,3,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(16,2,'issue_date','Issue Date','date',0,NULL,NULL,NULL,NULL,4,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(17,2,'expiry_date','Expiry Date','date',1,NULL,NULL,NULL,'When this license expires',5,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(18,2,'renewal_date','Renewal Date','date',0,NULL,NULL,NULL,'When to renew this license',6,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(19,2,'license_holder','License Holder','text',0,NULL,NULL,'Company or person name',NULL,7,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(20,2,'coverage_details','Coverage Details','textarea',0,NULL,NULL,'Details about what this license covers',NULL,8,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(21,2,'premium_amount','Premium Amount','number',0,NULL,NULL,'Annual premium or license cost',NULL,9,1,'2026-01-21 10:17:45','2026-01-21 10:17:45'),(22,2,'payment_frequency','Payment Frequency','select',0,NULL,'\"[\\\"Annual\\\",\\\"Monthly\\\",\\\"Quarterly\\\",\\\"One-time\\\"]\"',NULL,NULL,10,1,'2026-01-21 10:17:45','2026-01-21 10:17:45');
/*!40000 ALTER TABLE `asset_category_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_relationships`
--

DROP TABLE IF EXISTS `asset_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_relationships` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_asset_id` bigint unsigned NOT NULL,
  `related_asset_id` bigint unsigned NOT NULL,
  `relationship_type` enum('has_insurance','has_license','has_permit','requires','depends_on','linked_to','attached_to') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'linked_to',
  `metadata` json DEFAULT NULL,
  `starts_at` date DEFAULT NULL,
  `expires_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_relationship_unique` (`parent_asset_id`,`related_asset_id`,`relationship_type`),
  KEY `asset_relationships_parent_asset_id_is_active_index` (`parent_asset_id`,`is_active`),
  KEY `asset_relationships_related_asset_id_is_active_index` (`related_asset_id`,`is_active`),
  KEY `asset_relationships_expires_at_index` (`expires_at`),
  CONSTRAINT `asset_relationships_parent_asset_id_foreign` FOREIGN KEY (`parent_asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asset_relationships_related_asset_id_foreign` FOREIGN KEY (`related_asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_relationships`
--

LOCK TABLES `asset_relationships` WRITE;
/*!40000 ALTER TABLE `asset_relationships` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_relationships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_request_items`
--

DROP TABLE IF EXISTS `asset_request_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_request_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_request_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `quantity_requested` int NOT NULL,
  `quantity_approved` int NOT NULL DEFAULT '0',
  `quantity_fulfilled` int NOT NULL DEFAULT '0',
  `unit_price_at_request` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `special_requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `item_status` enum('pending','approved','partially_approved','rejected','fulfilled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_request_items_asset_request_id_index` (`asset_request_id`),
  KEY `asset_request_items_asset_id_index` (`asset_id`),
  KEY `asset_request_items_item_status_index` (`item_status`),
  CONSTRAINT `asset_request_items_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asset_request_items_asset_request_id_foreign` FOREIGN KEY (`asset_request_id`) REFERENCES `asset_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_request_items`
--

LOCK TABLES `asset_request_items` WRITE;
/*!40000 ALTER TABLE `asset_request_items` DISABLE KEYS */;
INSERT INTO `asset_request_items` VALUES (1,1,1,1,1,0,10.00,10.00,NULL,'approved','Requested via mobile app','2026-01-14 16:02:29','2026-01-20 19:01:04'),(2,2,1,1,1,0,10.00,10.00,NULL,'approved',NULL,'2026-01-20 19:05:09','2026-01-20 19:24:59');
/*!40000 ALTER TABLE `asset_request_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_requests`
--

DROP TABLE IF EXISTS `asset_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `request_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `status` enum('draft','pending','approved','rejected','fulfilled','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `priority` enum('low','normal','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `business_justification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `needed_by_date` date DEFAULT NULL,
  `delivery_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `total_estimated_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_center` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fulfilled_by` bigint unsigned DEFAULT NULL,
  `fulfilled_at` timestamp NULL DEFAULT NULL,
  `fulfillment_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_requests_request_number_unique` (`request_number`),
  KEY `asset_requests_fulfilled_by_foreign` (`fulfilled_by`),
  KEY `asset_requests_status_index` (`status`),
  KEY `asset_requests_employee_id_index` (`employee_id`),
  KEY `asset_requests_approved_by_index` (`approved_by`),
  KEY `asset_requests_created_at_index` (`created_at`),
  CONSTRAINT `asset_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asset_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asset_requests_fulfilled_by_foreign` FOREIGN KEY (`fulfilled_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_requests`
--

LOCK TABLES `asset_requests` WRITE;
/*!40000 ALTER TABLE `asset_requests` DISABLE KEYS */;
INSERT INTO `asset_requests` VALUES (1,'REQ-20260114-0001',3,'approved','low','xxx xxx xxx xxx xxx xxx xxx xxx','2026-01-15',NULL,10.00,NULL,NULL,3,'2026-01-20 19:01:04','come and collect',NULL,NULL,NULL,NULL,'2026-01-14 16:02:29','2026-01-20 19:01:04'),(2,'REQ-2026-002',3,'approved','normal','trial','2026-01-22','none',10.00,NULL,NULL,3,'2026-01-20 19:24:59','come collect',NULL,NULL,NULL,NULL,'2026-01-20 19:05:09','2026-01-20 19:24:59');
/*!40000 ALTER TABLE `asset_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `stock_quantity` int NOT NULL DEFAULT '0',
  `assigned_quantity` int DEFAULT '0',
  `min_stock_level` int NOT NULL DEFAULT '0',
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specifications` json DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','discontinued','available','in-use','under-repair','maintenance','retired') COLLATE utf8mb4_unicode_ci DEFAULT 'available',
  `is_requestable` tinyint(1) NOT NULL DEFAULT '1',
  `requires_approval` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assets_sku_unique` (`sku`),
  KEY `assets_category_index` (`category`),
  KEY `assets_status_index` (`status`),
  KEY `assets_is_requestable_index` (`is_requestable`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
INSERT INTO `assets` VALUES (1,'trial',NULL,'Mobile Devices','trial','trial',10.00,'USD',100,0,10,NULL,NULL,'[]',NULL,'available',1,1,NULL,'2026-01-12 13:40:43','2026-01-12 13:40:43'),(2,'company vihicle',NULL,'Vehicles','toyota','corrolla',0.00,'USD',1,3,3,NULL,NULL,'{\"fuel_type\": \"Hybrid\", \"vin_number\": \"trial\", \"vehicle_year\": \"2014\", \"license_plate\": \"AGE123\"}',NULL,'available',1,1,NULL,'2026-01-13 10:07:10','2026-01-20 19:29:31');
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `business_licenses`
--

DROP TABLE IF EXISTS `business_licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_licenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `priority_level` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `renewal_cost` decimal(10,2) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `renewal_date` date DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `responsible_employee_id` bigint unsigned DEFAULT NULL,
  `creator_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `license_direction` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_expiry_date` (`expiry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `business_licenses`
--

LOCK TABLES `business_licenses` WRITE;
/*!40000 ALTER TABLE `business_licenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_licenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('revival-tech-dev-cache-77de68daecd823babbb58edb1c8e14d7106e83bb','i:8;',1771244691),('revival-tech-dev-cache-77de68daecd823babbb58edb1c8e14d7106e83bb:timer','i:1771244691;',1771244691);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2196f3',
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_type_is_active_index` (`type`,`is_active`),
  KEY `categories_sort_order_index` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'asset_category','Computer Equipment','computer-equipment','Laptops, desktops, monitors','#2196F3','computer',1,1,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(2,'asset_category','Office Furniture','office-furniture','Desks, chairs, cabinets','#4CAF50','chair',1,2,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(3,'asset_category','Mobile Devices','mobile-devices','Phones, tablets','#FF9800','phone',1,3,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(4,'asset_status','Available','available','Ready for assignment','#4CAF50','check-circle',1,1,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(5,'asset_status','In Use','in-use','Currently assigned','#2196F3','user',1,2,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(6,'asset_status','Under Repair','under-repair','Being repaired','#FF9800','wrench',1,3,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(7,'terminal_status','Active','active','Terminal is operational','#4CAF50','check',1,1,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(8,'terminal_status','Offline','offline','Terminal is not responding','#F44336','x-circle',1,2,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(9,'terminal_status','Maintenance','maintenance','Under maintenance','#FF9800','tool',1,3,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(10,'service_type','Installation','installation','New terminal installation','#2196F3','download',1,1,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40'),(11,'service_type','Repair','repair','Terminal repair service','#FF9800','wrench',1,2,NULL,'2026-01-09 13:03:40','2026-01-09 13:03:40');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `industry` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_size` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `annual_revenue` decimal(15,2) DEFAULT NULL,
  `contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','prospect','lost') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'prospect',
  `priority` enum('high','medium','low') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `lead_source` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `acquired_date` date DEFAULT NULL,
  `last_contact_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES (1,'ENV1652','EnviroGas Private Limited','EnviroGas Private Limited','harare',NULL,NULL,NULL,'monah chimwamafuku','monahchimwamafuku@gmail.com','+263782509556','24\r\nCecil Rhodes','Newlands',NULL,NULL,NULL,'active','medium',NULL,NULL,NULL,NULL,'2026-01-12 13:31:04','2026-01-12 13:31:04','2026-01-12',NULL),(2,'TRI0636','Trial Client','Trial Client','harare',NULL,NULL,NULL,'Ingeniouss','ignenius@gmail.com','+263782509556','24 Cecil Rhodes dr Newlands Harare','Harare',NULL,NULL,NULL,'active','medium',NULL,NULL,NULL,NULL,'2026-01-12 14:10:35','2026-01-12 14:10:35',NULL,NULL),(3,'TES2204','test client','test client','harare',NULL,NULL,NULL,'john doe','test@example.com','+263782509556','28heron drive ridgeview belvedere\r\n28','Harare',NULL,NULL,NULL,'active','medium',NULL,NULL,NULL,NULL,'2026-01-20 19:19:31','2026-01-20 19:19:31','2026-01-20',NULL);
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `data_imports`
--

DROP TABLE IF EXISTS `data_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_imports`
--

LOCK TABLES `data_imports` WRITE;
/*!40000 ALTER TABLE `data_imports` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_imports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Information Technology','IT and Technical Support','2026-01-09 07:47:39','2026-01-09 07:47:39'),(2,'Finance & Accounting','Financial Operations and Accounting','2026-01-09 07:47:39','2026-01-09 07:47:39'),(3,'Human Resources','HR and Employee Management','2026-01-09 07:47:39','2026-01-09 07:47:39'),(4,'Operations','Day-to-day Operations','2026-01-09 07:47:39','2026-01-09 07:47:39');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_permissions`
--

DROP TABLE IF EXISTS `employee_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_permissions`
--

LOCK TABLES `employee_permissions` WRITE;
/*!40000 ALTER TABLE `employee_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_role`
--

DROP TABLE IF EXISTS `employee_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_role_employee_id_role_id_unique` (`employee_id`,`role_id`),
  KEY `employee_role_employee_id_index` (`employee_id`),
  KEY `employee_role_role_id_index` (`role_id`),
  CONSTRAINT `employee_role_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_role`
--

LOCK TABLES `employee_role` WRITE;
/*!40000 ALTER TABLE `employee_role` DISABLE KEYS */;
INSERT INTO `employee_role` VALUES (1,1,1,'2026-01-14 20:40:42','2026-01-14 20:40:42'),(2,3,3,'2026-01-14 20:40:42','2026-01-14 20:40:42'),(4,1,3,'2026-01-14 20:41:31','2026-01-14 20:41:31'),(5,4,1,'2026-01-20 19:43:44','2026-01-20 19:43:44');
/*!40000 ALTER TABLE `employee_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_roles`
--

DROP TABLE IF EXISTS `employee_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_roles_employee_id_role_id_unique` (`employee_id`,`role_id`),
  KEY `employee_roles_role_id_foreign` (`role_id`),
  CONSTRAINT `employee_roles_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_roles`
--

LOCK TABLES `employee_roles` WRITE;
/*!40000 ALTER TABLE `employee_roles` DISABLE KEYS */;
INSERT INTO `employee_roles` VALUES (1,1,1,NULL,NULL);
/*!40000 ALTER TABLE `employee_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `manager_id` bigint unsigned DEFAULT NULL,
  `time_zone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTC',
  `language` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','inactive','suspended') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relationship` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `skills` json DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `avatar_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  UNIQUE KEY `employees_email_unique` (`email`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_role_id_foreign` (`role_id`),
  KEY `employees_manager_id_foreign` (`manager_id`),
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'EMP0001','EMP0001','System','Administrator','admin@miav.com','2026-01-09 07:47:39','$2y$12$IOgiucX2r.pknLBKrhKv6uj8GZzrsYSg1/SfZmFVLjge1z7zMj6fK','+263712345678','IT','System Administrator',1,1,NULL,'Africa/Harare','en',0,'active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2024-01-01',NULL,'2026-02-17 11:38:45','ewhJlVlfbCkJvUKYKzeOfdCAWillU5zxux439GKBE2NoQqMM0NL4x2qa3fOU','2026-01-09 07:47:39','2026-02-17 11:38:45'),(3,'INF260001','INF260001','monah','chimwamafuku','monahchimwamafuku@gmail.com',NULL,'$2y$12$nWnlM21r/CNW5B54QvmmPeprrYLA4ChjwIObM.qNWqLC2b5UFtEDq','+263782509556','Information Technology','technician',1,3,NULL,'UTC','en',0,'active','28heron drive ridgeview belvedere\r\n28','Harare','harare','Zimbabwe','1234',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-12',NULL,'2026-02-16 07:03:52',NULL,'2026-01-12 14:06:00','2026-02-16 07:03:52'),(4,'OPE260001','OPE260001','test employee','smith','test@example.com',NULL,'$2y$12$fkOTrHc3TX/reN8/CpPVLe97xh/hm4NNSxBJ3BHIQpCN3WlgXv4L2','+263782509556','Operations',NULL,4,1,NULL,'UTC','en',1,'active','28heron drive ridgeview belvedere\r\n28','Harare','harare','Zimbabwe','1234',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-20',NULL,'2026-01-20 19:44:56',NULL,'2026-01-20 19:43:44','2026-01-20 19:45:31');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
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
-- Table structure for table `job_assignments`
--

DROP TABLE IF EXISTS `job_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assignment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `technician_id` bigint unsigned DEFAULT NULL,
  `region_id` bigint unsigned DEFAULT NULL,
  `pos_terminals` json DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `service_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('low','normal','high','emergency') COLLATE utf8mb4_unicode_ci DEFAULT 'normal',
  `status` enum('assigned','in_progress','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'assigned',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `estimated_duration_hours` decimal(5,2) DEFAULT NULL,
  `actual_start_time` datetime DEFAULT NULL,
  `actual_end_time` datetime DEFAULT NULL,
  `completion_notes` text COLLATE utf8mb4_unicode_ci,
  `completed_date` date DEFAULT NULL,
  `assignment_history` json DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_assignments`
--

LOCK TABLES `job_assignments` WRITE;
/*!40000 ALTER TABLE `job_assignments` DISABLE KEYS */;
INSERT INTO `job_assignments` VALUES (1,'ASN-20260112-001','2026-01-12 18:42:34','2026-01-12 18:42:34',3,NULL,'[\"1\", \"11\", \"21\", \"31\", \"41\", \"51\", \"61\", \"71\", \"81\", \"91\", \"101\", \"111\", \"121\", \"131\", \"141\", \"151\", \"161\", \"171\", \"181\", \"191\"]',1,2,'2026-01-13','routine_maintenance','normal','assigned','Deployment assignment - individual mode',NULL,NULL,NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `job_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_07_21_202636_create_departments_table',1),(5,'2025_07_21_202650_create_roles_table',1),(6,'2025_07_21_202723_create_employees_table',1),(7,'2025_07_21_205749_create_clients_table',1),(8,'2025_07_21_205749_create_regions_table',1),(9,'2025_07_21_205751_create_pos_terminals_table',1),(10,'2025_07_21_210015_create_job_assignments_table',1),(11,'2025_07_21_210017_create_service_reports_table',1),(12,'2025_07_21_210018_create_tickets_table',1),(13,'2025_07_21_210019_create_data_imports_table',1),(14,'2025_07_21_210026_create_activity_logs_table',1),(15,'2025_07_21_210322_create_technicians_table',1),(16,'2025_07_22_130331_create_assets_table',1),(17,'2025_07_22_130410_create_asset_requests_table',1),(18,'2025_07_22_130452_create_asset_request_items_table',1),(19,'2025_07_22_130523_create_asset_categories_table',1),(20,'2025_07_22_231829_add_industry_column_to_clients_table',1),(21,'2025_07_22_232009_add_missing_columns_to_clients_table',1),(22,'2025_07_23_081035_create_personal_access_tokens_table',1),(23,'2025_07_23_122923_create_permissions_table',1),(24,'2025_07_23_123046_update_employees_table_for_onboarding',1),(25,'2025_07_23_123130_create_employee_permissions_table',1),(26,'2025_07_23_123158_create_employee_roles_table',1),(27,'2025_07_23_123230_create_role_permissions_table',1),(28,'2026_01_05_120000_add_permissions_to_roles_table',1),(29,'2026_01_09_000001_create_categories_table',2),(31,'2026_01_09_000002_create_projects_table',3),(32,'2026_01_09_000003_create_visits_table',4),(33,'2026_01_09_000004_add_columns_to_tickets_table',5),(35,'2026_01_13_090028_add_merchant_id_to_pos_terminals_table',6),(36,'2026_01_13_090001_create_project_terminals_table',7),(37,'2026_01_09_000000_create_sessions_table',8),(38,'2026_01_10_000001_add_status_to_job_assignments_table',8),(39,'2026_01_12_000001_add_missing_columns_to_job_assignments',8),(40,'2026_01_12_000002_add_missing_columns_to_clients',8),(41,'2026_01_12_000003_make_employee_id_nullable',8),(42,'2026_01_12_000004_fix_clients_and_assets_tables',8),(43,'2026_01_12_000005_make_employee_department_nullable',8),(44,'2026_01_12_000006_update_asset_status_enum_values',8),(45,'2026_01_12_000007_make_employee_position_nullable',8),(46,'2026_01_12_000008_create_spatie_permission_tables',8),(47,'2026_01_13_090956_add_is_active_to_project_terminals_table',8),(48,'2026_01_13_092513_create_asset_category_fields_table',9),(49,'2026_01_13_092607_create_asset_relationships_table',9),(50,'2026_01_14_000001_add_view_own_data_permission',10),(51,'2026_01_14_000002_add_technician_permissions',11),(52,'2026_01_14_000003_add_view_assets_permission',12),(53,'2026_01_14_000004_update_technician_role_permissions_json',13),(54,'2026_01_14_000005_migrate_permissions_json_to_pivot_table',14),(55,'2026_01_14_000006_add_missing_technician_permissions',15),(56,'2026_01_14_100000_create_employee_role_pivot_table',16),(57,'2026_01_14_100001_assign_technician_role_to_all_employees',17),(58,'2026_01_14_110000_grant_report_builder_to_all_roles',17),(59,'2026_01_20_192605_add_missing_columns_to_asset_assignments_table',18),(60,'2026_01_20_193821_add_outcome_column_to_technician_visits_table',19),(61,'2026_01_21_082114_add_new_employee_onboarding_fields_to_employees_table',20),(62,'2026_01_21_094919_add_requires_individual_entry_to_asset_categories_table',21),(63,'2026_01_21_add_ticket_type_and_assignment_type',21),(64,'2026_01_21_150000_create_visit_terminals_table',22),(65,'2026_01_21_add_staged_resolution_system',23);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`),
  KEY `permissions_category_index` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view_report_builder','View Report Builder','Access to the report builder interface','reports',NULL,NULL),(2,'use-report-builder','Use Report Builder','Access and use the report builder tool','reports','2026-01-10 02:12:28','2026-01-10 02:12:28'),(3,'manage-report-templates','Manage Report Templates','Create and manage report templates','reports','2026-01-10 02:12:28','2026-01-10 02:12:28'),(4,'preview-reports','Preview Reports','Preview generated reports','reports','2026-01-10 02:12:28','2026-01-10 02:12:28'),(5,'export-reports','Export Reports','Export reports to various formats','reports','2026-01-10 02:12:28','2026-01-10 02:12:28'),(6,'view_own_data','View Own Data','View personal information','general','2026-01-14 07:15:03','2026-01-14 07:27:23'),(7,'view_jobs','View Jobs','View technical jobs/tickets','technical','2026-01-14 07:19:03','2026-01-14 07:27:23'),(8,'view_terminals','View Terminals','View POS terminals','technical','2026-01-14 07:19:03','2026-01-14 07:27:23'),(9,'update_terminals','Update Terminals','Update terminal information','technical','2026-01-14 07:19:03','2026-01-14 07:27:23'),(10,'view_clients','View Clients','View client information','clients','2026-01-14 07:19:03','2026-01-14 07:19:03'),(11,'view_assets','View Assets','View company assets','assets','2026-01-14 07:24:20','2026-01-14 07:24:20'),(12,'all','All Permissions','Full system access','admin','2026-01-14 07:27:23','2026-01-14 07:27:23'),(13,'view_dashboard','View Dashboard','Access main dashboard','general','2026-01-14 07:27:23','2026-01-14 07:27:23'),(14,'manage_assets','Manage Assets','Full asset management','assets','2026-01-14 07:27:23','2026-01-14 07:27:23'),(15,'request_assets','Request Assets','Request company assets','assets','2026-01-14 07:27:23','2026-01-14 07:27:23'),(16,'approve_requests','Approve Requests','Approve asset requests','assets','2026-01-14 07:27:23','2026-01-14 07:27:23'),(17,'manage_clients','Manage Clients','Full client management','clients','2026-01-14 07:27:23','2026-01-14 07:27:23'),(18,'manage_team','Manage Team','Manage team members','management','2026-01-14 07:27:23','2026-01-14 07:27:23'),(19,'view_reports','View Reports','Access reporting system','management','2026-01-14 07:27:23','2026-01-14 07:27:23'),(20,'manage_terminals','Manage Terminals','Manage POS terminals','technical','2026-01-14 07:27:23','2026-01-14 07:27:23'),(21,'update_jobs','Update Jobs','Update job information','technical','2026-01-14 07:34:56','2026-01-14 07:34:56'),(22,'create_reports','Create Reports','Create new reports','technical','2026-01-14 07:34:56','2026-01-14 07:34:56'),(23,'use_report_builder','Use Report Builder','Access report builder interface','reports','2026-01-14 20:41:32','2026-01-14 20:41:32'),(24,'view_employees','View Employees','View employee list and details','employees','2026-01-14 20:59:07','2026-01-14 20:59:07'),(25,'manage_employees','Manage Employees','Create, edit, and delete employees','employees','2026-01-14 20:59:07','2026-01-14 20:59:07');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
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
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\Employee',1,'mobile-app-token','e51b4932f269e09d448461a95a79264708b1305359334ce560ad194d9a1da44a','[\"*\"]',NULL,NULL,'2026-01-10 09:45:26','2026-01-10 09:45:26'),(2,'App\\Models\\Employee',1,'mobile-app-token','3e0a35800b05e727595f0d4f5f2e1486665512afd56f38e47d0ab1fa6ffeecd1','[\"*\"]',NULL,NULL,'2026-01-10 10:31:46','2026-01-10 10:31:46'),(3,'App\\Models\\Employee',1,'mobile-app-token','88cb2c9eca474e0f5d4fc3d26f269b9f54186caaf21d6b6565cdddbe7152f251','[\"*\"]',NULL,NULL,'2026-01-12 09:05:25','2026-01-12 09:05:25'),(4,'App\\Models\\Employee',1,'mobile-app-token','67c15155b59e39c299985d5167493aeab664820f210fedaad3aabb8c5de8c1f8','[\"*\"]',NULL,NULL,'2026-01-12 09:07:33','2026-01-12 09:07:33'),(5,'App\\Models\\Employee',1,'mobile-app-token','5e7d95bfaf22d98f473963f8046d28d21f70f1170db4614366274ec2cccc8265','[\"*\"]','2026-01-12 11:25:06',NULL,'2026-01-12 09:25:13','2026-01-12 11:25:06'),(8,'App\\Models\\Employee',3,'mobile-app-token','40b18a31014c8bc83fed7a538fbacb8c93f7d6033167788547ea2fc2b00155f1','[\"*\"]','2026-01-14 10:18:08',NULL,'2026-01-14 10:17:50','2026-01-14 10:18:08'),(10,'App\\Models\\Employee',3,'mobile-app-token','7e00799014816674cf895d98aedff2ce8605e2d840649ef29be014968a212dec','[\"*\"]','2026-01-20 18:00:47',NULL,'2026-01-20 17:37:10','2026-01-20 18:00:47'),(12,'App\\Models\\Employee',3,'mobile-app-token','9bfb5df9b493e7d3f2f6683e2e1caa55649cc250d5961272e897a4c37a9ae2af','[\"*\"]','2026-01-21 12:21:26',NULL,'2026-01-21 10:06:24','2026-01-21 12:21:26'),(13,'App\\Models\\Employee',3,'mobile-app-token','07280a8b20f7c6d406c32a36d0ef556c16584491ebaa361634d023ec60129e0a','[\"*\"]','2026-02-16 12:24:03',NULL,'2026-02-16 06:47:39','2026-02-16 12:24:03');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pos_terminals`
--

DROP TABLE IF EXISTS `pos_terminals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos_terminals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `terminal_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `merchant_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` bigint unsigned NOT NULL,
  `merchant_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `merchant_contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `physical_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `installation_date` date DEFAULT NULL,
  `terminal_model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('active','offline','maintenance','faulty','decommissioned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_service_date` date DEFAULT NULL,
  `next_service_due` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `current_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pos_terminals_terminal_id_unique` (`terminal_id`),
  KEY `pos_terminals_client_id_index` (`client_id`),
  KEY `pos_terminals_status_index` (`status`),
  KEY `pos_terminals_region_index` (`region`),
  CONSTRAINT `pos_terminals_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=501 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pos_terminals`
--

LOCK TABLES `pos_terminals` WRITE;
/*!40000 ALTER TABLE `pos_terminals` DISABLE KEYS */;
INSERT INTO `pos_terminals` VALUES (1,'T-2001',NULL,1,'Merchant 1','Contact Person 1','+263770000001','merchant1@example.com','1 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-02','iWL252','SN00001',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(2,'T-2002',NULL,1,'Merchant 2','Contact Person 2','+263770000002','merchant2@example.com','2 Main Street','Region 3','Rusape','Manicaland',NULL,'Retail','2025-01-03','VX-520','SN00002',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(3,'T-2003',NULL,1,'Merchant 3','Contact Person 3','+263770000003','merchant3@example.com','3 Main Street','Region 1','Bindura','Mashonaland Central',NULL,'Services','2025-01-04','iWL252','SN00003',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(4,'T-2004',NULL,1,'Merchant 4','Contact Person 4','+263770000004','merchant4@example.com','4 Main Street','Region 2','Murehwa','Mashonaland East',NULL,'Retail','2025-01-05','VX-520','SN00004',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(5,'T-2005',NULL,1,'Merchant 5','Contact Person 5','+263770000005','merchant5@example.com','5 Main Street','Region 3','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-06','iWL252','SN00005',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(6,'T-2006',NULL,1,'Merchant 6','Contact Person 6','+263770000006','merchant6@example.com','6 Main Street','Region 1','Chiredzi','Masvingo',NULL,'Retail','2025-01-07','VX-520','SN00006',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(7,'T-2007',NULL,1,'Merchant 7','Contact Person 7','+263770000007','merchant7@example.com','7 Main Street','Region 2','Kwekwe','Midlands',NULL,'Services','2025-01-08','iWL252','SN00007',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(8,'T-2008',NULL,1,'Merchant 8','Contact Person 8','+263770000008','merchant8@example.com','8 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-09','VX-520','SN00008',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(9,'T-2009',NULL,1,'Merchant 9','Contact Person 9','+263770000009','merchant9@example.com','9 Main Street','Region 1','Beitbridge','Matabeleland South',NULL,'Services','2025-01-10','iWL252','SN00009',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(10,'T-2010',NULL,1,'Merchant 10','Contact Person 10','+263770000010','merchant10@example.com','10 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-11','VX-520','SN00010',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(11,'T-2011',NULL,1,'Merchant 11','Contact Person 11','+263770000011','merchant11@example.com','11 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-12','iWL252','SN00011',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(12,'T-2012',NULL,1,'Merchant 12','Contact Person 12','+263770000012','merchant12@example.com','12 Main Street','Region 1','Chipinge','Manicaland',NULL,'Retail','2025-01-13','VX-520','SN00012',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(13,'T-2013',NULL,1,'Merchant 13','Contact Person 13','+263770000013','merchant13@example.com','13 Main Street','Region 2','Mazowe','Mashonaland Central',NULL,'Services','2025-01-14','iWL252','SN00013',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(14,'T-2014',NULL,1,'Merchant 14','Contact Person 14','+263770000014','merchant14@example.com','14 Main Street','Region 3','Mutoko','Mashonaland East',NULL,'Retail','2025-01-15','VX-520','SN00014',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(15,'T-2015',NULL,1,'Merchant 15','Contact Person 15','+263770000015','merchant15@example.com','15 Main Street','Region 1','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-16','iWL252','SN00015',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(16,'T-2016',NULL,1,'Merchant 16','Contact Person 16','+263770000016','merchant16@example.com','16 Main Street','Region 2','Masvingo','Masvingo',NULL,'Retail','2025-01-17','VX-520','SN00016',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(17,'T-2017',NULL,1,'Merchant 17','Contact Person 17','+263770000017','merchant17@example.com','17 Main Street','Region 3','Gweru','Midlands',NULL,'Services','2025-01-18','iWL252','SN00017',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(18,'T-2018',NULL,1,'Merchant 18','Contact Person 18','+263770000018','merchant18@example.com','18 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-19','VX-520','SN00018',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(19,'T-2019',NULL,1,'Merchant 19','Contact Person 19','+263770000019','merchant19@example.com','19 Main Street','Region 2','Gwanda','Matabeleland South',NULL,'Services','2025-01-20','iWL252','SN00019',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(20,'T-2020',NULL,1,'Merchant 20','Contact Person 20','+263770000020','merchant20@example.com','20 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-21','VX-520','SN00020',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(21,'T-2021',NULL,1,'Merchant 21','Contact Person 21','+263770000021','merchant21@example.com','21 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-22','iWL252','SN00021',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(22,'T-2022',NULL,1,'Merchant 22','Contact Person 22','+263770000022','merchant22@example.com','22 Main Street','Region 2','Rusape','Manicaland',NULL,'Retail','2025-01-23','VX-520','SN00022',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(23,'T-2023',NULL,1,'Merchant 23','Contact Person 23','+263770000023','merchant23@example.com','23 Main Street','Region 3','Mazowe','Mashonaland Central',NULL,'Services','2025-01-24','iWL252','SN00023',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(24,'T-2024',NULL,1,'Merchant 24','Contact Person 24','+263770000024','merchant24@example.com','24 Main Street','Region 1','Murehwa','Mashonaland East',NULL,'Retail','2025-01-25','VX-520','SN00024',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(25,'T-2025',NULL,1,'Merchant 25','Contact Person 25','+263770000025','merchant25@example.com','25 Main Street','Region 2','Kariba','Mashonaland West',NULL,'Services','2025-01-26','iWL252','SN00025',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(26,'T-2026',NULL,1,'Merchant 26','Contact Person 26','+263770000026','merchant26@example.com','26 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-27','VX-520','SN00026',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(27,'T-2027',NULL,1,'Merchant 27','Contact Person 27','+263770000027','merchant27@example.com','27 Main Street','Region 1','Zvishavane','Midlands',NULL,'Services','2025-01-28','iWL252','SN00027',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(28,'T-2028',NULL,1,'Merchant 28','Contact Person 28','+263770000028','merchant28@example.com','28 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-01','VX-520','SN00028',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(29,'T-2029',NULL,1,'Merchant 29','Contact Person 29','+263770000029','merchant29@example.com','29 Main Street','Region 3','Gwanda','Matabeleland South',NULL,'Services','2025-01-02','iWL252','SN00029',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(30,'T-2030',NULL,1,'Merchant 30','Contact Person 30','+263770000030','merchant30@example.com','30 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-03','VX-520','SN00030',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(31,'T-2031',NULL,1,'Merchant 31','Contact Person 31','+263770000031','merchant31@example.com','31 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-04','iWL252','SN00031',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(32,'T-2032',NULL,1,'Merchant 32','Contact Person 32','+263770000032','merchant32@example.com','32 Main Street','Region 3','Mutare','Manicaland',NULL,'Retail','2025-01-05','VX-520','SN00032',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(33,'T-2033',NULL,1,'Merchant 33','Contact Person 33','+263770000033','merchant33@example.com','33 Main Street','Region 1','Bindura','Mashonaland Central',NULL,'Services','2025-01-06','iWL252','SN00033',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(34,'T-2034',NULL,1,'Merchant 34','Contact Person 34','+263770000034','merchant34@example.com','34 Main Street','Region 2','Mutoko','Mashonaland East',NULL,'Retail','2025-01-07','VX-520','SN00034',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(35,'T-2035',NULL,1,'Merchant 35','Contact Person 35','+263770000035','merchant35@example.com','35 Main Street','Region 3','Kadoma','Mashonaland West',NULL,'Services','2025-01-08','iWL252','SN00035',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(36,'T-2036',NULL,1,'Merchant 36','Contact Person 36','+263770000036','merchant36@example.com','36 Main Street','Region 1','Gutu','Masvingo',NULL,'Retail','2025-01-09','VX-520','SN00036',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(37,'T-2037',NULL,1,'Merchant 37','Contact Person 37','+263770000037','merchant37@example.com','37 Main Street','Region 2','Gweru','Midlands',NULL,'Services','2025-01-10','iWL252','SN00037',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(38,'T-2038',NULL,1,'Merchant 38','Contact Person 38','+263770000038','merchant38@example.com','38 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-11','VX-520','SN00038',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(39,'T-2039',NULL,1,'Merchant 39','Contact Person 39','+263770000039','merchant39@example.com','39 Main Street','Region 1','Beitbridge','Matabeleland South',NULL,'Services','2025-01-12','iWL252','SN00039',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(40,'T-2040',NULL,1,'Merchant 40','Contact Person 40','+263770000040','merchant40@example.com','40 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-13','VX-520','SN00040',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(41,'T-2041',NULL,1,'Merchant 41','Contact Person 41','+263770000041','merchant41@example.com','41 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-14','iWL252','SN00041',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(42,'T-2042',NULL,1,'Merchant 42','Contact Person 42','+263770000042','merchant42@example.com','42 Main Street','Region 1','Chipinge','Manicaland',NULL,'Retail','2025-01-15','VX-520','SN00042',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(43,'T-2043',NULL,1,'Merchant 43','Contact Person 43','+263770000043','merchant43@example.com','43 Main Street','Region 2','Bindura','Mashonaland Central',NULL,'Services','2025-01-16','iWL252','SN00043',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(44,'T-2044',NULL,1,'Merchant 44','Contact Person 44','+263770000044','merchant44@example.com','44 Main Street','Region 3','Mutoko','Mashonaland East',NULL,'Retail','2025-01-17','VX-520','SN00044',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(45,'T-2045',NULL,1,'Merchant 45','Contact Person 45','+263770000045','merchant45@example.com','45 Main Street','Region 1','Kariba','Mashonaland West',NULL,'Services','2025-01-18','iWL252','SN00045',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(46,'T-2046',NULL,1,'Merchant 46','Contact Person 46','+263770000046','merchant46@example.com','46 Main Street','Region 2','Chiredzi','Masvingo',NULL,'Retail','2025-01-19','VX-520','SN00046',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(47,'T-2047',NULL,1,'Merchant 47','Contact Person 47','+263770000047','merchant47@example.com','47 Main Street','Region 3','Zvishavane','Midlands',NULL,'Services','2025-01-20','iWL252','SN00047',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(48,'T-2048',NULL,1,'Merchant 48','Contact Person 48','+263770000048','merchant48@example.com','48 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-21','VX-520','SN00048',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(49,'T-2049',NULL,1,'Merchant 49','Contact Person 49','+263770000049','merchant49@example.com','49 Main Street','Region 2','Plumtree','Matabeleland South',NULL,'Services','2025-01-22','iWL252','SN00049',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(50,'T-2050',NULL,1,'Merchant 50','Contact Person 50','+263770000050','merchant50@example.com','50 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-23','VX-520','SN00050',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(51,'T-2051',NULL,1,'Merchant 51','Contact Person 51','+263770000051','merchant51@example.com','51 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-24','iWL252','SN00051',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(52,'T-2052',NULL,1,'Merchant 52','Contact Person 52','+263770000052','merchant52@example.com','52 Main Street','Region 2','Chipinge','Manicaland',NULL,'Retail','2025-01-25','VX-520','SN00052',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(53,'T-2053',NULL,1,'Merchant 53','Contact Person 53','+263770000053','merchant53@example.com','53 Main Street','Region 3','Shamva','Mashonaland Central',NULL,'Services','2025-01-26','iWL252','SN00053',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(54,'T-2054',NULL,1,'Merchant 54','Contact Person 54','+263770000054','merchant54@example.com','54 Main Street','Region 1','Marondera','Mashonaland East',NULL,'Retail','2025-01-27','VX-520','SN00054',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(55,'T-2055',NULL,1,'Merchant 55','Contact Person 55','+263770000055','merchant55@example.com','55 Main Street','Region 2','Kariba','Mashonaland West',NULL,'Services','2025-01-28','iWL252','SN00055',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(56,'T-2056',NULL,1,'Merchant 56','Contact Person 56','+263770000056','merchant56@example.com','56 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-01','VX-520','SN00056',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(57,'T-2057',NULL,1,'Merchant 57','Contact Person 57','+263770000057','merchant57@example.com','57 Main Street','Region 1','Gweru','Midlands',NULL,'Services','2025-01-02','iWL252','SN00057',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(58,'T-2058',NULL,1,'Merchant 58','Contact Person 58','+263770000058','merchant58@example.com','58 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-03','VX-520','SN00058',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(59,'T-2059',NULL,1,'Merchant 59','Contact Person 59','+263770000059','merchant59@example.com','59 Main Street','Region 3','Gwanda','Matabeleland South',NULL,'Services','2025-01-04','iWL252','SN00059',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(60,'T-2060',NULL,1,'Merchant 60','Contact Person 60','+263770000060','merchant60@example.com','60 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-05','VX-520','SN00060',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(61,'T-2061',NULL,1,'Merchant 61','Contact Person 61','+263770000061','merchant61@example.com','61 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-06','iWL252','SN00061',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(62,'T-2062',NULL,1,'Merchant 62','Contact Person 62','+263770000062','merchant62@example.com','62 Main Street','Region 3','Chipinge','Manicaland',NULL,'Retail','2025-01-07','VX-520','SN00062',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(63,'T-2063',NULL,1,'Merchant 63','Contact Person 63','+263770000063','merchant63@example.com','63 Main Street','Region 1','Shamva','Mashonaland Central',NULL,'Services','2025-01-08','iWL252','SN00063',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(64,'T-2064',NULL,1,'Merchant 64','Contact Person 64','+263770000064','merchant64@example.com','64 Main Street','Region 2','Mutoko','Mashonaland East',NULL,'Retail','2025-01-09','VX-520','SN00064',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(65,'T-2065',NULL,1,'Merchant 65','Contact Person 65','+263770000065','merchant65@example.com','65 Main Street','Region 3','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-10','iWL252','SN00065',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(66,'T-2066',NULL,1,'Merchant 66','Contact Person 66','+263770000066','merchant66@example.com','66 Main Street','Region 1','Masvingo','Masvingo',NULL,'Retail','2025-01-11','VX-520','SN00066',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(67,'T-2067',NULL,1,'Merchant 67','Contact Person 67','+263770000067','merchant67@example.com','67 Main Street','Region 2','Kwekwe','Midlands',NULL,'Services','2025-01-12','iWL252','SN00067',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(68,'T-2068',NULL,1,'Merchant 68','Contact Person 68','+263770000068','merchant68@example.com','68 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-13','VX-520','SN00068',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(69,'T-2069',NULL,1,'Merchant 69','Contact Person 69','+263770000069','merchant69@example.com','69 Main Street','Region 1','Gwanda','Matabeleland South',NULL,'Services','2025-01-14','iWL252','SN00069',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(70,'T-2070',NULL,1,'Merchant 70','Contact Person 70','+263770000070','merchant70@example.com','70 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-15','VX-520','SN00070',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(71,'T-2071',NULL,1,'Merchant 71','Contact Person 71','+263770000071','merchant71@example.com','71 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-16','iWL252','SN00071',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(72,'T-2072',NULL,1,'Merchant 72','Contact Person 72','+263770000072','merchant72@example.com','72 Main Street','Region 1','Rusape','Manicaland',NULL,'Retail','2025-01-17','VX-520','SN00072',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(73,'T-2073',NULL,1,'Merchant 73','Contact Person 73','+263770000073','merchant73@example.com','73 Main Street','Region 2','Shamva','Mashonaland Central',NULL,'Services','2025-01-18','iWL252','SN00073',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(74,'T-2074',NULL,1,'Merchant 74','Contact Person 74','+263770000074','merchant74@example.com','74 Main Street','Region 3','Marondera','Mashonaland East',NULL,'Retail','2025-01-19','VX-520','SN00074',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(75,'T-2075',NULL,1,'Merchant 75','Contact Person 75','+263770000075','merchant75@example.com','75 Main Street','Region 1','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-20','iWL252','SN00075',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(76,'T-2076',NULL,1,'Merchant 76','Contact Person 76','+263770000076','merchant76@example.com','76 Main Street','Region 2','Gutu','Masvingo',NULL,'Retail','2025-01-21','VX-520','SN00076',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(77,'T-2077',NULL,1,'Merchant 77','Contact Person 77','+263770000077','merchant77@example.com','77 Main Street','Region 3','Gweru','Midlands',NULL,'Services','2025-01-22','iWL252','SN00077',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(78,'T-2078',NULL,1,'Merchant 78','Contact Person 78','+263770000078','merchant78@example.com','78 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-23','VX-520','SN00078',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(79,'T-2079',NULL,1,'Merchant 79','Contact Person 79','+263770000079','merchant79@example.com','79 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-24','iWL252','SN00079',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(80,'T-2080',NULL,1,'Merchant 80','Contact Person 80','+263770000080','merchant80@example.com','80 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-25','VX-520','SN00080',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(81,'T-2081',NULL,1,'Merchant 81','Contact Person 81','+263770000081','merchant81@example.com','81 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-26','iWL252','SN00081',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(82,'T-2082',NULL,1,'Merchant 82','Contact Person 82','+263770000082','merchant82@example.com','82 Main Street','Region 2','Rusape','Manicaland',NULL,'Retail','2025-01-27','VX-520','SN00082',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(83,'T-2083',NULL,1,'Merchant 83','Contact Person 83','+263770000083','merchant83@example.com','83 Main Street','Region 3','Bindura','Mashonaland Central',NULL,'Services','2025-01-28','iWL252','SN00083',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(84,'T-2084',NULL,1,'Merchant 84','Contact Person 84','+263770000084','merchant84@example.com','84 Main Street','Region 1','Murehwa','Mashonaland East',NULL,'Retail','2025-01-01','VX-520','SN00084',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(85,'T-2085',NULL,1,'Merchant 85','Contact Person 85','+263770000085','merchant85@example.com','85 Main Street','Region 2','Kariba','Mashonaland West',NULL,'Services','2025-01-02','iWL252','SN00085',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(86,'T-2086',NULL,1,'Merchant 86','Contact Person 86','+263770000086','merchant86@example.com','86 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-03','VX-520','SN00086',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(87,'T-2087',NULL,1,'Merchant 87','Contact Person 87','+263770000087','merchant87@example.com','87 Main Street','Region 1','Gweru','Midlands',NULL,'Services','2025-01-04','iWL252','SN00087',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(88,'T-2088',NULL,1,'Merchant 88','Contact Person 88','+263770000088','merchant88@example.com','88 Main Street','Region 2','Lupane','Matabeleland North',NULL,'Retail','2025-01-05','VX-520','SN00088',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(89,'T-2089',NULL,1,'Merchant 89','Contact Person 89','+263770000089','merchant89@example.com','89 Main Street','Region 3','Gwanda','Matabeleland South',NULL,'Services','2025-01-06','iWL252','SN00089',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(90,'T-2090',NULL,1,'Merchant 90','Contact Person 90','+263770000090','merchant90@example.com','90 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-07','VX-520','SN00090',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(91,'T-2091',NULL,1,'Merchant 91','Contact Person 91','+263770000091','merchant91@example.com','91 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-08','iWL252','SN00091',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(92,'T-2092',NULL,1,'Merchant 92','Contact Person 92','+263770000092','merchant92@example.com','92 Main Street','Region 3','Chipinge','Manicaland',NULL,'Retail','2025-01-09','VX-520','SN00092',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(93,'T-2093',NULL,1,'Merchant 93','Contact Person 93','+263770000093','merchant93@example.com','93 Main Street','Region 1','Shamva','Mashonaland Central',NULL,'Services','2025-01-10','iWL252','SN00093',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(94,'T-2094',NULL,1,'Merchant 94','Contact Person 94','+263770000094','merchant94@example.com','94 Main Street','Region 2','Mutoko','Mashonaland East',NULL,'Retail','2025-01-11','VX-520','SN00094',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(95,'T-2095',NULL,1,'Merchant 95','Contact Person 95','+263770000095','merchant95@example.com','95 Main Street','Region 3','Kadoma','Mashonaland West',NULL,'Services','2025-01-12','iWL252','SN00095',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(96,'T-2096',NULL,1,'Merchant 96','Contact Person 96','+263770000096','merchant96@example.com','96 Main Street','Region 1','Gutu','Masvingo',NULL,'Retail','2025-01-13','VX-520','SN00096',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(97,'T-2097',NULL,1,'Merchant 97','Contact Person 97','+263770000097','merchant97@example.com','97 Main Street','Region 2','Kwekwe','Midlands',NULL,'Services','2025-01-14','iWL252','SN00097',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(98,'T-2098',NULL,1,'Merchant 98','Contact Person 98','+263770000098','merchant98@example.com','98 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-15','VX-520','SN00098',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(99,'T-2099',NULL,1,'Merchant 99','Contact Person 99','+263770000099','merchant99@example.com','99 Main Street','Region 1','Plumtree','Matabeleland South',NULL,'Services','2025-01-16','iWL252','SN00099',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(100,'T-2100',NULL,1,'Merchant 100','Contact Person 100','+263770000100','merchant100@example.com','100 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-17','VX-520','SN00100',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(101,'T-2101',NULL,1,'Merchant 101','Contact Person 101','+263770000101','merchant101@example.com','101 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-18','iWL252','SN00101',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(102,'T-2102',NULL,1,'Merchant 102','Contact Person 102','+263770000102','merchant102@example.com','102 Main Street','Region 1','Chipinge','Manicaland',NULL,'Retail','2025-01-19','VX-520','SN00102',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(103,'T-2103',NULL,1,'Merchant 103','Contact Person 103','+263770000103','merchant103@example.com','103 Main Street','Region 2','Shamva','Mashonaland Central',NULL,'Services','2025-01-20','iWL252','SN00103',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(104,'T-2104',NULL,1,'Merchant 104','Contact Person 104','+263770000104','merchant104@example.com','104 Main Street','Region 3','Murehwa','Mashonaland East',NULL,'Retail','2025-01-21','VX-520','SN00104',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(105,'T-2105',NULL,1,'Merchant 105','Contact Person 105','+263770000105','merchant105@example.com','105 Main Street','Region 1','Kadoma','Mashonaland West',NULL,'Services','2025-01-22','iWL252','SN00105',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(106,'T-2106',NULL,1,'Merchant 106','Contact Person 106','+263770000106','merchant106@example.com','106 Main Street','Region 2','Masvingo','Masvingo',NULL,'Retail','2025-01-23','VX-520','SN00106',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(107,'T-2107',NULL,1,'Merchant 107','Contact Person 107','+263770000107','merchant107@example.com','107 Main Street','Region 3','Zvishavane','Midlands',NULL,'Services','2025-01-24','iWL252','SN00107',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(108,'T-2108',NULL,1,'Merchant 108','Contact Person 108','+263770000108','merchant108@example.com','108 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-25','VX-520','SN00108',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(109,'T-2109',NULL,1,'Merchant 109','Contact Person 109','+263770000109','merchant109@example.com','109 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-26','iWL252','SN00109',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(110,'T-2110',NULL,1,'Merchant 110','Contact Person 110','+263770000110','merchant110@example.com','110 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-27','VX-520','SN00110',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(111,'T-2111',NULL,1,'Merchant 111','Contact Person 111','+263770000111','merchant111@example.com','111 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-28','iWL252','SN00111',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(112,'T-2112',NULL,1,'Merchant 112','Contact Person 112','+263770000112','merchant112@example.com','112 Main Street','Region 2','Rusape','Manicaland',NULL,'Retail','2025-01-01','VX-520','SN00112',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(113,'T-2113',NULL,1,'Merchant 113','Contact Person 113','+263770000113','merchant113@example.com','113 Main Street','Region 3','Bindura','Mashonaland Central',NULL,'Services','2025-01-02','iWL252','SN00113',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(114,'T-2114',NULL,1,'Merchant 114','Contact Person 114','+263770000114','merchant114@example.com','114 Main Street','Region 1','Murehwa','Mashonaland East',NULL,'Retail','2025-01-03','VX-520','SN00114',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(115,'T-2115',NULL,1,'Merchant 115','Contact Person 115','+263770000115','merchant115@example.com','115 Main Street','Region 2','Kadoma','Mashonaland West',NULL,'Services','2025-01-04','iWL252','SN00115',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(116,'T-2116',NULL,1,'Merchant 116','Contact Person 116','+263770000116','merchant116@example.com','116 Main Street','Region 3','Masvingo','Masvingo',NULL,'Retail','2025-01-05','VX-520','SN00116',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(117,'T-2117',NULL,1,'Merchant 117','Contact Person 117','+263770000117','merchant117@example.com','117 Main Street','Region 1','Kwekwe','Midlands',NULL,'Services','2025-01-06','iWL252','SN00117',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(118,'T-2118',NULL,1,'Merchant 118','Contact Person 118','+263770000118','merchant118@example.com','118 Main Street','Region 2','Victoria Falls','Matabeleland North',NULL,'Retail','2025-01-07','VX-520','SN00118',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(119,'T-2119',NULL,1,'Merchant 119','Contact Person 119','+263770000119','merchant119@example.com','119 Main Street','Region 3','Plumtree','Matabeleland South',NULL,'Services','2025-01-08','iWL252','SN00119',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(120,'T-2120',NULL,1,'Merchant 120','Contact Person 120','+263770000120','merchant120@example.com','120 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-09','VX-520','SN00120',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(121,'T-2121',NULL,1,'Merchant 121','Contact Person 121','+263770000121','merchant121@example.com','121 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-10','iWL252','SN00121',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(122,'T-2122',NULL,1,'Merchant 122','Contact Person 122','+263770000122','merchant122@example.com','122 Main Street','Region 3','Chipinge','Manicaland',NULL,'Retail','2025-01-11','VX-520','SN00122',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(123,'T-2123',NULL,1,'Merchant 123','Contact Person 123','+263770000123','merchant123@example.com','123 Main Street','Region 1','Bindura','Mashonaland Central',NULL,'Services','2025-01-12','iWL252','SN00123',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(124,'T-2124',NULL,1,'Merchant 124','Contact Person 124','+263770000124','merchant124@example.com','124 Main Street','Region 2','Murehwa','Mashonaland East',NULL,'Retail','2025-01-13','VX-520','SN00124',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(125,'T-2125',NULL,1,'Merchant 125','Contact Person 125','+263770000125','merchant125@example.com','125 Main Street','Region 3','Kariba','Mashonaland West',NULL,'Services','2025-01-14','iWL252','SN00125',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(126,'T-2126',NULL,1,'Merchant 126','Contact Person 126','+263770000126','merchant126@example.com','126 Main Street','Region 1','Masvingo','Masvingo',NULL,'Retail','2025-01-15','VX-520','SN00126',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(127,'T-2127',NULL,1,'Merchant 127','Contact Person 127','+263770000127','merchant127@example.com','127 Main Street','Region 2','Gweru','Midlands',NULL,'Services','2025-01-16','iWL252','SN00127',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(128,'T-2128',NULL,1,'Merchant 128','Contact Person 128','+263770000128','merchant128@example.com','128 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-17','VX-520','SN00128',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(129,'T-2129',NULL,1,'Merchant 129','Contact Person 129','+263770000129','merchant129@example.com','129 Main Street','Region 1','Beitbridge','Matabeleland South',NULL,'Services','2025-01-18','iWL252','SN00129',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(130,'T-2130',NULL,1,'Merchant 130','Contact Person 130','+263770000130','merchant130@example.com','130 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-19','VX-520','SN00130',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(131,'T-2131',NULL,1,'Merchant 131','Contact Person 131','+263770000131','merchant131@example.com','131 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-20','iWL252','SN00131',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(132,'T-2132',NULL,1,'Merchant 132','Contact Person 132','+263770000132','merchant132@example.com','132 Main Street','Region 1','Rusape','Manicaland',NULL,'Retail','2025-01-21','VX-520','SN00132',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(133,'T-2133',NULL,1,'Merchant 133','Contact Person 133','+263770000133','merchant133@example.com','133 Main Street','Region 2','Bindura','Mashonaland Central',NULL,'Services','2025-01-22','iWL252','SN00133',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(134,'T-2134',NULL,1,'Merchant 134','Contact Person 134','+263770000134','merchant134@example.com','134 Main Street','Region 3','Murehwa','Mashonaland East',NULL,'Retail','2025-01-23','VX-520','SN00134',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(135,'T-2135',NULL,1,'Merchant 135','Contact Person 135','+263770000135','merchant135@example.com','135 Main Street','Region 1','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-24','iWL252','SN00135',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(136,'T-2136',NULL,1,'Merchant 136','Contact Person 136','+263770000136','merchant136@example.com','136 Main Street','Region 2','Masvingo','Masvingo',NULL,'Retail','2025-01-25','VX-520','SN00136',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(137,'T-2137',NULL,1,'Merchant 137','Contact Person 137','+263770000137','merchant137@example.com','137 Main Street','Region 3','Zvishavane','Midlands',NULL,'Services','2025-01-26','iWL252','SN00137',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(138,'T-2138',NULL,1,'Merchant 138','Contact Person 138','+263770000138','merchant138@example.com','138 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-27','VX-520','SN00138',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(139,'T-2139',NULL,1,'Merchant 139','Contact Person 139','+263770000139','merchant139@example.com','139 Main Street','Region 2','Gwanda','Matabeleland South',NULL,'Services','2025-01-28','iWL252','SN00139',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(140,'T-2140',NULL,1,'Merchant 140','Contact Person 140','+263770000140','merchant140@example.com','140 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-01','VX-520','SN00140',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(141,'T-2141',NULL,1,'Merchant 141','Contact Person 141','+263770000141','merchant141@example.com','141 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-02','iWL252','SN00141',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(142,'T-2142',NULL,1,'Merchant 142','Contact Person 142','+263770000142','merchant142@example.com','142 Main Street','Region 2','Mutare','Manicaland',NULL,'Retail','2025-01-03','VX-520','SN00142',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(143,'T-2143',NULL,1,'Merchant 143','Contact Person 143','+263770000143','merchant143@example.com','143 Main Street','Region 3','Shamva','Mashonaland Central',NULL,'Services','2025-01-04','iWL252','SN00143',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(144,'T-2144',NULL,1,'Merchant 144','Contact Person 144','+263770000144','merchant144@example.com','144 Main Street','Region 1','Mutoko','Mashonaland East',NULL,'Retail','2025-01-05','VX-520','SN00144',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(145,'T-2145',NULL,1,'Merchant 145','Contact Person 145','+263770000145','merchant145@example.com','145 Main Street','Region 2','Kadoma','Mashonaland West',NULL,'Services','2025-01-06','iWL252','SN00145',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(146,'T-2146',NULL,1,'Merchant 146','Contact Person 146','+263770000146','merchant146@example.com','146 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-07','VX-520','SN00146',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(147,'T-2147',NULL,1,'Merchant 147','Contact Person 147','+263770000147','merchant147@example.com','147 Main Street','Region 1','Zvishavane','Midlands',NULL,'Services','2025-01-08','iWL252','SN00147',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(148,'T-2148',NULL,1,'Merchant 148','Contact Person 148','+263770000148','merchant148@example.com','148 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-09','VX-520','SN00148',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(149,'T-2149',NULL,1,'Merchant 149','Contact Person 149','+263770000149','merchant149@example.com','149 Main Street','Region 3','Plumtree','Matabeleland South',NULL,'Services','2025-01-10','iWL252','SN00149',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(150,'T-2150',NULL,1,'Merchant 150','Contact Person 150','+263770000150','merchant150@example.com','150 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-11','VX-520','SN00150',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(151,'T-2151',NULL,1,'Merchant 151','Contact Person 151','+263770000151','merchant151@example.com','151 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-12','iWL252','SN00151',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(152,'T-2152',NULL,1,'Merchant 152','Contact Person 152','+263770000152','merchant152@example.com','152 Main Street','Region 3','Mutare','Manicaland',NULL,'Retail','2025-01-13','VX-520','SN00152',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(153,'T-2153',NULL,1,'Merchant 153','Contact Person 153','+263770000153','merchant153@example.com','153 Main Street','Region 1','Shamva','Mashonaland Central',NULL,'Services','2025-01-14','iWL252','SN00153',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(154,'T-2154',NULL,1,'Merchant 154','Contact Person 154','+263770000154','merchant154@example.com','154 Main Street','Region 2','Marondera','Mashonaland East',NULL,'Retail','2025-01-15','VX-520','SN00154',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(155,'T-2155',NULL,1,'Merchant 155','Contact Person 155','+263770000155','merchant155@example.com','155 Main Street','Region 3','Kariba','Mashonaland West',NULL,'Services','2025-01-16','iWL252','SN00155',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(156,'T-2156',NULL,1,'Merchant 156','Contact Person 156','+263770000156','merchant156@example.com','156 Main Street','Region 1','Gutu','Masvingo',NULL,'Retail','2025-01-17','VX-520','SN00156',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(157,'T-2157',NULL,1,'Merchant 157','Contact Person 157','+263770000157','merchant157@example.com','157 Main Street','Region 2','Zvishavane','Midlands',NULL,'Services','2025-01-18','iWL252','SN00157',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(158,'T-2158',NULL,1,'Merchant 158','Contact Person 158','+263770000158','merchant158@example.com','158 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-19','VX-520','SN00158',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(159,'T-2159',NULL,1,'Merchant 159','Contact Person 159','+263770000159','merchant159@example.com','159 Main Street','Region 1','Plumtree','Matabeleland South',NULL,'Services','2025-01-20','iWL252','SN00159',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(160,'T-2160',NULL,1,'Merchant 160','Contact Person 160','+263770000160','merchant160@example.com','160 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-21','VX-520','SN00160',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(161,'T-2161',NULL,1,'Merchant 161','Contact Person 161','+263770000161','merchant161@example.com','161 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-22','iWL252','SN00161',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(162,'T-2162',NULL,1,'Merchant 162','Contact Person 162','+263770000162','merchant162@example.com','162 Main Street','Region 1','Rusape','Manicaland',NULL,'Retail','2025-01-23','VX-520','SN00162',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(163,'T-2163',NULL,1,'Merchant 163','Contact Person 163','+263770000163','merchant163@example.com','163 Main Street','Region 2','Bindura','Mashonaland Central',NULL,'Services','2025-01-24','iWL252','SN00163',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(164,'T-2164',NULL,1,'Merchant 164','Contact Person 164','+263770000164','merchant164@example.com','164 Main Street','Region 3','Mutoko','Mashonaland East',NULL,'Retail','2025-01-25','VX-520','SN00164',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(165,'T-2165',NULL,1,'Merchant 165','Contact Person 165','+263770000165','merchant165@example.com','165 Main Street','Region 1','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-26','iWL252','SN00165',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(166,'T-2166',NULL,1,'Merchant 166','Contact Person 166','+263770000166','merchant166@example.com','166 Main Street','Region 2','Gutu','Masvingo',NULL,'Retail','2025-01-27','VX-520','SN00166',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(167,'T-2167',NULL,1,'Merchant 167','Contact Person 167','+263770000167','merchant167@example.com','167 Main Street','Region 3','Gweru','Midlands',NULL,'Services','2025-01-28','iWL252','SN00167',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(168,'T-2168',NULL,1,'Merchant 168','Contact Person 168','+263770000168','merchant168@example.com','168 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-01','VX-520','SN00168',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(169,'T-2169',NULL,1,'Merchant 169','Contact Person 169','+263770000169','merchant169@example.com','169 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-02','iWL252','SN00169',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(170,'T-2170',NULL,1,'Merchant 170','Contact Person 170','+263770000170','merchant170@example.com','170 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-03','VX-520','SN00170',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(171,'T-2171',NULL,1,'Merchant 171','Contact Person 171','+263770000171','merchant171@example.com','171 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-04','iWL252','SN00171',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(172,'T-2172',NULL,1,'Merchant 172','Contact Person 172','+263770000172','merchant172@example.com','172 Main Street','Region 2','Mutare','Manicaland',NULL,'Retail','2025-01-05','VX-520','SN00172',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(173,'T-2173',NULL,1,'Merchant 173','Contact Person 173','+263770000173','merchant173@example.com','173 Main Street','Region 3','Mazowe','Mashonaland Central',NULL,'Services','2025-01-06','iWL252','SN00173',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(174,'T-2174',NULL,1,'Merchant 174','Contact Person 174','+263770000174','merchant174@example.com','174 Main Street','Region 1','Mutoko','Mashonaland East',NULL,'Retail','2025-01-07','VX-520','SN00174',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(175,'T-2175',NULL,1,'Merchant 175','Contact Person 175','+263770000175','merchant175@example.com','175 Main Street','Region 2','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-08','iWL252','SN00175',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(176,'T-2176',NULL,1,'Merchant 176','Contact Person 176','+263770000176','merchant176@example.com','176 Main Street','Region 3','Chiredzi','Masvingo',NULL,'Retail','2025-01-09','VX-520','SN00176',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(177,'T-2177',NULL,1,'Merchant 177','Contact Person 177','+263770000177','merchant177@example.com','177 Main Street','Region 1','Gweru','Midlands',NULL,'Services','2025-01-10','iWL252','SN00177',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(178,'T-2178',NULL,1,'Merchant 178','Contact Person 178','+263770000178','merchant178@example.com','178 Main Street','Region 2','Victoria Falls','Matabeleland North',NULL,'Retail','2025-01-11','VX-520','SN00178',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(179,'T-2179',NULL,1,'Merchant 179','Contact Person 179','+263770000179','merchant179@example.com','179 Main Street','Region 3','Gwanda','Matabeleland South',NULL,'Services','2025-01-12','iWL252','SN00179',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(180,'T-2180',NULL,1,'Merchant 180','Contact Person 180','+263770000180','merchant180@example.com','180 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-13','VX-520','SN00180',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(181,'T-2181',NULL,1,'Merchant 181','Contact Person 181','+263770000181','merchant181@example.com','181 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-14','iWL252','SN00181',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(182,'T-2182',NULL,1,'Merchant 182','Contact Person 182','+263770000182','merchant182@example.com','182 Main Street','Region 3','Rusape','Manicaland',NULL,'Retail','2025-01-15','VX-520','SN00182',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(183,'T-2183',NULL,1,'Merchant 183','Contact Person 183','+263770000183','merchant183@example.com','183 Main Street','Region 1','Mazowe','Mashonaland Central',NULL,'Services','2025-01-16','iWL252','SN00183',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(184,'T-2184',NULL,1,'Merchant 184','Contact Person 184','+263770000184','merchant184@example.com','184 Main Street','Region 2','Marondera','Mashonaland East',NULL,'Retail','2025-01-17','VX-520','SN00184',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(185,'T-2185',NULL,1,'Merchant 185','Contact Person 185','+263770000185','merchant185@example.com','185 Main Street','Region 3','Kadoma','Mashonaland West',NULL,'Services','2025-01-18','iWL252','SN00185',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(186,'T-2186',NULL,1,'Merchant 186','Contact Person 186','+263770000186','merchant186@example.com','186 Main Street','Region 1','Masvingo','Masvingo',NULL,'Retail','2025-01-19','VX-520','SN00186',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(187,'T-2187',NULL,1,'Merchant 187','Contact Person 187','+263770000187','merchant187@example.com','187 Main Street','Region 2','Zvishavane','Midlands',NULL,'Services','2025-01-20','iWL252','SN00187',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(188,'T-2188',NULL,1,'Merchant 188','Contact Person 188','+263770000188','merchant188@example.com','188 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-21','VX-520','SN00188',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(189,'T-2189',NULL,1,'Merchant 189','Contact Person 189','+263770000189','merchant189@example.com','189 Main Street','Region 1','Beitbridge','Matabeleland South',NULL,'Services','2025-01-22','iWL252','SN00189',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(190,'T-2190',NULL,1,'Merchant 190','Contact Person 190','+263770000190','merchant190@example.com','190 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-23','VX-520','SN00190',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(191,'T-2191',NULL,1,'Merchant 191','Contact Person 191','+263770000191','merchant191@example.com','191 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-24','iWL252','SN00191',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(192,'T-2192',NULL,1,'Merchant 192','Contact Person 192','+263770000192','merchant192@example.com','192 Main Street','Region 1','Chipinge','Manicaland',NULL,'Retail','2025-01-25','VX-520','SN00192',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(193,'T-2193',NULL,1,'Merchant 193','Contact Person 193','+263770000193','merchant193@example.com','193 Main Street','Region 2','Shamva','Mashonaland Central',NULL,'Services','2025-01-26','iWL252','SN00193',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(194,'T-2194',NULL,1,'Merchant 194','Contact Person 194','+263770000194','merchant194@example.com','194 Main Street','Region 3','Marondera','Mashonaland East',NULL,'Retail','2025-01-27','VX-520','SN00194',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(195,'T-2195',NULL,1,'Merchant 195','Contact Person 195','+263770000195','merchant195@example.com','195 Main Street','Region 1','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-28','iWL252','SN00195',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(196,'T-2196',NULL,1,'Merchant 196','Contact Person 196','+263770000196','merchant196@example.com','196 Main Street','Region 2','Masvingo','Masvingo',NULL,'Retail','2025-01-01','VX-520','SN00196',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(197,'T-2197',NULL,1,'Merchant 197','Contact Person 197','+263770000197','merchant197@example.com','197 Main Street','Region 3','Zvishavane','Midlands',NULL,'Services','2025-01-02','iWL252','SN00197',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(198,'T-2198',NULL,1,'Merchant 198','Contact Person 198','+263770000198','merchant198@example.com','198 Main Street','Region 1','Lupane','Matabeleland North',NULL,'Retail','2025-01-03','VX-520','SN00198',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(199,'T-2199',NULL,1,'Merchant 199','Contact Person 199','+263770000199','merchant199@example.com','199 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-04','iWL252','SN00199',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(200,'T-2200',NULL,1,'Merchant 200','Contact Person 200','+263770000200','merchant200@example.com','200 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-05','VX-520','SN00200',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(201,'T-2201',NULL,1,'Merchant 201','Contact Person 201','+263770000201','merchant201@example.com','201 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-06','iWL252','SN00201',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(202,'T-2202',NULL,1,'Merchant 202','Contact Person 202','+263770000202','merchant202@example.com','202 Main Street','Region 2','Mutare','Manicaland',NULL,'Retail','2025-01-07','VX-520','SN00202',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(203,'T-2203',NULL,1,'Merchant 203','Contact Person 203','+263770000203','merchant203@example.com','203 Main Street','Region 3','Mazowe','Mashonaland Central',NULL,'Services','2025-01-08','iWL252','SN00203',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(204,'T-2204',NULL,1,'Merchant 204','Contact Person 204','+263770000204','merchant204@example.com','204 Main Street','Region 1','Marondera','Mashonaland East',NULL,'Retail','2025-01-09','VX-520','SN00204',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(205,'T-2205',NULL,1,'Merchant 205','Contact Person 205','+263770000205','merchant205@example.com','205 Main Street','Region 2','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-10','iWL252','SN00205',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(206,'T-2206',NULL,1,'Merchant 206','Contact Person 206','+263770000206','merchant206@example.com','206 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-11','VX-520','SN00206',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(207,'T-2207',NULL,1,'Merchant 207','Contact Person 207','+263770000207','merchant207@example.com','207 Main Street','Region 1','Kwekwe','Midlands',NULL,'Services','2025-01-12','iWL252','SN00207',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(208,'T-2208',NULL,1,'Merchant 208','Contact Person 208','+263770000208','merchant208@example.com','208 Main Street','Region 2','Lupane','Matabeleland North',NULL,'Retail','2025-01-13','VX-520','SN00208',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(209,'T-2209',NULL,1,'Merchant 209','Contact Person 209','+263770000209','merchant209@example.com','209 Main Street','Region 3','Plumtree','Matabeleland South',NULL,'Services','2025-01-14','iWL252','SN00209',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(210,'T-2210',NULL,1,'Merchant 210','Contact Person 210','+263770000210','merchant210@example.com','210 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-15','VX-520','SN00210',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(211,'T-2211',NULL,1,'Merchant 211','Contact Person 211','+263770000211','merchant211@example.com','211 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-16','iWL252','SN00211',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(212,'T-2212',NULL,1,'Merchant 212','Contact Person 212','+263770000212','merchant212@example.com','212 Main Street','Region 3','Rusape','Manicaland',NULL,'Retail','2025-01-17','VX-520','SN00212',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(213,'T-2213',NULL,1,'Merchant 213','Contact Person 213','+263770000213','merchant213@example.com','213 Main Street','Region 1','Mazowe','Mashonaland Central',NULL,'Services','2025-01-18','iWL252','SN00213',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(214,'T-2214',NULL,1,'Merchant 214','Contact Person 214','+263770000214','merchant214@example.com','214 Main Street','Region 2','Marondera','Mashonaland East',NULL,'Retail','2025-01-19','VX-520','SN00214',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(215,'T-2215',NULL,1,'Merchant 215','Contact Person 215','+263770000215','merchant215@example.com','215 Main Street','Region 3','Kariba','Mashonaland West',NULL,'Services','2025-01-20','iWL252','SN00215',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(216,'T-2216',NULL,1,'Merchant 216','Contact Person 216','+263770000216','merchant216@example.com','216 Main Street','Region 1','Chiredzi','Masvingo',NULL,'Retail','2025-01-21','VX-520','SN00216',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(217,'T-2217',NULL,1,'Merchant 217','Contact Person 217','+263770000217','merchant217@example.com','217 Main Street','Region 2','Zvishavane','Midlands',NULL,'Services','2025-01-22','iWL252','SN00217',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(218,'T-2218',NULL,1,'Merchant 218','Contact Person 218','+263770000218','merchant218@example.com','218 Main Street','Region 3','Lupane','Matabeleland North',NULL,'Retail','2025-01-23','VX-520','SN00218',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(219,'T-2219',NULL,1,'Merchant 219','Contact Person 219','+263770000219','merchant219@example.com','219 Main Street','Region 1','Plumtree','Matabeleland South',NULL,'Services','2025-01-24','iWL252','SN00219',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(220,'T-2220',NULL,1,'Merchant 220','Contact Person 220','+263770000220','merchant220@example.com','220 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-25','VX-520','SN00220',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(221,'T-2221',NULL,1,'Merchant 221','Contact Person 221','+263770000221','merchant221@example.com','221 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-26','iWL252','SN00221',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(222,'T-2222',NULL,1,'Merchant 222','Contact Person 222','+263770000222','merchant222@example.com','222 Main Street','Region 1','Mutare','Manicaland',NULL,'Retail','2025-01-27','VX-520','SN00222',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(223,'T-2223',NULL,1,'Merchant 223','Contact Person 223','+263770000223','merchant223@example.com','223 Main Street','Region 2','Bindura','Mashonaland Central',NULL,'Services','2025-01-28','iWL252','SN00223',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(224,'T-2224',NULL,1,'Merchant 224','Contact Person 224','+263770000224','merchant224@example.com','224 Main Street','Region 3','Mutoko','Mashonaland East',NULL,'Retail','2025-01-01','VX-520','SN00224',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(225,'T-2225',NULL,1,'Merchant 225','Contact Person 225','+263770000225','merchant225@example.com','225 Main Street','Region 1','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-02','iWL252','SN00225',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(226,'T-2226',NULL,1,'Merchant 226','Contact Person 226','+263770000226','merchant226@example.com','226 Main Street','Region 2','Chiredzi','Masvingo',NULL,'Retail','2025-01-03','VX-520','SN00226',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(227,'T-2227',NULL,1,'Merchant 227','Contact Person 227','+263770000227','merchant227@example.com','227 Main Street','Region 3','Kwekwe','Midlands',NULL,'Services','2025-01-04','iWL252','SN00227',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(228,'T-2228',NULL,1,'Merchant 228','Contact Person 228','+263770000228','merchant228@example.com','228 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-05','VX-520','SN00228',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(229,'T-2229',NULL,1,'Merchant 229','Contact Person 229','+263770000229','merchant229@example.com','229 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-06','iWL252','SN00229',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(230,'T-2230',NULL,1,'Merchant 230','Contact Person 230','+263770000230','merchant230@example.com','230 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-07','VX-520','SN00230',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(231,'T-2231',NULL,1,'Merchant 231','Contact Person 231','+263770000231','merchant231@example.com','231 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-08','iWL252','SN00231',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(232,'T-2232',NULL,1,'Merchant 232','Contact Person 232','+263770000232','merchant232@example.com','232 Main Street','Region 2','Chipinge','Manicaland',NULL,'Retail','2025-01-09','VX-520','SN00232',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(233,'T-2233',NULL,1,'Merchant 233','Contact Person 233','+263770000233','merchant233@example.com','233 Main Street','Region 3','Bindura','Mashonaland Central',NULL,'Services','2025-01-10','iWL252','SN00233',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(234,'T-2234',NULL,1,'Merchant 234','Contact Person 234','+263770000234','merchant234@example.com','234 Main Street','Region 1','Mutoko','Mashonaland East',NULL,'Retail','2025-01-11','VX-520','SN00234',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(235,'T-2235',NULL,1,'Merchant 235','Contact Person 235','+263770000235','merchant235@example.com','235 Main Street','Region 2','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-12','iWL252','SN00235',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(236,'T-2236',NULL,1,'Merchant 236','Contact Person 236','+263770000236','merchant236@example.com','236 Main Street','Region 3','Chiredzi','Masvingo',NULL,'Retail','2025-01-13','VX-520','SN00236',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(237,'T-2237',NULL,1,'Merchant 237','Contact Person 237','+263770000237','merchant237@example.com','237 Main Street','Region 1','Gweru','Midlands',NULL,'Services','2025-01-14','iWL252','SN00237',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(238,'T-2238',NULL,1,'Merchant 238','Contact Person 238','+263770000238','merchant238@example.com','238 Main Street','Region 2','Lupane','Matabeleland North',NULL,'Retail','2025-01-15','VX-520','SN00238',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(239,'T-2239',NULL,1,'Merchant 239','Contact Person 239','+263770000239','merchant239@example.com','239 Main Street','Region 3','Gwanda','Matabeleland South',NULL,'Services','2025-01-16','iWL252','SN00239',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(240,'T-2240',NULL,1,'Merchant 240','Contact Person 240','+263770000240','merchant240@example.com','240 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-17','VX-520','SN00240',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(241,'T-2241',NULL,1,'Merchant 241','Contact Person 241','+263770000241','merchant241@example.com','241 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-18','iWL252','SN00241',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(242,'T-2242',NULL,1,'Merchant 242','Contact Person 242','+263770000242','merchant242@example.com','242 Main Street','Region 3','Mutare','Manicaland',NULL,'Retail','2025-01-19','VX-520','SN00242',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(243,'T-2243',NULL,1,'Merchant 243','Contact Person 243','+263770000243','merchant243@example.com','243 Main Street','Region 1','Bindura','Mashonaland Central',NULL,'Services','2025-01-20','iWL252','SN00243',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(244,'T-2244',NULL,1,'Merchant 244','Contact Person 244','+263770000244','merchant244@example.com','244 Main Street','Region 2','Mutoko','Mashonaland East',NULL,'Retail','2025-01-21','VX-520','SN00244',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(245,'T-2245',NULL,1,'Merchant 245','Contact Person 245','+263770000245','merchant245@example.com','245 Main Street','Region 3','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-22','iWL252','SN00245',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(246,'T-2246',NULL,1,'Merchant 246','Contact Person 246','+263770000246','merchant246@example.com','246 Main Street','Region 1','Gutu','Masvingo',NULL,'Retail','2025-01-23','VX-520','SN00246',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(247,'T-2247',NULL,1,'Merchant 247','Contact Person 247','+263770000247','merchant247@example.com','247 Main Street','Region 2','Zvishavane','Midlands',NULL,'Services','2025-01-24','iWL252','SN00247',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(248,'T-2248',NULL,1,'Merchant 248','Contact Person 248','+263770000248','merchant248@example.com','248 Main Street','Region 3','Victoria Falls','Matabeleland North',NULL,'Retail','2025-01-25','VX-520','SN00248',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(249,'T-2249',NULL,1,'Merchant 249','Contact Person 249','+263770000249','merchant249@example.com','249 Main Street','Region 1','Beitbridge','Matabeleland South',NULL,'Services','2025-01-26','iWL252','SN00249',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(250,'T-2250',NULL,1,'Merchant 250','Contact Person 250','+263770000250','merchant250@example.com','250 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-27','VX-520','SN00250',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(251,'T-2251',NULL,1,'Merchant 251','Contact Person 251','+263770000251','merchant251@example.com','251 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-28','iWL252','SN00251',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(252,'T-2252',NULL,1,'Merchant 252','Contact Person 252','+263770000252','merchant252@example.com','252 Main Street','Region 1','Mutare','Manicaland',NULL,'Retail','2025-01-01','VX-520','SN00252',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(253,'T-2253',NULL,1,'Merchant 253','Contact Person 253','+263770000253','merchant253@example.com','253 Main Street','Region 2','Shamva','Mashonaland Central',NULL,'Services','2025-01-02','iWL252','SN00253',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(254,'T-2254',NULL,1,'Merchant 254','Contact Person 254','+263770000254','merchant254@example.com','254 Main Street','Region 3','Mutoko','Mashonaland East',NULL,'Retail','2025-01-03','VX-520','SN00254',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(255,'T-2255',NULL,1,'Merchant 255','Contact Person 255','+263770000255','merchant255@example.com','255 Main Street','Region 1','Kadoma','Mashonaland West',NULL,'Services','2025-01-04','iWL252','SN00255',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(256,'T-2256',NULL,1,'Merchant 256','Contact Person 256','+263770000256','merchant256@example.com','256 Main Street','Region 2','Chiredzi','Masvingo',NULL,'Retail','2025-01-05','VX-520','SN00256',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(257,'T-2257',NULL,1,'Merchant 257','Contact Person 257','+263770000257','merchant257@example.com','257 Main Street','Region 3','Zvishavane','Midlands',NULL,'Services','2025-01-06','iWL252','SN00257',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(258,'T-2258',NULL,1,'Merchant 258','Contact Person 258','+263770000258','merchant258@example.com','258 Main Street','Region 1','Lupane','Matabeleland North',NULL,'Retail','2025-01-07','VX-520','SN00258',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(259,'T-2259',NULL,1,'Merchant 259','Contact Person 259','+263770000259','merchant259@example.com','259 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-08','iWL252','SN00259',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(260,'T-2260',NULL,1,'Merchant 260','Contact Person 260','+263770000260','merchant260@example.com','260 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-09','VX-520','SN00260',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(261,'T-2261',NULL,1,'Merchant 261','Contact Person 261','+263770000261','merchant261@example.com','261 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-10','iWL252','SN00261',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(262,'T-2262',NULL,1,'Merchant 262','Contact Person 262','+263770000262','merchant262@example.com','262 Main Street','Region 2','Chipinge','Manicaland',NULL,'Retail','2025-01-11','VX-520','SN00262',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(263,'T-2263',NULL,1,'Merchant 263','Contact Person 263','+263770000263','merchant263@example.com','263 Main Street','Region 3','Shamva','Mashonaland Central',NULL,'Services','2025-01-12','iWL252','SN00263',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(264,'T-2264',NULL,1,'Merchant 264','Contact Person 264','+263770000264','merchant264@example.com','264 Main Street','Region 1','Murehwa','Mashonaland East',NULL,'Retail','2025-01-13','VX-520','SN00264',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(265,'T-2265',NULL,1,'Merchant 265','Contact Person 265','+263770000265','merchant265@example.com','265 Main Street','Region 2','Kariba','Mashonaland West',NULL,'Services','2025-01-14','iWL252','SN00265',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(266,'T-2266',NULL,1,'Merchant 266','Contact Person 266','+263770000266','merchant266@example.com','266 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-15','VX-520','SN00266',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(267,'T-2267',NULL,1,'Merchant 267','Contact Person 267','+263770000267','merchant267@example.com','267 Main Street','Region 1','Zvishavane','Midlands',NULL,'Services','2025-01-16','iWL252','SN00267',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(268,'T-2268',NULL,1,'Merchant 268','Contact Person 268','+263770000268','merchant268@example.com','268 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-17','VX-520','SN00268',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(269,'T-2269',NULL,1,'Merchant 269','Contact Person 269','+263770000269','merchant269@example.com','269 Main Street','Region 3','Plumtree','Matabeleland South',NULL,'Services','2025-01-18','iWL252','SN00269',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(270,'T-2270',NULL,1,'Merchant 270','Contact Person 270','+263770000270','merchant270@example.com','270 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-19','VX-520','SN00270',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(271,'T-2271',NULL,1,'Merchant 271','Contact Person 271','+263770000271','merchant271@example.com','271 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-20','iWL252','SN00271',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(272,'T-2272',NULL,1,'Merchant 272','Contact Person 272','+263770000272','merchant272@example.com','272 Main Street','Region 3','Chipinge','Manicaland',NULL,'Retail','2025-01-21','VX-520','SN00272',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(273,'T-2273',NULL,1,'Merchant 273','Contact Person 273','+263770000273','merchant273@example.com','273 Main Street','Region 1','Mazowe','Mashonaland Central',NULL,'Services','2025-01-22','iWL252','SN00273',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(274,'T-2274',NULL,1,'Merchant 274','Contact Person 274','+263770000274','merchant274@example.com','274 Main Street','Region 2','Murehwa','Mashonaland East',NULL,'Retail','2025-01-23','VX-520','SN00274',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(275,'T-2275',NULL,1,'Merchant 275','Contact Person 275','+263770000275','merchant275@example.com','275 Main Street','Region 3','Kariba','Mashonaland West',NULL,'Services','2025-01-24','iWL252','SN00275',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(276,'T-2276',NULL,1,'Merchant 276','Contact Person 276','+263770000276','merchant276@example.com','276 Main Street','Region 1','Masvingo','Masvingo',NULL,'Retail','2025-01-25','VX-520','SN00276',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(277,'T-2277',NULL,1,'Merchant 277','Contact Person 277','+263770000277','merchant277@example.com','277 Main Street','Region 2','Kwekwe','Midlands',NULL,'Services','2025-01-26','iWL252','SN00277',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(278,'T-2278',NULL,1,'Merchant 278','Contact Person 278','+263770000278','merchant278@example.com','278 Main Street','Region 3','Lupane','Matabeleland North',NULL,'Retail','2025-01-27','VX-520','SN00278',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(279,'T-2279',NULL,1,'Merchant 279','Contact Person 279','+263770000279','merchant279@example.com','279 Main Street','Region 1','Plumtree','Matabeleland South',NULL,'Services','2025-01-28','iWL252','SN00279',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(280,'T-2280',NULL,1,'Merchant 280','Contact Person 280','+263770000280','merchant280@example.com','280 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-01','VX-520','SN00280',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(281,'T-2281',NULL,1,'Merchant 281','Contact Person 281','+263770000281','merchant281@example.com','281 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-02','iWL252','SN00281',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(282,'T-2282',NULL,1,'Merchant 282','Contact Person 282','+263770000282','merchant282@example.com','282 Main Street','Region 1','Chipinge','Manicaland',NULL,'Retail','2025-01-03','VX-520','SN00282',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(283,'T-2283',NULL,1,'Merchant 283','Contact Person 283','+263770000283','merchant283@example.com','283 Main Street','Region 2','Mazowe','Mashonaland Central',NULL,'Services','2025-01-04','iWL252','SN00283',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(284,'T-2284',NULL,1,'Merchant 284','Contact Person 284','+263770000284','merchant284@example.com','284 Main Street','Region 3','Mutoko','Mashonaland East',NULL,'Retail','2025-01-05','VX-520','SN00284',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(285,'T-2285',NULL,1,'Merchant 285','Contact Person 285','+263770000285','merchant285@example.com','285 Main Street','Region 1','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-06','iWL252','SN00285',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(286,'T-2286',NULL,1,'Merchant 286','Contact Person 286','+263770000286','merchant286@example.com','286 Main Street','Region 2','Masvingo','Masvingo',NULL,'Retail','2025-01-07','VX-520','SN00286',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(287,'T-2287',NULL,1,'Merchant 287','Contact Person 287','+263770000287','merchant287@example.com','287 Main Street','Region 3','Gweru','Midlands',NULL,'Services','2025-01-08','iWL252','SN00287',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(288,'T-2288',NULL,1,'Merchant 288','Contact Person 288','+263770000288','merchant288@example.com','288 Main Street','Region 1','Lupane','Matabeleland North',NULL,'Retail','2025-01-09','VX-520','SN00288',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(289,'T-2289',NULL,1,'Merchant 289','Contact Person 289','+263770000289','merchant289@example.com','289 Main Street','Region 2','Plumtree','Matabeleland South',NULL,'Services','2025-01-10','iWL252','SN00289',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(290,'T-2290',NULL,1,'Merchant 290','Contact Person 290','+263770000290','merchant290@example.com','290 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-11','VX-520','SN00290',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(291,'T-2291',NULL,1,'Merchant 291','Contact Person 291','+263770000291','merchant291@example.com','291 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-12','iWL252','SN00291',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(292,'T-2292',NULL,1,'Merchant 292','Contact Person 292','+263770000292','merchant292@example.com','292 Main Street','Region 2','Chipinge','Manicaland',NULL,'Retail','2025-01-13','VX-520','SN00292',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(293,'T-2293',NULL,1,'Merchant 293','Contact Person 293','+263770000293','merchant293@example.com','293 Main Street','Region 3','Bindura','Mashonaland Central',NULL,'Services','2025-01-14','iWL252','SN00293',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(294,'T-2294',NULL,1,'Merchant 294','Contact Person 294','+263770000294','merchant294@example.com','294 Main Street','Region 1','Murehwa','Mashonaland East',NULL,'Retail','2025-01-15','VX-520','SN00294',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(295,'T-2295',NULL,1,'Merchant 295','Contact Person 295','+263770000295','merchant295@example.com','295 Main Street','Region 2','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-16','iWL252','SN00295',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(296,'T-2296',NULL,1,'Merchant 296','Contact Person 296','+263770000296','merchant296@example.com','296 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-17','VX-520','SN00296',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(297,'T-2297',NULL,1,'Merchant 297','Contact Person 297','+263770000297','merchant297@example.com','297 Main Street','Region 1','Gweru','Midlands',NULL,'Services','2025-01-18','iWL252','SN00297',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(298,'T-2298',NULL,1,'Merchant 298','Contact Person 298','+263770000298','merchant298@example.com','298 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-19','VX-520','SN00298',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(299,'T-2299',NULL,1,'Merchant 299','Contact Person 299','+263770000299','merchant299@example.com','299 Main Street','Region 3','Plumtree','Matabeleland South',NULL,'Services','2025-01-20','iWL252','SN00299',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(300,'T-2300',NULL,1,'Merchant 300','Contact Person 300','+263770000300','merchant300@example.com','300 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-21','VX-520','SN00300',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(301,'T-2301',NULL,1,'Merchant 301','Contact Person 301','+263770000301','merchant301@example.com','301 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-22','iWL252','SN00301',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(302,'T-2302',NULL,1,'Merchant 302','Contact Person 302','+263770000302','merchant302@example.com','302 Main Street','Region 3','Rusape','Manicaland',NULL,'Retail','2025-01-23','VX-520','SN00302',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(303,'T-2303',NULL,1,'Merchant 303','Contact Person 303','+263770000303','merchant303@example.com','303 Main Street','Region 1','Shamva','Mashonaland Central',NULL,'Services','2025-01-24','iWL252','SN00303',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(304,'T-2304',NULL,1,'Merchant 304','Contact Person 304','+263770000304','merchant304@example.com','304 Main Street','Region 2','Marondera','Mashonaland East',NULL,'Retail','2025-01-25','VX-520','SN00304',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(305,'T-2305',NULL,1,'Merchant 305','Contact Person 305','+263770000305','merchant305@example.com','305 Main Street','Region 3','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-26','iWL252','SN00305',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(306,'T-2306',NULL,1,'Merchant 306','Contact Person 306','+263770000306','merchant306@example.com','306 Main Street','Region 1','Gutu','Masvingo',NULL,'Retail','2025-01-27','VX-520','SN00306',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(307,'T-2307',NULL,1,'Merchant 307','Contact Person 307','+263770000307','merchant307@example.com','307 Main Street','Region 2','Kwekwe','Midlands',NULL,'Services','2025-01-28','iWL252','SN00307',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(308,'T-2308',NULL,1,'Merchant 308','Contact Person 308','+263770000308','merchant308@example.com','308 Main Street','Region 3','Lupane','Matabeleland North',NULL,'Retail','2025-01-01','VX-520','SN00308',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(309,'T-2309',NULL,1,'Merchant 309','Contact Person 309','+263770000309','merchant309@example.com','309 Main Street','Region 1','Plumtree','Matabeleland South',NULL,'Services','2025-01-02','iWL252','SN00309',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(310,'T-2310',NULL,1,'Merchant 310','Contact Person 310','+263770000310','merchant310@example.com','310 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-03','VX-520','SN00310',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(311,'T-2311',NULL,1,'Merchant 311','Contact Person 311','+263770000311','merchant311@example.com','311 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-04','iWL252','SN00311',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(312,'T-2312',NULL,1,'Merchant 312','Contact Person 312','+263770000312','merchant312@example.com','312 Main Street','Region 1','Rusape','Manicaland',NULL,'Retail','2025-01-05','VX-520','SN00312',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(313,'T-2313',NULL,1,'Merchant 313','Contact Person 313','+263770000313','merchant313@example.com','313 Main Street','Region 2','Mazowe','Mashonaland Central',NULL,'Services','2025-01-06','iWL252','SN00313',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(314,'T-2314',NULL,1,'Merchant 314','Contact Person 314','+263770000314','merchant314@example.com','314 Main Street','Region 3','Murehwa','Mashonaland East',NULL,'Retail','2025-01-07','VX-520','SN00314',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(315,'T-2315',NULL,1,'Merchant 315','Contact Person 315','+263770000315','merchant315@example.com','315 Main Street','Region 1','Kariba','Mashonaland West',NULL,'Services','2025-01-08','iWL252','SN00315',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(316,'T-2316',NULL,1,'Merchant 316','Contact Person 316','+263770000316','merchant316@example.com','316 Main Street','Region 2','Masvingo','Masvingo',NULL,'Retail','2025-01-09','VX-520','SN00316',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(317,'T-2317',NULL,1,'Merchant 317','Contact Person 317','+263770000317','merchant317@example.com','317 Main Street','Region 3','Kwekwe','Midlands',NULL,'Services','2025-01-10','iWL252','SN00317',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(318,'T-2318',NULL,1,'Merchant 318','Contact Person 318','+263770000318','merchant318@example.com','318 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-11','VX-520','SN00318',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(319,'T-2319',NULL,1,'Merchant 319','Contact Person 319','+263770000319','merchant319@example.com','319 Main Street','Region 2','Gwanda','Matabeleland South',NULL,'Services','2025-01-12','iWL252','SN00319',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(320,'T-2320',NULL,1,'Merchant 320','Contact Person 320','+263770000320','merchant320@example.com','320 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-13','VX-520','SN00320',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(321,'T-2321',NULL,1,'Merchant 321','Contact Person 321','+263770000321','merchant321@example.com','321 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-14','iWL252','SN00321',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(322,'T-2322',NULL,1,'Merchant 322','Contact Person 322','+263770000322','merchant322@example.com','322 Main Street','Region 2','Chipinge','Manicaland',NULL,'Retail','2025-01-15','VX-520','SN00322',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(323,'T-2323',NULL,1,'Merchant 323','Contact Person 323','+263770000323','merchant323@example.com','323 Main Street','Region 3','Bindura','Mashonaland Central',NULL,'Services','2025-01-16','iWL252','SN00323',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(324,'T-2324',NULL,1,'Merchant 324','Contact Person 324','+263770000324','merchant324@example.com','324 Main Street','Region 1','Mutoko','Mashonaland East',NULL,'Retail','2025-01-17','VX-520','SN00324',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(325,'T-2325',NULL,1,'Merchant 325','Contact Person 325','+263770000325','merchant325@example.com','325 Main Street','Region 2','Kadoma','Mashonaland West',NULL,'Services','2025-01-18','iWL252','SN00325',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(326,'T-2326',NULL,1,'Merchant 326','Contact Person 326','+263770000326','merchant326@example.com','326 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-19','VX-520','SN00326',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(327,'T-2327',NULL,1,'Merchant 327','Contact Person 327','+263770000327','merchant327@example.com','327 Main Street','Region 1','Zvishavane','Midlands',NULL,'Services','2025-01-20','iWL252','SN00327',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(328,'T-2328',NULL,1,'Merchant 328','Contact Person 328','+263770000328','merchant328@example.com','328 Main Street','Region 2','Lupane','Matabeleland North',NULL,'Retail','2025-01-21','VX-520','SN00328',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(329,'T-2329',NULL,1,'Merchant 329','Contact Person 329','+263770000329','merchant329@example.com','329 Main Street','Region 3','Beitbridge','Matabeleland South',NULL,'Services','2025-01-22','iWL252','SN00329',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(330,'T-2330',NULL,1,'Merchant 330','Contact Person 330','+263770000330','merchant330@example.com','330 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-23','VX-520','SN00330',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(331,'T-2331',NULL,1,'Merchant 331','Contact Person 331','+263770000331','merchant331@example.com','331 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-24','iWL252','SN00331',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(332,'T-2332',NULL,1,'Merchant 332','Contact Person 332','+263770000332','merchant332@example.com','332 Main Street','Region 3','Chipinge','Manicaland',NULL,'Retail','2025-01-25','VX-520','SN00332',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(333,'T-2333',NULL,1,'Merchant 333','Contact Person 333','+263770000333','merchant333@example.com','333 Main Street','Region 1','Mazowe','Mashonaland Central',NULL,'Services','2025-01-26','iWL252','SN00333',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(334,'T-2334',NULL,1,'Merchant 334','Contact Person 334','+263770000334','merchant334@example.com','334 Main Street','Region 2','Mutoko','Mashonaland East',NULL,'Retail','2025-01-27','VX-520','SN00334',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(335,'T-2335',NULL,1,'Merchant 335','Contact Person 335','+263770000335','merchant335@example.com','335 Main Street','Region 3','Kariba','Mashonaland West',NULL,'Services','2025-01-28','iWL252','SN00335',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(336,'T-2336',NULL,1,'Merchant 336','Contact Person 336','+263770000336','merchant336@example.com','336 Main Street','Region 1','Chiredzi','Masvingo',NULL,'Retail','2025-01-01','VX-520','SN00336',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(337,'T-2337',NULL,1,'Merchant 337','Contact Person 337','+263770000337','merchant337@example.com','337 Main Street','Region 2','Zvishavane','Midlands',NULL,'Services','2025-01-02','iWL252','SN00337',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(338,'T-2338',NULL,1,'Merchant 338','Contact Person 338','+263770000338','merchant338@example.com','338 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-03','VX-520','SN00338',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(339,'T-2339',NULL,1,'Merchant 339','Contact Person 339','+263770000339','merchant339@example.com','339 Main Street','Region 1','Gwanda','Matabeleland South',NULL,'Services','2025-01-04','iWL252','SN00339',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(340,'T-2340',NULL,1,'Merchant 340','Contact Person 340','+263770000340','merchant340@example.com','340 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-05','VX-520','SN00340',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(341,'T-2341',NULL,1,'Merchant 341','Contact Person 341','+263770000341','merchant341@example.com','341 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-06','iWL252','SN00341',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(342,'T-2342',NULL,1,'Merchant 342','Contact Person 342','+263770000342','merchant342@example.com','342 Main Street','Region 1','Mutare','Manicaland',NULL,'Retail','2025-01-07','VX-520','SN00342',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(343,'T-2343',NULL,1,'Merchant 343','Contact Person 343','+263770000343','merchant343@example.com','343 Main Street','Region 2','Shamva','Mashonaland Central',NULL,'Services','2025-01-08','iWL252','SN00343',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(344,'T-2344',NULL,1,'Merchant 344','Contact Person 344','+263770000344','merchant344@example.com','344 Main Street','Region 3','Murehwa','Mashonaland East',NULL,'Retail','2025-01-09','VX-520','SN00344',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(345,'T-2345',NULL,1,'Merchant 345','Contact Person 345','+263770000345','merchant345@example.com','345 Main Street','Region 1','Kadoma','Mashonaland West',NULL,'Services','2025-01-10','iWL252','SN00345',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(346,'T-2346',NULL,1,'Merchant 346','Contact Person 346','+263770000346','merchant346@example.com','346 Main Street','Region 2','Masvingo','Masvingo',NULL,'Retail','2025-01-11','VX-520','SN00346',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(347,'T-2347',NULL,1,'Merchant 347','Contact Person 347','+263770000347','merchant347@example.com','347 Main Street','Region 3','Kwekwe','Midlands',NULL,'Services','2025-01-12','iWL252','SN00347',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(348,'T-2348',NULL,1,'Merchant 348','Contact Person 348','+263770000348','merchant348@example.com','348 Main Street','Region 1','Victoria Falls','Matabeleland North',NULL,'Retail','2025-01-13','VX-520','SN00348',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(349,'T-2349',NULL,1,'Merchant 349','Contact Person 349','+263770000349','merchant349@example.com','349 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-14','iWL252','SN00349',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(350,'T-2350',NULL,1,'Merchant 350','Contact Person 350','+263770000350','merchant350@example.com','350 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-15','VX-520','SN00350',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(351,'T-2351',NULL,1,'Merchant 351','Contact Person 351','+263770000351','merchant351@example.com','351 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-16','iWL252','SN00351',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(352,'T-2352',NULL,1,'Merchant 352','Contact Person 352','+263770000352','merchant352@example.com','352 Main Street','Region 2','Mutare','Manicaland',NULL,'Retail','2025-01-17','VX-520','SN00352',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(353,'T-2353',NULL,1,'Merchant 353','Contact Person 353','+263770000353','merchant353@example.com','353 Main Street','Region 3','Bindura','Mashonaland Central',NULL,'Services','2025-01-18','iWL252','SN00353',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(354,'T-2354',NULL,1,'Merchant 354','Contact Person 354','+263770000354','merchant354@example.com','354 Main Street','Region 1','Murehwa','Mashonaland East',NULL,'Retail','2025-01-19','VX-520','SN00354',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(355,'T-2355',NULL,1,'Merchant 355','Contact Person 355','+263770000355','merchant355@example.com','355 Main Street','Region 2','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-20','iWL252','SN00355',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(356,'T-2356',NULL,1,'Merchant 356','Contact Person 356','+263770000356','merchant356@example.com','356 Main Street','Region 3','Chiredzi','Masvingo',NULL,'Retail','2025-01-21','VX-520','SN00356',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(357,'T-2357',NULL,1,'Merchant 357','Contact Person 357','+263770000357','merchant357@example.com','357 Main Street','Region 1','Zvishavane','Midlands',NULL,'Services','2025-01-22','iWL252','SN00357',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(358,'T-2358',NULL,1,'Merchant 358','Contact Person 358','+263770000358','merchant358@example.com','358 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-23','VX-520','SN00358',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(359,'T-2359',NULL,1,'Merchant 359','Contact Person 359','+263770000359','merchant359@example.com','359 Main Street','Region 3','Beitbridge','Matabeleland South',NULL,'Services','2025-01-24','iWL252','SN00359',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(360,'T-2360',NULL,1,'Merchant 360','Contact Person 360','+263770000360','merchant360@example.com','360 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-25','VX-520','SN00360',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(361,'T-2361',NULL,1,'Merchant 361','Contact Person 361','+263770000361','merchant361@example.com','361 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-26','iWL252','SN00361',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(362,'T-2362',NULL,1,'Merchant 362','Contact Person 362','+263770000362','merchant362@example.com','362 Main Street','Region 3','Mutare','Manicaland',NULL,'Retail','2025-01-27','VX-520','SN00362',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(363,'T-2363',NULL,1,'Merchant 363','Contact Person 363','+263770000363','merchant363@example.com','363 Main Street','Region 1','Shamva','Mashonaland Central',NULL,'Services','2025-01-28','iWL252','SN00363',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(364,'T-2364',NULL,1,'Merchant 364','Contact Person 364','+263770000364','merchant364@example.com','364 Main Street','Region 2','Mutoko','Mashonaland East',NULL,'Retail','2025-01-01','VX-520','SN00364',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(365,'T-2365',NULL,1,'Merchant 365','Contact Person 365','+263770000365','merchant365@example.com','365 Main Street','Region 3','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-02','iWL252','SN00365',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(366,'T-2366',NULL,1,'Merchant 366','Contact Person 366','+263770000366','merchant366@example.com','366 Main Street','Region 1','Chiredzi','Masvingo',NULL,'Retail','2025-01-03','VX-520','SN00366',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(367,'T-2367',NULL,1,'Merchant 367','Contact Person 367','+263770000367','merchant367@example.com','367 Main Street','Region 2','Zvishavane','Midlands',NULL,'Services','2025-01-04','iWL252','SN00367',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(368,'T-2368',NULL,1,'Merchant 368','Contact Person 368','+263770000368','merchant368@example.com','368 Main Street','Region 3','Lupane','Matabeleland North',NULL,'Retail','2025-01-05','VX-520','SN00368',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(369,'T-2369',NULL,1,'Merchant 369','Contact Person 369','+263770000369','merchant369@example.com','369 Main Street','Region 1','Gwanda','Matabeleland South',NULL,'Services','2025-01-06','iWL252','SN00369',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(370,'T-2370',NULL,1,'Merchant 370','Contact Person 370','+263770000370','merchant370@example.com','370 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-07','VX-520','SN00370',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(371,'T-2371',NULL,1,'Merchant 371','Contact Person 371','+263770000371','merchant371@example.com','371 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-08','iWL252','SN00371',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(372,'T-2372',NULL,1,'Merchant 372','Contact Person 372','+263770000372','merchant372@example.com','372 Main Street','Region 1','Rusape','Manicaland',NULL,'Retail','2025-01-09','VX-520','SN00372',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(373,'T-2373',NULL,1,'Merchant 373','Contact Person 373','+263770000373','merchant373@example.com','373 Main Street','Region 2','Shamva','Mashonaland Central',NULL,'Services','2025-01-10','iWL252','SN00373',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(374,'T-2374',NULL,1,'Merchant 374','Contact Person 374','+263770000374','merchant374@example.com','374 Main Street','Region 3','Murehwa','Mashonaland East',NULL,'Retail','2025-01-11','VX-520','SN00374',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(375,'T-2375',NULL,1,'Merchant 375','Contact Person 375','+263770000375','merchant375@example.com','375 Main Street','Region 1','Kariba','Mashonaland West',NULL,'Services','2025-01-12','iWL252','SN00375',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(376,'T-2376',NULL,1,'Merchant 376','Contact Person 376','+263770000376','merchant376@example.com','376 Main Street','Region 2','Chiredzi','Masvingo',NULL,'Retail','2025-01-13','VX-520','SN00376',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(377,'T-2377',NULL,1,'Merchant 377','Contact Person 377','+263770000377','merchant377@example.com','377 Main Street','Region 3','Kwekwe','Midlands',NULL,'Services','2025-01-14','iWL252','SN00377',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(378,'T-2378',NULL,1,'Merchant 378','Contact Person 378','+263770000378','merchant378@example.com','378 Main Street','Region 1','Victoria Falls','Matabeleland North',NULL,'Retail','2025-01-15','VX-520','SN00378',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(379,'T-2379',NULL,1,'Merchant 379','Contact Person 379','+263770000379','merchant379@example.com','379 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-16','iWL252','SN00379',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(380,'T-2380',NULL,1,'Merchant 380','Contact Person 380','+263770000380','merchant380@example.com','380 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-17','VX-520','SN00380',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(381,'T-2381',NULL,1,'Merchant 381','Contact Person 381','+263770000381','merchant381@example.com','381 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-18','iWL252','SN00381',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(382,'T-2382',NULL,1,'Merchant 382','Contact Person 382','+263770000382','merchant382@example.com','382 Main Street','Region 2','Chipinge','Manicaland',NULL,'Retail','2025-01-19','VX-520','SN00382',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(383,'T-2383',NULL,1,'Merchant 383','Contact Person 383','+263770000383','merchant383@example.com','383 Main Street','Region 3','Shamva','Mashonaland Central',NULL,'Services','2025-01-20','iWL252','SN00383',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(384,'T-2384',NULL,1,'Merchant 384','Contact Person 384','+263770000384','merchant384@example.com','384 Main Street','Region 1','Mutoko','Mashonaland East',NULL,'Retail','2025-01-21','VX-520','SN00384',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(385,'T-2385',NULL,1,'Merchant 385','Contact Person 385','+263770000385','merchant385@example.com','385 Main Street','Region 2','Kadoma','Mashonaland West',NULL,'Services','2025-01-22','iWL252','SN00385',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(386,'T-2386',NULL,1,'Merchant 386','Contact Person 386','+263770000386','merchant386@example.com','386 Main Street','Region 3','Gutu','Masvingo',NULL,'Retail','2025-01-23','VX-520','SN00386',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(387,'T-2387',NULL,1,'Merchant 387','Contact Person 387','+263770000387','merchant387@example.com','387 Main Street','Region 1','Zvishavane','Midlands',NULL,'Services','2025-01-24','iWL252','SN00387',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(388,'T-2388',NULL,1,'Merchant 388','Contact Person 388','+263770000388','merchant388@example.com','388 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-25','VX-520','SN00388',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(389,'T-2389',NULL,1,'Merchant 389','Contact Person 389','+263770000389','merchant389@example.com','389 Main Street','Region 3','Beitbridge','Matabeleland South',NULL,'Services','2025-01-26','iWL252','SN00389',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(390,'T-2390',NULL,1,'Merchant 390','Contact Person 390','+263770000390','merchant390@example.com','390 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-27','VX-520','SN00390',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(391,'T-2391',NULL,1,'Merchant 391','Contact Person 391','+263770000391','merchant391@example.com','391 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-28','iWL252','SN00391',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(392,'T-2392',NULL,1,'Merchant 392','Contact Person 392','+263770000392','merchant392@example.com','392 Main Street','Region 3','Rusape','Manicaland',NULL,'Retail','2025-01-01','VX-520','SN00392',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(393,'T-2393',NULL,1,'Merchant 393','Contact Person 393','+263770000393','merchant393@example.com','393 Main Street','Region 1','Shamva','Mashonaland Central',NULL,'Services','2025-01-02','iWL252','SN00393',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(394,'T-2394',NULL,1,'Merchant 394','Contact Person 394','+263770000394','merchant394@example.com','394 Main Street','Region 2','Murehwa','Mashonaland East',NULL,'Retail','2025-01-03','VX-520','SN00394',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(395,'T-2395',NULL,1,'Merchant 395','Contact Person 395','+263770000395','merchant395@example.com','395 Main Street','Region 3','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-04','iWL252','SN00395',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(396,'T-2396',NULL,1,'Merchant 396','Contact Person 396','+263770000396','merchant396@example.com','396 Main Street','Region 1','Chiredzi','Masvingo',NULL,'Retail','2025-01-05','VX-520','SN00396',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(397,'T-2397',NULL,1,'Merchant 397','Contact Person 397','+263770000397','merchant397@example.com','397 Main Street','Region 2','Zvishavane','Midlands',NULL,'Services','2025-01-06','iWL252','SN00397',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(398,'T-2398',NULL,1,'Merchant 398','Contact Person 398','+263770000398','merchant398@example.com','398 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-07','VX-520','SN00398',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(399,'T-2399',NULL,1,'Merchant 399','Contact Person 399','+263770000399','merchant399@example.com','399 Main Street','Region 1','Beitbridge','Matabeleland South',NULL,'Services','2025-01-08','iWL252','SN00399',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(400,'T-2400',NULL,1,'Merchant 400','Contact Person 400','+263770000400','merchant400@example.com','400 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-09','VX-520','SN00400',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(401,'T-2401',NULL,1,'Merchant 401','Contact Person 401','+263770000401','merchant401@example.com','401 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-10','iWL252','SN00401',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(402,'T-2402',NULL,1,'Merchant 402','Contact Person 402','+263770000402','merchant402@example.com','402 Main Street','Region 1','Rusape','Manicaland',NULL,'Retail','2025-01-11','VX-520','SN00402',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(403,'T-2403',NULL,1,'Merchant 403','Contact Person 403','+263770000403','merchant403@example.com','403 Main Street','Region 2','Mazowe','Mashonaland Central',NULL,'Services','2025-01-12','iWL252','SN00403',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(404,'T-2404',NULL,1,'Merchant 404','Contact Person 404','+263770000404','merchant404@example.com','404 Main Street','Region 3','Mutoko','Mashonaland East',NULL,'Retail','2025-01-13','VX-520','SN00404',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(405,'T-2405',NULL,1,'Merchant 405','Contact Person 405','+263770000405','merchant405@example.com','405 Main Street','Region 1','Kariba','Mashonaland West',NULL,'Services','2025-01-14','iWL252','SN00405',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(406,'T-2406',NULL,1,'Merchant 406','Contact Person 406','+263770000406','merchant406@example.com','406 Main Street','Region 2','Gutu','Masvingo',NULL,'Retail','2025-01-15','VX-520','SN00406',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(407,'T-2407',NULL,1,'Merchant 407','Contact Person 407','+263770000407','merchant407@example.com','407 Main Street','Region 3','Gweru','Midlands',NULL,'Services','2025-01-16','iWL252','SN00407',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(408,'T-2408',NULL,1,'Merchant 408','Contact Person 408','+263770000408','merchant408@example.com','408 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-17','VX-520','SN00408',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(409,'T-2409',NULL,1,'Merchant 409','Contact Person 409','+263770000409','merchant409@example.com','409 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-18','iWL252','SN00409',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(410,'T-2410',NULL,1,'Merchant 410','Contact Person 410','+263770000410','merchant410@example.com','410 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-19','VX-520','SN00410',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(411,'T-2411',NULL,1,'Merchant 411','Contact Person 411','+263770000411','merchant411@example.com','411 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-20','iWL252','SN00411',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(412,'T-2412',NULL,1,'Merchant 412','Contact Person 412','+263770000412','merchant412@example.com','412 Main Street','Region 2','Mutare','Manicaland',NULL,'Retail','2025-01-21','VX-520','SN00412',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(413,'T-2413',NULL,1,'Merchant 413','Contact Person 413','+263770000413','merchant413@example.com','413 Main Street','Region 3','Mazowe','Mashonaland Central',NULL,'Services','2025-01-22','iWL252','SN00413',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(414,'T-2414',NULL,1,'Merchant 414','Contact Person 414','+263770000414','merchant414@example.com','414 Main Street','Region 1','Murehwa','Mashonaland East',NULL,'Retail','2025-01-23','VX-520','SN00414',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(415,'T-2415',NULL,1,'Merchant 415','Contact Person 415','+263770000415','merchant415@example.com','415 Main Street','Region 2','Kadoma','Mashonaland West',NULL,'Services','2025-01-24','iWL252','SN00415',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(416,'T-2416',NULL,1,'Merchant 416','Contact Person 416','+263770000416','merchant416@example.com','416 Main Street','Region 3','Chiredzi','Masvingo',NULL,'Retail','2025-01-25','VX-520','SN00416',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(417,'T-2417',NULL,1,'Merchant 417','Contact Person 417','+263770000417','merchant417@example.com','417 Main Street','Region 1','Gweru','Midlands',NULL,'Services','2025-01-26','iWL252','SN00417',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(418,'T-2418',NULL,1,'Merchant 418','Contact Person 418','+263770000418','merchant418@example.com','418 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-27','VX-520','SN00418',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(419,'T-2419',NULL,1,'Merchant 419','Contact Person 419','+263770000419','merchant419@example.com','419 Main Street','Region 3','Plumtree','Matabeleland South',NULL,'Services','2025-01-28','iWL252','SN00419',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(420,'T-2420',NULL,1,'Merchant 420','Contact Person 420','+263770000420','merchant420@example.com','420 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-01','VX-520','SN00420',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(421,'T-2421',NULL,1,'Merchant 421','Contact Person 421','+263770000421','merchant421@example.com','421 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-02','iWL252','SN00421',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(422,'T-2422',NULL,1,'Merchant 422','Contact Person 422','+263770000422','merchant422@example.com','422 Main Street','Region 3','Rusape','Manicaland',NULL,'Retail','2025-01-03','VX-520','SN00422',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(423,'T-2423',NULL,1,'Merchant 423','Contact Person 423','+263770000423','merchant423@example.com','423 Main Street','Region 1','Bindura','Mashonaland Central',NULL,'Services','2025-01-04','iWL252','SN00423',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(424,'T-2424',NULL,1,'Merchant 424','Contact Person 424','+263770000424','merchant424@example.com','424 Main Street','Region 2','Marondera','Mashonaland East',NULL,'Retail','2025-01-05','VX-520','SN00424',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(425,'T-2425',NULL,1,'Merchant 425','Contact Person 425','+263770000425','merchant425@example.com','425 Main Street','Region 3','Kariba','Mashonaland West',NULL,'Services','2025-01-06','iWL252','SN00425',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(426,'T-2426',NULL,1,'Merchant 426','Contact Person 426','+263770000426','merchant426@example.com','426 Main Street','Region 1','Gutu','Masvingo',NULL,'Retail','2025-01-07','VX-520','SN00426',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(427,'T-2427',NULL,1,'Merchant 427','Contact Person 427','+263770000427','merchant427@example.com','427 Main Street','Region 2','Gweru','Midlands',NULL,'Services','2025-01-08','iWL252','SN00427',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(428,'T-2428',NULL,1,'Merchant 428','Contact Person 428','+263770000428','merchant428@example.com','428 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-09','VX-520','SN00428',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(429,'T-2429',NULL,1,'Merchant 429','Contact Person 429','+263770000429','merchant429@example.com','429 Main Street','Region 1','Gwanda','Matabeleland South',NULL,'Services','2025-01-10','iWL252','SN00429',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(430,'T-2430',NULL,1,'Merchant 430','Contact Person 430','+263770000430','merchant430@example.com','430 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-11','VX-520','SN00430',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(431,'T-2431',NULL,1,'Merchant 431','Contact Person 431','+263770000431','merchant431@example.com','431 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-12','iWL252','SN00431',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(432,'T-2432',NULL,1,'Merchant 432','Contact Person 432','+263770000432','merchant432@example.com','432 Main Street','Region 1','Mutare','Manicaland',NULL,'Retail','2025-01-13','VX-520','SN00432',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(433,'T-2433',NULL,1,'Merchant 433','Contact Person 433','+263770000433','merchant433@example.com','433 Main Street','Region 2','Shamva','Mashonaland Central',NULL,'Services','2025-01-14','iWL252','SN00433',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(434,'T-2434',NULL,1,'Merchant 434','Contact Person 434','+263770000434','merchant434@example.com','434 Main Street','Region 3','Mutoko','Mashonaland East',NULL,'Retail','2025-01-15','VX-520','SN00434',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(435,'T-2435',NULL,1,'Merchant 435','Contact Person 435','+263770000435','merchant435@example.com','435 Main Street','Region 1','Kariba','Mashonaland West',NULL,'Services','2025-01-16','iWL252','SN00435',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(436,'T-2436',NULL,1,'Merchant 436','Contact Person 436','+263770000436','merchant436@example.com','436 Main Street','Region 2','Chiredzi','Masvingo',NULL,'Retail','2025-01-17','VX-520','SN00436',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(437,'T-2437',NULL,1,'Merchant 437','Contact Person 437','+263770000437','merchant437@example.com','437 Main Street','Region 3','Zvishavane','Midlands',NULL,'Services','2025-01-18','iWL252','SN00437',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(438,'T-2438',NULL,1,'Merchant 438','Contact Person 438','+263770000438','merchant438@example.com','438 Main Street','Region 1','Lupane','Matabeleland North',NULL,'Retail','2025-01-19','VX-520','SN00438',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(439,'T-2439',NULL,1,'Merchant 439','Contact Person 439','+263770000439','merchant439@example.com','439 Main Street','Region 2','Beitbridge','Matabeleland South',NULL,'Services','2025-01-20','iWL252','SN00439',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(440,'T-2440',NULL,1,'Merchant 440','Contact Person 440','+263770000440','merchant440@example.com','440 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-21','VX-520','SN00440',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(441,'T-2441',NULL,1,'Merchant 441','Contact Person 441','+263770000441','merchant441@example.com','441 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-22','iWL252','SN00441',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(442,'T-2442',NULL,1,'Merchant 442','Contact Person 442','+263770000442','merchant442@example.com','442 Main Street','Region 2','Mutare','Manicaland',NULL,'Retail','2025-01-23','VX-520','SN00442',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(443,'T-2443',NULL,1,'Merchant 443','Contact Person 443','+263770000443','merchant443@example.com','443 Main Street','Region 3','Shamva','Mashonaland Central',NULL,'Services','2025-01-24','iWL252','SN00443',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(444,'T-2444',NULL,1,'Merchant 444','Contact Person 444','+263770000444','merchant444@example.com','444 Main Street','Region 1','Murehwa','Mashonaland East',NULL,'Retail','2025-01-25','VX-520','SN00444',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(445,'T-2445',NULL,1,'Merchant 445','Contact Person 445','+263770000445','merchant445@example.com','445 Main Street','Region 2','Chinhoyi','Mashonaland West',NULL,'Services','2025-01-26','iWL252','SN00445',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(446,'T-2446',NULL,1,'Merchant 446','Contact Person 446','+263770000446','merchant446@example.com','446 Main Street','Region 3','Chiredzi','Masvingo',NULL,'Retail','2025-01-27','VX-520','SN00446',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(447,'T-2447',NULL,1,'Merchant 447','Contact Person 447','+263770000447','merchant447@example.com','447 Main Street','Region 1','Kwekwe','Midlands',NULL,'Services','2025-01-28','iWL252','SN00447',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(448,'T-2448',NULL,1,'Merchant 448','Contact Person 448','+263770000448','merchant448@example.com','448 Main Street','Region 2','Lupane','Matabeleland North',NULL,'Retail','2025-01-01','VX-520','SN00448',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(449,'T-2449',NULL,1,'Merchant 449','Contact Person 449','+263770000449','merchant449@example.com','449 Main Street','Region 3','Beitbridge','Matabeleland South',NULL,'Services','2025-01-02','iWL252','SN00449',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(450,'T-2450',NULL,1,'Merchant 450','Contact Person 450','+263770000450','merchant450@example.com','450 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-03','VX-520','SN00450',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(451,'T-2451',NULL,1,'Merchant 451','Contact Person 451','+263770000451','merchant451@example.com','451 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-04','iWL252','SN00451',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(452,'T-2452',NULL,1,'Merchant 452','Contact Person 452','+263770000452','merchant452@example.com','452 Main Street','Region 3','Mutare','Manicaland',NULL,'Retail','2025-01-05','VX-520','SN00452',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(453,'T-2453',NULL,1,'Merchant 453','Contact Person 453','+263770000453','merchant453@example.com','453 Main Street','Region 1','Bindura','Mashonaland Central',NULL,'Services','2025-01-06','iWL252','SN00453',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(454,'T-2454',NULL,1,'Merchant 454','Contact Person 454','+263770000454','merchant454@example.com','454 Main Street','Region 2','Mutoko','Mashonaland East',NULL,'Retail','2025-01-07','VX-520','SN00454',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(455,'T-2455',NULL,1,'Merchant 455','Contact Person 455','+263770000455','merchant455@example.com','455 Main Street','Region 3','Kariba','Mashonaland West',NULL,'Services','2025-01-08','iWL252','SN00455',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(456,'T-2456',NULL,1,'Merchant 456','Contact Person 456','+263770000456','merchant456@example.com','456 Main Street','Region 1','Masvingo','Masvingo',NULL,'Retail','2025-01-09','VX-520','SN00456',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(457,'T-2457',NULL,1,'Merchant 457','Contact Person 457','+263770000457','merchant457@example.com','457 Main Street','Region 2','Zvishavane','Midlands',NULL,'Services','2025-01-10','iWL252','SN00457',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(458,'T-2458',NULL,1,'Merchant 458','Contact Person 458','+263770000458','merchant458@example.com','458 Main Street','Region 3','Hwange','Matabeleland North',NULL,'Retail','2025-01-11','VX-520','SN00458',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(459,'T-2459',NULL,1,'Merchant 459','Contact Person 459','+263770000459','merchant459@example.com','459 Main Street','Region 1','Beitbridge','Matabeleland South',NULL,'Services','2025-01-12','iWL252','SN00459',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(460,'T-2460',NULL,1,'Merchant 460','Contact Person 460','+263770000460','merchant460@example.com','460 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-13','VX-520','SN00460',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(461,'T-2461',NULL,1,'Merchant 461','Contact Person 461','+263770000461','merchant461@example.com','461 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-14','iWL252','SN00461',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(462,'T-2462',NULL,1,'Merchant 462','Contact Person 462','+263770000462','merchant462@example.com','462 Main Street','Region 1','Chipinge','Manicaland',NULL,'Retail','2025-01-15','VX-520','SN00462',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(463,'T-2463',NULL,1,'Merchant 463','Contact Person 463','+263770000463','merchant463@example.com','463 Main Street','Region 2','Bindura','Mashonaland Central',NULL,'Services','2025-01-16','iWL252','SN00463',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(464,'T-2464',NULL,1,'Merchant 464','Contact Person 464','+263770000464','merchant464@example.com','464 Main Street','Region 3','Murehwa','Mashonaland East',NULL,'Retail','2025-01-17','VX-520','SN00464',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(465,'T-2465',NULL,1,'Merchant 465','Contact Person 465','+263770000465','merchant465@example.com','465 Main Street','Region 1','Kadoma','Mashonaland West',NULL,'Services','2025-01-18','iWL252','SN00465',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(466,'T-2466',NULL,1,'Merchant 466','Contact Person 466','+263770000466','merchant466@example.com','466 Main Street','Region 2','Chiredzi','Masvingo',NULL,'Retail','2025-01-19','VX-520','SN00466',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(467,'T-2467',NULL,1,'Merchant 467','Contact Person 467','+263770000467','merchant467@example.com','467 Main Street','Region 3','Gweru','Midlands',NULL,'Services','2025-01-20','iWL252','SN00467',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(468,'T-2468',NULL,1,'Merchant 468','Contact Person 468','+263770000468','merchant468@example.com','468 Main Street','Region 1','Lupane','Matabeleland North',NULL,'Retail','2025-01-21','VX-520','SN00468',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(469,'T-2469',NULL,1,'Merchant 469','Contact Person 469','+263770000469','merchant469@example.com','469 Main Street','Region 2','Gwanda','Matabeleland South',NULL,'Services','2025-01-22','iWL252','SN00469',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(470,'T-2470',NULL,1,'Merchant 470','Contact Person 470','+263770000470','merchant470@example.com','470 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-23','VX-520','SN00470',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(471,'T-2471',NULL,1,'Merchant 471','Contact Person 471','+263770000471','merchant471@example.com','471 Main Street','Region 1','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-24','iWL252','SN00471',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(472,'T-2472',NULL,1,'Merchant 472','Contact Person 472','+263770000472','merchant472@example.com','472 Main Street','Region 2','Rusape','Manicaland',NULL,'Retail','2025-01-25','VX-520','SN00472',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(473,'T-2473',NULL,1,'Merchant 473','Contact Person 473','+263770000473','merchant473@example.com','473 Main Street','Region 3','Mazowe','Mashonaland Central',NULL,'Services','2025-01-26','iWL252','SN00473',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(474,'T-2474',NULL,1,'Merchant 474','Contact Person 474','+263770000474','merchant474@example.com','474 Main Street','Region 1','Marondera','Mashonaland East',NULL,'Retail','2025-01-27','VX-520','SN00474',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(475,'T-2475',NULL,1,'Merchant 475','Contact Person 475','+263770000475','merchant475@example.com','475 Main Street','Region 2','Kadoma','Mashonaland West',NULL,'Services','2025-01-28','iWL252','SN00475',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(476,'T-2476',NULL,1,'Merchant 476','Contact Person 476','+263770000476','merchant476@example.com','476 Main Street','Region 3','Chiredzi','Masvingo',NULL,'Retail','2025-01-01','VX-520','SN00476',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(477,'T-2477',NULL,1,'Merchant 477','Contact Person 477','+263770000477','merchant477@example.com','477 Main Street','Region 1','Zvishavane','Midlands',NULL,'Services','2025-01-02','iWL252','SN00477',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(478,'T-2478',NULL,1,'Merchant 478','Contact Person 478','+263770000478','merchant478@example.com','478 Main Street','Region 2','Hwange','Matabeleland North',NULL,'Retail','2025-01-03','VX-520','SN00478',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(479,'T-2479',NULL,1,'Merchant 479','Contact Person 479','+263770000479','merchant479@example.com','479 Main Street','Region 3','Plumtree','Matabeleland South',NULL,'Services','2025-01-04','iWL252','SN00479',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(480,'T-2480',NULL,1,'Merchant 480','Contact Person 480','+263770000480','merchant480@example.com','480 Main Street','Region 1','Harare','Harare Metropolitan',NULL,'Retail','2025-01-05','VX-520','SN00480',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(481,'T-2481',NULL,1,'Merchant 481','Contact Person 481','+263770000481','merchant481@example.com','481 Main Street','Region 2','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-06','iWL252','SN00481',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(482,'T-2482',NULL,1,'Merchant 482','Contact Person 482','+263770000482','merchant482@example.com','482 Main Street','Region 3','Rusape','Manicaland',NULL,'Retail','2025-01-07','VX-520','SN00482',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(483,'T-2483',NULL,1,'Merchant 483','Contact Person 483','+263770000483','merchant483@example.com','483 Main Street','Region 1','Shamva','Mashonaland Central',NULL,'Services','2025-01-08','iWL252','SN00483',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(484,'T-2484',NULL,1,'Merchant 484','Contact Person 484','+263770000484','merchant484@example.com','484 Main Street','Region 2','Murehwa','Mashonaland East',NULL,'Retail','2025-01-09','VX-520','SN00484',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(485,'T-2485',NULL,1,'Merchant 485','Contact Person 485','+263770000485','merchant485@example.com','485 Main Street','Region 3','Kadoma','Mashonaland West',NULL,'Services','2025-01-10','iWL252','SN00485',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(486,'T-2486',NULL,1,'Merchant 486','Contact Person 486','+263770000486','merchant486@example.com','486 Main Street','Region 1','Masvingo','Masvingo',NULL,'Retail','2025-01-11','VX-520','SN00486',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(487,'T-2487',NULL,1,'Merchant 487','Contact Person 487','+263770000487','merchant487@example.com','487 Main Street','Region 2','Kwekwe','Midlands',NULL,'Services','2025-01-12','iWL252','SN00487',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(488,'T-2488',NULL,1,'Merchant 488','Contact Person 488','+263770000488','merchant488@example.com','488 Main Street','Region 3','Lupane','Matabeleland North',NULL,'Retail','2025-01-13','VX-520','SN00488',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(489,'T-2489',NULL,1,'Merchant 489','Contact Person 489','+263770000489','merchant489@example.com','489 Main Street','Region 1','Beitbridge','Matabeleland South',NULL,'Services','2025-01-14','iWL252','SN00489',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(490,'T-2490',NULL,1,'Merchant 490','Contact Person 490','+263770000490','merchant490@example.com','490 Main Street','Region 2','Harare','Harare Metropolitan',NULL,'Retail','2025-01-15','VX-520','SN00490',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(491,'T-2491',NULL,1,'Merchant 491','Contact Person 491','+263770000491','merchant491@example.com','491 Main Street','Region 3','Bulawayo','Bulawayo Metropolitan',NULL,'Services','2025-01-16','iWL252','SN00491',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(492,'T-2492',NULL,1,'Merchant 492','Contact Person 492','+263770000492','merchant492@example.com','492 Main Street','Region 1','Chipinge','Manicaland',NULL,'Retail','2025-01-17','VX-520','SN00492',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(493,'T-2493',NULL,1,'Merchant 493','Contact Person 493','+263770000493','merchant493@example.com','493 Main Street','Region 2','Bindura','Mashonaland Central',NULL,'Services','2025-01-18','iWL252','SN00493',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(494,'T-2494',NULL,1,'Merchant 494','Contact Person 494','+263770000494','merchant494@example.com','494 Main Street','Region 3','Murehwa','Mashonaland East',NULL,'Retail','2025-01-19','VX-520','SN00494',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(495,'T-2495',NULL,1,'Merchant 495','Contact Person 495','+263770000495','merchant495@example.com','495 Main Street','Region 1','Kadoma','Mashonaland West',NULL,'Services','2025-01-20','iWL252','SN00495',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(496,'T-2496',NULL,1,'Merchant 496','Contact Person 496','+263770000496','merchant496@example.com','496 Main Street','Region 2','Gutu','Masvingo',NULL,'Retail','2025-01-21','VX-520','SN00496',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(497,'T-2497',NULL,1,'Merchant 497','Contact Person 497','+263770000497','merchant497@example.com','497 Main Street','Region 3','Zvishavane','Midlands',NULL,'Services','2025-01-22','iWL252','SN00497',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(498,'T-2498',NULL,1,'Merchant 498','Contact Person 498','+263770000498','merchant498@example.com','498 Main Street','Region 1','Hwange','Matabeleland North',NULL,'Retail','2025-01-23','VX-520','SN00498',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(499,'T-2499',NULL,1,'Merchant 499','Contact Person 499','+263770000499','merchant499@example.com','499 Main Street','Region 2','Plumtree','Matabeleland South',NULL,'Services','2025-01-24','iWL252','SN00499',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active'),(500,'T-2500',NULL,1,'Merchant 500','Contact Person 500','+263770000500','merchant500@example.com','500 Main Street','Region 3','Harare','Harare Metropolitan',NULL,'Retail','2025-01-25','VX-520','SN00500',NULL,'active',NULL,NULL,'2026-01-12 14:09:43','2026-01-12 14:09:43','active');
/*!40000 ALTER TABLE `pos_terminals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_terminals`
--

DROP TABLE IF EXISTS `project_terminals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_terminals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `terminal_id` bigint unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_terminals_project_id_terminal_id_unique` (`project_id`,`terminal_id`),
  KEY `project_terminals_terminal_id_foreign` (`terminal_id`),
  CONSTRAINT `project_terminals_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_terminals_terminal_id_foreign` FOREIGN KEY (`terminal_id`) REFERENCES `pos_terminals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_terminals`
--

LOCK TABLES `project_terminals` WRITE;
/*!40000 ALTER TABLE `project_terminals` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_terminals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `project_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `project_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','completed','paused','cancelled','closed','on_hold') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `priority` enum('emergency','high','normal','low') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `budget` decimal(15,2) DEFAULT NULL,
  `estimated_terminals_count` int NOT NULL DEFAULT '0',
  `actual_terminals_count` int NOT NULL DEFAULT '0',
  `completion_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `project_manager_id` bigint unsigned DEFAULT NULL,
  `previous_project_id` bigint unsigned DEFAULT NULL,
  `insights_from_previous` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terminal_selection_criteria` json DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `completed_by` bigint unsigned DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `closed_by` bigint unsigned DEFAULT NULL,
  `closure_reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_generated_at` timestamp NULL DEFAULT NULL,
  `report_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `projects_project_code_unique` (`project_code`),
  KEY `projects_client_id_foreign` (`client_id`),
  KEY `projects_project_manager_id_foreign` (`project_manager_id`),
  KEY `projects_previous_project_id_foreign` (`previous_project_id`),
  KEY `projects_created_by_foreign` (`created_by`),
  KEY `projects_completed_by_foreign` (`completed_by`),
  KEY `projects_closed_by_foreign` (`closed_by`),
  KEY `projects_status_index` (`status`),
  KEY `projects_project_type_index` (`project_type`),
  KEY `projects_start_date_index` (`start_date`),
  KEY `projects_end_date_index` (`end_date`),
  CONSTRAINT `projects_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_previous_project_id_foreign` FOREIGN KEY (`previous_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_project_manager_id_foreign` FOREIGN KEY (`project_manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'ENV1652-DIS-20260112-266','trial project',1,'discovery','xxx','2026-01-12',NULL,'active','normal',NULL,500,0,0.00,1,NULL,NULL,NULL,'xxx',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-12 18:31:03','2026-01-12 18:31:03'),(2,'ENV1652-DIS-20260112-081','trial project',1,'discovery','xxx','2026-01-12',NULL,'active','normal',NULL,500,0,0.00,1,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-12 18:40:34','2026-01-12 18:40:34'),(3,'TRI0636-MAI-20260127-176','trial project123',2,'maintenance',NULL,'2026-01-27','2026-02-26','active','normal',NULL,0,0,0.00,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-01-27 09:41:08','2026-01-27 09:41:08');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regions`
--

DROP TABLE IF EXISTS `regions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regions`
--

LOCK TABLES `regions` WRITE;
/*!40000 ALTER TABLE `regions` DISABLE KEYS */;
INSERT INTO `regions` VALUES (1,'North','Northern region covering areas like Westlands, Kasarani, and Thika Road','2026-01-09 07:47:39','2026-01-09 07:47:39',1),(2,'South','Southern region covering areas like Lang\'ata, Karen, and Rongai','2026-01-09 07:47:39','2026-01-09 07:47:39',1),(3,'East','Eastern region covering areas like Eastleigh, Donholm, and Embakasi','2026-01-09 07:47:39','2026-01-09 07:47:39',1),(4,'West','Western region covering areas like Ngong Road, Kawangware, and Kikuyu','2026-01-09 07:47:40','2026-01-09 07:47:40',1),(5,'Central','Central region covering CBD, Upper Hill, and surrounding areas','2026-01-09 07:47:40','2026-01-09 07:47:40',1);
/*!40000 ALTER TABLE `regions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_templates`
--

DROP TABLE IF EXISTS `report_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `report_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_global` tinyint(1) DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `configuration` json DEFAULT NULL,
  `fields` json DEFAULT NULL,
  `filters` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_is_global` (`is_global`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_templates`
--

LOCK TABLES `report_templates` WRITE;
/*!40000 ALTER TABLE `report_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_has_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(1,2),(1,3),(1,4),(1,5);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1,1,NULL,NULL),(2,1,2,'2026-01-12 07:54:33','2026-01-12 07:54:33'),(3,1,3,'2026-01-12 07:54:33','2026-01-12 07:54:33'),(4,1,4,'2026-01-12 07:54:33','2026-01-12 07:54:33'),(5,1,5,'2026-01-12 07:54:33','2026-01-12 07:54:33'),(6,3,6,'2026-01-14 07:15:03','2026-01-14 07:15:03'),(7,4,6,'2026-01-14 07:15:03','2026-01-14 07:15:03'),(8,2,6,'2026-01-14 07:15:03','2026-01-14 07:15:03'),(9,5,6,'2026-01-14 07:15:03','2026-01-14 07:15:03'),(10,3,7,'2026-01-14 07:19:03','2026-01-14 07:19:03'),(11,3,8,'2026-01-14 07:19:03','2026-01-14 07:19:03'),(12,3,9,'2026-01-14 07:19:03','2026-01-14 07:19:03'),(13,3,10,'2026-01-14 07:19:03','2026-01-14 07:19:03'),(14,3,11,'2026-01-14 07:24:20','2026-01-14 07:24:20'),(15,1,12,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(16,2,16,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(17,2,14,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(18,2,18,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(19,2,13,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(20,2,19,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(21,4,15,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(22,5,18,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(23,5,13,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(24,5,19,'2026-01-14 07:34:56','2026-01-14 07:34:56'),(25,3,21,'2026-01-14 07:36:54','2026-01-14 07:36:54'),(26,3,22,'2026-01-14 07:36:54','2026-01-14 07:36:54'),(27,3,16,'2026-01-14 07:51:58','2026-01-14 07:51:58'),(28,1,23,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(29,1,19,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(30,2,23,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(31,2,1,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(32,3,23,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(33,3,1,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(34,3,19,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(35,4,23,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(36,4,1,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(37,4,19,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(38,5,23,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(39,5,1,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(40,6,23,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(41,6,1,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(42,6,19,'2026-01-14 20:41:32','2026-01-14 20:41:32'),(43,1,24,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(44,1,25,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(45,4,24,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(46,4,25,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(47,2,24,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(48,2,25,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(49,6,24,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(50,6,25,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(51,5,24,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(52,5,25,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(53,3,24,'2026-01-14 20:59:21','2026-01-14 20:59:21'),(54,3,25,'2026-01-14 20:59:21','2026-01-14 20:59:21');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` json DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','Admin','[\"all\"]',NULL,'2026-01-09 07:47:39','2026-01-09 07:47:39'),(2,'Manager','Manager','[\"manage_team\", \"view_reports\", \"approve_requests\", \"manage_assets\", \"view_dashboard\", \"manage_departments\"]',NULL,'2026-01-09 07:47:39','2026-01-09 07:47:39'),(3,'Technician','Technician','[\"view_jobs\", \"update_jobs\", \"create_reports\", \"view_terminals\", \"update_terminals\", \"view_clients\", \"view_own_data\", \"view_assets\"]',NULL,'2026-01-09 07:47:39','2026-01-14 07:29:15'),(4,'Employee','Employee','[\"view_own_data\", \"request_assets\", \"view_documents\", \"update_profile\"]',NULL,'2026-01-09 07:47:39','2026-01-09 07:47:39'),(5,'Supervisor','Supervisor','[\"manage_team\", \"view_reports\", \"approve_minor_requests\", \"view_dashboard\"]',NULL,'2026-01-09 07:47:39','2026-01-09 07:47:39'),(6,'super_admin','Super Administrator',NULL,'Full system access','2026-01-14 07:27:23','2026-01-14 07:27:23');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_reports`
--

DROP TABLE IF EXISTS `service_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_reports`
--

LOCK TABLES `service_reports` WRITE;
/*!40000 ALTER TABLE `service_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technician_visits`
--

DROP TABLE IF EXISTS `technician_visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `technician_visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `technician_id` bigint unsigned DEFAULT NULL,
  `visit_date` datetime DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `pos_terminal_id` bigint unsigned DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `outcome` enum('successful','failed','partial','rescheduled','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pos_terminal_id` (`pos_terminal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technician_visits`
--

LOCK TABLES `technician_visits` WRITE;
/*!40000 ALTER TABLE `technician_visits` DISABLE KEYS */;
/*!40000 ALTER TABLE `technician_visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technicians`
--

DROP TABLE IF EXISTS `technicians`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `technicians` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `employee_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specializations` json DEFAULT NULL,
  `regions` json DEFAULT NULL,
  `availability_status` enum('available','busy','off_duty') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `technicians_employee_id_foreign` (`employee_id`),
  CONSTRAINT `technicians_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technicians`
--

LOCK TABLES `technicians` WRITE;
/*!40000 ALTER TABLE `technicians` DISABLE KEYS */;
/*!40000 ALTER TABLE `technicians` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_steps`
--

DROP TABLE IF EXISTS `ticket_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_steps` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `step_number` int NOT NULL DEFAULT '1',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'What work was done in this step',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Additional notes about this step',
  `resolution_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'What was resolved in this step',
  `transferred_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Why was this transferred to next person',
  `transferred_to` bigint unsigned DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_steps_transferred_to_foreign` (`transferred_to`),
  KEY `ticket_steps_ticket_id_index` (`ticket_id`),
  KEY `ticket_steps_employee_id_index` (`employee_id`),
  KEY `ticket_steps_status_index` (`status`),
  CONSTRAINT `ticket_steps_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `ticket_steps_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_steps_transferred_to_foreign` FOREIGN KEY (`transferred_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_steps`
--

LOCK TABLES `ticket_steps` WRITE;
/*!40000 ALTER TABLE `ticket_steps` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_steps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ticket_type` enum('pos_terminal','internal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pos_terminal',
  `assignment_type` enum('public','direct') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `estimated_resolution_days` int DEFAULT NULL COMMENT 'Estimated resolution time in days',
  `mobile_created` tinyint(1) NOT NULL DEFAULT '0',
  `offline_sync_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `technician_id` bigint unsigned DEFAULT NULL,
  `pos_terminal_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `visit_id` bigint unsigned DEFAULT NULL,
  `issue_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('open','in_progress','resolved','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `assigned_to` bigint unsigned DEFAULT NULL,
  `resolution` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `attachments` json DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_technician_id_foreign` (`technician_id`),
  KEY `tickets_pos_terminal_id_foreign` (`pos_terminal_id`),
  KEY `tickets_client_id_foreign` (`client_id`),
  KEY `tickets_visit_id_foreign` (`visit_id`),
  KEY `tickets_assigned_to_foreign` (`assigned_to`),
  KEY `tickets_ticket_type_index` (`ticket_type`),
  KEY `tickets_assignment_type_index` (`assignment_type`),
  CONSTRAINT `tickets_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_pos_terminal_id_foreign` FOREIGN KEY (`pos_terminal_id`) REFERENCES `pos_terminals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,'TCK-2026-H9ZMZK','pos_terminal','public',NULL,0,NULL,'2026-01-21 09:28:01','2026-01-21 09:28:01',3,NULL,NULL,NULL,'Technical Issue','low','5d5dd5e','5dd5ddd yvvugug jbububugub ububbub ubugugug uugigg','open',3,NULL,NULL,NULL),(2,'TKT-2026-001','internal','direct',NULL,0,NULL,'2026-01-21 11:53:09','2026-01-21 11:53:09',1,NULL,NULL,NULL,'hardware_malfunction','low','trial run','non','open',3,NULL,NULL,NULL);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
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
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visit_terminals`
--

DROP TABLE IF EXISTS `visit_terminals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visit_terminals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `visit_id` bigint unsigned NOT NULL,
  `terminal_id` bigint unsigned DEFAULT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `condition` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `terminal_model` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visit_terminals_visit_id_status_index` (`visit_id`,`status`),
  KEY `visit_terminals_terminal_id_index` (`terminal_id`),
  CONSTRAINT `visit_terminals_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visit_terminals`
--

LOCK TABLES `visit_terminals` WRITE;
/*!40000 ALTER TABLE `visit_terminals` DISABLE KEYS */;
/*!40000 ALTER TABLE `visit_terminals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `merchant_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `merchant_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_id` bigint unsigned DEFAULT NULL,
  `assignment_id` bigint unsigned DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `contact_person` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visit_summary` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `action_points` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `evidence` json DEFAULT NULL,
  `signature` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `other_terminals_found` json DEFAULT NULL,
  `terminal` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `visits_merchant_id_index` (`merchant_id`),
  KEY `visits_employee_id_index` (`employee_id`),
  KEY `visits_assignment_id_index` (`assignment_id`),
  KEY `visits_completed_at_index` (`completed_at`),
  CONSTRAINT `visits_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visits`
--

LOCK TABLES `visits` WRITE;
/*!40000 ALTER TABLE `visits` DISABLE KEYS */;
/*!40000 ALTER TABLE `visits` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-18  5:05:25
