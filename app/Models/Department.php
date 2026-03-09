<?php

namespace App\Models;

use CodeIgniter\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'description'];

    public function getDepartmentsWithDocuments()
    {
        return $this->select('departments.*, COUNT(documents.id) as document_count')
            ->join('documents', 'documents.department_id = departments.id', 'left')
            ->groupBy('departments.id')
            ->findAll();
    }

    public function getDepartments()
    {
        return $this->db->table('departments')
            ->select('departments.*, COUNT(documents.id) as document_count')
            ->join('documents', 'documents.department_id = departments.id', 'left')
            ->groupBy('departments.id')
            ->get()
            ->getResult();
    }

    public function getUserDepartments($id)
    {
        return $this->db->table('departments')
            ->select('departments.*, COUNT(documents.id) as document_count')
            ->join('documents', 'documents.department_id = departments.id', 'left')
            ->where('departments.id', $id)
            ->groupBy('departments.id')
            ->get()
            ->getRow();
    }
}
