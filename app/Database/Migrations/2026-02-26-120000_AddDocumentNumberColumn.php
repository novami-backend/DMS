<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentNumberColumn extends Migration
{
    public function up()
    {
        $this->forge->addColumn('documents', [
            'document_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'title'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('documents', 'document_number');
    }
}
