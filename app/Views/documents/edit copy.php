<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Document - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/x0p140w6y1cg1wqvis4ntlnj86m9u0o093a8i98q033d2pjd/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
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
                        <a href="<?= base_url('/documents') ?>" class="btn btn-secondary">
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
                            <form method="post" action="<?= base_url('/documents/update/' . $document['id']) ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-heading"></i>
                                            </span>
                                            <input type="text" name="title" class="form-control"
                                                value="<?= old('title', $document['title']) ?>" placeholder="Enter document title" required>
                                        </div>
                                        <div class="form-text">Document title (minimum 3 characters)</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-file"></i>
                                            </span>
                                            <select name="type_id" class="form-select" required>
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
                                    <label class="form-label">Content</label>
                                    <textarea name="content" id="editor" class="form-control"><?= old('content', $document['content']) ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('/documents') ?>" class="btn btn-secondary">Cancel</a>
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
        const AUTOSAVE_KEY = 'document_edit_autosave_<?= $document['id'] ?>';

        // Initialize TinyMCE
        tinymce.init({
            selector: '#editor',
            height: 500,
            menubar: true,
            plugins: [
                'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 
                'media', 'searchreplace', 'table', 'visualblocks', 'wordcount', 'code', 
                'fullscreen', 'insertdatetime', 'preview', 'help'
            ],
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | ' +
                'alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | ' +
                'forecolor backcolor | link image media table | code fullscreen | removeformat help',
            font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 28pt 32pt 36pt 48pt 72pt',
            font_family_formats: 'Arial=arial,helvetica,sans-serif; ' +
                'Times New Roman=times new roman,times,serif; ' +
                'Courier New=courier new,courier,monospace; ' +
                'Georgia=georgia,serif; ' +
                'Verdana=verdana,sans-serif; ' +
                'Tahoma=tahoma,sans-serif; ' +
                'Comic Sans MS=comic sans ms,cursive; ' +
                'Impact=impact,sans-serif',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14pt; }',
            images_upload_handler: function (blobInfo, success, failure) {
                let formData = new FormData();
                formData.append('upload', blobInfo.blob(), blobInfo.filename());

                fetch('/documents/upload-image', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.location) {
                        success(result.location);
                    } else {
                        failure(result.error || 'Upload failed');
                    }
                })
                .catch(() => failure('Upload failed'));
            },
            setup: function(editor) {
                editor.on('init', function() {
                    editorInstance = editor;
                    loadAutosave();
                });
                
                editor.on('change keyup', function() {
                    saveFormData();
                });
            }
        });

        // Save form data to localStorage
        // function saveFormData() {
        //     if (!editorInstance) return;
            
        //     const formData = {
        //         title: document.querySelector('input[name="title"]').value,
        //         type_id: document.querySelector('select[name="type_id"]').value,
        //         department_id: document.querySelector('select[name="department_id"]').value,
        //         status: document.querySelector('select[name="status"]').value,
        //         effective_date: document.querySelector('input[name="effective_date"]').value,
        //         review_date: document.querySelector('input[name="review_date"]').value,
        //         content: editorInstance.getContent(),
        //         timestamp: new Date().toISOString()
        //     };

        //     localStorage.setItem(AUTOSAVE_KEY, JSON.stringify(formData));
        //     showAutosaveIndicator();
        // }

        // Show autosave indicator
        // function showAutosaveIndicator() {
        //     let indicator = document.getElementById('autosave-indicator');

        //     if (!indicator) {
        //         indicator = document.createElement('div');
        //         indicator.id = 'autosave-indicator';
        //         indicator.style.position = 'fixed';
        //         indicator.style.bottom = '20px';
        //         indicator.style.right = '20px';
        //         indicator.style.padding = '10px 15px';
        //         indicator.style.backgroundColor = '#28a745';
        //         indicator.style.color = 'white';
        //         indicator.style.borderRadius = '5px';
        //         indicator.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        //         indicator.style.zIndex = '9999';
        //         indicator.style.transition = 'opacity 0.3s';
        //         indicator.innerHTML = '<i class="fas fa-check-circle me-2"></i>Autosaved';
        //         document.body.appendChild(indicator);
        //     }

        //     indicator.style.opacity = '1';
        //     setTimeout(() => {
        //         indicator.style.opacity = '0';
        //     }, 2000);
        // }

        // Load autosaved data
        // function loadAutosave() {
        //     const saved = localStorage.getItem(AUTOSAVE_KEY);
        //     if (!saved) return;

        //     const formData = JSON.parse(saved);

        //     const notification = document.createElement('div');
        //     notification.className = 'alert alert-warning fade show';
        //     notification.style.position = 'fixed';
        //     notification.style.top = '20px';
        //     notification.style.left = '50%';
        //     notification.style.transform = 'translateX(-50%)';
        //     notification.style.zIndex = '9999';
        //     notification.style.minWidth = '500px';
        //     notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
        //     notification.innerHTML = `
        //         <i class="fas fa-exclamation-triangle me-2"></i>
        //         <strong>Autosaved data found!</strong> Last saved: ${new Date(formData.timestamp).toLocaleString()}
        //         <div class="mt-2">
        //             <button type="button" class="btn btn-sm btn-success" onclick="restoreAutosave()">
        //                 <i class="fas fa-undo me-1"></i>Restore
        //             </button>
        //             <button type="button" class="btn btn-sm btn-danger ms-2" onclick="clearAutosave()">
        //                 <i class="fas fa-trash me-1"></i>Discard
        //             </button>
        //         </div>
        //     `;
        //     document.body.appendChild(notification);
        // }

        // Restore autosaved data
        // window.restoreAutosave = function () {
        //     const saved = localStorage.getItem(AUTOSAVE_KEY);
        //     if (!saved) return;

        //     const formData = JSON.parse(saved);

        //     document.querySelector('input[name="title"]').value = formData.title || '';
        //     document.querySelector('select[name="type_id"]').value = formData.type_id || '';
        //     document.querySelector('select[name="department_id"]').value = formData.department_id || '';
        //     document.querySelector('select[name="status"]').value = formData.status || 'draft';
        //     document.querySelector('input[name="effective_date"]').value = formData.effective_date || '';
        //     document.querySelector('input[name="review_date"]').value = formData.review_date || '';

        //     if (editorInstance && formData.content) {
        //         editorInstance.setContent(formData.content);
        //     }

        //     const alert = document.querySelector('.alert-warning');
        //     if (alert) alert.remove();

        //     const success = document.createElement('div');
        //     success.className = 'alert alert-success alert-dismissible fade show';
        //     success.innerHTML = `
        //         <i class="fas fa-check-circle me-2"></i>
        //         Autosaved data has been restored successfully!
        //         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        //     `;
        //     document.querySelector('.main-content').insertBefore(success, document.querySelector('.card'));

        //     setTimeout(() => {
        //         if (success.parentNode) success.remove();
        //     }, 3000);
        // };

        // Clear autosaved data
        // window.clearAutosave = function () {
        //     localStorage.removeItem(AUTOSAVE_KEY);
        //     const alert = document.querySelector('.alert-warning');
        //     if (alert) alert.remove();

        //     const confirmation = document.createElement('div');
        //     confirmation.className = 'alert alert-success alert-dismissible fade show';
        //     confirmation.innerHTML = `
        //         <i class="fas fa-check-circle me-2"></i>
        //         Autosaved data has been discarded.
        //         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        //     `;
        //     document.querySelector('.main-content').insertBefore(confirmation, document.querySelector('.card'));

        //     setTimeout(() => {
        //         if (confirmation.parentNode) confirmation.remove();
        //     }, 3000);
        // };

        // Autosave on form field changes
        // document.querySelector('form').addEventListener('input', function (e) {
        //     if (e.target.matches('input, select')) {
        //         saveFormData();
        //     }
        // });

        // Clear autosave on successful form submission
        document.querySelector('form').addEventListener('submit', function () {
            localStorage.removeItem(AUTOSAVE_KEY);
        });
    </script>
</body>

</html>
