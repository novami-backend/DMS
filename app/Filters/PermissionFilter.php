<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Database;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'You must be logged in.');
        }

        // Permission required is passed as argument in Routes
        $requiredPermission = $arguments[0] ?? null;
        if (!$requiredPermission) {
            return; // no specific permission required
        }

        if (!$this->userHasPermission($userId, $requiredPermission)) {
            return redirect()->to('/')->with('error', 'You do not have permission to access this resource.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing needed after
    }

    private function userHasPermission(int $userId, string $permission): bool
    {
        $db = Database::connect();
        $builder = $db->table('users')
            ->select('permissions.permission_name')
            ->join('role_permissions', 'role_permissions.role_id = users.role_id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('users.id', $userId);

        $results = $builder->get()->getResultArray();
        $userPermissions = array_column($results, 'permission_name');

        return in_array($permission, $userPermissions);
    }
}
