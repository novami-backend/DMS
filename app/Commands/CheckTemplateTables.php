<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckTemplateTables extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:check-template-tables';
    protected $description = 'Check template tables status';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Checking template tables...', 'yellow');
        CLI::newLine();

        try {
            // Check document_templates
            $query = $db->query("SHOW TABLES LIKE 'document_templates'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ document_templates table exists', 'green');
                
                // Check table structure
                $structure = $db->query("DESCRIBE document_templates")->getResultArray();
                CLI::write('  Columns:', 'white');
                foreach ($structure as $column) {
                    CLI::write("    - {$column['Field']} ({$column['Type']})", 'white');
                }
                
                // Check engine
                $engine = $db->query("SHOW TABLE STATUS LIKE 'document_templates'")->getRowArray();
                CLI::write("  Engine: {$engine['Engine']}", 'white');
                CLI::write("  Rows: {$engine['Rows']}", 'white');
            } else {
                CLI::error('✗ document_templates table does not exist');
            }
            CLI::newLine();

            // Check template_fields
            $query = $db->query("SHOW TABLES LIKE 'template_fields'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ template_fields table exists', 'green');
                
                // Check table structure
                $structure = $db->query("DESCRIBE template_fields")->getResultArray();
                CLI::write('  Columns:', 'white');
                foreach ($structure as $column) {
                    CLI::write("    - {$column['Field']} ({$column['Type']})", 'white');
                }
                
                // Check engine
                $engine = $db->query("SHOW TABLE STATUS LIKE 'template_fields'")->getRowArray();
                CLI::write("  Engine: {$engine['Engine']}", 'white');
                CLI::write("  Rows: {$engine['Rows']}", 'white');
            } else {
                CLI::error('✗ template_fields table does not exist');
            }
            CLI::newLine();

            // Try to query the tables
            CLI::write('Testing queries...', 'cyan');
            try {
                $count = $db->table('document_templates')->countAllResults();
                CLI::write("✓ Can query document_templates ({$count} records)", 'green');
            } catch (\Exception $e) {
                CLI::error("✗ Cannot query document_templates: " . $e->getMessage());
            }

            try {
                $count = $db->table('template_fields')->countAllResults();
                CLI::write("✓ Can query template_fields ({$count} records)", 'green');
            } catch (\Exception $e) {
                CLI::error("✗ Cannot query template_fields: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
        }
    }
}
