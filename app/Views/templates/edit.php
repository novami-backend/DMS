<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .field-item {
            border-left: 3px solid #0d6efd;
            transition: all 0.3s;
        }

        .field-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-3 p-0">
                <?= view('common/sidebar') ?>
            </div>

            <div class="p-0">
                <div class="main-content">
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-file-code me-2"></i>' . $pageTitle,
                        'pageDescription' => $pageDescription
                    ]) ?>

                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('templates') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Templates
                        </a>
                    </div>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata( 'error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Template Info -->
                        <div class="col-md-12">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Template Information</h5>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="<?= base_url('templates/update/' . $template['id']) ?>">
                                        <div class="row">
                                            <!-- Left Column -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Template Name</label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="<?= esc($template['name']) ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Document Type</label>
                                                    <select name="document_type_id" class="form-select" required>
                                                        <?php foreach ($documentTypes as $type): ?>
                                                            <option value="<?= $type['id'] ?>"
                                                                <?= $template['document_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                                                <?= esc($type['name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Right Column -->
                                            <div class="col-md-6">
                                                <!-- <div class="mb-3">
                                                    <label class="form-label">Version</label>
                                                    <input type="text" name="version" class="form-control"
                                                        value="<?= esc($template['version']) ?>" required>
                                                </div> -->

                                                <div class="mb-3">
                                                    <label class="form-label">Template Code</label>
                                                    <input type="text" name="code" class="form-control"
                                                        value="<?= esc($template['code']) ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <input type="text" name="description" class="form-control"
                                                        value="<?= esc($template['description']) ?>">
                                                </div>

                                                <!-- <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active"
                                                            value="1" <?= $template['is_active'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label">Active</label>
                                                    </div>
                                                </div> -->
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">PDF Layout Template</label>
                                                <textarea name="layout_template" class="form-control"
                                                    rows="10"><?= esc($template['layout_template']) ?></textarea>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-2"></i>Update Template
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>

    <!-- Edit Field Modal -->
    <div class="modal fade" id="editFieldModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Field</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editFieldForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_field_id" name="field_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Field Name</label>
                                <input type="text" id="edit_field_name" name="field_name" class="form-control" required>
                                <small class="form-text">No spaces, use underscores</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Field Label</label>
                                <input type="text" id="edit_field_label" name="field_label" class="form-control"
                                    required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Field Type</label>
                                <select id="edit_field_type" name="field_type" class="form-select" required>
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="email">Email</option>
                                    <option value="tel">Telephone</option>
                                    <option value="select">Select Dropdown</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="radio">Radio Buttons</option>
                                    <option value="table">Dynamic Table</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Section</label>
                                <input type="text" id="edit_section" name="section" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" id="edit_display_order" name="display_order" class="form-control">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Placeholder</label>
                                <input type="text" id="edit_placeholder" name="placeholder" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default Value</label>
                            <input type="text" id="edit_default_value" name="default_value" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Help Text</label>
                            <textarea id="edit_help_text" name="help_text" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Auto-fill Source</label>
                            <input type="text" id="edit_autofill_source" name="autofill_source" class="form-control">
                            <small class="form-text">user.name, department.name, system.date</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Options (JSON)</label>
                            <textarea id="edit_options" name="options" class="form-control" rows="3"></textarea>
                            <small class="form-text">For select/radio/checkbox/table fields</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_required" name="is_required"
                                    value="1">
                                <label class="form-check-label">Required</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_is_autofill" name="is_autofill"
                                    value="1">
                                <label class="form-check-label">Auto-fill</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Field
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Edit field functionality
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-field-btn');
            const editModal = new bootstrap.Modal(document.getElementById('editFieldModal'));
            const editForm = document.getElementById('editFieldForm');

            editButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const fieldData = JSON.parse(this.getAttribute('data-field'));

                    // Populate form
                    document.getElementById('edit_field_id').value = fieldData.id;
                    document.getElementById('edit_field_name').value = fieldData.field_name;
                    document.getElementById('edit_field_label').value = fieldData.field_label;
                    document.getElementById('edit_field_type').value = fieldData.field_type;
                    document.getElementById('edit_section').value = fieldData.section || 'General';
                    document.getElementById('edit_display_order').value = fieldData.display_order || 0;
                    document.getElementById('edit_placeholder').value = fieldData.placeholder || '';
                    document.getElementById('edit_default_value').value = fieldData.default_value || '';
                    document.getElementById('edit_help_text').value = fieldData.help_text || '';
                    document.getElementById('edit_autofill_source').value = fieldData.autofill_source || '';
                    document.getElementById('edit_options').value = fieldData.options || '';
                    document.getElementById('edit_is_required').checked = fieldData.is_required == 1;
                    document.getElementById('edit_is_autofill').checked = fieldData.is_autofill == 1;

                    editModal.show();
                });
            });

            // Handle form submission
            editForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const fieldId = document.getElementById('edit_field_id').value;
                const formData = new FormData(editForm);

                // Show loading
                const submitBtn = editForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
                submitBtn.disabled = true;

                fetch(`<?= base_url('templates/update-field/') ?>${fieldId}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            showNotification('Field updated successfully!', 'success');

                            // Reload page after short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showNotification(data.message || 'Failed to update field', 'error');
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('An error occurred while updating the field', 'error');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
            });
        });

        function showNotification(message, type = 'info') {
            const alertClass = type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info';
            const notification = document.createElement('div');
            notification.className = `alert alert-${alertClass} alert-dismissible fade show position-fixed`;
            notification.style.top = '20px';
            notification.style.right = '20px';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '300px';
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>

</html>