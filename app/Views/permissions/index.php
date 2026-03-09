<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permissions - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .permission-badge {
            font-size: 0.75rem;
            margin: 0.1rem;
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
                        'pageTitle' => '<i class="fas fa-key me-2"></i>Permission Management',
                        'pageDescription' => 'Manage system permissions and access rights'
                    ]) ?>
                    <div class="d-flex justify-content-end mb-3">
                        <?php /*if ($can_create_permissions):*/ ?>
                        <a href="<?= base_url('permissions/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Permission
                        </a>
                        <?php /*endif;*/ ?>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Permissions Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Permission Key</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                            <th>Updated At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($permissions as $permission): ?>
                                        <tr>
                                            <td><?= $permission['id'] ?></td>
                                            <td><strong><?= esc($permission['permission_key']) ?></strong></td>
                                            <td><?= esc($permission['description'] ?? 'No description') ?></td>
                                            <td><?= date('d-m-Y', strtotime($permission['created_at'])) ?></td>
                                            <td><?= date('d-m-Y', strtotime($permission['updated_at'])) ?></td>
                                            <td>
                                                <?php /*if ($can_edit_permissions):*/ ?>
                                                <a href="<?= base_url('permissions/edit/' . $permission['id']) ?>" class="btn btn-sm btn-outline-primary btn-action">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php /*endif;*/ ?>
                                                <?php /*if ($can_delete_permissions):*/ ?>
                                                <a href="#" class="btn btn-sm btn-outline-danger btn-action" 
                                                   onclick="confirmDelete(<?= $permission['id'] ?>, '<?= esc($permission['permission_key']) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php /*endif;*/ ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete permission <strong id="deletePermissionName"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        function confirmDelete(permissionId, permissionName) {
            document.getElementById('deletePermissionName').textContent = permissionName;
            document.getElementById('confirmDeleteBtn').href = '<?= base_url('permissions/delete/') ?>' + permissionId;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>