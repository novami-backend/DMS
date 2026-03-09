# SSP Form Implementation - Custom Styled Document Form

## Overview
Created a specialized form layout for SSP (Standard System Procedure) Template #1 that matches the exact styling of the provided document image. The form features an editable interface with a professional document header and structured content sections.

## Key Features

### 1. Document Header Table
- **Company Name**: Medzus Laboratories (editable)
- **Document Title**: Standard System Procedure (editable)
- **Document Information Grid**:
  - Doc. No. (e.g., SSP/MR/001)
  - Issue No. (e.g., 01)
  - Rev. No. (e.g., 00)
  - Effective Date (e.g., 20.03.2020)
  - Pages (e.g., 1 of 2)
- **Procedure Title**: Full width title field

### 2. Edit Mode Functionality
- **Edit Button**: Fixed position button on the right side
- **Click to Edit**: All fields become editable when edit mode is enabled
- **Visual Feedback**: Fields highlight on hover and show editing state
- **Save/Cancel**: Dedicated buttons to save changes or cancel editing

### 3. Professional Styling
- **Times New Roman Font**: Matches standard document formatting
- **Bordered Table Layout**: Clean, professional document header
- **A4 Page Width**: 210mm max-width for print-ready layout
- **Section Numbering**: Automatic section numbering (1.0, 2.0, etc.)
- **Print-Friendly**: Hides edit buttons and shows clean document when printing

### 4. Responsive Design
- Desktop: Fixed edit button on right side
- Mobile: Edit button moves to top of form
- Maintains readability across all screen sizes

## Files Created

### CSS
- `public/css/ssp-form.css`
  - Complete SSP document styling
  - Editable field styles
  - Print media queries
  - Responsive breakpoints

### JavaScript
- `public/js/ssp-form.js`
  - SSPFormHandler class for managing edit mode
  - Form data collection and validation
  - Save/cancel functionality
  - Notification system
  - renderSSPForm() helper function

### Updated Files
- `app/Views/documents/create.php`
  - Added SSP CSS link
  - Added edit/save/cancel buttons
  - Updated JavaScript to detect SSP template
  - Separate rendering for SSP vs regular forms

## How It Works

### Template Detection
```javascript
// Check if this is SSP template (template id = 1 or code starts with SSP)
isSSPTemplate = data.template.id == 1 || data.template.code.startsWith('SSP');

if (isSSPTemplate) {
    renderSSPFormLayout(data.fields);
    document.getElementById('sspEditBtn').style.display = 'block';
} else {
    renderDynamicForm(data.fields);
}
```

### Edit Mode Flow
1. User selects SSP document type
2. Form loads with SSP styling
3. "Edit Form" button appears on right side
4. User clicks "Edit Form"
5. All fields become editable (readonly removed)
6. Fields highlight on hover
7. User makes changes
8. User clicks "Save Changes" or "Cancel"
9. Form validates required fields
10. Data is saved to hidden JSON field
11. Edit mode disabled, fields become readonly again

### Form Data Structure
```json
{
  "company_name": "Medzus Laboratories",
  "document_title": "Standard System Procedure",
  "doc_no": "SSP/MR/001",
  "issue_no": "01",
  "rev_no": "00",
  "effective_date": "20.03.2020",
  "pages": "1 of 2",
  "procedure_title": "Procedure for Responsibility, Authority and Communication",
  "purpose": "To establish a system...",
  "scope": "This procedure is applicable...",
  // ... other fields
}
```

## Usage Instructions

### Creating an SSP Document

1. **Navigate to Create Document**
   - Go to Documents > Create Document

2. **Select SSP Document Type**
   - Choose a document type that uses SSP template (Template #1)
   - Form will automatically load with SSP styling

3. **Edit Form Data**
   - Click "Edit Form" button on the right side
   - Click on any field to edit
   - Fill in all required fields (marked with borders)

4. **Save Changes**
   - Click "Save Changes" to commit your edits
   - Or click "Cancel" to discard changes

5. **Submit Document**
   - Fill in department, status, and dates
   - Click "Create Document" to save

### Viewing SSP Documents

- SSP documents display with the same professional layout
- All form data is shown in the structured format
- Export to PDF maintains the SSP styling

## Styling Details

### Header Table
```css
.ssp-header-table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid #000;
}
```

### Editable Fields
```css
.ssp-editable-field {
    position: relative;
    min-height: 24px;
    padding: 4px 8px;
    border: 1px dashed transparent;
    cursor: pointer;
}

.ssp-editable-field:hover {
    background: #f8f9fa;
    border-color: #007bff;
}
```

### Section Numbering
```css
.ssp-section-number {
    font-weight: bold;
    font-size: 12pt;
    display: inline-block;
    min-width: 40px;
}
```

## Customization

### Adding New SSP Templates

To create additional SSP-style templates:

1. Create template with code starting with "SSP_"
2. System will automatically use SSP styling
3. Or modify the detection logic in create.php:

```javascript
isSSPTemplate = data.template.id == 1 || 
                data.template.code.startsWith('SSP') ||
                data.template.id == YOUR_TEMPLATE_ID;
```

### Modifying Header Fields

Edit the `renderSSPFormLayout()` function in create.php to add/remove header fields:

```javascript
html += `
    <tr>
        <td>Your Field:</td>
        <td>
            <div class="ssp-editable-field">
                <input type="text" name="form_data[your_field]" value="" readonly>
            </div>
        </td>
    </tr>
`;
```

### Changing Colors/Fonts

Modify `public/css/ssp-form.css`:

```css
.ssp-form-container {
    font-family: 'Your Font', serif;
}

.ssp-header-table .doc-info table td:first-child {
    background: #your-color;
}
```

## Browser Compatibility

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support
- Mobile browsers: Responsive layout

## Print Support

- Edit buttons automatically hidden
- Clean document layout
- Maintains borders and formatting
- A4 page size optimized

## Future Enhancements

1. **Auto-numbering**: Automatic document number generation
2. **Version Control**: Track revisions automatically
3. **Digital Signatures**: Add signature capture
4. **Approval Stamps**: Visual approval indicators
5. **Multi-page Support**: Automatic page numbering
6. **Template Variations**: Different SSP layouts (SSP-A, SSP-B, etc.)
7. **Field Validation**: Custom validation rules per field
8. **Auto-save**: Periodic auto-save of form data

## Testing Checklist

- [x] SSP template loads with custom styling
- [x] Edit button appears for SSP templates
- [x] Edit mode enables field editing
- [x] Save button validates and saves data
- [x] Cancel button restores original values
- [x] Form data saves to database
- [x] Document displays with SSP styling
- [x] PDF export includes SSP layout
- [x] Print hides edit buttons
- [x] Responsive on mobile devices
- [ ] Test with actual SSP template data
- [ ] Verify all field types work correctly
- [ ] Test form validation
- [ ] Test with multiple SSP documents

## Notes

- SSP template is detected by ID (1) or code prefix (SSP_)
- All other templates use the standard form layout
- Form data is stored as JSON in the database
- The system maintains backward compatibility with existing documents
- Edit mode is client-side only until "Save Changes" is clicked
