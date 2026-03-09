<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Document - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.tiny.cloud/1/x0p140w6y1cg1wqvis4ntlnj86m9u0o093a8i98q033d2pjd/tinymce/8/tinymce.min.js"
        referrerpolicy="origin" crossorigin="anonymous"></script>
    <script src="<?= base_url('js/tinymce-config.js') ?>"></script>
    <?= view('common/styles') ?>
    <style>
        .tox-tinymce {
            border: 1px solid #dee2e6 !important;
            border-radius: 0.375rem !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 p-0">
                <?= view('common/sidebar') ?>
            </div>

            <!-- Main Content -->
            <div class="p-0">
                <div class="main-content">
                    <!-- Header -->
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-file-medical me-2"></i>Create New Document',
                        'pageDescription' => 'Create a new quality document or procedure'
                    ]) ?>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('documents') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Documents
                        </a>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Please correct the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Document Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="<?= base_url('/documents/store') ?>" id="documentForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Document Type <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-file"></i>
                                            </span>
                                            <select name="type_id" class="form-select" id="docTypeSelect" required>
                                                <option value="">Select document type</option>
                                                <?php foreach ($documentTypes as $type): ?>
                                                    <option value="<?= $type['id'] ?>" <?= old('type_id') == $type['id'] ? 'selected' : '' ?>>
                                                        <?= esc($type['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-heading"></i>
                                            </span>
                                            <input type="text" name="title" class="form-control" id="docTitle"
                                                value="<?= old('title') ?>" placeholder="Enter document title" required>
                                        </div>
                                        <div class="form-text">Document title (minimum 3 characters)</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Department <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-building"></i>
                                            </span>
                                            <select name="department_id" class="form-select" required>
                                                <option value="">Select department</option>
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?= $dept['id'] ?>" <?= old('department_id') == $dept['id'] ? 'selected' : '' ?>>
                                                        <?= esc($dept['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <!-- Add new department button -->
                                            <button type="button" class="btn btn-outline-secondary" id="addDeptBtn" data-bs-toggle="modal" data-bs-target="#addDeptModal">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-toggle-on"></i>
                                            </span>
                                            <select name="status" class="form-select">
                                                <option value="draft" <?= old('status') == 'draft' ? 'selected' : '' ?>>
                                                    Draft</option>
                                                <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>
                                                    Active</option>
                                                <option value="archived" <?= old('status') == 'archived' ? 'selected' : '' ?>>Archived</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Effective Date</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-check"></i>
                                            </span>
                                            <input type="date" name="effective_date" class="form-control"
                                                value="<?= old('effective_date') ?>">
                                        </div>
                                        <div class="form-text">Date when document becomes effective</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Review Date</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-day"></i>
                                            </span>
                                            <input type="date" name="review_date" class="form-control"
                                                value="<?= old('review_date') ?>">
                                        </div>
                                        <div class="form-text">Next scheduled review date</div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Content</label>
                                        <div id="templateInfo" style="display: none;">
                                            <span class="badge bg-info">
                                                <i class="fas fa-file-alt me-1"></i>Template Loaded
                                            </span>
                                        </div>
                                    </div>
                                    <textarea name="content" id="editor"
                                        class="form-control"><?= old('content') ?></textarea>
                                </div>

                                <!-- Attachments Section -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-paperclip me-2"></i>Attachments
                                    </label>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <input type="file" name="attachments[]" id="attachmentsInput" class="form-control"
                                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.txt,.csv"
                                                multiple>
                                            <div class="form-text mt-2">
                                                <i class="fas fa-info-circle me-1"></i>You can upload multiple files (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, TXT, CSV)
                                            </div>
                                            <div id="attachmentsList" class="mt-3"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('documents'); ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-submit">
                                        <i class="fas fa-save me-2"></i>Create Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addDeptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addDeptForm" method="post" action="/departments/store">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Department</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="deptName" class="form-label">Department Name</label>
                        <input type="text" name="name" id="deptName" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        document.getElementById('addDeptForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('<?= base_url('departments/store') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(this)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        let select = document.querySelector('select[name="department_id"]');
                        let option = document.createElement('option');
                        option.value = data.id;
                        option.textContent = data.name;
                        option.selected = true;
                        select.appendChild(option);
                        this.reset();
                        bootstrap.Modal.getInstance(document.getElementById('addDeptModal')).hide();
                        showNotification('Department "' + data.name + '" added successfully!', 'success');
                    } else {
                        alert('Error: ' + (data.error || JSON.stringify(data.errors)));
                    }
                });
        });

        let editorInstance;
        const AUTOSAVE_KEY = 'document_create_autosave';

        // Initialize TinyMCE using helper function
        initializeTinyMCE('#editor', {
            onInit: function(editor) {
                editorInstance = editor;
                loadAutosave();
            },
            onChange: function(editor) {
                saveFormData();
            }
        });

        // Save form data to localStorage
        function saveFormData() {
            if (!editorInstance) return;

            const formData = {
                title: document.querySelector('input[name="title"]').value,
                type_id: document.querySelector('select[name="type_id"]').value,
                department_id: document.querySelector('select[name="department_id"]').value,
                status: document.querySelector('select[name="status"]').value,
                effective_date: document.querySelector('input[name="effective_date"]').value,
                review_date: document.querySelector('input[name="review_date"]').value,
                content: editorInstance.getContent(),
                timestamp: new Date().toISOString()
            };

            localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(formData));
            showAutosaveIndicator();
        }

        // Show autosave indicator
        function showAutosaveIndicator() {
            let indicator = document.getElementById('autosave-indicator');

            if (!indicator) {
                indicator = document.createElement('div');
                indicator.id = 'autosave-indicator';
                indicator.style.position = 'fixed';
                indicator.style.bottom = '20px';
                indicator.style.right = '20px';
                indicator.style.padding = '10px 15px';
                indicator.style.backgroundColor = '#28a745';
                indicator.style.color = 'white';
                indicator.style.borderRadius = '5px';
                indicator.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
                indicator.style.zIndex = '9999';
                indicator.style.transition = 'opacity 0.3s';
                indicator.innerHTML = '<i class="fas fa-check-circle me-2"></i>Autosaved';
                document.body.appendChild(indicator);
            }

            indicator.style.opacity = '1';
            setTimeout(() => {
                indicator.style.opacity = '0';
            }, 2000);
        }

        // Load autosaved data
        function loadAutosave() {
            const saved = localStorage.getItem(AUTOSAVE_KEY);
            if (!saved) return;

            const formData = JSON.parse(saved);

            const notification = document.createElement('div');
            notification.className = 'alert alert-warning fade show';
            notification.style.position = 'fixed';
            notification.style.top = '20px';
            notification.style.left = '50%';
            notification.style.transform = 'translateX(-50%)';
            notification.style.zIndex = '9999';
            notification.style.minWidth = '500px';
            notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
            notification.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Autosaved data found!</strong> Last saved: ${new Date(formData.timestamp).toLocaleString()}
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-success" onclick="restoreAutosave()">
                        <i class="fas fa-undo me-1"></i>Restore
                    </button>
                    <button type="button" class="btn btn-sm btn-danger ms-2" onclick="clearAutosave()">
                        <i class="fas fa-trash me-1"></i>Discard
                    </button>
                </div>
            `;
            document.body.appendChild(notification);
        }

        // Restore autosaved data
        window.restoreAutosave = function() {
            const saved = localStorage.getItem(AUTOSAVE_KEY);
            if (!saved) return;

            const formData = JSON.parse(saved);

            document.querySelector('input[name="title"]').value = formData.title || '';
            document.querySelector('select[name="type_id"]').value = formData.type_id || '';
            document.querySelector('select[name="department_id"]').value = formData.department_id || '';
            document.querySelector('select[name="status"]').value = formData.status || 'draft';
            document.querySelector('input[name="effective_date"]').value = formData.effective_date || '';
            document.querySelector('input[name="review_date"]').value = formData.review_date || '';

            if (editorInstance && formData.content) {
                editorInstance.setContent(formData.content);
            }

            const alert = document.querySelector('.alert-warning');
            if (alert) alert.remove();

            const success = document.createElement('div');
            success.className = 'alert alert-success alert-dismissible fade show';
            success.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Autosaved data has been restored successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.main-content').insertBefore(success, document.querySelector('.card'));

            setTimeout(() => {
                if (success.parentNode) success.remove();
            }, 3000);
        };

        // Clear autosaved data
        window.clearAutosave = function() {
            localStorage.removeItem(AUTOSAVE_KEY);
            const alert = document.querySelector('.alert-warning');
            if (alert) alert.remove();

            const confirmation = document.createElement('div');
            confirmation.className = 'alert alert-success alert-dismissible fade show';
            confirmation.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Autosaved data has been discarded.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.main-content').insertBefore(confirmation, document.querySelector('.card'));

            setTimeout(() => {
                if (confirmation.parentNode) confirmation.remove();
            }, 3000);
        };

        // Autosave on form field changes
        document.querySelector('form').addEventListener('input', function(e) {
            if (e.target.matches('input, select')) {
                saveFormData();
            }
        });

        // Clear autosave on successful form submission
        document.querySelector('form').addEventListener('submit', function() {
            localStorage.removeItem(AUTOSAVE_KEY);
        });

        // Document type change handler - Load template layout into editor
        document.getElementById('docTypeSelect').addEventListener('change', function() {
            const typeId = this.value;
            let selectedText = this.options[this.selectedIndex].text;

            if (selectedText && selectedText !== "Select document type") {
                // Find the index of the first '-'
                const hyphenIndex = selectedText.indexOf('-');

                // If a hyphen exists, slice the string from the character after the hyphen
                if (hyphenIndex !== -1) {
                    selectedText = selectedText.substring(hyphenIndex + 1).trim();
                }

                document.getElementById('docTitle').value = selectedText;
            }

            if (!typeId) {
                document.getElementById('templateInfo').style.display = 'none';
                if (typeof editorInstance !== 'undefined' && editorInstance) {
                    editorInstance.setContent('');
                }
                return;
            }

            // Fetch template for this document type
            fetch(`<?= base_url('/documents/get-template-by-type/') ?>${typeId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Template response:', data); // Debug log

                    if (data.success && data.layout_template) {
                        // Load template layout with updated document number into TinyMCE editor
                        if (editorInstance) {
                            editorInstance.setContent(data.layout_template);
                            document.getElementById('templateInfo').style.display = 'block';

                            // Show notification with document number
                            if (data.document_number) {
                                showNotification(`Template loaded with document number: ${data.document_number}`, 'success');

                                // Show debug info if available
                                if (data.debug) {
                                    console.log('Debug info:', data.debug);
                                }
                            } else {
                                showNotification('Template loaded successfully! You can now edit the content.', 'success');
                            }
                        }
                    } else {
                        document.getElementById('templateInfo').style.display = 'none';
                        if (editorInstance) {
                            editorInstance.setContent('');
                        }
                        if (data.success) {
                            showNotification('No template layout found for this document type', 'info');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching template:', error);
                    showNotification('Error loading template', 'error');
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
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Attachment handling
        const attachmentsInput = document.getElementById('attachmentsInput');
        const attachmentsList = document.getElementById('attachmentsList');

        attachmentsInput.addEventListener('change', function() {
            displayAttachments(this.files);
        });

        function displayAttachments(files) {
            attachmentsList.innerHTML = '';

            if (files.length === 0) {
                attachmentsList.innerHTML = '<p class="text-muted"><em>No files selected</em></p>';
                return;
            }

            const list = document.createElement('div');
            list.className = 'list-group';

            for (let file of files) {
                const item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center';

                const fileInfo = document.createElement('div');
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const fileIcons = {
                    'pdf': 'fas fa-file-pdf text-danger',
                    'doc': 'fas fa-file-word text-primary',
                    'docx': 'fas fa-file-word text-primary',
                    'xls': 'fas fa-file-excel text-success',
                    'xlsx': 'fas fa-file-excel text-success',
                    'jpg': 'fas fa-file-image text-warning',
                    'jpeg': 'fas fa-file-image text-warning',
                    'png': 'fas fa-file-image text-warning',
                    'gif': 'fas fa-file-image text-warning',
                    'txt': 'fas fa-file-alt text-secondary',
                    'csv': 'fas fa-file-csv text-info'
                };

                const icon = fileIcons[fileExtension] || 'fas fa-file text-secondary';

                fileInfo.innerHTML = `
                    <div>
                        <i class="${icon} me-2"></i>
                        <strong>${file.name}</strong>
                        <br>
                        <small class="text-muted">${formatFileSize(file.size)}</small>
                    </div>
                `;

                item.appendChild(fileInfo);
                list.appendChild(item);
            }

            attachmentsList.appendChild(list);
        }

        function formatFileSize(bytes) {
            const units = ['B', 'KB', 'MB', 'GB'];
            let size = bytes;
            let unitIndex = 0;

            while (size >= 1024 && unitIndex < units.length - 1) {
                size /= 1024;
                unitIndex++;
            }

            return size.toFixed(2) + ' ' + units[unitIndex];
        }
    </script>
</body>

</html>