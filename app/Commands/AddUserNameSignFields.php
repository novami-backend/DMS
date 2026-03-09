<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddUserNameSignFields extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:add-user-name-sign';
    protected $description = 'Add name and sign fields to users table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Adding name and sign fields to users table...', 'yellow');
        CLI::newLine();

        try {
            // Check and add name column
            $query = $db->query("SHOW COLUMNS FROM users LIKE 'name'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ name column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE users 
                    ADD COLUMN name VARCHAR(255) NULL AFTER username
                ");
                CLI::write('✓ Added name column', 'green');
            }

            // Check and add sign (signature) column
            $query = $db->query("SHOW COLUMNS FROM users LIKE 'sign'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ sign column already exists', 'yellow');
            } else {
                $db->query("
                    ALTER TABLE users 
                    ADD COLUMN sign VARCHAR(500) NULL AFTER email
                ");
                CLI::write('✓ Added sign (signature) column', 'green');
            }

            CLI::newLine();
            CLI::write('Name and sign fields added successfully!', 'green');
            CLI::newLine();
            CLI::write('Note: sign field stores the path to signature image file', 'white');

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
        }
    }
}
