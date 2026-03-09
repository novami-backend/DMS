<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceNotificationsTable extends Migration
{
    public function up()
    {
        $fields = [
            'frequency' => [
                'type'       => 'ENUM',
                'constraint' => ['once', 'daily', 'weekly', 'monthly', 'yearly', 'custom'],
                'default'    => 'once',
                'after'      => 'message'
            ],
            'interval_minutes' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'frequency'
            ],
            'priority' => [
                'type'       => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'urgent'],
                'default'    => 'medium',
                'after'      => 'frequency'
            ],
            'link_url' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'priority'
            ],
            'next_run_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'link_url'
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'after'      => 'next_run_at'
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'category'
            ]
        ];
        $this->forge->addColumn('notifications', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('notifications', ['frequency', 'priority', 'link_url', 'next_run_at', 'category', 'expires_at']);
    }
}
