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
                        'pageTitle' => '<i class="fas fa-file-alt me-2"></i>Document Approval Dashboard',
                        'pageDescription' => 'Manage quality documents and procedures'
                    ]) ?>

                    <!-- Approval Statistics -->
                    <div class="row mb-4">
                        <div class="col-lg-2 col-6">
                            <div class="card text-bg-warning mb-1">
                                <div class="card-body text-center">
                                    <h3><?= count($pending_documents ?? []) ?></h3>
                                    <p>Pending</p>
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="card text-bg-info mb-1">
                                <div class="card-body text-center">
                                    <h3><?= count($sent_for_review_documents ?? []) ?></h3>
                                    <p>Under Review</p>
                                    <i class="fas fa-search"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="card text-bg-primary mb-1">
                                <div class="card-body text-center">
                                    <h3><?= count($sent_for_approval_documents ?? []) ?></h3>
                                    <p>Reviewed</p>
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="card text-bg-success mb-1">
                                <div class="card-body text-center">
                                    <h3><?= count($approved_by_approver_documents ?? []) ?></h3>
                                    <p>Approved</p>
                                    <i class="fas fa-thumbs-up"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="card text-bg-secondary mb-1">
                                <div class="card-body text-center">
                                    <h3><?= count($returned_for_revision_documents ?? []) ?></h3>
                                    <p>Needs Revision</p>
                                    <i class="fas fa-edit"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-6">
                            <div class="card text-bg-danger mb-1">
                                <div class="card-body text-center">
                                    <h3><?= count($rejected_documents ?? []) ?></h3>
                                    <p>Rejected</p>
                                    <i class="fas fa-times-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Tabs -->
                    <ul class="nav nav-tabs" id="approvalTabs" role="tablist">
                        <!-- Pending Review -->
                        <li class="nav-item">
                            <a class="nav-link active" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab">
                                <i class="fas fa-clock text-warning"></i> Pending
                                (<?= count($pending_documents) ?>)
                            </a>
                        </li>

                        <!-- Sent for Review -->
                        <li class="nav-item">
                            <a class="nav-link" id="review-tab" data-bs-toggle="tab" href="#review" role="tab">
                                <i class="fas fa-search text-info"></i> In Review
                                (<?= count($sent_for_review_documents) ?>)
                            </a>
                        </li>

                        <!-- Sent for Approval -->
                        <li class="nav-item">
                            <a class="nav-link" id="approval-tab" data-bs-toggle="tab" href="#approval" role="tab">
                                <i class="fas fa-share-square text-primary"></i> Sent for Approval
                                (<?= count($sent_for_approval_documents) ?>)
                            </a>
                        </li>

                        <!-- Approved by Approver -->
                        <li class="nav-item">
                            <a class="nav-link" id="approved-tab" data-bs-toggle="tab" href="#approved" role="tab">
                                <i class="fas fa-thumbs-up text-success"></i> Approved by Approver
                                (<?= count($approved_by_approver_documents) ?>)
                            </a>
                        </li>

                        <!-- Admin Approved -->
                        <li class="nav-item">
                            <a class="nav-link" id="admin-approved-tab" data-bs-toggle="tab" href="#admin-approved"
                                role="tab">
                                <i class="fas fa-check-circle text-success"></i> Admin Approved
                                (<?= count($admin_approved_documents) ?>)
                            </a>
                        </li>

                        <!-- Returned for Revision -->
                        <li class="nav-item">
                            <a class="nav-link" id="revision-tab" data-bs-toggle="tab" href="#revision" role="tab">
                                <i class="fas fa-edit text-secondary"></i> Returned
                                (<?= count($returned_for_revision_documents) ?>)
                            </a>
                        </li>

                        <!-- Rejected -->
                        <li class="nav-item">
                            <a class="nav-link" id="rejected-tab" data-bs-toggle="tab" href="#rejected" role="tab">
                                <i class="fas fa-times-circle text-danger"></i> Rejected
                                (<?= count($rejected_documents) ?>)
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="approvalTabsContent">
                        <!-- Pending Documents -->
                        <div class="tab-pane fade show active" id="pending" role="tabpanel">
                            <?php if (empty($pending_documents)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No pending documents found.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th>Department</th>
                                                <th>Created By</th>
                                                <th>Created Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pending_documents as $doc): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($doc['title']) ?></strong>
                                                        <br><small class="text-muted">ID: <?= $doc['id'] ?></small>
                                                    </td>
                                                    <td><?= esc($doc['type_name']) ?></td>
                                                    <td><?= esc($doc['department_name']) ?></td>
                                                    <td><?= esc($doc['created_by_name']) ?></td>
                                                    <td><?= date('M j, Y', strtotime($doc['created_at'])) ?></td>
                                                    <td>
                                                        <a href="<?= base_url('documents/view/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <?php if ($role_name === 'admin' || $role_name === 'superadmin'): ?>
                                                            <button class="btn btn-sm btn-primary assign-reviewer-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>"
                                                                data-department-id="<?= $doc['department_id'] ?>">
                                                                <i class="fas fa-user-plus"></i> Share
                                                            </button>
                                                        <?php else: ?>
                                                            <a href="<?= base_url('documents/submit-for-review/' . $doc['id']) ?>"
                                                                class="btn btn-sm btn-primary">
                                                                <i class="fas fa-paper-plane"></i> Submit for Review
                                                            </a>
                                                        <?php endif ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif ?>
                        </div>

                        <!-- Under Review Documents -->
                        <div class="tab-pane fade" id="review" role="tabpanel">
                            <?php if (empty($sent_for_review_documents)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No documents under review.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th>Department</th>
                                                <th>Reviewer</th>
                                                <th>Submitted Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sent_for_review_documents as $doc): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($doc['title']) ?></strong>
                                                        <br><small class="text-muted">ID: <?= $doc['id'] ?></small>
                                                    </td>
                                                    <td><?= esc($doc['type_name']) ?></td>
                                                    <td><?= esc($doc['department_name']) ?></td>
                                                    <td><?= esc($doc['reviewer_name'] ?? 'Not assigned') ?></td>
                                                    <td><?= $doc['submitted_for_review_at'] ? date('M j, Y', strtotime($doc['submitted_for_review_at'])) : '-' ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('documents/view/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <?php if ($role_name === 'Reviewer'): ?>
                                                            <button class="btn btn-sm btn-success quick-review-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>"
                                                                data-action="approve_for_final">
                                                                <i class="fas fa-check"></i> Approve for Final
                                                            </button>
                                                            <button class="btn btn-sm btn-warning quick-review-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>"
                                                                data-action="return_for_revision">
                                                                <i class="fas fa-undo"></i> Return
                                                            </button>
                                                            <button class="btn btn-sm btn-danger quick-review-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>" data-action="reject">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        <?php else: ?>
                                                            
                                                        <?php endif ?>
                                                        <a href="<?= base_url('documents/approval-history/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="fas fa-history"></i> History
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif ?>
                        </div>

                        <!-- Reviewed Documents -->
                        <div class="tab-pane fade" id="approval" role="tabpanel">
                            <?php if (empty($sent_for_approval_documents)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No reviewed documents awaiting final approval.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th>Department</th>
                                                <th>Reviewer</th>
                                                <th>Reviewed Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sent_for_approval_documents as $doc): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($doc['title']) ?></strong>
                                                        <br><small class="text-muted">ID: <?= $doc['id'] ?></small>
                                                    </td>
                                                    <td><?= esc($doc['type_name']) ?></td>
                                                    <td><?= esc($doc['department_name']) ?></td>
                                                    <td><?= esc($doc['reviewer_name'] ?? 'Not assigned') ?></td>
                                                    <td><?= $doc['reviewed_at'] ? date('M j, Y', strtotime($doc['reviewed_at'])) : '-' ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('documents/view/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <?php if ($role_name === 'Approver'): ?>
                                                            <button class="btn btn-sm btn-success quick-approve-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>" data-action="approve">
                                                                <i class="fas fa-check"></i> Quick Approve
                                                            </button>
                                                            <button class="btn btn-sm btn-danger quick-approve-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>" data-action="reject">
                                                                <i class="fas fa-times"></i> Reject
                                                            </button>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning">Only approver can approve</span>
                                                        <?php endif ?>
                                                        <a href="<?= base_url('documents/approval-history/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="fas fa-history"></i> History
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif ?>
                        </div>

                        <!-- Approved Documents -->
                        <div class="tab-pane fade" id="approved" role="tabpanel">
                            <?php if (empty($approved_by_approver_documents)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No approved documents.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th>Department</th>
                                                <th>Approver</th>
                                                <th>Approved Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($approved_by_approver_documents as $doc): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($doc['title']) ?></strong>
                                                        <br><small class="text-muted">ID: <?= $doc['id'] ?></small>
                                                    </td>
                                                    <td><?= esc($doc['type_name']) ?></td>
                                                    <td><?= esc($doc['department_name']) ?></td>
                                                    <td><?= esc($doc['approver_name'] ?? 'System') ?></td>
                                                    <td><?= $doc['approved_at'] ? date('M j, Y', strtotime($doc['approved_at'])) : '-' ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('documents/view/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="<?= base_url('documents/approval-history/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="fas fa-history"></i> History
                                                        </a>
                                                        <?php if ($role_name === 'admin' || $role_name === 'superadmin'): ?>
                                                            <a href="<?= base_url('documents/lock/' . $doc['id']) ?>"
                                                                class="btn btn-sm btn-warning"
                                                                onclick="return confirm('Are you sure you want to lock this document as obsolete?')">
                                                                <i class="fas fa-lock"></i> Lock
                                                            </a>
                                                        <?php endif ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif ?>
                        </div>

                        <!-- Returned for Revision Documents -->
                        <div class="tab-pane fade" id="revision" role="tabpanel">
                            <?php if (empty($returned_for_revision_documents)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No documents returned for revision.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th>Department</th>
                                                <th>Returned By</th>
                                                <th>Returned Date</th>
                                                <th>Revision Count</th>
                                                <th>Comments</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($returned_for_revision_documents as $doc): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($doc['title']) ?></strong>
                                                        <br><small class="text-muted">ID: <?= $doc['id'] ?></small>
                                                    </td>
                                                    <td><?= esc($doc['type_name']) ?></td>
                                                    <td><?= esc($doc['department_name']) ?></td>
                                                    <td><?= esc($doc['reviewer_name'] ?? 'System') ?></td>
                                                    <td><?= $doc['returned_for_revision_at'] ? date('M j, Y', strtotime($doc['returned_for_revision_at'])) : '-' ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-warning"><?= $doc['revision_count'] ?? 1 ?></span>
                                                    </td>
                                                    <td>
                                                        <small><?= esc(substr($doc['revision_comments'] ?? 'No comments provided', 0, 50)) ?>...</small>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('documents/view/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <?php if ($role_name === 'admin' || $role_name === 'superadmin' || $doc['created_by'] == session()->get('user_id')): ?>
                                                            <a href="<?= base_url('documents/edit/' . $doc['id']) ?>"
                                                                class="btn btn-sm btn-warning">
                                                                <i class="fas fa-edit"></i> Revise
                                                            </a>
                                                            <button class="btn btn-sm btn-success resubmit-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>">
                                                                <i class="fas fa-paper-plane"></i> Resubmit
                                                            </button>
                                                        <?php endif ?>
                                                        <a href="<?= base_url('documents/approval-history/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="fas fa-history"></i> History
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif ?>
                        </div>

                        <!-- Rejected Documents -->
                        <div class="tab-pane fade" id="rejected" role="tabpanel">
                            <?php if (empty($rejected_documents)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No rejected documents.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th>Department</th>
                                                <th>Rejected By</th>
                                                <th>Rejected Date</th>
                                                <th>Reason</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($rejected_documents as $doc): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($doc['title']) ?></strong>
                                                        <br><small class="text-muted">ID: <?= $doc['id'] ?></small>
                                                    </td>
                                                    <td><?= esc($doc['type_name']) ?></td>
                                                    <td><?= esc($doc['department_name']) ?></td>
                                                    <td><?= esc($doc['reviewer_name'] ?? $doc['approver_name'] ?? 'System') ?>
                                                    </td>
                                                    <td><?= $doc['rejected_at'] ? date('M j, Y', strtotime($doc['rejected_at'])) : '-' ?>
                                                    </td>
                                                    <td>
                                                        <small><?= esc(substr($doc['rejection_reason'] ?? 'No reason provided', 0, 50)) ?>...</small>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('documents/view/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="<?= base_url('documents/approval-history/' . $doc['id']) ?>"
                                                            class="btn btn-sm btn-secondary">
                                                            <i class="fas fa-history"></i> History
                                                        </a>
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

        </div>
    </div>

    <!-- Assign Reviewer Modal -->
    <div class="modal fade" id="assignReviewerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Reviewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Assign a reviewer for: <strong id="assign-doc-title"></strong></p>
                    <div class="mb-3">
                        <label for="reviewer-select" class="form-label">Select Reviewer</label>
                        <select class="form-select" id="reviewer-select">
                            <option value="">Loading reviewers...</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-assign-reviewer">Assign Reviewer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Review Modal -->
    <div class="modal fade" id="quickReviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Review document: <strong id="review-doc-title"></strong></p>
                    <p>Action: <span id="review-action-text" class="badge"></span></p>
                    <div class="mb-3">
                        <label for="review-comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="review-comments" rows="3"
                            placeholder="Enter your comments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-review">Confirm Review</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Approve Modal -->
    <div class="modal fade" id="quickApproveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quick Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Approve document: <strong id="approve-doc-title"></strong></p>
                    <p>Action: <span id="approve-action-text" class="badge"></span></p>
                    <div class="mb-3">
                        <label for="approve-comments" class="form-label">Comments</label>
                        <textarea class="form-control" id="approve-comments" rows="3"
                            placeholder="Enter your comments..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-approve">Confirm Action</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resubmit Modal -->
    <div class="modal fade" id="resubmitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resubmit Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to resubmit this document for review?</p>
                    <p><strong id="resubmit-doc-title"></strong></p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> This will reset the document status to "Pending" and it will
                        need to be assigned to a reviewer again.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirm-resubmit">Resubmit Document</button>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <!-- jQuery (must come first) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Bootstrap JS (for tabs, modals, etc.) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize DataTables for better table handling
            $('.table').DataTable({
                "pageLength": 25,
                "order": [
                    [4, "desc"]
                ], // Sort by date column
                "columnDefs": [{
                    "orderable": false,
                    "targets": -1
                } // Disable sorting on Actions column
                ]
            });

            let currentDocId = null;
            let currentAction = null;

            // Assign Reviewer Modal
            $('.assign-reviewer-btn').on('click', function () {
                currentDocId = $(this).data('doc-id');
                const docTitle = $(this).data('doc-title');
                const departmentId = $(this).data('department-id');

                $('#assign-doc-title').text(docTitle);

                // Load reviewers for the department
                // $.get('<?= base_url('api/reviewers') ?>/' + departmentId)
                $.get('<?= base_url('api/reviewers') ?>')
                    .done(function (data) {
                        console.log(data.length);
                        const select = $('#reviewer-select');
                        select.empty();
                        select.append('<option value="">Select a reviewer...</option>');

                        if (data && data.length > 0) {
                            data.forEach(function (reviewer) {
                                select.append(`<option value="${reviewer.id}">${reviewer.name} - (${reviewer.username})</option>`);
                            });
                        } else {
                            select.append('<option value="">No reviewers available</option>');
                        }
                    })
                    .fail(function () {
                        $('#reviewer-select').html('<option value="">Error loading reviewers</option>');
                    });

                $('#assignReviewerModal').modal('show');
            });

            $('#confirm-assign-reviewer').on('click', function () {
                const reviewerId = $('#reviewer-select').val();

                if (!reviewerId) {
                    alert('Please select a reviewer');
                    return;
                }

                $(this).prop('disabled', true).text('Assigning...');

                $.post('<?= base_url('documents/assign-reviewer') ?>/' + currentDocId, {
                    reviewer_id: reviewerId
                })
                    .done(function (response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message || 'Failed to assign reviewer');
                        }
                    })
                    .fail(function () {
                        alert('Error assigning reviewer');
                    })
                    .always(function () {
                        $('#confirm-assign-reviewer').prop('disabled', false).text('Assign Reviewer');
                        $('#assignReviewerModal').modal('hide');
                    });
            });

            // Quick Review Modal
            $('.quick-review-btn').on('click', function () {
                currentDocId = $(this).data('doc-id');
                currentAction = $(this).data('action');
                const docTitle = $(this).data('doc-title');

                $('#review-doc-title').text(docTitle);

                const actionTexts = {
                    'approve_for_final': {
                        text: 'Approve for Final',
                        class: 'bg-success'
                    },
                    'return_for_revision': {
                        text: 'Return for Revision',
                        class: 'bg-warning'
                    },
                    'reject': {
                        text: 'Reject',
                        class: 'bg-danger'
                    }
                };

                const actionInfo = actionTexts[currentAction];
                $('#review-action-text').text(actionInfo.text).removeClass().addClass('badge ' + actionInfo.class);

                // Clear previous comments
                $('#review-comments').val('');

                $('#quickReviewModal').modal('show');
            });

            $('#confirm-review').on('click', function () {
                const comments = $('#review-comments').val();

                if (currentAction === 'reject' && !comments.trim()) {
                    alert('Comments are required for rejection');
                    return;
                }

                $(this).prop('disabled', true).text('Processing...');

                $.post('<?= base_url('documents/quick-review') ?>/' + currentDocId, {
                    action: currentAction,
                    comments: comments
                })
                    .done(function (response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message || 'Failed to process review');
                        }
                    })
                    .fail(function () {
                        alert('Error processing review');
                    })
                    .always(function () {
                        $('#confirm-review').prop('disabled', false).text('Confirm Review');
                        $('#quickReviewModal').modal('hide');
                    });
            });

            // Quick Approve Modal
            $('.quick-approve-btn').on('click', function () {
                currentDocId = $(this).data('doc-id');
                currentAction = $(this).data('action');
                const docTitle = $(this).data('doc-title');

                $('#approve-doc-title').text(docTitle);

                const actionTexts = {
                    'approve': {
                        text: 'Approve',
                        class: 'bg-success'
                    },
                    'reject': {
                        text: 'Reject',
                        class: 'bg-danger'
                    }
                };

                const actionInfo = actionTexts[currentAction];
                $('#approve-action-text').text(actionInfo.text).removeClass().addClass('badge ' + actionInfo.class);

                // Clear previous comments
                $('#approve-comments').val('');

                $('#quickApproveModal').modal('show');
            });

            $('#confirm-approve').on('click', function () {
                const comments = $('#approve-comments').val();

                if (currentAction === 'reject' && !comments.trim()) {
                    alert('Comments are required for rejection');
                    return;
                }

                $(this).prop('disabled', true).text('Processing...');

                $.post('<?= base_url('documents/quick-approve') ?>/' + currentDocId, {
                    action: currentAction,
                    comments: comments
                })
                    .done(function (response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message || 'Failed to process approval');
                        }
                    })
                    .fail(function () {
                        alert('Error processing approval');
                    })
                    .always(function () {
                        $('#confirm-approve').prop('disabled', false).text('Confirm Action');
                        $('#quickApproveModal').modal('hide');
                    });
            });

            // Resubmit Modal
            $('.resubmit-btn').on('click', function () {
                currentDocId = $(this).data('doc-id');
                const docTitle = $(this).data('doc-title');

                $('#resubmit-doc-title').text(docTitle);
                $('#resubmitModal').modal('show');
            });

            $('#confirm-resubmit').on('click', function () {
                $(this).prop('disabled', true).text('Resubmitting...');

                $.post('<?= base_url('documents/resubmit-after-revision') ?>/' + currentDocId)
                    .done(function (response) {
                        if (response && response.success) {
                            alert(response.message || 'Document resubmitted successfully');
                            location.reload();
                        } else {
                            // Handle non-AJAX response (redirect)
                            location.reload();
                        }
                    })
                    .fail(function () {
                        alert('Error resubmitting document');
                    })
                    .always(function () {
                        $('#confirm-resubmit').prop('disabled', false).text('Resubmit Document');
                        $('#resubmitModal').modal('hide');
                    });
            });
        });
    </script>
</body>

</html>