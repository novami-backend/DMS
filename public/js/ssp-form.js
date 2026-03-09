// SSP Form Handler - Editable Document Form

class SSPFormHandler {
    constructor() {
        this.isEditMode = false;
        this.formData = {};
        this.init();
    }

    init() {
        this.setupEditButton();
        this.loadFormData();
        this.applyAutoFill();
    }

    applyAutoFill() {
        // Apply auto-fill values when form loads
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
        // Parse auto-fill source and return appropriate value
        const today = new Date();
        
        switch(source) {
            case 'system.date':
                return today.toISOString().split('T')[0];
            
            case 'system.date.formatted':
                const day = String(today.getDate()).padStart(2, '0');
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const year = today.getFullYear();
                return `${day}.${month}.${year}`;
            
            case 'system.time':
                return today.toTimeString().split(' ')[0];
            
            case 'system.datetime':
                return today.toISOString();
            
            case 'user.name':
                // This should come from session/backend
                return document.querySelector('[data-user-name]')?.getAttribute('data-user-name') || '';
            
            case 'user.email':
                return document.querySelector('[data-user-email]')?.getAttribute('data-user-email') || '';
            
            case 'department.name':
                return document.querySelector('[data-department-name]')?.getAttribute('data-department-name') || '';
            
            default:
                return '';
        }
    }

    setupEditButton() {
        const editBtn = document.getElementById('sspEditBtn');
        const saveBtn = document.getElementById('sspSaveBtn');
        const cancelBtn = document.getElementById('sspCancelBtn');

        if (editBtn) {
            editBtn.addEventListener('click', () => this.enableEditMode());
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveForm());
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.cancelEdit());
        }
    }

    enableEditMode() {
        this.isEditMode = true;
        document.getElementById('sspEditBtn').style.display = 'none';
        document.getElementById('sspSaveCancelBtns').classList.add('active');

        // Make all editable fields active
        const editableFields = document.querySelectorAll('.ssp-editable-field');
        editableFields.forEach(field => {
            field.classList.add('editing');
            const input = field.querySelector('input, textarea');
            if (input) {
                input.removeAttribute('readonly');
                input.focus();
            }
        });

        // Show helper text
        this.showNotification('Edit mode enabled. Click on fields to edit.', 'info');
    }

    disableEditMode() {
        this.isEditMode = false;
        document.getElementById('sspEditBtn').style.display = 'block';
        document.getElementById('sspSaveCancelBtns').classList.remove('active');

        // Make all editable fields readonly
        const editableFields = document.querySelectorAll('.ssp-editable-field');
        editableFields.forEach(field => {
            field.classList.remove('editing');
            const input = field.querySelector('input, textarea');
            if (input) {
                input.setAttribute('readonly', 'readonly');
            }
        });
    }

    loadFormData() {
        // Load existing form data if available
        const formDataElement = document.getElementById('existingFormData');
        if (formDataElement) {
            try {
                this.formData = JSON.parse(formDataElement.value);
                this.populateFields();
            } catch (e) {
                console.error('Error loading form data:', e);
            }
        }
    }

    populateFields() {
        // Populate fields with existing data
        for (const [fieldName, value] of Object.entries(this.formData)) {
            const field = document.querySelector(`[name="form_data[${fieldName}]"]`);
            if (field) {
                field.value = value;
            }
        }
    }

    collectFormData() {
        const formData = {};
        const fields = document.querySelectorAll('[name^="form_data["]');
        
        fields.forEach(field => {
            const match = field.name.match(/form_data\[([^\]]+)\]/);
            if (match) {
                const fieldName = match[1];
                formData[fieldName] = field.value;
            }
        });

        return formData;
    }

    async saveForm() {
        const formData = this.collectFormData();
        
        // Validate required fields
        const requiredFields = document.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            this.showNotification('Please fill in all required fields.', 'error');
            return;
        }

        // Update hidden form data field
        const formDataField = document.getElementById('formDataJson');
        if (formDataField) {
            formDataField.value = JSON.stringify(formData);
        }

        this.formData = formData;
        this.disableEditMode();
        this.showNotification('Changes saved successfully!', 'success');
    }

    cancelEdit() {
        // Restore original values
        this.populateFields();
        this.disableEditMode();
        this.showNotification('Changes cancelled.', 'info');
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto-remove after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.ssp-form-container')) {
        window.sspFormHandler = new SSPFormHandler();
    }
});

