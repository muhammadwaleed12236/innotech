-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 07, 2026 at 12:00 PM
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
(1, 'Samsung', '2026-06-06 19:53:28', '2026-06-06 19:53:28');

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
(1, 'Electronics', '2026-06-06 19:53:27', '2026-06-06 19:53:27'),
(2, 'machine', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(3, 'Tools', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(4, 'Plumbing', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(5, 'Hardware', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(6, 'Electrical', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(7, 'Automotive', '2026-06-06 19:53:28', '2026-06-06 19:53:28');

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
(1, 'attendance_punch_gap_minutes', '20', 'integer', 'attendance', 'Punch Gap (Minutes)', 'Minimum minutes between punches to be considered as separate check-in/check-out. Punches within this gap will be ignored as duplicates.', '2026-06-06 19:53:18', '2026-06-06 19:53:18');

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
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7);

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
(25, 'chart.of.accounts', NULL, NULL),
(26, 'expense.voucher', NULL, NULL),
(27, 'receipts.voucher', NULL, NULL),
(28, 'journal.voucher', NULL, NULL),
(29, 'payment.voucher', NULL, NULL),
(30, 'income.voucher', NULL, NULL),
(31, 'item.stock.report', NULL, NULL),
(32, 'purchase.report', NULL, NULL),
(33, 'sale.report', NULL, NULL),
(34, 'reporting', NULL, NULL),
(35, 'inventory.onhand', NULL, NULL),
(36, 'users', NULL, NULL),
(37, 'roles', NULL, NULL),
(38, 'permissions', NULL, NULL),
(39, 'branches', NULL, NULL),
(40, 'zones', NULL, NULL),
(41, 'sales.officers', NULL, NULL),
(42, 'narrations', NULL, NULL),
(43, 'package.types', NULL, NULL),
(44, 'hr.departments', NULL, NULL),
(45, 'hr.employees', NULL, NULL),
(46, 'hr.attendance', NULL, NULL),
(47, 'hr.payroll', NULL, NULL),
(48, 'hr.leaves', NULL, NULL),
(49, 'hr.designations', NULL, NULL),
(50, 'hr.shifts', NULL, NULL),
(51, 'hr.holidays', NULL, NULL),
(52, 'hr.salary.structure', NULL, NULL),
(53, 'hr.loans', NULL, NULL),
(54, 'hr.biometric.devices', NULL, NULL);

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
(1, 'home.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(2, 'home.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(3, 'home.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(4, 'home.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(5, 'profile.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(6, 'profile.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(7, 'profile.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(8, 'profile.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(9, 'products.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(10, 'products.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(11, 'products.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(12, 'products.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(13, 'product.bookings.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(14, 'product.bookings.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(15, 'product.bookings.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(16, 'product.bookings.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(17, 'discount.products.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(18, 'discount.products.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(19, 'discount.products.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(20, 'discount.products.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(21, 'categories.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(22, 'categories.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(23, 'categories.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(24, 'categories.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(25, 'subcategories.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(26, 'subcategories.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(27, 'subcategories.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(28, 'subcategories.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(29, 'brands.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(30, 'brands.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(31, 'brands.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(32, 'brands.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(33, 'units.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(34, 'units.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(35, 'units.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(36, 'units.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(37, 'warehouse.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(38, 'warehouse.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(39, 'warehouse.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(40, 'warehouse.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(41, 'warehouse.stock.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(42, 'warehouse.stock.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(43, 'warehouse.stock.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(44, 'warehouse.stock.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(45, 'stock.transfer.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(46, 'stock.transfer.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(47, 'stock.transfer.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(48, 'stock.transfer.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(49, 'stock.adjust.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(50, 'stock.adjust.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(51, 'stock.adjust.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(52, 'stock.adjust.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(53, 'stocks.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(54, 'stocks.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(55, 'stocks.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(56, 'stocks.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(57, 'purchases.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(58, 'purchases.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(59, 'purchases.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(60, 'purchases.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(61, 'purchase.returns.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(62, 'purchase.returns.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(63, 'purchase.returns.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(64, 'purchase.returns.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(65, 'vendors.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(66, 'vendors.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(67, 'vendors.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(68, 'vendors.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(69, 'vendor.bilties.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(70, 'vendor.bilties.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(71, 'vendor.bilties.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(72, 'vendor.bilties.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(73, 'inward.gatepass.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(74, 'inward.gatepass.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(75, 'inward.gatepass.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(76, 'inward.gatepass.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(77, 'sales.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(78, 'sales.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(79, 'sales.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(80, 'sales.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(81, 'sales.returns.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(82, 'sales.returns.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(83, 'sales.returns.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(84, 'sales.returns.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(85, 'customers.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(86, 'customers.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(87, 'customers.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(88, 'customers.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(89, 'customer.ledger.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(90, 'customer.ledger.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(91, 'customer.ledger.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(92, 'customer.ledger.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(93, 'bookings.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(94, 'bookings.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(95, 'bookings.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(96, 'bookings.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(97, 'chart.of.accounts.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(98, 'chart.of.accounts.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(99, 'chart.of.accounts.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(100, 'chart.of.accounts.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(101, 'expense.voucher.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(102, 'expense.voucher.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(103, 'expense.voucher.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(104, 'expense.voucher.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(105, 'receipts.voucher.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(106, 'receipts.voucher.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(107, 'receipts.voucher.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(108, 'receipts.voucher.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(109, 'journal.voucher.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(110, 'journal.voucher.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(111, 'journal.voucher.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(112, 'journal.voucher.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(113, 'payment.voucher.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(114, 'payment.voucher.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(115, 'payment.voucher.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(116, 'payment.voucher.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(117, 'income.voucher.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(118, 'income.voucher.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(119, 'income.voucher.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(120, 'income.voucher.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(121, 'item.stock.report.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(122, 'item.stock.report.create', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(123, 'item.stock.report.edit', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(124, 'item.stock.report.delete', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(125, 'purchase.report.view', 'web', '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(126, 'purchase.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(127, 'purchase.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(128, 'purchase.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(129, 'sale.report.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(130, 'sale.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(131, 'sale.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(132, 'sale.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(133, 'reporting.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(134, 'reporting.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(135, 'reporting.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(136, 'reporting.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(137, 'recovery.report.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(138, 'recovery.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(139, 'recovery.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(140, 'recovery.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(141, 'payable.report.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(142, 'payable.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(143, 'payable.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(144, 'payable.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(145, 'parties.balance.report.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(146, 'parties.balance.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(147, 'parties.balance.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(148, 'parties.balance.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(149, 'aging.report.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(150, 'aging.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(151, 'aging.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(152, 'aging.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(153, 'balance.sheet.report.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(154, 'balance.sheet.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(155, 'balance.sheet.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(156, 'balance.sheet.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(157, 'profit.loss.report.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(158, 'profit.loss.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(159, 'profit.loss.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(160, 'profit.loss.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(161, 'inventory.onhand.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(162, 'inventory.onhand.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(163, 'inventory.onhand.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(164, 'inventory.onhand.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(165, 'vendor.ledger.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(166, 'vendor.ledger.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(167, 'vendor.ledger.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(168, 'vendor.ledger.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(169, 'users.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(170, 'users.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(171, 'users.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(172, 'users.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(173, 'roles.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(174, 'roles.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(175, 'roles.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(176, 'roles.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(177, 'permissions.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(178, 'permissions.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(179, 'permissions.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(180, 'permissions.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(181, 'branches.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(182, 'branches.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(183, 'branches.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(184, 'branches.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(185, 'zones.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(186, 'zones.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(187, 'zones.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(188, 'zones.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(189, 'sales.officers.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(190, 'sales.officers.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(191, 'sales.officers.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(192, 'sales.officers.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(193, 'narrations.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(194, 'narrations.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(195, 'narrations.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(196, 'narrations.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(197, 'executive.report.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(198, 'executive.report.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(199, 'executive.report.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(200, 'executive.report.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(201, 'package.types.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(202, 'package.types.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(203, 'package.types.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(204, 'package.types.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(205, 'hr.departments.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(206, 'hr.departments.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(207, 'hr.departments.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(208, 'hr.departments.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(209, 'hr.employees.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(210, 'hr.employees.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(211, 'hr.employees.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(212, 'hr.employees.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(213, 'hr.attendance.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(214, 'hr.attendance.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(215, 'hr.attendance.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(216, 'hr.attendance.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(217, 'hr.payroll.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(218, 'hr.payroll.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(219, 'hr.payroll.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(220, 'hr.payroll.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(221, 'hr.leaves.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(222, 'hr.leaves.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(223, 'hr.leaves.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(224, 'hr.leaves.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(225, 'hr.designations.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(226, 'hr.designations.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(227, 'hr.designations.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(228, 'hr.designations.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(229, 'hr.shifts.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(230, 'hr.shifts.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(231, 'hr.shifts.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(232, 'hr.shifts.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(233, 'hr.holidays.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(234, 'hr.holidays.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(235, 'hr.holidays.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(236, 'hr.holidays.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(237, 'hr.salary.structure.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(238, 'hr.salary.structure.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(239, 'hr.salary.structure.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(240, 'hr.salary.structure.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(241, 'hr.loans.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(242, 'hr.loans.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(243, 'hr.loans.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(244, 'hr.loans.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(245, 'hr.biometric.devices.view', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(246, 'hr.biometric.devices.create', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(247, 'hr.biometric.devices.edit', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(248, 'hr.biometric.devices.delete', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(249, 'checkbook.view', 'web', '2026-06-06 20:15:23', '2026-06-06 20:15:23');

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
(1, '1', '1', '5', 1, 0, 0, 1, 'ITEM-0001', '1', 'Formal Shirt', 'by_cartons', 0.00, 0.00, 12, 0.00, NULL, NULL, 0.00, 0.00, 5000.00, 375.00, 4500.00, 416.67, 0.00, '[\"Black\"]', '2026-06-06 19:53:28', '2026-06-06 19:53:28', NULL, '627869386192', NULL, NULL, NULL, 0, 0, 0, 0.00, 0.00, 0.00);

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
  `date` date NOT NULL DEFAULT '2026-06-07',
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
(1, 'Super Admin', 'web', '2026-06-06 19:53:29', '2026-06-06 19:53:29'),
(2, 'admin', 'web', '2026-06-06 19:55:11', '2026-06-06 19:55:11'),
(3, 'branch', 'web', '2026-06-07 08:54:51', '2026-06-07 08:54:51');

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
(1, 3),
(2, 1),
(2, 2),
(2, 3),
(3, 1),
(3, 2),
(3, 3),
(4, 1),
(4, 2),
(4, 3),
(5, 1),
(5, 2),
(5, 3),
(6, 1),
(6, 2),
(6, 3),
(7, 1),
(7, 2),
(7, 3),
(8, 1),
(8, 2),
(8, 3),
(9, 1),
(9, 2),
(9, 3),
(10, 1),
(10, 2),
(10, 3),
(11, 1),
(11, 2),
(11, 3),
(12, 1),
(12, 2),
(12, 3),
(13, 1),
(13, 2),
(13, 3),
(14, 1),
(14, 2),
(14, 3),
(15, 1),
(15, 2),
(15, 3),
(16, 1),
(16, 2),
(16, 3),
(17, 1),
(17, 2),
(17, 3),
(18, 1),
(18, 2),
(18, 3),
(19, 1),
(19, 2),
(19, 3),
(20, 1),
(20, 2),
(20, 3),
(21, 1),
(21, 2),
(21, 3),
(22, 1),
(22, 2),
(22, 3),
(23, 1),
(23, 2),
(23, 3),
(24, 1),
(24, 2),
(24, 3),
(25, 1),
(25, 2),
(25, 3),
(26, 1),
(26, 2),
(26, 3),
(27, 1),
(27, 2),
(27, 3),
(28, 1),
(28, 2),
(28, 3),
(29, 1),
(29, 2),
(29, 3),
(30, 1),
(30, 2),
(30, 3),
(31, 1),
(31, 2),
(31, 3),
(32, 1),
(32, 2),
(32, 3),
(33, 1),
(33, 2),
(33, 3),
(34, 1),
(34, 2),
(34, 3),
(35, 1),
(35, 2),
(35, 3),
(36, 1),
(36, 2),
(36, 3),
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
(53, 3),
(54, 1),
(54, 3),
(55, 1),
(55, 3),
(56, 1),
(56, 3),
(57, 1),
(57, 2),
(57, 3),
(58, 1),
(58, 2),
(58, 3),
(59, 1),
(59, 2),
(59, 3),
(60, 1),
(60, 2),
(60, 3),
(61, 1),
(61, 2),
(61, 3),
(62, 1),
(62, 2),
(62, 3),
(63, 1),
(63, 2),
(63, 3),
(64, 1),
(64, 2),
(64, 3),
(65, 1),
(65, 2),
(65, 3),
(66, 1),
(66, 2),
(66, 3),
(67, 1),
(67, 2),
(67, 3),
(68, 1),
(68, 2),
(68, 3),
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
(77, 3),
(78, 1),
(78, 2),
(78, 3),
(79, 1),
(79, 2),
(79, 3),
(80, 1),
(80, 2),
(80, 3),
(81, 1),
(81, 3),
(82, 1),
(82, 3),
(83, 1),
(83, 3),
(84, 1),
(84, 3),
(85, 1),
(85, 2),
(85, 3),
(86, 1),
(86, 2),
(86, 3),
(87, 1),
(87, 2),
(87, 3),
(88, 1),
(88, 2),
(88, 3),
(89, 1),
(89, 2),
(89, 3),
(90, 1),
(90, 2),
(90, 3),
(91, 1),
(91, 2),
(91, 3),
(92, 1),
(92, 2),
(92, 3),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(97, 2),
(97, 3),
(98, 1),
(98, 2),
(98, 3),
(99, 1),
(99, 2),
(99, 3),
(100, 1),
(100, 2),
(100, 3),
(101, 1),
(101, 2),
(101, 3),
(102, 1),
(102, 2),
(102, 3),
(103, 1),
(103, 2),
(103, 3),
(104, 1),
(104, 2),
(104, 3),
(105, 1),
(105, 2),
(105, 3),
(106, 1),
(106, 2),
(106, 3),
(107, 1),
(107, 2),
(107, 3),
(108, 1),
(108, 2),
(108, 3),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(113, 2),
(113, 3),
(114, 1),
(114, 2),
(114, 3),
(115, 1),
(115, 2),
(115, 3),
(116, 1),
(116, 2),
(116, 3),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(121, 2),
(121, 3),
(122, 1),
(122, 2),
(122, 3),
(123, 1),
(123, 2),
(123, 3),
(124, 1),
(124, 2),
(124, 3),
(125, 1),
(125, 2),
(125, 3),
(126, 1),
(126, 2),
(126, 3),
(127, 1),
(127, 2),
(127, 3),
(128, 1),
(128, 2),
(128, 3),
(129, 1),
(129, 2),
(129, 3),
(130, 1),
(130, 2),
(130, 3),
(131, 1),
(131, 2),
(131, 3),
(132, 1),
(132, 2),
(132, 3),
(133, 1),
(134, 1),
(135, 1),
(136, 1),
(137, 1),
(137, 3),
(138, 1),
(138, 3),
(139, 1),
(139, 3),
(140, 1),
(140, 3),
(141, 1),
(141, 2),
(141, 3),
(142, 1),
(142, 2),
(142, 3),
(143, 1),
(143, 2),
(143, 3),
(144, 1),
(144, 2),
(144, 3),
(145, 1),
(145, 3),
(146, 1),
(146, 3),
(147, 1),
(147, 3),
(148, 1),
(148, 3),
(149, 1),
(149, 2),
(149, 3),
(150, 1),
(150, 2),
(150, 3),
(151, 1),
(151, 2),
(151, 3),
(152, 1),
(152, 2),
(152, 3),
(153, 1),
(153, 2),
(153, 3),
(154, 1),
(154, 2),
(154, 3),
(155, 1),
(155, 2),
(155, 3),
(156, 1),
(156, 2),
(156, 3),
(157, 1),
(157, 2),
(157, 3),
(158, 1),
(158, 2),
(158, 3),
(159, 1),
(159, 2),
(159, 3),
(160, 1),
(160, 2),
(160, 3),
(161, 1),
(162, 1),
(163, 1),
(164, 1),
(165, 1),
(165, 2),
(165, 3),
(166, 1),
(166, 2),
(166, 3),
(167, 1),
(167, 2),
(167, 3),
(168, 1),
(168, 2),
(168, 3),
(169, 1),
(170, 1),
(171, 1),
(172, 1),
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
(185, 2),
(185, 3),
(186, 1),
(186, 2),
(186, 3),
(187, 1),
(187, 2),
(187, 3),
(188, 1),
(188, 2),
(188, 3),
(189, 1),
(190, 1),
(191, 1),
(192, 1),
(193, 1),
(194, 1),
(195, 1),
(196, 1),
(197, 1),
(197, 3),
(198, 1),
(198, 3),
(199, 1),
(199, 3),
(200, 1),
(200, 3),
(201, 1),
(202, 1),
(203, 1),
(204, 1),
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
(249, 2),
(249, 3);

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
('CWFnSvOIB0Mm6BPdaz5BpZEQWUciSjjA5uIxjX0q', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTW96a0xBaFVIRFZtMGdubUk2ejNHWGdzUEpZQXdjTWFEQkVhcFp3QiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ob21lIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Nzt9', 1780826345),
('RtVec9qorZUAKrQCwjVTA05j6zLwdg1kEkqQyxN5', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiYkdqTkdMVEdJMzI2M1ExbzNxM2Jsdzd2R1UwaEI1d1Q1bUdMc0YydSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yb2xlcyI7fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1780826343);

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
(1, 'company_name', 'Three Stars Medical', 'string', 'company', 'Company Name', 'Official company name displayed on invoices and reports', '2026-06-06 19:53:24', '2026-06-06 19:53:24'),
(2, 'company_address', '', 'text', 'company', 'Company Address', 'Full company address', '2026-06-06 19:53:24', '2026-06-06 19:53:24'),
(3, 'company_phone', '', 'string', 'company', 'Phone Number', 'Primary contact number', '2026-06-06 19:53:24', '2026-06-06 19:53:24'),
(4, 'currency_symbol', 'PKR', 'string', 'company', 'Currency Symbol', 'Currency used in the system', '2026-06-06 19:53:24', '2026-06-06 19:53:24'),
(5, 'debt_warning_days', '7', 'integer', 'sales', 'Debt Warning Days', 'Number of days after which a warning notification is sent for unpaid invoices', '2026-06-06 19:53:24', '2026-06-06 19:53:24'),
(6, 'debt_critical_days', '10', 'integer', 'sales', 'Debt Critical Days', 'Number of days after which a critical notification is sent for unpaid invoices', '2026-06-06 19:53:24', '2026-06-06 19:53:24'),
(7, 'invoice_terms', 'Payment due within 30 days. Late payments may incur additional charges.', 'text', 'sales', 'Invoice Terms & Conditions', 'Default terms and conditions displayed on invoices', '2026-06-06 19:53:24', '2026-06-06 19:53:24'),
(8, 'low_stock_threshold', '10', 'integer', 'inventory', 'Low Stock Threshold', 'Minimum quantity before low stock warning', '2026-06-06 19:53:24', '2026-06-06 19:53:24'),
(9, 'expiry_alert_days', '30', 'integer', 'inventory', 'Expiry Alert Days', 'Number of days before expiry to show warning', '2026-06-06 19:53:24', '2026-06-06 19:53:24');

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
(1, 'Fan', 1, '2026-06-06 19:53:27', '2026-06-06 19:53:27'),
(2, 'ceiling  Fan', 1, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(3, 'Pedestal  Fan', 1, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(4, 'Fridge', 1, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(5, 'Air-Condition(AC)', 1, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(6, 'Washing Machine', 1, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(7, 'Microwave Oven', 1, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(8, 'Drill Machine', 2, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(9, 'Grinder', 2, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(10, 'Lathe Machine', 2, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(11, 'Milling Machine', 2, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(12, 'Shaper Machine', 2, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(13, 'Hammer', 3, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(14, 'Screwdriver', 3, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(15, 'Wrench', 3, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(16, 'Pliers', 3, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(17, 'Tape Measure', 3, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(18, 'Pipe', 4, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(19, 'Faucet', 4, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(20, 'Valve', 4, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(21, 'Toilet', 4, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(22, 'Sink', 4, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(23, 'Nails', 5, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(24, 'Screws', 5, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(25, 'Bolts', 5, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(26, 'Hinges', 5, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(27, 'Brackets', 5, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(28, 'Light', 6, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(29, 'Switch', 6, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(30, 'Wire', 6, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(31, 'Cable', 6, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(32, 'Engine Oil', 7, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(33, 'Brake Pads', 7, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(34, 'Tires', 7, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(35, 'Batteries', 7, '2026-06-06 19:53:28', '2026-06-06 19:53:28'),
(36, 'Filters', 7, '2026-06-06 19:53:28', '2026-06-06 19:53:28');

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
(1, 'return_deadline_days', '30', 'integer', 'returns', 'Return Deadline (Days)', 'Number of days customers have to return items after purchase. Set to 0 to disable returns.', '2026-06-06 19:53:25', '2026-06-06 19:53:25'),
(2, 'return_require_approval', '1', 'boolean', 'returns', 'Require Manager Approval', 'If enabled, all returns must be approved by a manager before processing.', '2026-06-06 19:53:25', '2026-06-06 19:53:25'),
(3, 'return_auto_approve_threshold', '0', 'integer', 'returns', 'Auto-Approve Threshold', 'Returns under this amount will be auto-approved. Set to 0 to disable auto-approval.', '2026-06-06 19:53:25', '2026-06-06 19:53:25');

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
(1, 'Piece', '2026-06-06 19:53:28', '2026-06-06 19:53:28');

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
(1, 'Super Admin', 'superadmin@example.com', 0, 0, NULL, 'admin', '$2y$12$VzCLHQDJgT6J.KGUfafaSOcse0Pfow8qJ/glFujqvT.0cIKKi9BEy', NULL, '2026-06-06 19:53:30', '2026-06-06 19:53:30'),
(6, 'Qunoot', 'qunoot@gmail.com', 0, 0, NULL, 'admin', '$2y$12$7sfbajrTZwLHkzssNOm8buEtSrU2GZLsrIxMz/92okr2r9xIL3umi', NULL, '2026-06-07 08:57:10', '2026-06-07 08:57:10'),
(7, 'atif', 'admin@admin.com', 0, 0, NULL, 'admin', '$2y$12$Ngw4SNkoe/Ga9tpW8kIvYuy7boxCDSF35ekKW/IFcViTTl6c0P.te', NULL, '2026-06-07 09:10:25', '2026-06-07 09:10:25');

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
(1, 1, 'Main Store', 1, 'Karachi', 'Main stock storage', '2026-06-06 19:53:28', '2026-06-06 19:53:28', NULL),
(2, 1, 'Branch A', 1, 'Lahore', 'North region store', '2026-06-06 19:53:28', '2026-06-06 19:53:28', NULL),
(3, 1, 'Branch B', 1, 'Islamabad', 'Capital branch', '2026-06-06 19:53:28', '2026-06-06 19:53:28', NULL);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=250;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
