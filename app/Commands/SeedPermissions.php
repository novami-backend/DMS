<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SeedPermissions extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'permissions:seed';
    protected $description = 'Seed all required permissions for the application';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        CLI::write('Seeding Permissions...', 'yellow');
        CLI::write(str_repeat("=", 80));
        CLI::newLine();

        // Define all permissions needed by the application
        $permissions = [
            // Dashboard
            ['permission_key' => 'dashboard_access', 'description' => 'Access dashboard'],
            
            // Users
            ['permission_key' => 'user_read', 'description' => 'View users'],
            ['permission_key' => 'user_create', 'description' => 'Create users'],
            ['permission_key' => 'user_update', 'description' => 'Update users'],
            ['permission_key' => 'user_delete', 'description' => 'Delete users'],
            
            // Roles
            ['permission_key' => 'role_read', 'description' => 'View roles'],
            ['permission_key' => 'role_create', 'description' => 'Create roles'],
            ['permission_key' => 'role_update', 'description' => 'Update roles'],
            ['permission_key' => 'role_delete', 'description' => 'Delete roles'],
            
            // Permissions
            ['permission_key' => 'permission_read', 'description' => 'View permissions'],
            ['permission_key' => 'permission_create', 'description' => 'Create permissions'],
            ['permission_key' => 'permission_update', 'description' => 'Update permissions'],
            ['permission_key' => 'permission_delete', 'description' => 'Delete permissions'],
            
            // Departments
            ['permission_key' => 'department_read', 'description' => 'View departments'],
            ['permission_key' => 'department_create', 'description' => 'Create departments'],
            ['permission_key' => 'department_update', 'description' => 'Update departments'],
            ['permission_key' => 'department_delete', 'description' => 'Delete departments'],
            
            // Document Types
            ['permission_key' => 'document_type_read', 'description' => 'View document types'],
            ['permission_key' => 'document_type_create', 'description' => 'Create document types'],
            ['permission_key' => 'document_type_update', 'description' => 'Update document types'],
            ['permission_key' => 'document_type_delete', 'description' => 'Delete document types'],
            
            // Documents
            ['permission_key' => 'document_read', 'description' => 'View documents'],
            ['permission_key' => 'document_create', 'description' => 'Create documents'],
            ['permission_key' => 'document_update', 'description' => 'Update documents'],
            ['permission_key' => 'document_delete', 'description' => 'Delete documents'],
            ['permission_key' => 'document_approve', 'description' => 'Approve documents'],
            
            // Activity Logs
            ['permission_key' => 'activity_log_read', 'description' => 'View activity logs'],
            
            // Reports
            ['permission_key' => 'reports_view', 'description' => 'View reports'],
        ];

        $insertedCount = 0;
        $skippedCount = 0;

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $existing = $db->table('permissions')
                ->where('permission_key', $permission['permission_key'])
                ->countAllResults();
            
            if ($existing === 0) {
                $permission['created_at'] = date('Y-m-d H:i:s');
                $permission['updated_at'] = date('Y-m-d H:i:s');
                $db->table('permissions')->insert($permission);
                CLI::write("✓ Added: {$permission['permission_key']}", 'green');
                $insertedCount++;
            } else {
                CLI::write("- Skipped: {$permission['permission_key']} (already exists)", 'yellow');
                $skippedCount++;
            }
        }

        CLI::newLine();
        CLI::write(str_repeat("=", 80));
        CLI::write("Summary:", 'cyan');
        CLI::write("  Inserted: {$insertedCount}", 'green');
        CLI::write("  Skipped: {$skippedCount}", 'yellow');
        CLI::newLine();

        // Now assign all permissions to superadmin and admin roles
        CLI::write('Assigning permissions to roles...', 'yellow');
        
        // Get all permissions
        $allPermissions = $db->table('permissions')->get()->getResultArray();
        
        // Get superadmin role
        $superadminRole = $db->table('roles')->where('role_name', 'superadmin')->get()->getRow();
        if ($superadminRole) {
            // Clear existing permissions for superadmin
            $db->table('role_permissions')->where('role_id', $superadminRole->id)->delete();
            
            // Assign all permissions to superadmin
            $rolePermissions = [];
            foreach ($allPermissions as $permission) {
                $rolePermissions[] = [
                    'role_id' => $superadminRole->id,
                    'permission_id' => $permission['id']
                ];
            }
            if (!empty($rolePermissions)) {
                $db->table('role_permissions')->insertBatch($rolePermissions);
                CLI::write("✓ Assigned " . count($rolePermissions) . " permissions to superadmin role", 'green');
            }
        }
        
        // Get admin role
        $adminRole = $db->table('roles')->where('role_name', 'admin')->get()->getRow();
        if ($adminRole) {
            // Clear existing permissions for admin
            $db->table('role_permissions')->where('role_id', $adminRole->id)->delete();
            
            // Assign all permissions to admin
            $rolePermissions = [];
            foreach ($allPermissions as $permission) {
                $rolePermissions[] = [
                    'role_id' => $adminRole->id,
                    'permission_id' => $permission['id']
                ];
            }
            if (!empty($rolePermissions)) {
                $db->table('role_permissions')->insertBatch($rolePermissions);
                CLI::write("✓ Assigned " . count($rolePermissions) . " permissions to admin role", 'green');
            }
        }

        CLI::newLine();
        CLI::write('Permissions seeded successfully!', 'green');
    }
}
