<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserActivityLog;
use App\Models\MainModel;
use App\Models\NotificationModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $logModel;
    protected $mainModel;
    protected $notificationModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->logModel = new UserActivityLog();
        $this->notificationModel = new NotificationModel();
        $this->mainModel = new MainModel();
        $this->db = \Config\Database::connect();
        helper('permission');
    }

    public function index()
    {
        $users = $this->userModel->getUsersWithRolesDepartment();
        $data = [
            'users' => $users,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];
        // echo $this->db->getLastQuery();die;
        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;
        $this->logModel->logActivity(session()->get('user_id'), 'Viewed user list', 'Viewed user list');
        return view('users/index', $data);
    }

    public function create()
    {
        if ($resp = requirePermission('user_create', '/users')) {
            return $resp;
        }

        $roles = $this->roleModel->findAll();
        $departments = $this->mainModel->getRecords('departments');
        $data = [
            'roles' => $roles,
            'departments' => $departments,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];
        return view('users/create', $data);
    }

    public function store()
    {
        if ($resp = requirePermission('user_create', '/users')) {
            return $resp;
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'          => 'required|min_length[3]',
            'username'      => 'required|min_length[3]|is_unique[users.username]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'role_id'       => 'required|is_not_unique[roles.id]',
            'department_id' => 'required|is_not_unique[departments.id]',
            'sign'          => 'permit_empty|max_length[10]' // initials only, not file upload
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Generate a random unique token
        // Option 1: Strong random token
        $token = bin2hex(random_bytes(16)); // 32-char hex string
        // Option 2: Simpler fallback
        // $token = md5(uniqid(mt_rand(), true));

        $userId = $this->userModel->insert([
            'name'          => $this->request->getPost('name'),
            'username'      => $this->request->getPost('username'),
            'email'         => $this->request->getPost('email'),
            'sign'          => $this->request->getPost('sign'),
            'status'        => $this->request->getPost('status') ?? 'active',
            'department_id' => $this->request->getPost('department_id'),
            'role_id'       => $this->request->getPost('role_id'),
            'token'         => $token,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ]);

        if ($userId) {
            $this->logModel->logActivity(
                session()->get('user_id'),
                'Created user',
                'Created user: ' . $this->request->getPost('username')
            );
            return redirect()->to('/users')->with('success', 'User created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create user');
    }

    public function edit($id)
    {
        if ($resp = requirePermission('user_update', '/users')) {
            return $resp;
        }

        $user = $this->userModel->getUserWithRoles($id);
        $roles = $this->roleModel->findAll();

        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found');
        }

        $user['departments'] = $this->mainModel->getRecords('departments');

        $data = [
            'user' => $user,
            'roles' => $roles,
            'departments' => $user['departments'],
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        return view('users/edit', $data);
    }

    public function update($id)
    {
        if ($resp = requirePermission('user_update', '/users')) {
            return $resp;
        }

        // print_r($this->request->getPost());die;

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found');
        }

        $validationRules = [
            'name' => 'required|min_length[3]',
            'username' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'role_id' => 'required|is_not_unique[roles.id]',
            'department_id' => 'required|is_not_unique[departments.id]',
            // 'sign' => 'if_exist|uploaded[sign]|max_size[sign,2048]|ext_in[sign,png,jpg,jpeg]'
        ];

        // Check if username or email is being changed and validate uniqueness
        if ($this->request->getPost('username') !== $user['username']) {
            $validationRules['username'] .= '|is_unique[users.username]';
        }
        if ($this->request->getPost('email') !== $user['email']) {
            $validationRules['email'] .= '|is_unique[users.email]';
        }

        $validation = \Config\Services::validation();
        $validation->setRules($validationRules);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Handle signature upload
        // $signPath = $user['sign']; // Keep existing signature
        // $signFile = $this->request->getFile('sign');
        // if ($signFile && $signFile->isValid() && !$signFile->hasMoved()) {
        //     // Delete old signature if exists
        //     if ($user['sign'] && file_exists(WRITEPATH . 'uploads/' . $user['sign'])) {
        //         unlink(WRITEPATH . 'uploads/' . $user['sign']);
        //     }

        //     $newName = $signFile->getRandomName();
        //     $signFile->move(WRITEPATH . 'uploads/signatures', $newName);
        //     $signPath = 'signatures/' . $newName;
        // }

        $updateData = [
            'name' => $this->request->getPost('name'),
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            // 'sign' => $signPath,
            'sign' => $this->request->getPost('sign'),
            'status' => $this->request->getPost('status') ?? 'active',
            'department_id' => $this->request->getPost('department_id'),
            'role_id' => $this->request->getPost('role_id')
        ];

        // Only update password if provided
        // if ($this->request->getPost('password')) {
        //     $updateData['password_hash'] = $this->request->getPost('password');
        // }

        $this->userModel->update($id, $updateData);

        $this->logModel->logActivity(session()->get('user_id'), 'Updated user', 'Updated user: ' . $this->request->getPost('username'));
        return redirect()->to('/users')->with('success', 'User updated successfully');
    }

    public function delete($id)
    {
        if ($resp = requirePermission('user_delete', '/users')) {
            return $resp;
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'User not found');
        }

        $this->userModel->delete($id);
        $this->logModel->logActivity(session()->get('user_id'), 'Deleted user', 'Deleted user: ' . $user['username']);
        return redirect()->to('/users')->with('success', 'User deleted successfully');
    }

    // API method to get reviewer details
    public function getReviewers()
    {
        $conditions = ['role_id' => 4];
        // if ($departmentId) {
        //     $conditions['department_id'] = $departmentId;
        // }

        $users = $this->mainModel->getRecords('users', $conditions);

        if (empty($users)) {
            return $this->response->setStatusCode(404)
                ->setJSON(['error' => 'No reviewers found']);
        }

        return $this->response->setJSON($users);
    }

    // API method to get single reviewer details
    public function getReviewer($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Reviewer not found']);
        }

        // Get role name for designation if role_id exists
        $designation = '';
        if ($user['role_id']) {
            $role = $this->roleModel->find($user['role_id']);
            $designation = $role ? $role['role_name'] : '';
        }

        return $this->response->setJSON([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'username' => $user['username'],
            'designation' => $designation,
            'department_id' => $user['department_id']
        ]);
    }


    // API method to get user details
    public function getUser($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        // Get role name for designation if role_id exists
        $designation = '';
        if ($user['role_id']) {
            $role = $this->roleModel->find($user['role_id']);
            $designation = $role ? $role['role_name'] : '';
        }

        return $this->response->setJSON([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'username' => $user['username'],
            'designation' => $designation,
            'department_id' => $user['department_id']
        ]);
    }

    public function editProfile()
    {
        // Step 1: Fetch existing user
        $user = $this->userModel
            ->select('users.*, roles.role_name as role, departments.name as department')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('users.id', session()->get('user_id'))
            ->first();

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("User not found");
        }

        // Load edit form view
        return view('users/edit_profile', ['user' => $user]);
    }

    public function updateProfile()
    {
        // Step 1: Fetch existing user
        $user = $this->userModel->find(session()->get('user_id'));
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("User not found");
        }

        // print_r($_POST);die;
        $id = session()->get('user_id');

        $rules = [
            'name'        => 'required|min_length[3]',
            'designation' => 'permit_empty',
            'username' => "required|min_length[3]|is_unique[users.username,id,{$id}]",
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
        ];

        if ($this->validate($rules)) {
            // Step 3: Prepare updated data
            $data = [
                'name'        => $this->request->getPost('name'),
                'designation' => $this->request->getPost('designation'),
                'username'    => $this->request->getPost('username'),
                'email'       => $this->request->getPost('email'),
                'sign'        => strtoupper($this->request->getPost('sign')),
                // 'status'      => $this->request->getPost('status'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ];

            // Optional password update
            // if ($this->request->getPost('password')) {
            //     $data['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            // }

            // Save changes
            $this->userModel->update(session()->get('user_id'), $data);
            // echo $this->db->getLastQuery();
            // die;

            return redirect()->to('/profile/edit')->with('success', 'Profile updated successfully');
        } else {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }
}
