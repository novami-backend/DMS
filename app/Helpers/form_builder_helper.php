<?php

if (!function_exists('render_dynamic_form')) {
    function render_dynamic_form($fields, $data = [])
    {
        $html = '';
        $currentSection = '';
        
        foreach ($fields as $field) {
            // Section header
            if ($field['section'] !== $currentSection) {
                if ($currentSection !== '') {
                    $html .= '</div></div>'; // Close previous section
                }
                $html .= '<div class="card mb-4">';
                $html .= '<div class="card-header"><h5 class="mb-0">' . esc($field['section']) . '</h5></div>';
                $html .= '<div class="card-body">';
                $currentSection = $field['section'];
            }
            
            // Get field value
            $value = $data[$field['field_name']] ?? $field['default_value'] ?? '';
            
            // Auto-fill logic
            if ($field['is_autofill'] && empty($value)) {
                $value = get_autofill_value($field['autofill_source']);
            }
            
            // Render field based on type
            $html .= '<div class="mb-3 row">';
            $html .= '<label class="col-sm-3 col-form-label">' . esc($field['field_label']);
            if ($field['is_required']) {
                $html .= ' <span class="text-danger">*</span>';
            }
            $html .= '</label>';
            $html .= '<div class="col-sm-9">';
            
            switch ($field['field_type']) {
                case 'text':
                case 'email':
                case 'tel':
                    $html .= '<input type="' . $field['field_type'] . '" name="form_data[' . $field['field_name'] . ']" class="form-control" value="' . esc($value) . '"';
                    if ($field['placeholder']) $html .= ' placeholder="' . esc($field['placeholder']) . '"';
                    if ($field['is_required']) $html .= ' required';
                    if ($field['is_autofill']) $html .= ' readonly';
                    $html .= '>';
                    break;
                    
                case 'number':
                    $html .= '<input type="number" name="form_data[' . $field['field_name'] . ']" class="form-control" value="' . esc($value) . '"';
                    if ($field['placeholder']) $html .= ' placeholder="' . esc($field['placeholder']) . '"';
                    if ($field['is_required']) $html .= ' required';
                    $html .= '>';
                    break;
                    
                case 'textarea':
                    $html .= '<textarea name="form_data[' . $field['field_name'] . ']" class="form-control" rows="4"';
                    if ($field['placeholder']) $html .= ' placeholder="' . esc($field['placeholder']) . '"';
                    if ($field['is_required']) $html .= ' required';
                    $html .= '>' . esc($value) . '</textarea>';
                    break;
                    
                case 'date':
                    $html .= '<input type="date" name="form_data[' . $field['field_name'] . ']" class="form-control" value="' . esc($value) . '"';
                    if ($field['is_required']) $html .= ' required';
                    $html .= '>';
                    break;
                    
                case 'select':
                    $options = json_decode($field['options'], true) ?? [];
                    $html .= '<select name="form_data[' . $field['field_name'] . ']" class="form-select"';
                    if ($field['is_required']) $html .= ' required';
                    $html .= '>';
                    $html .= '<option value="">Select...</option>';
                    foreach ($options as $opt) {
                        $selected = ($value == $opt['value']) ? 'selected' : '';
                        $html .= '<option value="' . esc($opt['value']) . '" ' . $selected . '>' . esc($opt['label']) . '</option>';
                    }
                    $html .= '</select>';
                    break;
                    
                case 'checkbox':
                    $options = json_decode($field['options'], true) ?? [];
                    $values = is_array($value) ? $value : [];
                    foreach ($options as $opt) {
                        $checked = in_array($opt['value'], $values) ? 'checked' : '';
                        $html .= '<div class="form-check">';
                        $html .= '<input class="form-check-input" type="checkbox" name="form_data[' . $field['field_name'] . '][]" value="' . esc($opt['value']) . '" ' . $checked . '>';
                        $html .= '<label class="form-check-label">' . esc($opt['label']) . '</label>';
                        $html .= '</div>';
                    }
                    break;
                    
                case 'radio':
                    $options = json_decode($field['options'], true) ?? [];
                    foreach ($options as $opt) {
                        $checked = ($value == $opt['value']) ? 'checked' : '';
                        $html .= '<div class="form-check">';
                        $html .= '<input class="form-check-input" type="radio" name="form_data[' . $field['field_name'] . ']" value="' . esc($opt['value']) . '" ' . $checked;
                        if ($field['is_required']) $html .= ' required';
                        $html .= '>';
                        $html .= '<label class="form-check-label">' . esc($opt['label']) . '</label>';
                        $html .= '</div>';
                    }
                    break;
                    
                case 'table':
                    $html .= render_dynamic_table($field, $value);
                    break;
            }
            
            if ($field['help_text']) {
                $html .= '<div class="form-text">' . esc($field['help_text']) . '</div>';
            }
            
            $html .= '</div></div>';
        }
        
        if ($currentSection !== '') {
            $html .= '</div></div>'; // Close last section
        }
        
        return $html;
    }
}

