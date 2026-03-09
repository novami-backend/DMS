<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'permission_key' => 'view_permissions',
                'description' => 'Allow viewing permissions list',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'create_permissions',
                'description' => 'Allow creating new permissions',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'edit_permissions',
                'description' => 'Allow editing existing permissions',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'delete_permissions',
                'description' => 'Allow deleting permissions',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'create_users',
                'description' => 'Allow creating new users',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'edit_users',
                'description' => 'Allow editing existing users',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'delete_users',
                'description' => 'Allow deleting users',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'create_roles',
                'description' => 'Allow creating new roles',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'edit_roles',
                'description' => 'Allow editing existing roles',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'delete_roles',
                'description' => 'Allow deleting roles',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert permissions, ignoring duplicates
        foreach ($permissions as $permission) {
            $existing = $this->db->table('permissions')
                ->where('permission_key', $permission['permission_key'])
                ->countAllResults();
                
            if ($existing === 0) {
                $this->db->table('permissions')->insert($permission);
            }
        }
    }
}