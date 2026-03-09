-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2026 at 01:18 PM
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
-- Database: `dms_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Quality Assurance', 'Department responsible for quality assurance and compliance', '2026-02-16 10:30:02', '2026-02-16 10:30:02'),
(2, 'Human Resources', 'Department managing human resources and personnel matters', '2026-02-16 10:30:02', '2026-02-16 10:30:02'),
(3, 'Production', 'Department responsible for production and manufacturing', '2026-02-16 10:30:02', '2026-02-16 10:30:02'),
(4, 'Research & Development', 'Department handling research and product development', '2026-02-16 10:30:02', '2026-02-16 10:30:02'),
(5, 'Finance', 'Department managing financial operations', '2026-02-16 10:30:02', '2026-02-16 10:30:02');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `type_id` int(11) UNSIGNED NOT NULL,
  `department_id` int(11) UNSIGNED NOT NULL,
  `status` enum('draft','active','archived') NOT NULL DEFAULT 'draft',
  `effective_date` date DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `title`, `content`, `type_id`, `department_id`, `status`, `effective_date`, `review_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'In quibusdam quia te', '<p>qwertyu89iop wertyuio sdfghjk asdfghjk sdfvbnm ertyuio</p>', 4, 3, 'draft', '2025-02-16', '2025-02-16', 3, '2026-02-16 10:38:56', '2026-02-16 10:41:01');

-- --------------------------------------------------------

--
-- Table structure for table `document_metadata`
--

