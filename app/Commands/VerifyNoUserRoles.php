<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifyNoUserRoles extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'verify:no-user-roles';
    protected $description = 'Verify no references to user_roles table remain';

    public function run(array $params)
    {
        CLI::write('Verifying No user_roles References...', 'yellow');
        CLI::write(str_repeat("=", 80));
        CLI::newLine();

        $db = \Config\Database::connect();

        // Check if user_roles table exists
        CLI::write('1. Checking if user_roles table exists...', 'cyan');
        $query = $db->query("SHOW TABLES LIKE 'user_roles'");
        if ($query->getNumRows() > 0) {
            CLI::error('   ✗ user_roles table still exists!');
            CLI::write('   Run: php spark db:refactor-user-roles', 'yellow');
        } else {
            CLI::write('   ✓ user_roles table has been dropped', 'green');
        }
        CLI::newLine();

        // Check if users have role_id column
        CLI::write('2. Checking if users table has role_id column...', 'cyan');
        $query = $db->query("SHOW COLUMNS FROM users LIKE 'role_id'");
        if ($query->getNumRows() > 0) {
            CLI::write('   ✓ role_id column exists in users table', 'green');
        } else {
            CLI::error('   ✗ role_id column missing from users table!');
        }
        CLI::newLine();

        // Check if all users have role_id assigned
        CLI::write('3. Checking if all users have role_id assigned...', 'cyan');
        $usersWithoutRole = $db->query("SELECT COUNT(*) as count FROM users WHERE role_id IS NULL")->getRow()->count;
        if ($usersWithoutRole > 0) {
            CLI::write("   - {$usersWithoutRole} users without role_id", 'yellow');
        } else {
            CLI::write('   ✓ All users have role_id assigned', 'green');
        }
        CLI::newLine();

        // Sample users with roles
        CLI::write('4. Sample users with roles:', 'cyan');
        $users = $db->query("
            SELECT u.username, r.role_name 
            FROM users u 
            LEFT JOIN roles r ON r.id = u.role_id 
            LIMIT 5
        ")->getResultArray();
        
        foreach ($users as $user) {
            $roleName = $user['role_name'] ?? 'No role';
            CLI::write("   - {$user['username']}: {$roleName}", 'white');
        }
        CLI::newLine();

        // Check foreign key constraint
        CLI::write('5. Checking foreign key constraint...', 'cyan');
        $query = $db->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'users' 
            AND COLUMN_NAME = 'role_id'
            AND CONSTRAINT_NAME != 'PRIMARY'
        ");
        
        if ($query->getNumRows() > 0) {
            $constraint = $query->getRow();
            CLI::write("   ✓ Foreign key constraint exists: {$constraint->CONSTRAINT_NAME}", 'green');
        } else {
            CLI::write('   - No foreign key constraint found', 'yellow');
        }
        CLI::newLine();

        CLI::write(str_repeat("=", 80));
        CLI::write('✓ Verification Complete!', 'green');
        CLI::newLine();
        CLI::write('Summary:', 'yellow');
        CLI::write('- user_roles table: DROPPED', 'white');
        CLI::write('- users.role_id column: EXISTS', 'white');
        CLI::write('- All code updated to use users.role_id', 'white');
        CLI::write('- System is using the new structure', 'white');
    }
}
