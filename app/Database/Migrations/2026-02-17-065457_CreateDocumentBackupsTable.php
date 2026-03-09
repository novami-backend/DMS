<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentBackupsTable extends Migration
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
            'backup_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'backup_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'backup_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'retention_policy' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('document_id');
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('document_backups');
    }

    public function down()
    {
        $this->forge->dropTable('document_backups');
    }
}
