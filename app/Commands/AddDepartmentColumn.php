<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddDepartmentColumn extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:add-department-column';
    protected $description = 'Add department_id column to users table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Adding department_id column to users table...', 'yellow');
        CLI::newLine();

        try {
            // Check if column already exists
            $query = $db->query("SHOW COLUMNS FROM users LIKE 'department_id'");
            if ($query->getNumRows() > 0) {
                CLI::write('✓ Column department_id already exists', 'yellow');
                return;
            }

            // Add the column
            $db->query("
                ALTER TABLE users 
                ADD COLUMN department_id INT(11) UNSIGNED NULL AFTER status
            ");
            CLI::write('✓ Added department_id column', 'green');

            // Add foreign key constraint if departments table exists
            $query = $db->query("SHOW TABLES LIKE 'departments'");
            if ($query->getNumRows() > 0) {
                try {
                    $db->query("
                        ALTER TABLE users 
                        ADD CONSTRAINT users_department_id_fk 
                        FOREIGN KEY (department_id) 
                        REFERENCES departments(id) 
                        ON DELETE SET NULL 
                        ON UPDATE CASCADE
                    ");
                    CLI::write('✓ Added foreign key constraint', 'green');
                } catch (\Exception $e) {
                    CLI::write('- Foreign key constraint already exists or could not be added', 'yellow');
                }
            }

            CLI::newLine();
            CLI::write('Department column added successfully!', 'green');

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
        }
    }
}
