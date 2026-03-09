<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentWorkflowsTable extends Migration
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
            'document_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'workflow_type' => [
                'type' => 'ENUM',
                'constraint' => ['review', 'approval', 'publish'],
            ],
            'current_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'rejected'],
                'default' => 'pending',
            ],
            'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'due_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'comments' => [
                'type' => 'TEXT',
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
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('document_id');
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_to', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('document_workflows');
    }

    public function down()
    {
        $this->forge->dropTable('document_workflows');
    }
}
