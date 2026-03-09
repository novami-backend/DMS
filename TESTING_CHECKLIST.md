# Dynamic Form Template System - Testing Checklist

## ✅ Setup Complete

- [x] Migrations created and run successfully
- [x] Database tables created (document_templates, template_fields)
- [x] Sample data seeded (3 templates with fields)
- [x] Models created (DocumentTemplate, TemplateField)
- [x] Helper created (form_builder_helper)
- [x] Controller created (Templates)
- [x] Views created (index, create, edit)
- [x] Routes configured
- [x] Sidebar updated with Templates menu
- [x] JavaScript for dynamic tables added

## 🧪 Testing Steps

### 1. Access Template Management
- [ ] Navigate to: `http://localhost/DMS/templates`
- [ ] Verify you see 3 templates listed:
  - Standard System Procedure (SSP_001)
  - Equipment Calibration Form (CAL_FORM_001)
  - Training Record (TR_001)
- [ ] Check that each template shows:
  - Name, Code, Document Type, Version, Status, Created Date
  - Edit and Delete buttons

### 2. View Template Details
- [ ] Click "Edit" on "Standard System Procedure"
- [ ] Verify left panel shows template information
- [ ] Verify right panel shows fields grouped by sections:
  - Document Header (7 fields)
  - Document Content (6 fields)
- [ ] Check that fields show:
  - Field label and name
  - Field type badge
  - Required/Auto-fill badges
  - Help text (if any)
  - Auto-fill source (if any)

### 3. Create New Template
- [ ] Click "Create New Template"
- [ ] Fill in:
  - Name: "Test Template"
  - Code: "TEST_001"
  - Document Type: Select any
  - Version: "1.0"
  - Description: "Test template for validation"
- [ ] Click "Create Template"
- [ ] Verify redirect to edit page
- [ ] Verify success message appears

### 4. Add Fields to Template
- [ ] On template edit page, use "Add New Field" form
- [ ] Add a text field:
  - Field Name: test_field
  - Field Label: Test Field
  - Field Type: Text
  - Section: Test Section
  - Check "Required"
- [ ] Click "Add Field"
- [ ] Verify field appears in the fields list
- [ ] Verify it shows in "Test Section"

### 5. Add Different Field Types
Test each field type:
- [ ] Text field
- [ ] Textarea field
- [ ] Number field
- [ ] Date field
- [ ] Select field (with options JSON)
- [ ] Radio field (with options JSON)
- [ ] Checkbox field (with options JSON)
- [ ] Table field (with columns JSON)

### 6. Test Auto-fill
- [ ] Add a field with auto-fill enabled
- [ ] Set autofill_source to: `user.name`
- [ ] Save field
- [ ] Later verify it auto-fills in document creation

### 7. Test Table Field
- [ ] Add a table field with options:
```json
{
  "columns": [
    {"name": "col1", "label": "Column 1"},
    {"name": "col2", "label": "Column 2"}
  ]
}
```
- [ ] Verify field is created successfully

### 8. Delete Field
- [ ] Click delete button on any field
- [ ] Confirm deletion
- [ ] Verify field is removed from list

### 9. Update Template
- [ ] Change template name
- [ ] Change version number
- [ ] Toggle "Active" status
- [ ] Click "Update Template"
- [ ] Verify changes are saved
- [ ] Verify success message

### 10. Test Document Creation (Integration)
- [ ] Go to Documents → Create New Document
- [ ] Select a document type that has a template
- [ ] Verify dynamic form loads
- [ ] Check that:
  - Fields are grouped by sections
  - Required fields show asterisk
  - Auto-fill fields are pre-populated
  - Help text appears below fields
  - Table fields have "Add Row" button

### 11. Test Dynamic Table in Document
- [ ] In document creation, find a table field
- [ ] Click "Add Row" button
- [ ] Verify new row is added
- [ ] Fill in some data
- [ ] Click "Remove" button on a row
- [ ] Verify row is removed

### 12. Submit Document with Template
- [ ] Fill in all required fields
- [ ] Add some table rows
- [ ] Submit the form
- [ ] Verify document is created
- [ ] Check database: `documents.form_data` should contain JSON

### 13. View Saved Document
- [ ] Open the created document
- [ ] Verify form data is displayed
- [ ] Check that table data shows correctly

## 🐛 Common Issues & Solutions

### Issue: Templates page shows 404
**Solution**: Check routes are configured correctly in `app/Config/Routes.php`

### Issue: Helper not found error
**Solution**: Verify `form_builder` is in `app/Config/Autoload.php` helpers array

### Issue: Fields not rendering
**Solution**: 
- Check template has fields in database
- Verify helper function `render_dynamic_form()` exists
- Check browser console for JavaScript errors

### Issue: Auto-fill not working
**Solution**:
- Verify session data exists
- Check autofill_source syntax
- Ensure user is logged in

### Issue: Table rows not adding
**Solution**:
- Check `public/js/dynamic-forms.js` is loaded
- Verify jQuery is available
- Check browser console for errors

### Issue: Form data not saving
**Solution**:
- Check field names use `form_data[field_name]` format
- Verify JSON column exists in documents table
- Check controller is handling form_data correctly

## 📊 Expected Results

### Database Check
Run these queries to verify data:

```sql
-- Check templates
SELECT * FROM document_templates;
-- Should show 3 templates

-- Check fields
SELECT COUNT(*) FROM template_fields;
-- Should show 30+ fields total

-- Check template with fields
SELECT t.name, COUNT(f.id) as field_count
FROM document_templates t
LEFT JOIN template_fields f ON f.template_id = t.id
GROUP BY t.id;
-- Each template should have multiple fields

-- Check document with form data
SELECT id, title, JSON_PRETTY(form_data) 
FROM documents 
WHERE form_data IS NOT NULL 
LIMIT 1;
-- Should show formatted JSON data
```

### UI Check
- [ ] Templates list loads without errors
- [ ] Template edit page shows all sections
- [ ] Fields are grouped correctly
- [ ] Badges show correct status
- [ ] Forms are responsive
- [ ] No console errors

### Functionality Check
- [ ] CRUD operations work for templates
- [ ] CRUD operations work for fields
- [ ] Dynamic forms render correctly
- [ ] Auto-fill populates fields
- [ ] Table rows add/remove
- [ ] Form submission saves JSON
- [ ] Data retrieval works

## 🎯 Success Criteria

All checkboxes above should be checked ✅

The system should:
1. Allow creating templates without code changes
2. Support all 12 field types
3. Render forms dynamically
4. Handle auto-fill correctly
5. Support dynamic tables
6. Save data as JSON
7. Be scalable for 50+ templates

## 📝 Test Report Template

```
Test Date: ___________
Tester: ___________

Templates Created: ___/3 working
Fields Added: ___/8 types tested
Documents Created: ___/3 templates tested

Issues Found:
1. ___________
2. ___________
3. ___________

Overall Status: [ ] Pass [ ] Fail
Notes: ___________
```

## 🚀 Next Steps After Testing

Once all tests pass:
1. Create more templates for your specific needs
2. Customize field types if needed
3. Add PDF generation templates
4. Implement advanced validation
5. Add field dependencies
6. Build visual form builder

---

**Ready to Test!** 🎉

Start with Step 1 and work through each test systematically.
