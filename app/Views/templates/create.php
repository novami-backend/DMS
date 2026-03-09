<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Template - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
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
                        'pageTitle' => '<i class="fas fa-file-code me-2"></i>' . $pageTitle,
                        'pageDescription' => $pageDescription
                    ]) ?>

                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('templates') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Templates
                        </a>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Please correct the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Template Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="<?= base_url('templates/store') ?>">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Template Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control"
                                                value="<?= old('name') ?>" required>
                                            <div class="form-text">e.g., "Standard System Procedure"</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Document Type <span class="text-danger">*</span></label>
                                            <select name="document_type_id" class="form-select" required>
                                                <option value="">Select document type</option>
                                                <?php foreach ($documentTypes as $type): ?>
                                                    <option value="<?= $type['id'] ?>"
                                                        <?= old('document_type_id') == $type['id'] ? 'selected' : '' ?>>
                                                        <?= esc($type['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Template Code <span class="text-danger">*</span></label>
                                            <input type="text" name="code" class="form-control"
                                                value="<?= old('code') ?>" required>
                                            <div class="form-text">e.g., "SSP/MR/001/001" (must be unique)</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <input type="text" name="description" class="form-control"
                                                value="<?= old('description') ?>">
                                        </div>

                                        <!-- <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                                    value="1" <?= old('is_active', '1') ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="is_active">Active</label>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>

                                <!-- PDF Layout Template -->
                                <div class="mb-3">
                                    <label class="form-label">PDF Layout Template</label>
                                    <textarea name="layout_template" class="form-control" rows="3"><?= old('layout_template') ?></textarea>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('templates') ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Create Template
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
</body>

</html>