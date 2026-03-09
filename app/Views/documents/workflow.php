<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Workflows - <?= esc($document['title']) ?> - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .workflow-card {
            transition: all 0.3s;
            border-left: 4px solid #ffc107;
        }

        .workflow-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .status-pending {
            border-left-color: #ffc107;
            background-color: rgba(255, 193, 7, 0.05);
        }

        .status-in_progress {
            border-left-color: #17a2b8;
            background-color: rgba(23, 162, 184, 0.05);
        }

        .status-completed {
            border-left-color: #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }

        .status-rejected {
            border-left-color: #dc3545;
            background-color: rgba(220, 53, 69, 0.05);
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #007bff;
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
                        'pageTitle' => '<i class="fas fa-tasks me-2"></i>Document Workflows: ' . esc($document['title']),
                        'pageDescription' => 'Manage document review, approval, and publishing workflows'
                    ]) ?>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <div>
                            <a href="<?= base_url('documents') ?>" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-2"></i>Back to Documents
                            </a>
                            <a href="<?= base_url('documents/edit/' . $document['id']) ?>" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Edit Document
                            </a>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWorkflowModal">
                            <i class="fas fa-plus me-2"></i>Create Workflow
                        </button>
                    </div>

                    <!-- Document Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5>Document Information</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Title:</strong></td>
                                            <td><?= esc($document['title']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td><span class="badge bg-info"><?= esc($document['type_name']) ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Department:</strong></td>
                                            <td><span class="badge bg-secondary"><?= esc($document['department_name']) ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <h5>Workflow Statistics</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded mb-2">
                                                <h4 class="mb-0"><?= count($workflows) ?></h4>
                                                <small class="text-muted">Total Workflows</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-2 bg-light rounded mb-2">
                                                <h4 class="mb-0">
                                                    <?= count(array_filter($workflows, function ($w) {
                                                        return $w['current_status'] === 'pending' || $w['current_status'] === 'in_progress';
                                                    })) ?>
                                                </h4>
                                                <small class="text-muted">Active</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Workflows -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-list-check me-2"></i>Active Workflows
                                        </h5>
                                        <span class="badge bg-dark text-white">
                                            <?= count(array_filter($workflows, function ($w) {
                                                return $w['current_status'] === 'pending' || $w['current_status'] === 'in_progress';
                                            })) ?> active
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($workflows)): ?>
                                        <div class="text-center py-5">
                                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                            <h4>No workflows found</h4>
                                            <p class="text-muted">No workflows have been created for this document</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWorkflowModal">
                                                <i class="fas fa-plus me-2"></i>Create First Workflow
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="timeline">
                                            <?php foreach ($workflows as $workflow): ?>
                                                <div class="timeline-item">
                                                    <div class="card workflow-card status-<?= $workflow['current_status'] ?>">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <h6 class="card-title mb-1">
                                                                        <span class="badge bg-<?= $workflow['workflow_type'] === 'review' ? 'info' : ($workflow['workflow_type'] === 'approval' ? 'warning' : 'success') ?> me-2">
                                                                            <?= ucfirst($workflow['workflow_type']) ?>
                                                                        </span>
                                                                        <?= esc($workflow['workflow_type']) ?> Workflow
                                                                    </h6>
                                                                    <div class="mb-2">
                                                                        <span class="badge status-<?= $workflow['current_status'] ?> status-badge">
                                                                            <?= ucfirst(str_replace('_', ' ', $workflow['current_status'])) ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="text-end">
                                                                    <small class="text-muted">
                                                                        <?= date('M d, Y H:i', strtotime($workflow['created_at'])) ?>
                                                                    </small>
                                                                </div>
                                                            </div>

                                                            <?php if (!empty($workflow['assigned_to_name'])): ?>
                                                                <div class="mb-2">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-user me-1"></i>
                                                                        Assigned to: <?= esc($workflow['assigned_to_name']) ?>
                                                                    </small>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if (!empty($workflow['due_date'])): ?>
                                                                <div class="mb-2">
                                                                    <small class="text-<?= strtotime($workflow['due_date']) < time() ? 'danger' : 'warning' ?>">
                                                                        <i class="fas fa-clock me-1"></i>
                                                                        Due: <?= date('M d, Y H:i', strtotime($workflow['due_date'])) ?>
                                                                    </small>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if (!empty($workflow['comments'])): ?>
                                                                <div class="alert alert-info py-2 mb-3">
                                                                    <i class="fas fa-comment me-2"></i>
                                                                    <?= esc($workflow['comments']) ?>
                                                                </div>
                                                            <?php endif; ?>

                                                            <?php if ($workflow['current_status'] === 'pending' || $workflow['current_status'] === 'in_progress'): ?>
                                                                <div class="btn-group" role="group">
                                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateWorkflowModal"
                                                                        data-workflow-id="<?= $workflow['id'] ?>" data-current-status="<?= $workflow['current_status'] ?>">
                                                                        <i class="fas fa-edit me-1"></i>Update Status
                                                                    </button>
                                                                    <?php if (empty($workflow['assigned_to_name'])): ?>
                                                                        <button class="btn btn-sm btn-outline-success" onclick="assignToMe(<?= $workflow['id'] ?>)">
                                                                            <i class="fas fa-user-plus me-1"></i>Assign to Me
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Workflow Summary -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Workflow Summary
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6>Workflow Types</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Review:</span>
                                            <span class="badge bg-info">
                                                <?= count(array_filter($workflows, function ($w) {
                                                    return $w['workflow_type'] === 'review';
                                                })) ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Approval:</span>
                                            <span class="badge bg-warning">
                                                <?= count(array_filter($workflows, function ($w) {
                                                    return $w['workflow_type'] === 'approval';
                                                })) ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Publish:</span>
                                            <span class="badge bg-success">
                                                <?= count(array_filter($workflows, function ($w) {
                                                    return $w['workflow_type'] === 'publish';
                                                })) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <h6>Status Distribution</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Pending:</span>
                                            <span class="badge bg-warning">
                                                <?= count(array_filter($workflows, function ($w) {
                                                    return $w['current_status'] === 'pending';
                                                })) ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>In Progress:</span>
                                            <span class="badge bg-info">
                                                <?= count(array_filter($workflows, function ($w) {
                                                    return $w['current_status'] === 'in_progress';
                                                })) ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Completed:</span>
                                            <span class="badge bg-success">
                                                <?= count(array_filter($workflows, function ($w) {
                                                    return $w['current_status'] === 'completed';
                                                })) ?>
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Rejected:</span>
                                            <span class="badge bg-danger">
                                                <?= count(array_filter($workflows, function ($w) {
                                                    return $w['current_status'] === 'rejected';
                                                })) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#createWorkflowModal">
                                            <i class="fas fa-plus me-2"></i>Create New Workflow
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Workflow Modal -->
    <div class="modal fade" id="createWorkflowModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?= base_url('documents/workflow/' . $document['id']) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Workflow</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Workflow Type <span class="text-danger">*</span></label>
                            <select name="workflow_type" class="form-select" required>
                                <option value="">Select workflow type...</option>
                                <option value="review">Review</option>
                                <option value="approval">Approval</option>
                                <option value="publish">Publish</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Leave unassigned</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>"><?= esc($user['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Select a user to assign this workflow to</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="datetime-local" name="due_date" class="form-control">
                            <div class="form-text">When should this workflow be completed?</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Comments</label>
                            <textarea name="comments" class="form-control" rows="3" placeholder="Add any additional comments..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Workflow
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Workflow Modal -->
    <div class="modal fade" id="updateWorkflowModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="updateWorkflowForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Workflow Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="workflow_id" id="workflow_id">

                        <div class="mb-3">
                            <label class="form-label">New Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" id="statusSelect" required>
                                <option value="">Select new status...</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Comments</label>
                            <textarea name="comments" class="form-control" rows="3" placeholder="Add comments about the status change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        // Update workflow modal
        document.getElementById('updateWorkflowModal').addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var workflowId = button.getAttribute('data-workflow-id');
            var currentStatus = button.getAttribute('data-current-status');

            document.getElementById('workflow_id').value = workflowId;
            document.getElementById('updateWorkflowForm').action = '<?= base_url('documents/workflow/update/') ?>' + workflowId;

            // Disable current status in dropdown
            var statusSelect = document.getElementById('statusSelect');
            for (var i = 0; i < statusSelect.options.length; i++) {
                if (statusSelect.options[i].value === currentStatus) {
                    statusSelect.options[i].disabled = true;
                } else {
                    statusSelect.options[i].disabled = false;
                }
            }
        });

        function assignToMe(workflowId) {
            if (confirm('Assign this workflow to yourself?')) {
                // In a real implementation, this would make an API call
                alert('Workflow assigned to you');
                location.reload();
            }
        }
    </script>
</body>

</html>