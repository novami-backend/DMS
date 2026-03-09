<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFormDataToDocuments extends Migration
{
    public function up()
    {
        $fields = [
            'template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'type_id',
            ],
            'form_data' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'content',
            ],
        ];

        $this->forge->addColumn('documents', $fields);
        
        // Add foreign key
        $this->db->query('ALTER TABLE documents ADD CONSTRAINT fk_documents_template FOREIGN KEY (template_id) REFERENCES document_templates(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE documents DROP FOREIGN KEY fk_documents_template');
        $this->forge->dropColumn('documents', ['template_id', 'form_data']);
    }
}
