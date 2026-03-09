<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'permission_key' => 'department_create',
                'description' => 'Allow creating new departments',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'department_read',
                'description' => 'Allow viewing departments',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'department_update',
                'description' => 'Allow editing existing departments',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'department_delete',
                'description' => 'Allow deleting departments',
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