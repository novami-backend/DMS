<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddFormDataColumn extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'db:add-form-data';
    protected $description = 'Adds template_id and form_data columns to documents table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        try {
            // Check if columns already exist
            $query = $db->query("SHOW COLUMNS FROM documents LIKE 'form_data'");
            if ($query->getNumRows() > 0) {
                CLI::write('Column form_data already exists!', 'yellow');
                return;
            }
            
            // Add template_id column
            $db->query("ALTER TABLE documents ADD COLUMN template_id INT(11) UNSIGNED NULL AFTER type_id");
            CLI::write('Added template_id column', 'green');
            
            // Add form_data column
            $db->query("ALTER TABLE documents ADD COLUMN form_data JSON NULL AFTER content");
            CLI::write('Added form_data column', 'green');
            
            // Add foreign key
            $db->query("ALTER TABLE documents ADD CONSTRAINT fk_documents_template FOREIGN KEY (template_id) REFERENCES document_templates(id) ON DELETE SET NULL ON UPDATE CASCADE");
            CLI::write('Added foreign key constraint', 'green');
            
            CLI::write('Successfully added columns!', 'green');
            
        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
        }
    }
}
