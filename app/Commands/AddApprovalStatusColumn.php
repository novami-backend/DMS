<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddApprovalStatusColumn extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:add-approval-status';
    protected $description = 'Add approval_status and related columns to documents table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Adding approval workflow columns to documents table...', 'yellow');
        CLI::newLine();

        try {
            // Check if approval_status column exists
            $query = $db->query("SHOW COLUMNS FROM documents LIKE 'approval_status'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ approval_status column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE documents 
                    ADD COLUMN approval_status ENUM('pending', 'sent_for_review', 'reviewed', 'approved', 'rejected', 'returned_for_revision') 
                    DEFAULT 'pending' 
                    AFTER status
                ");
                CLI::write('✓ Added approval_status column', 'green');
            }

            // Check and add reviewer_id
            $query = $db->query("SHOW COLUMNS FROM documents LIKE 'reviewer_id'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ reviewer_id column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE documents 
                    ADD COLUMN reviewer_id INT(11) UNSIGNED NULL AFTER approval_status
                ");
                CLI::write('✓ Added reviewer_id column', 'green');
            }

            // Check and add approver_id
            $query = $db->query("SHOW COLUMNS FROM documents LIKE 'approver_id'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ approver_id column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE documents 
                    ADD COLUMN approver_id INT(11) UNSIGNED NULL AFTER reviewer_id
                ");
                CLI::write('✓ Added approver_id column', 'green');
            }

            // Check and add reviewer_comments
            $query = $db->query("SHOW COLUMNS FROM documents LIKE 'reviewer_comments'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ reviewer_comments column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE documents 
                    ADD COLUMN reviewer_comments TEXT NULL AFTER approver_id
                ");
                CLI::write('✓ Added reviewer_comments column', 'green');
            }

            // Check and add approver_comments
            $query = $db->query("SHOW COLUMNS FROM documents LIKE 'approver_comments'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ approver_comments column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE documents 
                    ADD COLUMN approver_comments TEXT NULL AFTER reviewer_comments
                ");
                CLI::write('✓ Added approver_comments column', 'green');
            }

            // Check and add rejection_reason
            $query = $db->query("SHOW COLUMNS FROM documents LIKE 'rejection_reason'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ rejection_reason column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE documents 
                    ADD COLUMN rejection_reason TEXT NULL AFTER approver_comments
                ");
                CLI::write('✓ Added rejection_reason column', 'green');
            }

            // Check and add revision_comments
            $query = $db->query("SHOW COLUMNS FROM documents LIKE 'revision_comments'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ revision_comments column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE documents 
                    ADD COLUMN revision_comments TEXT NULL AFTER rejection_reason
                ");
                CLI::write('✓ Added revision_comments column', 'green');
            }

            // Check and add timestamp columns
            $timestampColumns = [
                'submitted_for_review_at' => 'DATETIME NULL AFTER revision_comments',
                'reviewed_at' => 'DATETIME NULL AFTER submitted_for_review_at',
                'submitted_for_approval_at' => 'DATETIME NULL AFTER reviewed_at',
                'approved_at' => 'DATETIME NULL AFTER submitted_for_approval_at',
                'rejected_at' => 'DATETIME NULL AFTER approved_at',
                'returned_for_revision_at' => 'DATETIME NULL AFTER rejected_at'
            ];

            foreach ($timestampColumns as $column => $definition) {
                $query = $db->query("SHOW COLUMNS FROM documents LIKE '{$column}'");
                if ($query->getNumRows() > 0) {
                    CLI::write("✓ {$column} column already exists", 'yellow');
                } else {
                    $db->query("ALTER TABLE documents ADD COLUMN {$column} {$definition}");
                    CLI::write("✓ Added {$column} column", 'green');
                }
            }

            CLI::newLine();
            CLI::write('Approval workflow columns added successfully!', 'green');

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
        }
    }
}
