<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use App\Models\User;
use App\Models\Department;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $filters = [
            'status' => $this->request->getGet('status'),
            'priority' => $this->request->getGet('priority'),
            'category' => $this->request->getGet('category')
        ];

        $notifications = $this->notificationModel
            ->select('notifications.*, u.username as creator_name, r.username as recipient_name')
            ->join('users u', 'u.id = notifications.user_id')
            ->join('users r', 'r.id = notifications.recipient_id')
            ->where('recipient_id', session()->get('user_id'));

        if (!empty($filters['status'])) {
            $notifications->where('notifications.status', $filters['status']);
        }
        if (!empty($filters['priority'])) {
            $notifications->where('notifications.priority', $filters['priority']);
        }
        if (!empty($filters['category'])) {
            $notifications->where('notifications.category', $filters['category']);
        }

        $data = [
            'notifications' => $notifications->orderBy('notifications.created_at', 'DESC')->findAll(),
            'filters' => $filters,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        return view('notifications/index', $data);
    }

    public function create()
    {
        $userModel = new User();
        $deptModel = new Department();
        
        $data = [
            'users' => $userModel->findAll(),
            'departments' => $deptModel->findAll(),
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];
        return view('notifications/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'target_type' => 'required|in_list[individual,department,all]',
            'message' => 'required|min_length[5]',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'frequency' => 'required|in_list[once,daily,weekly,monthly,yearly,custom]',
            'interval_minutes' => 'permit_empty|integer|greater_than[0]'
        ]);

        if ($this->request->getPost('target_type') === 'individual') {
            $validation->setRule('recipient_ids', 'Users', 'required');
        } elseif ($this->request->getPost('target_type') === 'department') {
            $validation->setRule('department_id', 'Department', 'required|is_not_unique[departments.id]');
        }

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $targetType = $this->request->getPost('target_type');
        $recipientIds = [];
        $userModel = new User();

        if ($targetType === 'individual') {
            $recipientIds = (array) $this->request->getPost('recipient_ids');
        } elseif ($targetType === 'department') {
            $deptId = $this->request->getPost('department_id');
            $users = $userModel->where('department_id', $deptId)->findAll();
            $recipientIds = array_column($users, 'id');
        } elseif ($targetType === 'all') {
            $users = $userModel->findAll();
            $recipientIds = array_column($users, 'id');
        }

        if (empty($recipientIds)) {
            return redirect()->back()->withInput()->with('error', 'No recipients found for the selected target.');
        }

        $successCount = 0;
        foreach ($recipientIds as $recipientId) {
            $notifData = [
                'user_id' => session()->get('user_id'),
                'recipient_id' => $recipientId,
                'type' => 'custom',
                'message' => $this->request->getPost('message'),
                'priority' => $this->request->getPost('priority'),
                'frequency' => $this->request->getPost('frequency'),
                'interval_minutes' => $this->request->getPost('interval_minutes') ?: null,
                'category' => $this->request->getPost('category') ?: 'General',
                'link_url' => $this->request->getPost('link_url'),
                'status' => 'unread'
            ];

            if ($this->notificationModel->createNotification($notifData)) {
                $successCount++;
            }
        }

        if ($successCount > 0) {
            return redirect()->to('/notifications')->with('success', "Notification created successfully for $successCount user(s).");
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create notification');
    }

    public function markAllRead()
    {
        $this->notificationModel->markAllAsRead(session()->get('user_id'));
        return redirect()->to('/notifications')->with('success', 'All notifications marked as read');
    }

    public function view($id)
    {
        $notification = $this->notificationModel
            ->select('notifications.*, users.username as name')
            ->join('users', 'users.id = notifications.user_id')
            ->where('notifications.id', $id)
            ->first();

        if (!$notification) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Notification not found");
        }

        $this->notificationModel->markAsRead($id);

        return view('notifications/view', compact('notification'));
    }
}
