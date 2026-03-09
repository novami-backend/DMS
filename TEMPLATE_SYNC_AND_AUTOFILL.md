# Template Synchronization and Auto-Fill Implementation

## Overview
Fixed the issue where template field changes weren't reflected in the document creation form. Now all template modifications (default values, placeholders, required status, auto-fill settings) are immediately reflected when creating documents.

## Problem Solved

### Before
- Template fields had hardcoded default values in JavaScript
- Changing template field properties didn't affect document creation
- Auto-fill functionality wasn't implemented
- Effective date field always showed "20.03.2020" regardless of template settings

### After
- Template fields dynamically load from database
- All field properties (default value, placeholder, required, auto-fill) are respected
- Auto-fill automatically populates fields based on configured sources
- Changes to templates immediately reflect in document creation

## Changes Made

### 1. Dynamic Field Value Loading

**Before:**
```javascript
<input type="text" name="form_data[effective_date]" value="20.03.2020" readonly required>
```

**After:**
```javascript
<input type="text" 
       name="form_data[effective_date]" 
       value="${getFieldValue('effective_date') || ''}" 
       readonly 
       ${getField('effective_date')?.is_required ? 'required' : ''} 
       placeholder="${getField('effective_date')?.placeholder || 'DD.MM.YYYY'}"
       ${getAutoFillAttr('effective_date')}>
```

### 2. Helper Functions Added

```javascript
// Get field value from template
const getFieldValue = (fieldName) => {
    for (const [section, fields] of Object.entries(fieldsBySection)) {
        const field = fields.find(f => f.field_name === fieldName);
        if (field) return field.default_value || '';
    }
    return '';
};

// Get complete field object
const getField = (fieldName) => {
    for (const [section, fields] of Object.entries(fieldsBySection)) {
        const field = fields.find(f => f.field_name === fieldName);
        if (field) return field;
    }
    return null;
};

// Get auto-fill attribute
const getAutoFillAttr = (fieldName) => {
    const field = getField(fieldName);
    if (field && field.is_autofill && field.autofill_source) {
        return `data-autofill-source="${field.autofill_source}"`;
    }
    return '';
};
```

### 3. Auto-Fill Implementation

Added auto-fill functionality in `public/js/ssp-form.js`:

```javascript
applyAutoFill() {
    const autoFillFields = document.querySelectorAll('[data-autofill-source]');
    autoFillFields.forEach(field => {
        const source = field.getAttribute('data-autofill-source');
        const value = this.getAutoFillValue(source);
        if (value && !field.value) {
            field.value = value;
        }
    });
}

getAutoFillValue(source) {
    const today = new Date();
    
    switch(source) {
        case 'system.date':
            return today.toISOString().split('T')[0]; // YYYY-MM-DD
        
        case 'system.date.formatted':
            // DD.MM.YYYY format
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            return `${day}.${month}.${year}`;
        
        case 'system.time':
            return today.toTimeString().split(' ')[0]; // HH:MM:SS
        
        case 'system.datetime':
            return today.toISOString();
        
        case 'user.name':
            return document.querySelector('[data-user-name]')?.getAttribute('data-user-name') || '';
        
        case 'user.email':
            return document.querySelector('[data-user-email]')?.getAttribute('data-user-email') || '';
        
        case 'department.name':
            return document.querySelector('[data-department-name]')?.getAttribute('data-department-name') || '';
        
        default:
            return '';
    }
}
```

## Auto-Fill Sources

### System Sources
- `system.date` - Current date (YYYY-MM-DD format)
- `system.date.formatted` - Current date (DD.MM.YYYY format)
- `system.time` - Current time (HH:MM:SS)
- `system.datetime` - Current date and time (ISO format)

### User Sources
- `user.name` - Current logged-in user's name
- `user.email` - Current user's email
- `department.name` - User's department name

## How It Works

### Template Field Configuration
1. Admin edits template field
2. Sets default value: "" (empty)
3. Sets placeholder: "DD.MM.YYYY"
4. Enables auto-fill: Yes
5. Sets auto-fill source: "system.date.formatted"
6. Saves field

### Document Creation
1. User selects document type
2. Template loads from database
3. Form renders with field properties:
   - Default value: "" (empty, not hardcoded date)
   - Placeholder: "DD.MM.YYYY" (from template)
   - Auto-fill: Enabled
4. Auto-fill runs automatically
5. Field populates with current date in DD.MM.YYYY format

### Field Priority
```
Auto-fill value > Default value > Empty
```

