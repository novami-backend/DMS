<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentTemplatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'document_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'version' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => '1.0',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'layout_template' => [
                'type' => 'LONGTEXT',
                'constraint' => '',
                'null' => true,
                'comment' => 'PDF template file name',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('document_type_id', 'document_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('document_templates');
    }

    public function down()
    {
        $this->forge->dropTable('document_templates');
    }
}
