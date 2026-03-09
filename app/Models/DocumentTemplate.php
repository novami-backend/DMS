<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentTemplate extends Model
{
    protected $table = 'document_templates';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'document_type_id',
        'name',
        'code',
        'version',
        'description',
        'layout_template',
        // 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [];
    
    protected $updateValidationRules = [
        'document_type_id' => 'required|integer',
        'name' => 'required|min_length[3]|max_length[255]',
        'code' => 'required|min_length[2]|max_length[50]',
    ];

    protected $validationMessages = [
        'code' => [
            'is_unique' => 'This template code already exists.',
        ],
    ];

    public function getTemplateByTypeId($typeId)
    {
        return $this->where('document_type_id', $typeId)
                    // ->where('is_active', 1)
                    ->first();
    }

    public function getTemplateWithFields($templateId)
    {
        $template = $this->find($templateId);
        if ($template) {
            $fieldModel = new TemplateField();
            $template['fields'] = $fieldModel->where('template_id', $templateId)
                                             ->orderBy('display_order', 'ASC')
                                             ->findAll();
        }
        return $template;
    }

    public function getTemplatesWithType()
    {
        return $this->select('document_templates.*, document_types.name as type_name')
                    ->join('document_types', 'document_types.id = document_templates.document_type_id')
                    ->orderBy('document_templates.created_at', 'DESC')
                    ->findAll();
    }
}
