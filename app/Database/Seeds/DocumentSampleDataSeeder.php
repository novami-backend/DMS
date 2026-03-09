<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DocumentSampleDataSeeder extends Seeder
{
    public function run()
    {
        // Sample document types
        $documentTypes = [
            [
                'name' => 'Quality Manual',
                'description' => 'Main quality manual containing organizational policies and procedures',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'SOP',
                'description' => 'Standard Operating Procedures for various processes',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Policy',
                'description' => 'Organizational policies and guidelines',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Work Instruction',
                'description' => 'Detailed work instructions for specific tasks',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Form',
                'description' => 'Standard forms used in various processes',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert document types, ignoring duplicates
        foreach ($documentTypes as $type) {
            $existing = $this->db->table('document_types')
                ->where('name', $type['name'])
                ->countAllResults();
                
            if ($existing === 0) {
                $this->db->table('document_types')->insert($type);
            }
        }

        // Sample departments
        $departments = [
            [
                'name' => 'Quality Assurance',
                'description' => 'Department responsible for quality assurance and compliance',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Human Resources',
                'description' => 'Department managing human resources and personnel matters',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Production',
                'description' => 'Department responsible for production and manufacturing',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Research & Development',
                'description' => 'Department handling research and product development',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Finance',
                'description' => 'Department managing financial operations',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert departments, ignoring duplicates
        foreach ($departments as $dept) {
            $existing = $this->db->table('departments')
                ->where('name', $dept['name'])
                ->countAllResults();
                
            if ($existing === 0) {
                $this->db->table('departments')->insert($dept);
            }
        }
    }
}