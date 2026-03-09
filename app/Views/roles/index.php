<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles - DMS</title>
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
                        'pageTitle' => '<i class="fas fa-user-tag me-2"></i>Role Management',
                        'pageDescription' => 'Manage user roles and permissions'
                    ]) ?>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('roles/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New Role
                        </a>
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

                    <!-- Roles Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Role Name</th>
                                            <th>Description</th>
                                            <th>Permissions</th>
                                            <th>Users</th>
                                            <th width="100">Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($roles as $role): ?>
                                        <tr>
                                            <td><?= $role['id'] ?></td>
                                            <td>
                                                <strong><?= esc($role['role_name']) ?></strong>
                                            </td>
                                            <td><?= esc($role['description'] ?? 'No description') ?></td>
                                            <td>
                                                <?php if (!empty($role['permissions'])): ?>
                                                    <?php foreach ($role['permissions'] as $permission): ?>
                                                        <span class="badge bg-primary permission-badge">
                                                            <?= esc($permission['permission_key']) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No permissions</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $userCount = isset($role['user_count']) ? $role['user_count'] : 0;
                                                echo $userCount > 0 ? $userCount : '0';
                                                ?>
                                            </td>
                                            <!-- <td><?= date('M j, Y', strtotime($role['created_at'])) ?></td> -->
                                             <td><?= date('d-m-Y', strtotime($role['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('roles/edit/' . $role['id']) ?>" class="btn btn-sm btn-outline-primary btn-action">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-danger btn-action" 
                                                   onclick="confirmDelete(<?= $role['id'] ?>, '<?= esc($role['role_name']) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
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
                    <p>Are you sure you want to delete role <strong id="deleteRoleName"></strong>?</p>
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
        function confirmDelete(roleId, roleName) {
            document.getElementById('deleteRoleName').textContent = roleName;
            document.getElementById('confirmDeleteBtn').href = '<?= base_url('roles/delete/') ?>' + roleId;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>