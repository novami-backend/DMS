-- Add 'returned_for_revision' status to documents table
-- This allows documents to be tracked separately when sent back for revision

ALTER TABLE `documents` 
MODIFY COLUMN `approval_status` ENUM('pending', 'sent_for_review', 'reviewed', 'approved', 'rejected', 'returned_for_revision') DEFAULT 'pending';

-- Add additional fields to track revision information
ALTER TABLE `documents` 
ADD COLUMN `returned_for_revision_at` DATETIME DEFAULT NULL AFTER `rejected_at`,
ADD COLUMN `revision_comments` TEXT DEFAULT NULL AFTER `returned_for_revision_at`,
ADD COLUMN `revision_count` INT DEFAULT 0 AFTER `revision_comments`;

-- Create index for the new status
CREATE INDEX idx_documents_returned_for_revision ON documents(returned_for_revision_at);

-- Update any existing documents that were returned for revision
-- (This will help if there are existing documents in this state)
UPDATE `documents` 
SET `approval_status` = 'returned_for_revision', 
    `returned_for_revision_at` = NOW(),
    `revision_count` = 1
WHERE `approval_status` = 'pending' 
AND `reviewer_comments` IS NOT NULL 
AND `reviewer_comments` LIKE '%revision%';