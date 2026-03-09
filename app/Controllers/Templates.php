<?php

namespace App\Controllers;

use App\Models\DocumentTemplate;
use App\Models\NotificationModel;
use App\Models\TemplateField;
use App\Models\DocumentType;

class Templates extends BaseController
{
    protected $templateModel;
    protected $notificationModel;
    protected $fieldModel;
    protected $typeModel;
    protected $db;

    public function __construct()
    {
        $this->templateModel = new DocumentTemplate();
        $this->notificationModel = new NotificationModel();
        $this->fieldModel = new TemplateField();
        $this->typeModel = new DocumentType();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'templates' => $this->templateModel->getTemplatesWithType(),
            'pageTitle' => 'Document Templates',
            'pageDescription' => 'Manage document form templates'
        ];

        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;

        return view('templates/index', $data);
    }

    public function create()
    {
        $data = [
            'documentTypes' => $this->typeModel->findAll(),
            'pageTitle' => 'Create Template',
            'pageDescription' => 'Create a new document template'
        ];

        return view('templates/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'document_type_id' => 'required|integer',
            'name' => 'required|min_length[3]',
            'code' => 'required|is_unique[document_templates.code]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $templateData = [
            'document_type_id' => $this->request->getPost('document_type_id'),
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'version' => $this->request->getPost('version'),
            'description' => $this->request->getPost('description'),
            'layout_template' => $this->request->getPost('layout_template'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 1,
        ];

        $templateId = $this->templateModel->insert($templateData);

        if ($templateId) {
            return redirect()->to('/templates/edit/' . $templateId)->with('success', 'Template created successfully. Now add fields.');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create template');
    }

    public function view($id)
    {
        $template = $this->templateModel->find($id);

        if (!$template) {
            return redirect()->to('/templates')->with('error', 'Template not found');
        }

        $data = [
            'template' => $template,
            'fields' => $this->fieldModel->getFieldsBySection($id),
            'documentTypes' => $this->typeModel->findAll(),
            'pageTitle' => 'View Template',
            'pageDescription' => 'View template: ' . $template['name']
        ];

        return view('templates/view', $data);
    }

    public function edit($id)
    {
        $template = $this->templateModel->find($id);

        if (!$template) {
            return redirect()->to('/templates')->with('error', 'Template not found');
        }

        $data = [
            'template' => $template,
            'fields' => $this->fieldModel->getFieldsBySection($id),
            'documentTypes' => $this->typeModel->findAll(),
            'pageTitle' => 'Edit Template',
            'pageDescription' => 'Edit template: ' . $template['name']
        ];

        return view('templates/edit', $data);
    }

    public function update($id)
    {
        $template = $this->templateModel->find($id);

        if (!$template) {
            return redirect()->to('/templates')->with('error', 'Template not found');
        }

        $templateData = [
            'document_type_id' => $this->request->getPost('document_type_id'),
            'name' => $this->request->getPost('name'),
            'code' => $this->request->getPost('code'),
            'description' => $this->request->getPost('description'),
            'layout_template' => $this->request->getPost('layout_template'),
        ];

        // Disable validation temporarily to avoid unique constraint issues on update
        $this->templateModel->setValidationRules([]);
        
        if ($this->templateModel->update($id, $templateData)) {
            return redirect()->to('/templates')->with('success', 'Template updated successfully');
        }

        // Get any errors
        $errors = $this->templateModel->errors();
        if (!empty($errors)) {
            return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $errors));
        }

        return redirect()->to('/templates')->with('error', 'Failed to update template');
    }

    public function delete($id)
    {
        if ($this->templateModel->delete($id)) {
            return redirect()->to('/templates')->with('success', 'Template deleted successfully');
        }

        return redirect()->to('/templates')->with('error', 'Failed to delete template');
    }

    // Field Management
    public function addField($templateId)
    {
        $fieldData = [
            'template_id' => $templateId,
            'field_name' => $this->request->getPost('field_name'),
            'field_label' => $this->request->getPost('field_label'),
            'field_type' => $this->request->getPost('field_type'),
            'display_order' => $this->request->getPost('display_order') ?? 0,
            'section' => $this->request->getPost('section') ?? 'General',
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'is_autofill' => $this->request->getPost('is_autofill') ? 1 : 0,
            'autofill_source' => $this->request->getPost('autofill_source'),
            'options' => $this->request->getPost('options'),
            'default_value' => $this->request->getPost('default_value'),
            'help_text' => $this->request->getPost('help_text'),
            'placeholder' => $this->request->getPost('placeholder'),
        ];

        if ($this->fieldModel->insert($fieldData)) {
            return redirect()->back()->with('success', 'Field added successfully');
        }

        return redirect()->back()->with('error', 'Failed to add field');
    }

    public function updateField($fieldId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/templates')->with('error', 'Invalid request');
        }

        $field = $this->fieldModel->find($fieldId);
        if (!$field) {
            return $this->response->setJSON(['success' => false, 'message' => 'Field not found']);
        }

        $fieldData = [
            'field_name' => $this->request->getPost('field_name'),
            'field_label' => $this->request->getPost('field_label'),
            'field_type' => $this->request->getPost('field_type'),
            'display_order' => $this->request->getPost('display_order') ?? 0,
            'section' => $this->request->getPost('section') ?? 'General',
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'is_autofill' => $this->request->getPost('is_autofill') ? 1 : 0,
            'autofill_source' => $this->request->getPost('autofill_source'),
            'options' => $this->request->getPost('options'),
            'default_value' => $this->request->getPost('default_value'),
            'help_text' => $this->request->getPost('help_text'),
            'placeholder' => $this->request->getPost('placeholder'),
        ];

        if ($this->fieldModel->update($fieldId, $fieldData)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Field updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update field']);
    }

    public function deleteField($fieldId)
    {
        if ($this->fieldModel->delete($fieldId)) {
            return redirect()->back()->with('success', 'Field deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete field');
    }
}
