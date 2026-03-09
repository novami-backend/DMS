# Dynamic Forms Implementation

## Overview
Implemented a dynamic form system for the document creation page that loads template-based forms when a document type is selected. Users can fill out structured forms, preview the data, and export to PDF.

## Features Implemented

### 1. Dynamic Form Loading
- When a document type is selected on the create document page, the system fetches the associated template
- Form fields are dynamically rendered based on the template configuration
- Supports multiple field types: text, textarea, number, date, select, checkbox, radio, table, signature, email, tel

### 2. Form Data Storage
- Form data is saved as JSON in the `form_data` column of the documents table
- Data is preserved and can be edited later
- Structured data allows for easy querying and reporting

### 3. Preview Functionality
- Users can preview the document before saving
- Preview opens in a new window showing formatted form data
- Clean, professional layout with sections and field labels

### 4. PDF Export
- Enhanced PDF export to include form data
- Tables are properly rendered in PDF format
- Checkbox fields show checked/unchecked status
- Maintains document metadata and formatting

### 5. Document View
- Document view page now displays form data when available
- Falls back to regular content display for documents without templates
- Organized by sections with clear field labels

## Files Modified

### Controllers
- `app/Controllers/Documents.php`
  - Added `getTemplateByType($typeId)` - API endpoint to fetch template fields
  - Updated `store()` - Save form data as JSON
  - Added `preview($id)` - Preview document with form data
  - Updated `view($id)` - Load and display form data
  - Updated `exportPdf($id)` - Include form data in PDF export

### Views
- `app/Views/documents/create.php`
  - Added dynamic form container
  - Added preview button
  - JavaScript to load and render form fields
  - Support for all field types including tables

- `app/Views/documents/preview.php` (NEW)
  - Clean preview layout
  - Displays form data by sections
  - Print and export buttons

- `app/Views/documents/view.php`
  - Updated to display form data when available
  - Added PDF export button
  - Maintains backward compatibility with non-template documents

### Routes
- `app/Config/Routes.php`
  - Added `get-template-by-type/(:num)` - Fetch template API
  - Added `preview` routes (POST and GET)
  - Added `export-pdf/(:num)` - PDF export route

### JavaScript
- `public/js/dynamic-forms.js`
  - Table row add/remove functionality
  - Auto-save support (optional)

## How to Use

### Creating a Document with Dynamic Forms

1. Navigate to Documents > Create Document
2. Select a Document Type that has a template configured
3. The dynamic form will load automatically
4. Fill out all required fields (marked with *)
5. For table fields, click "Add Row" to add entries
6. Click "Preview" to see how the document will look
7. Click "Create Document" to save

### Viewing Documents

1. Navigate to Documents > View
2. Documents with form data will display structured information
3. Documents without templates show regular content
4. Click "Export as PDF" to download a formatted PDF

### Exporting to PDF

1. Open any document
2. Click "Export as PDF" in the action sidebar
3. PDF will open in a new tab with all form data formatted
4. Use browser's save function to download

## Field Types Supported

- **Text**: Single-line text input
- **Textarea**: Multi-line text input
- **Number**: Numeric input
- **Date**: Date picker
- **Email**: Email input with validation
- **Tel**: Telephone number input
- **Select**: Dropdown selection
- **Checkbox**: Single checkbox (Yes/No)
- **Radio**: Radio button group
- **Table**: Dynamic table with add/remove rows
- **Signature**: Signature field (text-based)

## Database Schema

The `documents` table already has a `form_data` column (TEXT) that stores the JSON data:
```sql
form_data TEXT NULL
```

## Testing Checklist

- [ ] Create a template with various field types
- [ ] Select document type and verify form loads
- [ ] Fill out form and save document
- [ ] View document and verify data displays correctly
- [ ] Preview document before saving
- [ ] Export document to PDF
- [ ] Verify table fields work (add/remove rows)
- [ ] Test required field validation
- [ ] Test with documents that don't have templates

## Future Enhancements

1. **Auto-fill**: Implement auto-fill from database sources
2. **Validation**: Add custom validation rules per field
3. **Conditional Fields**: Show/hide fields based on other field values
4. **File Upload**: Support file attachments in forms
5. **Digital Signatures**: Implement proper digital signature capture
6. **Form Versioning**: Track changes to form data over time
7. **Export to Word**: Add Word document export with form data

## Notes

- The system maintains backward compatibility with existing documents
- Documents without templates continue to use the rich text editor
- Form data is stored as JSON for flexibility and future enhancements
- All existing document features (approval workflow, versioning, etc.) work with form-based documents
