<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DocumentTypePermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'permission_key' => 'document_type_create',
                'description' => 'Allow creating new document types',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'document_type_read',
                'description' => 'Allow viewing document types',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'document_type_update',
                'description' => 'Allow editing existing document types',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'permission_key' => 'document_type_delete',
                'description' => 'Allow deleting document types',
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