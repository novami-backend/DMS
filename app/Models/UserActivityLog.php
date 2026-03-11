<?php

namespace App\Models;

use CodeIgniter\Model;

class UserActivityLog extends Model
{
    protected $table = 'user_activity_logs';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'action', 'timestamp', 'ip_address', 'details'];

    public function logActivity($userId, $action, $details = null)
    {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'timestamp' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'details' => $details
        ];

        return $this->insert($data);
    }

    public function getUserActivity($userId, $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orderBy('timestamp', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getRecentActivity($limit = 100)
    {
        return $this->select('user_activity_logs.*, users.username,users.name')
            ->join('users', 'users.id = user_activity_logs.user_id')
            ->orderBy('timestamp', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}