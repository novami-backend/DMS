<?php

namespace App\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\UserActivityLog;
use App\Models\NotificationModel;

class Roles extends BaseController
{
    protected $roleModel;
    protected $permissionModel;
    protected $notificationModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        $this->roleModel = new Role();
        $this->permissionModel = new Permission();        
        $this->notificationModel = new NotificationModel();
        $this->logModel = new UserActivityLog();
        $this->db = \Config\Database::connect();
        helper('permission');
    }

    public function index()
    {
        $roles = $this->roleModel->getRolesWithPermissions();
        $data = [
            'roles' => $roles,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
            // ,'can_create_roles' => userHasPermission('create_roles'),
            // 'can_edit_roles' => userHasPermission('edit_roles'),
            // 'can_delete_roles' => userHasPermission('delete_roles')
        ];

        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;

        $this->logModel->logActivity(session()->get('user_id'), 'Viewed roles list');
        return view('roles/index', $data);
    }

    public function create()
    {
        if ($resp = requirePermission('role_create', '/roles')) {
            return $resp;
        }
        $permissions = $this->permissionModel->findAll();
        $data = [
            'permissions' => $permissions,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];
        return view('roles/create', $data);
    }

    public function store()
    {
        if ($resp = requirePermission('role_create', '/roles')) {
            return $resp;
        }
        $validation = \Config\Services::validation();
        $validation->setRules([
            'role_name' => 'required|min_length[2]|is_unique[roles.role_name]',
            'description' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $roleId = $this->roleModel->insert([
            'role_name' => $this->request->getPost('role_name'),
            'description' => $this->request->getPost('description')
        ]);

        if ($roleId) {
            // Assign permissions
            $permissionIds = $this->request->getPost('permissions') ?? [];
            if (!empty($permissionIds)) {
                $this->roleModel->assignPermissions($roleId, $permissionIds);
            }

            $this->logModel->logActivity(session()->get('user_id'), 'Created role', 'Created role: ' . $this->request->getPost('role_name'));
            return redirect()->to('/roles')->with('success', 'Role created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create role');
    }

    public function edit($id)
    {
        if ($resp = requirePermission('role_update', '/roles')) {
            return $resp;
        }
        $role = $this->roleModel->find($id);
        $permissions = $this->permissionModel->findAll();
        $rolePermissions = $this->roleModel->getRolePermissions($id);

        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found');
        }

        // Get permission IDs for this role
        $rolePermissionIds = array_column($rolePermissions, 'id');

        $data = [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissionIds' => $rolePermissionIds,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        return view('roles/edit', $data);
    }

    public function update($id)
    {
        if ($resp = requirePermission('role_update', '/roles')) {
            return $resp;
        }
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found');
        }

        $validationRules = [
            'role_name' => 'required|min_length[2]',
            'description' => 'permit_empty'
        ];

        // Check if role name is being changed
        if ($this->request->getPost('role_name') !== $role['role_name']) {
            $validationRules['role_name'] .= '|is_unique[roles.role_name]';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($validationRules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $this->roleModel->update($id, [
            'role_name' => $this->request->getPost('role_name'),
            'description' => $this->request->getPost('description')
        ]);

        // Update permissions
        $permissionIds = $this->request->getPost('permissions') ?? [];
        $this->roleModel->assignPermissions($id, $permissionIds);

        $this->logModel->logActivity(session()->get('user_id'), 'Updated role', 'Updated role: ' . $this->request->getPost('role_name'));
        return redirect()->to('/roles')->with('success', 'Role updated successfully');
    }

    public function delete($id)
    {
        if ($resp = requirePermission('role_delete', '/roles')) {
            return $resp;
        }
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to('/roles')->with('error', 'Role not found');
        }

        // Check if role is assigned to users
        $userCount = $this->db->table('users')->where('role_id', $id)->countAllResults();
        if ($userCount > 0) {
            return redirect()->to('/roles')->with('error', 'Cannot delete role. It is assigned to ' . $userCount . ' user(s).');
        }

        $this->roleModel->delete($id);
        $this->logModel->logActivity(session()->get('user_id'), 'Deleted role', 'Deleted role: ' . $role['role_name']);
        return redirect()->to('/roles')->with('success', 'Role deleted successfully');
    }
}
