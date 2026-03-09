-- Simple Document Approval System
-- Add approval fields to existing documents table

ALTER TABLE `documents` 
ADD COLUMN `approval_status` ENUM('pending', 'sent_for_review', 'reviewed', 'approved', 'rejected') DEFAULT 'pending' AFTER `status`,
ADD COLUMN `reviewer_id` INT(11) UNSIGNED DEFAULT NULL AFTER `approval_status`,
ADD COLUMN `reviewer_comments` TEXT DEFAULT NULL AFTER `reviewer_id`,
ADD COLUMN `reviewed_at` DATETIME DEFAULT NULL AFTER `reviewer_comments`,
ADD COLUMN `approver_id` INT(11) UNSIGNED DEFAULT NULL AFTER `reviewed_at`,
ADD COLUMN `approver_comments` TEXT DEFAULT NULL AFTER `approver_id`,
ADD COLUMN `approved_at` DATETIME DEFAULT NULL AFTER `approver_comments`,
ADD COLUMN `rejection_reason` TEXT DEFAULT NULL AFTER `approved_at`,
ADD COLUMN `rejected_at` DATETIME DEFAULT NULL AFTER `rejection_reason`,
ADD COLUMN `submitted_for_review_at` DATETIME DEFAULT NULL AFTER `rejected_at`,
ADD COLUMN `submitted_for_approval_at` DATETIME DEFAULT NULL AFTER `submitted_for_review_at`;

-- Add foreign key constraints
ALTER TABLE `documents` 
ADD CONSTRAINT `documents_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
ADD CONSTRAINT `documents_approver_id_foreign` FOREIGN KEY (`approver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Create document_approval_history table for audit trail
CREATE TABLE IF NOT EXISTS `document_approval_history` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` int(11) UNSIGNED NOT NULL,
  `action` enum('submitted_for_review', 'reviewed', 'submitted_for_approval', 'approved', 'rejected', 'returned_for_revision') NOT NULL,
  `performed_by` int(11) UNSIGNED NOT NULL,
  `comments` text DEFAULT NULL,
  `previous_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `document_approval_history_document_id_foreign` (`document_id`),
  KEY `document_approval_history_performed_by_foreign` (`performed_by`),
  KEY `idx_document_action` (`document_id`, `action`),
  CONSTRAINT `document_approval_history_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `document_approval_history_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Update existing documents to have proper approval status
UPDATE `documents` SET `approval_status` = 'approved' WHERE `status` = 'active';
UPDATE `documents` SET `approval_status` = 'pending' WHERE `status` = 'draft';

-- Create indexes for better performance
CREATE INDEX idx_documents_approval_status ON documents(approval_status);
CREATE INDEX idx_documents_reviewer_id ON documents(reviewer_id);
CREATE INDEX idx_documents_approver_id ON documents(approver_id);
CREATE INDEX idx_documents_submitted_dates ON documents(submitted_for_review_at, submitted_for_approval_at);