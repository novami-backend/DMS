<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateDocumentNumberFormat extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'update:docnumber-format';
    protected $description = 'Updates document number format from SSP/MR/001 to SSP/MR/001/001';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Updating document number format...', 'yellow');
        CLI::newLine();

        // Update template fields placeholders
        CLI::write('Updating template field placeholders...', 'cyan');
        $builder = $db->table('template_fields');
        $builder->where('field_name', 'doc_number');
        $builder->where('placeholder LIKE', '%/%/%'); // Already has 3 slashes
        $alreadyUpdated = $builder->countAllResults();

        if ($alreadyUpdated > 0) {
            CLI::write("Found {$alreadyUpdated} template fields already using new format.", 'green');
        }

        // Update old format placeholders
        $builder = $db->table('template_fields');
        $builder->where('field_name', 'doc_number');
        $builder->where('placeholder NOT LIKE', '%/%/%/%'); // Doesn't have 4 slashes
        $builder->where('placeholder LIKE', '%/%/%'); // Has 2 slashes (old format)
        
        $oldFormatFields = $builder->get()->getResultArray();
        
        if (count($oldFormatFields) > 0) {
            foreach ($oldFormatFields as $field) {
                $oldPlaceholder = $field['placeholder'];
                $newPlaceholder = $oldPlaceholder . '/001';
                
                $db->table('template_fields')
                    ->where('id', $field['id'])
                    ->update(['placeholder' => $newPlaceholder]);
                
                CLI::write("  Updated: {$oldPlaceholder} → {$newPlaceholder}", 'green');
            }
        } else {
            CLI::write('No template fields need updating.', 'green');
        }

        CLI::newLine();
        CLI::write('Document number format update completed!', 'green');
        CLI::newLine();
        
        // Show information about existing documents
        $existingDocs = $db->table('documents')
            ->where('document_number IS NOT NULL')
            ->where('document_number !=', '')
            ->countAllResults();
        
        if ($existingDocs > 0) {
            CLI::write("Note: Found {$existingDocs} existing documents with document numbers.", 'yellow');
            CLI::write('Existing documents will keep their current format.', 'yellow');
            CLI::write('New documents will use the new format: SSP/MR/001/001', 'yellow');
        }
    }
}
