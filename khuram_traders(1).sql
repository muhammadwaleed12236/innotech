-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2026 at 08:27 PM
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
-- Database: `khuram_traders`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `head_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `account_code` varchar(255) DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `type` varchar(255) NOT NULL DEFAULT 'Debit',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_heads`
--

CREATE TABLE `account_heads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Asset','Liability','Equity','Revenue','Expense') DEFAULT NULL,
  `level` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Group, 2=Control, 3=Detail',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `biometric_devices`
--

CREATE TABLE `biometric_devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `port` int(11) NOT NULL DEFAULT 4370,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_sync_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Samsung', '2026-06-08 18:19:34', '2026-06-08 18:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Electronics', '2026-06-08 18:19:33', '2026-06-08 18:19:33'),
(2, 'machine', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(3, 'Tools', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(4, 'Plumbing', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(5, 'Hardware', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(6, 'Electrical', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(7, 'Automotive', '2026-06-08 18:19:34', '2026-06-08 18:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` varchar(255) NOT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_name_ur` varchar(255) DEFAULT NULL,
  `cnic` varchar(255) DEFAULT NULL,
  `filer_type` varchar(255) DEFAULT NULL,
  `zone` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `contact_person_2` varchar(255) DEFAULT NULL,
  `mobile_2` varchar(255) DEFAULT NULL,
  `email_address_2` varchar(255) DEFAULT NULL,
  `customer_type` varchar(255) DEFAULT NULL,
  `sales_officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL,
  `previous_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance_range` decimal(12,2) NOT NULL DEFAULT 0.00,
  `address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `payment_reminder_date` date DEFAULT NULL,
  `reminder_day` varchar(255) DEFAULT NULL,
  `reminder_snoozed_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_ledgers`
--

CREATE TABLE `customer_ledgers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `admin_or_user_id` bigint(20) UNSIGNED NOT NULL,
  `previous_balance` decimal(12,2) NOT NULL,
  `closing_balance` decimal(12,2) NOT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `admin_or_user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_salary_structures`
--

CREATE TABLE `employee_salary_structures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `salary_structure_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_custom` tinyint(1) NOT NULL DEFAULT 0,
  `assigned_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_vouchers`
--

CREATE TABLE `expense_vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `evid` varchar(255) DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `party_id` varchar(255) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `reference_no` text DEFAULT NULL,
  `narration_id` text DEFAULT NULL,
  `row_account_head` text DEFAULT NULL,
  `row_account_id` text DEFAULT NULL,
  `amount` text DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
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
-- Table structure for table `hr_attendances`
--

CREATE TABLE `hr_attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `check_in_photo` varchar(255) DEFAULT NULL,
  `check_in_latitude` decimal(10,8) DEFAULT NULL,
  `check_in_longitude` decimal(11,8) DEFAULT NULL,
  `check_in_location` varchar(255) DEFAULT NULL,
  `check_out_photo` varchar(255) DEFAULT NULL,
  `check_out_latitude` decimal(10,8) DEFAULT NULL,
  `check_out_longitude` decimal(11,8) DEFAULT NULL,
  `check_out_location` varchar(255) DEFAULT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `status` enum('present','absent','late','leave') NOT NULL DEFAULT 'present',
  `is_late` tinyint(1) NOT NULL DEFAULT 0,
  `late_minutes` int(11) NOT NULL DEFAULT 0,
  `is_early_in` tinyint(1) NOT NULL DEFAULT 0,
  `early_in_minutes` int(11) NOT NULL DEFAULT 0,
  `is_early_leave` tinyint(1) NOT NULL DEFAULT 0,
  `early_leave_minutes` int(11) NOT NULL DEFAULT 0,
  `total_hours` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_departments`
--

CREATE TABLE `hr_departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_designations`
--

CREATE TABLE `hr_designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_location` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_employees`
--

CREATE TABLE `hr_employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `designation_id` bigint(20) UNSIGNED NOT NULL,
  `shift_id` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_start_time` time DEFAULT NULL,
  `custom_end_time` time DEFAULT NULL,
  `face_encoding` text DEFAULT NULL,
  `face_photo` varchar(255) DEFAULT NULL,
  `biometric_device_id` bigint(20) UNSIGNED DEFAULT NULL,
  `device_user_id` varchar(255) DEFAULT NULL,
  `fingerprint_enrolled_at` timestamp NULL DEFAULT NULL,
  `last_device_sync_at` timestamp NULL DEFAULT NULL,
  `punch_gap_minutes` int(10) UNSIGNED DEFAULT NULL,
  `pending_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_docs_submitted` tinyint(1) NOT NULL DEFAULT 0,
  `date_of_birth` date DEFAULT NULL,
  `joining_date` date NOT NULL,
  `status` enum('active','non-active','terminated') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_employee_documents`
--

CREATE TABLE `hr_employee_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_holidays`
--

CREATE TABLE `hr_holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `type` enum('public','company','optional') NOT NULL DEFAULT 'public',
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_leaves`
--

CREATE TABLE `hr_leaves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loans`
--

CREATE TABLE `hr_loans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `installment_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Monthly deductible amount, 0 for manual/large sum',
  `status` enum('pending','approved','rejected','paid') NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loan_payments`
--

CREATE TABLE `hr_loan_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'salary_deduction',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_loan_scheduled_deductions`
--

CREATE TABLE `hr_loan_scheduled_deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loan_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `deduction_month` varchar(255) NOT NULL,
  `status` enum('pending','deducted','skipped') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payrolls`
--

CREATE TABLE `hr_payrolls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `payroll_type` enum('monthly','daily') NOT NULL DEFAULT 'monthly',
  `month` varchar(255) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `gross_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allowances` decimal(10,2) NOT NULL DEFAULT 0.00,
  `attendance_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `manual_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `manual_allowances` decimal(10,2) NOT NULL DEFAULT 0.00,
  `carried_forward_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
  `carried_forward_to_next` decimal(10,2) NOT NULL DEFAULT 0.00,
  `deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
  `bonuses` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_salary` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `auto_generated` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('generated','reviewed','paid') NOT NULL DEFAULT 'generated',
  `payment_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payroll_details`
--

CREATE TABLE `hr_payroll_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('allowance','deduction') NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_salary_structures`
--

CREATE TABLE `hr_salary_structures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_structure_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_type` enum('salary','commission','both') NOT NULL DEFAULT 'salary',
  `base_salary` decimal(12,2) NOT NULL DEFAULT 0.00,
  `daily_wages` decimal(10,2) DEFAULT NULL,
  `use_daily_wages` tinyint(1) NOT NULL DEFAULT 0,
  `commission_percentage` decimal(5,2) DEFAULT NULL,
  `sales_target` decimal(12,2) DEFAULT NULL,
  `commission_tiers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`commission_tiers`)),
  `allowances` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`allowances`)),
  `deductions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`deductions`)),
  `attendance_deduction_policy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attendance_deduction_policy`)),
  `carry_forward_deductions` tinyint(1) NOT NULL DEFAULT 0,
  `leave_salary_per_day` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_settings`
--

CREATE TABLE `hr_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `label` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hr_settings`
--

INSERT INTO `hr_settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'attendance_punch_gap_minutes', '20', 'integer', 'attendance', 'Punch Gap (Minutes)', 'Minimum minutes between punches to be considered as separate check-in/check-out. Punches within this gap will be ignored as duplicates.', '2026-06-08 18:19:27', '2026-06-08 18:19:27');

-- --------------------------------------------------------

--
-- Table structure for table `hr_shifts`
--

CREATE TABLE `hr_shifts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_start` time DEFAULT NULL,
  `break_end` time DEFAULT NULL,
  `grace_minutes` int(11) NOT NULL DEFAULT 15,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inward_gatepasses`
--

CREATE TABLE `inward_gatepasses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gatepass_date` date DEFAULT NULL,
  `gatepass_no` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending','linked','cancelled') NOT NULL DEFAULT 'pending',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inward_gatepass_items`
--

CREATE TABLE `inward_gatepass_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inward_gatepass_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `source_type` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `entry_date` date NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `is_reconciled` tinyint(1) NOT NULL DEFAULT 0,
  `reconciled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `party_type` varchar(255) DEFAULT NULL,
  `party_id` bigint(20) UNSIGNED DEFAULT NULL
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
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_07_18_203013_create_categories_table', 1),
(6, '2025_07_18_215154_create_brands_table', 1),
(7, '2025_07_18_220702_create_units_table', 1),
(8, '2025_07_18_231906_create_subcategories_table', 1),
(9, '2025_07_19_074903_create_products_table', 1),
(10, '2025_07_20_220859_create_vendors_table', 1),
(11, '2025_07_20_220905_create_warehouses_table', 1),
(12, '2025_07_20_232951_create_branches_table', 1),
(13, '2025_07_21_125354_create_customers_table', 1),
(14, '2025_07_21_135411_add_status_to_customers_table', 1),
(15, '2025_07_21_181654_create_zones_table', 1),
(16, '2025_07_21_185626_create_sales_officers_table', 1),
(17, '2025_07_21_195751_create_permission_tables', 1),
(18, '2025_07_21_203215_create_transports_table', 1),
(19, '2025_07_21_213504_create_purchases_table', 1),
(20, '2025_07_21_214736_add_urdu_fields_to_transports_table', 1),
(21, '2025_08_09_220420_create_narrations_table', 1),
(22, '2025_08_09_231230_create_vouchers_table', 1),
(23, '2025_08_11_071804_add_barcode_path_to_products_table', 1),
(24, '2025_08_11_092258_add_missing_columns_to_products_table', 1),
(25, '2025_08_11_133922_add_brand_id_to_products_table', 1),
(26, '2025_08_13_225511_create_purchase_items_table', 1),
(27, '2025_08_13_225620_create_stocks_table', 1),
(28, '2025_08_17_213427_create_table_vendor_ledgers', 1),
(29, '2025_08_17_221914_create_vendor_payments_table', 1),
(30, '2025_08_17_222748_create_vendor_bilties_table', 1),
(31, '2025_08_17_225451_create_customer_ledgers_table', 1),
(32, '2025_08_17_225912_create_customer_payments_table', 1),
(33, '2025_08_20_235830_create_table_sales', 1),
(34, '2025_08_22_122351_create_product_discounts_table', 1),
(35, '2025_08_22_213952_create_warehouse_stocks_table', 1),
(36, '2025_08_22_214859_create_stock_transfers_table', 1),
(37, '2025_08_28_223934_create_inward_gatepasses_table', 1),
(38, '2025_08_28_224111_create_inward_gatepass_items_table', 1),
(39, '2025_08_31_093412_create_product_bookings_table', 1),
(40, '2025_09_02_164836_create_purchase_returns_table', 1),
(41, '2025_09_02_164843_create_purchase_return_items_table', 1),
(42, '2025_09_10_181016_add_opening_balance_to_customer_ledgers_table', 1),
(43, '2025_09_13_012942_add_part_fields_to_products_table', 1),
(44, '2025_09_13_022223_create_product_boms_table', 1),
(45, '2025_09_13_022411_create_stock_movements_table', 1),
(46, '2025_09_13_055335_add_index_and_view_for_onhand', 1),
(47, '2025_11_09_135454_add_is_auto_pluck_to_stock_movements_table', 1),
(48, '2025_11_09_135841_add_ref_uuid_to_stock_movements_table', 1),
(49, '2025_12_19_072030_create_package_types_table', 1),
(50, '2025_12_22_190616_add_columns_to_products_table', 1),
(51, '2025_12_30_020855_create_sessions_table', 1),
(52, '2026_01_16_000000_create_modules_table', 1),
(53, '2026_01_17_000000_create_hr_module_tables', 1),
(54, '2026_01_17_000001_create_designations_table', 1),
(55, '2026_01_17_000002_add_details_to_hr_employees_table', 1),
(56, '2026_01_17_000003_create_hr_employee_documents_table', 1),
(57, '2026_01_17_000004_create_hr_salary_structures_table', 1),
(58, '2026_01_17_000005_add_commission_tiers_to_hr_salary_structures', 1),
(59, '2026_01_17_000006_create_hr_shifts_table', 1),
(60, '2026_01_17_000007_create_hr_holidays_table', 1),
(61, '2026_01_17_000008_update_hr_attendance_system', 1),
(62, '2026_01_17_000009_add_location_to_hr_attendances', 1),
(63, '2026_01_19_140000_change_employee_status_inactive_to_non_active', 1),
(64, '2026_01_19_150000_add_requires_location_to_designations', 1),
(65, '2026_01_19_163000_create_hr_loans_tables', 1),
(66, '2026_01_19_185007_add_face_encoding_to_hr_employees_table', 1),
(67, '2026_01_20_162642_create_biometric_devices_table', 1),
(68, '2026_01_20_162647_add_biometric_fields_to_hr_employees_table', 1),
(69, '2026_01_21_024920_create_hr_settings_table', 1),
(70, '2026_01_21_025224_add_punch_gap_minutes_to_hr_employees_table', 1),
(71, '2026_01_21_162017_add_early_in_to_hr_attendances_table', 1),
(72, '2026_01_22_000000_remove_parts_and_bom', 1),
(73, '2026_01_22_152000_add_name_to_modules_table', 1),
(74, '2026_01_22_204519_add_daily_wages_to_salary_structures_table', 1),
(75, '2026_01_22_211414_add_attendance_deduction_policy_to_salary_structures_table', 1),
(76, '2026_01_23_004309_enhance_hr_payrolls_table', 1),
(77, '2026_01_23_004533_create_hr_payroll_details_table', 1),
(78, '2026_01_23_004536_add_pending_deductions_to_hr_employees', 1),
(79, '2026_01_23_012507_add_carried_forward_to_next_to_hr_payrolls', 1),
(80, '2026_01_23_154045_create_employee_salary_structures_table', 1),
(81, '2026_01_23_164530_update_salary_structures_for_standalone_and_naming', 1),
(82, '2026_01_23_180926_add_is_custom_to_employee_salary_structures', 1),
(83, '2026_01_23_182108_add_parent_id_to_salary_structures', 1),
(84, '2026_01_23_230000_drop_basic_salary_from_employees', 1),
(85, '2026_01_24_161500_modify_products_table_for_m2_pricing', 1),
(86, '2026_01_24_165800_add_extended_size_mode_fields', 1),
(87, '2026_01_24_170900_add_purchase_fields', 1),
(88, '2026_01_25_000000_migrate_legacy_stocks_to_warehouse_stocks', 1),
(89, '2026_01_26_013600_rename_column_in_products_table', 1),
(90, '2026_01_26_183704_add_description_to_customer_ledgers_table', 1),
(91, '2026_01_27_000000_add_extra_columns_to_sales_table', 1),
(92, '2026_01_27_000001_add_loose_pieces_to_sales_table', 1),
(93, '2026_01_27_134746_add_stock_details_to_warehouse_stocks_table', 1),
(94, '2026_01_27_135604_remove_stock_columns_from_products_table', 1),
(95, '2026_01_27_135755_remove_price_from_warehouse_stocks_table', 1),
(96, '2026_01_27_141644_simplify_warehouse_stocks_table', 1),
(97, '2026_01_27_143933_add_pricing_columns_to_products_table', 1),
(98, '2026_01_27_144520_add_boxes_quantity_to_warehouse_stocks', 1),
(99, '2026_01_27_144857_rename_pieces_per_box_to_total_pieces_in_warehouse_stocks', 1),
(100, '2026_01_27_190000_update_sales_and_create_sale_items', 1),
(101, '2026_01_27_191000_align_sale_items_columns', 1),
(102, '2026_01_28_014000_make_legacy_sales_columns_nullable', 1),
(103, '2026_01_28_015500_drop_product_column_from_sales', 1),
(104, '2026_01_28_020000_drop_legacy_sales_columns', 1),
(105, '2026_01_28_030000_add_ids_to_sale_items', 1),
(106, '2026_01_28_040000_refactor_sales_table_final', 1),
(107, '2026_01_28_050000_reorder_sales_columns', 1),
(108, '2026_01_28_070000_add_balance_range_to_customers', 1),
(109, '2026_01_28_194600_add_previous_balance_to_customers', 1),
(110, '2026_01_30_000000_refactor_sale_status_and_items', 1),
(111, '2026_01_30_000001_create_financial_accounts_tables', 1),
(112, '2026_01_30_000002_create_voucher_tables', 1),
(113, '2026_01_31_000003_create_settings_table', 1),
(114, '2026_01_31_000004_create_erp_voucher_system', 1),
(115, '2026_01_31_000005_create_system_notifications_table', 1),
(116, '2026_01_31_030620_add_party_to_journal_entries', 1),
(117, '2026_01_31_205213_add_opening_balance_to_vendors_table', 1),
(118, '2026_02_01_011152_fix_sale_status_column_type', 1),
(119, '2026_02_02_010450_add_voucher_id_to_customer_payments_table', 1),
(120, '2026_02_02_013524_create_system_settings_table', 1),
(121, '2026_02_02_015156_add_can_approve_returns_to_users_table', 1),
(122, '2026_02_02_153600_add_pieces_per_m2_to_products', 1),
(123, '2026_02_02_175600_add_due_date_to_sales_table', 1),
(124, '2026_02_02_181246_create_notifications_table', 1),
(125, '2026_02_02_181921_add_credit_days_to_sales_table', 1),
(126, '2026_02_03_135824_change_total_m2_column_type_in_products_table', 1),
(127, '2026_02_04_184220_add_size_mode_to_sale_items_table', 1),
(128, '2026_02_05_040428_add_stock_fields_to_products_table', 1),
(129, '2026_02_05_042018_change_total_m2_decimal_precision', 1),
(130, '2026_02_06_170933_add_snapshot_columns_to_purchase_items_table', 1),
(131, '2026_02_07_133524_add_purchase_id_to_purchase_returns_table', 1),
(132, '2026_02_07_142845_create_sale_returns_table', 1),
(133, '2026_02_07_143037_create_sale_return_items_table', 1),
(134, '2026_02_07_143751_drop_old_sales_returns_table', 1),
(135, '2026_02_18_110000_add_sales_officer_id_to_customers_table', 1),
(136, '2026_02_22_173050_fix_purchase_items_columns', 1),
(137, '2026_02_24_022723_add_default_discounts_to_products_table', 1),
(138, '2026_02_28_040416_add_reminder_fields_to_customers_table', 1),
(139, '2026_03_06_214616_add_reminder_day_to_customers_table', 1),
(140, '2026_05_01_213524_add_is_active_to_products_table', 1),
(141, '2026_05_20_231443_add_alert_quantity_to_products_table', 1),
(142, '2026_05_21_043429_create_expense_categories_table', 1),
(143, '2026_05_21_043430_add_reference_no_to_expense_vouchers_table', 1),
(144, '2026_05_30_192619_add_alert_carton_quantity_to_products_table', 1),
(145, '2026_06_01_171926_add_is_booking_to_sales_table', 1),
(146, '2026_06_02_190558_add_additional_discount_to_purchases_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'home', NULL, NULL),
(2, 'profile', NULL, NULL),
(3, 'products', NULL, NULL),
(4, 'product.bookings', NULL, NULL),
(5, 'discount.products', NULL, NULL),
(6, 'categories', NULL, NULL),
(7, 'subcategories', NULL, NULL),
(8, 'brands', NULL, NULL),
(9, 'units', NULL, NULL),
(10, 'warehouse', NULL, NULL),
(11, 'warehouse.stock', NULL, NULL),
(12, 'stock.transfer', NULL, NULL),
(13, 'stock.adjust', NULL, NULL),
(14, 'stocks', NULL, NULL),
(15, 'purchases', NULL, NULL),
(16, 'purchase.returns', NULL, NULL),
(17, 'vendors', NULL, NULL),
(18, 'vendor.bilties', NULL, NULL),
(19, 'inward.gatepass', NULL, NULL),
(20, 'sales', NULL, NULL),
(21, 'sales.returns', NULL, NULL),
(22, 'customers', NULL, NULL),
(23, 'customer.ledger', NULL, NULL),
(24, 'bookings', NULL, NULL),
(25, 'checkbook', NULL, NULL),
(26, 'chart.of.accounts', NULL, NULL),
(27, 'expense.voucher', NULL, NULL),
(28, 'receipts.voucher', NULL, NULL),
(29, 'journal.voucher', NULL, NULL),
(30, 'payment.voucher', NULL, NULL),
(31, 'income.voucher', NULL, NULL),
(32, 'item.stock.report', NULL, NULL),
(33, 'purchase.report', NULL, NULL),
(34, 'sale.report', NULL, NULL),
(35, 'reporting', NULL, NULL),
(36, 'inventory.onhand', NULL, NULL),
(37, 'users', NULL, NULL),
(38, 'roles', NULL, NULL),
(39, 'permissions', NULL, NULL),
(40, 'branches', NULL, NULL),
(41, 'zones', NULL, NULL),
(42, 'sales.officers', NULL, NULL),
(43, 'narrations', NULL, NULL),
(44, 'package.types', NULL, NULL),
(45, 'hr.departments', NULL, NULL),
(46, 'hr.employees', NULL, NULL),
(47, 'hr.attendance', NULL, NULL),
(48, 'hr.payroll', NULL, NULL),
(49, 'hr.leaves', NULL, NULL),
(50, 'hr.designations', NULL, NULL),
(51, 'hr.shifts', NULL, NULL),
(52, 'hr.holidays', NULL, NULL),
(53, 'hr.salary.structure', NULL, NULL),
(54, 'hr.loans', NULL, NULL),
(55, 'hr.biometric.devices', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `narrations`
--

CREATE TABLE `narrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_head` varchar(255) NOT NULL,
  `narration` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_types`
--

CREATE TABLE `package_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `payment_vouchers`
--

CREATE TABLE `payment_vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pvid` varchar(255) DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `party_id` varchar(255) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `narration_id` text DEFAULT NULL,
  `reference_no` text DEFAULT NULL,
  `row_account_head` text DEFAULT NULL,
  `row_account_id` text DEFAULT NULL,
  `discount_value` text DEFAULT NULL,
  `kg` text DEFAULT NULL,
  `rate` text DEFAULT NULL,
  `amount` text DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'home.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(2, 'home.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(3, 'home.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(4, 'home.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(5, 'profile.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(6, 'profile.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(7, 'profile.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(8, 'profile.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(9, 'products.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(10, 'products.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(11, 'products.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(12, 'products.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(13, 'product.bookings.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(14, 'product.bookings.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(15, 'product.bookings.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(16, 'product.bookings.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(17, 'discount.products.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(18, 'discount.products.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(19, 'discount.products.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(20, 'discount.products.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(21, 'categories.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(22, 'categories.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(23, 'categories.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(24, 'categories.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(25, 'subcategories.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(26, 'subcategories.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(27, 'subcategories.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(28, 'subcategories.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(29, 'brands.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(30, 'brands.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(31, 'brands.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(32, 'brands.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(33, 'units.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(34, 'units.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(35, 'units.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(36, 'units.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(37, 'warehouse.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(38, 'warehouse.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(39, 'warehouse.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(40, 'warehouse.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(41, 'warehouse.stock.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(42, 'warehouse.stock.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(43, 'warehouse.stock.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(44, 'warehouse.stock.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(45, 'stock.transfer.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(46, 'stock.transfer.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(47, 'stock.transfer.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(48, 'stock.transfer.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(49, 'stock.adjust.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(50, 'stock.adjust.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(51, 'stock.adjust.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(52, 'stock.adjust.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(53, 'stocks.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(54, 'stocks.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(55, 'stocks.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(56, 'stocks.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(57, 'purchases.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(58, 'purchases.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(59, 'purchases.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(60, 'purchases.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(61, 'purchase.returns.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(62, 'purchase.returns.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(63, 'purchase.returns.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(64, 'purchase.returns.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(65, 'vendors.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(66, 'vendors.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(67, 'vendors.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(68, 'vendors.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(69, 'vendor.bilties.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(70, 'vendor.bilties.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(71, 'vendor.bilties.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(72, 'vendor.bilties.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(73, 'inward.gatepass.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(74, 'inward.gatepass.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(75, 'inward.gatepass.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(76, 'inward.gatepass.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(77, 'sales.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(78, 'sales.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(79, 'sales.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(80, 'sales.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(81, 'sales.returns.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(82, 'sales.returns.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(83, 'sales.returns.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(84, 'sales.returns.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(85, 'customers.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(86, 'customers.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(87, 'customers.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(88, 'customers.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(89, 'customer.ledger.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(90, 'customer.ledger.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(91, 'customer.ledger.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(92, 'customer.ledger.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(93, 'bookings.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(94, 'bookings.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(95, 'bookings.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(96, 'bookings.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(97, 'checkbook.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(98, 'checkbook.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(99, 'checkbook.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(100, 'checkbook.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(101, 'chart.of.accounts.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(102, 'chart.of.accounts.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(103, 'chart.of.accounts.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(104, 'chart.of.accounts.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(105, 'expense.voucher.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(106, 'expense.voucher.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(107, 'expense.voucher.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(108, 'expense.voucher.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(109, 'receipts.voucher.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(110, 'receipts.voucher.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(111, 'receipts.voucher.edit', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(112, 'receipts.voucher.delete', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(113, 'journal.voucher.view', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(114, 'journal.voucher.create', 'web', '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(115, 'journal.voucher.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(116, 'journal.voucher.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(117, 'payment.voucher.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(118, 'payment.voucher.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(119, 'payment.voucher.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(120, 'payment.voucher.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(121, 'income.voucher.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(122, 'income.voucher.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(123, 'income.voucher.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(124, 'income.voucher.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(125, 'item.stock.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(126, 'item.stock.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(127, 'item.stock.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(128, 'item.stock.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(129, 'purchase.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(130, 'purchase.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(131, 'purchase.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(132, 'purchase.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(133, 'sale.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(134, 'sale.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(135, 'sale.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(136, 'sale.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(137, 'reporting.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(138, 'reporting.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(139, 'reporting.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(140, 'reporting.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(141, 'recovery.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(142, 'recovery.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(143, 'recovery.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(144, 'recovery.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(145, 'payable.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(146, 'payable.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(147, 'payable.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(148, 'payable.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(149, 'parties.balance.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(150, 'parties.balance.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(151, 'parties.balance.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(152, 'parties.balance.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(153, 'aging.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(154, 'aging.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(155, 'aging.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(156, 'aging.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(157, 'balance.sheet.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(158, 'balance.sheet.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(159, 'balance.sheet.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(160, 'balance.sheet.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(161, 'profit.loss.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(162, 'profit.loss.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(163, 'profit.loss.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(164, 'profit.loss.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(165, 'inventory.onhand.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(166, 'inventory.onhand.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(167, 'inventory.onhand.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(168, 'inventory.onhand.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(169, 'vendor.ledger.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(170, 'vendor.ledger.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(171, 'vendor.ledger.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(172, 'vendor.ledger.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(173, 'users.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(174, 'users.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(175, 'users.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(176, 'users.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(177, 'roles.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(178, 'roles.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(179, 'roles.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(180, 'roles.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(181, 'permissions.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(182, 'permissions.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(183, 'permissions.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(184, 'permissions.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(185, 'branches.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(186, 'branches.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(187, 'branches.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(188, 'branches.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(189, 'zones.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(190, 'zones.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(191, 'zones.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(192, 'zones.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(193, 'sales.officers.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(194, 'sales.officers.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(195, 'sales.officers.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(196, 'sales.officers.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(197, 'narrations.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(198, 'narrations.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(199, 'narrations.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(200, 'narrations.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(201, 'executive.report.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(202, 'executive.report.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(203, 'executive.report.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(204, 'executive.report.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(205, 'package.types.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(206, 'package.types.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(207, 'package.types.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(208, 'package.types.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(209, 'hr.departments.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(210, 'hr.departments.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(211, 'hr.departments.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(212, 'hr.departments.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(213, 'hr.employees.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(214, 'hr.employees.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(215, 'hr.employees.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(216, 'hr.employees.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(217, 'hr.attendance.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(218, 'hr.attendance.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(219, 'hr.attendance.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(220, 'hr.attendance.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(221, 'hr.payroll.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(222, 'hr.payroll.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(223, 'hr.payroll.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(224, 'hr.payroll.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(225, 'hr.leaves.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(226, 'hr.leaves.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(227, 'hr.leaves.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(228, 'hr.leaves.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(229, 'hr.designations.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(230, 'hr.designations.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(231, 'hr.designations.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(232, 'hr.designations.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(233, 'hr.shifts.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(234, 'hr.shifts.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(235, 'hr.shifts.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(236, 'hr.shifts.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(237, 'hr.holidays.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(238, 'hr.holidays.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(239, 'hr.holidays.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(240, 'hr.holidays.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(241, 'hr.salary.structure.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(242, 'hr.salary.structure.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(243, 'hr.salary.structure.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(244, 'hr.salary.structure.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(245, 'hr.loans.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(246, 'hr.loans.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(247, 'hr.loans.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(248, 'hr.loans.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(249, 'hr.biometric.devices.view', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(250, 'hr.biometric.devices.create', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(251, 'hr.biometric.devices.edit', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(252, 'hr.biometric.devices.delete', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35');

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
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `creater_id` text DEFAULT NULL,
  `category_id` text DEFAULT NULL,
  `sub_category_id` text DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_part` tinyint(1) NOT NULL DEFAULT 0,
  `is_assembled` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `item_code` text DEFAULT NULL,
  `unit_id` text DEFAULT NULL,
  `item_name` text DEFAULT NULL,
  `size_mode` varchar(255) NOT NULL DEFAULT 'by_size',
  `height` decimal(8,2) DEFAULT NULL COMMENT 'Height in cm',
  `width` decimal(8,2) DEFAULT NULL COMMENT 'Width in cm',
  `pieces_per_box` int(11) NOT NULL DEFAULT 0,
  `pieces_per_m2` decimal(10,2) NOT NULL DEFAULT 0.00,
  `alert_quantity` int(11) DEFAULT NULL,
  `alert_carton_quantity` int(11) DEFAULT NULL,
  `total_m2` decimal(10,2) NOT NULL,
  `price_per_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sale_price_per_box` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Used for By Cartons and By Pieces',
  `purchase_price_per_piece` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Used for By Cartons and By Pieces',
  `purchase_price_per_box` decimal(15,2) DEFAULT 0.00,
  `sale_price_per_piece` decimal(15,2) DEFAULT 0.00,
  `purchase_price_per_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `color` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `barcode_path` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `hs_code` varchar(255) DEFAULT NULL,
  `boxes_quantity` int(11) DEFAULT 0,
  `loose_pieces` int(11) DEFAULT 0,
  `piece_quantity` int(11) DEFAULT 0,
  `total_stock_qty` decimal(10,2) DEFAULT 0.00,
  `purchase_discount_percent` decimal(8,2) NOT NULL DEFAULT 0.00,
  `sale_discount_percent` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `creater_id`, `category_id`, `sub_category_id`, `brand_id`, `is_part`, `is_assembled`, `is_active`, `item_code`, `unit_id`, `item_name`, `size_mode`, `height`, `width`, `pieces_per_box`, `pieces_per_m2`, `alert_quantity`, `alert_carton_quantity`, `total_m2`, `price_per_m2`, `sale_price_per_box`, `purchase_price_per_piece`, `purchase_price_per_box`, `sale_price_per_piece`, `purchase_price_per_m2`, `color`, `created_at`, `updated_at`, `deleted_at`, `barcode_path`, `image`, `model`, `hs_code`, `boxes_quantity`, `loose_pieces`, `piece_quantity`, `total_stock_qty`, `purchase_discount_percent`, `sale_discount_percent`) VALUES
(1, '1', '1', '5', 1, 0, 0, 1, 'ITEM-0001', '1', 'Formal Shirt', 'by_cartons', 0.00, 0.00, 12, 0.00, NULL, NULL, 0.00, 0.00, 5000.00, 375.00, 4500.00, 416.67, 0.00, '[\"Black\"]', '2026-06-08 18:19:34', '2026-06-08 18:19:34', NULL, '573895997126', NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `product_bookings`
--

CREATE TABLE `product_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer` text NOT NULL,
  `reference` text DEFAULT NULL,
  `product` text NOT NULL,
  `product_code` text NOT NULL,
  `brand` text NOT NULL,
  `unit` text NOT NULL,
  `per_price` text NOT NULL,
  `per_discount` text NOT NULL,
  `qty` text NOT NULL,
  `per_total` text NOT NULL,
  `color` text NOT NULL,
  `total_amount_Words` text NOT NULL,
  `total_bill_amount` text NOT NULL,
  `total_extradiscount` text NOT NULL,
  `total_net` text NOT NULL,
  `cash` text NOT NULL,
  `card` text NOT NULL,
  `change` text NOT NULL,
  `total_items` text NOT NULL,
  `booking_date` text NOT NULL,
  `sale_date` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_discounts`
--

CREATE TABLE `product_discounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `actual_price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `final_price` decimal(10,2) NOT NULL,
  `total_discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `date` date NOT NULL DEFAULT '2026-06-08',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `additional_discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `extra_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status_purchase` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `size_mode` varchar(255) DEFAULT NULL,
  `pieces_per_box` decimal(12,2) NOT NULL DEFAULT 1.00,
  `pieces_per_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `boxes_qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `loose_qty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `length` varchar(255) DEFAULT NULL,
  `width` varchar(255) DEFAULT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `item_discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `qty` int(11) NOT NULL DEFAULT 0,
  `line_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_returns`
--

CREATE TABLE `purchase_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `return_invoice` varchar(255) NOT NULL,
  `return_date` date NOT NULL,
  `return_reason` text DEFAULT NULL,
  `transport` varchar(255) DEFAULT NULL,
  `vehicle_no` varchar(255) DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `delivery_person` varchar(255) DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bill_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `item_discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `extra_discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_return_items`
--

CREATE TABLE `purchase_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_return_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `unit` varchar(255) DEFAULT NULL,
  `qty` int(11) NOT NULL DEFAULT 0,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `item_discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `line_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receipts_vouchers`
--

CREATE TABLE `receipts_vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rvid` varchar(255) DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `party_id` varchar(255) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `narration_id` text DEFAULT NULL,
  `reference_no` text DEFAULT NULL,
  `row_account_head` text DEFAULT NULL,
  `row_account_id` text DEFAULT NULL,
  `discount_value` text DEFAULT NULL,
  `kg` text DEFAULT NULL,
  `rate` text DEFAULT NULL,
  `amount` text DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `processed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'web', '2026-06-08 18:19:35', '2026-06-08 18:19:35'),
(2, 'branch', 'web', '2026-06-08 18:20:21', '2026-06-08 18:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(15, 2),
(16, 1),
(16, 2),
(17, 1),
(17, 2),
(18, 1),
(18, 2),
(19, 1),
(19, 2),
(20, 1),
(20, 2),
(21, 1),
(21, 2),
(22, 1),
(22, 2),
(23, 1),
(23, 2),
(24, 1),
(24, 2),
(25, 1),
(25, 2),
(26, 1),
(26, 2),
(27, 1),
(27, 2),
(28, 1),
(28, 2),
(29, 1),
(29, 2),
(30, 1),
(30, 2),
(31, 1),
(31, 2),
(32, 1),
(32, 2),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 2),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(53, 2),
(54, 1),
(54, 2),
(55, 1),
(55, 2),
(56, 1),
(56, 2),
(57, 1),
(57, 2),
(58, 1),
(58, 2),
(59, 1),
(59, 2),
(60, 1),
(60, 2),
(61, 1),
(61, 2),
(62, 1),
(62, 2),
(63, 1),
(63, 2),
(64, 1),
(64, 2),
(65, 1),
(65, 2),
(66, 1),
(66, 2),
(67, 1),
(67, 2),
(68, 1),
(68, 2),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(77, 2),
(78, 1),
(78, 2),
(79, 1),
(79, 2),
(80, 1),
(80, 2),
(81, 1),
(81, 2),
(82, 1),
(82, 2),
(83, 1),
(83, 2),
(84, 1),
(84, 2),
(85, 1),
(85, 2),
(86, 1),
(86, 2),
(87, 1),
(87, 2),
(88, 1),
(88, 2),
(89, 1),
(89, 2),
(90, 1),
(90, 2),
(91, 1),
(91, 2),
(92, 1),
(92, 2),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(97, 2),
(98, 1),
(98, 2),
(99, 1),
(99, 2),
(100, 1),
(100, 2),
(101, 1),
(101, 2),
(102, 1),
(102, 2),
(103, 1),
(103, 2),
(104, 1),
(104, 2),
(105, 1),
(105, 2),
(106, 1),
(106, 2),
(107, 1),
(107, 2),
(108, 1),
(108, 2),
(109, 1),
(109, 2),
(110, 1),
(110, 2),
(111, 1),
(111, 2),
(112, 1),
(112, 2),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(117, 2),
(118, 1),
(118, 2),
(119, 1),
(119, 2),
(120, 1),
(120, 2),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(125, 2),
(126, 1),
(126, 2),
(127, 1),
(127, 2),
(128, 1),
(128, 2),
(129, 1),
(129, 2),
(130, 1),
(130, 2),
(131, 1),
(131, 2),
(132, 1),
(132, 2),
(133, 1),
(133, 2),
(134, 1),
(134, 2),
(135, 1),
(135, 2),
(136, 1),
(136, 2),
(137, 1),
(138, 1),
(139, 1),
(140, 1),
(141, 1),
(141, 2),
(142, 1),
(142, 2),
(143, 1),
(143, 2),
(144, 1),
(144, 2),
(145, 1),
(145, 2),
(146, 1),
(146, 2),
(147, 1),
(147, 2),
(148, 1),
(148, 2),
(149, 1),
(149, 2),
(150, 1),
(150, 2),
(151, 1),
(151, 2),
(152, 1),
(152, 2),
(153, 1),
(153, 2),
(154, 1),
(154, 2),
(155, 1),
(155, 2),
(156, 1),
(156, 2),
(157, 1),
(157, 2),
(158, 1),
(158, 2),
(159, 1),
(159, 2),
(160, 1),
(160, 2),
(161, 1),
(161, 2),
(162, 1),
(162, 2),
(163, 1),
(163, 2),
(164, 1),
(164, 2),
(165, 1),
(166, 1),
(167, 1),
(168, 1),
(169, 1),
(169, 2),
(170, 1),
(170, 2),
(171, 1),
(171, 2),
(172, 1),
(172, 2),
(173, 1),
(174, 1),
(175, 1),
(176, 1),
(177, 1),
(178, 1),
(179, 1),
(180, 1),
(181, 1),
(182, 1),
(183, 1),
(184, 1),
(185, 1),
(186, 1),
(187, 1),
(188, 1),
(189, 1),
(189, 2),
(190, 1),
(190, 2),
(191, 1),
(191, 2),
(192, 1),
(192, 2),
(193, 1),
(194, 1),
(195, 1),
(196, 1),
(197, 1),
(198, 1),
(199, 1),
(200, 1),
(201, 1),
(201, 2),
(202, 1),
(202, 2),
(203, 1),
(203, 2),
(204, 1),
(204, 2),
(205, 1),
(206, 1),
(207, 1),
(208, 1),
(209, 1),
(210, 1),
(211, 1),
(212, 1),
(213, 1),
(214, 1),
(215, 1),
(216, 1),
(217, 1),
(218, 1),
(219, 1),
(220, 1),
(221, 1),
(222, 1),
(223, 1),
(224, 1),
(225, 1),
(226, 1),
(227, 1),
(228, 1),
(229, 1),
(230, 1),
(231, 1),
(232, 1),
(233, 1),
(234, 1),
(235, 1),
(236, 1),
(237, 1),
(238, 1),
(239, 1),
(240, 1),
(241, 1),
(242, 1),
(243, 1),
(244, 1),
(245, 1),
(246, 1),
(247, 1),
(248, 1),
(249, 1),
(250, 1),
(251, 1),
(252, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_no` varchar(255) DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `sale_status` enum('booked','posted','cancelled','returned') NOT NULL DEFAULT 'booked',
  `is_booking` tinyint(1) NOT NULL DEFAULT 0,
  `credit_days` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `total_items` decimal(12,2) DEFAULT NULL,
  `total_bill_amount` decimal(12,2) DEFAULT NULL,
  `total_extradiscount` decimal(12,2) DEFAULT NULL,
  `total_net` decimal(12,2) DEFAULT NULL,
  `cash` decimal(12,2) DEFAULT NULL,
  `card` decimal(12,2) DEFAULT NULL,
  `change` decimal(12,2) DEFAULT NULL,
  `total_amount_Words` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales_officers`
--

CREATE TABLE `sales_officers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `name_urdu` text DEFAULT NULL,
  `mobile` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `size_mode` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `brand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sub_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `color` text DEFAULT NULL,
  `stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `price_level` decimal(12,2) NOT NULL DEFAULT 0.00,
  `price` decimal(12,2) DEFAULT 0.00,
  `price_per_piece` decimal(12,2) NOT NULL DEFAULT 0.00,
  `price_per_m2` decimal(12,2) NOT NULL DEFAULT 0.00,
  `qty` decimal(12,2) DEFAULT 0.00,
  `total_pieces` int(11) NOT NULL DEFAULT 0,
  `loose_pieces` int(11) NOT NULL DEFAULT 0,
  `retail_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_returns`
--

CREATE TABLE `sale_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `return_invoice` varchar(255) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `return_date` date NOT NULL,
  `bill_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `item_discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `extra_discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `net_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid` decimal(15,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'posted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_return_items`
--

CREATE TABLE `sale_return_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_return_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `qty` decimal(15,2) NOT NULL COMMENT 'Total pieces returned',
  `boxes` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Box quantity (can be decimal like 1.2)',
  `loose_pieces` int(11) NOT NULL DEFAULT 0 COMMENT 'Loose pieces',
  `price` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Price per piece',
  `item_discount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `unit` varchar(255) NOT NULL DEFAULT 'pc',
  `line_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('TnzxCOh0NiKS14Jyc3UhNVHSnVhjr7Mp2UHX9MW2', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQnNzOUZEVzBTZklCVENYNTJLVEU5QURWVTd0MWFMZU5rOURVMGVncyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGVja2Jvb2siO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1780943196);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `label` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'company_name', 'Three Stars Medical', 'string', 'company', 'Company Name', 'Official company name displayed on invoices and reports', '2026-06-08 18:19:31', '2026-06-08 18:19:31'),
(2, 'company_address', '', 'text', 'company', 'Company Address', 'Full company address', '2026-06-08 18:19:31', '2026-06-08 18:19:31'),
(3, 'company_phone', '', 'string', 'company', 'Phone Number', 'Primary contact number', '2026-06-08 18:19:31', '2026-06-08 18:19:31'),
(4, 'currency_symbol', 'PKR', 'string', 'company', 'Currency Symbol', 'Currency used in the system', '2026-06-08 18:19:31', '2026-06-08 18:19:31'),
(5, 'debt_warning_days', '7', 'integer', 'sales', 'Debt Warning Days', 'Number of days after which a warning notification is sent for unpaid invoices', '2026-06-08 18:19:31', '2026-06-08 18:19:31'),
(6, 'debt_critical_days', '10', 'integer', 'sales', 'Debt Critical Days', 'Number of days after which a critical notification is sent for unpaid invoices', '2026-06-08 18:19:31', '2026-06-08 18:19:31'),
(7, 'invoice_terms', 'Payment due within 30 days. Late payments may incur additional charges.', 'text', 'sales', 'Invoice Terms & Conditions', 'Default terms and conditions displayed on invoices', '2026-06-08 18:19:31', '2026-06-08 18:19:31'),
(8, 'low_stock_threshold', '10', 'integer', 'inventory', 'Low Stock Threshold', 'Minimum quantity before low stock warning', '2026-06-08 18:19:31', '2026-06-08 18:19:31'),
(9, 'expiry_alert_days', '30', 'integer', 'inventory', 'Expiry Alert Days', 'Number of days before expiry to show warning', '2026-06-08 18:19:31', '2026-06-08 18:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 0,
  `reserved_qty` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('in','out','assembly_in','assembly_out','adjustment') NOT NULL,
  `qty` decimal(12,3) NOT NULL,
  `ref_type` varchar(255) DEFAULT NULL,
  `ref_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ref_uuid` varchar(255) DEFAULT NULL,
  `is_auto_pluck` tinyint(4) NOT NULL DEFAULT 0,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `to_warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_shop` tinyint(1) NOT NULL DEFAULT 0,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Fan', 1, '2026-06-08 18:19:33', '2026-06-08 18:19:33'),
(2, 'ceiling  Fan', 1, '2026-06-08 18:19:33', '2026-06-08 18:19:33'),
(3, 'Pedestal  Fan', 1, '2026-06-08 18:19:33', '2026-06-08 18:19:33'),
(4, 'Fridge', 1, '2026-06-08 18:19:33', '2026-06-08 18:19:33'),
(5, 'Air-Condition(AC)', 1, '2026-06-08 18:19:33', '2026-06-08 18:19:33'),
(6, 'Washing Machine', 1, '2026-06-08 18:19:33', '2026-06-08 18:19:33'),
(7, 'Microwave Oven', 1, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(8, 'Drill Machine', 2, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(9, 'Grinder', 2, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(10, 'Lathe Machine', 2, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(11, 'Milling Machine', 2, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(12, 'Shaper Machine', 2, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(13, 'Hammer', 3, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(14, 'Screwdriver', 3, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(15, 'Wrench', 3, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(16, 'Pliers', 3, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(17, 'Tape Measure', 3, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(18, 'Pipe', 4, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(19, 'Faucet', 4, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(20, 'Valve', 4, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(21, 'Toilet', 4, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(22, 'Sink', 4, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(23, 'Nails', 5, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(24, 'Screws', 5, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(25, 'Bolts', 5, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(26, 'Hinges', 5, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(27, 'Brackets', 5, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(28, 'Light', 6, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(29, 'Switch', 6, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(30, 'Wire', 6, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(31, 'Cable', 6, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(32, 'Engine Oil', 7, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(33, 'Brake Pads', 7, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(34, 'Tires', 7, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(35, 'Batteries', 7, '2026-06-08 18:19:34', '2026-06-08 18:19:34'),
(36, 'Filters', 7, '2026-06-08 18:19:34', '2026-06-08 18:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `system_notifications`
--

CREATE TABLE `system_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'info',
  `source_id` bigint(20) UNSIGNED DEFAULT NULL,
  `source_type` varchar(255) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `label` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'return_deadline_days', '30', 'integer', 'returns', 'Return Deadline (Days)', 'Number of days customers have to return items after purchase. Set to 0 to disable returns.', '2026-06-08 18:19:32', '2026-06-08 18:19:32'),
(2, 'return_require_approval', '1', 'boolean', 'returns', 'Require Manager Approval', 'If enabled, all returns must be approved by a manager before processing.', '2026-06-08 18:19:32', '2026-06-08 18:19:32'),
(3, 'return_auto_approve_threshold', '0', 'integer', 'returns', 'Auto-Approve Threshold', 'Returns under this amount will be auto-approved. Set to 0 to disable auto-approval.', '2026-06-08 18:19:32', '2026-06-08 18:19:32');

-- --------------------------------------------------------

--
-- Table structure for table `transports`
--

CREATE TABLE `transports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_or_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_ur` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `address_ur` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Piece', '2026-06-08 18:19:34', '2026-06-08 18:19:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `can_approve_returns` tinyint(1) NOT NULL DEFAULT 0,
  `can_approve_past_deadline_returns` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `usertype` varchar(255) NOT NULL DEFAULT 'admin',
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `can_approve_returns`, `can_approve_past_deadline_returns`, `email_verified_at`, `usertype`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@example.com', 0, 0, NULL, 'admin', '$2y$12$I.AM3g8roZ75YA0of/QE2.zCaZaUW91w8oARu19BFsEP17zmrfmfS', NULL, '2026-06-08 18:19:36', '2026-06-08 18:19:36'),
(2, 'Atif', 'admin@admin.com', 0, 0, NULL, 'admin', '$2y$12$JXpjApzWkdPySn.SPw/v1.TL01PusX9TmZWj29j9ZIeJa1NYhN0FC', NULL, '2026-06-08 18:24:41', '2026-06-08 18:24:41');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `opening_balance` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bilties`
--

CREATE TABLE `vendor_bilties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bilty_no` varchar(255) DEFAULT NULL,
  `vehicle_no` varchar(255) DEFAULT NULL,
  `transporter_name` varchar(255) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_ledgers`
--

CREATE TABLE `vendor_ledgers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_or_user_id` text NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `opening_balance` text NOT NULL,
  `previous_balance` text NOT NULL,
  `closing_balance` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_payments`
--

CREATE TABLE `vendor_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vendor_id` bigint(20) UNSIGNED NOT NULL,
  `admin_or_user_id` bigint(20) UNSIGNED NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `voucher_type` varchar(255) NOT NULL,
  `sales_officer` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(255) NOT NULL,
  `person` varchar(255) NOT NULL,
  `sub_head` varchar(255) NOT NULL,
  `narration` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` enum('draft','posted') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher_details`
--

CREATE TABLE `voucher_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `voucher_master_id` bigint(20) UNSIGNED NOT NULL,
  `account_id` bigint(20) UNSIGNED NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `narration` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher_masters`
--

CREATE TABLE `voucher_masters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `voucher_type` enum('receipt','payment','expense','journal','contra') NOT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `voucher_no` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `fiscal_year` varchar(255) DEFAULT NULL,
  `party_type` varchar(255) DEFAULT NULL,
  `party_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_stock_onhand`
-- (See below for the actual view)
--
CREATE TABLE `v_stock_onhand` (
`product_id` bigint(20) unsigned
,`onhand_qty` decimal(34,3)
);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `warehouse_name` varchar(255) DEFAULT NULL,
  `creater_id` bigint(20) UNSIGNED DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warehouses`
--

INSERT INTO `warehouses` (`id`, `branch_id`, `warehouse_name`, `creater_id`, `location`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Main Store', 1, 'Karachi', 'Main stock storage', '2026-06-08 18:19:34', '2026-06-08 18:19:34', NULL),
(2, 1, 'Branch A', 1, 'Lahore', 'North region store', '2026-06-08 18:19:34', '2026-06-08 18:19:34', NULL),
(3, 1, 'Branch B', 1, 'Islamabad', 'Capital branch', '2026-06-08 18:19:34', '2026-06-08 18:19:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_stocks`
--

CREATE TABLE `warehouse_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `total_pieces` int(11) DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `zone` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `v_stock_onhand`
--
DROP TABLE IF EXISTS `v_stock_onhand`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_stock_onhand`  AS SELECT `stock_movements`.`product_id` AS `product_id`, round(coalesce(sum(case when `stock_movements`.`type` in ('in','assembly_in') then abs(`stock_movements`.`qty`) when `stock_movements`.`type` in ('out','assembly_out') then -abs(`stock_movements`.`qty`) when `stock_movements`.`type` = 'adjustment' then `stock_movements`.`qty` else 0 end),0),3) AS `onhand_qty` FROM `stock_movements` GROUP BY `stock_movements`.`product_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accounts_head_id_foreign` (`head_id`);

--
-- Indexes for table `account_heads`
--
ALTER TABLE `account_heads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_heads_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `biometric_devices_is_active_index` (`is_active`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_user_id_unique` (`user_id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_customer_id_unique` (`customer_id`);

--
-- Indexes for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_ledgers_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_payments_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_salary_structures_assigned_by_foreign` (`assigned_by`),
  ADD KEY `employee_salary_structures_employee_id_is_active_index` (`employee_id`,`is_active`),
  ADD KEY `employee_salary_structures_salary_structure_id_is_active_index` (`salary_structure_id`,`is_active`),
  ADD KEY `employee_salary_structures_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_vouchers`
--
ALTER TABLE `expense_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_attendances_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_departments`
--
ALTER TABLE `hr_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hr_designations`
--
ALTER TABLE `hr_designations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_designations_name_unique` (`name`);

--
-- Indexes for table `hr_employees`
--
ALTER TABLE `hr_employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_employees_email_unique` (`email`),
  ADD KEY `hr_employees_user_id_foreign` (`user_id`),
  ADD KEY `hr_employees_department_id_foreign` (`department_id`),
  ADD KEY `hr_employees_designation_id_foreign` (`designation_id`),
  ADD KEY `hr_employees_shift_id_foreign` (`shift_id`),
  ADD KEY `hr_employees_biometric_device_id_foreign` (`biometric_device_id`),
  ADD KEY `hr_employees_device_user_id_index` (`device_user_id`);

--
-- Indexes for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_employee_documents_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_holidays`
--
ALTER TABLE `hr_holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_holidays_date_unique` (`date`);

--
-- Indexes for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_leaves_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_loans`
--
ALTER TABLE `hr_loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loans_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loan_payments_loan_id_foreign` (`loan_id`);

--
-- Indexes for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_loan_scheduled_deductions_loan_id_foreign` (`loan_id`);

--
-- Indexes for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_payrolls_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_payrolls_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_payroll_details_payroll_id_foreign` (`payroll_id`);

--
-- Indexes for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_salary_structures_employee_id_index` (`employee_id`),
  ADD KEY `hr_salary_structures_parent_structure_id_foreign` (`parent_structure_id`);

--
-- Indexes for table `hr_settings`
--
ALTER TABLE `hr_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_settings_key_unique` (`key`);

--
-- Indexes for table `hr_shifts`
--
ALTER TABLE `hr_shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inward_gatepasses`
--
ALTER TABLE `inward_gatepasses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inward_gatepass_items_inward_gatepass_id_foreign` (`inward_gatepass_id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entries_source_type_source_id_index` (`source_type`,`source_id`),
  ADD KEY `journal_entries_account_id_entry_date_index` (`account_id`,`entry_date`),
  ADD KEY `journal_entries_entry_date_index` (`entry_date`),
  ADD KEY `journal_entries_party_type_party_id_index` (`party_type`,`party_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modules_name_unique` (`name`);

--
-- Indexes for table `narrations`
--
ALTER TABLE `narrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `package_types`
--
ALTER TABLE `package_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_types_name_unique` (`name`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `product_bookings`
--
ALTER TABLE `product_bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_discounts`
--
ALTER TABLE `product_discounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_discounts_product_id_foreign` (`product_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_items_product_id_index` (`product_id`);

--
-- Indexes for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_returns_return_invoice_unique` (`return_invoice`),
  ADD KEY `purchase_returns_vendor_id_foreign` (`vendor_id`),
  ADD KEY `purchase_returns_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `purchase_returns_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  ADD KEY `purchase_return_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `receipts_vouchers`
--
ALTER TABLE `receipts_vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_invoice_no_unique` (`invoice_no`),
  ADD KEY `sales_due_date_index` (`due_date`);

--
-- Indexes for table `sales_officers`
--
ALTER TABLE `sales_officers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`);

--
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_returns_return_invoice_unique` (`return_invoice`),
  ADD KEY `sale_returns_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_returns_customer_id_foreign` (`customer_id`),
  ADD KEY `sale_returns_warehouse_id_foreign` (`warehouse_id`);

--
-- Indexes for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_return_items_sale_return_id_foreign` (`sale_return_id`),
  ADD KEY `sale_return_items_product_id_foreign` (`product_id`),
  ADD KEY `sale_return_items_warehouse_id_foreign` (`warehouse_id`);

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
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stocks_unique_triplet` (`branch_id`,`warehouse_id`,`product_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sm_product_created` (`product_id`,`created_at`),
  ADD KEY `stock_movements_ref_uuid_index` (`ref_uuid`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
  ADD KEY `stock_transfers_to_warehouse_id_foreign` (`to_warehouse_id`),
  ADD KEY `stock_transfers_product_id_foreign` (`product_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategories_category_id_foreign` (`category_id`);

--
-- Indexes for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `system_notifications_type_created_at_index` (`type`,`created_at`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- Indexes for table `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_bilties_vendor_id_foreign` (`vendor_id`),
  ADD KEY `vendor_bilties_purchase_id_foreign` (`purchase_id`);

--
-- Indexes for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_ledgers_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_payments_vendor_id_foreign` (`vendor_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `voucher_details`
--
ALTER TABLE `voucher_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voucher_details_voucher_master_id_foreign` (`voucher_master_id`),
  ADD KEY `voucher_details_account_id_foreign` (`account_id`);

--
-- Indexes for table `voucher_masters`
--
ALTER TABLE `voucher_masters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `voucher_masters_voucher_no_unique` (`voucher_no`),
  ADD KEY `voucher_masters_party_type_party_id_index` (`party_type`,`party_id`),
  ADD KEY `voucher_masters_voucher_type_index` (`voucher_type`),
  ADD KEY `voucher_masters_status_index` (`status`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouses_creater_id_index` (`creater_id`);

--
-- Indexes for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `warehouse_stocks_warehouse_id_foreign` (`warehouse_id`),
  ADD KEY `warehouse_stocks_product_id_foreign` (`product_id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_heads`
--
ALTER TABLE `account_heads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `biometric_devices`
--
ALTER TABLE `biometric_devices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_vouchers`
--
ALTER TABLE `expense_vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_departments`
--
ALTER TABLE `hr_departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_designations`
--
ALTER TABLE `hr_designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_employees`
--
ALTER TABLE `hr_employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_holidays`
--
ALTER TABLE `hr_holidays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loans`
--
ALTER TABLE `hr_loans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_settings`
--
ALTER TABLE `hr_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `hr_shifts`
--
ALTER TABLE `hr_shifts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inward_gatepasses`
--
ALTER TABLE `inward_gatepasses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `narrations`
--
ALTER TABLE `narrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package_types`
--
ALTER TABLE `package_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_vouchers`
--
ALTER TABLE `payment_vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `product_bookings`
--
ALTER TABLE `product_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_discounts`
--
ALTER TABLE `product_discounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receipts_vouchers`
--
ALTER TABLE `receipts_vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales_officers`
--
ALTER TABLE `sales_officers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `system_notifications`
--
ALTER TABLE `system_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transports`
--
ALTER TABLE `transports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher_details`
--
ALTER TABLE `voucher_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher_masters`
--
ALTER TABLE `voucher_masters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_head_id_foreign` FOREIGN KEY (`head_id`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `account_heads`
--
ALTER TABLE `account_heads`
  ADD CONSTRAINT `account_heads_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `account_heads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_ledgers`
--
ALTER TABLE `customer_ledgers`
  ADD CONSTRAINT `customer_ledgers_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_salary_structures`
--
ALTER TABLE `employee_salary_structures`
  ADD CONSTRAINT `employee_salary_structures_assigned_by_foreign` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `employee_salary_structures_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_salary_structures_salary_structure_id_foreign` FOREIGN KEY (`salary_structure_id`) REFERENCES `hr_salary_structures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_salary_structures_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_attendances`
--
ALTER TABLE `hr_attendances`
  ADD CONSTRAINT `hr_attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_employees`
--
ALTER TABLE `hr_employees`
  ADD CONSTRAINT `hr_employees_biometric_device_id_foreign` FOREIGN KEY (`biometric_device_id`) REFERENCES `biometric_devices` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `hr_departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_employees_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `hr_designations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_employees_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `hr_shifts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_employee_documents`
--
ALTER TABLE `hr_employee_documents`
  ADD CONSTRAINT `hr_employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_leaves`
--
ALTER TABLE `hr_leaves`
  ADD CONSTRAINT `hr_leaves_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loans`
--
ALTER TABLE `hr_loans`
  ADD CONSTRAINT `hr_loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loan_payments`
--
ALTER TABLE `hr_loan_payments`
  ADD CONSTRAINT `hr_loan_payments_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `hr_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_loan_scheduled_deductions`
--
ALTER TABLE `hr_loan_scheduled_deductions`
  ADD CONSTRAINT `hr_loan_scheduled_deductions_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `hr_loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_payrolls`
--
ALTER TABLE `hr_payrolls`
  ADD CONSTRAINT `hr_payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_payrolls_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_payroll_details`
--
ALTER TABLE `hr_payroll_details`
  ADD CONSTRAINT `hr_payroll_details_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `hr_payrolls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_salary_structures`
--
ALTER TABLE `hr_salary_structures`
  ADD CONSTRAINT `hr_salary_structures_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `hr_employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_salary_structures_parent_structure_id_foreign` FOREIGN KEY (`parent_structure_id`) REFERENCES `hr_salary_structures` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `inward_gatepass_items`
--
ALTER TABLE `inward_gatepass_items`
  ADD CONSTRAINT `inward_gatepass_items_inward_gatepass_id_foreign` FOREIGN KEY (`inward_gatepass_id`) REFERENCES `inward_gatepasses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `journal_entries_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_discounts`
--
ALTER TABLE `product_discounts`
  ADD CONSTRAINT `product_discounts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_returns`
--
ALTER TABLE `purchase_returns`
  ADD CONSTRAINT `purchase_returns_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchase_returns_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_return_items`
--
ALTER TABLE `purchase_return_items`
  ADD CONSTRAINT `purchase_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD CONSTRAINT `sale_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_returns_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sale_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_return_items`
--
ALTER TABLE `sale_return_items`
  ADD CONSTRAINT `sale_return_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_return_items_sale_return_id_foreign` FOREIGN KEY (`sale_return_id`) REFERENCES `sale_returns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_return_items_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `stock_transfers_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_transfers_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `system_notifications`
--
ALTER TABLE `system_notifications`
  ADD CONSTRAINT `system_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_bilties`
--
ALTER TABLE `vendor_bilties`
  ADD CONSTRAINT `vendor_bilties_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendor_bilties_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_ledgers`
--
ALTER TABLE `vendor_ledgers`
  ADD CONSTRAINT `vendor_ledgers_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_payments`
--
ALTER TABLE `vendor_payments`
  ADD CONSTRAINT `vendor_payments_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `voucher_details`
--
ALTER TABLE `voucher_details`
  ADD CONSTRAINT `voucher_details_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `voucher_details_voucher_master_id_foreign` FOREIGN KEY (`voucher_master_id`) REFERENCES `voucher_masters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_stocks`
--
ALTER TABLE `warehouse_stocks`
  ADD CONSTRAINT `warehouse_stocks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warehouse_stocks_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
