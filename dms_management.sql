-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 05:37 AM
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
  `approval_status` enum('draft','pending','sent_for_review','sent_for_approval','approved_by_approver','admin_approved','rejected') DEFAULT 'pending',
  `reviewer_id` int(11) UNSIGNED DEFAULT NULL,
  `reviewer_comments` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `approver_id` int(11) UNSIGNED DEFAULT NULL,
  `approver_comments` text DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `submitted_for_review_at` datetime DEFAULT NULL,
  `submitted_for_approval_at` datetime DEFAULT NULL,
  `effective_date` date DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `title`, `content`, `type_id`, `department_id`, `status`, `approval_status`, `reviewer_id`, `reviewer_comments`, `reviewed_at`, `approver_id`, `approver_comments`, `approved_at`, `rejection_reason`, `rejected_at`, `submitted_for_review_at`, `submitted_for_approval_at`, `effective_date`, `review_date`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Quality Manual', '<h4>Quality Management System</h4><ol><li><strong>General Requirements:</strong> The <a href=\"https://www.quality-assurance-solutions.com/Quality-Assurance-vs-Quality-Control.html\">quality control (or assurance)</a> manual is a textbook for training. Train your quality control, quality assurance and inspection employees to the quality manual. You can use the manual to train other supervisors, leads, engineers and managers.</li><li><strong>General Requirements1:</strong> The <a href=\"https://www.quality-assurance-solutions.com/Quality-Assurance-vs-Quality-Control.html\">quality control (or assurance)</a> manual is a textbook for training. Train your quality control, quality assurance and inspection employees to the quality manual. You can use the manual to train other supervisors, leads, engineers and managers.</li><li><strong>General Requirements2:</strong> The <a href=\"https://www.quality-assurance-solutions.com/Quality-Assurance-vs-Quality-Control.html\">quality control (or assurance)</a> manual is a textbook for training. Train your quality control, quality assurance and inspection employees to the quality manual. You can use the manual to train other supervisors, leads, engineers and managers.</li><li><strong>Images :&nbsp;</strong></li></ol><figure class=\"table\"><table><tbody><tr><td>Image 1</td><td>&nbsp;</td></tr><tr><td>image 2</td><td>&nbsp;</td></tr></tbody></table></figure>', 1, 1, 'draft', 'sent_for_review', 14, '', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-18 12:53:06', NULL, '2025-02-16', NULL, 1, '2026-02-16 10:38:56', '2026-02-18 12:53:06'),
