<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentSharesTable extends Migration
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
            'shared_with_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'shared_with_role_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'shared_with_department_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'permission_level' => [
                'type' => 'ENUM',
                'constraint' => ['view', 'edit', 'full'],
                'default' => 'view',
            ],
            'expiration_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('document_id');
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('shared_with_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('shared_with_role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('shared_with_department_id', 'departments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('document_shares');
    }

    public function down()
    {
        $this->forge->dropTable('document_shares');
    }
}
