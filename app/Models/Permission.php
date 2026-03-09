<?php

namespace App\Models;

use CodeIgniter\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['permission_key', 'description'];

    public function getPermissionsByRole($roleId)
    {
        return $this->select('permissions.*')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->where('role_permissions.role_id', $roleId)
            ->findAll();
    }

    public function getAllPermissionsWithRoles()
    {
        $permissions = $this->findAll();
        foreach ($permissions as &$permission) {
            $permission['roles'] = $this->db->table('role_permissions')
                ->select('roles.role_name')
                ->join('roles', 'roles.id = role_permissions.role_id')
                ->where('role_permissions.permission_id', $permission['id'])
                ->get()
                ->getResultArray();
        }
        return $permissions;
    }
}