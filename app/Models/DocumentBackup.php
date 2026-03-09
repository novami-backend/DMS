<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentBackup extends Model
{
    protected $table            = 'document_backups';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'document_id',
        'backup_path',
        'backup_size',
        'backup_date',
        'retention_policy'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'document_id' => 'required|is_natural_no_zero',
        'backup_path' => 'required|max_length[500]',
        'backup_date' => 'required|valid_date'
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

    public function getBackupById($id)
    {
        return $this->select('document_backups.*, documents.title as document_title')
            ->join('documents', 'documents.id = document_backups.document_id', 'left')
            ->where('document_backups.id', $id)
            ->first();
    }

    public function getBackupsByDocument($documentId)
    {
        return $this->select('document_backups.*, documents.title as document_title')
            ->join('documents', 'documents.id = document_backups.document_id', 'left')
            ->where('document_id', $documentId)
            ->orderBy('backup_date', 'DESC')
            ->findAll();
    }

    public function getRecentBackups($limit = 10)
    {
        return $this->select('document_backups.*, documents.title as document_title')
            ->join('documents', 'documents.id = document_backups.document_id', 'left')
            ->orderBy('backup_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function createBackupRecord($documentId, $backupPath, $backupSize = null, $retentionPolicy = null)
    {
        $backupData = [
            'document_id' => $documentId,
            'backup_path' => $backupPath,
            'backup_size' => $backupSize,
            'backup_date' => date('Y-m-d H:i:s'),
            'retention_policy' => $retentionPolicy
        ];
        
        return $this->insert($backupData);
    }

    public function deleteExpiredBackups($retentionDays = 3)
    {
        $expiryDate = date('Y-m-d H:i:s', strtotime("-$retentionDays days"));
        return $this->where('backup_date <', $expiryDate)
            ->delete();
    }
}
