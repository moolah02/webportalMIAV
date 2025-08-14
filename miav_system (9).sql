-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 09:22 AM
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
-- Database: `miav_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `currency` varchar(3) DEFAULT 'USD',
  `stock_quantity` int(11) DEFAULT 0,
  `assigned_quantity` int(11) DEFAULT 0,
  `available_quantity` int(11) GENERATED ALWAYS AS (`stock_quantity` - `assigned_quantity`) STORED,
  `min_stock_level` int(11) DEFAULT 0,
  `sku` varchar(255) DEFAULT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `image_url` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'asset-active',
  `is_requestable` tinyint(1) DEFAULT 1,
  `requires_approval` tinyint(1) DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `name`, `description`, `category`, `brand`, `model`, `unit_price`, `currency`, `stock_quantity`, `assigned_quantity`, `min_stock_level`, `sku`, `barcode`, `specifications`, `image_url`, `status`, `is_requestable`, `requires_approval`, `notes`, `created_at`, `updated_at`) VALUES
(29, 'MacBook Pro 16\"', '16-inch MacBook Pro with M2 Pro chip, 16GB RAM, 512GB SSD', 'Computer and IT Equipment', 'Apple', 'MacBook Pro 16-inch', 2499.00, 'USD', 10, 4, 2, 'APPLE-MBP16-M2', NULL, NULL, NULL, 'asset-active', 1, 1, 'For development and design work', '2025-08-04 19:00:09', '2025-08-06 10:19:55'),
(30, 'ThinkPad X1 Carbon', 'Ultrabook laptop with Intel i7, 16GB RAM, 512GB SSD', 'Computer and IT Equipment', 'Lenovo', 'ThinkPad X1 Carbon Gen 10', 1899.00, 'USD', 8, 0, 2, 'LEN-X1C-G10', NULL, NULL, NULL, 'asset-active', 1, 1, 'Business laptops for executives', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(31, 'Gaming Desktop PC', 'High-performance desktop with RTX 4080, 32GB RAM, 1TB NVMe', 'Computer and IT Equipment', 'Custom Build', 'Workstation Pro', 3299.00, 'USD', 3, 0, 1, 'CUST-WS-PRO', NULL, NULL, NULL, 'asset-active', 1, 1, 'For video editing and 3D rendering', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(32, '4K Monitor 27\"', '27-inch 4K IPS monitor with USB-C hub', 'Computer and IT Equipment', 'Dell', 'U2723QE', 649.00, 'USD', 15, 0, 3, 'DELL-MON-27-4K', NULL, NULL, NULL, 'asset-active', 1, 0, 'Standard monitors for workstations', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(33, 'Ultrawide Monitor 34\"', '34-inch curved ultrawide monitor, 3440x1440', 'Computer and IT Equipment', 'LG', '34WN80C-B', 449.00, 'USD', 6, 0, 2, 'LG-UW-34', NULL, NULL, NULL, 'asset-active', 1, 1, 'For productivity and multitasking', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(34, 'Executive Office Chair', 'Ergonomic leather executive chair with lumbar support', 'Office Furniture', 'Herman Miller', 'Aeron Chair Size B', 1395.00, 'USD', 12, 0, 2, 'HM-AERON-B', NULL, NULL, NULL, 'asset-active', 1, 1, 'Premium chairs for executives', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(35, 'Standing Desk', 'Electric height-adjustable standing desk 60x30 inches', 'Office Furniture', 'Steelcase', 'Series 7 Desk', 1200.00, 'USD', 8, 0, 2, 'SC-S7-DESK', NULL, NULL, NULL, 'asset-active', 1, 1, 'Adjustable desks for health-conscious employees', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(36, 'Conference Table', 'Large conference table for 8-10 people', 'Office Furniture', 'Knoll', 'Reff Profiles Table', 2500.00, 'USD', 2, 0, 1, 'KNOLL-CONF-8', NULL, NULL, NULL, 'asset-active', 1, 1, 'For meeting rooms', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(37, 'Company Van', 'Cargo van for equipment transport and deliveries', 'Vehicles', 'Ford', 'Transit 350', 45000.00, 'USD', 3, 0, 1, 'FORD-VAN-001', NULL, NULL, NULL, 'asset-active', 1, 1, 'For field operations and equipment transport', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(38, 'Sedan Company Car', 'Fuel-efficient sedan for business travel', 'Vehicles', 'Toyota', 'Camry Hybrid LE', 32000.00, 'USD', 5, 0, 1, 'TOY-CAM-HYB', NULL, NULL, NULL, 'asset-active', 1, 1, 'For sales team and client visits', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(39, 'Drill Set Professional', 'Cordless drill set with multiple bits and batteries', 'Tools', 'DeWalt', 'DCD771C2', 199.00, 'USD', 6, 0, 2, 'DEWALT-DRILL-C2', NULL, NULL, NULL, 'asset-active', 1, 0, 'For maintenance and construction work', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(40, 'Laser Printer', 'High-speed black and white laser printer', 'Computer and IT Equipment', 'HP', 'LaserJet Pro M404dn', 299.00, 'USD', 4, 0, 1, 'HP-LJ-M404', NULL, NULL, NULL, 'asset-active', 1, 0, 'Office printing needs', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(41, 'Projector 4K', 'Portable 4K projector for presentations', 'Computer and IT Equipment', 'Epson', 'PowerLite 2250U', 1899.00, 'USD', 3, 0, 1, 'EPSON-PJ-4K', NULL, NULL, NULL, 'asset-active', 1, 1, 'For presentations and training', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(42, 'Wireless Router Enterprise', 'High-performance WiFi 6 router for office use', 'Computer and IT Equipment', 'Ubiquiti', 'UniFi Dream Machine Pro', 799.00, 'USD', 4, 0, 1, 'UBI-UDM-PRO', NULL, NULL, NULL, 'asset-active', 1, 1, 'Network infrastructure', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(43, 'Network Switch 24-port', '24-port gigabit managed switch', 'Computer and IT Equipment', 'Cisco', 'Catalyst 1000-24T', 449.00, 'USD', 6, 0, 2, 'CISCO-C1K-24T', NULL, NULL, NULL, 'asset-active', 1, 1, 'Network connectivity', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(44, 'Shredder Industrial', 'Heavy-duty document shredder for office use', 'Office Furniture', 'Fellowes', 'Powershred 225Ci', 899.00, 'USD', 2, 0, 1, 'FEL-PS-225C', NULL, NULL, NULL, 'asset-active', 1, 0, 'Document security and disposal', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(45, 'Coffee Machine Commercial', 'Commercial-grade coffee machine for office break room', 'Office Furniture', 'Keurig', 'K-1500 Commercial', 599.00, 'USD', 3, 0, 1, 'KEUR-K1500', NULL, NULL, NULL, 'asset-active', 1, 0, 'Employee amenities', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(46, 'Low Stock Item', 'Item with very low stock for testing alerts', 'Stationery', 'Generic', 'Test Item', 25.00, 'USD', 2, 0, 5, 'TEST-LOW-STOCK', NULL, NULL, NULL, 'asset-active', 1, 0, 'For testing low stock alerts', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(47, 'Out of Stock Item', 'Item with zero stock for testing', 'Stationery', 'Generic', 'Empty Item', 15.00, 'USD', 0, 0, 3, 'TEST-NO-STOCK', NULL, NULL, NULL, 'asset-active', 1, 0, 'For testing out of stock scenarios', '2025-08-04 19:00:09', '2025-08-04 19:00:09'),
(48, 'High Value Asset', 'Expensive item for testing approval workflows', 'Computer and IT Equipment', 'Enterprise', 'Server Rack', 15000.00, 'USD', 1, 0, 1, 'ENT-SERVER-01', NULL, NULL, NULL, 'asset-active', 1, 1, 'Critical infrastructure equipment', '2025-08-04 19:00:09', '2025-08-04 19:00:09');

-- --------------------------------------------------------

--
-- Table structure for table `asset_assignments`
--

CREATE TABLE `asset_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_assigned` int(11) NOT NULL DEFAULT 1,
  `assignment_date` date NOT NULL,
  `expected_return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `status` enum('assigned','returned','lost','damaged','transferred') NOT NULL DEFAULT 'assigned',
  `condition_when_assigned` enum('new','good','fair','poor') DEFAULT 'good',
  `condition_when_returned` enum('new','good','fair','poor') DEFAULT NULL,
  `assigned_by` bigint(20) UNSIGNED NOT NULL,
  `returned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `assignment_notes` text DEFAULT NULL,
  `return_notes` text DEFAULT NULL,
  `asset_request_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Links to original request if applicable',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_assignments`
--

INSERT INTO `asset_assignments` (`id`, `asset_id`, `employee_id`, `quantity_assigned`, `assignment_date`, `expected_return_date`, `actual_return_date`, `status`, `condition_when_assigned`, `condition_when_returned`, `assigned_by`, `returned_to`, `assignment_notes`, `return_notes`, `asset_request_id`, `created_at`, `updated_at`) VALUES
(2, 29, 5, 1, '2025-08-05', NULL, NULL, 'assigned', 'good', NULL, 5, NULL, 'for development purposes', NULL, NULL, '2025-08-05 06:04:48', '2025-08-05 06:04:48'),
(3, 29, 11, 1, '2025-08-06', NULL, NULL, 'assigned', 'new', NULL, 5, NULL, 'xxxxx', NULL, NULL, '2025-08-06 10:19:55', '2025-08-06 10:19:55');

-- --------------------------------------------------------

--
-- Table structure for table `asset_assignment_history`
--

CREATE TABLE `asset_assignment_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` bigint(20) UNSIGNED NOT NULL,
  `action` enum('assigned','returned','transferred','lost','damaged','status_changed') NOT NULL,
  `from_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_date` datetime NOT NULL,
  `performed_by` bigint(20) UNSIGNED NOT NULL,
  `notes` text DEFAULT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `asset_availability`
-- (See below for the actual view)
--
CREATE TABLE `asset_availability` (
`id` bigint(20) unsigned
,`name` varchar(255)
,`category` varchar(255)
,`brand` varchar(255)
,`model` varchar(255)
,`stock_quantity` int(11)
,`assigned_quantity` int(11)
,`available_quantity` int(11)
,`min_stock_level` int(11)
,`availability_status` varchar(12)
,`asset_status` varchar(50)
);

-- --------------------------------------------------------

--
-- Table structure for table `asset_requests`
--