CREATE TABLE `document_metadata` (
  `id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Quality Manual', 'Main quality manual containing organizational policies and procedures', '2026-02-16 10:30:02', '2026-02-16 10:30:02'),
(2, 'SOP', 'Standard Operating Procedures for various processes', '2026-02-16 10:30:02', '2026-02-16 10:30:02'),
(3, 'Policy', 'Organizational policies and guidelines', '2026-02-16 10:30:02', '2026-02-16 10:30:02'),
(4, 'Work Instruction', 'Detailed work instructions for specific tasks', '2026-02-16 10:30:02', '2026-02-16 10:30:02'),
(5, 'Form', 'Standard forms used in various processes', '2026-02-16 10:30:02', '2026-02-16 10:30:02');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '20260213000000', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1770961958, 1),
(2, '20260213000001', 'App\\Database\\Migrations\\CreateRolesTable', 'default', 'App', 1770961958, 1),
(3, '20260213000002', 'App\\Database\\Migrations\\CreatePermissionsTable', 'default', 'App', 1770961958, 1),
(4, '20260213000003', 'App\\Database\\Migrations\\CreateRolePermissionsTable', 'default', 'App', 1770961959, 1),
(5, '20260213000004', 'App\\Database\\Migrations\\CreateUserRolesTable', 'default', 'App', 1770961959, 1),
(6, '20260213000005', 'App\\Database\\Migrations\\CreateUserActivityLogsTable', 'default', 'App', 1770961959, 1),
(7, '2026-02-16-101945', 'App\\Database\\Migrations\\CreateDepartmentsTable', 'default', 'App', 1771237275, 2),
(8, '2026-02-16-101954', 'App\\Database\\Migrations\\CreateDocumentTypesTable', 'default', 'App', 1771237275, 2),
(9, '2026-02-16-102002', 'App\\Database\\Migrations\\CreateDocumentsTable', 'default', 'App', 1771237275, 2),
(10, '2026-02-16-103048', 'App\\Database\\Migrations\\CreateDocumentMetadataTable', 'default', 'App', 1771237883, 3);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) UNSIGNED NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `permission_key`, `description`, `created_at`, `updated_at`) VALUES
(15, 'user_create', 'Create users', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(16, 'user_read', 'View users', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(17, 'user_update', 'Update users', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(18, 'user_delete', 'Delete users', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(19, 'role_create', 'Create roles', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(20, 'role_read', 'View roles', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(21, 'role_update', 'Update roles', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(22, 'role_delete', 'Delete roles', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(23, 'permission_create', 'Create permissions', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(24, 'permission_read', 'View permissions', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(25, 'permission_update', 'Update permissions', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(26, 'permission_delete', 'Delete permissions', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(27, 'dashboard_access', 'Access dashboard', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(28, 'reports_view', 'View reports', '2026-02-16 10:32:10', '2026-02-16 10:32:10'),
(50, 'document_create', 'Allow creating new documents', '2026-02-16 10:25:00', '2026-02-16 10:25:00'),
(51, 'document_read', 'Allow viewing documents', '2026-02-16 10:25:00', '2026-02-16 10:25:00'),
(52, 'document_update', 'Allow editing existing documents', '2026-02-16 10:25:00', '2026-02-16 10:25:00'),
(53, 'document_delete', 'Allow deleting documents', '2026-02-16 10:25:00', '2026-02-16 10:25:00'),
(54, 'department_create', 'Allow creating new departments', '2026-02-16 10:29:00', '2026-02-16 10:29:00'),
(55, 'department_read', 'Allow viewing departments', '2026-02-16 10:29:00', '2026-02-16 10:29:00'),
(56, 'department_update', 'Allow editing existing departments', '2026-02-16 10:29:00', '2026-02-16 10:29:00'),
(57, 'department_delete', 'Allow deleting departments', '2026-02-16 10:29:00', '2026-02-16 10:29:00'),
(58, 'document_type_create', 'Allow creating new document types', '2026-02-16 10:29:12', '2026-02-16 10:29:12'),
(59, 'document_type_read', 'Allow viewing document types', '2026-02-16 10:29:12', '2026-02-16 10:29:12'),
(60, 'document_type_update', 'Allow editing existing document types', '2026-02-16 10:29:12', '2026-02-16 10:29:12'),
(61, 'document_type_delete', 'Allow deleting document types', '2026-02-16 10:29:12', '2026-02-16 10:29:12');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'Superadmin panel with full system access', '2026-02-16 10:30:35', '2026-02-16 10:45:10'),
(2, 'admin', 'Quality Head / QA Head with administrative privileges', '2026-02-16 10:30:35', '2026-02-16 09:50:15'),
(3, 'lab_manager', 'Lab Manager panel with lab-specific controls', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(4, 'reviewer', 'Reviewer panel for document and process review', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(5, 'approver', 'Approver panel for final approvals and sign-offs', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(6, 'user_staff1', 'User panel - Staff Level 1 (Analyst/Technician)', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(7, 'user_staff2', 'User panel - Staff Level 2 (Support Staff)', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(8, 'auditor', 'Auditor panel (internal/external) with read-only oversight', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(9, 'Test', 'Tester', '2026-02-16 05:43:14', '2026-02-16 06:37:16');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) UNSIGNED NOT NULL,
  `permission_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(1, 27),
(1, 28),
(1, 50),
(1, 51),
(1, 52),
(1, 53),
(1, 54),
(1, 55),
(1, 56),
(1, 57),
(1, 58),
(1, 59),
(1, 60),
(1, 61),
(2, 16),
(2, 17),
(2, 20),
(2, 21),
(2, 27),
(2, 28),
(3, 16),
(3, 27),
(3, 28),
(4, 24),
(4, 27),
(4, 28),
(5, 24),
(5, 25),
(5, 27),
(5, 28),
(6, 16),
(6, 27),
(6, 28),
(7, 27),
(8, 16),
(8, 20),
(8, 24),
(8, 27),
(8, 28),
(9, 16),
(9, 20),
(9, 24),
(9, 28);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$TisSJeLR/WQLzmLEHHJ70.9.SYs.i.JmyWRhRkLU8Ipx.tRu27SqS', 'admin@gmail.com', 'active', '2026-02-13 15:23:42', '2026-02-16 09:58:23'),
(2, 'user', '$2y$10$NI/HQAyZW2oi9sONsyliL.8eVzHHz3F6XOf8.ZqdB8g3NNLUI.sXu', 'user@gmail.com', 'active', '2026-02-13 09:58:05', '2026-02-13 09:58:05'),
(3, 'superadmin', '$2y$10$R/4nQCZDvucuWTn97ptYnuaCdza2IZODMGWXUVtAzEI6Lcfxh/fUi', 'superadmin@gmail.com', 'active', '2026-02-16 05:05:58', '2026-02-16 05:10:39'),
(7, 'tester', '$2y$10$BX2U2GCTai3VFbSQgD9BtO/BKzbdRTcHbxXbB6f2DKWdzwRIhs1yy', 'tester@gmail.com', 'active', '2026-02-16 05:44:05', '2026-02-16 05:44:05'),
(9, 'staff', '$2y$10$8arUe4/mElHNVIPLKHSB5usNM.Lkc.a6EWW2S71A/q3UqkyKH0V8O', 'duqiful@mailinator.com', 'active', '2026-02-16 09:38:13', '2026-02-16 09:38:13'),
(10, 'pevuceviqu', '$2y$10$2E1LacJt7q7cWl94q77Mi.1D.Kns03eMOoCHCluOqRN3oBYDq1lV.', 'vurud@mailinator.com', 'inactive', '2026-02-16 10:00:56', '2026-02-16 10:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_logs`
--

CREATE TABLE `user_activity_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity_logs`
--

INSERT INTO `user_activity_logs` (`id`, `user_id`, `action`, `timestamp`, `ip_address`, `details`) VALUES
(17, 1, 'User logged in', '2026-02-13 09:57:15', '::1', 'Login successful from IP: ::1'),
(18, 1, 'Accessed dashboard', '2026-02-13 09:57:15', '::1', NULL),
(19, 1, 'Viewed user list', '2026-02-13 09:57:20', '::1', NULL),
(20, 1, 'Created user', '2026-02-13 09:58:05', '::1', 'Created user: user'),
(21, 1, 'Viewed user list', '2026-02-13 09:58:05', '::1', NULL),
(22, 2, 'User logged in', '2026-02-13 10:03:34', '::1', 'Login successful from IP: ::1'),
(23, 2, 'Accessed dashboard', '2026-02-13 10:03:34', '::1', NULL),
(24, 2, 'Viewed user list', '2026-02-13 10:03:38', '::1', NULL),
(25, 1, 'Viewed roles list', '2026-02-13 10:03:56', '::1', NULL),
(26, 1, 'Viewed user list', '2026-02-13 11:24:16', '::1', NULL),
(27, 1, 'Accessed dashboard', '2026-02-13 11:37:13', '::1', NULL),
(28, 1, 'Accessed dashboard', '2026-02-13 11:40:06', '::1', NULL),
(29, 1, 'Accessed dashboard', '2026-02-13 11:40:42', '::1', NULL),
(30, 1, 'Accessed dashboard', '2026-02-13 11:50:28', '::1', NULL),
(31, 1, 'Viewed user list', '2026-02-13 11:58:11', '::1', NULL),
(32, 1, 'Viewed user list', '2026-02-13 12:02:37', '::1', NULL),
(33, 1, 'Viewed user list', '2026-02-13 12:03:00', '::1', NULL),
(34, 1, 'Viewed user list', '2026-02-13 12:04:16', '::1', NULL),
(35, 1, 'Viewed user list', '2026-02-13 12:04:19', '::1', NULL),
(36, 1, 'Viewed user list', '2026-02-13 12:06:43', '::1', NULL),
(37, 1, 'Viewed user list', '2026-02-13 12:07:30', '::1', NULL),
(38, 1, 'Viewed user list', '2026-02-13 12:07:44', '::1', NULL),
(39, 1, 'Viewed user list', '2026-02-13 12:10:43', '::1', NULL),
(40, 1, 'Viewed user list', '2026-02-13 12:11:13', '::1', NULL),
(41, 1, 'Viewed user list', '2026-02-13 12:12:08', '::1', NULL),
(42, 1, 'Viewed user list', '2026-02-13 12:12:16', '::1', NULL),
(43, 1, 'Viewed user list', '2026-02-13 12:12:32', '::1', NULL),
(44, 1, 'Viewed user list', '2026-02-13 12:13:28', '::1', NULL),
(45, 1, 'Viewed user list', '2026-02-13 12:14:01', '::1', NULL),
(46, 1, 'Accessed dashboard', '2026-02-13 12:19:58', '::1', NULL),
(47, 1, 'Accessed dashboard', '2026-02-13 12:20:13', '::1', NULL),
(48, 1, 'Accessed dashboard', '2026-02-13 12:22:57', '::1', NULL),
(49, 1, 'Accessed dashboard', '2026-02-13 12:24:12', '::1', NULL),
(50, 1, 'Accessed dashboard', '2026-02-13 12:25:00', '::1', NULL),
(51, 1, 'Accessed dashboard', '2026-02-13 12:30:37', '::1', NULL),
(52, 1, 'Accessed dashboard', '2026-02-13 12:31:43', '::1', NULL),
(53, 1, 'Viewed user list', '2026-02-13 12:31:53', '::1', NULL),
(54, 1, 'Accessed dashboard', '2026-02-13 12:31:57', '::1', NULL),
(55, 1, 'Accessed dashboard', '2026-02-13 12:39:43', '::1', NULL),
(56, 1, 'Accessed dashboard', '2026-02-13 12:39:50', '::1', NULL),
(57, 1, 'Accessed dashboard', '2026-02-13 12:40:15', '::1', NULL),
(58, 1, 'Accessed dashboard', '2026-02-13 12:44:49', '::1', NULL),
(59, 1, 'Viewed user list', '2026-02-13 12:46:22', '::1', NULL),
(60, 1, 'Viewed user list', '2026-02-13 12:46:31', '::1', NULL),
(61, 1, 'Viewed user list', '2026-02-13 12:47:46', '::1', NULL),
(62, 1, 'Viewed user list', '2026-02-13 12:48:20', '::1', NULL),
(63, 1, 'Viewed user list', '2026-02-13 12:48:23', '::1', NULL),
(64, 1, 'Viewed user list', '2026-02-13 12:49:01', '::1', NULL),
(65, 1, 'Accessed dashboard', '2026-02-13 12:49:04', '::1', NULL),
(66, 1, 'Accessed dashboard', '2026-02-13 12:50:43', '::1', NULL),
(67, 1, 'Accessed dashboard', '2026-02-13 12:51:04', '::1', NULL),
(68, 1, 'Accessed dashboard', '2026-02-13 12:51:29', '::1', NULL),
(69, 1, 'Accessed dashboard', '2026-02-13 12:51:31', '::1', NULL),
(70, 1, 'Accessed dashboard', '2026-02-13 12:51:31', '::1', NULL),
(71, 1, 'Accessed dashboard', '2026-02-13 12:51:32', '::1', NULL),
(72, 1, 'Viewed user list', '2026-02-13 12:51:35', '::1', NULL),
(73, 1, 'Viewed user list', '2026-02-13 12:51:52', '::1', NULL),
(74, 1, 'Viewed user list', '2026-02-13 12:51:53', '::1', NULL),
(75, 1, 'Viewed user list', '2026-02-13 12:51:53', '::1', NULL),
(76, 1, 'Viewed user list', '2026-02-13 12:51:53', '::1', NULL),
(77, 1, 'Viewed user list', '2026-02-13 12:51:53', '::1', NULL),
(78, 1, 'Accessed dashboard', '2026-02-13 12:51:58', '::1', NULL),
(79, 1, 'Accessed dashboard', '2026-02-13 12:52:03', '::1', NULL),
(80, 1, 'Accessed dashboard', '2026-02-13 12:52:15', '::1', NULL),
(81, 1, 'Accessed dashboard', '2026-02-13 12:52:17', '::1', NULL),
(82, 1, 'Accessed dashboard', '2026-02-13 12:53:13', '::1', NULL),
(83, 1, 'Accessed dashboard', '2026-02-13 12:53:14', '::1', NULL),
(84, 1, 'Accessed dashboard', '2026-02-13 12:53:30', '::1', NULL),
(85, 1, 'Accessed dashboard', '2026-02-13 12:53:44', '::1', NULL),
(86, 1, 'Accessed dashboard', '2026-02-13 12:54:38', '::1', NULL),
(87, 1, 'Viewed user list', '2026-02-13 12:54:42', '::1', NULL),
(88, 1, 'Accessed dashboard', '2026-02-13 12:54:57', '::1', NULL),
(89, 1, 'Viewed user list', '2026-02-13 12:55:04', '::1', NULL),
(90, 1, 'Viewed user list', '2026-02-13 12:55:13', '::1', NULL),
(91, 1, 'Accessed dashboard', '2026-02-13 12:55:18', '::1', NULL),
(92, 1, 'Accessed dashboard', '2026-02-13 12:55:27', '::1', NULL),
(93, 1, 'Accessed dashboard', '2026-02-13 12:56:00', '::1', NULL),
(94, 1, 'Accessed dashboard', '2026-02-13 12:56:21', '::1', NULL),
(95, 1, 'Accessed dashboard', '2026-02-13 12:58:21', '::1', NULL),
(96, 1, 'Accessed dashboard', '2026-02-13 12:58:46', '::1', NULL),
(97, 1, 'Accessed dashboard', '2026-02-13 12:59:09', '::1', NULL),
(98, 1, 'Accessed dashboard', '2026-02-13 12:59:27', '::1', NULL),
(99, 1, 'User logged in', '2026-02-16 04:17:43', '::1', 'Login successful from IP: ::1'),
(100, 1, 'Accessed dashboard', '2026-02-16 04:17:43', '::1', NULL),
(101, 1, 'Accessed dashboard', '2026-02-16 04:20:53', '::1', NULL),
(102, 1, 'Viewed user list', '2026-02-16 04:21:02', '::1', NULL),
(103, 1, 'Accessed dashboard', '2026-02-16 04:24:13', '::1', NULL),
(104, 1, 'Accessed dashboard', '2026-02-16 04:41:43', '::1', NULL),
(105, 1, 'Viewed user list', '2026-02-16 04:44:53', '::1', NULL),
(106, 1, 'Created user', '2026-02-16 05:05:59', '::1', 'Created user: superadmin'),
(107, 1, 'Viewed user list', '2026-02-16 05:05:59', '::1', NULL),
(108, 3, 'User logged in', '2026-02-16 05:09:18', '::1', 'Login successful from IP: ::1'),
(109, 3, 'Accessed dashboard', '2026-02-16 05:09:18', '::1', NULL),
(110, 3, 'Viewed user list', '2026-02-16 05:09:21', '::1', NULL),
(111, 1, 'Viewed user list', '2026-02-16 05:09:43', '::1', NULL),
(112, 1, 'Viewed user list', '2026-02-16 05:10:02', '::1', NULL),
(113, 1, 'Updated user', '2026-02-16 05:10:39', '::1', 'Updated user: superadmin'),
(114, 1, 'Viewed user list', '2026-02-16 05:10:39', '::1', NULL),
(115, 1, 'Viewed user list', '2026-02-16 05:11:22', '::1', NULL),
(116, 1, 'Created user', '2026-02-16 05:11:34', '::1', 'Created user: kudycehol'),
(117, 1, 'Viewed user list', '2026-02-16 05:11:34', '::1', NULL),
(118, 1, 'Updated user', '2026-02-16 05:11:44', '::1', 'Updated user: kudycehol'),
(119, 1, 'Viewed user list', '2026-02-16 05:11:44', '::1', NULL),
(120, 1, 'Created user', '2026-02-16 05:11:54', '::1', 'Created user: jykulujyju'),
(121, 1, 'Viewed user list', '2026-02-16 05:11:54', '::1', NULL),
(122, 1, 'Deleted user', '2026-02-16 05:12:00', '::1', 'Deleted user: jykulujyju'),
(123, 1, 'Viewed user list', '2026-02-16 05:12:00', '::1', NULL),
(124, 1, 'Deleted user', '2026-02-16 05:12:05', '::1', 'Deleted user: kudycehol'),
(125, 1, 'Viewed user list', '2026-02-16 05:12:05', '::1', NULL),
(126, 1, 'Viewed user list', '2026-02-16 05:14:23', '::1', NULL),
(127, 1, 'Created user', '2026-02-16 05:14:33', '::1', 'Created user: gitupyvy'),
(128, 1, 'Viewed user list', '2026-02-16 05:14:33', '::1', NULL),
(129, 1, 'Deleted user', '2026-02-16 05:17:02', '::1', 'Deleted user: gitupyvy'),
(130, 1, 'Viewed user list', '2026-02-16 05:17:02', '::1', NULL),
(131, 1, 'Viewed user list', '2026-02-16 05:17:07', '::1', NULL),
(132, 1, 'Viewed user list', '2026-02-16 05:17:31', '::1', NULL),
(133, 1, 'Accessed dashboard', '2026-02-16 05:17:46', '::1', NULL),
(134, 1, 'Viewed user list', '2026-02-16 05:17:52', '::1', NULL),
(135, 1, 'Viewed user list', '2026-02-16 05:29:27', '::1', NULL),
(136, 1, 'Viewed user list', '2026-02-16 05:30:22', '::1', NULL),
(137, 1, 'Viewed user list', '2026-02-16 05:30:23', '::1', NULL),
(138, 3, 'User logged in', '2026-02-16 05:31:02', '::1', 'Login successful from IP: ::1'),
(139, 3, 'Accessed dashboard', '2026-02-16 05:31:02', '::1', NULL),
(140, 3, 'Viewed user list', '2026-02-16 05:31:17', '::1', NULL),
(141, 3, 'Accessed dashboard', '2026-02-16 05:31:19', '::1', NULL),
(142, 3, 'Viewed user list', '2026-02-16 05:31:22', '::1', NULL),
(143, 3, 'Viewed user list', '2026-02-16 05:31:45', '::1', NULL),
(144, 3, 'Accessed dashboard', '2026-02-16 05:31:46', '::1', NULL),
(145, 3, 'Viewed user list', '2026-02-16 05:31:49', '::1', NULL),
(146, 3, 'Viewed user list', '2026-02-16 05:37:02', '::1', NULL),
(147, 3, 'Viewed user list', '2026-02-16 05:37:28', '::1', NULL),
(148, 3, 'Viewed roles list', '2026-02-16 05:37:30', '::1', NULL),
(149, 3, 'Created role', '2026-02-16 05:43:14', '::1', 'Created role: Test'),
(150, 3, 'Viewed roles list', '2026-02-16 05:43:14', '::1', NULL),
(151, 3, 'Viewed user list', '2026-02-16 05:43:37', '::1', NULL),
(152, 3, 'Created user', '2026-02-16 05:44:05', '::1', 'Created user: tester'),
(153, 3, 'Viewed user list', '2026-02-16 05:44:05', '::1', NULL),
(154, 7, 'User logged in', '2026-02-16 05:44:49', '::1', 'Login successful from IP: ::1'),
(155, 7, 'Accessed dashboard', '2026-02-16 05:44:49', '::1', NULL),
(156, 7, 'Viewed user list', '2026-02-16 05:44:55', '::1', NULL),
(157, 7, 'Viewed roles list', '2026-02-16 05:45:12', '::1', NULL),
(158, 7, 'Created role', '2026-02-16 05:45:37', '::1', 'Created role: sdds'),
(159, 7, 'Viewed roles list', '2026-02-16 05:45:37', '::1', NULL),
(160, 7, 'Deleted role', '2026-02-16 05:46:52', '::1', 'Deleted role: sdds'),
(161, 7, 'Viewed roles list', '2026-02-16 05:46:52', '::1', NULL),
(162, 3, 'Viewed user list', '2026-02-16 05:46:59', '::1', NULL),
(163, 3, 'Viewed roles list', '2026-02-16 05:47:01', '::1', NULL),
(164, 3, 'Viewed user list', '2026-02-16 05:47:32', '::1', NULL),
(165, 3, 'Viewed roles list', '2026-02-16 05:47:36', '::1', NULL),
(166, 3, 'Viewed user list', '2026-02-16 05:47:45', '::1', NULL),
(167, 3, 'Updated user', '2026-02-16 05:47:55', '::1', 'Updated user: admin'),
(168, 3, 'Viewed user list', '2026-02-16 05:47:55', '::1', NULL),
(169, 3, 'Viewed roles list', '2026-02-16 05:48:00', '::1', NULL),
(170, 3, 'Updated role', '2026-02-16 06:37:02', '::1', 'Updated role: Test'),
(171, 3, 'Updated role', '2026-02-16 06:37:16', '::1', 'Updated role: Test'),
(172, 3, 'Viewed user list', '2026-02-16 06:38:52', '::1', NULL),
(173, 3, 'Created role', '2026-02-16 06:39:57', '::1', 'Created role: theodore'),
(174, 7, 'Deleted role', '2026-02-16 06:40:07', '::1', 'Deleted role: theodore'),
(175, 3, 'Viewed user list', '2026-02-16 07:15:54', '::1', NULL),
(176, 3, 'Created role', '2026-02-16 08:03:42', '::1', 'Created role: Allegra Garza'),
(177, 3, 'Updated role', '2026-02-16 08:03:52', '::1', 'Updated role: Allegra Garza'),
(178, 3, 'Deleted role', '2026-02-16 08:03:57', '::1', 'Deleted role: Allegra Garza'),
(179, 3, 'Viewed user list', '2026-02-16 08:10:47', '::1', NULL),
(180, 3, 'Created role', '2026-02-16 08:11:01', '::1', 'Created role: Emery Frost'),
(181, 3, 'Created user', '2026-02-16 08:11:09', '::1', 'Created user: hepiqi'),
(182, 3, 'Viewed user list', '2026-02-16 08:11:09', '::1', NULL),
(183, 7, 'Deleted role', '2026-02-16 08:11:28', '::1', 'Deleted role: Emery Frost'),
(184, 7, 'Viewed user list', '2026-02-16 08:20:34', '::1', NULL),
(185, 7, 'Accessed dashboard', '2026-02-16 08:20:37', '::1', NULL),
(186, 7, 'Viewed user list', '2026-02-16 08:20:39', '::1', NULL),
(187, 7, 'Viewed user list', '2026-02-16 08:22:54', '::1', NULL),
(188, 3, 'Viewed user list', '2026-02-16 08:27:00', '::1', 'Viewed user list'),
(189, 3, 'Viewed user list', '2026-02-16 08:27:45', '::1', 'Viewed user list'),
(190, 3, 'Viewed user list', '2026-02-16 08:27:47', '::1', 'Viewed user list'),
(191, 3, 'Viewed user list', '2026-02-16 08:31:30', '::1', 'Viewed user list'),
(192, 3, 'Viewed user list', '2026-02-16 08:32:46', '::1', 'Viewed user list'),
(193, 3, 'Accessed dashboard', '2026-02-16 08:33:14', '::1', NULL),
(194, 3, 'Viewed user list', '2026-02-16 08:33:16', '::1', 'Viewed user list'),
(195, 7, 'Viewed user list', '2026-02-16 08:33:23', '::1', 'Viewed user list'),
(196, 7, 'Viewed user list', '2026-02-16 08:33:27', '::1', 'Viewed user list'),
(197, 7, 'Viewed user list', '2026-02-16 08:33:43', '::1', 'Viewed user list'),
(198, 3, 'Viewed user list', '2026-02-16 08:33:52', '::1', 'Viewed user list'),
(199, 3, 'Created role', '2026-02-16 08:40:10', '::1', 'Created role: edit_roles'),
(200, 3, 'Deleted role', '2026-02-16 08:40:30', '::1', 'Deleted role: edit_roles'),
(201, 3, 'Viewed user list', '2026-02-16 08:53:11', '::1', 'Viewed user list'),
(202, 3, 'Viewed user list', '2026-02-16 08:54:51', '::1', 'Viewed user list'),
(203, 3, 'Accessed dashboard', '2026-02-16 08:54:58', '::1', NULL),
(204, 3, 'Viewed user list', '2026-02-16 08:55:00', '::1', 'Viewed user list'),
(205, 3, 'Viewed user list', '2026-02-16 08:55:35', '::1', 'Viewed user list'),
(206, 3, 'Viewed permissions list', '2026-02-16 08:55:37', '::1', NULL),
(207, 3, 'Viewed roles list', '2026-02-16 08:55:45', '::1', NULL),
(208, 3, 'Accessed dashboard', '2026-02-16 08:55:52', '::1', NULL),
(209, 3, 'Viewed user list', '2026-02-16 08:55:53', '::1', 'Viewed user list'),
(210, 3, 'Viewed permissions list', '2026-02-16 08:55:55', '::1', NULL),
(211, 3, 'Viewed roles list', '2026-02-16 08:55:58', '::1', NULL),
(212, 3, 'Viewed permissions list', '2026-02-16 08:57:27', '::1', NULL),
(213, 3, 'Viewed permissions list', '2026-02-16 08:57:32', '::1', NULL),
(214, 3, 'Viewed permissions list', '2026-02-16 08:57:34', '::1', NULL),
(215, 3, 'Viewed permissions list', '2026-02-16 08:57:35', '::1', NULL),
(216, 3, 'Viewed permissions list', '2026-02-16 08:58:18', '::1', NULL),
(217, 3, 'Created permission', '2026-02-16 08:59:02', '::1', 'Created permission: edit_role'),
(218, 3, 'Viewed permissions list', '2026-02-16 08:59:02', '::1', NULL),
(219, 3, 'Viewed permissions list', '2026-02-16 09:00:30', '::1', NULL),
(220, 3, 'Deleted permission', '2026-02-16 09:00:44', '::1', 'Deleted permission: edit_role'),
(221, 3, 'Viewed permissions list', '2026-02-16 09:00:44', '::1', NULL),
(222, 3, 'Viewed roles list', '2026-02-16 09:03:00', '::1', NULL),
(223, 3, 'Updated role', '2026-02-16 09:03:24', '::1', 'Updated role: superadmin'),
(224, 3, 'Viewed roles list', '2026-02-16 09:03:24', '::1', NULL),
(225, 7, 'Accessed dashboard', '2026-02-16 09:03:30', '::1', NULL),
(226, 7, 'Viewed user list', '2026-02-16 09:03:32', '::1', 'Viewed user list'),
(227, 7, 'Viewed roles list', '2026-02-16 09:03:33', '::1', NULL),
(228, 7, 'Viewed permissions list', '2026-02-16 09:03:35', '::1', NULL),
(229, 7, 'Viewed user list', '2026-02-16 09:03:39', '::1', 'Viewed user list'),
(230, 7, 'Viewed user list', '2026-02-16 09:03:44', '::1', 'Viewed user list'),
(231, 7, 'Viewed user list', '2026-02-16 09:03:45', '::1', 'Viewed user list'),
(232, 7, 'Viewed permissions list', '2026-02-16 09:03:46', '::1', NULL),
(233, 7, 'Viewed user list', '2026-02-16 09:03:47', '::1', 'Viewed user list'),
(234, 7, 'Viewed user list', '2026-02-16 09:03:49', '::1', 'Viewed user list'),
(235, 7, 'Viewed roles list', '2026-02-16 09:03:50', '::1', NULL),
(236, 7, 'Viewed permissions list', '2026-02-16 09:03:51', '::1', NULL),
(237, 7, 'Viewed user list', '2026-02-16 09:03:52', '::1', 'Viewed user list'),
(238, 7, 'Viewed user list', '2026-02-16 09:03:54', '::1', 'Viewed user list'),
(239, 3, 'Accessed dashboard', '2026-02-16 09:05:49', '::1', NULL),
(240, 3, 'Viewed user list', '2026-02-16 09:05:51', '::1', 'Viewed user list'),
(241, 7, 'Viewed user list', '2026-02-16 09:06:15', '::1', 'Viewed user list'),
(242, 7, 'Viewed user list', '2026-02-16 09:06:22', '::1', 'Viewed user list'),
(243, 7, 'Viewed user list', '2026-02-16 09:06:26', '::1', 'Viewed user list'),
(244, 7, 'Viewed user list', '2026-02-16 09:06:28', '::1', 'Viewed user list'),
(245, 7, 'Viewed user list', '2026-02-16 09:06:29', '::1', 'Viewed user list'),
(246, 7, 'Viewed user list', '2026-02-16 09:06:30', '::1', 'Viewed user list'),
(247, 7, 'Viewed user list', '2026-02-16 09:06:31', '::1', 'Viewed user list'),
(248, 7, 'Viewed user list', '2026-02-16 09:06:39', '::1', 'Viewed user list'),
(249, 7, 'Viewed roles list', '2026-02-16 09:06:46', '::1', NULL),
(250, 3, 'Viewed user list', '2026-02-16 09:07:33', '::1', 'Viewed user list'),
(251, 3, 'Viewed permissions list', '2026-02-16 09:07:46', '::1', NULL),
(252, 3, 'Viewed permissions list', '2026-02-16 09:07:54', '::1', NULL),
(253, 3, 'Viewed permissions list', '2026-02-16 09:08:29', '::1', NULL),
(254, 3, 'Viewed roles list', '2026-02-16 09:08:31', '::1', NULL),
(255, 3, 'Viewed permissions list', '2026-02-16 09:08:35', '::1', NULL),
(256, 3, 'Viewed permissions list', '2026-02-16 09:09:18', '::1', NULL),
(257, 3, 'Accessed dashboard', '2026-02-16 09:09:21', '::1', NULL),
(258, 3, 'Viewed user list', '2026-02-16 09:09:22', '::1', 'Viewed user list'),
(259, 3, 'Viewed roles list', '2026-02-16 09:09:24', '::1', NULL),
(260, 3, 'Viewed permissions list', '2026-02-16 09:09:26', '::1', NULL),
(261, 3, 'Accessed dashboard', '2026-02-16 09:09:28', '::1', NULL),
(262, 3, 'Viewed user list', '2026-02-16 09:09:29', '::1', 'Viewed user list'),
(263, 3, 'Viewed user list', '2026-02-16 09:09:35', '::1', 'Viewed user list'),
(264, 3, 'Viewed roles list', '2026-02-16 09:09:38', '::1', NULL),
(265, 7, 'Accessed dashboard', '2026-02-16 09:09:44', '::1', NULL),
(266, 7, 'Viewed user list', '2026-02-16 09:09:45', '::1', 'Viewed user list'),
(267, 7, 'Viewed roles list', '2026-02-16 09:09:46', '::1', NULL),
(268, 7, 'Viewed permissions list', '2026-02-16 09:09:47', '::1', NULL),
(269, 7, 'Accessed dashboard', '2026-02-16 09:09:50', '::1', NULL),
(270, 7, 'Viewed user list', '2026-02-16 09:09:51', '::1', 'Viewed user list'),
(271, 7, 'Viewed user list', '2026-02-16 09:09:53', '::1', 'Viewed user list'),
(272, 7, 'Viewed roles list', '2026-02-16 09:09:55', '::1', NULL),
(273, 7, 'Viewed roles list', '2026-02-16 09:09:56', '::1', NULL),
(274, 7, 'Viewed permissions list', '2026-02-16 09:09:58', '::1', NULL),
(275, 7, 'Viewed permissions list', '2026-02-16 09:10:00', '::1', NULL),
(276, 3, 'Viewed user list', '2026-02-16 09:21:34', '::1', 'Viewed user list'),
(277, 7, 'Accessed dashboard', '2026-02-16 09:22:36', '::1', NULL),
(278, 7, 'Viewed user list', '2026-02-16 09:22:37', '::1', 'Viewed user list'),
(279, 7, 'Viewed user list', '2026-02-16 09:22:40', '::1', 'Viewed user list'),
(280, 7, 'Viewed roles list', '2026-02-16 09:22:41', '::1', NULL),
(281, 7, 'Viewed roles list', '2026-02-16 09:22:44', '::1', NULL),
(282, 7, 'Viewed permissions list', '2026-02-16 09:22:48', '::1', NULL),
(283, 7, 'Viewed permissions list', '2026-02-16 09:22:50', '::1', NULL),
(284, 7, 'Viewed permissions list', '2026-02-16 09:22:54', '::1', NULL),
(285, 3, 'Viewed user list', '2026-02-16 09:25:28', '::1', 'Viewed user list'),
(286, 3, 'Viewed user list', '2026-02-16 09:25:31', '::1', 'Viewed user list'),
(287, 3, 'Viewed roles list', '2026-02-16 09:25:33', '::1', NULL),
(288, 3, 'Viewed roles list', '2026-02-16 09:25:38', '::1', NULL),
(289, 3, 'Viewed permissions list', '2026-02-16 09:25:40', '::1', NULL),
(290, 3, 'Viewed permissions list', '2026-02-16 09:25:42', '::1', NULL),
(291, 3, 'Viewed user list', '2026-02-16 09:25:56', '::1', 'Viewed user list'),
(292, 3, 'Viewed user list', '2026-02-16 09:33:45', '::1', 'Viewed user list'),
(293, 7, 'Viewed roles list', '2026-02-16 09:33:55', '::1', NULL),
(294, 7, 'Viewed roles list', '2026-02-16 09:34:57', '::1', NULL),
(295, 7, 'Viewed roles list', '2026-02-16 09:35:25', '::1', NULL),
(296, 7, 'Viewed roles list', '2026-02-16 09:35:29', '::1', NULL),
(297, 7, 'Accessed dashboard', '2026-02-16 09:37:14', '::1', NULL),
(298, 7, 'Viewed user list', '2026-02-16 09:37:16', '::1', 'Viewed user list'),
(299, 7, 'Viewed user list', '2026-02-16 09:37:26', '::1', 'Viewed user list'),
(300, 3, 'Deleted user', '2026-02-16 09:37:34', '::1', 'Deleted user: hepiqi'),
(301, 3, 'Viewed user list', '2026-02-16 09:37:34', '::1', 'Viewed user list'),
(302, 3, 'Viewed roles list', '2026-02-16 09:37:38', '::1', NULL),
(303, 3, 'Viewed user list', '2026-02-16 09:37:47', '::1', 'Viewed user list'),
(304, 3, 'Created user', '2026-02-16 09:38:13', '::1', 'Created user: staff'),
(305, 3, 'Viewed user list', '2026-02-16 09:38:14', '::1', 'Viewed user list'),
(306, 7, 'Viewed user list', '2026-02-16 09:38:25', '::1', 'Viewed user list'),
(307, 9, 'User logged in', '2026-02-16 09:39:58', '::1', 'Login successful from IP: ::1'),
(308, 9, 'Accessed dashboard', '2026-02-16 09:39:58', '::1', NULL),
(309, 9, 'Viewed roles list', '2026-02-16 09:40:01', '::1', NULL),
(310, 9, 'Viewed user list', '2026-02-16 09:40:01', '::1', 'Viewed user list'),
(311, 9, 'Viewed roles list', '2026-02-16 09:40:04', '::1', NULL),
(312, 9, 'Viewed user list', '2026-02-16 09:40:09', '::1', 'Viewed user list'),
(313, 9, 'Viewed roles list', '2026-02-16 09:40:11', '::1', NULL),
(314, 9, 'Viewed permissions list', '2026-02-16 09:40:25', '::1', NULL),
(315, 9, 'Viewed permissions list', '2026-02-16 09:40:42', '::1', NULL),
(316, 9, 'Viewed permissions list', '2026-02-16 09:40:45', '::1', NULL),
(317, 9, 'Viewed roles list', '2026-02-16 09:41:01', '::1', NULL),
(318, 9, 'Viewed user list', '2026-02-16 09:41:08', '::1', 'Viewed user list'),
(319, 9, 'Accessed dashboard', '2026-02-16 09:41:22', '::1', NULL),
(320, 9, 'Viewed user list', '2026-02-16 09:41:24', '::1', 'Viewed user list'),
(321, 9, 'Viewed roles list', '2026-02-16 09:41:44', '::1', NULL),
(322, 9, 'Viewed permissions list', '2026-02-16 09:41:45', '::1', NULL),
(323, 9, 'Viewed user list', '2026-02-16 09:41:52', '::1', 'Viewed user list'),
(324, 3, 'Viewed user list', '2026-02-16 09:47:50', '::1', 'Viewed user list'),
(325, 9, 'Viewed user list', '2026-02-16 09:47:56', '::1', 'Viewed user list'),
(326, 1, 'User logged in', '2026-02-16 09:48:11', '::1', 'Login successful from IP: ::1'),
(327, 1, 'Accessed dashboard', '2026-02-16 09:48:11', '::1', NULL),
(328, 1, 'Viewed user list', '2026-02-16 09:48:21', '::1', 'Viewed user list'),
(329, 1, 'Viewed roles list', '2026-02-16 09:48:25', '::1', NULL),
(330, 1, 'Viewed permissions list', '2026-02-16 09:48:27', '::1', NULL),
(331, 1, 'Viewed permissions list', '2026-02-16 09:48:39', '::1', NULL),
(332, 1, 'Viewed roles list', '2026-02-16 09:48:44', '::1', NULL),
(333, 1, 'Viewed user list', '2026-02-16 09:48:46', '::1', 'Viewed user list'),
(334, 1, 'Viewed permissions list', '2026-02-16 09:49:36', '::1', NULL),
(335, 1, 'Viewed roles list', '2026-02-16 09:49:41', '::1', NULL),
(336, 1, 'Updated role', '2026-02-16 09:49:55', '::1', 'Updated role: admin'),
(337, 1, 'Viewed roles list', '2026-02-16 09:49:56', '::1', NULL),
(338, 1, 'Viewed roles list', '2026-02-16 09:49:59', '::1', NULL),
(339, 1, 'Updated role', '2026-02-16 09:50:15', '::1', 'Updated role: admin'),
(340, 1, 'Viewed roles list', '2026-02-16 09:50:15', '::1', NULL),
(341, 3, 'Viewed user list', '2026-02-16 09:50:21', '::1', 'Viewed user list'),
(342, 3, 'Viewed permissions list', '2026-02-16 09:50:44', '::1', NULL),
(343, 3, 'Viewed permissions list', '2026-02-16 09:51:00', '::1', NULL),
(344, 3, 'Viewed user list', '2026-02-16 09:51:44', '::1', 'Viewed user list'),
(345, 3, 'Updated user', '2026-02-16 09:52:07', '::1', 'Updated user: admin'),
(346, 3, 'Viewed user list', '2026-02-16 09:52:07', '::1', 'Viewed user list'),
(347, 1, 'Viewed roles list', '2026-02-16 09:52:12', '::1', NULL),
(348, 1, 'Viewed user list', '2026-02-16 09:52:14', '::1', 'Viewed user list'),
(349, 1, 'Accessed dashboard', '2026-02-16 09:52:16', '::1', NULL),
(350, 1, 'Login attempt failed', '2026-02-16 09:52:31', '::1', 'Account disabled'),
(351, 3, 'Viewed user list', '2026-02-16 09:57:41', '::1', 'Viewed user list'),
(352, 3, 'Viewed roles list', '2026-02-16 09:58:04', '::1', NULL),
(353, 3, 'Viewed user list', '2026-02-16 09:58:12', '::1', 'Viewed user list'),
(354, 3, 'Updated user', '2026-02-16 09:58:23', '::1', 'Updated user: admin'),
(355, 3, 'Viewed user list', '2026-02-16 09:58:23', '::1', 'Viewed user list'),
(356, 3, 'Viewed user list', '2026-02-16 09:59:05', '::1', 'Viewed user list'),
(357, 3, 'Viewed user list', '2026-02-16 09:59:35', '::1', 'Viewed user list'),
(358, 3, 'Viewed user list', '2026-02-16 10:00:44', '::1', 'Viewed user list'),
(359, 3, 'Created user', '2026-02-16 10:00:56', '::1', 'Created user: pevuceviqu'),
(360, 3, 'Viewed user list', '2026-02-16 10:00:56', '::1', 'Viewed user list'),
(361, 3, 'Viewed roles list', '2026-02-16 10:03:46', '::1', NULL),
(362, 3, 'Viewed roles list', '2026-02-16 10:03:55', '::1', NULL),
(363, 3, 'Viewed roles list', '2026-02-16 10:04:30', '::1', NULL),
(364, 3, 'Viewed roles list', '2026-02-16 10:04:34', '::1', NULL),
(365, 3, 'Viewed permissions list', '2026-02-16 10:04:44', '::1', NULL),
(366, 3, 'Viewed roles list', '2026-02-16 10:04:47', '::1', NULL),
(367, 3, 'Viewed user list', '2026-02-16 10:04:49', '::1', 'Viewed user list'),
(368, 3, 'Accessed dashboard', '2026-02-16 10:04:53', '::1', NULL),
(369, 3, 'Viewed permissions list', '2026-02-16 10:04:57', '::1', NULL),
(370, 3, 'Viewed user list', '2026-02-16 10:04:58', '::1', 'Viewed user list'),
(371, 3, 'Viewed roles list', '2026-02-16 10:05:00', '::1', NULL),
(372, 3, 'Viewed permissions list', '2026-02-16 10:05:02', '::1', NULL),
(373, 3, 'Viewed permissions list', '2026-02-16 10:05:05', '::1', NULL),
(374, 3, 'Viewed permissions list', '2026-02-16 10:08:30', '::1', NULL),
(375, 3, 'Accessed dashboard', '2026-02-16 10:08:32', '::1', NULL),
(376, 3, 'Viewed permissions list', '2026-02-16 10:08:33', '::1', NULL),
(377, 3, 'Accessed dashboard', '2026-02-16 10:08:34', '::1', NULL),
(378, 3, 'Accessed dashboard', '2026-02-16 10:08:36', '::1', NULL),
(379, 3, 'Accessed dashboard', '2026-02-16 10:08:37', '::1', NULL),
(380, 3, 'Accessed dashboard', '2026-02-16 10:09:06', '::1', NULL),
(381, 3, 'Viewed permissions list', '2026-02-16 10:09:12', '::1', NULL),
(382, 3, 'Viewed roles list', '2026-02-16 10:09:15', '::1', NULL),
(383, 3, 'Viewed user list', '2026-02-16 10:09:18', '::1', 'Viewed user list'),
(384, 3, 'Viewed user list', '2026-02-16 10:25:46', '::1', 'Viewed user list'),
(385, 3, 'Viewed user list', '2026-02-16 10:25:55', '::1', 'Viewed user list'),
(386, 3, 'Viewed permissions list', '2026-02-16 10:25:59', '::1', NULL),
(387, 3, 'Viewed roles list', '2026-02-16 10:26:04', '::1', NULL),
(388, 3, 'Updated role', '2026-02-16 10:26:22', '::1', 'Updated role: superadmin'),
(389, 3, 'Viewed roles list', '2026-02-16 10:26:23', '::1', NULL),
(390, 3, 'Viewed documents list', '2026-02-16 10:26:52', '::1', NULL),
(391, 3, 'Viewed documents list', '2026-02-16 10:28:02', '::1', NULL),
(392, 3, 'Viewed documents list', '2026-02-16 10:28:05', '::1', NULL),
(393, 3, 'Viewed documents list', '2026-02-16 10:37:53', '::1', NULL),
(394, 3, 'Accessed dashboard', '2026-02-16 10:37:57', '::1', NULL),
(395, 3, 'Viewed documents list', '2026-02-16 10:37:59', '::1', NULL),
(396, 3, 'Created document', '2026-02-16 10:38:56', '::1', 'Created document: In quibusdam quia te'),
(397, 3, 'Viewed documents list', '2026-02-16 10:38:56', '::1', NULL),
(398, 3, 'Updated document', '2026-02-16 10:40:03', '::1', 'Updated document: In quibusdam quia te'),
(399, 3, 'Viewed documents list', '2026-02-16 10:40:03', '::1', NULL),
(400, 3, 'Updated document', '2026-02-16 10:41:01', '::1', 'Updated document: In quibusdam quia te'),
(401, 3, 'Viewed documents list', '2026-02-16 10:41:01', '::1', NULL),
(402, 3, 'Viewed documents list', '2026-02-16 10:41:16', '::1', NULL),
(403, 3, 'Viewed permissions list', '2026-02-16 10:42:03', '::1', NULL),
(404, 3, 'Viewed roles list', '2026-02-16 10:42:07', '::1', NULL),
(405, 3, 'Updated role', '2026-02-16 10:45:10', '::1', 'Updated role: superadmin'),
(406, 3, 'Viewed roles list', '2026-02-16 10:45:10', '::1', NULL),
(407, 3, 'Viewed document types list', '2026-02-16 10:45:13', '::1', NULL),
(408, 3, 'Viewed departments list', '2026-02-16 10:45:38', '::1', NULL),
(409, 3, 'Viewed document types list', '2026-02-16 10:46:08', '::1', NULL),
(410, 3, 'Viewed documents list', '2026-02-16 10:46:16', '::1', NULL),
(411, 3, 'Viewed permissions list', '2026-02-16 10:48:15', '::1', NULL),
(412, 3, 'Viewed roles list', '2026-02-16 10:48:17', '::1', NULL),
(413, 3, 'Accessed dashboard', '2026-02-16 10:49:08', '::1', NULL),
(414, 3, 'Viewed user list', '2026-02-16 10:49:09', '::1', 'Viewed user list'),
(415, 3, 'Viewed roles list', '2026-02-16 10:49:14', '::1', NULL),
(416, 3, 'Viewed permissions list', '2026-02-16 10:49:16', '::1', NULL),
(417, 3, 'Accessed dashboard', '2026-02-16 10:51:53', '::1', NULL),
(418, 3, 'Accessed dashboard', '2026-02-16 10:56:27', '::1', NULL),
(419, 3, 'Accessed dashboard', '2026-02-16 10:57:01', '::1', NULL),
(420, 3, 'Accessed dashboard', '2026-02-16 11:00:43', '::1', NULL),
(421, 3, 'Accessed dashboard', '2026-02-16 11:03:23', '::1', NULL),
(422, 3, 'Accessed dashboard', '2026-02-16 11:03:34', '::1', NULL),
(423, 3, 'Accessed dashboard', '2026-02-16 11:05:33', '::1', NULL),
(424, 3, 'Accessed dashboard', '2026-02-16 12:04:08', '::1', NULL),
(425, 3, 'Viewed user list', '2026-02-16 12:10:08', '::1', 'Viewed user list');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 2),
(2, 3),
(3, 1),
(7, 9),
(9, 7),
(10, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_type_id_foreign` (`type_id`),
  ADD KEY `documents_department_id_foreign` (`department_id`),
  ADD KEY `documents_created_by_foreign` (`created_by`);

--
-- Indexes for table `document_metadata`
--
ALTER TABLE `document_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_metadata_document_id_foreign` (`document_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permission_key` (`permission_key`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD KEY `role_permissions_permission_id_foreign` (`permission_id`),
  ADD KEY `role_id_permission_id` (`role_id`,`permission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD KEY `user_roles_role_id_foreign` (`role_id`),
  ADD KEY `user_id_role_id` (`user_id`,`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `document_metadata`
--
ALTER TABLE `document_metadata`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=426;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `documents_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `documents_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_metadata`
--
ALTER TABLE `document_metadata`
  ADD CONSTRAINT `document_metadata_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  ADD CONSTRAINT `user_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
