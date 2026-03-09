<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentSearchIndex extends Model
{
    protected $table            = 'document_search_index';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'document_id',
        'search_terms',
        'indexed_content',
        'tags',
        'keywords'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'document_id' => 'required|is_natural_no_zero',
        'search_terms' => 'permit_empty',
        'indexed_content' => 'permit_empty'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function indexDocument($documentId, $documentData)
    {
        // Extract searchable content
        $searchTerms = $documentData['title'] ?? '';
        $indexedContent = $documentData['content'] ?? '';
        $tags = $documentData['tags'] ?? '';
        $keywords = $documentData['keywords'] ?? '';
        
        // Check if document is already indexed
        $existingIndex = $this->where('document_id', $documentId)->first();
        
        $indexData = [
            'document_id' => $documentId,
            'search_terms' => $searchTerms,
            'indexed_content' => $indexedContent,
            'tags' => $tags,
            'keywords' => $keywords,
            'indexed_at' => date('Y-m-d H:i:s')
        ];
        
        if ($existingIndex) {
            // Update existing index
            return $this->update($existingIndex['id'], $indexData);
        } else {
            // Create new index
            return $this->insert($indexData);
        }
    }

    public function search($searchTerm, $filters = [])
    {
        $builder = $this->select('document_search_index.*, documents.title as document_title, document_types.name as type_name, departments.name as department_name')
            ->join('documents', 'documents.id = document_search_index.document_id', 'left')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->where('documents.status', 'active');
        
        // Add search condition using fulltext search
        if (!empty($searchTerm)) {
            $builder->where("MATCH(search_terms, indexed_content, tags, keywords) AGAINST(? IN NATURAL LANGUAGE MODE)", $searchTerm);
        }
        
        // Add filters
        if (!empty($filters['type_id'])) {
            $builder->where('documents.type_id', $filters['type_id']);
        }
        
        if (!empty($filters['department_id'])) {
            $builder->where('documents.department_id', $filters['department_id']);
        }
        
        if (!empty($filters['created_by'])) {
            $builder->where('documents.created_by', $filters['created_by']);
        }
        
        return $builder->orderBy('document_search_index.indexed_at', 'DESC')->findAll();
    }

    public function getIndexedDocument($documentId)
    {
        return $this->select('document_search_index.*, documents.title as document_title')
            ->join('documents', 'documents.id = document_search_index.document_id', 'left')
            ->where('document_id', $documentId)
            ->first();
    }

    public function removeDocumentIndex($documentId)
    {
        return $this->where('document_id', $documentId)->delete();
    }
}