// Helper function to render SSP form
function renderSSPForm(fields, formData = {}) {
    const container = document.getElementById('dynamicFormFields');
    if (!container) return;

    let html = '<div class="ssp-form-container">';
    
    // Document Header
    html += `
        <table class="ssp-header-table">
            <tr>
                <td class="company-name" rowspan="2">
                    <div class="ssp-editable-field" data-field="company_name">
                        <input type="text" name="form_data[company_name]" value="${formData.company_name || 'Medzus Laboratories'}" readonly>
                    </div>
                </td>
                <td class="doc-title" rowspan="2">
                    <div class="ssp-editable-field" data-field="document_title">
                        <input type="text" name="form_data[document_title]" value="${formData.document_title || 'Standard System Procedure'}" readonly>
                    </div>
                </td>
                <td class="doc-info">
                    <table>
                        <tr>
                            <td>Doc. No.</td>
                            <td>
                                <div class="ssp-editable-field" data-field="doc_no">
                                    <input type="text" name="form_data[doc_no]" value="${formData.doc_no || 'SSP/MR/001/001'}" readonly required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Issue No.</td>
                            <td>
                                <div class="ssp-editable-field" data-field="issue_no">
                                    <input type="text" name="form_data[issue_no]" value="${formData.issue_no || '01'}" readonly required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Rev. No.</td>
                            <td>
                                <div class="ssp-editable-field" data-field="rev_no">
                                    <input type="text" name="form_data[rev_no]" value="${formData.rev_no || '00'}" readonly required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Effective</td>
                            <td>
                                <div class="ssp-editable-field" data-field="effective_date">
                                    <input type="text" name="form_data[effective_date]" value="${formData.effective_date || '20.03.2020'}" readonly required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Pages</td>
                            <td>
                                <div class="ssp-editable-field" data-field="pages">
                                    <input type="text" name="form_data[pages]" value="${formData.pages || '1 of 2'}" readonly>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="ssp-title-row">
                    Title: <span class="ssp-editable-field" data-field="procedure_title" style="display: inline-block; min-width: 300px;">
                        <input type="text" name="form_data[procedure_title]" value="${formData.procedure_title || 'Procedure for Responsibility, Authority and Communication'}" readonly required style="border: none; background: transparent; width: 100%;">
                    </span>
                </td>
            </tr>
        </table>
    `;

    // Content sections
    html += '<div class="ssp-content">';

    // Render dynamic sections based on fields
    const sections = {};
    fields.forEach(field => {
        const section = field.section || 'General';
        if (!sections[section]) {
            sections[section] = [];
        }
        sections[section].push(field);
    });

    let sectionNumber = 1;
    for (const [sectionName, sectionFields] of Object.entries(sections)) {
        if (sectionName === 'Document Header') continue; // Already rendered

        html += `<div class="ssp-section">`;
        html += `<span class="ssp-section-number">${sectionNumber}.0</span>`;
        html += `<span class="ssp-section-title">${sectionName}:</span>`;
        html += `<div class="ssp-section-content">`;

        sectionFields.forEach((field, idx) => {
            const fieldValue = formData[field.field_name] || field.default_value || '';
            const required = field.is_required ? 'required' : '';
            
            if (field.field_type === 'textarea') {
                html += `
                    <div class="ssp-form-field">
                        <div class="ssp-editable-field" data-field="${field.field_name}">
                            <textarea name="form_data[${field.field_name}]" rows="4" readonly ${required}>${fieldValue}</textarea>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="ssp-form-field">
                        <div class="ssp-editable-field" data-field="${field.field_name}">
                            <input type="${field.field_type}" name="form_data[${field.field_name}]" value="${fieldValue}" readonly ${required}>
                        </div>
                    </div>
                `;
            }
        });

        html += `</div></div>`;
        sectionNumber++;
    }

    html += '</div>'; // Close ssp-content
    html += '</div>'; // Close ssp-form-container

    container.innerHTML = html;
}
