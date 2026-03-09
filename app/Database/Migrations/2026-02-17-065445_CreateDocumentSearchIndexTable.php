<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentSearchIndexTable extends Migration
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
            'search_terms' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indexed_content' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'tags' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'keywords' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'indexed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('document_id');
        $this->forge->addForeignKey('document_id', 'documents', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('document_search_index');
        
        // Add fulltext index
        $this->db->query('ALTER TABLE document_search_index ADD FULLTEXT(search_terms, indexed_content, tags, keywords)');
    }

    public function down()
    {
        $this->forge->dropTable('document_search_index');
    }
}
