<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'username', 'password_hash', 'email', 'sign', 'status','token', 'department_id', 'role_id'];
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password_hash'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password_hash'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getUserWithRoles($userId)
    {
        return $this->select('users.*, roles.role_name, roles.id as role_id')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->where('users.id', $userId)
            ->first();
    }

    public function getUserPermissions($userId)
    {
        return $this->db->table('users')
            ->select('permissions.permission_key')
            ->join('role_permissions', 'role_permissions.role_id = users.role_id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('users.id', $userId)
            ->get()
            ->getResultArray();
    }

    public function hasPermission($userId, $permissionKey)
    {
        $permission = $this->db->table('users')
            ->select('permissions.permission_key')
            ->join('role_permissions', 'role_permissions.role_id = users.role_id')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('users.id', $userId)
            ->where('permissions.permission_key', $permissionKey)
            ->get()
            ->getRow();
        
        return $permission !== null;
    }

    public function getUsersWithRoles()
    {
        return $this->select('users.*, roles.role_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->findAll();
    }

    public function getUsersWithRolesDepartment()
    {
        return $this->select('users.*, roles.role_name, departments.name as department_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('users.role_id !=', 1) // Exclude superadmin
            ->orderBy('users.id', 'ASC')
            ->findAll();
    }
}