If field has:
- Auto-fill enabled: Uses auto-fill value
- No auto-fill but has default: Uses default value
- Neither: Remains empty (shows placeholder)

## Files Modified

### 1. app/Views/documents/create.php
- Added helper functions for field data retrieval
- Updated SSP form rendering to use template data
- Added auto-fill attribute support
- Removed all hardcoded values

### 2. public/js/ssp-form.js
- Added `applyAutoFill()` method
- Added `getAutoFillValue()` method
- Auto-fill runs on form initialization

## Testing Scenarios

### Scenario 1: Remove Default Value
1. Edit template field "effective_date"
2. Clear default value
3. Save template
4. Create new document
5. ✅ Field is empty (no hardcoded date)

### Scenario 2: Change Placeholder
1. Edit template field "effective_date"
2. Change placeholder to "Enter date"
3. Save template
4. Create new document
5. ✅ Field shows "Enter date" placeholder

### Scenario 3: Enable Auto-Fill
1. Edit template field "effective_date"
2. Enable auto-fill
3. Set source: "system.date.formatted"
4. Save template
5. Create new document
6. ✅ Field automatically fills with current date

### Scenario 4: Make Field Optional
1. Edit template field "effective_date"
2. Uncheck "Required"
3. Save template
4. Create new document
5. ✅ Field is not required (no asterisk)

### Scenario 5: Change Field Order
1. Edit template field
2. Change order from 10 to 5
3. Save template
4. Create new document
5. ✅ Field appears in new position

## Benefits

### For Administrators
- Template changes immediately affect all new documents
- No need to update JavaScript code
- Centralized field management
- Easy to maintain consistency

### For Users
- Always see current template configuration
- Auto-fill saves time
- Clear placeholders guide input
- Consistent experience across documents

### For Developers
- No hardcoded values in JavaScript
- Single source of truth (database)
- Easy to add new auto-fill sources
- Maintainable codebase

## Future Enhancements

### 1. Advanced Auto-Fill Sources
```javascript
- document.type - Current document type name
- document.number - Auto-generated document number
- template.code - Template code
- template.version - Template version
- user.role - User's role name
- user.department.code - Department code
```

### 2. Conditional Auto-Fill
```javascript
// Only auto-fill if another field has specific value
if (field.autofill_condition) {
    const conditionField = document.querySelector(`[name="${field.autofill_condition.field}"]`);
    if (conditionField.value === field.autofill_condition.value) {
        applyAutoFill(field);
    }
}
```

### 3. Custom Auto-Fill Functions
```javascript
// Allow custom JavaScript functions
case 'custom.document_number':
    return generateDocumentNumber(documentType, department);

case 'custom.next_review_date':
    return calculateReviewDate(effectiveDate, reviewPeriod);
```

### 4. Auto-Fill from Database
```javascript
// Fetch values from database
case 'db.latest_version':
    return await fetchLatestVersion(documentType);

case 'db.next_sequence':
    return await getNextSequenceNumber(documentType);
```

## Troubleshooting

### Issue: Field still shows old default value
**Solution**: Clear browser cache and reload page

### Issue: Auto-fill not working
**Solution**: 
1. Check field has `is_autofill` = 1
2. Verify `autofill_source` is set
3. Check browser console for errors
4. Ensure SSP form handler is initialized

### Issue: Template changes not appearing
**Solution**:
1. Verify template was saved successfully
2. Check database for updated values
3. Clear any caching layers
4. Reload document creation page

### Issue: Wrong date format
**Solution**: Use correct auto-fill source:
- `system.date` for YYYY-MM-DD
- `system.date.formatted` for DD.MM.YYYY

## Code Examples

### Example 1: Add New Auto-Fill Source
```javascript
// In ssp-form.js
case 'user.phone':
    return document.querySelector('[data-user-phone]')?.getAttribute('data-user-phone') || '';
```

### Example 2: Custom Date Format
```javascript
case 'system.date.custom':
    const d = new Date();
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
```

### Example 3: Conditional Default
```javascript
const defaultValue = field.default_value || 
                    (field.field_name === 'status' ? 'Draft' : '');
```

## Conclusion

The template synchronization and auto-fill implementation ensures that:
1. Template changes immediately reflect in document creation
2. No hardcoded values in the codebase
3. Auto-fill reduces manual data entry
4. System is maintainable and extensible
5. User experience is consistent and efficient

All template field properties (default values, placeholders, required status, auto-fill settings) are now properly synchronized between the template management and document creation interfaces.
