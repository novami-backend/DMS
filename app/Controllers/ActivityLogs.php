<?php

namespace App\Controllers;

use App\Models\UserActivityLog;
use App\Models\NotificationModel;

class ActivityLogs extends BaseController
{
    protected $logModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->logModel = new UserActivityLog();
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $logs = $this->logModel->getRecentActivity(100);
        $data = [
            'logs' => $logs,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;

        return view('activity_logs/index', $data);
    }

    public function userLogs($userId)
    {
        if ($resp = requirePermission('view_activity_logs', '/dashboard')) { return $resp; }

        $logs = $this->logModel->getUserActivity($userId, 50);
        $data = [
            'logs' => $logs,
            'username' => session()->get('username'),
            'role_name' => session()->get('role_name')
        ];

        return view('activity_logs/user_logs', $data);
    }
}
