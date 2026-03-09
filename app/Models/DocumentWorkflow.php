<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentWorkflow extends Model
{
    protected $table            = 'document_workflows';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'document_id',
        'workflow_type',
        'current_status',
        'assigned_to',
        'due_date',
        'comments'
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
        'workflow_type' => 'required|in_list[review,approval,publish]',
        'current_status' => 'required|in_list[pending,in_progress,completed,rejected]',
        'assigned_to' => 'permit_empty|is_natural_no_zero'
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

    public function getWorkflowById($id)
    {
        return $this->select('document_workflows.*, documents.title as document_title, users.name as assigned_to_name, creator.name as created_by_name')
            ->join('documents', 'documents.id = document_workflows.document_id', 'left')
            ->join('users as assigned', 'assigned.id = document_workflows.assigned_to', 'left')
            ->join('users as creator', 'creator.id = documents.created_by', 'left')
            ->where('document_workflows.id', $id)
            ->first();
    }

    public function getWorkflowsByDocument($documentId)
    {
        return $this->select('document_workflows.*, users.name as assigned_to_name')
            ->join('users', 'users.id = document_workflows.assigned_to', 'left')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getPendingWorkflows($userId = null)
    {
        $builder = $this->select('document_workflows.*, documents.title as document_title, document_types.name as type_name')
            ->join('documents', 'documents.id = document_workflows.document_id', 'left')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->where('current_status', 'pending')
            ->orWhere('current_status', 'in_progress')
            ->orderBy('due_date', 'ASC');
        
        if ($userId) {
            $builder->where('assigned_to', $userId);
        }
        
        return $builder->findAll();
    }

    public function updateStatus($workflowId, $status, $comments = '')
    {
        $updateData = [
            'current_status' => $status,
            'comments' => $comments,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($status === 'completed') {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->update($workflowId, $updateData);
    }

    public function assignWorkflow($workflowId, $userId)
    {
        return $this->update($workflowId, [
            'assigned_to' => $userId,
            'current_status' => 'in_progress',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
