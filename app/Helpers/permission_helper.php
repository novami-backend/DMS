<?php

use Config\Database;

if (!function_exists('userHasPermission')) {
    function userHasPermission(string $permission): bool
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return false;
        }

        // Superadmin has all permissions
        $roleName = session()->get('role_name');
        if ($roleName === 'superadmin') {
            return true;
        }

        $db = Database::connect();
        $builder = $db->table('users')
            ->select('permissions.permission_key')
            ->join('role_permissions', 'role_permissions.role_id = users.role_id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('users.id', $userId);

        $results = $builder->get()->getResultArray();
        $userPermissions = array_column($results, 'permission_key');

        return in_array($permission, $userPermissions);
    }
}

if (!function_exists('requirePermission')) {
    function requirePermission(string $permission, string $redirectPath = '/')
    {
        if (!userHasPermission($permission)) {
            return redirect()
                ->to($redirectPath)
                ->with('error', 'You do not have permission to perform this action.');
        }
        return null; // means permission granted
    }
}
