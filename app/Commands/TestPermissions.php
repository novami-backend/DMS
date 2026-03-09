<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestPermissions extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:permissions';
    protected $description = 'Test permissions for superadmin and admin users';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Testing User Permissions', 'yellow');
        CLI::write(str_repeat("=", 80));
        CLI::newLine();

        // Test superadmin
        $this->testUserPermissions('superadmin', $db);
        CLI::newLine();

        // Test admin
        $this->testUserPermissions('admin', $db);
        CLI::newLine();

        CLI::write(str_repeat("=", 80));
        CLI::write('Test Complete!', 'green');
    }

    private function testUserPermissions($username, $db)
    {
        CLI::write("Testing: {$username}", 'cyan');
        CLI::write(str_repeat("-", 80));

        // Get user with role
        $user = $db->query("
            SELECT u.id, u.username, r.role_name, r.id as role_id
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.username = ?
        ", [$username])->getRowArray();

        if (!$user) {
            CLI::error("User not found!");
            return;
        }

        CLI::write("User ID: {$user['id']}", 'white');
        CLI::write("Role: {$user['role_name']}", 'white');

        // Get permissions count
        $permCount = $db->query("
            SELECT COUNT(*) as count
            FROM role_permissions
            WHERE role_id = ?
        ", [$user['role_id']])->getRow()->count;

        CLI::write("Permissions assigned to role: {$permCount}", 'white');

        // Test specific permissions
        $testPermissions = [
            'dashboard_access',
            'user_read',
            'role_read',
            'permission_read',
            'department_read',
            'document_type_read',
            'document_read',
            'document_approve'
        ];

        CLI::newLine();
        CLI::write("Testing specific permissions:", 'white');
        
        foreach ($testPermissions as $perm) {
            $hasPermission = $db->query("
                SELECT COUNT(*) as count
                FROM role_permissions rp
                JOIN permissions p ON p.id = rp.permission_id
                WHERE rp.role_id = ? AND p.permission_key = ?
            ", [$user['role_id'], $perm])->getRow()->count;

            if ($hasPermission > 0) {
                CLI::write("  ✓ {$perm}", 'green');
            } else {
                CLI::write("  ✗ {$perm}", 'red');
            }
        }
    }
}
