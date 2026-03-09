<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Permission - DMS</title>
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
                        'pageTitle' => '<i class="fas fa-key me-2"></i>Create New Permission',
                        'pageDescription' => 'Add a new permission to the system'
                    ]) ?>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('permissions'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Permissions
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

                    <!-- Permission Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="<?= base_url('/permissions/store') ?>">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Permission Key <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <input type="text" name="permission_key" class="form-control"
                                                value="<?= old('permission_key') ?>" required>
                                        </div>
                                        <div class="form-text">A unique identifier for this permission (e.g., create_users, edit_roles)</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Description</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-info-circle"></i>
                                            </span>
                                            <input type="text" name="description" class="form-control"
                                                value="<?= old('description') ?>">
                                        </div>
                                        <div class="form-text">Brief description of what this permission allows</div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('permissions'); ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-submit">
                                        <i class="fas fa-save me-2"></i>Create Permission
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