(2, 'Policy Manual', '<h4>Policy Manual Examples Benefits</h4><blockquote><p>Your quality manual is the top level document that specifies your quality management system. It describes top level standard operating procedures, processes and specifications. Your customer will use it as a reference guide. Within the quality manual you describe the implementation and maintenance of the quality management system. Quality manual examples benefits include:</p></blockquote><ul><li>Business Reference</li><li>Training standard</li><li>Continuity Standard</li><li>Improvement Audits</li><li>Meets quality management standards</li><li>Supports supplier quality</li></ul>', 3, 3, 'draft', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-17', NULL, 1, '2026-02-17 06:01:49', '2026-02-17 06:01:49'),
(3, 'SOP', '<h3>Document Metadata:</h3><ul><li>What’s a Standard Operating Procedure (SOP)?</li><li>How to Write an SOP</li><li>How to use Process.st for your SOPs</li></ul><h4><strong>What’s a Standard Operating Procedure (SOP)?</strong></h4><p><strong>A standard operating procedure, or SOP, is a set of detailed step-by-step instructions that describe how to carry out any given process.</strong></p><p>Most companies that are serious about process management use SOPs to manage their day-to-day activities.</p><p>Why?</p>', 2, 2, 'draft', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-20', NULL, 1, '2026-02-17 09:48:12', '2026-02-17 09:48:12');

-- --------------------------------------------------------

--
-- Table structure for table `document_approval_history`
--

CREATE TABLE `document_approval_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `action` enum('pending','sent_for_review','sent_for_approval','approved_by_approver','admin_approved','rejected') NOT NULL,
  `performed_by` int(11) UNSIGNED NOT NULL,
  `comments` text DEFAULT NULL,
  `previous_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_approval_history`
--

INSERT INTO `document_approval_history` (`id`, `document_id`, `action`, `performed_by`, `comments`, `previous_status`, `new_status`, `created_at`) VALUES
(1, 1, '', 1, 'Document submitted for review', '', 'sent_for_review', '2026-02-18 12:53:06');
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

--
-- Dumping data for table `document_metadata`
--

INSERT INTO `document_metadata` (`id`, `document_id`, `meta_key`, `meta_value`, `created_at`, `updated_at`) VALUES
(2, 2, 'created by ', 'pooja mane', NULL, NULL),
(3, 1, 'review date', '16-2-25', NULL, NULL),
(4, 1, 'admin review', '17-2-25', NULL, NULL),
(5, 3, 'metadata key', 'meta value', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `document_search_index`
--

CREATE TABLE `document_search_index` (
  `id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `search_terms` text DEFAULT NULL,
  `indexed_content` longtext DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `keywords` text DEFAULT NULL,
  `indexed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_shares`
--

CREATE TABLE `document_shares` (
  `id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `shared_with_user_id` int(11) UNSIGNED DEFAULT NULL,
  `shared_with_role_id` int(11) UNSIGNED DEFAULT NULL,
  `shared_with_department_id` int(11) UNSIGNED DEFAULT NULL,
  `permission_level` enum('view','edit','full') NOT NULL DEFAULT 'view',
  `expiration_date` datetime DEFAULT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
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
-- Table structure for table `document_versions`
--

CREATE TABLE `document_versions` (
  `id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `version_number` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_hash` varchar(255) DEFAULT NULL,
  `changes_description` text DEFAULT NULL,
  `created_by` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_workflows`
--

CREATE TABLE `document_workflows` (
  `id` int(11) UNSIGNED NOT NULL,
  `document_id` int(11) UNSIGNED NOT NULL,
  `workflow_type` enum('review','approval','publish') NOT NULL,
  `current_status` enum('pending','in_progress','completed','rejected') NOT NULL DEFAULT 'pending',
  `assigned_to` int(11) UNSIGNED DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, '2026-02-16-103048', 'App\\Database\\Migrations\\CreateDocumentMetadataTable', 'default', 'App', 1771237883, 3),
(11, '2026-02-17-065433', 'App\\Database\\Migrations\\CreateDocumentVersionsTable', 'default', 'App', 1771311383, 4),
(12, '2026-02-17-065440', 'App\\Database\\Migrations\\CreateDocumentSharesTable', 'default', 'App', 1771311383, 4),
(13, '2026-02-17-065445', 'App\\Database\\Migrations\\CreateDocumentSearchIndexTable', 'default', 'App', 1771311383, 4),
(14, '2026-02-17-065451', 'App\\Database\\Migrations\\CreateDocumentWorkflowsTable', 'default', 'App', 1771311383, 4),
(15, '2026-02-17-065457', 'App\\Database\\Migrations\\CreateDocumentBackupsTable', 'default', 'App', 1771311383, 4);

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
(50, 'document_create', 'Create new document', '2026-02-16 10:25:00', '2026-02-16 10:25:00'),
(51, 'document_read', 'View documents', '2026-02-16 10:25:00', '2026-02-16 10:25:00'),
(52, 'document_update', 'Editing documents', '2026-02-16 10:25:00', '2026-02-16 10:25:00'),
(53, 'document_delete', 'Deleting documents', '2026-02-16 10:25:00', '2026-02-16 10:25:00'),
(54, 'department_create', 'Create new departments', '2026-02-16 10:29:00', '2026-02-16 10:29:00'),
(55, 'department_read', 'View departments', '2026-02-16 10:29:00', '2026-02-16 10:29:00'),
(56, 'department_update', 'Edit existing departments', '2026-02-16 10:29:00', '2026-02-16 10:29:00'),
(57, 'department_delete', 'Delete departments', '2026-02-16 10:29:00', '2026-02-16 10:29:00'),
(58, 'document_type_create', 'Create new document types', '2026-02-16 10:29:12', '2026-02-16 10:29:12'),
(59, 'document_type_read', 'View document types', '2026-02-16 10:29:12', '2026-02-16 10:29:12'),
(60, 'document_type_update', 'Edit document types', '2026-02-16 10:29:12', '2026-02-16 10:29:12'),
(61, 'document_type_delete', 'Allow deleting document types', '2026-02-16 10:29:12', '2026-02-16 10:29:12'),
(62, 'document_final_approval', 'Final approval on documents', '2026-02-18 07:16:20', '2026-02-18 07:16:20'),
(63, 'document_approve', 'Approval on documents', '2026-02-18 09:34:30', '2026-02-18 09:34:30');

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
(1, 'superadmin', 'Superadmin panel with full system access', '2026-02-16 10:30:35', '2026-02-18 09:34:42'),
(2, 'admin', 'Quality Head / QA Head with administrative privileges', '2026-02-16 10:30:35', '2026-02-18 09:37:35'),
(3, 'lab_manager', 'Lab Manager panel with lab-specific controls', '2026-02-16 10:30:35', '2026-02-16 12:41:04'),
(4, 'reviewer', 'Reviewer panel for document and process review', '2026-02-16 10:30:35', '2026-02-19 04:30:18'),
(5, 'approver', 'Approver panel for final approvals and sign-offs', '2026-02-16 10:30:35', '2026-02-19 04:32:43'),
(6, 'user_staff1', 'User panel - Staff Level 1 (Analyst/Technician)', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(7, 'user_staff2', 'User panel - Staff Level 2 (Support Staff)', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(8, 'auditor', 'Auditor panel (internal/external) with read-only oversight', '2026-02-16 10:30:35', '2026-02-16 10:30:35'),
(9, 'Test', 'Tester', '2026-02-16 05:43:14', '2026-02-16 12:29:33');

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
(1, 62),
(1, 63),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 27),
(2, 28),
(2, 50),
(2, 51),
(2, 52),
(2, 53),
(2, 54),
(2, 55),
(2, 56),
(2, 57),
(2, 58),
(2, 59),
(2, 60),
(2, 61),
(2, 62),
(2, 63),
(3, 15),
(3, 16),
(3, 17),
(3, 18),
(3, 19),
(3, 21),
(3, 22),
(3, 23),
(3, 25),
(3, 26),
(3, 27),
(3, 28),
(3, 50),
(3, 51),
(3, 52),
(3, 53),
(3, 54),
(3, 56),
(3, 57),
(3, 58),
(3, 59),
(3, 60),
(3, 61),
(4, 27),
(4, 50),
(4, 51),
(4, 52),
(4, 58),
(4, 59),
(4, 60),
(4, 63),
(5, 27),
(5, 50),
(5, 51),
(5, 52),
(5, 62),
(5, 63),
(6, 16),
(6, 27),
(6, 28),
(7, 27),
(8, 16),
(8, 20),
(8, 24),
(8, 27),
(8, 28),
(9, 27);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `department_id`, `password_hash`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 2, '$2y$10$TisSJeLR/WQLzmLEHHJ70.9.SYs.i.JmyWRhRkLU8Ipx.tRu27SqS', 'admin@gmail.com', 'active', '2026-02-13 15:23:42', '2026-02-17 11:20:37'),
(2, 'lab', 1, '$2y$10$cyV/ldJQXu4TwPeTvUQVRuI2eZM96DKEoI.XdPqLbF2ztXW43TRk.', 'user@gmail.com', 'active', '2026-02-13 09:58:05', '2026-02-16 12:40:05'),
(3, 'superadmin', 2, '$2y$10$a24AfL/zJufPr.i8myk5GueMCFHrIiEAzYrWwx/Kx69Bzdn004b.m', 'superadmin@gmail.com', 'active', '2026-02-16 05:05:58', '2026-02-17 04:16:43'),
(7, 'tester', 1, '$2y$10$BX2U2GCTai3VFbSQgD9BtO/BKzbdRTcHbxXbB6f2DKWdzwRIhs1yy', 'tester@gmail.com', 'active', '2026-02-16 05:44:05', '2026-02-16 05:44:05'),
(9, 'staff', 3, '$2y$10$8arUe4/mElHNVIPLKHSB5usNM.Lkc.a6EWW2S71A/q3UqkyKH0V8O', 'staff@medzus.com', 'active', '2026-02-16 09:38:13', '2026-02-16 09:38:13'),
(13, 'User5', 1, '$2y$10$EV5VjcWiyaijD11pubq6KOVPqxorKbSnmjyDKTfOuNJ4b/HISYlxe', 'userstaff@medzus.com', 'active', '2026-02-17 11:04:58', '2026-02-17 11:19:33'),
(14, 'reviwer', 1, '$2y$10$sm1z8RXBOqOI5OxQo0ZERejIiVVqFIPD4O9VoR3N7zmtE.FZDe1GK', 'reviwer@medzus.com', 'active', '2026-02-18 05:09:42', '2026-02-18 05:09:42'),
(15, 'reviwer1', 1, '$2y$10$PYzCEzE2wOzdcFERZVCIvu.VX6rXIbBbxwYP6pj6kzjwq9M23sC7a', 'reviwer1@medzus.com', 'active', '2026-02-18 05:10:15', '2026-02-18 05:10:15'),
(16, 'approver', 1, '$2y$10$Y9arIi3wSeL7RMnK1t2uzem7spwWLJlgsOzZH1/5vtnova5FO/nO6', 'approver@gmail.com', 'active', '2026-02-18 05:10:56', '2026-02-18 05:10:56'),
(17, 'approver1', 1, '$2y$10$Rt8mTpjX5.TiPZvzHMjfG.2ceN3VKQ6OM.T20e1s/Mvf9pqJ2gCX6', 'approver1@medzus.com', 'active', '2026-02-18 05:11:21', '2026-02-18 05:11:21');

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
(1, 3, 'Viewed user list', '2026-02-18 04:55:24', '::1', 'Viewed user list'),
(2, 3, 'Viewed user list', '2026-02-18 04:58:47', '::1', 'Viewed user list'),
(3, 3, 'User logged out', '2026-02-18 05:05:35', '::1', NULL),
(4, 1, 'User logged in', '2026-02-18 05:05:47', '::1', 'Login successful from IP: ::1'),
(5, 1, 'Accessed dashboard', '2026-02-18 05:05:47', '::1', NULL),
(6, 1, 'Viewed documents list', '2026-02-18 05:05:51', '::1', NULL),
(7, 1, 'Viewed documents list', '2026-02-18 05:06:46', '::1', NULL),
(8, 1, 'Shared document', '2026-02-18 05:07:31', '::1', 'Shared document: SOP'),
(9, 1, 'Viewed user list', '2026-02-18 05:07:47', '::1', 'Viewed user list'),
(10, 1, 'Viewed user list', '2026-02-18 05:08:01', '::1', 'Viewed user list'),
(11, 3, 'User logged in', '2026-02-18 05:08:26', '::1', 'Login successful from IP: ::1'),
(12, 3, 'Accessed dashboard', '2026-02-18 05:08:26', '::1', NULL),
(13, 3, 'Viewed permissions list', '2026-02-18 05:08:29', '::1', NULL),
(14, 3, 'Viewed roles list', '2026-02-18 05:08:32', '::1', NULL),
(15, 3, 'Viewed user list', '2026-02-18 05:08:54', '::1', 'Viewed user list'),
(16, 3, 'Created user', '2026-02-18 05:09:42', '::1', 'Created user: reviwer'),
(17, 3, 'Viewed user list', '2026-02-18 05:09:42', '::1', 'Viewed user list'),
(18, 3, 'Created user', '2026-02-18 05:10:16', '::1', 'Created user: reviwer1'),
(19, 3, 'Viewed user list', '2026-02-18 05:10:16', '::1', 'Viewed user list'),
(20, 3, 'Created user', '2026-02-18 05:10:56', '::1', 'Created user: approver'),
(21, 3, 'Viewed user list', '2026-02-18 05:10:56', '::1', 'Viewed user list'),
(22, 3, 'Created user', '2026-02-18 05:11:21', '::1', 'Created user: approver1'),
(23, 3, 'Viewed user list', '2026-02-18 05:11:21', '::1', 'Viewed user list'),
(24, 3, 'User logged out', '2026-02-18 05:11:34', '::1', NULL),
(25, 14, 'User logged in', '2026-02-18 05:11:41', '::1', 'Login successful from IP: ::1'),
(26, 14, 'Accessed dashboard', '2026-02-18 05:11:41', '::1', NULL),
(27, 14, 'User logged out', '2026-02-18 05:11:56', '::1', NULL),
(28, 3, 'User logged in', '2026-02-18 05:12:01', '::1', 'Login successful from IP: ::1'),
(29, 3, 'Accessed dashboard', '2026-02-18 05:12:01', '::1', NULL),
(30, 3, 'Viewed permissions list', '2026-02-18 05:12:15', '::1', NULL),
(31, 3, 'Viewed roles list', '2026-02-18 05:12:16', '::1', NULL),
(32, 3, 'Updated role', '2026-02-18 05:12:33', '::1', 'Updated role: reviewer'),
(33, 3, 'Viewed roles list', '2026-02-18 05:12:33', '::1', NULL),
(34, 1, 'Viewed user list', '2026-02-18 05:12:40', '::1', 'Viewed user list'),
(35, 1, 'User logged out', '2026-02-18 05:12:43', '::1', NULL),
(36, 3, 'Viewed user list', '2026-02-18 05:13:07', '::1', 'Viewed user list'),
(37, 14, 'User logged in', '2026-02-18 05:13:15', '::1', 'Login successful from IP: ::1'),
(38, 14, 'Accessed dashboard', '2026-02-18 05:13:15', '::1', NULL),
(39, 14, 'Viewed documents list', '2026-02-18 05:13:18', '::1', NULL),
(40, 14, 'Viewed documents list', '2026-02-18 05:13:24', '::1', NULL),
(41, 14, 'Viewed documents list', '2026-02-18 05:21:12', '::1', NULL),
(42, 3, 'User logged in', '2026-02-18 05:21:48', '::1', 'Login successful from IP: ::1'),
(43, 3, 'Accessed dashboard', '2026-02-18 05:21:48', '::1', NULL),
(44, 3, 'User logged out', '2026-02-18 05:22:01', '::1', NULL),
(45, 15, 'User logged in', '2026-02-18 05:22:06', '::1', 'Login successful from IP: ::1'),
(46, 15, 'Accessed dashboard', '2026-02-18 05:22:06', '::1', NULL),
(47, 15, 'Viewed documents list', '2026-02-18 05:22:08', '::1', NULL),
(48, 15, 'User logged out', '2026-02-18 05:22:15', '::1', NULL),
(49, 16, 'User logged in', '2026-02-18 05:22:21', '::1', 'Login successful from IP: ::1'),
(50, 16, 'Accessed dashboard', '2026-02-18 05:22:21', '::1', NULL),
(51, 16, 'Viewed permissions list', '2026-02-18 05:22:23', '::1', NULL),
(52, 16, 'Viewed permissions list', '2026-02-18 05:22:27', '::1', NULL),
(53, 16, 'Accessed dashboard', '2026-02-18 05:22:28', '::1', NULL),
(54, 14, 'Viewed documents list', '2026-02-18 05:22:50', '::1', NULL),
(55, 14, 'User logged out', '2026-02-18 05:23:01', '::1', NULL),
(56, 1, 'User logged in', '2026-02-18 05:23:09', '::1', 'Login successful from IP: ::1'),
(57, 1, 'Accessed dashboard', '2026-02-18 05:23:09', '::1', NULL),
(58, 1, 'Viewed documents list', '2026-02-18 05:23:12', '::1', NULL),
(59, 1, 'Viewed documents list', '2026-02-18 05:49:00', '::1', NULL),
(60, 1, 'Viewed documents list', '2026-02-18 05:50:15', '::1', NULL),
(61, 1, 'Viewed documents list', '2026-02-18 06:26:03', '::1', NULL),
(62, 16, 'Accessed dashboard', '2026-02-18 06:50:53', '::1', NULL),
(63, 1, 'Viewed documents list', '2026-02-18 06:58:24', '::1', NULL),
(64, 3, 'Accessed dashboard', '2026-02-18 07:13:49', '::1', NULL),
(65, 3, 'Viewed permissions list', '2026-02-18 07:13:51', '::1', NULL),
(66, 3, 'Viewed permissions list', '2026-02-18 07:13:53', '::1', NULL),
(67, 3, 'Viewed roles list', '2026-02-18 07:14:01', '::1', NULL),
(68, 3, 'Updated role', '2026-02-18 07:14:15', '::1', 'Updated role: superadmin'),
(69, 3, 'Viewed roles list', '2026-02-18 07:14:15', '::1', NULL),
(70, 3, 'Viewed permissions list', '2026-02-18 07:14:18', '::1', NULL),
(71, 3, 'Viewed permissions list', '2026-02-18 07:14:20', '::1', NULL),
(72, 3, 'Created permission', '2026-02-18 07:16:20', '::1', 'Created permission: document_final_approval'),
(73, 3, 'Viewed permissions list', '2026-02-18 07:16:20', '::1', NULL),
(74, 3, 'Viewed roles list', '2026-02-18 07:16:25', '::1', NULL),
(75, 3, 'Updated role', '2026-02-18 07:16:34', '::1', 'Updated role: superadmin'),
(76, 3, 'Viewed roles list', '2026-02-18 07:16:34', '::1', NULL),
(77, 3, 'Viewed documents list', '2026-02-18 07:17:59', '::1', NULL),
(78, 3, 'Viewed roles list', '2026-02-18 07:18:17', '::1', NULL),
(79, 3, 'Viewed documents list', '2026-02-18 07:18:20', '::1', NULL),
(80, 3, 'Viewed documents list', '2026-02-18 07:18:48', '::1', NULL),
(81, 1, 'Viewed documents list', '2026-02-18 07:19:36', '::1', NULL),
(82, 1, 'Viewed documents list', '2026-02-18 07:23:15', '::1', NULL),
(83, 1, 'Viewed documents list', '2026-02-18 07:23:21', '::1', NULL),
(84, 14, 'User logged in', '2026-02-18 07:23:41', '::1', 'Login successful from IP: ::1'),
(85, 14, 'Accessed dashboard', '2026-02-18 07:23:41', '::1', NULL),
(86, 14, 'Viewed documents list', '2026-02-18 07:23:43', '::1', NULL),
(87, 1, 'Viewed documents list', '2026-02-18 07:29:33', '::1', NULL),
(88, 1, 'Accessed dashboard', '2026-02-18 07:29:36', '::1', NULL),
(89, 1, 'Viewed user list', '2026-02-18 07:29:38', '::1', 'Viewed user list'),
(90, 1, 'Viewed roles list', '2026-02-18 07:29:42', '::1', NULL),
(91, 1, 'Viewed documents list', '2026-02-18 07:29:46', '::1', NULL),
(92, 3, 'Accessed dashboard', '2026-02-18 07:29:54', '::1', NULL),
(93, 3, 'Viewed user list', '2026-02-18 07:29:55', '::1', 'Viewed user list'),
(94, 3, 'Viewed roles list', '2026-02-18 07:29:56', '::1', NULL),
(95, 3, 'Viewed permissions list', '2026-02-18 07:29:58', '::1', NULL),
(96, 3, 'Viewed documents list', '2026-02-18 07:30:01', '::1', NULL),
(97, 1, 'Viewed documents list', '2026-02-18 07:30:06', '::1', NULL),
(98, 1, 'Accessed dashboard', '2026-02-18 07:30:31', '::1', NULL),
(99, 1, 'User logged out', '2026-02-18 08:06:33', '::1', NULL),
(100, 1, 'User logged in', '2026-02-18 08:06:42', '::1', 'Login successful from IP: ::1'),
(101, 1, 'Accessed dashboard', '2026-02-18 08:06:42', '::1', NULL),
(102, 1, 'Viewed user list', '2026-02-18 08:07:06', '::1', 'Viewed user list'),
(103, 1, 'Accessed dashboard', '2026-02-18 08:07:24', '::1', NULL),
(104, 1, 'Accessed dashboard', '2026-02-18 08:07:30', '::1', NULL),
(105, 1, 'Accessed dashboard', '2026-02-18 08:08:40', '::1', NULL),
(106, 1, 'Viewed user list', '2026-02-18 08:08:42', '::1', 'Viewed user list'),
(107, 1, 'Viewed roles list', '2026-02-18 08:08:44', '::1', NULL),
(108, 1, 'Viewed documents list', '2026-02-18 08:08:46', '::1', NULL),
(109, 1, 'Viewed documents list', '2026-02-18 08:14:45', '::1', NULL),
(110, 1, 'Viewed documents list', '2026-02-18 08:14:46', '::1', NULL),
(111, 1, 'Viewed documents list', '2026-02-18 08:14:47', '::1', NULL),
(112, 1, 'Viewed documents list', '2026-02-18 08:14:47', '::1', NULL),
(113, 14, 'Viewed documents list', '2026-02-18 08:14:55', '::1', NULL),
(114, 3, 'Viewed documents list', '2026-02-18 08:15:04', '::1', NULL),
(115, 1, 'Viewed documents list', '2026-02-18 08:18:54', '::1', NULL),
(116, 1, 'Viewed documents list', '2026-02-18 08:31:07', '::1', NULL),
(117, 1, 'Viewed documents list', '2026-02-18 08:31:09', '::1', NULL),
(118, 1, 'Viewed documents list', '2026-02-18 08:33:20', '::1', NULL),
(119, 1, 'Viewed documents list', '2026-02-18 08:42:49', '::1', NULL),
(120, 1, 'Viewed documents list', '2026-02-18 08:43:30', '::1', NULL),
(121, 1, 'Viewed documents list', '2026-02-18 08:48:08', '::1', NULL),
(122, 1, 'Viewed documents list', '2026-02-18 08:48:10', '::1', NULL),
(123, 1, 'Viewed documents list', '2026-02-18 08:48:33', '::1', NULL),
(124, 1, 'Viewed documents list', '2026-02-18 09:00:19', '::1', NULL),
(125, 3, 'Viewed permissions list', '2026-02-18 09:02:08', '::1', NULL),
(126, 1, 'Viewed documents list', '2026-02-18 09:02:56', '::1', NULL),
(127, 1, 'Viewed documents list', '2026-02-18 09:03:03', '::1', NULL),
(128, 1, 'Viewed documents list', '2026-02-18 09:03:40', '::1', NULL),
(129, 1, 'Viewed documents list', '2026-02-18 09:04:34', '::1', NULL),
(130, 3, 'Viewed documents list', '2026-02-18 09:04:38', '::1', NULL),
(131, 1, 'Viewed documents list', '2026-02-18 09:06:21', '::1', NULL),
(132, 1, 'Viewed documents list', '2026-02-18 09:06:45', '::1', NULL),
(133, 1, 'Viewed documents list', '2026-02-18 09:07:05', '::1', NULL),
(134, 1, 'Viewed documents list', '2026-02-18 09:08:59', '::1', NULL),
(135, 1, 'Viewed documents list', '2026-02-18 09:09:51', '::1', NULL),
(136, 1, 'Viewed documents list', '2026-02-18 09:10:17', '::1', NULL),
(137, 1, 'Viewed documents list', '2026-02-18 09:11:50', '::1', NULL),
(138, 1, 'Viewed document', '2026-02-18 09:17:24', '::1', 'Document ID: 3'),
(139, 3, 'Viewed roles list', '2026-02-18 09:33:55', '::1', NULL),
(140, 3, 'Viewed permissions list', '2026-02-18 09:34:13', '::1', NULL),
(141, 3, 'Created permission', '2026-02-18 09:34:30', '::1', 'Created permission: document_approve'),
(142, 3, 'Viewed permissions list', '2026-02-18 09:34:30', '::1', NULL),
(143, 3, 'Viewed roles list', '2026-02-18 09:34:32', '::1', NULL),
(144, 3, 'Updated role', '2026-02-18 09:34:42', '::1', 'Updated role: superadmin'),
(145, 3, 'Viewed roles list', '2026-02-18 09:34:42', '::1', NULL),
(146, 3, 'Viewed roles list', '2026-02-18 09:35:09', '::1', NULL),
(147, 3, 'Viewed documents list', '2026-02-18 09:35:11', '::1', NULL),
(148, 14, 'Viewed documents list', '2026-02-18 09:36:26', '::1', NULL),
(149, 3, 'Viewed documents list', '2026-02-18 09:36:29', '::1', NULL),
(150, 3, 'Viewed documents list', '2026-02-18 09:36:30', '::1', NULL),
(151, 3, 'Viewed documents list', '2026-02-18 09:36:34', '::1', NULL),
(152, 3, 'Viewed permissions list', '2026-02-18 09:36:52', '::1', NULL),
(153, 3, 'Viewed user list', '2026-02-18 09:36:59', '::1', 'Viewed user list'),
(154, 3, 'Viewed roles list', '2026-02-18 09:37:02', '::1', NULL),
(155, 3, 'Updated role', '2026-02-18 09:37:35', '::1', 'Updated role: admin'),
(156, 3, 'Viewed roles list', '2026-02-18 09:37:36', '::1', NULL),
(157, 3, 'Viewed documents list', '2026-02-18 09:37:48', '::1', NULL),
(158, 3, 'Viewed permissions list', '2026-02-18 09:38:07', '::1', NULL),
(159, 3, 'Viewed roles list', '2026-02-18 09:38:09', '::1', NULL),
(160, 3, 'Viewed user list', '2026-02-18 09:38:12', '::1', 'Viewed user list'),
(161, 3, 'Viewed roles list', '2026-02-18 09:38:15', '::1', NULL),
(162, 3, 'Accessed dashboard', '2026-02-18 09:44:38', '::1', NULL),
(163, 1, 'Viewed documents list', '2026-02-18 09:49:09', '::1', NULL),
(164, 1, 'Viewed document', '2026-02-18 09:55:09', '::1', 'Document ID: 3'),
(165, 1, 'Viewed documents list', '2026-02-18 09:56:53', '::1', NULL),
(166, 14, 'Viewed documents list', '2026-02-18 10:04:10', '::1', NULL),
(167, 1, 'Viewed document', '2026-02-18 10:06:57', '::1', 'Document ID: 3'),
(168, 1, 'Viewed documents list', '2026-02-18 10:07:03', '::1', NULL),
(169, 1, 'Viewed documents list', '2026-02-18 10:07:10', '::1', NULL),
(170, 1, 'Viewed documents list', '2026-02-18 10:13:43', '::1', NULL),
(171, 1, 'Assigned reviewer to document', '2026-02-18 10:14:43', '::1', 'Document ID: 1'),
(172, 1, 'Viewed documents list', '2026-02-18 10:14:53', '::1', NULL),
(173, 14, 'Viewed documents list', '2026-02-18 10:17:16', '::1', NULL),
(174, 3, 'Viewed permissions list', '2026-02-18 10:17:32', '::1', NULL),
(175, 3, 'Viewed roles list', '2026-02-18 10:17:33', '::1', NULL),
(176, 3, 'Updated role', '2026-02-18 10:17:52', '::1', 'Updated role: reviewer'),
(177, 3, 'Viewed roles list', '2026-02-18 10:17:52', '::1', NULL),
(178, 3, 'Updated role', '2026-02-18 10:18:01', '::1', 'Updated role: approver'),
(179, 3, 'Viewed roles list', '2026-02-18 10:18:01', '::1', NULL),
(180, 1, 'Viewed documents list', '2026-02-18 10:18:23', '::1', NULL),
(181, 14, 'Viewed documents list', '2026-02-18 10:18:34', '::1', NULL),
(182, 14, 'Viewed documents list', '2026-02-18 10:18:36', '::1', NULL),
(183, 14, 'Viewed documents list', '2026-02-18 10:18:44', '::1', NULL),
(184, 14, 'Viewed documents list', '2026-02-18 10:18:46', '::1', NULL),
(185, 14, 'Viewed documents list', '2026-02-18 10:19:11', '::1', NULL),
(186, 14, 'Viewed documents list', '2026-02-18 10:19:41', '::1', NULL),
(187, 1, 'Viewed documents list', '2026-02-18 10:20:43', '::1', NULL),
(188, 1, 'Viewed document', '2026-02-18 10:21:11', '::1', 'Document ID: 1'),
(189, 1, 'Viewed documents list', '2026-02-18 10:22:24', '::1', NULL),
(190, 1, 'Viewed document', '2026-02-18 10:22:50', '::1', 'Document ID: 3'),
(191, 1, 'Viewed document', '2026-02-18 10:23:10', '::1', 'Document ID: 3'),
(192, 1, 'Viewed documents list', '2026-02-18 10:23:36', '::1', NULL),
(193, 1, 'Viewed document', '2026-02-18 10:23:46', '::1', 'Document ID: 1'),
(194, 14, 'Viewed documents list', '2026-02-18 10:24:18', '::1', NULL),
(195, 14, 'Viewed documents list', '2026-02-18 10:24:20', '::1', NULL),
(196, 14, 'Viewed documents list', '2026-02-18 10:24:23', '::1', NULL),
(197, 14, 'Viewed documents list', '2026-02-18 10:24:25', '::1', NULL),
(198, 14, 'Viewed documents list', '2026-02-18 10:24:39', '::1', NULL),
(199, 3, 'Accessed dashboard', '2026-02-18 10:32:40', '::1', NULL),
(200, 3, 'Viewed documents list', '2026-02-18 10:32:42', '::1', NULL),
(201, 3, 'Viewed roles list', '2026-02-18 10:32:52', '::1', NULL),
(202, 1, 'Reviewed document', '2026-02-18 10:42:49', '::1', 'Document ID: 1 - returned for revision'),
(203, 1, 'Viewed documents list', '2026-02-18 10:42:50', '::1', NULL),
(204, 1, 'Viewed documents list', '2026-02-18 10:51:05', '::1', NULL),
(205, 1, 'Assigned reviewer to document', '2026-02-18 10:51:44', '::1', 'Document ID: 1'),
(206, 14, 'Viewed documents list', '2026-02-18 10:51:53', '::1', NULL),
(207, 14, 'Viewed documents list', '2026-02-18 10:51:57', '::1', NULL),
(208, 14, 'Viewed documents list', '2026-02-18 10:52:01', '::1', NULL),
(209, 14, 'Viewed documents list', '2026-02-18 10:52:08', '::1', NULL),
(210, 1, 'Viewed documents list', '2026-02-18 10:53:25', '::1', NULL),
(211, 14, 'Viewed documents list', '2026-02-18 10:53:30', '::1', NULL),
(212, 1, 'Viewed documents list', '2026-02-18 10:55:02', '::1', NULL),
(213, 14, 'Viewed documents list', '2026-02-18 10:55:06', '::1', NULL),
(214, 14, 'Viewed documents list', '2026-02-18 10:55:17', '::1', NULL),
(215, 14, 'Viewed document', '2026-02-18 10:55:20', '::1', 'Document ID: 1'),
(216, 1, 'Viewed documents list', '2026-02-18 10:56:16', '::1', NULL),
(217, 1, 'Viewed document', '2026-02-18 10:56:19', '::1', 'Document ID: 3'),
(218, 14, 'Viewed document', '2026-02-18 10:56:27', '::1', 'Document ID: 1'),
(219, 1, 'Viewed document', '2026-02-18 10:58:01', '::1', 'Document ID: 3'),
(220, 14, 'Viewed document', '2026-02-18 10:58:10', '::1', 'Document ID: 1'),
(221, 14, 'Viewed document', '2026-02-18 10:58:22', '::1', 'Document ID: 1'),
(222, 1, 'Viewed document', '2026-02-18 11:01:52', '::1', 'Document ID: 3'),
(223, 1, 'Viewed document', '2026-02-18 11:02:21', '::1', 'Document ID: 3'),
(224, 14, 'Viewed document', '2026-02-18 11:02:25', '::1', 'Document ID: 1'),
(225, 1, 'Viewed document', '2026-02-18 11:02:39', '::1', 'Document ID: 1'),
(226, 1, 'Viewed document', '2026-02-18 11:02:53', '::1', 'Document ID: 1'),
(227, 1, 'Viewed document', '2026-02-18 11:05:42', '::1', 'Document ID: 1'),
(228, 14, 'Viewed document', '2026-02-18 11:06:07', '::1', 'Document ID: 1'),
(229, 14, 'Viewed document', '2026-02-18 11:06:22', '::1', 'Document ID: 1'),
(230, 14, 'Viewed document', '2026-02-18 11:07:57', '::1', 'Document ID: 1'),
(231, 1, 'Viewed document', '2026-02-18 11:08:45', '::1', 'Document ID: 1'),
(232, 1, 'Viewed document', '2026-02-18 11:09:14', '::1', 'Document ID: 1'),
(233, 14, 'Viewed document', '2026-02-18 11:09:46', '::1', 'Document ID: 1'),
(234, 14, 'Viewed document', '2026-02-18 11:10:05', '::1', 'Document ID: 1'),
(235, 1, 'Viewed document', '2026-02-18 11:10:35', '::1', 'Document ID: 1'),
(236, 1, 'Viewed document', '2026-02-18 11:10:51', '::1', 'Document ID: 1'),
(237, 14, 'Viewed document', '2026-02-18 11:11:58', '::1', 'Document ID: 1'),
(238, 14, 'Viewed document', '2026-02-18 11:13:21', '::1', 'Document ID: 1'),
(239, 14, 'Viewed document', '2026-02-18 11:13:26', '::1', 'Document ID: 1'),
(240, 1, 'Viewed document', '2026-02-18 11:13:29', '::1', 'Document ID: 1'),
(241, 1, 'Viewed document', '2026-02-18 11:14:36', '::1', 'Document ID: 1'),
(242, 1, 'Viewed document', '2026-02-18 11:15:01', '::1', 'Document ID: 1'),
(243, 1, 'Viewed document', '2026-02-18 11:19:06', '::1', 'Document ID: 1'),
(244, 1, 'Viewed document', '2026-02-18 11:20:16', '::1', 'Document ID: 1'),
(245, 14, 'Viewed document', '2026-02-18 11:21:32', '::1', 'Document ID: 1'),
(246, 1, 'Viewed document', '2026-02-18 11:21:38', '::1', 'Document ID: 1'),
(247, 1, 'Viewed document', '2026-02-18 11:22:18', '::1', 'Document ID: 1'),
(248, 14, 'Viewed document', '2026-02-18 11:22:43', '::1', 'Document ID: 1'),
(249, 1, 'Viewed document', '2026-02-18 11:22:47', '::1', 'Document ID: 1'),
(250, 14, 'Viewed document', '2026-02-18 11:23:20', '::1', 'Document ID: 1'),
(251, 1, 'Viewed document', '2026-02-18 11:23:23', '::1', 'Document ID: 1'),
(252, 1, 'Viewed documents list', '2026-02-18 11:58:58', '::1', NULL),
(253, 14, 'Viewed documents list', '2026-02-18 11:59:01', '::1', NULL),
(254, 14, 'Viewed documents list', '2026-02-18 11:59:26', '::1', NULL),
(255, 14, 'Viewed documents list', '2026-02-18 12:05:19', '::1', NULL),
(256, 14, 'Viewed documents list', '2026-02-18 12:06:32', '::1', NULL),
(257, 1, 'Viewed documents list', '2026-02-18 12:16:07', '::1', NULL),
(258, 1, 'Viewed documents list', '2026-02-18 12:16:10', '::1', NULL),
(259, 1, 'Viewed documents list', '2026-02-18 12:16:11', '::1', NULL),
(260, 1, 'Viewed documents list', '2026-02-18 12:17:29', '::1', NULL),
(261, 1, 'Viewed documents list', '2026-02-18 12:19:06', '::1', NULL),
(262, 1, 'Viewed documents list', '2026-02-18 12:19:47', '::1', NULL),
(263, 1, 'Viewed documents list', '2026-02-18 12:20:24', '::1', NULL),
(264, 1, 'Viewed documents list', '2026-02-18 12:20:35', '::1', NULL),
(265, 1, 'Viewed documents list', '2026-02-18 12:21:17', '::1', NULL),
(266, 14, 'Viewed documents list', '2026-02-18 12:21:27', '::1', NULL),
(267, 1, 'Viewed documents list', '2026-02-18 12:21:56', '::1', NULL),
(268, 14, 'Viewed documents list', '2026-02-18 12:21:59', '::1', NULL),
(269, 14, 'Viewed documents list', '2026-02-18 12:22:24', '::1', NULL),
(270, 1, 'Viewed documents list', '2026-02-18 12:22:42', '::1', NULL),
(271, 1, 'Viewed documents list', '2026-02-18 12:22:54', '::1', NULL),
(272, 1, 'Viewed documents list', '2026-02-18 12:23:07', '::1', NULL),
(273, 1, 'Viewed documents list', '2026-02-18 12:23:14', '::1', NULL),
(274, 1, 'Viewed documents list', '2026-02-18 12:24:09', '::1', NULL),
(275, 1, 'Viewed documents list', '2026-02-18 12:24:21', '::1', NULL),
(276, 1, 'Viewed documents list', '2026-02-18 12:29:57', '::1', NULL),
(277, 14, 'Viewed documents list', '2026-02-18 12:30:00', '::1', NULL),
(278, 1, 'Viewed documents list', '2026-02-18 12:30:07', '::1', NULL),
(279, 1, 'Viewed documents list', '2026-02-18 12:30:21', '::1', NULL),
(280, 1, 'Viewed documents list', '2026-02-18 12:30:27', '::1', NULL),
(281, 14, 'Viewed documents list', '2026-02-18 12:31:02', '::1', NULL),
(282, 14, 'Viewed documents list', '2026-02-18 12:31:03', '::1', NULL),
(283, 1, 'Viewed documents list', '2026-02-18 12:31:07', '::1', NULL),
(284, 1, 'Viewed documents list', '2026-02-18 12:31:11', '::1', NULL),
(285, 1, 'Viewed documents list', '2026-02-18 12:31:23', '::1', NULL),
(286, 1, 'Viewed document', '2026-02-18 12:31:36', '::1', 'Document ID: 3'),
(287, 1, 'Viewed documents list', '2026-02-18 12:31:43', '::1', NULL),
(288, 1, 'Viewed documents list', '2026-02-18 12:31:48', '::1', NULL),
(289, 1, 'Viewed document', '2026-02-18 12:31:51', '::1', 'Document ID: 3'),
(290, 1, 'Viewed document', '2026-02-18 12:31:52', '::1', 'Document ID: 2'),
(291, 1, 'Viewed documents list', '2026-02-18 12:32:20', '::1', NULL),
(292, 1, 'Viewed documents list', '2026-02-18 12:32:25', '::1', NULL),
(293, 1, 'Viewed documents list', '2026-02-18 12:32:31', '::1', NULL),
(294, 1, 'Viewed documents list', '2026-02-18 12:33:58', '::1', NULL),
(295, 1, 'Viewed documents list', '2026-02-18 12:34:20', '::1', NULL),
(296, 1, 'Viewed documents list', '2026-02-18 12:34:24', '::1', NULL),
(297, 1, 'Viewed document', '2026-02-18 12:44:57', '::1', 'Document ID: 1'),
(298, 1, 'Viewed documents list', '2026-02-18 12:46:02', '::1', NULL),
(299, 1, 'Viewed document', '2026-02-18 12:46:08', '::1', 'Document ID: 3'),
(300, 1, 'Viewed document', '2026-02-18 12:47:24', '::1', 'Document ID: 3'),
(301, 1, 'Viewed documents list', '2026-02-18 12:49:03', '::1', NULL),
(302, 1, 'Viewed document', '2026-02-18 12:49:07', '::1', 'Document ID: 3'),
(303, 14, 'Viewed documents list', '2026-02-18 12:49:18', '::1', NULL),
(304, 14, 'Viewed document', '2026-02-18 12:49:20', '::1', 'Document ID: 1'),
(305, 1, 'Viewed documents list', '2026-02-18 12:50:21', '::1', NULL),
(306, 1, 'Viewed document', '2026-02-18 12:50:24', '::1', 'Document ID: 3'),
(307, 1, 'Viewed document', '2026-02-18 12:50:29', '::1', 'Document ID: 1'),
(308, 14, 'Viewed document', '2026-02-18 12:52:43', '::1', 'Document ID: 1'),
(309, 1, 'Assigned reviewer to document', '2026-02-18 12:53:06', '::1', 'Document ID: 1'),
(310, 14, 'Viewed document', '2026-02-18 12:53:20', '::1', 'Document ID: 1'),
(311, 1, 'User logged in', '2026-02-19 04:16:56', '::1', 'Login successful from IP: ::1'),
(312, 1, 'Accessed dashboard', '2026-02-19 04:16:57', '::1', NULL),
(313, 1, 'Viewed user list', '2026-02-19 04:17:06', '::1', 'Viewed user list'),
(314, 1, 'Viewed user list', '2026-02-19 04:17:16', '::1', 'Viewed user list'),
(315, 1, 'Viewed roles list', '2026-02-19 04:17:18', '::1', NULL),
(316, 1, 'Viewed documents list', '2026-02-19 04:17:36', '::1', NULL),
(317, 3, 'User logged in', '2026-02-19 04:26:19', '::1', 'Login successful from IP: ::1'),
(318, 3, 'Accessed dashboard', '2026-02-19 04:26:19', '::1', NULL),
(319, 3, 'Viewed user list', '2026-02-19 04:26:21', '::1', 'Viewed user list'),
(320, 3, 'Viewed roles list', '2026-02-19 04:26:23', '::1', NULL),
(321, 3, 'Viewed permissions list', '2026-02-19 04:26:24', '::1', NULL),
(322, 3, 'Viewed documents list', '2026-02-19 04:26:33', '::1', NULL),
(323, 1, 'Viewed documents list', '2026-02-19 04:28:03', '::1', NULL),
(324, 14, 'User logged in', '2026-02-19 04:28:47', '::1', 'Login successful from IP: ::1'),
(325, 14, 'Accessed dashboard', '2026-02-19 04:28:47', '::1', NULL),
(326, 14, 'Viewed documents list', '2026-02-19 04:28:50', '::1', NULL),
(327, 3, 'Viewed roles list', '2026-02-19 04:29:07', '::1', NULL),
(328, 3, 'Viewed permissions list', '2026-02-19 04:29:29', '::1', NULL),
(329, 3, 'Viewed roles list', '2026-02-19 04:29:57', '::1', NULL),
(330, 3, 'Updated role', '2026-02-19 04:30:18', '::1', 'Updated role: reviewer'),
(331, 3, 'Viewed roles list', '2026-02-19 04:30:18', '::1', NULL),
(332, 14, 'Viewed documents list', '2026-02-19 04:30:22', '::1', NULL),
(333, 16, 'User logged in', '2026-02-19 04:30:58', '::1', 'Login successful from IP: ::1'),
(334, 16, 'Accessed dashboard', '2026-02-19 04:30:58', '::1', NULL),
(335, 16, 'Viewed permissions list', '2026-02-19 04:31:05', '::1', NULL),
(336, 16, 'Viewed documents list', '2026-02-19 04:31:12', '::1', NULL),
(337, 3, 'Updated role', '2026-02-19 04:32:07', '::1', 'Updated role: approver'),
(338, 3, 'Viewed roles list', '2026-02-19 04:32:07', '::1', NULL),
(339, 16, 'Viewed documents list', '2026-02-19 04:32:11', '::1', NULL),
(340, 16, 'Viewed documents list', '2026-02-19 04:32:16', '::1', NULL),
(341, 16, 'Viewed documents list', '2026-02-19 04:32:19', '::1', NULL),
(342, 16, 'Viewed documents list', '2026-02-19 04:32:22', '::1', NULL),
(343, 3, 'Updated role', '2026-02-19 04:32:43', '::1', 'Updated role: approver'),
(344, 3, 'Viewed roles list', '2026-02-19 04:32:43', '::1', NULL),
(345, 16, 'Viewed documents list', '2026-02-19 04:32:46', '::1', NULL),
(346, 16, 'Viewed documents list', '2026-02-19 04:32:48', '::1', NULL),
(347, 1, 'Viewed user list', '2026-02-19 04:35:30', '::1', 'Viewed user list'),
(348, 1, 'Viewed user list', '2026-02-19 04:36:40', '::1', 'Viewed user list'),
(349, 1, 'Viewed documents list', '2026-02-19 04:36:42', '::1', NULL);

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
(13, 6),
(14, 4),
(15, 4),
(16, 5),
(17, 5);

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
  ADD KEY `documents_created_by_foreign` (`created_by`),
  ADD KEY `idx_documents_approval_status` (`approval_status`),
  ADD KEY `idx_documents_reviewer_id` (`reviewer_id`),
  ADD KEY `idx_documents_approver_id` (`approver_id`),
  ADD KEY `idx_documents_submitted_dates` (`submitted_for_review_at`,`submitted_for_approval_at`);
ALTER TABLE `documents` ADD FULLTEXT KEY `title` (`title`,`content`);

--
-- Indexes for table `document_approval_history`
--
ALTER TABLE `document_approval_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_approval_history_document_id_foreign` (`document_id`),
  ADD KEY `document_approval_history_performed_by_foreign` (`performed_by`),
  ADD KEY `idx_document_action` (`document_id`,`action`);

--
-- Indexes for table `document_backups`
--
ALTER TABLE `document_backups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `document_metadata`
--
ALTER TABLE `document_metadata`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_metadata_document_id_foreign` (`document_id`);

--
-- Indexes for table `document_search_index`
--
ALTER TABLE `document_search_index`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`);
ALTER TABLE `document_search_index` ADD FULLTEXT KEY `search_terms` (`search_terms`,`indexed_content`,`tags`,`keywords`);

--
-- Indexes for table `document_shares`
--
ALTER TABLE `document_shares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_shares_shared_with_user_id_foreign` (`shared_with_user_id`),
  ADD KEY `document_shares_shared_with_role_id_foreign` (`shared_with_role_id`),
  ADD KEY `document_shares_shared_with_department_id_foreign` (`shared_with_department_id`),
  ADD KEY `document_shares_created_by_foreign` (`created_by`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_versions_created_by_foreign` (`created_by`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `document_workflows`
--
ALTER TABLE `document_workflows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_workflows_assigned_to_foreign` (`assigned_to`),
  ADD KEY `document_id` (`document_id`);

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document_approval_history`
--
ALTER TABLE `document_approval_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `document_backups`
--
ALTER TABLE `document_backups`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_metadata`
--
ALTER TABLE `document_metadata`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `document_search_index`
--
ALTER TABLE `document_search_index`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_shares`
--
ALTER TABLE `document_shares`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `document_versions`
--
ALTER TABLE `document_versions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_workflows`
--
ALTER TABLE `document_workflows`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_activity_logs`
--
ALTER TABLE `user_activity_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=350;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `documents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `documents_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `documents_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `documents_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_approval_history`
--
ALTER TABLE `document_approval_history`
  ADD CONSTRAINT `document_approval_history_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_approval_history_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_backups`
--
ALTER TABLE `document_backups`
  ADD CONSTRAINT `document_backups_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_metadata`
--
ALTER TABLE `document_metadata`
  ADD CONSTRAINT `document_metadata_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_search_index`
--
ALTER TABLE `document_search_index`
  ADD CONSTRAINT `document_search_index_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_shares`
--
ALTER TABLE `document_shares`
  ADD CONSTRAINT `document_shares_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_shares_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_shares_shared_with_department_id_foreign` FOREIGN KEY (`shared_with_department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_shares_shared_with_role_id_foreign` FOREIGN KEY (`shared_with_role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_shares_shared_with_user_id_foreign` FOREIGN KEY (`shared_with_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_versions`
--
ALTER TABLE `document_versions`
  ADD CONSTRAINT `document_versions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_versions_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `document_workflows`
--
ALTER TABLE `document_workflows`
  ADD CONSTRAINT `document_workflows_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_workflows_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
