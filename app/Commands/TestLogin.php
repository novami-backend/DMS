<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestLogin extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'users:test-login';
    protected $description = 'Test login functionality for admin and superadmin';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Testing Login Functionality', 'yellow');
        CLI::write(str_repeat("=", 80));
        CLI::newLine();

        // Test superadmin
        $this->testUser('superadmin', 'superadmin123', $db);
        CLI::newLine();

        // Test admin
        $this->testUser('admin', 'admin123', $db);
        CLI::newLine();

        CLI::write(str_repeat("=", 80));
        CLI::write('Test Complete!', 'green');
    }

    private function testUser($username, $password, $db)
    {
        CLI::write("Testing: {$username}", 'cyan');
        CLI::write(str_repeat("-", 80));

        // Get user
        $query = $db->query("SELECT * FROM users WHERE username = ?", [$username]);
        $user = $query->getRowArray();

        if (!$user) {
            CLI::error("✗ User '{$username}' not found!");
            return;
        }

        CLI::write("✓ User found (ID: {$user['id']})", 'green');
        CLI::write("  Email: {$user['email']}");
        CLI::write("  Status: {$user['status']}");

        // Test password
        if (password_verify($password, $user['password_hash'])) {
            CLI::write("✓ Password verification successful", 'green');
        } else {
            CLI::error("✗ Password verification failed!");
            return;
        }

        // Get role
        $roleQuery = $db->query("
            SELECT r.role_name, r.id as role_id
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.id = ?
        ", [$user['id']]);
        
        $role = $roleQuery->getRowArray();

        if ($role) {
            CLI::write("✓ Role assigned: {$role['role_name']} (ID: {$role['role_id']})", 'green');
        } else {
            CLI::error("✗ No role assigned!");
            return;
        }

        // Check permissions
        $permQuery = $db->query("
            SELECT COUNT(*) as perm_count
            FROM role_permissions
            WHERE role_id = ?
        ", [$role['role_id']]);
        
        $permCount = $permQuery->getRow()->perm_count;
        CLI::write("✓ Permissions assigned: {$permCount}", 'green');

        CLI::write("\n✓ Login should work for: {$username} / {$password}", 'green');
    }
}
