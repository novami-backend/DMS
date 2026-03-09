<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DocumentPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'permission_key' => 'document_create',
                'description' => 'Allow creating new documents',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'document_read',
                'description' => 'Allow viewing documents',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'document_update',
                'description' => 'Allow editing existing documents',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'document_delete',
                'description' => 'Allow deleting documents',
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