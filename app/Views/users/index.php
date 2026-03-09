<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-suspended {
            background-color: #fff3cd;
            color: #856404;
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
                        'pageTitle' => '<i class="fas fa-users me-2"></i>User Management',
                        'pageDescription' => 'Manage system users and their roles'
                    ]) ?>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add New User
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

                    <!-- Users Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="usersTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= $user['id'] ?></td>
                                                <td><?= esc($user['name']) ?></td>
                                                <td><?= esc($user['username']) ?></td>
                                                <td><?= esc($user['email']) ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?= esc($user['role_name']) ?></span>
                                                </td>
                                                <td>
                                                    <span class=""><?= esc($user['department_name']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= ucfirst($user['status']) === 'Active' ? 'Enable' : 'Disable' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                                <td>
                                                    <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary btn-action">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <!-- <a href="<?= base_url('activity-logs/user/' . $user['id']) ?>" class="btn btn-sm btn-outline-info btn-action">
                                                        <i class="fas fa-history"></i>
                                                    </a> -->
                                                    <a href="#" class="btn btn-sm btn-outline-danger btn-action" onclick="confirmDelete(<?= $user['id'] ?>, '<?= esc($user['username']) ?>')">
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
                    <p>Are you sure you want to delete user <strong id="deleteUserName"></strong>?</p>
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
        function confirmDelete(userId, username) {
            document.getElementById('deleteUserName').textContent = username;
            document.getElementById('confirmDeleteBtn').href = '<?= base_url('users/delete/') ?>' + userId;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>

</html>