<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Templates - DMS</title>
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

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex justify-content-end mb-3">
                        <a href="<?= base_url('templates/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Template
                        </a>
                    </div>

                    <!-- Templates Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Document Type</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($templates)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                                    No templates found. Create your first template!
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($templates as $template): ?>
                                                <tr>
                                                    <td><?= $template['id'] ?></td>
                                                    <td>
                                                        <strong><?= esc($template['name']) ?></strong>
                                                        <?php if ($template['description']): ?>
                                                            <br><small class="text-muted"><?= esc($template['description']) ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><code><?= esc($template['code']) ?></code></td>
                                                    <td><?= esc($template['type_name']) ?></td>
                                                    <td>
                                                        <?php if ($template['is_active']): ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('M j, Y', strtotime($template['created_at'])) ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="<?= base_url(relativePath: 'templates/view/' . $template['id']) ?>" class="btn btn-outline-primary" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="<?= base_url('templates/edit/' . $template['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <button onclick="confirmDelete(<?= $template['id'] ?>)" class="btn btn-outline-danger" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    
    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this template? This will also delete all associated fields.')) {
                window.location.href = '<?= base_url('templates/delete') ?>/' + id;
            }
        }
    </script>
</body>
</html>
