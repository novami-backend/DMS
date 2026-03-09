<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .status-badge {
            font-size: 0.75rem;
        }

        .status-draft {
            background-color: #ffc107;
            color: #000;
        }

        .status-active {
            background-color: #28a745;
            color: #fff;
        }

        .status-archived {
            background-color: #6c757d;
            color: #fff;
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
                        'pageTitle' => '<i class="fas fa-file-alt me-2"></i>Document Management',
                        'pageDescription' => 'Manage quality documents and procedures'
                    ]) ?>
                    <div class="d-flex justify-content-end mb-3">
                        <div>
                            <?php if ($can_create_documents): ?>
                                <a href="<?= base_url('documents/create') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create New Document
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Search Form -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="<?= base_url('documents') ?>" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Search Documents</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" name="q" class="form-control"
                                            value="<?= esc($filters['q'] ?? '') ?>"
                                            placeholder="Search by title or content...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Document Type</label>
                                    <select name="type_id" class="form-select">
                                        <option value="">All Types</option>
                                        <?php foreach ($documentTypes ?? [] as $type): ?>
                                            <option value="<?= $type['id'] ?>" <?= (isset($filters['type_id']) && $filters['type_id'] == $type['id']) ? 'selected' : '' ?>>
                                                <?= esc($type['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select">
                                        <option value="">All Departments</option>
                                        <?php foreach ($departments ?? [] as $dept): ?>
                                            <option value="<?= $dept['id'] ?>" <?= (isset($filters['department_id']) && $filters['department_id'] == $dept['id']) ? 'selected' : '' ?>>
                                                <?= esc($dept['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Review Status</label>
                                    <select name="review_status" class="form-select">
                                        <option value="">All</option>
                                        <option value="pending" <?= (isset($filters['review_status']) && $filters['review_status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                        <option value="approved" <?= (isset($filters['review_status']) && $filters['review_status'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                                        <option value="rejected" <?= (isset($filters['review_status']) && $filters['review_status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Search/Reset</label>
                                    <div class="btn-group w-100" role="group">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        <a href="<?= base_url('documents') ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
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

                    <!-- Documents Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Type</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Created By</th>
                                            <th>Effective Date</th>
                                            <th>Review Date</th>
                                            <th>Created At</th>
                                            <?php if ($can_edit_documents || $can_delete_documents): ?>
                                                <th>Actions</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($documents as $document): ?>
                                            <tr>
                                                <td><?= $document['id'] ?></td>
                                                <td><strong><?= esc($document['title']) ?></strong></td>
                                                <td><span class=""><?= esc($document['document_number']) ?></span></td>
                                                <td><span class="badge bg-secondary"><?= esc($document['department_name']) ?></span></td>
                                                <td><span class="badge status-<?= $document['status'] ?> status-badge"><?= ucfirst($document['status']) ?></span></td>
                                                <td><?= esc($document['created_by_name']) ?></td>
                                                <td><?= $document['effective_date'] ? date('d-m-Y', strtotime($document['effective_date'])) : '-' ?></td>
                                                <td><?= $document['review_date'] ? date('d-m-Y', strtotime($document['review_date'])) : '-' ?></td>
                                                <td><?= date('d-m-Y', strtotime($document['created_at'])) ?></td>
                                                <?php /*if ($can_edit_documents || $can_delete_documents): */ ?>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <?php /*if ($can_edit_documents): */ ?>
                                                        <a href="<?= base_url('documents/edit/' . $document['id']) ?>" class="btn btn-outline-primary btn-action" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="<?= base_url('documents/view/' . $document['id']) ?>" class="btn btn-outline-info btn-action" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <!-- <a href="<?= base_url('documents/history/' . $document['id']) ?>" class="btn btn-outline-info btn-action" title="Version History">
                                                                    <i class="fas fa-history"></i>
                                                                </a> -->
                                                        <!-- <a href="<?= base_url('documents/share/' . $document['id']) ?>" class="btn btn-outline-success btn-action" title="Share">
                                                            <i class="fas fa-share-alt"></i>
                                                        </a> -->
                                                        <!-- <a href="<?= base_url('documents/workflow/' . $document['id']) ?>" class="btn btn-outline-warning btn-action" title="Workflow">
                                                                    <i class="fas fa-tasks"></i>
                                                                </a>
                                                                <a href="<?= base_url('documents/backup/' . $document['id']) ?>" class="btn btn-outline-secondary btn-action" title="Backup">
                                                                    <i class="fas fa-save"></i>
                                                                </a> -->
                                                        <?php /*endif; */ ?>
                                                        <?php if ($can_delete_documents): ?>
                                                            <a href="#" class="btn btn-outline-danger btn-action"
                                                                onclick="confirmDelete(<?= $document['id'] ?>, '<?= esc($document['title']) ?>')" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php /*if (userHasPermission('document_final_approval') && $document['status'] === 'approval_completed'): */ ?>
                                                        <a href="<?= base_url('documents/finalApprove/' . $document['id']) ?>"
                                                            class="btn btn-outline-success btn-action" title="Final Approve"><i class="fas fa-check-circle"></i></a>
                                                        <?php /* endif; */ ?>
                                                    </div>
                                                </td>
                                                <?php /*endif;*/ ?>
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
                    <p>Are you sure you want to delete document <strong id="deleteDocumentTitle"></strong>?</p>
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
        function confirmDelete(documentId, documentTitle) {
            document.getElementById('deleteDocumentTitle').textContent = documentTitle;
            document.getElementById('confirmDeleteBtn').href = '<?= base_url('documents/delete/') ?>' + documentId;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>

</html>