<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRevisionCountToDocuments extends Migration
{
    public function up()
    {
        $fields = [
            'revision_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
                'after'      => 'revision_comments', // place after existing column
            ],
        ];

        $this->forge->addColumn('documents', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('documents', 'revision_count');
    }
}
