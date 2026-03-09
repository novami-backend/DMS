<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run()
    {
        // Sample Template 1: Standard System Procedure
        $template1Id = $this->db->table('document_templates')->insert([
            'document_type_id' => 1, // Assuming SOP type exists
            'name' => 'Standard System Procedure',
            'code' => 'SSP_001',
            'version' => '1.0',
            'description' => 'Template for Standard System Procedures with header and structured sections',
            'layout_template' => 'ssp_template.php',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $template1Id = $this->db->insertID();

        // Fields for Template 1
        $fields1 = [
            // Header Section
            [
                'template_id' => $template1Id,
                'field_name' => 'company_name',
                'field_label' => 'Company Name',
                'field_type' => 'text',
                'display_order' => 1,
                'section' => 'Document Header',
                'is_required' => 1,
                'is_autofill' => 1,
                'autofill_source' => 'department.name',
                'default_value' => 'Medzus Laboratories',
                'help_text' => 'Company or laboratory name',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'document_title',
                'field_label' => 'Document Title',
                'field_type' => 'text',
                'display_order' => 2,
                'section' => 'Document Header',
                'is_required' => 1,
                'default_value' => 'Standard System Procedure',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'doc_number',
                'field_label' => 'Document Number',
                'field_type' => 'text',
                'display_order' => 3,
                'section' => 'Document Header',
                'is_required' => 1,
                'is_autofill' => 1,
                'autofill_source' => 'document.next_number',
                'placeholder' => 'SSP/MR/001/001',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'issue_number',
                'field_label' => 'Issue Number',
                'field_type' => 'text',
                'display_order' => 4,
                'section' => 'Document Header',
                'is_required' => 1,
                'default_value' => '01',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'revision_number',
                'field_label' => 'Revision Number',
                'field_type' => 'text',
                'display_order' => 5,
                'section' => 'Document Header',
                'is_required' => 1,
                'default_value' => '00',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'effective_date',
                'field_label' => 'Effective Date',
                'field_type' => 'date',
                'display_order' => 6,
                'section' => 'Document Header',
                'is_required' => 1,
                'is_autofill' => 1,
                'autofill_source' => 'system.date',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'page_count',
                'field_label' => 'Page Count',
                'field_type' => 'text',
                'display_order' => 7,
                'section' => 'Document Header',
                'default_value' => '1 of 2',
            ],
            
            // Content Sections
            [
                'template_id' => $template1Id,
                'field_name' => 'purpose',
                'field_label' => '1.0 Purpose of this procedure',
                'field_type' => 'textarea',
                'display_order' => 10,
                'section' => 'Document Content',
                'is_required' => 1,
                'help_text' => 'Describe the purpose and objectives of this procedure',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'scope',
                'field_label' => '2.0 Scope of this procedure',
                'field_type' => 'textarea',
                'display_order' => 11,
                'section' => 'Document Content',
                'is_required' => 1,
                'help_text' => 'Define the scope and applicability',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'authority',
                'field_label' => '3.0 Authority and responsibility',
                'field_type' => 'textarea',
                'display_order' => 12,
                'section' => 'Document Content',
                'is_required' => 1,
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'abbreviations',
                'field_label' => '4.0 Abbreviations used',
                'field_type' => 'textarea',
                'display_order' => 13,
                'section' => 'Document Content',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'pre_assessment',
                'field_label' => '5.0 Pre-assessment',
                'field_type' => 'textarea',
                'display_order' => 14,
                'section' => 'Document Content',
            ],
            [
                'template_id' => $template1Id,
                'field_name' => 'procedure',
                'field_label' => '6.0 Procedure',
                'field_type' => 'textarea',
                'display_order' => 15,
                'section' => 'Document Content',
                'is_required' => 1,
                'help_text' => 'Detailed procedure steps',
            ],
        ];

        foreach ($fields1 as $field) {
            $field['created_at'] = date('Y-m-d H:i:s');
            $field['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('template_fields')->insert($field);
        }

        // Sample Template 2: Equipment Calibration Form
        $template2Id = $this->db->table('document_templates')->insert([
            'document_type_id' => 2, // Assuming Form type exists
            'name' => 'Equipment Calibration Form',
            'code' => 'CAL_FORM_001',
            'version' => '1.0',
            'description' => 'Form for recording equipment calibration details',
            'layout_template' => 'calibration_template.php',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $template2Id = $this->db->insertID();

        // Fields for Template 2
        $fields2 = [
            [
                'template_id' => $template2Id,
                'field_name' => 'equipment_name',
                'field_label' => 'Equipment Name',
                'field_type' => 'text',
                'display_order' => 1,
                'section' => 'Equipment Information',
                'is_required' => 1,
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'equipment_id',
                'field_label' => 'Equipment ID',
                'field_type' => 'text',
                'display_order' => 2,
                'section' => 'Equipment Information',
                'is_required' => 1,
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'manufacturer',
                'field_label' => 'Manufacturer',
                'field_type' => 'text',
                'display_order' => 3,
                'section' => 'Equipment Information',
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'model_number',
                'field_label' => 'Model Number',
                'field_type' => 'text',
                'display_order' => 4,
                'section' => 'Equipment Information',
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'calibration_date',
                'field_label' => 'Calibration Date',
                'field_type' => 'date',
                'display_order' => 10,
                'section' => 'Calibration Details',
                'is_required' => 1,
                'is_autofill' => 1,
                'autofill_source' => 'system.date',
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'next_calibration_date',
                'field_label' => 'Next Calibration Date',
                'field_type' => 'date',
                'display_order' => 11,
                'section' => 'Calibration Details',
                'is_required' => 1,
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'calibration_standard',
                'field_label' => 'Calibration Standard Used',
                'field_type' => 'text',
                'display_order' => 12,
                'section' => 'Calibration Details',
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'calibration_results',
                'field_label' => 'Calibration Results',
                'field_type' => 'table',
                'display_order' => 13,
                'section' => 'Calibration Details',
                'options' => json_encode([
                    'columns' => [
                        ['name' => 'parameter', 'label' => 'Parameter'],
                        ['name' => 'standard_value', 'label' => 'Standard Value'],
                        ['name' => 'measured_value', 'label' => 'Measured Value'],
                        ['name' => 'deviation', 'label' => 'Deviation'],
                        ['name' => 'status', 'label' => 'Status'],
                    ]
                ]),
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'calibrated_by',
                'field_label' => 'Calibrated By',
                'field_type' => 'text',
                'display_order' => 20,
                'section' => 'Approval',
                'is_required' => 1,
                'is_autofill' => 1,
                'autofill_source' => 'user.name',
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'verified_by',
                'field_label' => 'Verified By',
                'field_type' => 'text',
                'display_order' => 21,
                'section' => 'Approval',
            ],
            [
                'template_id' => $template2Id,
                'field_name' => 'remarks',
                'field_label' => 'Remarks',
                'field_type' => 'textarea',
                'display_order' => 22,
                'section' => 'Approval',
            ],
        ];

        foreach ($fields2 as $field) {
            $field['created_at'] = date('Y-m-d H:i:s');
            $field['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('template_fields')->insert($field);
        }

        // Sample Template 3: Training Record
        $template3Id = $this->db->table('document_templates')->insert([
            'document_type_id' => 3, // Assuming Record type exists
            'name' => 'Training Record',
            'code' => 'TR_001',
            'version' => '1.0',
            'description' => 'Template for recording employee training sessions',
            'layout_template' => 'training_template.php',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $template3Id = $this->db->insertID();

        // Fields for Template 3
        $fields3 = [
            [
                'template_id' => $template3Id,
                'field_name' => 'training_title',
                'field_label' => 'Training Title',
                'field_type' => 'text',
                'display_order' => 1,
                'section' => 'Training Information',
                'is_required' => 1,
            ],
            [
                'template_id' => $template3Id,
                'field_name' => 'training_date',
                'field_label' => 'Training Date',
                'field_type' => 'date',
                'display_order' => 2,
                'section' => 'Training Information',
                'is_required' => 1,
            ],
            [
                'template_id' => $template3Id,
                'field_name' => 'trainer_name',
                'field_label' => 'Trainer Name',
                'field_type' => 'text',
                'display_order' => 3,
                'section' => 'Training Information',
                'is_required' => 1,
            ],
            [
                'template_id' => $template3Id,
                'field_name' => 'training_type',
                'field_label' => 'Training Type',
                'field_type' => 'select',
                'display_order' => 4,
                'section' => 'Training Information',
                'is_required' => 1,
                'options' => json_encode([
                    ['value' => 'onboarding', 'label' => 'Onboarding'],
                    ['value' => 'technical', 'label' => 'Technical'],
                    ['value' => 'safety', 'label' => 'Safety'],
                    ['value' => 'compliance', 'label' => 'Compliance'],
                    ['value' => 'refresher', 'label' => 'Refresher'],
                ]),
            ],
            [
                'template_id' => $template3Id,
                'field_name' => 'training_objectives',
                'field_label' => 'Training Objectives',
                'field_type' => 'textarea',
                'display_order' => 5,
                'section' => 'Training Information',
            ],
            [
                'template_id' => $template3Id,
                'field_name' => 'attendees',
                'field_label' => 'Attendees',
                'field_type' => 'table',
                'display_order' => 10,
                'section' => 'Attendance',
                'options' => json_encode([
                    'columns' => [
                        ['name' => 'employee_name', 'label' => 'Employee Name'],
                        ['name' => 'employee_id', 'label' => 'Employee ID'],
                        ['name' => 'department', 'label' => 'Department'],
                        ['name' => 'signature', 'label' => 'Signature'],
                        ['name' => 'assessment_score', 'label' => 'Assessment Score'],
                    ]
                ]),
            ],
            [
                'template_id' => $template3Id,
                'field_name' => 'training_materials',
                'field_label' => 'Training Materials Used',
                'field_type' => 'textarea',
                'display_order' => 11,
                'section' => 'Training Details',
            ],
            [
                'template_id' => $template3Id,
                'field_name' => 'assessment_method',
                'field_label' => 'Assessment Method',
                'field_type' => 'radio',
                'display_order' => 12,
                'section' => 'Training Details',
                'options' => json_encode([
                    ['value' => 'written_test', 'label' => 'Written Test'],
                    ['value' => 'practical_demo', 'label' => 'Practical Demonstration'],
                    ['value' => 'observation', 'label' => 'Observation'],
                    ['value' => 'none', 'label' => 'No Assessment'],
                ]),
            ],
        ];

        foreach ($fields3 as $field) {
            $field['created_at'] = date('Y-m-d H:i:s');
            $field['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('template_fields')->insert($field);
        }

        echo "Template seeder completed successfully!\n";
        echo "Created 3 templates with their fields.\n";
    }
}
