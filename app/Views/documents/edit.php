<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/x0p140w6y1cg1wqvis4ntlnj86m9u0o093a8i98q033d2pjd/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
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
                        'pageTitle' => '<i class="fas fa-file-edit me-2"></i>Edit Document',
                        'pageDescription' => 'Update document details and content'
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
                            <form method="post" action="<?= base_url('/documents/update/' . $document['id']) ?>" id="documentForm" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-file"></i>
                                            </span>
                                            <select name="type_id" class="form-select" id="docTypeSelect" required>
                                                <option value="">Select document type</option>
                                                <?php foreach ($documentTypes as $type): ?>
                                                    <option value="<?= $type['id'] ?>"
                                                        <?= (old('type_id', $document['type_id']) == $type['id']) ? 'selected' : '' ?>>
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
                                                value="<?= old('title', $document['title']) ?>" placeholder="Enter document title" required>
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
                                                    <option value="<?= $dept['id'] ?>"
                                                        <?= (old('department_id', $document['department_id']) == $dept['id']) ? 'selected' : '' ?>>
                                                        <?= esc($dept['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-toggle-on"></i>
                                            </span>
                                            <select name="status" class="form-select">
                                                <option value="draft" <?= (old('status', $document['status']) == 'draft') ? 'selected' : '' ?>>Draft</option>
                                                <option value="active" <?= (old('status', $document['status']) == 'active') ? 'selected' : '' ?>>Active</option>
                                                <option value="archived" <?= (old('status', $document['status']) == 'archived') ? 'selected' : '' ?>>Archived</option>
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
                                                value="<?= old('effective_date', $document['effective_date']) ?>">
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
                                                value="<?= old('review_date', $document['review_date']) ?>">
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
                                    <textarea name="content" id="editor" class="form-control"><?= old('content', $document['content']) ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-paperclip me-2"></i>Attachments
                                    </label>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <!-- Existing Attachments -->
                                            <?php
                                            $attachmentModel = new \App\Models\DocumentAttachment();
                                            $existingAttachments = $attachmentModel->getDocumentAttachmentsWithUploaders($document['id']);
                                            ?>
                                            <?php if (!empty($existingAttachments)): ?>
                                                <h6 class="mb-2">Current Attachments</h6>
                                                <div class="list-group mb-3">
                                                    <?php foreach ($existingAttachments as $attachment): ?>
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <i class="<?= \App\Models\DocumentAttachment::getFileIcon($attachment['file_type']) ?> me-2"></i>
                                                                <strong><?= esc($attachment['file_name']) ?></strong>
                                                                <br>
                                                                <small class="text-muted">
                                                                    Uploaded by: <?= esc($attachment['name'] ?? $attachment['username']) ?>
                                                                    | Size: <?= \App\Models\DocumentAttachment::formatFileSize($attachment['file_size']) ?>
                                                                    | Date: <?= date('M d, Y H:i', strtotime($attachment['created_at'])) ?>
                                                                </small>
                                                            </div>
                                                            <div>
                                                                <?php
                                                                $ext = strtolower(pathinfo($attachment['file_path'], PATHINFO_EXTENSION));
                                                                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                                    <button type="button" class="btn btn-sm btn-info text-white ms-1" data-bs-toggle="modal" data-bs-target="#imageModal<?= $attachment['id'] ?>">
                                                                        <i class="fas fa-eye me-1"></i>Preview
                                                                    </button>
                                                                <?php elseif ($ext === 'pdf'): ?>
                                                                    <button type="button" class="btn btn-sm btn-info text-white ms-1" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $attachment['id'] ?>">
                                                                        <i class="fas fa-eye me-1"></i>Preview
                                                                    </button>
                                                                <?php endif; ?>
                                                                <button type="button" class="btn btn-sm btn-danger ms-1" onclick="deleteAttachment(<?= $attachment['id'] ?>)">
                                                                    <i class="fas fa-trash me-1"></i>Delete
                                                                </button>
                                                                <a href="<?= base_url($attachment['file_path']) ?>" class="btn btn-sm btn-primary" download>
                                                                    <i class="fas fa-download me-1"></i>Download
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Add New Attachments -->
                                            <h6 class="mb-2">Add New Attachments</h6>
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
                                    <a href="<?= base_url('documents') ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-submit">
                                        <i class="fas fa-save me-2"></i>Update Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        let editorInstance;
        let currentTemplate = null;
        const AUTOSAVE_KEY = 'document_edit_autosave_<?= $document['id'] ?>';

        // Existing form data from database
        const existingFormData = <?= !empty($document['form_data']) ? $document['form_data'] : '{}' ?>;

        // Initialize TinyMCE using helper function
        initializeTinyMCE('#editor', {
            onInit: function(editor) {
                editorInstance = editor;

                // Check if document has a template
                const typeSelect = document.getElementById('docTypeSelect');
                if (typeSelect.value) {
                    loadTemplateForEdit(typeSelect.value);
                }
            }
        });

        // Load template for editing
        function loadTemplateForEdit(typeId) {
            fetch(`<?= base_url('/documents/get-template-by-type/') ?>${typeId}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.template) {
                        currentTemplate = data.template;
                        document.getElementById('templateInfo').style.display = 'block';
                    } else {
                        document.getElementById('templateInfo').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching template:', error);
                });
        }

        // Document type change handler - Load template into TinyMCE
        document.getElementById('docTypeSelect').addEventListener('change', function() {
            const typeId = this.value;

            if (!typeId) {
                document.getElementById('templateInfo').style.display = 'none';
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
                    if (data.success && data.template) {
                        currentTemplate = data.template;

                        // Generate HTML content from template
                        const templateContent = generateTemplateHTML(data.template, data.fields, existingFormData);

                        // Load into TinyMCE
                        if (editorInstance) {
                            editorInstance.setContent(templateContent);
                            document.getElementById('templateInfo').style.display = 'block';

                            showNotification('Template loaded successfully! You can now edit the content.', 'success');
                        }
                    } else {
                        document.getElementById('templateInfo').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching template:', error);
                    showNotification('Error loading template', 'error');
                });
        });

        // Generate HTML content from template
        function generateTemplateHTML(template, fieldsBySection, existingData = {}) {
            let html = '';

            // Add template header
            html += `<h2 style="text-align: center;">${template.name}</h2>`;
            html += `<p style="text-align: center;"><em>Document Code: ${template.code}</em></p>`;
            html += '<hr>';

            // Generate content for each section
            for (const [section, fields] of Object.entries(fieldsBySection)) {
                html += `<h3>${section}</h3>`;

                fields.forEach(field => {
                    // Use existing data if available, otherwise use default value
                    const fieldValue = existingData[field.field_name] || field.default_value || '';
                    const placeholder = field.placeholder || `Enter ${field.field_label.toLowerCase()}`;

                    if (field.field_type === 'textarea') {
                        html += `<p><strong>${field.field_label}:</strong></p>`;
                        html += `<p>${fieldValue || placeholder}</p>`;
                    } else if (field.field_type === 'table') {
                        const columns = field.options ? JSON.parse(field.options) : [];
                        html += `<p><strong>${field.field_label}:</strong></p>`;
                        html += '<table border="1" style="width: 100%; border-collapse: collapse;">';
                        html += '<thead><tr>';
                        columns.forEach(col => {
                            html += `<th style="padding: 8px; background-color: #f0f0f0;">${col.label}</th>`;
                        });
                        html += '</tr></thead>';
                        html += '<tbody>';
                        html += '<tr>';
                        columns.forEach(() => {
                            html += '<td style="padding: 8px;">&nbsp;</td>';
                        });
                        html += '</tr>';
                        html += '</tbody></table>';
                    } else {
                        html += `<p><strong>${field.field_label}:</strong> ${fieldValue || placeholder}</p>`;
                    }
                });

                html += '<br>';
            }

            return html;
        }

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

        // Clear autosave on successful form submission
        document.querySelector('form').addEventListener('submit', function() {
            localStorage.removeItem(AUTOSAVE_KEY);
        });
    </script>
    <script>
        // Attachment handling for multiple files
        const attachmentsInput = document.getElementById('attachmentsInput');
        const attachmentsList = document.getElementById('attachmentsList');

        if (attachmentsInput) {
            attachmentsInput.addEventListener('change', function() {
                displayAttachments(this.files);
            });
        }

        function displayAttachments(files) {
            if (!attachmentsList) return;

            attachmentsList.innerHTML = '';

            if (files.length === 0) {
                attachmentsList.innerHTML = '';
                return;
            }

            const list = document.createElement('div');
            list.className = 'list-group mt-3';

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

        function deleteAttachment(attachmentId) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                fetch('<?= base_url('attachments/delete') ?>/' + attachmentId, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error deleting attachment');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the attachment');
                    });
            }
        }
    </script>

    <!-- Attachment Modals -->
    <?php if (!empty($existingAttachments)): ?>
        <?php foreach ($existingAttachments as $attachment): ?>
            <?php
            $ext = strtolower(pathinfo($attachment['file_path'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])):
            ?>
                <!-- Image Modal -->
                <div class="modal fade" id="imageModal<?= $attachment['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= esc($attachment['file_name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center p-0">
                                <img src="<?= base_url($attachment['file_path']) ?>" class="img-fluid" alt="<?= esc($attachment['file_name']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            <?php elseif ($ext === 'pdf'): ?>
                <!-- PDF Modal -->
                <div class="modal fade" id="pdfModal<?= $attachment['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
                        <div class="modal-content h-100">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= esc($attachment['file_name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-0 h-100">
                                <object data="<?= base_url($attachment['file_path']) ?>" type="application/pdf" width="100%" height="100%" style="min-height: 75vh;">
                                    <iframe src="<?= base_url($attachment['file_path']) ?>" width="100%" height="100%" style="border: none; min-height: 75vh;">
                                        <p>Your browser does not support PDFs.
                                            <a href="<?= base_url($attachment['file_path']) ?>" target="_blank">Download the PDF</a>.
                                        </p>
                                    </iframe>
                                </object>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>