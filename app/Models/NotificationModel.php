<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'recipient_id',
        'type',
        'message',
        'frequency',
        'interval_minutes',
        'priority',
        'link_url',
        'next_run_at',
        'category',
        'expires_at',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true; // auto-manage created_at/updated_at
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get unread notifications for a recipient.
     */
    public function getUnread($recipientId = null)
    {
        $builder = $this->where('status', 'unread');
        if ($recipientId !== null) {
            $builder->where('recipient_id', $recipientId);
        }
        return $builder->orderBy('priority', 'DESC')->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get all notifications for a user with filters.
     */
    public function getNotificationsForUser($recipientId, $filters = [])
    {
        $builder = $this->where('recipient_id', $recipientId);

        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $builder->where('priority', $filters['priority']);
        }

        if (!empty($filters['category'])) {
            $builder->where('category', $filters['category']);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        return $this->update($id, [
            'status' => 'read'
        ]);
    }

    /**
     * Mark all notifications for a user as read.
     */
    public function markAllAsRead($recipientId)
    {
        return $this->where('recipient_id', $recipientId)
                    ->where('status', 'unread')
                    ->set(['status' => 'read'])
                    ->update();
    }

    /**
     * Calculate the next run time based on frequency.
     */
    public function calculateNextRun($frequency, $lastRun = null, $intervalMinutes = null)
    {
        $lastRun = $lastRun ?: date('Y-m-d H:i:s');
        $time = strtotime($lastRun);

        switch ($frequency) {
            case 'daily':
                return date('Y-m-d H:i:s', strtotime('+1 day', $time));
            case 'weekly':
                return date('Y-m-d H:i:s', strtotime('+1 week', $time));
            case 'monthly':
                return date('Y-m-d H:i:s', strtotime('+1 month', $time));
            case 'yearly':
                return date('Y-m-d H:i:s', strtotime('+1 year', $time));
            case 'custom':
                if ($intervalMinutes) {
                    return date('Y-m-d H:i:s', strtotime("+{$intervalMinutes} minutes", $time));
                }
                return null;
            default:
                return null;
        }
    }

    /**
     * Create a new notification with enhanced fields.
     */
    public function createNotification($data)
    {
        if (!is_array($data)) {
            return false;
        }
        if (isset($data['frequency']) && $data['frequency'] !== 'once') {
            $data['next_run_at'] = $this->calculateNextRun($data['frequency'], null, $data['interval_minutes'] ?? null);
        }
        $data['status'] = $data['status'] ?? 'unread';
        return $this->insert($data);
    }
}
