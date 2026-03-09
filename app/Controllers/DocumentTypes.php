<?php

namespace App\Controllers;

use App\Models\DocumentType;
use App\Models\UserActivityLog;
use App\Models\NotificationModel;

class DocumentTypes extends BaseController
{
    protected $documentTypeModel;
    protected $notificationModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->documentTypeModel = new DocumentType();
        $this->notificationModel = new NotificationModel();
        $this->logModel = new UserActivityLog();
        $this->db = \Config\Database::connect();
        helper('permission');
    }

    public function index()
    {
        $documentTypes = $this->documentTypeModel->getDocumentTypesWithDocuments();
        $data = [
            'documentTypes' => $documentTypes,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name'),
            'can_create_document_types' => userHasPermission('document_type_create'),
            'can_edit_document_types' => userHasPermission('document_type_update'),
            'can_delete_document_types' => userHasPermission('document_type_delete')
        ];

        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;

        $this->logModel->logActivity(session()->get('user_id'), 'Viewed document types list');
        return view('document_types/index', $data);
    }

    public function create()
    {
        if ($resp = requirePermission('document_type_create', '/document-types')) {
            return $resp;
        }

        $data = [
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];
        return view('document_types/create', $data);
    }

    public function store()
    {
        if ($resp = requirePermission('document_type_create', '/document-types')) {
            return $resp;
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[2]|is_unique[document_types.name]',
            'description' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $documentTypeId = $this->documentTypeModel->insert([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);

        if ($documentTypeId) {
            $this->logModel->logActivity(session()->get('user_id'), 'Created document type', 'Created document type: ' . $this->request->getPost('name'));
            return redirect()->to('/document-types')->with('success', 'Document type created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create document type');
    }

    public function edit($id)
    {
        if ($resp = requirePermission('document_type_update', '/document-types')) {
            return $resp;
        }

        $documentType = $this->documentTypeModel->find($id);

        if (!$documentType) {
            return redirect()->to('/document-types')->with('error', 'Document type not found');
        }

        $data = [
            'documentType' => $documentType,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        return view('document_types/edit', $data);
    }

    public function update($id)
    {
        if ($resp = requirePermission('document_type_update', '/document-types')) {
            return $resp;
        }

        $documentType = $this->documentTypeModel->find($id);
        if (!$documentType) {
            return redirect()->to('/document-types')->with('error', 'Document type not found');
        }

        $validationRules = [
            'name' => 'required|min_length[2]',
            'description' => 'permit_empty'
        ];

        // Check if document type name is being changed
        if ($this->request->getPost('name') !== $documentType['name']) {
            $validationRules['name'] .= '|is_unique[document_types.name]';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($validationRules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $this->documentTypeModel->update($id, [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);

        $this->logModel->logActivity(session()->get('user_id'), 'Updated document type', 'Updated document type: ' . $this->request->getPost('name'));
        return redirect()->to('/document-types')->with('success', 'Document type updated successfully');
    }

    public function delete($id)
    {
        if ($resp = requirePermission('document_type_delete', '/document-types')) {
            return $resp;
        }

        $documentType = $this->documentTypeModel->find($id);
        if (!$documentType) {
            return redirect()->to('/document-types')->with('error', 'Document type not found');
        }

        // Check if document type is assigned to any documents
        $documentCount = $this->db->table('documents')->where('type_id', $id)->countAllResults();
        if ($documentCount > 0) {
            return redirect()->to('/document-types')->with('error', 'Cannot delete document type. It is assigned to ' . $documentCount . ' document(s).');
        }

        $this->documentTypeModel->delete($id);
        $this->logModel->logActivity(session()->get('user_id'), 'Deleted document type', 'Deleted document type: ' . $documentType['name']);
        return redirect()->to('/document-types')->with('success', 'Document type deleted successfully');
    }
}