CREATE TABLE `asset_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(255) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('draft','pending','approved','rejected','fulfilled','cancelled') DEFAULT 'pending',
  `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
  `business_justification` text NOT NULL,
  `needed_by_date` date DEFAULT NULL,
  `delivery_instructions` text DEFAULT NULL,
  `total_estimated_cost` decimal(12,2) DEFAULT 0.00,
  `department` varchar(255) DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `fulfilled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `fulfilled_at` timestamp NULL DEFAULT NULL,
  `fulfillment_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_requests`
--

INSERT INTO `asset_requests` (`id`, `request_number`, `employee_id`, `status`, `priority`, `business_justification`, `needed_by_date`, `delivery_instructions`, `total_estimated_cost`, `department`, `approved_by`, `approved_at`, `approval_notes`, `rejection_reason`, `fulfilled_by`, `fulfilled_at`, `fulfillment_notes`, `created_at`, `updated_at`) VALUES
(2, 'REQ-2025-001', 1, 'rejected', 'high', 'for work xxxxxxxxxxxx', '2025-07-24', 'collect at office', 4092.00, NULL, 1, '2025-07-22 20:41:21', NULL, 'camr in very late', NULL, NULL, NULL, '2025-07-22 15:42:03', '2025-07-22 20:41:21'),
(3, 'REQ-2025-002', 1, 'approved', 'low', 'need it to ise in working with the client', '2025-07-24', 'non', 0.00, NULL, 5, '2025-08-06 02:52:16', NULL, NULL, NULL, NULL, NULL, '2025-07-22 20:42:26', '2025-08-06 02:52:16'),
(4, 'REQ-2025-003', 5, 'approved', 'high', 'for testing purposes', '2025-07-26', 'ill collect at office', 0.00, NULL, 5, '2025-08-06 02:52:16', NULL, NULL, NULL, NULL, NULL, '2025-07-23 16:32:48', '2025-08-06 02:52:16'),
(5, 'REQ-2025-004', 5, 'approved', 'urgent', 'trial and testigqqqqqq', '2025-08-02', 'trial test', 899.00, NULL, 5, '2025-08-04 09:49:22', NULL, NULL, NULL, NULL, NULL, '2025-07-30 08:36:55', '2025-08-04 09:49:22'),
(6, 'REQ-2025-005', 5, 'approved', 'low', 'trial...........................................', '2025-08-02', 'none...................', 112.50, NULL, 5, '2025-08-03 13:45:22', NULL, NULL, NULL, NULL, NULL, '2025-07-31 10:45:36', '2025-08-03 13:45:22'),
(7, 'REQ-2025-006', 5, 'approved', 'normal', 'need for job to use in field', '2025-08-07', 'none', 3148.00, NULL, 5, '2025-08-04 17:03:04', NULL, NULL, NULL, NULL, NULL, '2025-08-04 17:02:30', '2025-08-04 17:03:04'),
(8, 'REQ-2025-007', 5, 'approved', 'low', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', '2025-08-08', 'pick up at......', 6198.00, NULL, 5, '2025-08-06 10:56:27', NULL, NULL, NULL, NULL, NULL, '2025-08-06 10:44:20', '2025-08-06 10:56:27');

-- --------------------------------------------------------

--
-- Table structure for table `asset_request_items`
--

CREATE TABLE `asset_request_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `asset_request_id` bigint(20) UNSIGNED NOT NULL,
  `asset_id` bigint(20) UNSIGNED NOT NULL,
  `quantity_requested` int(11) NOT NULL,
  `quantity_approved` int(11) DEFAULT 0,
  `quantity_fulfilled` int(11) DEFAULT 0,
  `unit_price_at_request` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `item_status` enum('pending','approved','partially_approved','rejected','fulfilled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `assignment_created` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `asset_request_items`
--

INSERT INTO `asset_request_items` (`id`, `asset_request_id`, `asset_id`, `quantity_requested`, `quantity_approved`, `quantity_fulfilled`, `unit_price_at_request`, `total_price`, `item_status`, `notes`, `assignment_created`, `created_at`, `updated_at`) VALUES
(8, 7, 32, 1, 1, 0, 649.00, 649.00, 'approved', NULL, 0, '2025-08-04 17:02:30', '2025-08-04 17:03:04'),
(9, 7, 29, 1, 1, 0, 2499.00, 2499.00, 'approved', NULL, 0, '2025-08-04 17:02:30', '2025-08-04 17:03:04'),
(10, 8, 35, 1, 1, 0, 1200.00, 1200.00, 'approved', NULL, 0, '2025-08-06 10:44:20', '2025-08-06 10:56:27'),
(11, 8, 29, 2, 2, 0, 2499.00, 4998.00, 'approved', NULL, 0, '2025-08-06 10:44:20', '2025-08-06 10:56:27');

-- --------------------------------------------------------

--
-- Table structure for table `business_licenses`
--

CREATE TABLE `business_licenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `license_direction` enum('company_held','customer_issued') NOT NULL DEFAULT 'company_held',
  `license_name` varchar(255) NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `license_type` varchar(100) NOT NULL,
  `issuing_authority` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `renewal_date` date DEFAULT NULL,
  `status` enum('active','expired','pending_renewal','suspended','cancelled','under_review') NOT NULL DEFAULT 'active',
  `priority_level` enum('critical','high','medium','low') NOT NULL DEFAULT 'medium',
  `cost` decimal(10,2) DEFAULT NULL,
  `renewal_cost` decimal(10,2) DEFAULT NULL,
  `revenue_amount` decimal(10,2) DEFAULT NULL,
  `billing_cycle` enum('monthly','quarterly','annually','one_time') DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `responsible_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `customer_company` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `compliance_notes` text DEFAULT NULL,
  `document_path` varchar(500) DEFAULT NULL,
  `business_impact` text DEFAULT NULL,
  `regulatory_body` varchar(255) DEFAULT NULL,
  `license_conditions` text DEFAULT NULL,
  `license_terms` text DEFAULT NULL,
  `usage_limit` varchar(255) DEFAULT NULL,
  `support_level` enum('basic','standard','premium','enterprise') DEFAULT NULL,
  `customer_reference` varchar(255) DEFAULT NULL,
  `service_start_date` date DEFAULT NULL,
  `license_quantity` int(11) DEFAULT NULL,
  `auto_renewal_customer` tinyint(1) DEFAULT 0,
  `renewal_reminder_days` int(11) NOT NULL DEFAULT 30,
  `auto_renewal` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_licenses`
--

INSERT INTO `business_licenses` (`id`, `license_direction`, `license_name`, `license_number`, `license_type`, `issuing_authority`, `issue_date`, `expiry_date`, `renewal_date`, `status`, `priority_level`, `cost`, `renewal_cost`, `revenue_amount`, `billing_cycle`, `location`, `department_id`, `responsible_employee_id`, `customer_id`, `customer_name`, `customer_email`, `customer_company`, `customer_phone`, `customer_address`, `description`, `compliance_notes`, `document_path`, `business_impact`, `regulatory_body`, `license_conditions`, `license_terms`, `usage_limit`, `support_level`, `customer_reference`, `service_start_date`, `license_quantity`, `auto_renewal_customer`, `renewal_reminder_days`, `auto_renewal`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'company_held', 'Business Operating License edit testing', 'BOL-2024-001', 'business_operation', 'Department of Commerce', '2024-01-15', '2025-10-31', NULL, 'pending_renewal', 'critical', 500.00, 450.00, NULL, NULL, NULL, 1, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Primary business operating license required for all commercial activities', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 10, 1, NULL, 5, '2025-08-03 14:08:07', '2025-08-03 13:15:28'),
(2, 'company_held', 'Health & Safety Certificate', 'HSC-2024-002', 'health_safety', 'Occupational Safety Authority', '2024-02-01', '2025-12-31', '2025-08-03', 'active', 'high', 750.00, 650.00, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Workplace health and safety compliance certificate', NULL, 'business-licenses/r5urjejpSZXpM28h7nyTWXtvlZlj79y0IEPBFy0i.docx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 45, 1, NULL, NULL, '2025-08-03 14:08:07', '2025-08-03 13:14:30'),
(3, 'company_held', 'Environmental Compliance License', 'ECL-2024-003', 'environmental', 'Environmental Protection Agency', '2024-03-10', '2026-03-09', '2025-08-11', 'active', 'medium', 1200.00, 1100.00, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Environmental impact compliance for business operations', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 60, 0, NULL, NULL, '2025-08-03 14:08:07', '2025-08-11 03:53:50'),
(4, 'company_held', 'Microsoft Office 365 Subscription', 'MS-O365-2025', 'software', 'Microsoft Corporation', '2025-01-01', '2025-12-31', NULL, 'active', 'critical', 1320.00, 1320.00, NULL, NULL, NULL, 1, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Office 365 Business Premium - 50 user licenses', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 30, 1, NULL, NULL, '2025-08-04 22:01:54', '2025-08-04 22:01:54'),
(5, 'company_held', 'Adobe Creative Cloud License', 'ADOBE-CC-2025', 'software', 'Adobe Inc.', '2025-01-01', '2025-12-31', NULL, 'active', 'medium', 659.88, 659.88, NULL, NULL, NULL, 1, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Creative Cloud All Apps - 10 user licenses', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 30, 1, NULL, NULL, '2025-08-04 22:01:54', '2025-08-04 22:01:54'),
(6, 'company_held', 'Zoom Pro Licenses', 'ZOOM-PRO-2025', 'software', 'Zoom Video Communications', '2025-01-01', '2025-12-31', NULL, 'active', 'medium', 449.75, 449.75, NULL, NULL, NULL, 1, 5, NULL, NULL, NULL, NULL, NULL, NULL, 'Zoom Pro licenses - 25 users', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 30, 1, NULL, NULL, '2025-08-04 22:01:54', '2025-08-04 22:01:54');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('revival-technologies-cache-assigned_terminal_ids', 'a:2:{i:0;i:1;i:1;i:2;}', 1754980070),
('revival-technologies-cache-deployment_stats', 'a:6:{s:15:\"total_terminals\";i:53;s:14:\"active_clients\";i:4;s:21:\"available_technicians\";i:3;s:19:\"terminals_by_status\";a:4:{s:6:\"active\";i:32;s:7:\"offline\";i:14;s:11:\"maintenance\";i:2;s:6:\"faulty\";i:5;}s:18:\"assigned_terminals\";i:2;s:20:\"unassigned_terminals\";i:51;}', 1754980310),
('revival-technologies-cache-h200069n@hit.ac.zw|::1', 'i:1;', 1754476168),
('revival-technologies-cache-h200069n@hit.ac.zw|::1:timer', 'i:1754476168;', 1754476168),
('revival-technologies-cache-simba@gmail.com|::1', 'i:1;', 1754476118),
('revival-technologies-cache-simba@gmail.com|::1:timer', 'i:1754476118;', 1754476118);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `type`, `name`, `slug`, `description`, `color`, `icon`, `is_active`, `sort_order`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 'asset_category', 'POS Terminals', 'asset-pos-terminals', 'Point of sale terminals and payment processing equipment', '#007bff', '🖥️', 1, 1, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(2, 'asset_category', 'Office Furniture', 'asset-office-furniture', 'Desks, chairs, cabinets and office furniture', '#6f42c1', '🪑', 1, 2, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(3, 'asset_category', 'Tools', 'asset-tools', 'Work tools and equipment for maintenance and operations', '#fd7e14', '🔧', 1, 3, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(4, 'asset_category', 'Vehicles', 'asset-vehicles', 'Company vehicles and transportation equipment', '#dc3545', '🚗', 1, 4, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(5, 'asset_category', 'Stationery', 'asset-stationery', 'Office supplies, stationery and consumables', '#20c997', '📝', 1, 5, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(7, 'asset_category', 'Computer and IT Equipment', 'asset-computer-it-equipment', 'Computers, servers, networking and IT hardware', '#17a2b8', '💻', 1, 7, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(8, 'asset_status', 'Active', 'asset-active', 'Asset is in use and functional', '#28a745', '✅', 1, 1, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(9, 'asset_status', 'Inactive', 'asset-inactive', 'Asset is not currently in use', '#6c757d', '⏸️', 1, 2, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(10, 'asset_status', 'Discontinued', 'asset-discontinued', 'Asset is no longer supported', '#dc3545', '❌', 1, 3, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(11, 'terminal_status', 'Active', 'terminal-active', 'Terminal is working normally', '#28a745', '✅', 1, 1, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(12, 'terminal_status', 'Offline', 'terminal-offline', 'Terminal is not responding', '#dc3545', '📶', 1, 2, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(13, 'terminal_status', 'Maintenance', 'terminal-maintenance', 'Terminal is under maintenance', '#ffc107', '🔧', 1, 3, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(14, 'terminal_status', 'Faulty', 'terminal-faulty', 'Terminal has hardware/software issues', '#fd7e14', '⚠️', 1, 4, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(15, 'terminal_status', 'Decommissioned', 'terminal-decommissioned', 'Terminal is retired from service', '#6c757d', '🗑️', 1, 5, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(16, 'service_type', 'Routine Maintenance', 'service-routine-maintenance', 'Regular scheduled maintenance', '#007bff', '🔄', 1, 1, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(17, 'service_type', 'Emergency Repair', 'service-emergency-repair', 'Urgent repair needed', '#dc3545', '🚨', 1, 2, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(18, 'service_type', 'Software Update', 'service-software-update', 'Software or firmware update', '#28a745', '⬇️', 1, 3, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(19, 'service_type', 'Hardware Replacement', 'service-hardware-replacement', 'Replace faulty hardware components', '#fd7e14', '🔄', 1, 4, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(20, 'service_type', 'Network Configuration', 'service-network-configuration', 'Network setup and configuration', '#20c997', '🌐', 1, 5, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(21, 'service_type', 'Installation', 'service-installation', 'New equipment installation', '#6f42c1', '📦', 1, 6, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(22, 'service_type', 'Decommission', 'service-decommission', 'Remove equipment from service', '#6c757d', '🗑️', 1, 7, NULL, '2025-07-28 08:22:16', '2025-07-28 08:22:16'),
(23, 'service_type', 'trial123123', 'service_type-trial123123', NULL, '#000000', NULL, 1, 8, NULL, '2025-07-28 15:32:25', '2025-07-28 15:32:25'),
(24, 'visit_type', 'Scheduled Visit', 'visit-scheduled', 'Regular scheduled maintenance visit', '#007bff', '📅', 1, 1, NULL, NULL, NULL),
(25, 'visit_type', 'Emergency Visit', 'visit-emergency', 'Emergency repair or issue resolution', '#dc3545', '🚨', 1, 2, NULL, NULL, NULL),
(26, 'visit_type', 'Follow-up Visit', 'visit-followup', 'Follow-up on previous issues', '#28a745', '🔄', 1, 3, NULL, NULL, NULL),
(27, 'visit_type', 'Inspection Visit', 'visit-inspection', 'General inspection and status check', '#6c757d', '🔍', 1, 4, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `region_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `region_id`, `is_active`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Harare', 1, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(2, 'Chitungwiza', 1, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(3, 'Epworth', 1, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(4, 'Norton', 1, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(5, 'Ruwa', 1, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(6, 'Domboshava', 1, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(7, 'Mazowe', 1, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(8, 'Bindura', 1, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(9, 'Bulawayo', 2, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(10, 'Gwanda', 2, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(11, 'Plumtree', 2, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(12, 'Beitbridge', 2, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(13, 'Hwange', 2, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(14, 'Victoria Falls', 2, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(15, 'Lupane', 2, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(16, 'Tsholotsho', 2, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(17, 'Mutare', 3, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(18, 'Rusape', 3, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(19, 'Chipinge', 3, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(20, 'Chimanimani', 3, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(21, 'Nyanga', 3, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(22, 'Penhalonga', 3, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(23, 'Odzi', 3, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(24, 'Hauna', 3, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(25, 'Chinhoyi', 4, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(26, 'Kadoma', 4, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(27, 'Chegutu', 4, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(28, 'Kariba', 4, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(29, 'Karoi', 4, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(30, 'Mhangura', 4, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(31, 'Alaska', 4, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(32, 'Lion\'s Den', 4, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(33, 'Gweru', 5, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(34, 'Kwekwe', 5, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(35, 'Shurugwi', 5, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(36, 'Zvishavane', 5, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(37, 'Gokwe', 5, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(38, 'Redcliff', 5, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(39, 'Mvuma', 5, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39'),
(40, 'Lalapanzi', 5, 1, 1, NULL, '2025-08-03 19:48:39', '2025-08-03 19:48:39');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_code` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `contract_start_date` date DEFAULT NULL,
  `contract_end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_code`, `company_name`, `contact_person`, `email`, `phone`, `address`, `city`, `region`, `status`, `contract_start_date`, `contract_end_date`, `created_at`, `updated_at`) VALUES
(1, 'ABC001', 'ABC Bank', 'John Manager', 'john@abcbank.com', '+254712345678', NULL, 'Nairobi', 'Central', 'active', '2025-07-23', '2025-08-09', '2025-07-22 10:24:14', '2025-07-22 21:35:40'),
(2, 'XYZ001', 'XYZ Bank', 'Sarah Manager', 'sarah@xyzbank.com', '+254798765432', NULL, 'Mombasa', 'South', 'active', '2024-01-01', '2024-12-31', '2025-07-22 10:43:40', '2025-07-22 10:43:40'),
(3, 'TES4390', 'testing company 123', 'monah test', 'monah@ingeniouss.tech', '+263782509556', '24 Cecil Rhodes dr Newlands Harare', 'Harare', NULL, 'active', NULL, NULL, '2025-07-30 08:41:45', '2025-08-06 11:09:19'),
(4, 'OKS7556', 'Ok Supermarket', 'John Doe', 'ok@gmail.com', '+263782509556', '24 Cecil Rhodes dr Newlands Harare', 'Harare', NULL, 'active', '2024-01-31', '2025-03-08', '2025-08-06 09:10:49', '2025-08-06 09:14:34');

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `contract_number` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `vendor_company` varchar(255) NOT NULL,
  `vendor_contact_person` varchar(255) DEFAULT NULL,
  `vendor_email` varchar(255) DEFAULT NULL,
  `vendor_phone` varchar(20) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `value` decimal(12,2) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `payment_frequency` enum('one_time','monthly','quarterly','annually') NOT NULL DEFAULT 'annually',
  `status` enum('active','expired','terminated','pending') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `terms_summary` text DEFAULT NULL,
  `renewal_reminders` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`renewal_reminders`)),
  `auto_renewal` tinyint(1) NOT NULL DEFAULT 0,
  `renewal_period_months` int(11) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `next_payment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`id`, `title`, `contract_number`, `type`, `vendor_company`, `vendor_contact_person`, `vendor_email`, `vendor_phone`, `start_date`, `end_date`, `value`, `currency`, `payment_frequency`, `status`, `description`, `terms_summary`, `renewal_reminders`, `auto_renewal`, `renewal_period_months`, `document_path`, `next_payment_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Office Lease Agreement', 'CON2024001', 'lease', 'Prime Properties LLC', 'John Smith', 'john@primeproperties.com', NULL, '2024-01-01', '2025-12-31', 24000.00, 'USD', 'monthly', 'active', 'Main office space rental agreement', NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59'),
(2, 'IT Support Contract', 'CON2024002', 'service', 'TechSupport Solutions', 'Sarah Johnson', 'sarah@techsupport.com', NULL, '2024-02-01', '2025-01-31', 12000.00, 'USD', 'monthly', 'active', 'Comprehensive IT support and maintenance', NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59'),
(3, 'Insurance Policy', 'CON2024003', 'insurance', 'Business Insurance Corp', 'Mike Davis', 'mike@businessins.com', NULL, '2024-01-01', '2024-12-31', 3600.00, 'USD', 'annually', 'active', 'General liability and property insurance', NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59'),
(4, 'Cleaning Service Agreement', 'CON2023004', 'service', 'CleanPro Services', 'Lisa Wilson', 'lisa@cleanpro.com', NULL, '2023-06-01', '2024-05-31', 6000.00, 'USD', 'monthly', 'expired', 'Office cleaning and maintenance services', NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59');

-- --------------------------------------------------------

--
-- Stand-in structure for view `current_asset_assignments`
-- (See below for the actual view)
--
CREATE TABLE `current_asset_assignments` (
`assignment_id` bigint(20) unsigned
,`asset_id` bigint(20) unsigned
,`asset_name` varchar(255)
,`category` varchar(255)
,`brand` varchar(255)
,`model` varchar(255)
,`sku` varchar(255)
,`quantity_assigned` int(11)
,`employee_id` bigint(20) unsigned
,`employee_name` varchar(511)
,`employee_number` varchar(255)
,`department_name` varchar(255)
,`assignment_date` date
,`expected_return_date` date
,`condition_when_assigned` enum('new','good','fair','poor')
,`assignment_notes` text
,`assigned_by_name` varchar(511)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'IT', 'IT Department', '2025-07-22 10:16:20', '2025-07-22 10:16:20'),
(2, 'HR', NULL, '2025-07-23 10:45:05', '2025-07-23 10:45:05'),
(3, 'Sales', NULL, '2025-07-23 10:45:05', '2025-07-23 10:45:05'),
(4, 'Marketing', NULL, '2025-07-23 10:45:05', '2025-07-23 10:45:05'),
(5, 'Finance', NULL, '2025-07-23 10:45:05', '2025-07-23 10:45:05'),
(6, 'Operations', NULL, '2025-07-23 10:45:05', '2025-07-23 10:45:05');

-- --------------------------------------------------------

--
-- Table structure for table `deployment_templates`
--

CREATE TABLE `deployment_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `group_by` enum('region','city','province','area','address') NOT NULL DEFAULT 'region',
  `region_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `pos_terminals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`pos_terminals`)),
  `service_type` varchar(255) NOT NULL,
  `priority` enum('low','normal','high','emergency') NOT NULL DEFAULT 'normal',
  `estimated_duration_hours` decimal(4,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_registrations`
--

CREATE TABLE `device_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `technician_id` bigint(20) UNSIGNED NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `platform` enum('android','ios') NOT NULL,
  `app_version` varchar(20) DEFAULT NULL,
  `fcm_token` text DEFAULT NULL COMMENT 'For push notifications',
  `last_active` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `time_zone` varchar(255) NOT NULL DEFAULT 'UTC',
  `language` varchar(255) NOT NULL DEFAULT 'en',
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `hire_date` date DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_number`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `phone`, `department_id`, `role_id`, `manager_id`, `time_zone`, `language`, `two_factor_enabled`, `status`, `hire_date`, `last_login_at`, `remember_token`, `created_at`, `updated_at`, `last_login`) VALUES
(1, 'ADMIN001', 'Admin', 'User', 'admin@miav.com', NULL, '$2y$12$mxy1e1hR4FCIFrKJZ.4qmeuaWi/.TONIoSMgaudL20pMS849Nwvj2', NULL, NULL, 9, NULL, 'UTC', 'en', 0, 'active', NULL, NULL, NULL, '2025-07-22 10:08:15', '2025-07-23 15:53:04', NULL),
(3, 'IT250001', 'Monah', 'Chimwamafuku', 'monahchimwamafuku@gmail.com', NULL, '$2y$12$4IAXgINs0wOAirVegBlCuOyf1ZjU0xqeRIDxAva1gVjesFqGcgnRe', '+263782509556', 1, 8, 1, 'UTC', 'en', 0, 'active', '2025-07-23', NULL, NULL, '2025-07-23 10:48:13', '2025-07-23 15:42:53', NULL),
(4, 'IT250002', 'Vongai', 'Chimwamafuku', 'monah@ingeniouss.tech', NULL, '$2y$12$50Fowrw4NCgy9L7wrapRZua7ENkFzqnC5B7ZGvW02a6dZRDi/5mu2', '+263782509556', 1, 8, 1, 'UTC', 'en', 0, 'active', '2025-07-23', NULL, 'MZqOcSUSmdCHBFaraMzma7JJeeUJ77cZx2KZafRD1mhfzKMcIcp2cQoFf3XR', '2025-07-23 10:57:40', '2025-07-23 15:42:53', NULL),
(5, 'SUPER001', 'Monah', 'Chimwa', 'superadmin@company.com', NULL, '$2y$12$nCRK5QFKyT/y2SS390gITeNtNKcuYKfwVn81bJXNXKCJhHjzDn/Bm', '+263782509556', NULL, 8, NULL, 'UTC', 'en', 0, 'active', '2025-07-23', '2025-08-13 08:29:53', 'qFwtiq5aQcb6p24nGdp42O40sPKLF2Ny4z32qVj8QROrMCl0uiHKcEmqU8Xh', '2025-07-23 14:40:49', '2025-08-13 08:29:53', NULL),
(10, 'EMP250001', 'trial', 'Ingeniouss', 'trial@trial.com', NULL, '$2y$12$bFsCOylv3.ugnWemqzukfO69dybOfzU1CSlr9BO2kjPZ8eeFEaqqK', '+254712345678', 1, 4, NULL, 'UTC', 'en', 0, 'active', '2025-07-23', NULL, NULL, '2025-07-23 17:19:36', '2025-07-23 17:19:36', NULL),
(11, 'EMP250002', 'tatenda', 'juru', 'tatenda@gmail.com', NULL, '$2y$12$t/JSiFOdawEgxD7Q/8LO6uCWZvBiHldPML5W1CbwnPuKabekk8Fu6', '+263782509556', 6, 3, NULL, 'UTC', 'en', 0, 'active', '2025-07-27', NULL, NULL, '2025-07-27 13:48:09', '2025-07-27 13:48:09', NULL),
(12, 'EMP250003', 'simba', 'chidzimba', 'h200069n@hit.ac.zw', NULL, '$2y$12$JURYRANjI2dpPnFISGJUveiCaqcxVNVPjtMxT4c1UoTjQGj1dBZNS', '+263782509556', 6, 3, NULL, 'UTC', 'en', 0, 'active', '2025-07-27', NULL, NULL, '2025-07-27 14:12:12', '2025-07-27 14:12:12', NULL),
(13, 'OPE250001', 'anesu', 'tembo', 'anesu@gmail.com', NULL, '$2y$12$Pvz385ILTSpYsETv1nYBa.z03HQbywoTDRYYnhxd8fFIVSTmPbgSG', '+263782509556', 6, 3, 5, 'UTC', 'en', 0, 'active', '2025-07-27', NULL, NULL, '2025-07-27 15:12:09', '2025-07-27 15:12:09', NULL);

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
-- Table structure for table `import_mappings`
--

CREATE TABLE `import_mappings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mapping_name` varchar(255) NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `column_mappings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`column_mappings`)),
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_assignments`
--

CREATE TABLE `job_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assignment_id` varchar(50) NOT NULL,
  `technician_id` bigint(20) UNSIGNED NOT NULL,
  `region_id` int(11) DEFAULT NULL,
  `pos_terminals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pos_terminals`)),
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `service_type` enum('routine_maintenance','emergency_repair','software_update','hardware_replacement','network_configuration','installation','decommission') NOT NULL,
  `priority` enum('low','normal','high','emergency') DEFAULT 'normal',
  `status` enum('assigned','in_progress','completed','cancelled') DEFAULT 'assigned',
  `notes` text DEFAULT NULL,
  `estimated_duration_hours` decimal(4,2) DEFAULT NULL,
  `actual_start_time` datetime DEFAULT NULL,
  `actual_end_time` datetime DEFAULT NULL,
  `completion_notes` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_assignments`
--

INSERT INTO `job_assignments` (`id`, `assignment_id`, `technician_id`, `region_id`, `pos_terminals`, `client_id`, `project_id`, `scheduled_date`, `service_type`, `priority`, `status`, `notes`, `estimated_duration_hours`, `actual_start_time`, `actual_end_time`, `completion_notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'TEST-001', 13, 1, '[1,2]', NULL, NULL, '2025-07-29', 'routine_maintenance', 'normal', 'assigned', NULL, NULL, NULL, NULL, NULL, NULL, '2025-07-28 18:48:28', '2025-07-28 18:48:28');

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `licenses`
--

CREATE TABLE `licenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `license_number` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `issuing_authority` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'USD',
  `status` enum('active','expired','pending_renewal','suspended') NOT NULL DEFAULT 'active',
  `description` text DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `renewal_notes` text DEFAULT NULL,
  `renewal_reminders` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`renewal_reminders`)),
  `document_path` varchar(255) DEFAULT NULL,
  `auto_renewal` tinyint(1) NOT NULL DEFAULT 0,
  `renewal_period_months` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `licenses`
--

INSERT INTO `licenses` (`id`, `name`, `license_number`, `type`, `issuing_authority`, `issue_date`, `expiry_date`, `cost`, `currency`, `status`, `description`, `contact_person`, `contact_email`, `contact_phone`, `renewal_notes`, `renewal_reminders`, `document_path`, `auto_renewal`, `renewal_period_months`, `created_at`, `updated_at`) VALUES
(1, 'Business Operating License', 'BOL2024001', 'business_license', 'City Business Department', '2024-01-15', '2025-01-15', 250.00, 'USD', 'active', 'Primary business operating license', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59'),
(2, 'Health Department Permit', 'HDP2024002', 'health_permit', 'County Health Department', '2024-03-01', '2025-03-01', 150.00, 'USD', 'active', 'Food handling and safety permit', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59'),
(3, 'Fire Safety Certificate', 'FSC2024003', 'fire_permit', 'Fire Marshal Office', '2024-02-10', '2025-02-10', 75.00, 'USD', 'active', 'Building fire safety compliance', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59'),
(4, 'Professional License', 'PL2023004', 'professional', 'State Professional Board', '2023-06-01', '2024-06-01', 300.00, 'USD', 'expired', 'Professional certification license', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59'),
(5, 'Environmental Permit', 'EP2024005', 'environmental', 'Environmental Protection Agency', '2024-01-01', '2025-12-31', 500.00, 'USD', 'active', 'Waste disposal and environmental compliance', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-07-24 07:33:59', '2025-07-24 07:33:59');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_07_21_202636_create_departments_table', 1),
(5, '2025_07_21_202650_create_roles_table', 1),
(6, '2025_07_21_202723_create_employees_table', 1),
(7, '2025_08_03_000000_create_cities_table', 7),
(8, '2025_08_03_000000_create_cities_table', 8),
(9, '2025_08_03_000000_create_cities_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pos_terminals`
--

CREATE TABLE `pos_terminals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `merchant_id` varchar(255) DEFAULT NULL COMMENT 'Column A: merchant ID from Excel',
  `terminal_id` varchar(255) NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `merchant_name` varchar(255) NOT NULL,
  `legal_name` varchar(255) DEFAULT NULL COMMENT 'Column D: Legal name from Excel',
  `merchant_contact_person` varchar(255) DEFAULT NULL,
  `merchant_phone` varchar(255) DEFAULT NULL,
  `merchant_email` varchar(255) DEFAULT NULL,
  `physical_address` text DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `business_type` varchar(255) DEFAULT NULL,
  `installation_date` date DEFAULT NULL,
  `terminal_model` varchar(255) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `contract_details` text DEFAULT NULL,
  `status` enum('active','offline','maintenance','faulty','decommissioned') DEFAULT 'active',
  `last_service_date` date DEFAULT NULL,
  `next_service_due` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `region_id` bigint(20) UNSIGNED DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `current_status` enum('active','offline','maintenance','faulty','decommissioned') DEFAULT 'active',
  `deployment_status` enum('pending','assigned','deployed','decommissioned') DEFAULT 'deployed',
  `condition_status` varchar(100) DEFAULT NULL COMMENT 'Column P: Condition from Excel',
  `issues_raised` text DEFAULT NULL COMMENT 'Column Q: Issue Raised from Excel',
  `corrective_action` text DEFAULT NULL COMMENT 'Column S: Corrective Action from Excel',
  `site_contact_person` varchar(255) DEFAULT NULL COMMENT 'Column T: Contact Person at site',
  `site_contact_number` varchar(20) DEFAULT NULL COMMENT 'Column U: Contact Number at site',
  `last_updated_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Who last updated this record',
  `last_visit_date` datetime DEFAULT NULL COMMENT 'Last technician visit date'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pos_terminals`
--

INSERT INTO `pos_terminals` (`id`, `merchant_id`, `terminal_id`, `client_id`, `merchant_name`, `legal_name`, `merchant_contact_person`, `merchant_phone`, `merchant_email`, `physical_address`, `city`, `province`, `area`, `business_type`, `installation_date`, `terminal_model`, `serial_number`, `contract_details`, `status`, `last_service_date`, `next_service_due`, `created_at`, `updated_at`, `region_id`, `region`, `current_status`, `deployment_status`, `condition_status`, `issues_raised`, `corrective_action`, `site_contact_person`, `site_contact_number`, `last_updated_by`, `last_visit_date`) VALUES
(1, NULL, 'POS-001', 1, 'Green Valley Supermarket', NULL, 'Jane Doe', '+254723456789', NULL, 'Westlands, Nairobi', NULL, NULL, 'Westlands', 'Retail', '2022-11-23', NULL, NULL, NULL, 'maintenance', '2024-07-15', '2025-08-09', '2025-07-22 10:24:14', '2025-07-30 10:13:16', 1, NULL, 'maintenance', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, NULL, 'POS-002', 1, 'City Electronics', NULL, 'Mike Smith', '+254734567890', NULL, 'CBD, Nairobi', NULL, NULL, 'CBD', 'Electronics', NULL, NULL, NULL, NULL, 'offline', '2024-07-10', NULL, '2025-07-22 10:24:14', '2025-07-22 10:24:14', 5, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, NULL, '77202134', 1, 'ELS PHARMACIES (PVT) LTD', NULL, 'Pauline Mawire', '26377403397', NULL, 'ST 237 KILWINNING SHOPPING CENTRE HATFIE', 'HARARE', 'Harare', 'HATFIELD', 'Verifone', '2025-04-13', 'VX-520', '34323433', 'Condition: Good\nIssues: Cable Faulty\nComments: Merchant had a Vx 520 and an unbranded 675 that we could not verify if it belongs to Stanbic. The merchant is the company with country harvest\nCorrective Action: Device Collected\nSite Phone: 2.63779E+11', 'offline', NULL, NULL, '2025-07-27 17:22:46', '2025-07-30 18:05:50', 1, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, NULL, '40103242444343', 2, '77202134', NULL, 'Verifone', 'SINYORO AND PARTNERS', NULL, 'SINYORO AND CO LEGAL PRACTITIONERS', 'NO 3 ASHTON ROAD ALEXANDRA PARK', 'HARARE', NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, '2025-07-27 17:27:03', '2025-07-27 17:28:07', 1, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, NULL, 'pos2345', 2, 'greenfields', NULL, 'Ingeniouss', '777852223', 'admin@company.com', '24 Cecil Rhodes dr Newlands Harare', 'Harare', 'harare', NULL, 'Retail', '2025-07-02', 'Ingenico iWL220', 'sn4567', 'none', 'active', NULL, NULL, '2025-07-27 17:57:26', '2025-07-27 17:57:26', 1, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, NULL, '67500465', 1, 'ECONET INSURANCE', NULL, 'Hillary Banda', '26377403397', NULL, 'EPWORTH', 'HARARE', 'Harare', 'EPWORTH', 'N Genius', '2025-04-14', NULL, '34323433', 'We failed to locate thi merchant and contacts are not going through', 'offline', NULL, NULL, '2025-07-30 08:24:19', '2025-07-30 08:24:19', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, NULL, '88203567', 3, 'CITY GROCERS LTD', NULL, 'Tendai Moyo', '263782345678', NULL, '45 VICTORIA FALLS RD', 'BULAWAYO', 'Bulawayo', NULL, 'Ingenico', '2025-04-22', 'iWL252', '45566778', 'Site Contact: Grace Chikomo\nSite Phone: 263774556677', 'active', NULL, NULL, '2025-07-30 18:55:22', '2025-07-30 18:55:22', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, NULL, '99204678', 3, 'GLOBAL TECH SOLUTIONS', NULL, 'Alice Ncube', '263772334455', NULL, '12 SAMORA MACHEL AVE', 'GWERU', 'Midlands', NULL, 'PAX', '2025-04-25', 'S80', '55677889', 'Condition: Error\nIssues: Display not working\nComments: Screen flickers intermittently\nCorrective Action: Replace display module\nSite Contact: Bob Sithole\nSite Phone: 263783345566', 'faulty', NULL, NULL, '2025-07-30 18:55:22', '2025-07-30 18:55:22', NULL, NULL, 'faulty', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, NULL, '80774445', 2, 'BRIGHT-CROSS', NULL, 'Teresa Sullivan', '2637765109216', NULL, '637 MILES DRIVES', 'GWERU', 'Midlands', NULL, 'PAX', '2025-04-14', 'S80', '83038960', 'Issues: Whose include.\nComments: Continue memory receive long art.\nCorrective Action: Follow up needed\nSite Contact: Belinda Turner\nSite Phone: 2637742687720', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, NULL, '88240283', 2, 'ROJAS, MURPHY AND BENNETT', NULL, 'Joy Mendoza', '2637757885596', NULL, '433 CHRISTINA RIDGE APT. 275', 'GWERU', 'Midlands', NULL, 'Ingenico', '2025-11-28', 'VX-520', '77253893', 'Issues: Guy measure push traditional.\nComments: Kind result style easy.\nCorrective Action: Follow up needed\nSite Contact: Bryan Ayala\nSite Phone: 2637749471179', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, NULL, '88736953', 2, 'MORENO GROUP', NULL, 'Stephanie Castillo', '2637781270169', NULL, '51032 HOPKINS PORTS', 'GWERU', 'Midlands', NULL, 'Ingenico', '2025-06-21', 'S80', '30812559', 'Issues: Student gas.\nComments: Quite imagine school pretty parent.\nCorrective Action: Follow up needed\nSite Contact: Michael Brooks\nSite Phone: 2637762460691', 'faulty', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'faulty', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, NULL, '77907161', 2, 'SANDERS-TRAN', NULL, 'Jose Garrett', '2637744539398', NULL, '3428 MCCLURE FLATS APT. 624', 'GWERU', 'Midlands', NULL, 'Castles', '2025-05-04', 'S80', '22134417', 'Condition: Not applicable\nSite Contact: Carla Gaines\nSite Phone: 2637739590384', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, NULL, '72626793', 2, 'GARCIA INC', NULL, 'Linda Todd', '2637702372069', NULL, '60061 KATIE FORGES APT. 577', 'GWERU', 'Midlands', NULL, 'Miura', '2025-03-13', 'S80', '35245446', 'Condition: Good\nIssues: Record large however.\nComments: Send table simple affect.\nCorrective Action: Follow up needed\nSite Contact: Jonathan Long\nSite Phone: 2637763989576', 'faulty', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'faulty', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, NULL, '96718943', 2, 'JOHNSON PLC', NULL, 'Carly George', '2637737026644', NULL, '4517 MICHAEL CRESCENT', 'HARARE', 'Harare', NULL, 'Verifone', '2025-08-26', 'iWL252', '88939371', 'Site Contact: Dr. Elizabeth Harrington\nSite Phone: 2637745200708', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, NULL, '95394595', 2, 'DAVIS LTD', NULL, 'Jorge Sloan', '2637701499170', NULL, '4278 HORN LAKES', 'HARARE', 'Harare', NULL, 'Castles', '2025-11-29', 'iWL252', '98831777', 'Condition: Worn\nIssues: Argue claim paper station.\nComments: Serve degree sometimes popular which.\nCorrective Action: Follow up needed\nSite Contact: Angela Wilkinson\nSite Phone: 2637725728372', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, NULL, '81880489', 2, 'GATES-CHRISTIAN', NULL, 'Taylor Henderson', '2637767062627', NULL, '8131 KIM PASS', 'KWEKWE', 'Midlands', NULL, 'Ingenico', '2025-10-07', 'iWL252', '29575587', 'Condition: Good\nSite Contact: Jessica Green\nSite Phone: 2637790190453', 'active', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, NULL, '80505043', 2, 'LEON-HERNANDEZ', NULL, 'Dawn Cox', '2637725258247', NULL, '675 HILL NECK', 'BULAWAYO', 'Bulawayo', NULL, 'Miura', '2025-12-10', 'iWL252', '78276883', 'Condition: Error\nSite Contact: Laura Williams\nSite Phone: 2637735084288', 'active', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, NULL, '81807039', 2, 'SWEENEY-BROCK', NULL, 'Hannah Jackson', '2637701451719', NULL, '5889 POPE MOUNT APT. 530', 'BULAWAYO', 'Bulawayo', NULL, 'Castles', '2025-11-14', 'A920', '46128833', 'Issues: Picture we dinner.\nComments: Nation poor avoid enjoy really.\nCorrective Action: Follow up needed\nSite Contact: Kathy Lane\nSite Phone: 2637734188449', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, NULL, '88975724', 2, 'OCHOA, GARZA AND MOORE', NULL, 'Thomas Brown', '2637723999473', NULL, '746 HILL PINES', 'CHITUNGWIZA', 'Harare', NULL, 'Ingenico', '2025-07-13', 'A920', '96534727', 'Condition: Error\nIssues: Back article.\nComments: Above remain any job expect product.\nCorrective Action: Follow up needed\nSite Contact: Diana Logan\nSite Phone: 2637791048358', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, NULL, '95506635', 2, 'GARCIA-EDWARDS', NULL, 'Maria Hawkins', '2637769135293', NULL, '9137 JENNIFER STREETS', 'KWEKWE', 'Midlands', NULL, 'Castles', '2025-04-14', 'S80', '13962096', 'Condition: Good\nSite Contact: Steve Woods\nSite Phone: 2637770653996', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, NULL, '91261002', 2, 'MCDANIEL, MORGAN AND LOWE', NULL, 'Nicholas Miller', '2637707671271', NULL, '14877 ROTH SQUARES', 'BULAWAYO', 'Bulawayo', NULL, 'Castles', '2025-11-07', 'iWL252', '45356102', 'Condition: Error\nSite Contact: Bradley Church\nSite Phone: 2637768102406', 'active', NULL, NULL, '2025-07-30 18:56:59', '2025-08-10 13:49:03', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, NULL, '71818079', 2, 'JOHNSON PLC', NULL, 'Brandon Robinson', '2637731899601', NULL, '20306 MARY CREST APT. 837', 'GWERU', 'Midlands', NULL, 'PAX', '2025-02-28', 'S80', '69668613', 'Condition: Error\nIssues: Effect understand position word.\nComments: Discuss point meeting.\nCorrective Action: Follow up needed\nSite Contact: Christopher Kelly\nSite Phone: 2637728783906', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, NULL, '75444463', 2, 'NUNEZ, CASTANEDA AND FOX', NULL, 'Amanda Cooper', '2637716621514', NULL, '115 LARRY PASSAGE APT. 623', 'GWERU', 'Midlands', NULL, 'Verifone', '2025-03-27', 'S80', '68878567', 'Condition: Good\nSite Contact: Crystal Smith\nSite Phone: 2637725250413', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, NULL, '75319161', 2, 'JOHNSON-UNDERWOOD', NULL, 'Kathleen Randall', '2637709360376', NULL, '176 PHILLIPS RIDGE', 'GWERU', 'Midlands', NULL, 'Castles', '2025-05-08', 'S80', '21788452', 'Condition: Worn\nSite Contact: Renee Lewis\nSite Phone: 2637739988147', 'maintenance', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'maintenance', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, NULL, '80622282', 2, 'WEBB-SMITH', NULL, 'Amanda Wright', '2637777725098', NULL, '8119 COHEN GROVE', 'HARARE', 'Harare', NULL, 'Verifone', '2025-05-23', 'VX-520', '42425481', 'Condition: Worn\nSite Contact: Ricky Becker\nSite Phone: 2637783152458', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, NULL, '95318965', 2, 'MORRIS, ENGLISH AND CANTU', NULL, 'Holly Allen', '2637731497040', NULL, '28716 CAROLYN POINT', 'GWERU', 'Midlands', NULL, 'Verifone', '2025-04-18', 'A920', '33404625', 'Issues: True dream.\nComments: These television sea rich.\nCorrective Action: Follow up needed\nSite Contact: Bradley Martin\nSite Phone: 2637764770792', 'faulty', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'faulty', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, NULL, '95858418', 2, 'COLLIER-PINEDA', NULL, 'Julia Thompson', '2637761754448', NULL, '8564 FRANCISCO EXTENSIONS APT. 551', 'BULAWAYO', 'Bulawayo', NULL, 'PAX', '2025-01-27', 'S80', '31406618', 'Condition: Not applicable\nSite Contact: Justin Perkins\nSite Phone: 2637771165847', 'offline', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'offline', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, NULL, '95596657', 2, 'CARTER-DODSON', NULL, 'Nicholas Davies', '2637723523732', NULL, '36873 JOSHUA EXTENSIONS APT. 063', 'HARARE', 'Harare', NULL, 'Ingenico', '2025-01-08', 'A920', '53104607', 'Condition: Not applicable\nIssues: Mind job condition.\nComments: Information easy play real word yet future her.\nCorrective Action: Follow up needed\nSite Contact: Frank Huerta\nSite Phone: 2637786339244', 'faulty', NULL, NULL, '2025-07-30 18:56:59', '2025-07-30 18:56:59', NULL, NULL, 'faulty', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, NULL, 'TERM-1000', 4, 'and Sons', NULL, 'check', '03624194', NULL, 'Barbara Ortiz', '(629)780-0031x68524', 'michael51@gmail.com', NULL, '5', NULL, 'move', '2024-08-29', 'Condition: 76264257\nIssues: Woman trial put plan.\nComments: active\nCorrective Action: 2025-06-27\nSite Contact: 2026-06-27\nSite Phone: 2025-03-08 22:01:46', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, NULL, 'TERM-1001', 4, 'LLC', NULL, 'friend', '7475242114', NULL, 'Ashley Sanchez', '1779531306', 'oclark@yahoo.com', NULL, '2', NULL, 'movie', '2024-11-27', 'Condition: 80775164\nIssues: My present nor ground.\nComments: maintenance\nCorrective Action: 2025-03-28\nSite Contact: 2026-03-28\nSite Phone: 2024-08-08 19:14:23', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, NULL, 'TERM-1002', 4, 'PLC', NULL, 'our', '403757287685', NULL, 'Brian Snyder', '001-841-391-0722x106', 'batesdebra@payne-page.com', NULL, '3', NULL, 'toward', '2025-03-24', 'Condition: 92029690\nIssues: Product thing some everything.\nComments: faulty\nCorrective Action: 2025-07-13\nSite Contact: 2026-07-13\nSite Phone: 2025-05-02 12:43:06', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, NULL, 'TERM-1003', 4, 'and Sons', NULL, 'just', '692372906342', NULL, 'John Parker', '2862854379', 'jodipowell@smith-davis.com', NULL, '4', NULL, 'growth', '2024-10-04', 'Condition: 05444993\nIssues: Year by garden factor between relate key.\nComments: faulty\nCorrective Action: 2025-08-01\nSite Contact: 2026-08-01\nSite Phone: 2025-07-27 05:09:56', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, NULL, 'TERM-1004', 4, 'Group', NULL, 'toward', '2280517857586', NULL, 'Ann Wright', '304-616-5428', 'sarahbarr@barron.org', NULL, '4', NULL, 'reveal', '2025-06-07', 'Condition: 96084006\nIssues: Modern word bar article.\nComments: maintenance\nCorrective Action: 2025-08-01\nSite Contact: 2026-08-01\nSite Phone: 2025-02-03 13:11:59', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, NULL, 'TERM-1005', 4, 'LLC', NULL, 'nature', '2016921655', NULL, 'Kristine Reid', '(770)533-2318x3219', 'virginiadean@bates.com', NULL, '4', NULL, 'important', '2023-08-27', 'Condition: 03945990\nIssues: Central I quite until account science employee.\nComments: active\nCorrective Action: 2023-11-09\nSite Contact: 2024-11-08\nSite Phone: 2025-02-01 14:49:55', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, NULL, 'TERM-1006', 4, 'PLC', NULL, 'together', '1775792626404', NULL, 'Christopher Fisher', '+1-293-734-0138x15657', 'higginsruben@hotmail.com', NULL, '4', NULL, 'wait', '2024-09-12', 'Condition: 53969384\nIssues: Answer brother fast little pick another.\nComments: decommissioned\nCorrective Action: 2025-04-17\nSite Contact: 2026-04-17\nSite Phone: 2025-05-25 05:50:02', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, NULL, 'TERM-1007', 4, 'Ltd', NULL, 'herself', '51991726637', NULL, 'Kimberly Webb', '684.025.4774', 'cory30@ramirez.com', NULL, '5', NULL, 'material', '2025-07-04', 'Condition: 26267435\nIssues: Anyone four dog sing raise necessary her.\nComments: maintenance\nCorrective Action: 2025-07-11\nSite Contact: 2026-07-11\nSite Phone: 2025-06-27 10:42:54', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, NULL, 'TERM-1008', 4, 'PLC', NULL, 'citizen', '064089930', NULL, 'Robert Freeman', '083.862.9578x8519', 'veronica05@cox.com', NULL, '4', NULL, 'than', '2025-02-08', 'Condition: 92608801\nIssues: Even under kind say.\nComments: offline\nCorrective Action: 2025-06-21\nSite Contact: 2026-06-21\nSite Phone: 2024-09-10 00:41:11', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, NULL, 'TERM-1009', 4, 'and Sons', NULL, 'TV', '971516637', NULL, 'Casey Santos', '+1-347-882-4701x687', 'yflores@williams.biz', NULL, '1', NULL, 'wife', '2024-04-03', 'Condition: 28257245\nIssues: Class really thus little one ball.\nComments: offline\nCorrective Action: 2024-08-26\nSite Contact: 2025-08-26\nSite Phone: 2025-06-20 07:19:07', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, NULL, 'TERM-1010', 4, 'Inc', NULL, 'have', '91308741', NULL, 'Tina Neal', '948-166-9974', 'brownelizabeth@black.com', NULL, '4', NULL, 'health', '2024-12-12', 'Condition: 51263725\nIssues: Time nor serious note personal.\nComments: offline\nCorrective Action: 2024-12-16\nSite Contact: 2025-12-16\nSite Phone: 2025-01-28 10:06:35', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, NULL, 'TERM-1011', 4, 'LLC', NULL, 'those', '7460629931440', NULL, 'Lisa Patel', '(003)340-6523x42236', 'jameshorton@stephens.org', NULL, '5', NULL, 'stock', '2023-11-28', 'Condition: 24191698\nIssues: Across college site sort.\nComments: maintenance\nCorrective Action: 2024-05-11\nSite Contact: 2025-05-11\nSite Phone: 2025-06-20 15:56:34', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, NULL, 'TERM-1012', 4, 'and Sons', NULL, 'apply', '1234132722031', NULL, 'Charlene Scott', '001-525-275-5139', 'kimberly81@hotmail.com', NULL, '4', NULL, 'seat', '2024-10-13', 'Condition: 06495410\nIssues: Cultural body during note task thousand test.\nComments: offline\nCorrective Action: 2025-02-17\nSite Contact: 2026-02-17\nSite Phone: 2024-10-15 09:58:19', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, NULL, 'TERM-1013', 4, 'Group', NULL, 'certainly', '265788030965', NULL, 'Douglas Gonzalez', '001-806-588-5219x316', 'qwilson@hotmail.com', NULL, '5', NULL, 'store', '2023-09-25', 'Condition: 15654382\nIssues: Read prove exactly order.\nComments: offline\nCorrective Action: 2024-12-18\nSite Contact: 2025-12-18\nSite Phone: 2025-05-03 06:22:06', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, NULL, 'TERM-1014', 4, 'Inc', NULL, 'right', '7751959722885', NULL, 'Thomas Salinas', '001-494-532-7853x8161', 'wesley61@pena-phillips.com', NULL, '2', NULL, 'though', '2025-07-29', 'Condition: 97824286\nIssues: Law use blood democratic approach anyone share.\nComments: offline\nCorrective Action: 2025-07-30\nSite Contact: 2026-07-30\nSite Phone: 2025-04-10 15:01:13', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, NULL, 'TERM-1015', 4, 'Ltd', NULL, 'base', '01748107', NULL, 'Lynn Cruz', '(847)525-1228x4667', 'kperez@hotmail.com', NULL, '1', NULL, 'father', '2024-03-09', 'Condition: 52457735\nIssues: Soldier note near cold ok international tough.\nComments: faulty\nCorrective Action: 2024-04-03\nSite Contact: 2025-04-03\nSite Phone: 2024-09-16 06:36:10', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, NULL, 'TERM-1016', 4, 'Ltd', NULL, 'run', '5849936051383', NULL, 'James Schmidt', '353-722-0794', 'rwebb@yahoo.com', NULL, '1', NULL, 'unit', '2024-10-28', 'Condition: 12783953\nIssues: Guy most family face staff growth conference.\nComments: offline\nCorrective Action: 2025-01-01\nSite Contact: 2026-01-01\nSite Phone: 2025-06-02 10:39:09', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, NULL, 'TERM-1017', 4, 'Inc', NULL, 'relate', '998535730172', NULL, 'Alexandra Walters', '001-717-339-1368x92297', 'franciscomartin@shaw.com', NULL, '1', NULL, 'oil', '2024-03-30', 'Condition: 53949826\nIssues: Claim TV our finally reveal personal forward.\nComments: offline\nCorrective Action: 2024-08-11\nSite Contact: 2025-08-11\nSite Phone: 2025-07-25 12:59:32', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, NULL, 'TERM-1018', 4, 'Inc', NULL, 'sense', '93504823850', NULL, 'Bianca Carter', '962-480-3919', 'richard46@yahoo.com', NULL, '4', NULL, 'focus', '2025-02-20', 'Condition: 14975516\nIssues: Upon drop information ability.\nComments: faulty\nCorrective Action: 2025-05-27\nSite Contact: 2026-05-27\nSite Phone: 2024-11-29 17:12:11', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, NULL, 'TERM-1019', 4, 'Ltd', NULL, 'three', '872240747655', NULL, 'Lisa Johnson', '001-882-864-1805', 'carl91@yahoo.com', NULL, '2', NULL, 'religious', '2023-08-08', 'Condition: 01493558\nIssues: Tough commercial than right consumer meet.\nComments: active\nCorrective Action: 2023-08-27\nSite Contact: 2024-08-26\nSite Phone: 2024-08-28 15:09:48', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, NULL, 'TERM-1020', 4, 'Group', NULL, 'skin', '173311622', NULL, 'Aaron Lee', '9749889903', 'michaelherring@hotmail.com', NULL, '3', NULL, 'business', '2024-07-22', 'Condition: 92167547\nIssues: Painting personal number now evidence simply key.\nComments: maintenance\nCorrective Action: 2024-08-30\nSite Contact: 2025-08-30\nSite Phone: 2025-03-11 08:30:25', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, NULL, 'TERM-1021', 4, 'Inc', NULL, 'increase', '7723023876', NULL, 'Christopher Pierce', '485.533.3166x86085', 'rodriguezkevin@wilkinson-snow.com', NULL, '3', NULL, 'per', '2025-05-02', 'Condition: 86971920\nIssues: Above time such her yes for new.\nComments: active\nCorrective Action: 2025-08-05\nSite Contact: 2026-08-05\nSite Phone: 2024-11-03 06:25:02', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, NULL, 'TERM-1022', 4, 'Inc', NULL, 'key', '7765950687890', NULL, 'David Maynard', '235.762.7811', 'steven52@nguyen.info', NULL, '3', NULL, 'girl', '2025-06-03', 'Condition: 93404292\nIssues: Form might teacher type shoulder.\nComments: active\nCorrective Action: 2025-06-13\nSite Contact: 2026-06-13\nSite Phone: 2025-04-18 18:53:10', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, NULL, 'TERM-1023', 4, 'PLC', NULL, 'feeling', '6444864679938', NULL, 'Charlene Perkins', '+1-340-099-2246', 'carlos23@cortez.com', NULL, '3', NULL, 'job', '2024-11-29', 'Condition: 39724029\nIssues: Town much best arrive light.\nComments: maintenance\nCorrective Action: 2025-03-23\nSite Contact: 2026-03-23\nSite Phone: 2025-03-24 18:55:06', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, NULL, 'TERM-1024', 4, 'Ltd', NULL, 'build', '88744935', NULL, 'Charles Stewart', '503.112.6786', 'rvega@hotmail.com', NULL, '5', NULL, 'hair', '2025-05-22', 'Condition: 29815981\nIssues: Pretty already both reveal Mrs.\nComments: decommissioned\nCorrective Action: 2025-06-11\nSite Contact: 2026-06-11\nSite Phone: 2025-05-13 05:41:12', 'active', NULL, NULL, '2025-08-06 10:40:58', '2025-08-06 10:40:58', NULL, NULL, 'active', 'deployed', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Triggers `pos_terminals`
--
DELIMITER $$
CREATE TRIGGER `trg_pos_terminals_bi` BEFORE INSERT ON `pos_terminals` FOR EACH ROW BEGIN
  DECLARE _name VARCHAR(255);
  SELECT name INTO _name
    FROM regions
   WHERE id = NEW.region_id
   LIMIT 1;
  SET NEW.region = _name;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_pos_terminals_bu` BEFORE UPDATE ON `pos_terminals` FOR EACH ROW BEGIN
  IF NEW.region_id <> OLD.region_id THEN
    SET NEW.region = (
      SELECT name
      FROM regions
      WHERE id = NEW.region_id
      LIMIT 1
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_code` varchar(50) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `project_type` enum('discovery','servicing','support','maintenance','installation','upgrade','decommission') NOT NULL DEFAULT 'support',
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','completed','paused','cancelled') NOT NULL DEFAULT 'active',
  `priority` enum('low','normal','high','emergency') NOT NULL DEFAULT 'normal',
  `budget` decimal(12,2) DEFAULT NULL,
  `estimated_terminals_count` int(11) DEFAULT 0,
  `actual_terminals_count` int(11) DEFAULT 0,
  `project_manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_code`, `project_name`, `client_id`, `project_type`, `description`, `start_date`, `end_date`, `status`, `priority`, `budget`, `estimated_terminals_count`, `actual_terminals_count`, `project_manager_id`, `created_by`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'CLI-DIS-202508-01', 'Terminal Discovery Phase 1', 1, 'discovery', 'Initial discovery and assessment of all Client A terminals in Harare region', NULL, NULL, 'active', 'high', NULL, 25, 0, NULL, 1, NULL, '2025-08-11 10:29:12', '2025-08-11 10:29:12'),
(2, 'CLI-SER-202508-01', 'Monthly Servicing Round', 1, 'servicing', 'Regular monthly servicing for active terminals', NULL, NULL, 'active', 'normal', NULL, 15, 0, NULL, 1, NULL, '2025-08-11 10:29:12', '2025-08-11 10:29:12'),
(3, 'CLI-SUP-202508-01', 'Technical Support Project', 1, 'support', 'Ongoing technical support and troubleshooting', NULL, NULL, 'active', 'normal', NULL, 0, 0, NULL, 1, NULL, '2025-08-11 10:29:12', '2025-08-11 10:29:12'),
(4, 'JOH-SER-202508-01', 'Johnson PLC Servicing', 2, 'servicing', 'Quarterly servicing for Johnson PLC terminals', NULL, NULL, 'active', 'normal', NULL, 8, 0, NULL, 1, NULL, '2025-08-11 10:29:12', '2025-08-11 10:29:12'),
(5, 'PROJ-SER-20250813-985', 'trial project', 4, 'servicing', NULL, '2025-08-13', NULL, 'active', 'normal', NULL, 0, 0, NULL, 5, NULL, '2025-08-13 14:31:19', '2025-08-13 14:31:19');

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `region_code` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `regions`
--

INSERT INTO `regions` (`id`, `name`, `description`, `created_at`, `updated_at`, `region_code`, `is_active`) VALUES
(1, 'North Region', 'Northern area covering Westlands, Kasarani, and surrounding areas', NULL, NULL, 'NTH', 1),
(2, 'South Region', 'Southern area covering Karen, Langata, and surrounding areas', NULL, NULL, 'STH', 1),
(3, 'East Region', 'Eastern area covering Eastlands, Embakasi, and surrounding areas', NULL, NULL, 'EST', 1),
(4, 'West Region', 'Western area covering Kiambu, Kikuyu, and surrounding areas', NULL, NULL, 'WST', 1),
(5, 'Central Region', 'Central area covering CBD, Upper Hill, and surrounding areas', NULL, NULL, 'CTR', 1);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'Manager', '[\"view_dashboard\",\"manage_team\",\"view_clients\",\"view_reports\",\"approve_requests\",\"view_own_data\"]', 1, '2025-07-22 08:54:31', '2025-07-23 10:43:27'),
(3, 'Technician', '[\"view_jobs\",\"view_terminals\",\"update_terminals\",\"view_own_data\"]', 1, '2025-07-22 08:54:31', '2025-07-23 10:43:27'),
(4, 'Employee', '[\"view_own_data\",\"request_assets\",\"view_clients\"]', 1, '2025-07-22 08:54:31', '2025-07-23 10:43:27'),
(5, 'Supervisor', '[\"manage_team\",\"view_reports\",\"approve_minor_requests\",\"view_dashboard\"]', 1, '2025-07-22 08:54:31', '2025-07-22 08:54:31'),
(7, 'Admin', '[\"view_dashboard\",\"manage_assets\",\"manage_clients\",\"manage_team\",\"view_reports\",\"approve_requests\"]', 1, '2025-07-22 10:20:43', '2025-07-23 10:43:27'),
(8, 'super_admin', '[\"all\",\"manage_team\",\"view_dashboard\",\"manage_assets\",\"view_clients\",\"manage_clients\",\"view_reports\",\"approve_requests\"]', 1, '2025-07-23 10:43:27', '2025-07-23 16:17:14'),
(9, 'bypass_all', '[\"all\",\"view_dashboard\",\"manage_assets\",\"view_clients\",\"manage_team\",\"view_reports\",\"approve_requests\",\"view_jobs\",\"manage_terminals\",\"view_terminals\",\"update_terminals\",\"view_own_data\",\"request_assets\"]', 1, '2025-07-23 15:53:04', '2025-07-23 15:53:04');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
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
('ik3N0zfSC0VjiRBNAPVZzp5nH6aBfDT7OiDuCkyx', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV0xDUktOTXlNM3l4ZmExTkdSTVRiRUFmcWxMSUViY1RMdkJ3TE9CUCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo3ODoiaHR0cDovL2xvY2FsaG9zdC9kYXNoYm9hcmQvUmV2aXZhbF9UZWNobm9sb2dpZXMvcHVibGljL2RlcGxveW1lbnQvaGllcmFyY2hpY2FsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755105391),
('lOlMBsNlclFbj52eVM5NeM70KJay4aRm8JGdAU4h', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZzF4YjVPZDJnQmJ2UWVZVmZSS0tNUHJ5SkQ5RDFpM1JpY2M5djhHQiI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo3ODoiaHR0cDovL2xvY2FsaG9zdC9kYXNoYm9hcmQvUmV2aXZhbF9UZWNobm9sb2dpZXMvcHVibGljL2RlcGxveW1lbnQvaGllcmFyY2hpY2FsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755156050),
('pvOG2yLXHZ8cXckBRwRBgk6EFI4ROsZmkTYE83xu', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWkpYeTlaY3BSQVZvbU5uRkNXVkxXOVlBRnR6SDd6YUwxcllrVXJHVyI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo3ODoiaHR0cDovL2xvY2FsaG9zdC9kYXNoYm9hcmQvUmV2aXZhbF9UZWNobm9sb2dpZXMvcHVibGljL2RlcGxveW1lbnQvaGllcmFyY2hpY2FsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755087445),
('QTaHWoq1boYdnehI8U97JXbZbZbo4WIEc8Zzu8H4', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY1oxc3cwOXR2UDhqZ3ZtNk9UMEVOQkY5SVB4ZGw3ODhnYldQVnN3RCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo3ODoiaHR0cDovL2xvY2FsaG9zdC9kYXNoYm9hcmQvUmV2aXZhbF9UZWNobm9sb2dpZXMvcHVibGljL2RlcGxveW1lbnQvaGllcmFyY2hpY2FsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754980010),
('RuV2PWoXYTzXk9LC6gwjyDgpeli7qnYwsZtGWuqt', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibUFFNjZxU3lZTERIdjJDYUU4N0ZielZKVURqS2NnaGxzbFRQc0N3TyI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo3ODoiaHR0cDovL2xvY2FsaG9zdC9kYXNoYm9hcmQvUmV2aXZhbF9UZWNobm9sb2dpZXMvcHVibGljL2RlcGxveW1lbnQvaGllcmFyY2hpY2FsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1754974841);

-- --------------------------------------------------------

--
-- Table structure for table `sync_logs`
--

CREATE TABLE `sync_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `technician_id` bigint(20) UNSIGNED NOT NULL,
  `sync_type` enum('visits','tickets','terminals','full') NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `sync_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `records_count` int(11) DEFAULT 0,
  `success` tinyint(1) DEFAULT 1,
  `error_message` text DEFAULT NULL,
  `data_hash` varchar(64) DEFAULT NULL COMMENT 'For integrity checking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` longtext DEFAULT NULL,
  `type` varchar(50) DEFAULT 'string',
  `description` text DEFAULT NULL,
  `group` varchar(100) DEFAULT 'general',
  `is_encrypted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `technician_visits`
--

CREATE TABLE `technician_visits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `visit_id` varchar(50) NOT NULL,
  `technician_id` bigint(20) UNSIGNED NOT NULL,
  `pos_terminal_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visit_date` datetime NOT NULL,
  `visit_type` enum('scheduled','emergency','follow_up','inspection') DEFAULT 'scheduled',
  `asset_type` enum('pos_terminal','vehicle','it_equipment','furniture','other') NOT NULL,
  `asset_id` varchar(100) DEFAULT NULL,
  `terminal_status` enum('seen_working','seen_issues','not_seen','relocated','missing') NOT NULL,
  `completion_status` enum('completed','partial','cancelled','rescheduled') DEFAULT 'completed',
  `next_visit_required` tinyint(1) DEFAULT 0,
  `next_visit_reason` text DEFAULT NULL,
  `technician_feedback` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photos`)),
  `duration_minutes` int(11) DEFAULT NULL,
  `issues_found` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`issues_found`)),
  `merchant_feedback` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `signature_data` longtext DEFAULT NULL COMMENT 'Digital signature from merchant',
  `offline_sync_id` varchar(100) DEFAULT NULL COMMENT 'For offline mobile sync',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` varchar(50) NOT NULL,
  `mobile_created` tinyint(1) DEFAULT 0 COMMENT 'Created from mobile app',
  `offline_sync_id` varchar(100) DEFAULT NULL COMMENT 'For offline mobile sync',
  `technician_id` bigint(20) UNSIGNED NOT NULL,
  `pos_terminal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `issue_type` enum('hardware_malfunction','software_issue','network_connectivity','user_training','maintenance_required','replacement_needed','other') NOT NULL,
  `priority` enum('critical','high','medium','low') NOT NULL,
  `estimated_resolution_time` int(11) DEFAULT NULL COMMENT 'Minutes to resolve',
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','in_progress','resolved','closed','cancelled') DEFAULT 'open',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `resolution` text DEFAULT NULL,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `ticket_id`, `mobile_created`, `offline_sync_id`, `technician_id`, `pos_terminal_id`, `client_id`, `visit_id`, `issue_type`, `priority`, `estimated_resolution_time`, `title`, `description`, `status`, `assigned_to`, `resolution`, `attachments`, `created_at`, `updated_at`, `resolved_at`) VALUES
(11, 'TKT-2025-001', 0, NULL, 5, 6, NULL, NULL, 'network_connectivity', 'high', 800, 'Attend to Client XYZ issue', 'please attend to this before end of week', 'resolved', NULL, NULL, NULL, '2025-08-06 08:23:17', '2025-08-06 08:24:08', '2025-08-06 08:23:34');

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
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `asset_availability`
--
DROP TABLE IF EXISTS `asset_availability`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `asset_availability`  AS SELECT `a`.`id` AS `id`, `a`.`name` AS `name`, `a`.`category` AS `category`, `a`.`brand` AS `brand`, `a`.`model` AS `model`, `a`.`stock_quantity` AS `stock_quantity`, `a`.`assigned_quantity` AS `assigned_quantity`, `a`.`available_quantity` AS `available_quantity`, `a`.`min_stock_level` AS `min_stock_level`, CASE WHEN `a`.`available_quantity` <= 0 THEN 'Out of Stock' WHEN `a`.`available_quantity` <= `a`.`min_stock_level` THEN 'Low Stock' ELSE 'Available' END AS `availability_status`, `a`.`status` AS `asset_status` FROM `assets` AS `a` WHERE `a`.`status` = 'asset-active' ;

-- --------------------------------------------------------

--
-- Structure for view `current_asset_assignments`
--
DROP TABLE IF EXISTS `current_asset_assignments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `current_asset_assignments`  AS SELECT `aa`.`id` AS `assignment_id`, `a`.`id` AS `asset_id`, `a`.`name` AS `asset_name`, `a`.`category` AS `category`, `a`.`brand` AS `brand`, `a`.`model` AS `model`, `a`.`sku` AS `sku`, `aa`.`quantity_assigned` AS `quantity_assigned`, `e`.`id` AS `employee_id`, concat(`e`.`first_name`,' ',`e`.`last_name`) AS `employee_name`, `e`.`employee_number` AS `employee_number`, `d`.`name` AS `department_name`, `aa`.`assignment_date` AS `assignment_date`, `aa`.`expected_return_date` AS `expected_return_date`, `aa`.`condition_when_assigned` AS `condition_when_assigned`, `aa`.`assignment_notes` AS `assignment_notes`, concat(`assigned_by`.`first_name`,' ',`assigned_by`.`last_name`) AS `assigned_by_name`, `aa`.`created_at` AS `created_at` FROM ((((`asset_assignments` `aa` join `assets` `a` on(`aa`.`asset_id` = `a`.`id`)) join `employees` `e` on(`aa`.`employee_id` = `e`.`id`)) left join `departments` `d` on(`e`.`department_id` = `d`.`id`)) join `employees` `assigned_by` on(`aa`.`assigned_by` = `assigned_by`.`id`)) WHERE `aa`.`status` = 'assigned' ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_category_status` (`category`,`status`),
  ADD KEY `idx_stock_levels` (`stock_quantity`,`min_stock_level`),
  ADD KEY `idx_requestable` (`is_requestable`),
  ADD KEY `idx_assets_availability` (`available_quantity`,`status`);

--
-- Indexes for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_asset_assignments_asset` (`asset_id`),
  ADD KEY `idx_asset_assignments_employee` (`employee_id`),
  ADD KEY `idx_asset_assignments_status` (`status`),
  ADD KEY `idx_asset_assignments_dates` (`assignment_date`,`actual_return_date`),
  ADD KEY `idx_asset_assignments_assigned_by` (`assigned_by`),
  ADD KEY `idx_asset_assignments_returned_to` (`returned_to`),
  ADD KEY `idx_asset_assignments_request` (`asset_request_id`);

--
-- Indexes for table `asset_assignment_history`
--
ALTER TABLE `asset_assignment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_assignment_history_assignment` (`assignment_id`),
  ADD KEY `idx_assignment_history_action` (`action`),
  ADD KEY `idx_assignment_history_date` (`action_date`),
  ADD KEY `fk_assignment_history_from_employee` (`from_employee_id`),
  ADD KEY `fk_assignment_history_to_employee` (`to_employee_id`),
  ADD KEY `fk_assignment_history_performed_by` (`performed_by`);

--
-- Indexes for table `asset_requests`
--
ALTER TABLE `asset_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `request_number` (`request_number`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `asset_request_items`
--
ALTER TABLE `asset_request_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `asset_request_id` (`asset_request_id`),
  ADD KEY `asset_id` (`asset_id`);

--
-- Indexes for table `business_licenses`
--
ALTER TABLE `business_licenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `idx_business_licenses_status_expiry` (`status`,`expiry_date`),
  ADD KEY `idx_business_licenses_type` (`license_type`),
  ADD KEY `idx_business_licenses_department` (`department_id`),
  ADD KEY `idx_business_licenses_priority` (`priority_level`),
  ADD KEY `idx_business_licenses_responsible` (`responsible_employee_id`),
  ADD KEY `idx_business_licenses_created_by` (`created_by`),
  ADD KEY `idx_business_licenses_updated_by` (`updated_by`),
  ADD KEY `idx_business_licenses_direction` (`license_direction`),
  ADD KEY `idx_business_licenses_customer_email` (`customer_email`),
  ADD KEY `idx_business_licenses_customer_id` (`customer_id`),
  ADD KEY `idx_business_licenses_billing_cycle` (`billing_cycle`);

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
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_type_slug_unique` (`type`,`slug`),
  ADD KEY `idx_categories_type_active` (`type`,`is_active`),
  ADD KEY `idx_categories_sort_order` (`sort_order`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cities_region_active` (`region_id`,`is_active`),
  ADD KEY `idx_cities_name` (`name`),
  ADD KEY `fk_cities_created_by` (`created_by`),
  ADD KEY `fk_cities_updated_by` (`updated_by`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_code` (`client_code`),
  ADD KEY `idx_company_name` (`company_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_status_company_name` (`status`,`company_name`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contracts_contract_number_unique` (`contract_number`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deployment_templates`
--
ALTER TABLE `deployment_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `deployment_templates_template_name_unique` (`template_name`),
  ADD KEY `deployment_templates_region_id_index` (`region_id`),
  ADD KEY `deployment_templates_created_by_index` (`created_by`);

--
-- Indexes for table `device_registrations`
--
ALTER TABLE `device_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_technician_device` (`technician_id`,`device_id`),
  ADD KEY `idx_device_active` (`device_id`,`is_active`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  ADD UNIQUE KEY `employees_email_unique` (`email`),
  ADD KEY `employees_department_id_foreign` (`department_id`),
  ADD KEY `employees_role_id_foreign` (`role_id`),
  ADD KEY `employees_manager_id_foreign` (`manager_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `import_mappings`
--
ALTER TABLE `import_mappings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `import_mappings_mapping_name_unique` (`mapping_name`),
  ADD KEY `import_mappings_client_id_index` (`client_id`),
  ADD KEY `import_mappings_created_by_index` (`created_by`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_assignments`
--
ALTER TABLE `job_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `assignment_id` (`assignment_id`),
  ADD KEY `region_id` (`region_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_job_assignments_technician` (`technician_id`),
  ADD KEY `idx_job_assignments_date` (`scheduled_date`),
  ADD KEY `idx_job_assignments_project` (`project_id`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `licenses`
--
ALTER TABLE `licenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `licenses_license_number_unique` (`license_number`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pos_terminals`
--
ALTER TABLE `pos_terminals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `terminal_id` (`terminal_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_pos_terminals_region` (`region_id`),
  ADD KEY `idx_pos_terminals_status` (`current_status`),
  ADD KEY `idx_pos_terminals_current_status` (`current_status`),
  ADD KEY `fk_pos_terminals_updated_by` (`last_updated_by`),
  ADD KEY `idx_merchant_search` (`merchant_name`,`terminal_id`),
  ADD KEY `idx_location_search` (`city`,`province`),
  ADD KEY `idx_status_date` (`status`,`last_service_date`),
  ADD KEY `idx_deployment_status` (`deployment_status`),
  ADD KEY `idx_status_city` (`current_status`,`city`),
  ADD KEY `idx_merchant_terminal_search` (`merchant_name`,`terminal_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_code` (`project_code`),
  ADD KEY `idx_projects_client` (`client_id`),
  ADD KEY `idx_projects_type` (`project_type`),
  ADD KEY `idx_projects_status` (`status`),
  ADD KEY `idx_projects_manager` (`project_manager_id`),
  ADD KEY `idx_projects_creator` (`created_by`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `region_code` (`region_code`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sync_logs`
--
ALTER TABLE `sync_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_technician_sync` (`technician_id`,`sync_timestamp`),
  ADD KEY `idx_device_sync` (`device_id`,`sync_timestamp`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`),
  ADD KEY `idx_system_settings_group` (`group`);

--
-- Indexes for table `technician_visits`
--
ALTER TABLE `technician_visits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visit_id` (`visit_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `idx_technician_visits_date` (`visit_date`),
  ADD KEY `idx_technician_visits_technician` (`technician_id`),
  ADD KEY `idx_technician_visits_terminal` (`pos_terminal_id`),
  ADD KEY `idx_offline_sync` (`offline_sync_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `technician_id` (`technician_id`),
  ADD KEY `pos_terminal_id` (`pos_terminal_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `idx_tickets_status` (`status`),
  ADD KEY `idx_tickets_priority` (`priority`),
  ADD KEY `idx_mobile_sync` (`offline_sync_id`),
  ADD KEY `idx_mobile_created` (`mobile_created`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `asset_assignment_history`
--
ALTER TABLE `asset_assignment_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `asset_requests`
--
ALTER TABLE `asset_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `asset_request_items`
--
ALTER TABLE `asset_request_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `business_licenses`
--
ALTER TABLE `business_licenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `deployment_templates`
--
ALTER TABLE `deployment_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `device_registrations`
--
ALTER TABLE `device_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `import_mappings`
--
ALTER TABLE `import_mappings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_assignments`
--
ALTER TABLE `job_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `licenses`
--
ALTER TABLE `licenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pos_terminals`
--
ALTER TABLE `pos_terminals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sync_logs`
--
ALTER TABLE `sync_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `technician_visits`
--
ALTER TABLE `technician_visits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `asset_assignments`
--
ALTER TABLE `asset_assignments`
  ADD CONSTRAINT `fk_asset_assignments_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asset_assignments_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `fk_asset_assignments_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_asset_assignments_request` FOREIGN KEY (`asset_request_id`) REFERENCES `asset_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_asset_assignments_returned_to` FOREIGN KEY (`returned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `asset_assignment_history`
--
ALTER TABLE `asset_assignment_history`
  ADD CONSTRAINT `fk_assignment_history_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `asset_assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assignment_history_from_employee` FOREIGN KEY (`from_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_assignment_history_performed_by` FOREIGN KEY (`performed_by`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `fk_assignment_history_to_employee` FOREIGN KEY (`to_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `asset_requests`
--
ALTER TABLE `asset_requests`
  ADD CONSTRAINT `asset_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `asset_request_items`
--
ALTER TABLE `asset_request_items`
  ADD CONSTRAINT `asset_request_items_ibfk_1` FOREIGN KEY (`asset_request_id`) REFERENCES `asset_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asset_request_items_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `business_licenses`
--
ALTER TABLE `business_licenses`
  ADD CONSTRAINT `fk_business_licenses_created_by` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_business_licenses_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_business_licenses_responsible_employee` FOREIGN KEY (`responsible_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_business_licenses_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `fk_cities_created_by` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cities_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cities_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `deployment_templates`
--
ALTER TABLE `deployment_templates`
  ADD CONSTRAINT `deployment_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `deployment_templates_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `device_registrations`
--
ALTER TABLE `device_registrations`
  ADD CONSTRAINT `fk_device_technician` FOREIGN KEY (`technician_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `employees_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `import_mappings`
--
ALTER TABLE `import_mappings`
  ADD CONSTRAINT `import_mappings_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `import_mappings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_assignments`
--
ALTER TABLE `job_assignments`
  ADD CONSTRAINT `fk_job_assignments_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_assignments_ibfk_1` FOREIGN KEY (`technician_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_assignments_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `job_assignments_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pos_terminals`
--
ALTER TABLE `pos_terminals`
  ADD CONSTRAINT `fk_pos_terminals_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pos_terminals_updated_by` FOREIGN KEY (`last_updated_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pos_terminals_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pos_terminals_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_projects_creator` FOREIGN KEY (`created_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_projects_manager` FOREIGN KEY (`project_manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sync_logs`
--
ALTER TABLE `sync_logs`
  ADD CONSTRAINT `fk_sync_logs_technician` FOREIGN KEY (`technician_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `technician_visits`
--
ALTER TABLE `technician_visits`
  ADD CONSTRAINT `technician_visits_ibfk_1` FOREIGN KEY (`technician_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technician_visits_ibfk_2` FOREIGN KEY (`pos_terminal_id`) REFERENCES `pos_terminals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `technician_visits_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`technician_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`pos_terminal_id`) REFERENCES `pos_terminals` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_ibfk_4` FOREIGN KEY (`visit_id`) REFERENCES `technician_visits` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tickets_ibfk_5` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
