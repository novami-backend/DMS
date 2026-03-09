<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTemplateFieldsTable extends Migration
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
            'template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'field_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'field_label' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'field_type' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'textarea', 'number', 'date', 'select', 'checkbox', 'radio', 'file', 'table', 'signature', 'email', 'tel'],
                'default' => 'text',
            ],
            'field_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'section' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'General',
            ],
            'is_required' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_autofill' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'autofill_source' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'e.g., user.name, department.name, system.date',
            ],
            'validation_rules' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON validation rules',
            ],
            'options' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON options for select/radio/checkbox',
            ],
            'default_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'help_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'placeholder' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
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
        $this->forge->addForeignKey('template_id', 'document_templates', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('template_fields');
    }

    public function down()
    {
        $this->forge->dropTable('template_fields');
    }
}
