<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentType extends Model
{
    protected $table = 'document_types';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'description'];

    public function getDocumentTypesWithDocuments()
    {
        return $this->select('document_types.*, COUNT(documents.id) as document_count')
            ->join('documents', 'documents.type_id = document_types.id', 'left')
            ->groupBy('document_types.id')
            ->findAll();
    }
}