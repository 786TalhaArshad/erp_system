-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 10, 2026 at 08:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erp_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `banks`
--

CREATE TABLE `banks` (
  `id` int(11) NOT NULL,
  `bank_name` varchar(200) NOT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `bank_address` text DEFAULT NULL,
  `account_type` enum('current','savings') DEFAULT 'current',
  `opening_balance` decimal(18,2) DEFAULT 0.00,
  `current_balance` decimal(18,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `banks` (`id`, `bank_name`, `account_number`, `bank_address`, `account_type`, `opening_balance`, `current_balance`, `notes`, `status`, `date_time`) VALUES
(1, 'Habib Bank Limited', '1234-5678-9012', 'Main Branch, Lahore', 'current', 500000.00, 500000.00, 'Primary business account', 'active', '2026-07-01 09:00:00'),
(2, 'Allied Bank Limited', '9876-5432-1098', 'DHA Branch, Lahore', 'current', 250000.00, 250000.00, 'Secondary account', 'active', '2026-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `account_transactions`
--

CREATE TABLE `account_transactions` (
  `id` int(11) NOT NULL,
  `transaction_no` varchar(50) NOT NULL,
  `transaction_date` date DEFAULT NULL,
  `account_type` enum('asset','liability','equity','income','expense') NOT NULL,
  `account_name` varchar(100) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `debit` decimal(18,2) DEFAULT 0.00,
  `credit` decimal(18,2) DEFAULT 0.00,
  `balance` decimal(18,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `account_transactions` (`id`, `transaction_no`, `transaction_date`, `account_type`, `account_name`, `reference_type`, `reference_id`, `debit`, `credit`, `balance`, `description`, `date_time`) VALUES
(1, 'AT20260701_0001', '2026-07-01', 'expense', 'Utilities', 'expense', 1, 15000.00, 0.00, -15000.00, 'Electricity bill payment', '2026-07-01 10:00:00'),
(2, 'AT20260702_0002', '2026-07-02', 'asset', 'Import Purchase', 'import_purchase', 1, 469800.00, 0.00, -484800.00, 'Import purchase from Zhang Wei Trading', '2026-07-02 11:00:00'),
(3, 'AT20260705_0003', '2026-07-05', 'income', 'Sales Revenue', 'sale', 1, 0.00, 100000.00, -384800.00, 'Cash received from Raza Fashion House', '2026-07-05 14:00:00'),
(4, 'AT20260708_0004', '2026-07-08', 'income', 'Sales Revenue', 'sale', 2, 0.00, 340000.00, -44800.00, 'Full payment from Metro Clothing', '2026-07-08 15:00:00'),
(5, 'AT20260710_0005', '2026-07-10', 'expense', 'Supplier Payment', 'supplier_payment', 3, 30000.00, 0.00, -74800.00, 'Partial payment to Sialkot Industries', '2026-07-10 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `chinese_suppliers`
--

CREATE TABLE `chinese_suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(200) NOT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'China',
  `currency_id` int(11) DEFAULT NULL,
  `cnic` varchar(50) DEFAULT NULL,
  `ntn` varchar(50) DEFAULT NULL,
  `opening_balance` decimal(18,2) DEFAULT 0.00,
  `opening_balance_type` varchar(20) DEFAULT 'payable',
  `current_balance` decimal(18,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `chinese_suppliers` (`id`, `supplier_name`, `company_name`, `contact_person`, `phone`, `email`, `address`, `city`, `country`, `currency_id`, `cnic`, `ntn`, `opening_balance`, `opening_balance_type`, `current_balance`, `status`, `date_time`) VALUES
(1, 'Zhang Wei Trading', 'Zhang Wei Import Export Co.', 'Zhang Wei', '+86-138-0001-0001', 'zhangwei@trading.cn', 'No. 88 Huaqiang Road', 'Shenzhen', 'China', 2, NULL, NULL, 0.00, 'payable', 0.00, 'active', '2026-07-01 09:00:00'),
(2, 'Li Ming Electronics', 'Li Ming Tech Co. Ltd.', 'Li Ming', '+86-139-0002-0002', 'liming@tech.cn', 'No. 56 Baiyun Avenue', 'Guangzhou', 'China', 2, NULL, NULL, 5000.00, 'payable', 5000.00, 'active', '2026-07-01 09:00:00'),
(3, 'Wang & Co', 'Wang Fang Trading Ltd.', 'Wang Fang', '+86-137-0003-0003', 'wangfang@trading.cn', 'No. 12 Yiwu International Market', 'Yiwu', 'China', 2, NULL, NULL, 0.00, 'payable', 3200.00, 'active', '2026-07-01 09:00:00'),
(4, 'Chen Brothers', 'Chen Long Industrial Co.', 'Chen Long', '+86-136-0004-0004', 'chenlong@industrial.cn', 'No. 200 Beilun Port Road', 'Ningbo', 'China', 2, NULL, NULL, 3000.00, 'payable', 3000.00, 'active', '2026-07-01 09:00:00'),
(5, 'Liu Enterprises', 'Liu Yang Textile Co.', 'Liu Yang', '+86-135-0005-0005', 'liuyang@textile.cn', 'No. 45 Jingnan Road', 'Shanghai', 'China', 2, NULL, NULL, 0.00, 'payable', 0.00, 'active', '2026-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `chinese_supplier_payments`
--

CREATE TABLE `chinese_supplier_payments` (
  `id` int(11) NOT NULL,
  `payment_no` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount_cny` decimal(18,2) NOT NULL,
  `amount_pkr` decimal(18,2) NOT NULL,
  `exchange_rate` decimal(18,4) NOT NULL,
  `payment_type` enum('cash','bank_transfer','cheque','online','telegraphic_transfer') DEFAULT 'cash',
  `reference_no` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `cheque_no` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `chinese_supplier_payments` (`id`, `payment_no`, `supplier_id`, `payment_date`, `amount_cny`, `amount_pkr`, `exchange_rate`, `payment_type`, `reference_no`, `bank_name`, `cheque_no`, `notes`, `status`, `date_time`) VALUES
(1, 'CP20260702_0001', 1, '2026-07-02', 11600.00, 469800.00, 40.5000, 'telegraphic_transfer', 'TT-2026-001', 'Habib Bank Ltd.', NULL, 'Full payment for import purchase IP20260702_0001', 'completed', '2026-07-02 14:00:00'),
(2, 'CP20260708_0002', 3, '2026-07-08', 5000.00, 202500.00, 40.5000, 'bank_transfer', 'BT-2026-005', 'MCB Bank', NULL, 'Partial payment for import purchase IP20260706_0002', 'completed', '2026-07-08 11:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `currency_name` varchar(50) NOT NULL,
  `symbol` varchar(10) DEFAULT NULL,
  `exchange_rate` decimal(18,4) DEFAULT 1.0000,
  `base_currency` enum('yes','no') DEFAULT 'no',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `currencies` (`id`, `currency_code`, `currency_name`, `symbol`, `exchange_rate`, `base_currency`, `date_time`) VALUES
(1, 'PKR', 'Pakistani Rupee', 'Rs.', 1.0000, 'yes', '2026-07-01 09:00:00'),
(2, 'CNY', 'Chinese Yuan', '¥', 40.5000, 'no', '2026-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Pakistan',
  `cnic` varchar(20) DEFAULT NULL,
  `ntn` varchar(20) DEFAULT NULL,
  `opening_balance` decimal(18,2) DEFAULT 0.00,
  `current_balance` decimal(18,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customers` (`id`, `customer_name`, `company_name`, `contact_person`, `phone`, `email`, `address`, `city`, `country`, `cnic`, `ntn`, `opening_balance`, `current_balance`, `status`, `date_time`) VALUES
(1, 'Raza Fashion House', 'Raza Enterprises', 'Raza Ahmed', '0321-1234567', 'raza@fashion.pk', 'Mall Road, Near DATA Center', 'Lahore', 'Pakistan', '35202-1234567-1', '1234567-8', 0.00, 75000.00, 'active', '2026-07-01 09:00:00'),
(2, 'Metro Clothing', 'Metro Group of Companies', 'Tariq Mehmood', '0300-9876543', 'tariq@metro.pk', 'Saddar Town, Block A', 'Karachi', 'Pakistan', '42101-2345678-2', '2345678-9', 50000.00, 0.00, 'active', '2026-07-01 09:00:00'),
(3, 'Style Mart', 'Style Retailers', 'Imran Khan', '0333-5551234', 'imran@stylemart.pk', 'Blue Area, Jinnah Avenue', 'Islamabad', 'Pakistan', '17301-3456789-3', '3456789-0', 0.00, 75000.00, 'active', '2026-07-01 09:00:00'),
(4, 'City Textile', 'City Textile Mills', 'Naveed Akhtar', '0312-7778899', 'naveed@citytextile.pk', 'D Ground, Peoples Colony', 'Faisalabad', 'Pakistan', '36104-4567890-4', '4567890-1', 25000.00, 25000.00, 'active', '2026-07-01 09:00:00'),
(5, 'Premium Outfits', 'Premium Clothing Co.', 'Saad Malik', '0345-2223344', 'saad@premium.pk', 'Satellite Town, Commercial Area', 'Rawalpindi', 'Pakistan', '14301-5678901-5', '5678901-2', 0.00, 0.00, 'active', '2026-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_code` varchar(50) NOT NULL,
  `employee_name` varchar(200) NOT NULL,
  `father_name` varchar(200) DEFAULT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `designation` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `monthly_salary` decimal(18,2) DEFAULT 0.00,
  `opening_balance` decimal(18,2) DEFAULT 0.00,
  `current_balance` decimal(18,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `employees` (`id`, `employee_code`, `employee_name`, `father_name`, `cnic`, `phone`, `email`, `address`, `city`, `designation`, `department`, `joining_date`, `monthly_salary`, `opening_balance`, `current_balance`, `status`, `date_time`) VALUES
(1, 'EMP001', 'Muhammad Asif', 'Asif Khan', '36102-1111111-1', '0321-1111111', 'asif@erp.local', 'Ward 5, Main Street', 'Khanewal', 'Cutting Master', 'Production', '2025-01-15', 35000.00, 0.00, 0.00, 'active', '2025-01-15 09:00:00'),
(2, 'EMP002', 'Rashid Mehmood', 'Mehmood Ahmad', '36103-2222222-2', '0300-2222222', 'rashid@erp.local', 'Street 12, Colony B', 'Khanewal', 'Stitching Incharge', 'Production', '2025-02-01', 40000.00, 0.00, 0.00, 'active', '2025-02-01 09:00:00'),
(3, 'EMP003', 'Tariq Hussain', 'Hussain Bakhsh', '35202-3333333-3', '0333-3333333', 'tariq@erp.local', 'Model Town, Block C', 'Lahore', 'Quality Checker', 'Quality', '2025-03-10', 30000.00, 5000.00, 5000.00, 'active', '2025-03-10 09:00:00'),
(4, 'EMP004', 'Bilal Ahmad', 'Ahmad Ali', '36104-4444444-4', '0312-4444444', 'bilal@erp.local', 'Madina Colony, Near Mosque', 'Faisalabad', 'Supervisor', 'Production', '2025-01-15', 45000.00, 0.00, 0.00, 'active', '2025-01-15 09:00:00'),
(5, 'EMP005', 'Kashif Raza', 'Raza Hussain', '14301-5555555-5', '0345-5555555', 'kashif@erp.local', 'Satellite Town, House 12', 'Rawalpindi', 'Store Keeper', 'Store', '2025-04-01', 25000.00, 0.00, 0.00, 'active', '2025-04-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `employee_payables`
--

CREATE TABLE `employee_payables` (
  `id` int(11) NOT NULL,
  `bill_no` varchar(50) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `month_year` varchar(20) DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `description` text DEFAULT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_payments`
--

CREATE TABLE `employee_payments` (
  `id` int(11) NOT NULL,
  `payment_no` varchar(50) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `month_year` varchar(20) DEFAULT NULL,
  `payment_type` enum('salary','advance','bonus','other') DEFAULT 'salary',
  `notes` text DEFAULT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `employee_payments` (`id`, `payment_no`, `employee_id`, `payment_date`, `amount`, `month_year`, `payment_type`, `notes`, `date_time`) VALUES
(1, 'EP20260701_0001', 1, '2026-07-01', 35000.00, 'Jul-2026', 'salary', 'July 2026 salary paid', '2026-07-01 17:00:00'),
(2, 'EP20260701_0002', 2, '2026-07-01', 40000.00, 'Jul-2026', 'salary', 'July 2026 salary paid', '2026-07-01 17:00:00'),
(3, 'EP20260701_0003', 4, '2026-07-01', 45000.00, 'Jul-2026', 'salary', 'July 2026 salary paid', '2026-07-01 17:00:00'),
(4, 'EP20260705_0004', 3, '2026-07-05', 5000.00, NULL, 'advance', 'Advance against salary', '2026-07-05 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_no` varchar(50) NOT NULL,
  `expense_date` date DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `paid_amount` decimal(18,2) DEFAULT 0.00,
  `balance` decimal(18,2) DEFAULT 0.00,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `paid_by` varchar(100) DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `expenses` (`id`, `expense_no`, `expense_date`, `category`, `description`, `amount`, `paid_amount`, `balance`, `payment_status`, `paid_by`, `reference_no`, `status`, `date_time`) VALUES
(1, 'EXP20260701_0001', '2026-07-01', 'Utilities', 'July electricity bill - factory + office', 15000.00, 15000.00, 0.00, 'paid', 'Kashif Raza', 'ELEC-2026-07', 'paid', '2026-07-01 10:00:00'),
(2, 'EXP20260705_0002', '2026-07-05', 'Transportation', 'Delivery charges for local purchase LP20260701_0001', 8500.00, 8500.00, 0.00, 'paid', 'Bilal Ahmad', 'CHALLAN-001', 'paid', '2026-07-05 11:00:00'),
(3, 'EXP20260710_0003', '2026-07-10', 'Office Supplies', 'Stationery and printing paper', 3200.00, 3200.00, 0.00, 'paid', 'Kashif Raza', 'INV-2026-105', 'paid', '2026-07-10 09:30:00'),
(4, 'EXP20260710_0004', '2026-07-10', 'Maintenance', 'Sewing machine oil and minor repairs', 4500.00, 4500.00, 0.00, 'paid', 'Bilal Ahmad', NULL, 'paid', '2026-07-10 14:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `expense_categories` (`id`, `category_name`, `description`, `status`, `date_time`) VALUES
(1, 'Utilities', 'Electricity, water, gas bills', 'active', '2026-07-01 09:00:00'),
(2, 'Rent', 'Office and factory rent', 'active', '2026-07-01 09:00:00'),
(3, 'Transportation', 'Logistics and delivery costs', 'active', '2026-07-01 09:00:00'),
(4, 'Office Supplies', 'Stationery and office items', 'active', '2026-07-01 09:00:00'),
(5, 'Maintenance', 'Equipment and building maintenance', 'active', '2026-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `finished_goods`
--

CREATE TABLE `finished_goods` (
  `id` int(11) NOT NULL,
  `product_code` varchar(50) NOT NULL COMMENT 'Unique product code e.g., FG20260109_1234',
  `product_name` varchar(200) NOT NULL COMMENT 'Product name',
  `category` varchar(100) DEFAULT NULL COMMENT 'Product category',
  `unit` varchar(20) NOT NULL COMMENT 'Unit of measurement e.g., Pcs, Kg, Meter',
  `current_stock` decimal(18,2) DEFAULT 0.00 COMMENT 'Current available stock',
  `minimum_stock` decimal(18,2) DEFAULT 0.00 COMMENT 'Minimum stock level for alerts',
  `selling_price` decimal(18,2) DEFAULT 0.00 COMMENT 'Selling price in PKR',
  `cost_price` decimal(18,2) DEFAULT 0.00 COMMENT 'Production cost in PKR',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT 'Product status',
  `date_time` datetime DEFAULT NULL COMMENT 'Creation/Update date time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `finished_goods` (`id`, `product_code`, `product_name`, `category`, `unit`, `current_stock`, `minimum_stock`, `selling_price`, `cost_price`, `status`, `date_time`) VALUES
(1, 'FG20260701_0001', 'Classic Cotton Shirt', 'Apparel', 'PCS', 50.00, 100.00, 1200.00, 800.00, 'active', '2026-07-01 09:00:00'),
(2, 'FG20260701_0002', 'Silk Scarf Premium', 'Accessories', 'PCS', 66.00, 50.00, 2500.00, 1800.00, 'active', '2026-07-01 09:00:00'),
(3, 'FG20260701_0003', 'Denim Jeans Classic', 'Apparel', 'PCS', 20.00, 80.00, 3500.00, 2200.00, 'active', '2026-07-01 09:00:00'),
(4, 'FG20260701_0004', 'Linen Blazer', 'Apparel', 'PCS', 0.00, 30.00, 5000.00, 3500.00, 'active', '2026-07-01 09:00:00'),
(5, 'FG20260701_0005', 'Casual Polo Shirt', 'Apparel', 'PCS', 50.00, 120.00, 900.00, 600.00, 'active', '2026-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `import_purchases`
--

CREATE TABLE `import_purchases` (
  `id` int(11) NOT NULL,
  `purchase_no` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `exchange_rate` decimal(18,4) DEFAULT NULL,
  `total_cny` decimal(18,2) DEFAULT 0.00,
  `total_pkr` decimal(18,2) DEFAULT 0.00,
  `freight_charges_cny` decimal(18,2) DEFAULT 0.00,
  `freight_charges_pkr` decimal(18,2) DEFAULT 0.00,
  `insurance_cny` decimal(18,2) DEFAULT 0.00,
  `insurance_pkr` decimal(18,2) DEFAULT 0.00,
  `other_charges_cny` decimal(18,2) DEFAULT 0.00,
  `other_charges_pkr` decimal(18,2) DEFAULT 0.00,
  `grand_total_cny` decimal(18,2) DEFAULT 0.00,
  `grand_total_pkr` decimal(18,2) DEFAULT 0.00,
  `paid_amount_cny` decimal(18,2) DEFAULT 0.00,
  `paid_amount_pkr` decimal(18,2) DEFAULT 0.00,
  `balance_cny` decimal(18,2) DEFAULT 0.00,
  `balance_pkr` decimal(18,2) DEFAULT 0.00,
  `previous_amount_cny` decimal(18,2) DEFAULT 0.00,
  `tax_amount_cny` decimal(18,2) DEFAULT 0.00,
  `payment_status` enum('paid','partial','unpaid') DEFAULT 'unpaid',
  `status` enum('pending','received','cancelled') DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `import_purchases` (`id`, `purchase_no`, `supplier_id`, `purchase_date`, `invoice_no`, `currency_id`, `exchange_rate`, `total_cny`, `total_pkr`, `freight_charges_cny`, `freight_charges_pkr`, `insurance_cny`, `insurance_pkr`, `other_charges_cny`, `other_charges_pkr`, `grand_total_cny`, `grand_total_pkr`, `paid_amount_cny`, `paid_amount_pkr`, `balance_cny`, `balance_pkr`, `previous_amount_cny`, `tax_amount_cny`, `payment_status`, `status`, `date_time`) VALUES
(1, 'IP20260702_0001', 1, '2026-07-02', 'ZW-INV-20260701', 2, 40.5000, 10500.00, 425250.00, 800.00, 32400.00, 300.00, 12150.00, 0.00, 0.00, 11600.00, 469800.00, 11600.00, 469800.00, 0.00, 0.00, 0.00, 0.00, 'paid', 'received', '2026-07-02 10:00:00'),
(2, 'IP20260706_0002', 4, '2026-07-06', 'CL-INV-20260705', 2, 40.5000, 7600.00, 307800.00, 400.00, 16200.00, 200.00, 8100.00, 0.00, 0.00, 8200.00, 332100.00, 5000.00, 202500.00, 3200.00, 129600.00, 0.00, 0.00, 'partial', 'received', '2026-07-06 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `import_purchase_items`
--

CREATE TABLE `import_purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `unit_price_cny` decimal(18,2) NOT NULL,
  `unit_price_pkr` decimal(18,2) NOT NULL,
  `total_cny` decimal(18,2) NOT NULL,
  `total_pkr` decimal(18,2) NOT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `import_purchase_items` (`id`, `purchase_id`, `material_id`, `quantity`, `unit_price_cny`, `unit_price_pkr`, `total_cny`, `total_pkr`, `date_time`) VALUES
(1, 1, 3, 1000.00, 3.00, 121.50, 3000.00, 121500.00, '2026-07-02 10:00:00'),
(2, 1, 4, 500.00, 5.00, 202.50, 2500.00, 101250.00, '2026-07-02 10:00:00'),
(3, 1, 5, 200.00, 25.00, 1012.50, 5000.00, 202500.00, '2026-07-02 10:00:00'),
(4, 2, 8, 500.00, 8.00, 324.00, 4000.00, 162000.00, '2026-07-06 10:00:00'),
(5, 2, 11, 300.00, 12.00, 486.00, 3600.00, 145800.00, '2026-07-06 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `local_purchases`
--

CREATE TABLE `local_purchases` (
  `id` int(11) NOT NULL,
  `purchase_no` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `total_amount` decimal(18,2) DEFAULT 0.00,
  `paid_amount` decimal(18,2) DEFAULT 0.00,
  `balance` decimal(18,2) DEFAULT 0.00,
  `payment_status` enum('paid','partial','unpaid') DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT 'credit',
  `status` enum('pending','received','cancelled') DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `local_purchases` (`id`, `purchase_no`, `supplier_id`, `purchase_date`, `invoice_no`, `total_amount`, `paid_amount`, `balance`, `payment_status`, `payment_method`, `status`, `date_time`) VALUES
(1, 'LP20260701_0001', 1, '2026-07-01', 'AB-INV-001', 130000.00, 80000.00, 50000.00, 'partial', 'cash', 'received', '2026-07-01 10:00:00'),
(2, 'LP20260703_0002', 2, '2026-07-03', 'KT-INV-002', 38000.00, 38000.00, 0.00, 'paid', 'bank_transfer', 'received', '2026-07-03 10:00:00'),
(3, 'LP20260705_0003', 4, '2026-07-05', 'SI-INV-003', 60000.00, 30000.00, 30000.00, 'partial', 'cheque', 'received', '2026-07-05 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `local_purchase_items`
--

CREATE TABLE `local_purchase_items` (
  `id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `local_purchase_items` (`id`, `purchase_id`, `material_id`, `quantity`, `unit_price`, `total`, `date_time`) VALUES
(1, 1, 1, 500.00, 200.00, 100000.00, '2026-07-01 10:00:00'),
(2, 1, 2, 200.00, 150.00, 30000.00, '2026-07-01 10:00:00'),
(3, 2, 6, 500.00, 40.00, 20000.00, '2026-07-03 10:00:00'),
(4, 2, 10, 100.00, 180.00, 18000.00, '2026-07-03 10:00:00'),
(5, 3, 9, 50.00, 800.00, 40000.00, '2026-07-05 10:00:00'),
(6, 3, 12, 100.00, 200.00, 20000.00, '2026-07-05 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `local_suppliers`
--

CREATE TABLE `local_suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(200) NOT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Pakistan',
  `cnic` varchar(20) DEFAULT NULL,
  `ntn` varchar(20) DEFAULT NULL,
  `opening_balance` decimal(18,2) DEFAULT 0.00,
  `opening_balance_type` varchar(20) DEFAULT 'payable',
  `current_balance` decimal(18,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `local_suppliers` (`id`, `supplier_name`, `company_name`, `contact_person`, `phone`, `email`, `address`, `city`, `country`, `cnic`, `ntn`, `opening_balance`, `opening_balance_type`, `current_balance`, `status`, `date_time`) VALUES
(1, 'Ahmed Brothers', 'Ahmed Textile Traders', 'Ahmed Khan', '0321-1234567', 'ahmed@brothers.pk', 'Main Bazaar, Near Chowk', 'Khanewal', 'Pakistan', '36102-1234567-1', '1234567-8', 0.00, 'payable', 50000.00, 'active', '2026-07-01 09:00:00'),
(2, 'Khan Traders', 'Khan & Sons Trading', 'Muhammad Khan', '0300-9876543', 'khan@traders.pk', 'Waller Road, Shop 12', 'Multan', 'Pakistan', '36103-2345678-2', '2345678-9', 0.00, 'payable', 0.00, 'active', '2026-07-01 09:00:00'),
(3, 'Punjab Supplies', 'Punjab Raw Material Supply', 'Ali Raza', '0333-5551234', 'ali@punjabsupplies.pk', 'GT Road, Near Thokar', 'Lahore', 'Pakistan', '35202-3456789-3', '3456789-0', 0.00, 'payable', 0.00, 'active', '2026-07-01 09:00:00'),
(4, 'Sialkot Industries', 'Sialkot Industrial Supplies', 'Hassan Ahmed', '0312-7778899', 'hassan@sialkotind.pk', 'Sialkot Cantonment, Daska Road', 'Sialkot', 'Pakistan', '35203-4567890-4', '4567890-1', 0.00, 'payable', 30000.00, 'active', '2026-07-01 09:00:00'),
(5, 'Faisalabad Trading Co.', 'Faisalabad Textile Hub', 'Usman Sheikh', '0345-2223344', 'usman@fstc.pk', 'Clock Tower, Bawana Chowk', 'Faisalabad', 'Pakistan', '36104-5678901-5', '5678901-2', 0.00, 'payable', 0.00, 'active', '2026-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `production`
--

CREATE TABLE `production` (
  `id` int(11) NOT NULL,
  `production_no` varchar(50) NOT NULL,
  `finished_good_id` int(11) NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `production_date` date DEFAULT NULL,
  `total_cost` decimal(18,2) DEFAULT 0.00,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `production` (`id`, `production_no`, `finished_good_id`, `quantity`, `production_date`, `total_cost`, `status`, `date_time`) VALUES
(1, 'PRD20260704_0001', 1, 200.00, '2026-07-04', 160000.00, 'completed', '2026-07-04 08:00:00'),
(2, 'PRD20260707_0002', 2, 80.00, '2026-07-07', 144000.00, 'completed', '2026-07-07 08:00:00'),
(3, 'PRD20260703_0003', 3, 120.00, '2026-07-03', 264000.00, 'completed', '2026-07-03 08:00:00'),
(4, 'PRD20260702_0004', 5, 150.00, '2026-07-02', 90000.00, 'completed', '2026-07-02 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `production_raw_materials`
--

CREATE TABLE `production_raw_materials` (
  `id` int(11) NOT NULL,
  `production_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity_used` decimal(18,2) NOT NULL,
  `cost_per_unit` decimal(18,2) NOT NULL,
  `total_cost` decimal(18,2) NOT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `production_raw_materials` (`id`, `production_id`, `material_id`, `quantity_used`, `cost_per_unit`, `total_cost`, `date_time`) VALUES
(1, 1, 1, 100.00, 200.00, 20000.00, '2026-07-04 08:00:00'),
(2, 1, 2, 40.00, 150.00, 6000.00, '2026-07-04 08:00:00'),
(3, 1, 12, 80.00, 200.00, 16000.00, '2026-07-04 08:00:00'),
(4, 2, 5, 60.00, 1012.50, 60750.00, '2026-07-07 08:00:00'),
(5, 2, 11, 20.00, 486.00, 9720.00, '2026-07-07 08:00:00'),
(6, 3, 1, 150.00, 200.00, 30000.00, '2026-07-03 08:00:00'),
(7, 3, 6, 80.00, 40.00, 3200.00, '2026-07-03 08:00:00'),
(8, 3, 9, 30.00, 800.00, 24000.00, '2026-07-03 08:00:00'),
(9, 4, 1, 100.00, 200.00, 20000.00, '2026-07-02 08:00:00'),
(10, 4, 2, 30.00, 150.00, 4500.00, '2026-07-02 08:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `account_date` date DEFAULT NULL,
  `account_type` varchar(100) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `debit` decimal(18,2) DEFAULT 0.00,
  `credit` decimal(18,2) DEFAULT 0.00,
  `balance` decimal(18,2) DEFAULT 0.00,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `accounts` (`id`, `account_date`, `account_type`, `reference_type`, `reference_id`, `description`, `debit`, `credit`, `balance`, `date_time`) VALUES
(1, '2026-07-01', 'Cash', NULL, NULL, 'Opening cash balance', 0.00, 0.00, 500000.00, '2026-07-01 09:00:00'),
(2, '2026-07-05', 'Sales', 'sale', 1, 'Sale to Raza Fashion House', 180000.00, 0.00, 680000.00, '2026-07-05 14:00:00'),
(3, '2026-07-05', 'Cash', 'receipt', 1, 'Payment received from Raza Fashion House', 0.00, 100000.00, 580000.00, '2026-07-05 14:30:00'),
(4, '2026-07-08', 'Sales', 'sale', 2, 'Sale to Metro Clothing', 340000.00, 0.00, 920000.00, '2026-07-08 15:00:00'),
(5, '2026-07-08', 'Bank', 'receipt', 2, 'Payment received from Metro Clothing', 0.00, 340000.00, 580000.00, '2026-07-08 16:00:00'),
(6, '2026-07-10', 'Sales', 'sale', 3, 'Sale to Style Mart', 125000.00, 0.00, 705000.00, '2026-07-10 16:00:00'),
(7, '2026-07-10', 'Cash', 'receipt', 3, 'Payment received from Style Mart', 0.00, 50000.00, 655000.00, '2026-07-10 17:00:00'),
(8, '2026-07-01', 'Cash', NULL, NULL, 'Opening cash balance', 0.00, 0.00, 500000.00, '2026-07-01 09:00:00'),
(9, '2026-07-05', 'Sales', 'sale', 1, 'Sale to Raza Fashion House', 180000.00, 0.00, 680000.00, '2026-07-05 14:00:00'),
(10, '2026-07-05', 'Cash', 'receipt', 1, 'Payment received from Raza Fashion House', 0.00, 100000.00, 580000.00, '2026-07-05 14:30:00'),
(11, '2026-07-08', 'Sales', 'sale', 2, 'Sale to Metro Clothing', 340000.00, 0.00, 920000.00, '2026-07-08 15:00:00'),
(12, '2026-07-08', 'Bank', 'receipt', 2, 'Payment received from Metro Clothing', 0.00, 340000.00, 580000.00, '2026-07-08 16:00:00'),
(13, '2026-07-10', 'Sales', 'sale', 3, 'Sale to Style Mart', 125000.00, 0.00, 705000.00, '2026-07-10 16:00:00'),
(14, '2026-07-10', 'Cash', 'receipt', 3, 'Payment received from Style Mart', 0.00, 50000.00, 655000.00, '2026-07-10 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `customer_receipts`
--

CREATE TABLE `customer_receipts` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `receipt_no` varchar(50) NOT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `customer_receipts` (`id`, `sale_id`, `customer_id`, `receipt_no`, `amount`, `payment_method`, `reference`, `payment_date`, `notes`, `date_time`) VALUES
(1, 1, 1, 'RCT20260705_0001', 100000.00, 'cash', NULL, '2026-07-05', 'Partial payment for sale SAL20260705_0001', '2026-07-05 14:30:00'),
(2, 2, 2, 'RCT20260708_0002', 340000.00, 'bank_transfer', 'CHQ-001234', '2026-07-08', 'Full payment for sale SAL20260708_0002', '2026-07-08 16:00:00'),
(3, 3, 3, 'RCT20260710_0003', 50000.00, 'cash', NULL, '2026-07-10', 'Partial payment for sale SAL20260710_0003', '2026-07-10 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `material_issues`
--

CREATE TABLE `material_issues` (
  `id` int(11) NOT NULL,
  `issue_no` varchar(50) NOT NULL,
  `issue_date` date DEFAULT NULL,
  `production_order_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `total_value` decimal(18,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `material_issue_items`
--

CREATE TABLE `material_issue_items` (
  `id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_orders`
--

CREATE TABLE `production_orders` (
  `id` int(11) NOT NULL,
  `production_no` varchar(50) NOT NULL,
  `production_date` date DEFAULT NULL,
  `finished_good_id` int(11) NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `unit_cost` decimal(18,2) DEFAULT 0.00,
  `total_cost` decimal(18,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `production_order_materials`
--

CREATE TABLE `production_order_materials` (
  `id` int(11) NOT NULL,
  `production_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `quantity_used` decimal(18,2) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `id` int(11) NOT NULL,
  `material_code` varchar(50) NOT NULL,
  `material_name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `current_stock` decimal(18,2) DEFAULT 0.00,
  `minimum_stock` decimal(18,2) DEFAULT 0.00,
  `purchase_price_pkr` decimal(18,2) DEFAULT 0.00,
  `selling_price` decimal(18,2) DEFAULT 0.00,
  `supplier_type` enum('chinese','local') DEFAULT 'local',
  `supplier_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `raw_materials` (`id`, `material_code`, `material_name`, `category`, `unit`, `current_stock`, `minimum_stock`, `purchase_price_pkr`, `selling_price`, `supplier_type`, `supplier_id`, `status`, `date_time`) VALUES
(1, 'RM20260701_0001', 'Cotton Fabric 60 inch', 'Fabric', 'Meter', 150.00, 200.00, 200.00, 250.00, 'local', 1, 'active', '2026-07-01 09:00:00'),
(2, 'RM20260701_0002', 'Polyester Thread 500m', 'Thread', 'Spool', 130.00, 100.00, 150.00, 200.00, 'local', 2, 'active', '2026-07-01 09:00:00'),
(3, 'RM20260701_0003', 'Zipper #5 Metal', 'Accessories', 'Piece', 1000.00, 500.00, 121.50, 160.00, 'chinese', 1, 'active', '2026-07-01 09:00:00'),
(4, 'RM20260701_0004', 'Button Set 4-Hole', 'Accessories', 'Set', 500.00, 200.00, 202.50, 260.00, 'chinese', 2, 'active', '2026-07-01 09:00:00'),
(5, 'RM20260701_0005', 'Silk Fabric Premium', 'Fabric', 'Meter', 140.00, 100.00, 1012.50, 1300.00, 'chinese', 3, 'active', '2026-07-01 09:00:00'),
(6, 'RM20260701_0006', 'Elastic Band 1 inch', 'Accessories', 'Meter', 420.00, 300.00, 40.00, 60.00, 'local', 3, 'active', '2026-07-01 09:00:00'),
(7, 'RM20260701_0007', 'Linen Fabric Natural', 'Fabric', 'Meter', 0.00, 50.00, 500.00, 650.00, 'local', 4, 'active', '2026-07-01 09:00:00'),
(8, 'RM20260701_0008', 'Metal Buckle Silver', 'Accessories', 'Piece', 500.00, 200.00, 324.00, 420.00, 'chinese', 4, 'active', '2026-07-01 09:00:00'),
(9, 'RM20260701_0009', 'Dye Chemical Vat Blue', 'Chemical', 'Liter', 20.00, 30.00, 800.00, 1000.00, 'local', 5, 'active', '2026-07-01 09:00:00'),
(10, 'RM20260701_0010', 'Cotton Yarn 2/60s', 'Thread', 'Kg', 100.00, 50.00, 180.00, 240.00, 'local', 2, 'active', '2026-07-01 09:00:00'),
(11, 'RM20260701_0011', 'Silk Thread 100m', 'Thread', 'Spool', 280.00, 150.00, 486.00, 620.00, 'chinese', 5, 'active', '2026-07-01 09:00:00'),
(12, 'RM20260701_0012', 'Poly Bag Packaging', 'Packaging', 'Roll', 20.00, 100.00, 200.00, 280.00, 'local', 3, 'active', '2026-07-01 09:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `sale_no` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sale_date` date DEFAULT NULL,
  `invoice_no` varchar(50) DEFAULT NULL,
  `total_amount` decimal(18,2) DEFAULT 0.00,
  `discount` decimal(18,2) DEFAULT 0.00,
  `net_amount` decimal(18,2) DEFAULT 0.00,
  `paid_amount` decimal(18,2) DEFAULT 0.00,
  `balance` decimal(18,2) DEFAULT 0.00,
  `payment_status` enum('paid','partial','unpaid') DEFAULT 'unpaid',
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sales` (`id`, `sale_no`, `customer_id`, `sale_date`, `invoice_no`, `total_amount`, `discount`, `net_amount`, `paid_amount`, `balance`, `payment_status`, `status`, `date_time`) VALUES
(1, 'SAL20260705_0001', 1, '2026-07-05', 'INV-2026-001', 185000.00, 5000.00, 180000.00, 100000.00, 80000.00, 'partial', 'completed', '2026-07-05 14:00:00'),
(2, 'SAL20260708_0002', 2, '2026-07-08', 'INV-2026-002', 350000.00, 10000.00, 340000.00, 340000.00, 0.00, 'paid', 'completed', '2026-07-08 15:00:00'),
(3, 'SAL20260710_0003', 3, '2026-07-10', 'INV-2026-003', 125000.00, 0.00, 125000.00, 50000.00, 75000.00, 'partial', 'completed', '2026-07-10 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `total` decimal(18,2) NOT NULL,
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `total`, `date_time`) VALUES
(1, 1, 1, 100.00, 1200.00, 120000.00, '2026-07-05 14:00:00'),
(2, 1, 5, 50.00, 900.00, 45000.00, '2026-07-05 14:00:00'),
(3, 1, 2, 8.00, 2500.00, 20000.00, '2026-07-05 14:00:00'),
(4, 2, 3, 100.00, 3500.00, 350000.00, '2026-07-08 15:00:00'),
(5, 3, 5, 50.00, 900.00, 45000.00, '2026-07-10 16:00:00'),
(6, 3, 1, 50.00, 1200.00, 60000.00, '2026-07-10 16:00:00'),
(7, 3, 2, 8.00, 2500.00, 20000.00, '2026-07-10 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` int(11) NOT NULL,
  `payment_no` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(18,2) NOT NULL,
  `payment_type` enum('cash','bank_transfer','cheque','online') DEFAULT 'cash',
  `reference_no` varchar(50) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `cheque_no` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `supplier_payments` (`id`, `payment_no`, `supplier_id`, `payment_date`, `amount`, `payment_type`, `reference_no`, `bank_name`, `cheque_no`, `notes`, `status`, `date_time`) VALUES
(1, 'SP20260701_0001', 1, '2026-07-01', 80000.00, 'cash', NULL, NULL, NULL, 'Partial payment for LP20260701_0001', 'completed', '2026-07-01 16:00:00'),
(2, 'SP20260703_0002', 2, '2026-07-03', 38000.00, 'bank_transfer', 'BT-2026-003', 'Allied Bank', NULL, 'Full payment for LP20260703_0002', 'completed', '2026-07-03 16:00:00'),
(3, 'SP20260705_0003', 4, '2026-07-05', 30000.00, 'cheque', 'CHQ-2026-010', 'Habib Bank', '456789', 'Partial payment for LP20260705_0003', 'completed', '2026-07-05 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','manager','user') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `phone`, `role`, `status`, `date_time`) VALUES
(1, 'admin', 'admin', 'System Administrator', 'admin@erpsystem.com', NULL, 'admin', 'active', '2026-07-09 15:58:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banks`
--
ALTER TABLE `banks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_receipts`
--
ALTER TABLE `customer_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sale_id` (`sale_id`),
  ADD KEY `idx_customer_id` (`customer_id`);

--
-- Indexes for table `material_issues`
--
ALTER TABLE `material_issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_production_order_id` (`production_order_id`);

--
-- Indexes for table `material_issue_items`
--
ALTER TABLE `material_issue_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_issue_id` (`issue_id`);

--
-- Indexes for table `production_orders`
--
ALTER TABLE `production_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_finished_good_id` (`finished_good_id`);

--
-- Indexes for table `production_order_materials`
--
ALTER TABLE `production_order_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_production_id` (`production_id`);

--
-- Indexes for table `account_transactions`
--
ALTER TABLE `account_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_no` (`transaction_no`);

--
-- Indexes for table `chinese_suppliers`
--
ALTER TABLE `chinese_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `chinese_supplier_payments`
--
ALTER TABLE `chinese_supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_no` (`payment_no`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currency_code` (`currency_code`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_code` (`employee_code`);

--
-- Indexes for table `employee_payables`
--
ALTER TABLE `employee_payables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bill_no` (`bill_no`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employee_payments`
--
ALTER TABLE `employee_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_no` (`payment_no`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_no` (`expense_no`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `finished_goods`
--
ALTER TABLE `finished_goods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_code` (`product_code`),
  ADD KEY `idx_product_code` (`product_code`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_stock` (`current_stock`);

--
-- Indexes for table `import_purchases`
--
ALTER TABLE `import_purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_no` (`purchase_no`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `import_purchase_items`
--
ALTER TABLE `import_purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `local_purchases`
--
ALTER TABLE `local_purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_no` (`purchase_no`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `local_purchase_items`
--
ALTER TABLE `local_purchase_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `local_suppliers`
--
ALTER TABLE `local_suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production`
--
ALTER TABLE `production`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `production_no` (`production_no`),
  ADD KEY `finished_good_id` (`finished_good_id`);

--
-- Indexes for table `production_raw_materials`
--
ALTER TABLE `production_raw_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_id` (`production_id`),
  ADD KEY `material_id` (`material_id`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `material_code` (`material_code`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sale_no` (`sale_no`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_no` (`payment_no`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banks`
--
ALTER TABLE `banks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customer_receipts`
--
ALTER TABLE `customer_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `material_issues`
--
ALTER TABLE `material_issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `material_issue_items`
--
ALTER TABLE `material_issue_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `production_orders`
--
ALTER TABLE `production_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `production_order_materials`
--
ALTER TABLE `production_order_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `account_transactions`
--
ALTER TABLE `account_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chinese_suppliers`
--
ALTER TABLE `chinese_suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chinese_supplier_payments`
--
ALTER TABLE `chinese_supplier_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_payables`
--
ALTER TABLE `employee_payables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `employee_payments`
--
ALTER TABLE `employee_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `finished_goods`
--
ALTER TABLE `finished_goods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `import_purchases`
--
ALTER TABLE `import_purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `import_purchase_items`
--
ALTER TABLE `import_purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `local_purchases`
--
ALTER TABLE `local_purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `local_purchase_items`
--
ALTER TABLE `local_purchase_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `local_suppliers`
--
ALTER TABLE `local_suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `production`
--
ALTER TABLE `production`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `production_raw_materials`
--
ALTER TABLE `production_raw_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chinese_suppliers`
--
ALTER TABLE `chinese_suppliers`
  ADD CONSTRAINT `chinese_suppliers_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`);

--
-- Constraints for table `chinese_supplier_payments`
--
ALTER TABLE `chinese_supplier_payments`
  ADD CONSTRAINT `chinese_supplier_payments_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `chinese_suppliers` (`id`);

--
-- Constraints for table `employee_payments`
--
ALTER TABLE `employee_payments`
  ADD CONSTRAINT `employee_payments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`);

--
-- Constraints for table `import_purchases`
--
ALTER TABLE `import_purchases`
  ADD CONSTRAINT `import_purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `chinese_suppliers` (`id`),
  ADD CONSTRAINT `import_purchases_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`);

--
-- Constraints for table `import_purchase_items`
--
ALTER TABLE `import_purchase_items`
  ADD CONSTRAINT `import_purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `import_purchases` (`id`),
  ADD CONSTRAINT `import_purchase_items_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `raw_materials` (`id`);

--
-- Constraints for table `local_purchases`
--
ALTER TABLE `local_purchases`
  ADD CONSTRAINT `local_purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `local_suppliers` (`id`);

--
-- Constraints for table `local_purchase_items`
--
ALTER TABLE `local_purchase_items`
  ADD CONSTRAINT `local_purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `local_purchases` (`id`),
  ADD CONSTRAINT `local_purchase_items_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `raw_materials` (`id`);

--
-- Constraints for table `production`
--
ALTER TABLE `production`
  ADD CONSTRAINT `production_ibfk_1` FOREIGN KEY (`finished_good_id`) REFERENCES `finished_goods` (`id`);

--
-- Constraints for table `production_raw_materials`
--
ALTER TABLE `production_raw_materials`
  ADD CONSTRAINT `production_raw_materials_ibfk_1` FOREIGN KEY (`production_id`) REFERENCES `production` (`id`),
  ADD CONSTRAINT `production_raw_materials_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `raw_materials` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `finished_goods` (`id`);

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `local_suppliers` (`id`);
--
-- Constraints for table `customer_receipts`
--
ALTER TABLE `customer_receipts`
  ADD CONSTRAINT `customer_receipts_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `customer_receipts_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `material_issues`
--
ALTER TABLE `material_issues`
  ADD CONSTRAINT `material_issues_ibfk_1` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`);

--
-- Constraints for table `material_issue_items`
--
ALTER TABLE `material_issue_items`
  ADD CONSTRAINT `material_issue_items_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `material_issues` (`id`),
  ADD CONSTRAINT `material_issue_items_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `raw_materials` (`id`);

--
-- Constraints for table `production_orders`
--
ALTER TABLE `production_orders`
  ADD CONSTRAINT `production_orders_ibfk_1` FOREIGN KEY (`finished_good_id`) REFERENCES `finished_goods` (`id`);

--
-- Constraints for table `production_order_materials`
--
ALTER TABLE `production_order_materials`
  ADD CONSTRAINT `production_order_materials_ibfk_1` FOREIGN KEY (`production_id`) REFERENCES `production_orders` (`id`),
  ADD CONSTRAINT `production_order_materials_ibfk_2` FOREIGN KEY (`material_id`) REFERENCES `raw_materials` (`id`);

-- --------------------------------------------------------

--
-- Table structure for table `parties`
--

CREATE TABLE `parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `party_name` varchar(200) NOT NULL,
  `party_code` varchar(50) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `opening_balance` decimal(18,2) DEFAULT 0.00,
  `current_balance` decimal(18,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `party_transactions`
--

CREATE TABLE `party_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_no` varchar(50) NOT NULL,
  `party_id` int(11) NOT NULL,
  `type` enum('payable','received','paid') NOT NULL,
  `amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `transaction_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `payment_method` enum('cash','bank_transfer','cheque') DEFAULT 'cash',
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_party_id` (`party_id`),
  KEY `idx_type` (`type`),
  KEY `idx_date` (`transaction_date`),
  KEY `idx_party_type` (`party_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_settings`
--

CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(200) NOT NULL DEFAULT 'ERP System',
  `company_tagline` varchar(300) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `logo_path` varchar(300) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_settings`
--

INSERT INTO `company_settings` (`id`, `company_name`, `company_tagline`, `address`, `phone`, `email`, `website`, `logo_path`, `date_time`) VALUES
(1, 'Your Company Name', 'Manufacturing ERP System', 'Your Company Address', '+92-300-1234567', 'info@company.com', 'www.company.com', NULL, NOW());

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
