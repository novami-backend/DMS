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

                    <!-- Documents Hierarchical View -->
                    <?php if (empty($groupedDocuments)): ?>
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5>No documents found</h5>
                                <p class="text-muted">Try adjusting your filters or search query.</p>
                                <a href="<?= base_url('documents') ?>" class="btn btn-outline-primary">Clear All Filters</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="documentAccordion">
                            <?php 
                            $first = true;
                            foreach ($groupedDocuments as $typeId => $typeData): 
                            ?>
                                <div class="accordion-item mb-4 border-0 shadow-sm rounded">
                                    <h2 class="accordion-header" id="headingType<?= $typeId ?>">
                                        <button class="accordion-button bg-light fw-bold py-3 <?= $first ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseType<?= $typeId ?>" aria-expanded="<?= $first ? 'true' : 'false' ?>">
                                            <i class="fas fa-folder-open me-2 text-primary"></i>
                                            <span class="text-dark"><?= esc($typeData['name']) ?></span>
                                            <span class="badge bg-primary ms-3 rounded-pill">
                                                <?php 
                                                    $count = 0;
                                                    foreach($typeData['departments'] as $d) $count += count($d['documents']);
                                                    echo $count;
                                                ?> Documents
                                            </span>
                                        </button>
                                    </h2>
                                    <div id="collapseType<?= $typeId ?>" class="accordion-collapse collapse <?= $first ? 'show' : '' ?>" aria-labelledby="headingType<?= $typeId ?>" data-bs-parent="#documentAccordion">
                                        <div class="accordion-body p-4">
                                            <?php 
                                            foreach ($typeData['departments'] as $deptId => $deptData): 
                                            ?>
                                                <div class="card mb-4 border-start border-4 border-info shadow-sm">
                                                    <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
                                                        <h6 class="mb-0 text-info fw-bold">
                                                            <i class="fas fa-building me-2"></i><?= esc($deptData['name']) ?>
                                                        </h6>
                                                        <span class="badge bg-info text-white rounded-pill"><?= count($deptData['documents']) ?> Documents</span>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover mb-0 align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width: 80px;">ID</th>
                                                                        <th>Document Number & Title</th>
                                                                        <th>Status</th>
                                                                        <th>Prepared By</th>
                                                                        <th>Dates</th>
                                                                        <th class="text-end">Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php foreach ($deptData['documents'] as $document): ?>
                                                                        <tr>
                                                                            <td><small class="text-muted">#<?= $document['id'] ?></small></td>
                                                                            <td>
                                                                                <div class="d-flex flex-column">
                                                                                    <span class="badge bg-light text-dark border mb-1 w-fit-content" style="width: fit-content;"><?= esc($document['document_number']) ?></span>
                                                                                    <span class="fw-bold"><?= esc($document['title']) ?></span>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge status-<?= $document['status'] ?> status-badge"><?= ucfirst($document['status']) ?></span>
                                                                            </td>
                                                                            <td>
                                                                                <small><?= esc($document['created_by_name']) ?></small>
                                                                            </td>
                                                                            <td>
                                                                                <div class="d-flex flex-column">
                                                                                    <small class="text-muted" title="Created At">
                                                                                        <i class="far fa-clock me-1"></i><?= date('d-m-Y', strtotime($document['created_at'])) ?>
                                                                                    </small>
                                                                                    <?php if($document['effective_date']): ?>
                                                                                        <small class="text-success" title="Effective Date">
                                                                                            <i class="far fa-calendar-check me-1"></i><?= date('d-m-Y', strtotime($document['effective_date'])) ?>
                                                                                        </small>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text-end">
                                                                                <div class="btn-group btn-group-sm" role="group">
                                                                                    <a href="<?= base_url('documents/view/' . $document['id']) ?>" class="btn btn-outline-info" title="View">
                                                                                        <i class="fas fa-eye"></i>
                                                                                    </a>
                                                                                    <?php if ($can_edit_documents): ?>
                                                                                        <a href="<?= base_url('documents/edit/' . $document['id']) ?>" class="btn btn-outline-primary" title="Edit">
                                                                                            <i class="fas fa-edit"></i>
                                                                                        </a>
                                                                                    <?php endif; ?>
                                                                                    <?php if ($can_delete_documents): ?>
                                                                                        <button type="button" class="btn btn-outline-danger" 
                                                                                            onclick="confirmDelete(<?= $document['id'] ?>, '<?= esc($document['title']) ?>')" title="Delete">
                                                                                            <i class="fas fa-trash"></i>
                                                                                        </button>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                $first = false;
                                endforeach; 
                            ?>
                        </div>
                    <?php endif; ?>
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