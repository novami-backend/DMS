<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
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
            <div class="col-lg-10 col-md-9 p-0">
                <div class="main-content">
                    <!-- Header -->
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-search me-2"></i>' . $page_title,
                        'pageDescription' => 'Review assigned documents'
                    ]) ?>

                    <div class="p-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">
                                    <i class="fas fa-list text-primary me-2"></i> Document List
                                </h3>
                                <div class="card-tools">
                                    <span class="badge bg-info"><?= count($documents) ?> Documents</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (empty($documents)): ?>
                                    <div class="alert alert-info text-center py-5">
                                        <i class="fas fa-info-circle fa-3x mb-3 text-info"></i>
                                        <h5>No Documents Assigned for Review</h5>
                                        <p class="text-muted">You currently have no documents assigned for review. Check back later or contact your administrator.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle" id="reviewsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Document</th>
                                                    <th>Type</th>
                                                    <th>Department</th>
                                                    <th>Created By</th>
                                                    <th>Submitted Date</th>
                                                    <th>Status</th>
                                                    <th class="text-end">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($documents as $doc): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="fw-bold"><?= esc($doc['title']) ?></div>
                                                            <small class="text-muted">ID: #<?= $doc['id'] ?></small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-secondary"><?= esc($doc['type_name']) ?></span>
                                                        </td>
                                                        <td><?= esc($doc['department_name']) ?></td>
                                                        <td><?= esc($doc['created_by_name']) ?></td>
                                                        <td>
                                                            <?php if ($doc['submitted_for_review_at']): ?>
                                                                <div><?= date('M j, Y', strtotime($doc['submitted_for_review_at'])) ?></div>
                                                                <small class="text-muted"><?= date('g:i A', strtotime($doc['submitted_for_review_at'])) ?></small>
                                                            <?php else: ?>
                                                                <span class="text-muted small">Not submitted</span>
                                                            <?php endif ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $statusColors = [
                                                                'pending' => 'warning',
                                                                'sent_for_review' => 'info',
                                                                'reviewed' => 'primary',
                                                                'approved' => 'success',
                                                                'rejected' => 'danger'
                                                            ];
                                                            $statusColor = $statusColors[$doc['approval_status']] ?? 'secondary';
                                                            ?>
                                                            <span class="badge bg-<?= $statusColor ?> text-uppercase">
                                                                <?= str_replace('_', ' ', $doc['approval_status']) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="btn-group">
                                                                <a href="<?= base_url('documents/view/' . $doc['id']) ?>" 
                                                                   class="btn btn-sm btn-outline-info" title="View Document">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <?php if ($doc['approval_status'] === 'sent_for_review'): ?>
                                                                    <a href="<?= base_url('documents/review/' . $doc['id']) ?>" 
                                                                       class="btn btn-sm btn-outline-primary" title="Review Document">
                                                                        <i class="fas fa-search"></i>
                                                                    </a>
                                                                <?php endif ?>
                                                                <a href="<?= base_url('documents/approval-history/' . $doc['id']) ?>" 
                                                                   class="btn btn-sm btn-outline-secondary" title="View History">
                                                                    <i class="fas fa-history"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?= view('common/footer') ?>
            </div>
        </div>
    </div>

    <?= view('common/scripts') ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#reviewsTable').DataTable({
                "pageLength": 25,
                "order": [[4, "desc"]], // Sort by submitted date
                "columnDefs": [
                    { "orderable": false, "targets": -1 } // Disable sorting on Actions column
                ]
            });
        });
    </script>
</body>

</html>