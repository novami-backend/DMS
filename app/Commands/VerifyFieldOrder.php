<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifyFieldOrder extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'verify:field-order';
    protected $description = 'Verify no field_order references remain';

    public function run(array $params)
    {
        CLI::write('Verifying field_order references...', 'yellow');
        CLI::newLine();

        $files = [
            'app/Models/TemplateField.php',
            'app/Models/DocumentTemplate.php',
            'app/Controllers/Templates.php',
            'app/Views/templates/edit.php',
            'app/Database/Seeds/TemplateSeeder.php'
        ];

        $foundIssues = false;

        foreach ($files as $file) {
            $fullPath = ROOTPATH . $file;
            if (!file_exists($fullPath)) {
                CLI::write("  - Skipping {$file} (not found)", 'yellow');
                continue;
            }

            $content = file_get_contents($fullPath);
            
            // Check for field_order (excluding comments)
            if (preg_match("/['\"]field_order['\"]/", $content)) {
                CLI::error("  ✗ Found field_order in {$file}");
                $foundIssues = true;
            } else {
                CLI::write("  ✓ {$file} - OK", 'green');
            }
        }

        CLI::newLine();

        if ($foundIssues) {
            CLI::error('Some files still contain field_order references!');
        } else {
            CLI::write('✓ All files updated to use display_order', 'green');
        }

        // Check database
        CLI::newLine();
        CLI::write('Checking database...', 'cyan');
        
        $db = \Config\Database::connect();
        
        try {
            $query = $db->query("SHOW COLUMNS FROM template_fields LIKE 'display_order'");
            if ($query->getNumRows() > 0) {
                CLI::write('  ✓ display_order column exists in template_fields', 'green');
            } else {
                CLI::error('  ✗ display_order column not found in template_fields');
            }
        } catch (\Exception $e) {
            CLI::error('  ✗ Error checking database: ' . $e->getMessage());
        }
    }
}
