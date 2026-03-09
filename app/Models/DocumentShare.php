<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentShare extends Model
{
    protected $table            = 'document_shares';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'document_id',
        'shared_with_user_id',
        'shared_with_role_id',
        'shared_with_department_id',
        'permission_level',
        'expiration_date',
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
        'permission_level' => 'required|in_list[view,edit,full]',
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

    public function getShareById($id)
    {
        return $this->select('document_shares.*, users.name as shared_with_user_name, roles.role_name as shared_with_role_name, departments.name as shared_with_department_name, creator.name as created_by_name')
            ->join('users as shared_with', 'shared_with.id = document_shares.shared_with_user_id', 'left')
            ->join('roles', 'roles.id = document_shares.shared_with_role_id', 'left')
            ->join('departments', 'departments.id = document_shares.shared_with_department_id', 'left')
            ->join('users as creator', 'creator.id = document_shares.created_by', 'left')
            ->where('document_shares.id', $id)
            ->first();
    }

    public function getSharesByDocument($documentId)
    {
        return $this->select('document_shares.*, users.name as shared_with_user_name, roles.role_name as shared_with_role_name, departments.name as shared_with_department_name, creator.name as created_by_name')
            ->join('users as shared_with', 'shared_with.id = document_shares.shared_with_user_id', 'left')
            ->join('roles', 'roles.id = document_shares.shared_with_role_id', 'left')
            ->join('departments', 'departments.id = document_shares.shared_with_department_id', 'left')
            ->join('users as creator', 'creator.id = document_shares.created_by', 'left')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getSharesForUser($userId)
    {
        return $this->select('document_shares.*, documents.title as document_title, users.name as shared_by_name')
            ->join('documents', 'documents.id = document_shares.document_id', 'left')
            ->join('users', 'users.id = document_shares.created_by', 'left')
            ->where('shared_with_user_id', $userId)
            ->where('(expiration_date IS NULL OR expiration_date > NOW())')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function checkUserAccess($documentId, $userId)
    {
        return $this->select('permission_level')
            ->where('document_id', $documentId)
            ->where('shared_with_user_id', $userId)
            ->where('(expiration_date IS NULL OR expiration_date > NOW())')
            ->first();
    }
}
