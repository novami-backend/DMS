<?php

namespace App\Models;

use CodeIgniter\Model;

class Document extends Model
{
    protected $table = 'documents';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title',
        'content',
        'type_id',
        'document_number',
        'department_id',
        'status',
        'approval_status',
        'reviewer_id',
        'reviewer_comments',
        'reviewed_at',
        'approver_id',
        'approver_comments',
        'approved_at',
        'rejection_reason',
        'rejected_at',
        'returned_for_revision_at',
        'revision_comments',
        'revision_count',
        'submitted_for_review_at',
        'submitted_for_approval_at',
        'effective_date',
        'review_date',
        'created_by'
    ];

    protected $returnType = 'array';

    public function getDocumentsWithDetails()
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, users.username as created_by_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->orderBy('documents.created_at', 'DESC')
            ->findAll();
    }

    public function getDocumentById($id)
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, 
                            users.name as created_by_name, reviewer.name as reviewer_name, 
                            approver.name as approver_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->join('users as reviewer', 'reviewer.id = documents.reviewer_id', 'left')
            ->join('users as approver', 'approver.id = documents.approver_id', 'left')
            ->where('documents.id', $id)
            ->first();
    }

    public function getDocumentsByStatus($status)
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, users.username as created_by_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->where('documents.status', $status)
            ->orderBy('documents.created_at', 'DESC')
            ->findAll();
    }

    public function getDocumentsByDepartment($departmentId)
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, users.username as created_by_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->where('documents.department_id', $departmentId)
            ->orderBy('documents.created_at', 'DESC')
            ->findAll();
    }

    public function getDocumentsByType($typeId)
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, users.username as created_by_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->where('documents.type_id', $typeId)
            ->orderBy('documents.created_at', 'DESC')
            ->findAll();
    }

    public function getDocumentMetadata($documentId)
    {
        return $this->db->table('document_metadata')
            ->where('document_id', $documentId)
            ->orderBy('meta_key', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function setDocumentMetadata($documentId, array $metadata)
    {
        // Clear existing metadata for this document
        $this->db->table('document_metadata')
            ->where('document_id', $documentId)
            ->delete();

        // Insert new metadata rows
        foreach ($metadata as $meta) {
            $this->db->table('document_metadata')->insert([
                'document_id' => $documentId,
                'meta_key'    => $meta['key'],
                'meta_value'  => $meta['value']
            ]);
        }
    }

    // Version Control Methods
    public function createVersion($documentId, $changesDescription = '', $userId = null)
    {
        $document = $this->find($documentId);
        if (!$document) {
            return false;
        }

        // Get the latest version number
        $latestVersion = $this->db->table('document_versions')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRow();

        $versionNumber = '1.0';
        if ($latestVersion) {
            $currentVersion = $latestVersion->version_number;
            $versionParts = explode('.', $currentVersion);
            $versionParts[1] = (int)$versionParts[1] + 1;
            $versionNumber = implode('.', $versionParts);
        }

        $versionData = [
            'document_id' => $documentId,
            'version_number' => $versionNumber,
            'title' => $document['title'],
            'content' => $document['content'],
            'changes_description' => $changesDescription,
            'created_by' => $userId ?? session()->get('user_id')
        ];

        return $this->db->table('document_versions')->insert($versionData);
    }

    public function getDocumentHistory($documentId)
    {
        return $this->db->table('document_versions')
            ->select('document_versions.*, users.username as created_by_name')
            ->join('users', 'users.id = document_versions.created_by', 'left')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function restoreVersion($documentId, $versionId)
    {
        $version = $this->db->table('document_versions')
            ->where('id', $versionId)
            ->where('document_id', $documentId)
            ->get()
            ->getRow();

        if (!$version) {
            return false;
        }

        // Create a new version before restoring
        $this->createVersion($documentId, 'Restored from version ' . $version->version_number);

        // Update the document with version data
        return $this->update($documentId, [
            'title' => $version->title,
            'content' => $version->content
        ]);
    }

    // Document Sharing Methods
    public function shareDocument($documentId, $shareData)
    {
        $shareData['document_id'] = $documentId;
        $shareData['created_by'] = session()->get('user_id');
        $shareData['created_at'] = date('Y-m-d H:i:s');
            
        return $this->db->table('document_shares')->insert($shareData);
    }

    // public function getDocumentShares($documentId)
    // {
    //     return $this->db->table('document_shares')
    //         ->select('document_shares.*, users.username as shared_with_user_name, roles.role_name as shared_with_role_name, departments.name as shared_with_department_name')
    //         ->join('users', 'users.id = document_shares.shared_with_user_id', 'left')
    //         ->join('roles', 'roles.id = document_shares.shared_with_role_id', 'left')
    //         ->join('departments', 'departments.id = document_shares.shared_with_department_id', 'left')
    //         ->where('document_id', $documentId)
    //         ->get()
    //         ->getResultArray();
    // }

    public function getDocumentShares($documentId)
    {
        return $this->db->table('document_shares ds')
            ->select('ds.*, 
                  u.username as shared_with_user_name, 
                  r.role_name as shared_with_role_name, 
                  dpt.name as shared_with_department_name,
                  cb.name as created_by_name')
            ->join('users u', 'u.id = ds.shared_with_user_id', 'left')
            ->join('roles r', 'r.id = ds.shared_with_role_id', 'left')
            ->join('departments dpt', 'dpt.id = ds.shared_with_department_id', 'left')
            ->join('users cb', 'cb.id = ds.created_by', 'left') // join for "Shared by"
            ->where('ds.document_id', $documentId)
            ->get()
            ->getResultArray();
    }

    // Search Methods
    public function searchDocuments($searchTerm, $filters = [])
    {
        $builder = $this->select('documents.*, document_types.name as type_name, departments.name as department_name, users.username as created_by_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left');

        // Add search term condition
        if (!empty($searchTerm)) {
            $builder->groupStart()
                ->like('documents.title', $searchTerm)
                ->orLike('documents.content', $searchTerm)
                ->groupEnd();
        }

        // Add filters
        if (!empty($filters['type_id'])) {
            $builder->where('documents.type_id', $filters['type_id']);
        }

        if (!empty($filters['department_id'])) {
            $builder->where('documents.department_id', $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('documents.status', $filters['status']);
        }

        if (!empty($filters['created_by'])) {
            $builder->where('documents.created_by', $filters['created_by']);
        }

        return $builder->orderBy('documents.created_at', 'DESC')->findAll();
    }

    // Workflow Methods
    public function createWorkflow($documentId, $workflowData)
    {
        $workflowData['document_id'] = $documentId;
        return $this->db->table('document_workflows')->insert($workflowData);
    }

    public function getDocumentWorkflows($documentId)
    {
        return $this->db->table('document_workflows')
            ->select('document_workflows.*, users.username as assigned_to_name')
            ->join('users', 'users.id = document_workflows.assigned_to', 'left')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function updateWorkflowStatus($workflowId, $status, $comments = '')
    {
        $updateData = [
            'current_status' => $status,
            'comments' => $comments,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'completed') {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->table('document_workflows')
            ->where('id', $workflowId)
            ->update($updateData);
    }

    // Backup Methods
    public function createBackup($documentId, $backupPath, $backupSize = null)
    {
        $backupData = [
            'document_id' => $documentId,
            'backup_path' => $backupPath,
            'backup_size' => $backupSize,
            'backup_date' => date('Y-m-d H:i:s')
        ];

        return $this->db->table('document_backups')->insert($backupData);
    }

    public function getDocumentBackups($documentId)
    {
        return $this->db->table('document_backups')
            ->where('document_id', $documentId)
            ->orderBy('backup_date', 'DESC')
            ->get()
            ->getResultArray();
    }

    // Simple Approval System Methods
    public function submitForReview($documentId, $reviewerId, $userId)
    {
        $updateData = [
            'approval_status' => 'sent_for_review',
            'reviewer_id' => $reviewerId,
            'submitted_for_review_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->update($documentId, $updateData);

        if ($result) {
            $this->logApprovalAction($documentId, 'submitted_for_review', $userId, 'Document submitted for review');
        }

        return $result;
    }

    public function reviewDocument($documentId, $action, $comments, $userId)
    {
        $updateData = [
            'reviewer_comments' => $comments,
            'reviewed_at' => date('Y-m-d H:i:s')
        ];

        if ($action === 'approve_for_final') {
            $updateData['approval_status'] = 'reviewed';
        } elseif ($action === 'reject') {
            $updateData['approval_status'] = 'rejected';
            $updateData['rejection_reason'] = $comments;
            $updateData['rejected_at'] = date('Y-m-d H:i:s');
        } elseif ($action === 'return_for_revision') {
            $updateData['approval_status'] = 'returned_for_revision';
            $updateData['revision_comments'] = $comments;
            $updateData['returned_for_revision_at'] = date('Y-m-d H:i:s');
            // Increment revision count
            $document = $this->find($documentId);
            $updateData['revision_count'] = ($document['revision_count'] ?? 0) + 1;
        }

        $result = $this->update($documentId, $updateData);

        if ($result) {
            $this->logApprovalAction($documentId, $action, $userId, $comments);
        }

        return $result;
    }

    public function submitForApproval($documentId, $approverId, $userId)
    {
        $updateData = [
            'approval_status' => 'approved', // Admin has final approval authority
            'approver_id' => $approverId,
            'approved_at' => date('Y-m-d H:i:s'),
            'submitted_for_approval_at' => date('Y-m-d H:i:s'),
            'status' => 'active' // Activate the document
        ];

        $result = $this->update($documentId, $updateData);

        if ($result) {
            $this->logApprovalAction($documentId, 'submitted_for_approval', $userId, 'Document submitted for final approval');
        }

        return $result;
    }

    public function approveDocument($documentId, $comments, $userId)
    {
        $updateData = [
            'approval_status' => 'approved_by_approver',
            'approver_id' => $userId,
            'approver_comments' => $comments,
            'approved_at' => date('Y-m-d H:i:s'),
            'status' => 'active' // Activate the document
        ];

        $result = $this->update($documentId, $updateData);

        if ($result) {
            $this->logApprovalAction($documentId, 'approved', $userId, $comments);
        }

        return $result;
    }

    public function rejectDocument($documentId, $reason, $userId)
    {
        $updateData = [
            'approval_status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->update($documentId, $updateData);

        if ($result) {
            $this->logApprovalAction($documentId, 'rejected', $userId, $reason);
        }

        return $result;
    }

    private function getNewStatusForAction($action)
    {
        $statusMap = [
            'submitted_for_review' => 'sent_for_review',
            'reviewed' => 'reviewed',
            'submitted_for_approval' => 'approved',
            'approved' => 'approved',
            'rejected' => 'rejected',
            'return_for_revision' => 'returned_for_revision',
            'resubmitted_after_revision' => 'pending'
        ];

        return $statusMap[$action] ?? 'pending';
    }

    // Public method to log approval actions
    public function logApprovalAction($documentId, $action, $userId, $comments = '')
    {
        $document = $this->find($documentId);
        
        $logData = [
            'document_id' => $documentId,
            'action' => $action,
            'performed_by' => $userId,
            'comments' => $comments,
            'previous_status' => $document['approval_status'] ?? 'pending',
            'new_status' => $this->getNewStatusForAction($action),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->table('document_approval_history')->insert($logData);
    }

    public function getApprovalHistory($documentId)
    {
        return $this->db->table('document_approval_history')
            ->select('document_approval_history.*, users.name as performed_by_name')
            ->join('users', 'users.id = document_approval_history.performed_by', 'left')
            ->where('document_id', $documentId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getDocumentsForReview($userId)
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, users.username as created_by_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->where('documents.reviewer_id', $userId)
            ->where('documents.approval_status', 'sent_for_review')
            ->orderBy('documents.submitted_for_review_at', 'ASC')
            ->findAll();
    }

    public function getDocumentsForApproval($userId)
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, users.username as created_by_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->where('documents.approver_id', $userId)
            ->where('documents.approval_status', 'reviewed')
            ->orderBy('documents.submitted_for_approval_at', 'ASC')
            ->findAll();
    }

    public function getPendingDocuments()
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, users.username as created_by_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->where('documents.approval_status', 'pending')
            ->orderBy('documents.created_at', 'DESC')
            ->findAll();
    }

    public function getDocumentsByApprovalStatus($status)
    {
        return $this->select('documents.*, document_types.name as type_name, departments.name as department_name, 
                            users.name as created_by_name, reviewer.name as reviewer_name, 
                            approver.name as approver_name')
            ->join('document_types', 'document_types.id = documents.type_id', 'left')
            ->join('departments', 'departments.id = documents.department_id', 'left')
            ->join('users', 'users.id = documents.created_by', 'left')
            ->join('users as reviewer', 'reviewer.id = documents.reviewer_id', 'left')
            ->join('users as approver', 'approver.id = documents.approver_id', 'left')
            ->where('documents.approval_status', $status)
            ->orderBy('documents.created_at', 'DESC')
            ->findAll();
    }

    public function getReviewersByDepartment($departmentId)
    {
        return $this->db->table('users')
            ->select('users.id, users.username')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.department_id', $departmentId)
            ->where('roles.role_name', 'Reviewer')
            ->where('users.status', 'active')
            ->get()
            ->getResultArray();
    }

    public function getApproversByDepartment($departmentId)
    {
        return $this->db->table('users')
            ->select('users.id, users.username')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.department_id', $departmentId)
            ->whereIn('roles.role_name', ['Approver', 'Admin', 'Super Admin'])
            ->where('users.status', 'active')
            ->get()
            ->getResultArray();
    }
}
