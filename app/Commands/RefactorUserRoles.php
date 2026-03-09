<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RefactorUserRoles extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:refactor-user-roles';
    protected $description = 'Refactor user_roles table to role_id column in users table';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Refactoring User Roles Structure...', 'yellow');
        CLI::write(str_repeat("=", 80));
        CLI::newLine();

        try {
            // Step 1: Check if role_id column already exists
            CLI::write('Step 1: Checking if role_id column exists...', 'cyan');
            $query = $db->query("SHOW COLUMNS FROM users LIKE 'role_id'");
            
            if ($query->getNumRows() > 0) {
                CLI::write('  ✓ role_id column already exists', 'yellow');
            } else {
                // Add role_id column
                $db->query("
                    ALTER TABLE users 
                    ADD COLUMN role_id INT(11) UNSIGNED NULL AFTER email
                ");
                CLI::write('  ✓ Added role_id column to users table', 'green');
            }
            CLI::newLine();

            // Step 2: Migrate data from user_roles to users.role_id
            CLI::write('Step 2: Migrating data from user_roles to users.role_id...', 'cyan');
            
            // Check if user_roles table exists
            $query = $db->query("SHOW TABLES LIKE 'user_roles'");
            if ($query->getNumRows() > 0) {
                // Get all user-role mappings
                $userRoles = $db->query("SELECT user_id, role_id FROM user_roles")->getResultArray();
                
                $migratedCount = 0;
                foreach ($userRoles as $userRole) {
                    $db->query("
                        UPDATE users 
                        SET role_id = ? 
                        WHERE id = ?
                    ", [$userRole['role_id'], $userRole['user_id']]);
                    $migratedCount++;
                }
                
                CLI::write("  ✓ Migrated {$migratedCount} user-role assignments", 'green');
            } else {
                CLI::write('  - user_roles table does not exist', 'yellow');
            }
            CLI::newLine();

            // Step 3: Add foreign key constraint
            CLI::write('Step 3: Adding foreign key constraint...', 'cyan');
            try {
                // Check if constraint already exists
                $query = $db->query("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_NAME = 'users' 
                    AND CONSTRAINT_NAME = 'users_role_id_fk'
                ");
                
                if ($query->getNumRows() > 0) {
                    CLI::write('  - Foreign key constraint already exists', 'yellow');
                } else {
                    $db->query("
                        ALTER TABLE users 
                        ADD CONSTRAINT users_role_id_fk 
                        FOREIGN KEY (role_id) 
                        REFERENCES roles(id) 
                        ON DELETE SET NULL 
                        ON UPDATE CASCADE
                    ");
                    CLI::write('  ✓ Added foreign key constraint', 'green');
                }
            } catch (\Exception $e) {
                CLI::write('  - Could not add foreign key: ' . $e->getMessage(), 'yellow');
            }
            CLI::newLine();

            // Step 4: Drop user_roles table
            CLI::write('Step 4: Dropping user_roles table...', 'cyan');
            $query = $db->query("SHOW TABLES LIKE 'user_roles'");
            if ($query->getNumRows() > 0) {
                $db->query("DROP TABLE user_roles");
                CLI::write('  ✓ Dropped user_roles table', 'green');
            } else {
                CLI::write('  - user_roles table does not exist', 'yellow');
            }
            CLI::newLine();

            // Step 5: Verify the changes
            CLI::write('Step 5: Verifying changes...', 'cyan');
            $usersWithRoles = $db->query("
                SELECT u.id, u.username, u.role_id, r.role_name 
                FROM users u 
                LEFT JOIN roles r ON r.id = u.role_id 
                LIMIT 5
            ")->getResultArray();
            
            CLI::write('  Sample users with roles:', 'white');
            foreach ($usersWithRoles as $user) {
                $roleName = $user['role_name'] ?? 'No role';
                CLI::write("    - {$user['username']}: {$roleName} (role_id: {$user['role_id']})", 'white');
            }
            CLI::newLine();

            CLI::write(str_repeat("=", 80));
            CLI::write('✓ Refactoring completed successfully!', 'green');
            CLI::newLine();
            CLI::write('Next steps:', 'yellow');
            CLI::write('1. Update User model methods', 'white');
            CLI::write('2. Update permission helper', 'white');
            CLI::write('3. Update Auth controller', 'white');
            CLI::write('4. Test login and permissions', 'white');

        } catch (\Exception $e) {
            CLI::error('Error: ' . $e->getMessage());
            CLI::error($e->getTraceAsString());
        }
    }
}