if (!function_exists('get_autofill_value')) {
    function get_autofill_value($source)
    {
        if (empty($source)) return '';
        
        $parts = explode('.', $source);
        
        switch ($parts[0]) {
            case 'user':
                $user = session()->get('user');
                if (isset($parts[1]) && isset($user[$parts[1]])) {
                    return $user[$parts[1]];
                }
                break;
                
            case 'department':
                $deptModel = new \App\Models\Department();
                $dept = $deptModel->find(session()->get('department_id'));
                if ($dept && isset($parts[1])) {
                    return $dept[$parts[1]] ?? '';
                }
                break;
                
            case 'system':
                if (isset($parts[1])) {
                    if ($parts[1] === 'date') return date('Y-m-d');
                    if ($parts[1] === 'datetime') return date('Y-m-d H:i:s');
                    if ($parts[1] === 'year') return date('Y');
                }
                break;
                
            case 'document':
                if (isset($parts[1]) && $parts[1] === 'next_number') {
                    return generate_document_number();
                }
                break;
        }
        
        return '';
    }
}

if (!function_exists('generate_document_number')) {
    function generate_document_number()
    {
        $db = \Config\Database::connect();
        $result = $db->query("SELECT MAX(CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)) as max_num FROM documents")->getRow();
        $nextNum = ($result->max_num ?? 0) + 1;
        return 'DOC-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('render_dynamic_table')) {
    function render_dynamic_table($field, $value)
    {
        $tableConfig = json_decode($field['options'], true) ?? ['columns' => []];
        $data = is_string($value) ? json_decode($value, true) : $value;
        $data = is_array($data) ? $data : [];
        
        $html = '<div class="dynamic-table-wrapper">';
        $html .= '<table class="table table-bordered table-sm dynamic-table" data-field="' . $field['field_name'] . '">';
        $html .= '<thead class="table-light"><tr>';
        
        foreach ($tableConfig['columns'] as $col) {
            $html .= '<th>' . esc($col['label']) . '</th>';
        }
        $html .= '<th width="80">Actions</th></tr></thead>';
        $html .= '<tbody>';
        
        // Render existing rows
        if (!empty($data)) {
            foreach ($data as $index => $row) {
                $html .= '<tr>';
                foreach ($tableConfig['columns'] as $col) {
                    $html .= '<td><input type="text" class="form-control form-control-sm" name="form_data[' . $field['field_name'] . '][' . $index . '][' . $col['name'] . ']" value="' . esc($row[$col['name']] ?? '') . '"></td>';
                }
                $html .= '<td><button type="button" class="btn btn-sm btn-danger remove-row"><i class="fas fa-trash"></i></button></td>';
                $html .= '</tr>';
            }
        }
        
        $html .= '</tbody></table>';
        $html .= '<button type="button" class="btn btn-sm btn-primary add-table-row" data-field="' . $field['field_name'] . '" data-columns=\'' . json_encode($tableConfig['columns']) . '\'>';
        $html .= '<i class="fas fa-plus me-1"></i>Add Row</button>';
        $html .= '</div>';
        
        return $html;
    }
}
