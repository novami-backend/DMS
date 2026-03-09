# Dynamic Form Template System - Setup Guide

## Overview
This system allows you to create dynamic forms for different document types without writing code. Each document type can have its own custom form with various field types.

## Step 1: Run Migrations

Run the migrations to create the necessary database tables:

```bash
php spark migrate
```

This will create:
- `document_templates` - Stores template definitions
- `template_fields` - Stores form field configurations
- Updates `documents` table with `template_id` and `form_data` columns

## Step 2: Load Sample Templates

Run the seeder to create 3 sample templates:

```bash
php spark db:seed TemplateSeeder
```

This creates:
1. **Standard System Procedure (SSP_001)** - For SOPs with structured sections
2. **Equipment Calibration Form (CAL_FORM_001)** - For equipment calibration records
3. **Training Record (TR_001)** - For employee training documentation

## Step 3: Load the Helper

Add the form builder helper to your autoload:

Edit `app/Config/Autoload.php`:

```php
public $helpers = ['form_builder'];
```

## Step 4: Access Template Management

Navigate to: `http://your-domain/templates`

You can:
- View all templates
- Create new templates
- Edit existing templates
- Add/edit/delete form fields
- Activate/deactivate templates

## Step 5: Test Document Creation

1. Go to `http://your-domain/documents/create`
2. Select a document type that has a template
3. The form will dynamically load based on the template
4. Fill in the form and submit

## Field Types Supported

1. **text** - Single line text input
2. **textarea** - Multi-line text input
3. **number** - Numeric input
4. **date** - Date picker
5. **email** - Email input with validation
6. **tel** - Telephone number input
7. **select** - Dropdown selection
8. **checkbox** - Multiple checkboxes
9. **radio** - Radio buttons
10. **table** - Dynamic table with add/remove rows
11. **file** - File upload (future)
12. **signature** - Digital signature (future)

## Auto-fill Sources

Fields can be automatically filled from:

- `user.name` - Current user's name
- `user.email` - Current user's email
- `department.name` - User's department name
- `system.date` - Current date
- `system.datetime` - Current date and time
- `system.year` - Current year
- `document.next_number` - Auto-generated document number

## Creating a New Template

### Via UI (Recommended):

1. Go to Templates → Create New Template
2. Fill in template details:
   - Name: "Your Template Name"
   - Code: "UNIQUE_CODE"
   - Document Type: Select from dropdown
   - Version: "1.0"
   - Description: Optional description
3. Click "Create Template"
4. Add fields one by one using the field builder

### Via Database/Seeder:

See `TemplateSeeder.php` for examples of how to create templates programmatically.

## Field Configuration Options

When adding a field:

- **Field Name**: Internal name (no spaces, use underscores)
- **Field Label**: Display label shown to users
- **Field Type**: Select from supported types
- **Section**: Group fields into sections
- **Field Order**: Display order (lower numbers first)
- **Required**: Make field mandatory
- **Auto-fill**: Enable automatic population
- **Auto-fill Source**: Where to get the value from
- **Options**: JSON for select/radio/checkbox/table
- **Default Value**: Pre-filled value
- **Help Text**: Guidance text below field
- **Placeholder**: Placeholder text in input

## Table Field Configuration

For table fields, use this JSON format in Options:

```json
{
  "columns": [
    {"name": "column1", "label": "Column 1"},
    {"name": "column2", "label": "Column 2"},
    {"name": "column3", "label": "Column 3"}
  ]
}
```

## Select/Radio/Checkbox Options

For select, radio, and checkbox fields:

```json
[
  {"value": "option1", "label": "Option 1"},
  {"value": "option2", "label": "Option 2"},
  {"value": "option3", "label": "Option 3"}
]
```

## Document Data Storage

Form data is stored in two ways:

1. **JSON Column**: `documents.form_data` - Stores all form field values as JSON
2. **Searchable**: Can be queried using JSON functions in MySQL

Example query:
```sql
SELECT * FROM documents 
WHERE JSON_EXTRACT(form_data, '$.equipment_name') = 'Centrifuge';
```

## Next Steps

1. ✅ Run migrations
2. ✅ Run seeder
3. ✅ Test template management UI
4. ✅ Test document creation with templates
5. ⏳ Create PDF templates (next phase)
6. ⏳ Add validation rules engine
7. ⏳ Build form builder UI for easier template creation

## Troubleshooting

### Templates not showing in document create:
- Ensure template is marked as "Active"
- Check that template is linked to correct document type
- Verify template has at least one field

### Auto-fill not working:
- Check auto-fill source syntax
- Ensure user session data is available
- Verify department data exists

### Table rows not adding:
- Check browser console for JavaScript errors
- Ensure jQuery is loaded
- Verify table field options JSON is valid

## Support

For issues or questions, check:
1. Migration status: `php spark migrate:status`
2. Database tables exist
3. Helper is loaded
4. Routes are configured correctly

## Future Enhancements

- Visual form builder (drag & drop)
- Field dependencies (show/hide based on other fields)
- Advanced validation rules
- Field calculations
- Conditional sections
- Template versioning
- Template import/export
- PDF generation with templates
