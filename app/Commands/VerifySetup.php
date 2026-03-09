<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class VerifySetup extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'verify:setup';
    protected $description = 'Verify complete system setup for login and permissions';

    public function run(array $params)
    {
        CLI::write('=======================================================', 'yellow');
        CLI::write('   DOCUMENT MANAGEMENT SYSTEM - SETUP VERIFICATION', 'yellow');
        CLI::write('=======================================================', 'yellow');
        CLI::newLine();

        $allPassed = true;

        // 1. Database Connection
        CLI::write('1. Database Connection', 'cyan');
        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');
            CLI::write('   ✓ Database connection successful', 'green');
        } catch (\Exception $e) {
            CLI::error('   ✗ Database connection failed: ' . $e->getMessage());
            $allPassed = false;
        }
        CLI::newLine();

        // 2. Users
        CLI::write('2. User Accounts', 'cyan');
        $superadmin = $db->query("SELECT * FROM users WHERE username = 'superadmin'")->getRowArray();
        $admin = $db->query("SELECT * FROM users WHERE username = 'admin'")->getRowArray();
        
        if ($superadmin && $superadmin['status'] === 'active') {
            CLI::write('   ✓ Superadmin user exists and is active', 'green');
            if (password_verify('superadmin123', $superadmin['password_hash'])) {
                CLI::write('   ✓ Superadmin password is correct', 'green');
            } else {
                CLI::error('   ✗ Superadmin password verification failed');
                $allPassed = false;
            }
        } else {
            CLI::error('   ✗ Superadmin user not found or inactive');
            $allPassed = false;
        }
        
        if ($admin && $admin['status'] === 'active') {
            CLI::write('   ✓ Admin user exists and is active', 'green');
            if (password_verify('admin123', $admin['password_hash'])) {
                CLI::write('   ✓ Admin password is correct', 'green');
            } else {
                CLI::error('   ✗ Admin password verification failed');
                $allPassed = false;
            }
        } else {
            CLI::error('   ✗ Admin user not found or inactive');
            $allPassed = false;
        }
        CLI::newLine();

        // 3. Roles
        CLI::write('3. Roles', 'cyan');
        $roles = $db->query("SELECT * FROM roles WHERE role_name IN ('superadmin', 'admin')")->getResultArray();
        if (count($roles) >= 2) {
            CLI::write('   ✓ Superadmin and Admin roles exist', 'green');
        } else {
            CLI::error('   ✗ Missing required roles');
            $allPassed = false;
        }
        CLI::newLine();

        // 4. Permissions
        CLI::write('4. Permissions', 'cyan');
        $permCount = $db->query("SELECT COUNT(*) as count FROM permissions")->getRow()->count;
        CLI::write("   ✓ {$permCount} permissions in database", 'green');
        
        $requiredPerms = ['dashboard_access', 'user_read', 'document_read', 'document_approve'];
        $missingPerms = [];
        foreach ($requiredPerms as $perm) {
            $exists = $db->query("SELECT COUNT(*) as count FROM permissions WHERE permission_key = ?", [$perm])->getRow()->count;
            if (!$exists) {
                $missingPerms[] = $perm;
            }
        }
        
        if (empty($missingPerms)) {
            CLI::write('   ✓ All required permissions exist', 'green');
        } else {
            CLI::error('   ✗ Missing permissions: ' . implode(', ', $missingPerms));
            $allPassed = false;
        }
        CLI::newLine();

        // 5. Role Permissions
        CLI::write('5. Role Permissions', 'cyan');
        $superadminRole = $db->query("SELECT id FROM roles WHERE role_name = 'superadmin'")->getRow();
        if ($superadminRole) {
            $superadminPermCount = $db->query("SELECT COUNT(*) as count FROM role_permissions WHERE role_id = ?", [$superadminRole->id])->getRow()->count;
            CLI::write("   ✓ Superadmin has {$superadminPermCount} permissions", 'green');
        }
        
        $adminRole = $db->query("SELECT id FROM roles WHERE role_name = 'admin'")->getRow();
        if ($adminRole) {
            $adminPermCount = $db->query("SELECT COUNT(*) as count FROM role_permissions WHERE role_id = ?", [$adminRole->id])->getRow()->count;
            CLI::write("   ✓ Admin has {$adminPermCount} permissions", 'green');
        }
        CLI::newLine();

        // 6. Files
        CLI::write('6. Required Files', 'cyan');
        $files = [
            'app/Controllers/Auth.php' => 'Auth Controller',
            'app/Views/auth/login.php' => 'Login View',
            'app/Views/dashboard/index.php' => 'Dashboard View',
            'app/Views/common/sidebar.php' => 'Sidebar View',
            'app/Helpers/permission_helper.php' => 'Permission Helper',
            'app/Filters/AuthFilter.php' => 'Auth Filter',
        ];
        
        foreach ($files as $path => $name) {
            if (file_exists(ROOTPATH . $path)) {
                CLI::write("   ✓ {$name} exists", 'green');
            } else {
                CLI::error("   ✗ {$name} not found");
                $allPassed = false;
            }
        }
        CLI::newLine();

        // 7. Session Directory
        CLI::write('7. Session Configuration', 'cyan');
        $sessionPath = WRITEPATH . 'session';
        if (is_dir($sessionPath) && is_writable($sessionPath)) {
            CLI::write('   ✓ Session directory exists and is writable', 'green');
        } else {
            CLI::error('   ✗ Session directory issue');
            $allPassed = false;
        }
        CLI::newLine();

        // 8. Helper Autoload
        CLI::write('8. Helper Autoload', 'cyan');
        $autoload = new \Config\Autoload();
        if (in_array('permission', $autoload->helpers)) {
            CLI::write('   ✓ Permission helper is autoloaded', 'green');
        } else {
            CLI::error('   ✗ Permission helper not autoloaded');
            $allPassed = false;
        }
        CLI::newLine();

        // Final Summary
        CLI::write('=======================================================', 'yellow');
        if ($allPassed) {
            CLI::write('   ✓✓✓ ALL CHECKS PASSED! ✓✓✓', 'green');
            CLI::newLine();
            CLI::write('Your system is ready to use!', 'green');
            CLI::newLine();
            CLI::write('Login at: ' . base_url('login'), 'white');
            CLI::write('Credentials:', 'white');
            CLI::write('  - superadmin / superadmin123', 'white');
            CLI::write('  - admin / admin123', 'white');
        } else {
            CLI::error('   ✗✗✗ SOME CHECKS FAILED ✗✗✗');
            CLI::newLine();
            CLI::write('Please review the errors above and fix them.', 'yellow');
        }
        CLI::write('=======================================================', 'yellow');
    }
}
