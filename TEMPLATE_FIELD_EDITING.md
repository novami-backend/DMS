# Template Field Editing Implementation

## Overview
Added the ability to edit template fields directly from the template edit page. Users can now modify field properties without having to delete and recreate fields.

## Features Added

### 1. Edit Button for Each Field
- Added an "Edit" button next to the delete button for each field
- Button displays a pencil icon for easy identification
- Clicking opens a modal with the field's current data

### 2. Edit Field Modal
- **Full-featured modal** with all field properties:
  - Field Name (technical name)
  - Field Label (display name)
  - Field Type (text, textarea, number, date, etc.)
  - Section (grouping)
  - Field Order (sorting)
  - Placeholder text
  - Default Value
  - Help Text
  - Auto-fill Source
  - Options (JSON for select/radio/table fields)
  - Required checkbox
  - Auto-fill checkbox

### 3. AJAX Update
- Form submits via AJAX (no page reload)
- Shows loading spinner during update
- Displays success/error notifications
- Auto-reloads page after successful update

### 4. Visual Feedback
- Loading state on submit button
- Success notification (green)
- Error notification (red)
- Auto-dismissing notifications after 3 seconds

## Files Modified

### Views
- `app/Views/templates/edit.php`
  - Added edit button to each field
  - Added edit modal HTML
  - Added JavaScript for modal population and form submission
  - Added notification system

### Controllers
- `app/Controllers/Templates.php`
  - Enhanced `updateField()` method
  - Added AJAX request validation
  - Added field existence check
  - Returns JSON response

## How to Use

### Editing a Template Field

1. **Navigate to Template Edit Page**
   - Go to Templates > Click "Edit" on any template
   - You'll see the template info and list of fields

2. **Click Edit Button**
   - Find the field you want to edit
   - Click the blue "Edit" button (pencil icon)
   - Modal opens with current field data

3. **Modify Field Properties**
   - Change any field properties as needed
   - All fields are pre-populated with current values
   - Required fields are marked

4. **Save Changes**
   - Click "Update Field" button
   - Loading spinner appears
   - Success notification shows
   - Page reloads with updated data

5. **Cancel Editing**
   - Click "Cancel" or close modal
   - No changes are saved

## Field Properties Explained

### Basic Properties
- **Field Name**: Technical identifier (no spaces, use underscores)
  - Example: `equipment_name`, `test_date`
  
- **Field Label**: Display name shown to users
  - Example: "Equipment Name", "Test Date"

- **Field Type**: Input type
  - text, textarea, number, date, email, tel
  - select, checkbox, radio, table

### Organization
- **Section**: Groups related fields together
  - Example: "Document Header", "Test Details", "Results"

- **Field Order**: Numeric value for sorting
  - Lower numbers appear first
  - Example: 0, 10, 20, 30

### User Experience
- **Placeholder**: Hint text in empty fields
  - Example: "Enter equipment name"

- **Default Value**: Pre-filled value
  - Example: "N/A", "Pending", current date

- **Help Text**: Additional guidance below field
  - Example: "Enter the full equipment model number"

### Advanced Features
- **Auto-fill Source**: Automatic data population
  - `user.name` - Current user's name
  - `user.email` - Current user's email
  - `department.name` - User's department
  - `system.date` - Current date
  - `system.time` - Current time

- **Options (JSON)**: For select/radio/table fields
  ```json
  ["Option 1", "Option 2", "Option 3"]
  ```
  
  Or for tables:
  ```json
  [
    {"name": "column1", "label": "Column 1"},
    {"name": "column2", "label": "Column 2"}
  ]
  ```

- **Required**: Makes field mandatory
- **Auto-fill**: Enables automatic population

## API Endpoint

### Update Field
```
POST /templates/update-field/{fieldId}
```

**Request Body (FormData):**
```
field_name: string
field_label: string
field_type: string
section: string
field_order: number
placeholder: string
default_value: string
help_text: string
autofill_source: string
options: string (JSON)
is_required: 0|1
is_autofill: 0|1
```

**Response (JSON):**
```json
{
  "success": true,
  "message": "Field updated successfully"
}
```

## JavaScript Functions

### Modal Population
```javascript
// Populates modal with field data
const fieldData = JSON.parse(button.getAttribute('data-field'));
document.getElementById('edit_field_name').value = fieldData.field_name;
// ... etc
```

### AJAX Submission
```javascript
fetch(`/templates/update-field/${fieldId}`, {
    method: 'POST',
    body: formData,
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
})
```

### Notification System
```javascript
showNotification('Field updated successfully!', 'success');
showNotification('Failed to update field', 'error');
```

## Validation

### Client-Side
- Required fields marked with `required` attribute
- Browser validates before submission

### Server-Side
- Field existence check
- AJAX request validation
- Data sanitization via CodeIgniter

## Error Handling

### Common Errors
1. **Field not found**: Field ID doesn't exist
2. **Invalid request**: Not an AJAX request
3. **Update failed**: Database error

### Error Display
- Red notification with error message
- Submit button re-enabled
- User can retry

## Browser Compatibility

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- Mobile: Responsive modal

## Future Enhancements

1. **Drag-and-Drop Reordering**: Visual field ordering
2. **Bulk Edit**: Edit multiple fields at once
3. **Field Duplication**: Copy existing fields
4. **Field Templates**: Pre-configured field sets
5. **Validation Rules**: Custom validation per field
6. **Conditional Logic**: Show/hide based on other fields
7. **Field Groups**: Nested field organization
8. **Import/Export**: Share field configurations

## Testing Checklist

- [x] Edit button appears for all fields
- [x] Modal opens with correct data
- [x] All field properties are editable
- [x] Form validates required fields
- [x] AJAX submission works
- [x] Success notification appears
- [x] Page reloads after update
- [x] Error handling works
- [x] Cancel button closes modal
- [x] Changes persist in database
- [ ] Test with all field types
- [ ] Test with special characters
- [ ] Test with long text
- [ ] Test concurrent edits

## Notes

- Modal uses Bootstrap 5 modal component
- AJAX requests include CSRF protection (if enabled)
- Page reload ensures data consistency
- All changes are logged in database timestamps
- Field name changes may affect existing documents
- Consider data migration when changing field types
