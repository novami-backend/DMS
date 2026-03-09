<?php

namespace App\Controllers;

use App\Models\Department;
use App\Models\UserActivityLog;
use App\Models\NotificationModel;

class Departments extends BaseController
{
    protected $departmentModel;
    protected $notificationModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->departmentModel = new Department();
        $this->notificationModel = new NotificationModel();
        $this->logModel = new UserActivityLog();
        $this->db = \Config\Database::connect();
        helper('permission');
    }

    public function index()
    {
        $departments = $this->departmentModel->getDepartmentsWithDocuments();
        $data = [
            'departments' => $departments,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name'),
            'can_create_departments' => userHasPermission('department_create'),
            'can_edit_departments' => userHasPermission('department_update'),
            'can_delete_departments' => userHasPermission('department_delete')
        ];

        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;

        $this->logModel->logActivity(session()->get('user_id'), 'Viewed departments list');
        return view('departments/index', $data);
    }

    public function create()
    {
        if ($resp = requirePermission('department_create', '/departments')) {
            return $resp;
        }

        $data = [
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];
        return view('departments/create', $data);
    }

    // public function store()
    // {
    //     if ($resp = requirePermission('department_create', '/departments')) {
    //         return $resp;
    //     }

    //     $validation = \Config\Services::validation();
    //     $validation->setRules([
    //         'name' => 'required|min_length[2]|is_unique[departments.name]',
    //         'description' => 'permit_empty'
    //     ]);

    //     if (!$validation->withRequest($this->request)->run()) {
    //         return redirect()->back()->withInput()->with('errors', $validation->getErrors());
    //     }

    //     $departmentId = $this->departmentModel->insert([
    //         'name' => $this->request->getPost('name'),
    //         'description' => $this->request->getPost('description')
    //     ]);

    //     if ($departmentId) {
    //         $this->logModel->logActivity(session()->get('user_id'), 'Created department', 'Created department: ' . $this->request->getPost('name'));
    //         return redirect()->to('/departments')->with('success', 'Department created successfully');
    //     }

    //     return redirect()->back()->with('error', 'Failed to create department');
    // }

    public function store()
    {
        if ($resp = requirePermission('department_create', '/departments')) {
            return $resp;
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[2]|is_unique[departments.name]',
            'description' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $validation->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $departmentId = $this->departmentModel->insert([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);

        if ($departmentId) {
            $this->logModel->logActivity(
                session()->get('user_id'),
                'Created department',
                'Created department: ' . $this->request->getPost('name')
            );

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'id' => $departmentId,
                    'name' => $this->request->getPost('name')
                ]);
            }

            return redirect()->to('/departments')->with('success', 'Department created successfully');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'error' => 'Failed to create department']);
        }

        return redirect()->back()->with('error', 'Failed to create department');
    }

    public function edit($id)
    {
        if ($resp = requirePermission('department_update', '/departments')) {
            return $resp;
        }

        $department = $this->departmentModel->find($id);

        if (!$department) {
            return redirect()->to('/departments')->with('error', 'Department not found');
        }

        $data = [
            'department' => $department,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        return view('departments/edit', $data);
    }

    public function update($id)
    {
        if ($resp = requirePermission('department_update', '/departments')) {
            return $resp;
        }

        $department = $this->departmentModel->find($id);
        if (!$department) {
            return redirect()->to('/departments')->with('error', 'Department not found');
        }

        $validationRules = [
            'name' => 'required|min_length[2]',
            'description' => 'permit_empty'
        ];

        // Check if department name is being changed
        if ($this->request->getPost('name') !== $department['name']) {
            $validationRules['name'] .= '|is_unique[departments.name]';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($validationRules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $this->departmentModel->update($id, [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ]);

        $this->logModel->logActivity(session()->get('user_id'), 'Updated department', 'Updated department: ' . $this->request->getPost('name'));
        return redirect()->to('/departments')->with('success', 'Department updated successfully');
    }

    public function delete($id)
    {
        if ($resp = requirePermission('department_delete', '/departments')) {
            return $resp;
        }

        $department = $this->departmentModel->find($id);
        if (!$department) {
            return redirect()->to('/departments')->with('error', 'Department not found');
        }

        // Check if department is assigned to any documents
        $documentCount = $this->db->table('documents')->where('department_id', $id)->countAllResults();
        if ($documentCount > 0) {
            return redirect()->to('/departments')->with('error', 'Cannot delete department. It is assigned to ' . $documentCount . ' document(s).');
        }

        $this->departmentModel->delete($id);
        $this->logModel->logActivity(session()->get('user_id'), 'Deleted department', 'Deleted department: ' . $department['name']);
        return redirect()->to('/departments')->with('success', 'Department deleted successfully');
    }
}
