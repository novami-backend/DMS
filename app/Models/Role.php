<?php

namespace App\Models;

use CodeIgniter\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['role_name', 'description'];

    public function getRolePermissions($roleId)
    {
        return $this->db->table('roles')
            ->select('permissions.*')
            ->join('role_permissions', 'role_permissions.role_id = roles.id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('roles.id', $roleId)
            ->get()
            ->getResultArray();
    }

    // public function getRolesWithPermissions()
    // {
    //     $roles = $this->findAll();
    //     foreach ($roles as &$role) {
    //         $role['permissions'] = $this->getRolePermissions($role['id']);
    //     }
    //     return $roles;
    // }

    public function getRolesWithPermissions()
    {
        // Fetch all roles
        $roles = $this->findAll();

        foreach ($roles as &$role) {
            // Attach permissions
            $role['permissions'] = $this->getRolePermissions($role['id']);

            // Count users assigned to this role
            $role['user_count'] = $this->db->table('users')
                ->where('role_id', $role['id'])
                ->countAllResults();
        }

        return $roles;
    }

    public function assignPermissions($roleId, $permissionIds)
    {
        // First remove existing permissions
        $this->db->table('role_permissions')->where('role_id', $roleId)->delete();

        // Add new permissions
        $data = [];
        foreach ($permissionIds as $permissionId) {
            $data[] = [
                'role_id' => $roleId,
                'permission_id' => $permissionId
            ];
        }

        if (!empty($data)) {
            $this->db->table('role_permissions')->insertBatch($data);
        }
    }
}
