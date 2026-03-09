<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AuthSeeder extends Seeder
{
    public function run()
    {
        // Create default roles
        $roles = [
            [
                'role_name' => 'superadmin',
                'description' => 'Super Administrator with unrestricted access'
            ],
            [
                'role_name' => 'admin',
                'description' => 'Administrator with full access'
            ],
            [
                'role_name' => 'manager',
                'description' => 'Manager with moderate access'
            ],
            [
                'role_name' => 'user',
                'description' => 'Regular user with limited access'
            ]
        ];

        $this->db->table('roles')->insertBatch($roles);

        // Create default permissions
        $permissions = [
            ['permission_key' => 'user_create', 'description' => 'Create users'],
            ['permission_key' => 'user_read', 'description' => 'View users'],
            ['permission_key' => 'user_update', 'description' => 'Update users'],
            ['permission_key' => 'user_delete', 'description' => 'Delete users'],
            ['permission_key' => 'role_create', 'description' => 'Create roles'],
            ['permission_key' => 'role_read', 'description' => 'View roles'],
            ['permission_key' => 'role_update', 'description' => 'Update roles'],
            ['permission_key' => 'role_delete', 'description' => 'Delete roles'],
            ['permission_key' => 'permission_create', 'description' => 'Create permissions'],
            ['permission_key' => 'permission_read', 'description' => 'View permissions'],
            ['permission_key' => 'permission_update', 'description' => 'Update permissions'],
            ['permission_key' => 'permission_delete', 'description' => 'Delete permissions'],
            ['permission_key' => 'dashboard_access', 'description' => 'Access dashboard'],
            ['permission_key' => 'reports_view', 'description' => 'View reports']
        ];

        $this->db->table('permissions')->insertBatch($permissions);

        // Assign permissions to roles
        // Superadmin gets all permissions
        $superadminRoleId = $this->db->table('roles')->where('role_name', 'superadmin')->get()->getRow()->id;
        $allPermissions = $this->db->table('permissions')->get()->getResultArray();
        
        $rolePermissions = [];
        foreach ($allPermissions as $permission) {
            $rolePermissions[] = [
                'role_id' => $superadminRoleId,
                'permission_id' => $permission['id']
            ];
        }
        $this->db->table('role_permissions')->insertBatch($rolePermissions);

        // Admin gets all permissions
        $adminRoleId = $this->db->table('roles')->where('role_name', 'admin')->get()->getRow()->id;
        
        $adminRolePermissions = [];
        foreach ($allPermissions as $permission) {
            $adminRolePermissions[] = [
                'role_id' => $adminRoleId,
                'permission_id' => $permission['id']
            ];
        }
        $this->db->table('role_permissions')->insertBatch($adminRolePermissions);

        // Manager gets most permissions except user/role management
        $managerRoleId = $this->db->table('roles')->where('role_name', 'manager')->get()->getRow()->id;
        $managerPermissions = $this->db->table('permissions')
            ->whereNotIn('permission_key', ['user_create', 'user_delete', 'role_create', 'role_delete', 'permission_create', 'permission_delete'])
            ->get()->getResultArray();
        
        $managerRolePermissions = [];
        foreach ($managerPermissions as $permission) {
            $managerRolePermissions[] = [
                'role_id' => $managerRoleId,
                'permission_id' => $permission['id']
            ];
        }
        $this->db->table('role_permissions')->insertBatch($managerRolePermissions);

        // User gets basic permissions
        $userRoleId = $this->db->table('roles')->where('role_name', 'user')->get()->getRow()->id;
        $userPermissions = $this->db->table('permissions')
            ->whereIn('permission_key', ['user_read', 'dashboard_access', 'reports_view'])
            ->get()->getResultArray();
        
        $userRolePermissions = [];
        foreach ($userPermissions as $permission) {
            $userRolePermissions[] = [
                'role_id' => $userRoleId,
                'permission_id' => $permission['id']
            ];
        }
        $this->db->table('role_permissions')->insertBatch($userRolePermissions);

        // Create default superadmin user
        $superadminUser = [
            'username' => 'superadmin',
            'password_hash' => password_hash('superadmin123', PASSWORD_DEFAULT),
            'email' => 'superadmin@example.com',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('users')->insert($superadminUser);
        $superadminUserId = $this->db->insertID();

        // Assign superadmin role to superadmin user (set role_id directly)
        $this->db->query("UPDATE users SET role_id = ? WHERE id = ?", [$superadminRoleId, $superadminUserId]);

        // Create default admin user
        $adminUser = [
            'username' => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'email' => 'admin@example.com',
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('users')->insert($adminUser);
        $adminUserId = $this->db->insertID();

        // Assign admin role to admin user (set role_id directly)
        $this->db->query("UPDATE users SET role_id = ? WHERE id = ?", [$adminRoleId, $adminUserId]);
    }
}