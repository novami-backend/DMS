<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentAttachment extends Model
{
    protected $table = 'document_attachments';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['document_id', 'file_name', 'file_path', 'file_size', 'file_type', 'uploaded_by'];

    /**
     * Get all attachments for a specific document
     */
    public function getDocumentAttachments($documentId)
    {
        return $this->where('document_id', $documentId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Add a new attachment
     */
    public function addAttachment($documentId, $fileName, $filePath, $fileSize, $fileType, $uploadedBy)
    {
        return $this->insert([
            'document_id' => $documentId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'uploaded_by' => $uploadedBy
        ]);
    }

    /**
     * Delete attachment by ID
     */
    public function deleteAttachment($attachmentId)
    {
        return $this->delete($attachmentId);
    }

    /**
     * Get attachment with uploader details
     */
    public function getAttachmentWithUploader($attachmentId)
    {
        return $this->select('document_attachments.*, users.username, users.name')
            ->join('users', 'users.id = document_attachments.uploaded_by', 'left')
            ->where('document_attachments.id', $attachmentId)
            ->first();
    }

    /**
     * Get all attachments for a document with uploader details
     */
    public function getDocumentAttachmentsWithUploaders($documentId)
    {
        return $this->select('document_attachments.*, users.username, users.name')
            ->join('users', 'users.id = document_attachments.uploaded_by', 'left')
            ->where('document_attachments.document_id', $documentId)
            ->orderBy('document_attachments.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Delete all attachments for a document
     */
    public function deleteDocumentAttachments($documentId)
    {
        return $this->where('document_id', $documentId)->delete();
    }

    /**
     * Get formatted file size
     */
    public static function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get file icon based on file type
     */
    public static function getFileIcon($fileType)
    {
        $icons = [
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'xls' => 'fas fa-file-excel',
            'xlsx' => 'fas fa-file-excel',
            'jpg' => 'fas fa-file-image',
            'jpeg' => 'fas fa-file-image',
            'png' => 'fas fa-file-image',
            'gif' => 'fas fa-file-image',
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
            'txt' => 'fas fa-file-alt',
            'csv' => 'fas fa-file-csv'
        ];

        return $icons[strtolower($fileType)] ?? 'fas fa-file';
    }
}
