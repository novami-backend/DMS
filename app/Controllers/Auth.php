<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\Document;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Department;
use App\Models\DocumentType;
use App\Models\NotificationModel;

class Auth extends BaseController
{
    protected $db;
    protected $userModel;
    protected $logModel;
    protected $documentModel;
    protected $roleModel;
    protected $permissionModel;
    protected $departmentModel;
    protected $documentTypeModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->logModel = new UserActivityLog();
        $this->documentModel = new Document();
        $this->roleModel = new Role();
        $this->db = \Config\Database::connect();
        $this->permissionModel = new Permission();
        $this->departmentModel = new Department();
        $this->documentTypeModel = new DocumentType();
        $this->notificationModel = new NotificationModel();
        helper('mail');
    }

    public function login()
    {
        $step = $this->request->getGet('step') ?? 'username';
        $username = $this->request->getGet('username') ?? null;

        if ($this->request->getMethod() === 'POST') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $token    = $this->request->getPost('token');

            $user = $this->userModel
                ->groupStart()
                ->where('username', $username)
                ->orWhere('email', $username)
                ->groupEnd()
                ->first();

            if ($user) {
                $userWithRole = $this->userModel->getUserWithRoles($user['id']);

                if (!empty($user['password_hash'])) {
                    if ($password) {
                        if (password_verify($password, $user['password_hash'])) {
                            session()->regenerate();
                            // success
                            session()->set([
                                'user_id'   => $user['id'],
                                'username'  => $user['username'],
                                'role_id'   => $userWithRole['role_id'],
                                'role_name' => $userWithRole['role_name'],
                                'department_id' => $user['department_id'],
                                'logged_in' => true
                            ]);
                            $this->logModel->logActivity($user['id'], 'User logged in', 'Login successful');
                            return redirect()->to('/dashboard');
                        } else {
                            $this->logModel->logActivity($user['id'], 'Login failed', 'Invalid password');
                            session()->setFlashdata('error', 'Invalid password.');
                        }
                    } else {
                        $this->logModel->logActivity(
                            $user['id'],
                            'Login step',
                            'Opening password window'
                        );
                    }

                    $step = 'password';
                } else {
                    // token flow
                    if ($token && $password) {
                        return $this->verifyToken();
                    } else {
                        $token = bin2hex(random_bytes(16));
                        $this->userModel->update($user['id'], ['token' => $token]);
                        // send email + notification...
                        $this->logModel->logActivity($user['id'], 'Login attempt', 'No password set, token generated and sent to admin');
                        session()->setFlashdata('info', 'No password set. Token has been sent to admin for verification.');
                        $step = 'token';
                    }
                }
            } else {
                $this->logModel->logActivity(null, 'Login failed', "Invalid username/email: {$username}");
                session()->setFlashdata('error', 'Invalid username/email.');
                $step = 'username';
            }
        }

        return view('auth/login', compact('step', 'username'));
    }

    public function resetPassword()
    {
        $step = 'reset';
        // log that user opened reset page (user may not be logged in)
        $this->logModel->logActivity(session()->get('user_id'), 'Opened password reset page');
        return view('auth/login', compact('step'));
    }

    public function resetPasswordRequest()
    {
        $username = $this->request->getPost('username');

        if ($this->request->getMethod() === 'POST') {
            $user = $this->userModel
                ->where('username', $username)
                ->orWhere('email', $username)
                ->first();

            if ($user) {
                // Generate token and update user
                $token = bin2hex(random_bytes(16));
                $this->userModel->update($user['id'], ['token' => $token]);

                // log the reset request
                $this->logModel->logActivity($user['id'], 'Password reset requested', 'Token generated and sent to admin');

                // Send email to admin
                $adminEmail = "jr.developer.novami@gmail.com";
                $subject = "Password Reset Token for User: {$user['name']}";
                $message = "
                <p>User <strong>{$user['name']}</strong> requested a password reset.</p>
                <p>Token: <strong>{$token}</strong></p>
                <p>Please share this token with the user so they can verify and reset their password.</p>";
                sendEmail($adminEmail, $subject, $message);

                // Find admin user record (example: superadmin)
                $admin = $this->userModel->where('username', 'superadmin')->first();

                if ($admin) {
                    // Insert notification
                    $this->notificationModel->createNotification([
                        'user_id' => $user['id'],
                        'type'    => 'password_reset',
                        'message' => "User {$user['name']} requested a password reset. Token sent to admin.",
                        'recipient_id' => $admin['id'],
                        'priority' => 'high'
                    ]);
                }

                session()->setFlashdata('info', 'Password reset token has been sent to admin.');
                return redirect()->to('/login?step=token&username=' . urlencode($username));
            } else {
                // log failed request
                $this->logModel->logActivity(null, 'Password reset failed', 'User not found: ' . $username);
                session()->setFlashdata('error', 'User not found.');
                return redirect()->to('/login?step=reset');
            }
        }

        return redirect()->to('/login?step=reset');
    }

    public function verifyToken()
    {
        if ($this->request->getMethod() === 'POST') {
            $username = $this->request->getPost('username');
            $token    = $this->request->getPost('token');
            $password = $this->request->getPost('new_password');
            $confirmPassword = $this->request->getPost('confirm_password');

            if ($password !== $confirmPassword) {
                return redirect()->back()->with('error', 'Passwords do not match.');
            }

            $user = $this->userModel->where('username', $username)->orWhere('email', $username)->first();
            if ($user && $user['token'] === $token) {
                log_message('debug', 'Reset password for ' . $username . ' with value: ' . $password);

                // Update password directly to avoid double hashing by model's beforeUpdate hook
                $this->db->table('users')->update([
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'token' => null
                ], ['id' => $user['id']]);

                $this->logModel->logActivity($user['id'], 'Password set/reset', 'User updated password via token verification');
                session()->setFlashdata('success', 'Password updated successfully. Please log in with your new password.');
                return redirect()->to('/login');
            } else {
                return redirect()->back()->with('error', 'Invalid token.');
            }
        }
        return view('auth/verify_token');
    }

    public function logout()
    {
        $userId = session()->get('user_id');
        if ($userId) {
            $this->logModel->logActivity($userId, 'User logged out');
        }

        session()->destroy();
        return redirect()->to('/login');
    }

    public function dashboard()
    {
        $data = [
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        $userCount = $this->userModel->countAllResults();
        $data['userCount'] = $userCount;

        $activeUserCount = $this->userModel->where('status', 'active')->countAllResults();
        $data['activeUserCount'] = $activeUserCount;

        $inactiveUserCount = $this->userModel->where('status', 'inactive')->countAllResults();
        $data['inactiveUserCount'] = $inactiveUserCount;

        $documentCount = $this->documentModel->countAllResults();
        $data['documentCount'] = $documentCount;

        $departmentCount = $this->departmentModel->countAllResults();
        $data['departmentCount'] = $departmentCount;

        $documentTypeCount = $this->documentTypeModel->countAllResults();
        $data['documentTypeCount'] = $documentTypeCount;
        // Log dashboard access
        $this->logModel->logActivity(session()->get('user_id'), 'Accessed dashboard');

        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;

        return view('dashboard/index', $data);
    }
}
