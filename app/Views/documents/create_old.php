<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Document - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script> -->
    <!-- <script src="https://cdn.ckeditor.com/ckeditor5/super-build/ckeditor.js"></script> -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <?= view('common/styles') ?>
    <style>
        .ck-editor__editable {
            min-height: 400px;
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
                        <a href="<?= base_url('documents'); ?>" class="btn btn-secondary">
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

                    <!-- Autosave Status -->
                    <!-- <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Autosave is enabled.</strong> Your changes are automatically saved as you type.
                    </div> -->

                    <!-- Document Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="<?= base_url('/documents/store') ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-heading"></i>
                                            </span>
                                            <input type="text" name="title" class="form-control"
                                                value="<?= old('title') ?>" placeholder="Enter document title" required>
                                        </div>
                                        <div class="form-text">Document title (minimum 3 characters)</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Document Type <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-file"></i>
                                            </span>
                                            <select name="type_id" class="form-select" required>
                                                <option value="">Select document type</option>
                                                <?php foreach ($documentTypes as $type): ?>
                                                    <option value="<?= $type['id'] ?>" <?= old('type_id') == $type['id'] ? 'selected' : '' ?>>
                                                        <?= esc($type['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
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

                                <div class="mb-3" style="width: 800px; margin: auto;">
                                    <label class="form-label">Content</label>

                                    <!-- Font Size Control -->
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <label for="fontSizeSelect" class="mb-0"><i
                                                class="fas fa-text-height me-1"></i>Font Size:</label>
                                        <select id="fontSizeSelect" class="form-select form-select-sm"
                                            style="width: 120px;" onchange="applyFontSize(this.value)">
                                            <option value="">Select Size</option>
                                            <option value="8px">8px</option>
                                            <option value="10px">10px</option>
                                            <option value="12px">12px</option>
                                            <option value="14px">14px</option>
                                            <option value="16px">16px</option>
                                            <option value="18px">18px</option>
                                            <option value="20px">20px</option>
                                            <option value="22px">22px</option>
                                            <option value="24px">24px</option>
                                            <option value="28px">28px</option>
                                            <option value="32px">32px</option>
                                            <option value="36px">36px</option>
                                            <option value="48px">48px</option>
                                        </select>

                                        <label for="fontFamilySelect" class="mb-0 ms-3"><i
                                                class="fas fa-font me-1"></i>Font:</label>
                                        <select id="fontFamilySelect" class="form-select form-select-sm"
                                            style="width: 180px;" onchange="applyFontFamily(this.value)">
                                            <option value="">Select Font</option>
                                            <option value="Arial, sans-serif">Arial</option>
                                            <option value="Times New Roman, serif">Times New Roman</option>
                                            <option value="Courier New, monospace">Courier New</option>
                                            <option value="Georgia, serif">Georgia</option>
                                            <option value="Verdana, sans-serif">Verdana</option>
                                            <option value="Tahoma, sans-serif">Tahoma</option>
                                        </select>
                                    </div>

                                    <textarea name="content" id="editor" class="form-control"><?= old('content') ?>
                                    </textarea>
                                </div>

                                <!-- Document Metadata Section -->
                                <!-- <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-tags me-1"></i>Document Metadata</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="metadata-container">
                                            <div class="row mb-2">
                                                <div class="col-md-5">
                                                    <input type="text" name="metadata[key][]" class="form-control" placeholder="Metadata Key">
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" name="metadata[value][]" class="form-control" placeholder="Metadata Value">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-success btn-sm add-metadata" title="Add another metadata">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

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

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        let editorInstance;
        const AUTOSAVE_KEY = 'document_create_autosave';

        // Font size and family control functions
        function applyFontSize(size) {
            if (!size || !editorInstance) return;

            // Get current HTML content
            let content = editorInstance.getData();

            // Get selected text (if any) - we'll wrap the entire content for now
            // Or insert a span that user can type into
            const spanHtml = `<span style="font-size: ${size};">Type here or paste text</span>&nbsp;`;

            // Insert at cursor position
            editorInstance.model.change(writer => {
                const viewFragment = editorInstance.data.processor.toView(spanHtml);
                const modelFragment = editorInstance.data.toModel(viewFragment);
                editorInstance.model.insertContent(modelFragment);
            });

            // Reset select and focus editor
            document.getElementById('fontSizeSelect').value = '';
            editorInstance.editing.view.focus();
        }

        function applyFontFamily(font) {
            if (!font || !editorInstance) return;

            const spanHtml = `<span style="font-family: ${font};">Type here or paste text</span>&nbsp;`;

            // Insert at cursor position
            editorInstance.model.change(writer => {
                const viewFragment = editorInstance.data.processor.toView(spanHtml);
                const modelFragment = editorInstance.data.toModel(viewFragment);
                editorInstance.model.insertContent(modelFragment);
            });

            // Reset select and focus editor
            document.getElementById('fontFamilySelect').value = '';
            editorInstance.editing.view.focus();
        }

        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#editor'), {
                toolbar: {
                    items: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'fontSize', 'fontFamily', '|',
                        'alignment', '|',
                        'insertTable', 'imageUpload', 'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight', '|',
                        'undo', 'redo'
                    ]
                },
                fontSize: {
                    options: [8, 10, 12, 14, 'default', 18, 24, 36]
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                },
                image: {
                    toolbar: ['imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight', 'resizeImage']
                },
                simpleUpload: {
                    uploadUrl: '/documents/upload-image'
                }
            })
            .catch(error => console.error(error));

        // Save form data to localStorage
        function saveFormData() {
            const formData = {
                title: document.querySelector('input[name="title"]').value,
                type_id: document.querySelector('select[name="type_id"]').value,
                department_id: document.querySelector('select[name="department_id"]').value,
                status: document.querySelector('select[name="status"]').value,
                effective_date: document.querySelector('input[name="effective_date"]').value,
                review_date: document.querySelector('input[name="review_date"]').value,
                content: editorInstance ? editorInstance.getData() : '',
                metadata: getMetadataValues(),
                timestamp: new Date().toISOString()
            };

            localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(formData));

            // Show autosave indicator
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

            // Fade out after 2 seconds
            setTimeout(() => {
                indicator.style.opacity = '0';
            }, 2000);
        }

        // Load autosaved data
        function loadAutosave() {
            const saved = localStorage.getItem(AUTOSAVE_KEY);
            if (!saved) return;

            const formData = JSON.parse(saved);

            // Show persistent notification (not dismissable)
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
        window.restoreAutosave = function () {
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
                editorInstance.setData(formData.content);
            }

            // Restore metadata
            if (formData.metadata && formData.metadata.length > 0) {
                const container = document.getElementById('metadata-container');
                container.innerHTML = '';
                formData.metadata.forEach((meta, index) => {
                    const row = document.createElement('div');
                    row.className = 'row mb-2';
                    row.innerHTML = `
                        <div class="col-md-5">
                            <input type="text" name="metadata[key][]" class="form-control" placeholder="Metadata Key" value="${meta.key || ''}">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="metadata[value][]" class="form-control" placeholder="Metadata Value" value="${meta.value || ''}">
                        </div>
                        <div class="col-md-1">
                            <button type="button" class="btn ${index === 0 ? 'btn-success' : 'btn-danger'} btn-sm ${index === 0 ? 'add-metadata' : 'remove-metadata'}" title="${index === 0 ? 'Add another metadata' : 'Remove this metadata'}">
                                <i class="fas ${index === 0 ? 'fa-plus' : 'fa-times'}"></i>
                            </button>
                        </div>
                    `;
                    container.appendChild(row);
                });
            }

            const alert = document.querySelector('.alert-warning');
            if (alert) alert.remove();

            // Show success message
            const success = document.createElement('div');
            success.className = 'alert alert-success alert-dismissible fade show';
            success.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Autosaved data has been restored successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.main-content').insertBefore(success, document.querySelector('.card'));

            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                if (success.parentNode) {
                    success.remove();
                }
            }, 3000);
        };

        // Clear autosaved data
        window.clearAutosave = function () {
            localStorage.removeItem(AUTOSAVE_KEY);
            const alert = document.querySelector('.alert-warning');
            if (alert) alert.remove();

            // Show confirmation
            const confirmation = document.createElement('div');
            confirmation.className = 'alert alert-success alert-dismissible fade show';
            confirmation.innerHTML = `
                <i class="fas fa-check-circle me-2"></i>
                Autosaved data has been discarded.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.main-content').insertBefore(confirmation, document.querySelector('.card'));

            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                if (confirmation.parentNode) {
                    confirmation.remove();
                }
            }, 3000);
        };

        // Get metadata values
        function getMetadataValues() {
            const keys = document.querySelectorAll('input[name="metadata[key][]"]');
            const values = document.querySelectorAll('input[name="metadata[value][]"]');
            const metadata = [];

            keys.forEach((keyInput, index) => {
                if (keyInput.value || values[index].value) {
                    metadata.push({
                        key: keyInput.value,
                        value: values[index].value
                    });
                }
            });

            return metadata;
        }

        // Autosave on form field changes
        document.querySelector('form').addEventListener('input', function (e) {
            if (e.target.matches('input, select, textarea')) {
                saveFormData();
            }
        });

        // Clear autosave on successful form submission
        document.querySelector('form').addEventListener('submit', function () {
            localStorage.removeItem(AUTOSAVE_KEY);
        });

        // Add/remove metadata fields
        document.addEventListener('click', function (e) {
            // Add metadata row
            if (e.target.closest('.add-metadata')) {
                const container = document.getElementById('metadata-container');
                const newRow = document.createElement('div');
                newRow.className = 'row mb-2';
                newRow.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="metadata[key][]" class="form-control" placeholder="Metadata Key">
            </div>
            <div class="col-md-6">
                <input type="text" name="metadata[value][]" class="form-control" placeholder="Metadata Value">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger btn-sm remove-metadata" title="Remove this metadata">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
                container.appendChild(newRow);
            }

            // Remove metadata row
            if (e.target.closest('.remove-metadata')) {
                e.target.closest('.row').remove();
                saveFormData();
            }
        });
    </script>
</body>

</html>