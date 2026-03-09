<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentVersion extends Model
{
    protected $table            = 'document_versions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'document_id',
        'version_number',
        'title',
        'content',
        'file_path',
        'file_hash',
        'changes_description',
        'created_by'
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
        'version_number' => 'required|max_length[20]',
        'title' => 'required|max_length[255]',
        'created_by' => 'required|is_natural_no_zero'
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

    public function getVersionById($id)
    {
        return $this->select('document_versions.*, users.name as created_by_name, documents.title as document_title')
            ->join('users', 'users.id = document_versions.created_by', 'left')
            ->join('documents', 'documents.id = document_versions.document_id', 'left')
            ->where('document_versions.id', $id)
            ->first();
    }

    public function getVersionsByDocument($documentId)
    {
        return $this->select('document_versions.*, users.name as created_by_name')
            ->join('users', 'users.id = document_versions.created_by', 'left')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
