<?php

namespace App\Models;

use CodeIgniter\Model;

class TemplateField extends Model
{
    protected $table = 'template_fields';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'template_id',
        'field_name',
        'field_label',
        'field_type',
        'display_order',
        'section',
        'is_required',
        'is_autofill',
        'autofill_source',
        'validation_rules',
        'options',
        'default_value',
        'help_text',
        'placeholder'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'template_id' => 'required|integer',
        'field_name' => 'required|min_length[2]|max_length[100]',
        'field_label' => 'required|min_length[2]|max_length[255]',
        'field_type' => 'required|in_list[text,textarea,number,date,select,checkbox,radio,file,table,signature,email,tel]',
    ];

    public function getFieldsByTemplate($templateId)
    {
        return $this->where('template_id', $templateId)
                    ->orderBy('display_order', 'ASC')
                    ->findAll();
    }

    public function getFieldsBySection($templateId)
    {
        $fields = $this->where('template_id', $templateId)
                       ->orderBy('display_order', 'ASC')
                       ->findAll();

        $grouped = [];
        foreach ($fields as $field) {
            $section = $field['section'] ?? 'General';
            if (!isset($grouped[$section])) {
                $grouped[$section] = [];
            }
            $grouped[$section][] = $field;
        }

        return $grouped;
    }

    public function duplicateFields($fromTemplateId, $toTemplateId)
    {
        $fields = $this->where('template_id', $fromTemplateId)->findAll();
        
        foreach ($fields as $field) {
            unset($field['id']);
            $field['template_id'] = $toTemplateId;
            $this->insert($field);
        }
        
        return true;
    }
}
