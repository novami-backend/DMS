<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Role - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .permission-card {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .permission-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .permission-card.selected {
            border-color: #667eea;
            background-color: rgba(102, 126, 234, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="p-0">
                <?= view('common/sidebar') ?>
            </div>

            <!-- Main Content -->
            <div class="p-0">
                <div class="main-content">
                    <!-- Header -->
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-user-edit me-2"></i>Edit Role',
                        'pageDescription' => 'Update role information and permissions'
                    ]) ?>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('roles'); ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Roles
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

                    <!-- Role Form -->
                    <div class="card">
                        <div class="card-body">
                            <form method="post" action="<?= base_url('roles/update/' . $role['id']) ?>">
                                <div class="row">
                                    <div class="mb-4 col-4">
                                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-tag"></i>
                                            </span>
                                            <input type="text" name="role_name" class="form-control"
                                                value="<?= old('role_name', $role['role_name']) ?>"
                                                placeholder="Enter role name" required>
                                        </div>
                                        <div class="form-text">Must be unique and at least 2 characters long</div>
                                    </div>

                                    <div class="mb-4 col-8">
                                        <label class="form-label">Description</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-align-left"></i>
                                            </span>
                                            <textarea name="description" class="form-control"
                                                rows="1" placeholder="Enter role description"><?= old('description', $role['description']) ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Permissions</label>
                                    <!-- Global Select All + Action-level Select All -->
                                    <div class="d-flex gap-4 mb-3 align-items-center flex-wrap">
                                        <!-- Global Select All -->
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label" for="selectAll">
                                                <strong>Select All Permissions</strong>
                                            </label>
                                        </div>

                                        <!-- Action-level Select All -->
                                        <div class="form-check">
                                            <input class="form-check-input select-action" type="checkbox" data-action="read" id="selectAllRead">
                                            <label class="form-check-label" for="selectAllRead">All Read</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input select-action" type="checkbox" data-action="create" id="selectAllCreate">
                                            <label class="form-check-label" for="selectAllCreate">All Create</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input select-action" type="checkbox" data-action="update" id="selectAllUpdate">
                                            <label class="form-check-label" for="selectAllUpdate">All Update</label>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input select-action" type="checkbox" data-action="delete" id="selectAllDelete">
                                            <label class="form-check-label" for="selectAllDelete">All Delete</label>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <?php foreach ($permissions as $permission): ?>
                                            <?php if (!preg_match('/^(user_|role_|permission_|document_|document_type_|department_)/', $permission['permission_key'])): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card permission-card <?= in_array($permission['id'], $rolePermissionIds) ? 'selected' : '' ?>">
                                                        <div class="card-body py-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                                    name="permissions[]" value="<?= $permission['id'] ?>"
                                                                    id="perm_<?= $permission['id'] ?>"
                                                                    data-key="<?= $permission['permission_key'] ?>"
                                                                    <?= in_array($permission['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                                    <strong><?= esc($permission['permission_key']) ?></strong><br>
                                                                    <small class="text-muted"><?= esc($permission['description']) ?></small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- User Permissions -->
                                    <h6 class="mt-3 d-flex align-items-center justify-content-left">
                                        User Management
                                        <div class="form-check ms-3">
                                            <input class="form-check-input select-group" type="checkbox" data-group="user_" id="selectUserAll">
                                            <label class="form-check-label" for="selectUserAll">All</label>
                                        </div>
                                    </h6>
                                    <div class="row">
                                        <?php foreach ($permissions as $permission): ?>
                                            <?php if (strpos($permission['permission_key'], 'user_') === 0): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card permission-card <?= in_array($permission['id'], $rolePermissionIds) ? 'selected' : '' ?>">
                                                        <div class="card-body py-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                                    name="permissions[]" value="<?= $permission['id'] ?>"
                                                                    id="perm_<?= $permission['id'] ?>"
                                                                    data-key="<?= $permission['permission_key'] ?>"
                                                                    <?= in_array($permission['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                                    <strong><?= esc($permission['permission_key']) ?></strong><br>
                                                                    <small class="text-muted"><?= esc($permission['description']) ?></small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Role Permissions -->
                                    <h6 class="mt-3 d-flex align-items-center justify-content-left">
                                        Role Management
                                        <div class="form-check ms-3">
                                            <input class="form-check-input select-group" type="checkbox" data-group="role_" id="selectRoleAll">
                                            <label class="form-check-label" for="selectRoleAll">All</label>
                                        </div>
                                    </h6>
                                    <div class="row">
                                        <?php foreach ($permissions as $permission): ?>
                                            <?php if (strpos($permission['permission_key'], 'role_') === 0): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card permission-card <?= in_array($permission['id'], $rolePermissionIds) ? 'selected' : '' ?>">
                                                        <div class="card-body py-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                                    name="permissions[]" value="<?= $permission['id'] ?>"
                                                                    id="perm_<?= $permission['id'] ?>"
                                                                    data-key="<?= $permission['permission_key'] ?>"
                                                                    <?= in_array($permission['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                                    <strong><?= esc($permission['permission_key']) ?></strong><br>
                                                                    <small class="text-muted"><?= esc($permission['description']) ?></small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <h6 class="mt-3 d-flex align-items-center justify-content-left">
                                        Permission Management
                                        <div class="form-check ms-3">
                                            <input class="form-check-input select-group" type="checkbox" data-group="permission_" id="selectPermissionAll">
                                            <label class="form-check-label" for="selectPermissionAll">All</label>
                                        </div>
                                    </h6>
                                    <div class="row">
                                        <?php foreach ($permissions as $permission): ?>
                                            <?php if (strpos($permission['permission_key'], 'permission_') === 0): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card permission-card <?= in_array($permission['id'], $rolePermissionIds) ? 'selected' : '' ?>">
                                                        <div class="card-body py-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                                    name="permissions[]" value="<?= $permission['id'] ?>"
                                                                    id="perm_<?= $permission['id'] ?>"
                                                                    data-key="<?= $permission['permission_key'] ?>"
                                                                    <?= in_array($permission['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                                    <strong><?= esc($permission['permission_key']) ?></strong><br>
                                                                    <small class="text-muted"><?= esc($permission['description']) ?></small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Document Management -->
                                    <h6 class="mt-3 d-flex align-items-center justify-content-left">
                                        Document Management
                                        <div class="form-check ms-3">
                                            <input class="form-check-input select-group" type="checkbox" data-group="document_" id="selectDocumentAll">
                                            <label class="form-check-label" for="selectDocumentAll">All</label>
                                        </div>
                                    </h6>
                                    <div class="row">
                                        <?php foreach ($permissions as $permission): ?>
                                            <?php if (strpos($permission['permission_key'], 'document_') === 0): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card permission-card <?= in_array($permission['id'], $rolePermissionIds) ? 'selected' : '' ?>">
                                                        <div class="card-body py-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                                    name="permissions[]" value="<?= $permission['id'] ?>"
                                                                    id="perm_<?= $permission['id'] ?>"
                                                                    data-key="<?= $permission['permission_key'] ?>"
                                                                    <?= in_array($permission['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                                    <strong><?= esc($permission['permission_key']) ?></strong><br>
                                                                    <small class="text-muted"><?= esc($permission['description']) ?></small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- document type Management -->
                                    <!-- <h6 class="mt-3 d-flex align-items-center justify-content-left">
                                        Document Type Management
                                        <div class="form-check ms-3">
                                            <input class="form-check-input select-group" type="checkbox" data-group="document_type_" id="selectDocumentTypeAll">
                                            <label class="form-check-label" for="selectDocumentTypeAll">All</label>
                                        </div>
                                    </h6>
                                    <div class="row">
                                        <?php foreach ($permissions as $permission): ?>
                                            <?php if (strpos($permission['permission_key'], 'document_type_') === 0): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card permission-card <?= in_array($permission['id'], $rolePermissionIds) ? 'selected' : '' ?>">
                                                        <div class="card-body py-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                                    name="permissions[]" value="<?= $permission['id'] ?>"
                                                                    id="perm_<?= $permission['id'] ?>"
                                                                    data-key="<?= $permission['permission_key'] ?>"
                                                                    <?= in_array($permission['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                                    <strong><?= esc($permission['permission_key']) ?></strong><br>
                                                                    <small class="text-muted"><?= esc($permission['description']) ?></small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div> -->

                                    <!-- Department Management -->
                                    <h6 class="mt-3 d-flex align-items-center justify-content-left">
                                        Department Management
                                        <div class="form-check ms-3">
                                            <input class="form-check-input select-group" type="checkbox" data-group="department_" id="selectDepartmentAll">
                                            <label class="form-check-label" for="selectDepartmentAll">All</label>
                                        </div>
                                    </h6>
                                    <div class="row">
                                        <?php foreach ($permissions as $permission): ?>
                                            <?php if (strpos($permission['permission_key'], 'department_') === 0): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card permission-card <?= in_array($permission['id'], $rolePermissionIds) ? 'selected' : '' ?>">
                                                        <div class="card-body py-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input permission-checkbox" type="checkbox"
                                                                    name="permissions[]" value="<?= $permission['id'] ?>"
                                                                    id="perm_<?= $permission['id'] ?>"
                                                                    data-key="<?= $permission['permission_key'] ?>"
                                                                    <?= in_array($permission['id'], $rolePermissionIds) ? 'checked' : '' ?>>
                                                                <label class="form-check-label" for="perm_<?= $permission['id'] ?>">
                                                                    <strong><?= esc($permission['permission_key']) ?></strong><br>
                                                                    <small class="text-muted"><?= esc($permission['description']) ?></small>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('roles') ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary btn-submit">
                                        <i class="fas fa-save me-2"></i>Update Role
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
        // Initialize card highlighting for checked checkboxes
        document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
            const card = checkbox.closest('.permission-card');
            if (checkbox.checked) {
                card.classList.add('selected');
            }
            checkbox.addEventListener('change', function() {
                card.classList.toggle('selected', this.checked);
            });
        });

        // Global Select All
        const selectAll = document.getElementById('selectAll');
        selectAll.addEventListener('change', function() {
            // Toggle ALL types of checkboxes
            document.querySelectorAll('.permission-checkbox, .select-action, .select-group').forEach(cb => {
                cb.checked = this.checked;
                // For permission checkboxes, also update card highlighting
                if (cb.classList.contains('permission-checkbox')) {
                    const card = cb.closest('.permission-card');
                    card.classList.toggle('selected', this.checked);
                }
            });
        });

        // Group-level Select All
        document.querySelectorAll('.select-group').forEach(groupCheckbox => {
            groupCheckbox.addEventListener('change', function() {
                const prefix = this.dataset.group;
                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    if (cb.dataset.key && cb.dataset.key.startsWith(prefix)) {
                        cb.checked = this.checked;
                        const card = cb.closest('.permission-card');
                        card.classList.toggle('selected', this.checked);
                    }
                });
            });
        });

        // Action-level Select All
        document.querySelectorAll('.select-action').forEach(actionCheckbox => {
            actionCheckbox.addEventListener('change', function() {
                const action = this.dataset.action;
                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    if (cb.dataset.key && cb.dataset.key.endsWith('_' + action)) {
                        cb.checked = this.checked;
                        const card = cb.closest('.permission-card');
                        card.classList.toggle('selected', this.checked);
                    }
                });
            });
        });
    </script>
</body>

</html>