# Dynamic "Prepared By" Feature

## Overview
The approval table in document templates now automatically populates the "Prepared By" column with the document creator's information, while preserving the document's font styling.

## How It Works

### When Creating a New Document
- The "Prepared By" column shows the **current logged-in user's** information
- Name: User's full name (or username if name is not set)
- Designation: User's role name
- Signature: User's signature (if set)

### When Editing/Viewing a Document
- The "Prepared By" column shows the **original document creator's** information
- This ensures the creator's information is preserved even if someone else edits the document

### When Exporting to PDF
- The "Prepared By" information is included in the PDF export
- Shows the original creator's information

## Font Style Preservation

The system automatically preserves the document's font styling:

1. **Detects existing styles**: The system looks for any `<span>` tags with `style` attributes in the approval table
2. **Applies to dynamic content**: The detected styles are applied to the dynamically inserted user information
3. **Inherits parent styles**: If no specific styles are found, the content inherits the table cell's styles
4. **No default styling**: The system does not apply any default styles, ensuring consistency with your document

### Example:
If your approval table uses:
```html
<span style="font-size: 10pt; font-family: verdana, sans-serif;">Name:</span>
```

The dynamic content will automatically use the same styling:
```html
<span id="prepared-by-name" style="font-size: 10pt; font-family: verdana, sans-serif;">John Doe</span>
```

## Approval Table Structure

Your template should include an approval table with the following structure:

```html
<table id="ssp-approval-table" style="border-collapse: collapse; width: 100%;" border="1">
    <tbody>
        <tr>
            <th style="text-align: center;">
                <span style="font-size: 10pt; font-family: verdana, sans-serif;">Authorization</span>
            </th>
            <th style="text-align: center;">
                <span style="font-size: 10pt; font-family: verdana, sans-serif;">Prepared By</span>
            </th>
            <th style="text-align: center;">
                <span style="font-size: 10pt; font-family: verdana, sans-serif;">Checked By</span>
            </th>
            <th style="text-align: center;">
                <span style="font-size: 10pt; font-family: verdana, sans-serif;">Approved By</span>
            </th>
        </tr>
        <tr>
            <td style="text-align: center;">
                <span style="font-size: 10pt; font-family: verdana, sans-serif;">Name:</span>
            </td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
        </tr>
        <tr>
            <td style="text-align: center;">
                <span style="font-size: 10pt; font-family: verdana, sans-serif;">Designation:</span>
            </td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
        </tr>
        <tr>
            <td style="text-align: center;">
                <span style="font-size: 10pt; font-family: verdana, sans-serif;">Signature:</span>
            </td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
            <td style="text-align: center;">&nbsp;</td>
        </tr>
    </tbody>
</table>
```

## Important Notes

1. **Table ID Required**: The table must have `id="ssp-approval-table"` for the system to recognize it
2. **Column Order**: The "Prepared By" column should be the second column (after "Authorization")
3. **Row Labels**: The system looks for "Name:", "Designation:", and "Signature:" labels to populate the correct cells
4. **Automatic Population**: The system automatically fills in the empty cells (`&nbsp;`) in the "Prepared By" column
5. **Style Preservation**: Any font styles applied to other cells in the table will be automatically applied to the dynamic content

## Technical Details

The system uses the following logic:
- Extracts user information from the `users` table
- Gets role name from the `roles` table via `role_id`
- Detects existing font styles from the table's `<span>` elements
- Replaces empty cells in the "Prepared By" column with:
  - `<span id="prepared-by-name" style="...">[User Name]</span>` for the Name row
  - `<span id="prepared-by-designation" style="...">[Role Name]</span>` for the Designation row
  - `<span id="prepared-by-sign" style="...">[User Signature]</span>` for the Signature row

## Checking Your Templates

To verify your templates are configured correctly, run:
```bash
php spark update:approval-table
```

This command will check all active templates and report which ones have approval tables.
