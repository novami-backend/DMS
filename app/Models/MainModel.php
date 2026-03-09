<?php

namespace App\Models;

use CodeIgniter\Model;

class MainModel extends Model
{
    protected $db;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    public function getRecords(string $table, array $where = [])
    {
        $builder = $this->db->table($table);
        if (!empty($where)) {
            $builder->where($where);
        }
        return $builder->get()->getResultArray();
    }

    public function getRecord(string $table, array $where)
    {
        return $this->db->table($table)->where($where)->get()->getRowArray();
    }

    public function getRecordById(string $table, $id)
    {
        return $this->db->table($table)->where('id', $id)->get()->getRowArray();
    }

    public function getRecordCount(string $table)
    {
        return $this->db->table($table)->countAll();
    }

    public function insertRecord(string $table, array $data)
    {
        $builder = $this->db->table($table);
        $inserted = $builder->insert($data);
        return $inserted ? $this->db->insertID() : false;
    }

    public function updateRecord(string $table, array $where, array $data)
    {
        return $this->db->table($table)->where($where)->update($data);
    }

    public function deleteRecord(string $table, array $where)
    {
        return $this->db->table($table)->where($where)->delete();
    }

    public function join(string $table1, string $table2, string $condition, string $type = 'inner')
    {
        return $this->db->table($table1)
            ->join($table2, $condition, $type)
            ->get()
            ->getResultArray();
    }

    public function customQuery(string $sql)
    {
        return $this->db->query($sql)->getResultArray();
    }
}
