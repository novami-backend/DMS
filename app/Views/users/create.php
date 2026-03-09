<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - DMS</title>
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
                        'pageTitle' => '<i class="fas fa-user-plus me-2"></i>Create New User',
                        'pageDescription' => 'Add a new user to the system'
                    ]) ?>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('users'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Users
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

                    <!-- User Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="<?= base_url('/users/store') ?>" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-id-card"></i>
                                            </span>
                                            <input type="text" name="name" class="form-control"
                                                value="<?= old('name') ?>" required>
                                        </div>
                                        <div class="form-text">Full name of the user</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <input type="text" name="username" class="form-control"
                                                value="<?= old('username') ?>" required>
                                        </div>
                                        <div class="form-text">Must be at least 3 characters long</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" name="email" class="form-control"
                                                value="<?= old('email') ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Signature (Initials)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-signature"></i>
                                            </span>
                                            <input type="text" name="sign" class="form-control"
                                                value="<?= old('sign') ?>"
                                                placeholder="Enter initials (e.g. AB)">
                                        </div>
                                    </div>

                                    <!-- <div class="col-md-6 mb-3">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" name="password" class="form-control" required>
                                        </div>
                                        <div class="form-text">Must be at least 6 characters long</div>
                                    </div> -->
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Role <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-user-tag"></i>
                                            </span>
                                            <select name="role_id" class="form-select" required>
                                                <option value="">Select a role</option>
                                                <?php foreach ($roles as $role): ?>
                                                    <option value="<?= $role['id'] ?>" <?= old('role_id') == $role['id'] ? 'selected' : '' ?>>
                                                        <?= esc($role['role_name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Department <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-building"></i>
                                            </span>
                                            <select name="department_id" class="form-select" required>
                                                <option value="">Select a department</option>
                                                <?php foreach ($departments as $department): ?>
                                                    <option value="<?= $department['id'] ?>" <?= old('department_id') == $department['id'] ? 'selected' : '' ?>>
                                                        <?= esc($department['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">


                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-toggle-on"></i>
                                            </span>
                                            <select name="status" class="form-select">
                                                <option value="active" <?= old('status') == 'active' ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                <option value="suspended" <?= old('status') == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('/users') ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-submit">
                                        <i class="fas fa-save me-2"></i>Create User
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