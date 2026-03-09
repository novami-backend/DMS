<?php

namespace App\Controllers;

use App\Models\Permission;
use App\Models\UserActivityLog;
use App\Models\NotificationModel;

class Permissions extends BaseController
{
    protected $permissionModel;
    protected $notificationModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->permissionModel = new Permission();
        $this->notificationModel = new NotificationModel();
        $this->logModel = new UserActivityLog();
        $this->db = \Config\Database::connect();
        helper('permission');
    }

    public function index()
    {
        $permissions = $this->permissionModel->orderBy('permission_key', 'ASC')->findAll();
        $data = [
            'permissions' => $permissions,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name'),
            'can_permission_create' => userHasPermission('permission_create'),
            'can_edit_permissions' => userHasPermission('edit_permissions'),
            'can_delete_permissions' => userHasPermission('delete_permissions')
        ];

        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;

        $this->logModel->logActivity(session()->get('user_id'), 'Viewed permissions list');
        return view('permissions/index', $data);
    }

    public function create()
    {
        if ($resp = requirePermission('permission_create', '/permissions')) {
            return $resp;
        }

        $data = [
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];
        return view('permissions/create', $data);
    }

    public function store()
    {
        if ($resp = requirePermission('permission_create', '/permissions')) {
            return $resp;
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'permission_key' => 'required|min_length[2]|is_unique[permissions.permission_key]',
            'description' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $permissionId = $this->permissionModel->insert([
            'permission_key' => $this->request->getPost('permission_key'),
            'description' => $this->request->getPost('description')
        ]);

        if ($permissionId) {
            $this->logModel->logActivity(session()->get('user_id'), 'Created permission', 'Created permission: ' . $this->request->getPost('permission_key'));
            return redirect()->to('/permissions')->with('success', 'Permission created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create permission');
    }

    public function edit($id)
    {
        if ($resp = requirePermission('permission_update', '/permissions')) {
            return $resp;
        }

        $permission = $this->permissionModel->find($id);

        if (!$permission) {
            return redirect()->to('/permissions')->with('error', 'Permission not found');
        }

        $data = [
            'permission' => $permission,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        return view('permissions/edit', $data);
    }

    public function update($id)
    {
        if ($resp = requirePermission('permission_update', '/permissions')) {
            return $resp;
        }

        $permission = $this->permissionModel->find($id);
        if (!$permission) {
            return redirect()->to('/permissions')->with('error', 'Permission not found');
        }

        $validationRules = [
            'permission_key' => 'required|min_length[2]',
            'description' => 'permit_empty'
        ];

        // Check if permission key is being changed
        if ($this->request->getPost('permission_key') !== $permission['permission_key']) {
            $validationRules['permission_key'] .= '|is_unique[permissions.permission_key]';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($validationRules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $this->permissionModel->update($id, [
            'permission_key' => $this->request->getPost('permission_key'),
            'description' => $this->request->getPost('description')
        ]);

        $this->logModel->logActivity(session()->get('user_id'), 'Updated permission', 'Updated permission: ' . $this->request->getPost('permission_key'));
        return redirect()->to('/permissions')->with('success', 'Permission updated successfully');
    }

    public function delete($id)
    {
        if ($resp = requirePermission('permission_delete', '/permissions')) {
            return $resp;
        }

        $permission = $this->permissionModel->find($id);
        if (!$permission) {
            return redirect()->to('/permissions')->with('error', 'Permission not found');
        }

        // Check if permission is assigned to any roles
        $rolePermissionCount = $this->db->table('role_permissions')->where('permission_id', $id)->countAllResults();
        if ($rolePermissionCount > 0) {
            return redirect()->to('/permissions')->with('error', 'Cannot delete permission. It is assigned to ' . $rolePermissionCount . ' role(s).');
        }

        $this->permissionModel->delete($id);
        $this->logModel->logActivity(session()->get('user_id'), 'Deleted permission', 'Deleted permission: ' . $permission['permission_key']);
        return redirect()->to('/permissions')->with('success', 'Permission deleted successfully');
    }
}
