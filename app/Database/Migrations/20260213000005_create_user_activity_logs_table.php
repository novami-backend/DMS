<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserActivityLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'timestamp' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => true,
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_activity_logs');
    }

    public function down()
    {
        $this->forge->dropTable('user_activity_logs');
    }
}