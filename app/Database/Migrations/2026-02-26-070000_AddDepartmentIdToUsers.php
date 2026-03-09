<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDepartmentIdToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'status'
            ]
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'SET NULL', 'CASCADE', 'users_department_id_fk');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('users', 'users_department_id_fk');
        
        // Drop column
        $this->forge->dropColumn('users', 'department_id');
    }
}
