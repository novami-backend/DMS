<?php

namespace App\Controllers;

use App\Models\Document;
use App\Models\DocumentSearchIndex;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\UserActivityLog;

class DocumentSearch extends BaseController
{
    protected $documentModel;
    protected $searchIndexModel;
    protected $documentTypeModel;
    protected $departmentModel;
    protected $logModel;

    public function __construct()
    {
        $this->documentModel = new Document();
        $this->searchIndexModel = new DocumentSearchIndex();
        $this->documentTypeModel = new DocumentType();
        $this->departmentModel = new Department();
        $this->logModel = new UserActivityLog();
        helper('permission');
    }

    public function index()
    {
        if ($resp = requirePermission('document_read', '/documents')) {
            return $resp;
        }

        $searchTerm = $this->request->getGet('q');
        $filters = [
            'type_id' => $this->request->getGet('type_id'),
            'department_id' => $this->request->getGet('department_id'),
            'created_by' => $this->request->getGet('created_by')
        ];

        $results = [];
        if (!empty($searchTerm)) {
            $results = $this->searchIndexModel->search($searchTerm, $filters);
            $this->logModel->logActivity(session()->get('user_id'), 'Searched documents', 'Search term: ' . $searchTerm);
        }

        $data = [
            'searchTerm' => $searchTerm,
            'filters' => $filters,
            'results' => $results,
            'documentTypes' => $this->documentTypeModel->findAll(),
            'departments' => $this->departmentModel->findAll(),
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        return view('documents/search_advanced', $data);
    }

    public function apiSearch()
    {
        $searchTerm = $this->request->getGet('q');
        $filters = [
            'type_id' => $this->request->getGet('type_id'),
            'department_id' => $this->request->getGet('department_id'),
            'limit' => $this->request->getGet('limit', 20)
        ];

        if (empty($searchTerm)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Search term is required'
            ])->setStatusCode(400);
        }

        $results = $this->searchIndexModel->search($searchTerm, $filters);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $results,
            'count' => count($results)
        ]);
    }

    public function indexDocument($documentId)
    {
        if ($resp = requirePermission('document_update', '/documents')) {
            return $resp;
        }

        $document = $this->documentModel->find($documentId);
        if (!$document) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Document not found'
            ])->setStatusCode(404);
        }

        $documentData = [
            'title' => $document['title'],
            'content' => $document['content'],
            'tags' => $this->request->getPost('tags') ?? '',
            'keywords' => $this->request->getPost('keywords') ?? ''
        ];

        if ($this->searchIndexModel->indexDocument($documentId, $documentData)) {
            $this->logModel->logActivity(session()->get('user_id'), 'Indexed document', 'Indexed document: ' . $document['title']);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Document indexed successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to index document'
        ])->setStatusCode(500);
    }

    public function removeIndex($documentId)
    {
        if ($resp = requirePermission('document_update', '/documents')) {
            return $resp;
        }

        if ($this->searchIndexModel->removeDocumentIndex($documentId)) {
            $this->logModel->logActivity(session()->get('user_id'), 'Removed document index', 'Removed index for document ID: ' . $documentId);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Document index removed successfully'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to remove document index'
        ])->setStatusCode(500);
    }
}
