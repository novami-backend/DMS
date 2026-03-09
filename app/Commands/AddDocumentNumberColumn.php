<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddDocumentNumberColumn extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:add-document-number';
    protected $description = 'Add document_number column to documents table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        try {
            // Check if column already exists
            $fields = $db->getFieldNames('documents');
            if (in_array('document_number', $fields)) {
                CLI::write('Column document_number already exists!', 'yellow');
                return;
            }
            
            $sql = "ALTER TABLE documents ADD COLUMN document_number VARCHAR(100) NULL AFTER title";
            $db->query($sql);
            
            CLI::write('Column document_number added successfully!', 'green');
        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
        }
    }
}
