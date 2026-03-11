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
                        <div class="col-lg-2 col-4 mb-2">
                            <div class="card text-bg-warning h-100">
                                <div class="card-body text-center p-2">
                                    <h4><?= count($pending_documents ?? []) ?></h4>
                                    <p class="mb-0 small">Pending</p>
                                    <i class="fas fa-clock fa-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-4 mb-2">
                            <div class="card text-bg-info h-100">
                                <div class="card-body text-center p-2">
                                    <h4><?= count($sent_for_review_documents ?? []) ?></h4>
                                    <p class="mb-0 small">In Review</p>
                                    <i class="fas fa-search fa-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-4 mb-2">
                            <div class="card text-bg-primary h-100">
                                <div class="card-body text-center p-2">
                                    <h4><?= count($sent_for_approval_documents ?? []) ?></h4>
                                    <p class="mb-0 small">Sent for Approval</p>
                                    <i class="fas fa-check-circle fa-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-4 mb-2">
                            <div class="card text-bg-success h-100">
                                <div class="card-body text-center p-2">
                                    <h4><?= count($approved_by_approver_documents ?? []) ?></h4>
                                    <p class="mb-0 small">Approved by Approver</p>
                                    <i class="fas fa-thumbs-up fa-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-4 mb-2">
                            <div class="card text-bg-success h-100" style="background-color: #198754 !important;">
                                <div class="card-body text-center p-2">
                                    <h4><?= count($admin_approved_documents ?? []) ?></h4>
                                    <p class="mb-0 small">Admin Approved</p>
                                    <i class="fas fa-check-double fa-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-4 mb-2">
                            <div class="card text-bg-secondary h-100">
                                <div class="card-body text-center p-1">
                                    <h5><?= count($returned_for_revision_documents ?? []) ?></h5>
                                    <p class="mb-0 small">Returned</p>
                                    <i class="fas fa-edit fa-xs"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-1 col-4 mb-2">
                            <div class="card text-bg-danger h-100">
                                <div class="card-body text-center p-1">
                                    <h5><?= count($rejected_documents ?? []) ?></h5>
                                    <p class="mb-0 small">Rejected</p>
                                    <i class="fas fa-times-circle fa-xs"></i>
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
                                                        <?php if ($role_name === 'admin' || $role_name === 'dept_admin' || $role_name === 'superadmin'): ?>
                                                            <button class="btn btn-sm btn-primary assign-reviewer-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>"
                                                                data-department-id="<?= $doc['department_id'] ?>">
                                                                <i class="fas fa-user-plus"></i> Share
                                                            </button>
                                                        <?php else: ?>
                                                            <?php if (!empty($doc['reviewer_id'])): ?>
                                                                <a href="<?= base_url('documents/submit-for-review/' . $doc['id']) ?>"
                                                                    class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-paper-plane"></i> Submit for Review
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-sm btn-secondary" disabled title="No reviewer assigned">
                                                                    <i class="fas fa-paper-plane"></i> Submit for Review
                                                                </button>
                                                            <?php endif; ?>
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
                                                        <?php if ($role_name === 'reviewer'): ?>
                                                            <!-- <button class="btn btn-sm btn-success quick-review-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>"
                                                                data-action="approve_for_final">
                                                                <i class="fas fa-check"></i> Approve
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
                                                            </button> -->
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
                                                        <?php if ((($role_name === 'approver' && (empty($doc['approver_id']) || $doc['approver_id'] == session()->get('user_id'))) || $role_name === 'lab_manager' || $role_name === 'superadmin')): ?>
                                                            <?php $revList = json_encode($reviewer_lists[$doc['id']] ?? []); ?>
                                                            <?php $aprList = json_encode($approver_lists[$doc['id']] ?? []); ?>
                                                            <!-- <button class="btn btn-sm btn-success quick-approve-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>" data-action="approve"
                                                                data-creator-id="<?= $doc['created_by'] ?>"
                                                                data-reviewer-id="<?= $doc['reviewer_id'] ?? '' ?>"
                                                                data-reviewers='<?= esc($revList) ?>'
                                                                data-approvers='<?= esc($aprList) ?>'>
                                                                <i class="fas fa-check"></i> Quick Approve
                                                            </button>
                                                            <button class="btn btn-sm btn-warning quick-approve-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>" data-action="return_for_revision"
                                                                data-creator-id="<?= $doc['created_by'] ?>"
                                                                data-reviewer-id="<?= $doc['reviewer_id'] ?? '' ?>"
                                                                data-reviewers='<?= esc($revList) ?>'
                                                                data-approvers='<?= esc($aprList) ?>'>
                                                                <i class="fas fa-undo"></i> Return
                                                            </button>
                                                            <button class="btn btn-sm btn-danger quick-approve-btn"
                                                                data-doc-id="<?= $doc['id'] ?>"
                                                                data-doc-title="<?= esc($doc['title']) ?>" data-action="reject"
                                                                data-creator-id="<?= $doc['created_by'] ?>"
                                                                data-reviewer-id="<?= $doc['reviewer_id'] ?? '' ?>"
                                                                data-reviewers='<?= esc($revList) ?>'
                                                                data-approvers='<?= esc($aprList) ?>'>
                                                                <i class="fas fa-times"></i> Reject
                                                            </button> -->
                                                        <?php else: ?>
                                                            <!-- Approver not assigned or not you -->
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
                                                        <?php if ($role_name === 'admin' || $role_name === 'dept_admin' || $role_name === 'superadmin'): ?>
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


                        <!-- Admin Approved Documents -->
                        <div class="tab-pane fade" id="admin-approved" role="tabpanel">
                            <?php if (empty($admin_approved_documents)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> No admin approved documents.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Document</th>
                                                <th>Type</th>
                                                <th>Department</th>
                                                <th>Approved By</th>
                                                <th>Approved Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($admin_approved_documents as $doc): ?>
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
                                                    <td><?= esc($doc['approver_name'] ?? $doc['reviewer_name'] ?? 'System') ?></td>
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
                                                        <?php if ($role_name === 'admin' || $role_name === 'dept_admin' || $role_name === 'superadmin' || $doc['created_by'] == session()->get('user_id')): ?>
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
                    <div class="mb-3" id="quick-target-section" style="display:none;">
                        <label for="quick-target-user" class="form-label">Return To</label>
                        <select class="form-select" id="quick-target-user">
                            <option value="creator">Creator</option>
                            <option value="reviewer">Reviewer</option>
                        </select>
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

    <!-- jQuery (must come first) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
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



            // assign reviewer button opens modal and loads department reviewers
            $('.assign-reviewer-btn').on('click', function() {
                currentDocId = $(this).data('doc-id');
                const departmentId = $(this).data('department-id');
                const docTitle = $(this).data('doc-title');

                $('#assign-doc-title').text(docTitle);

                const select = $('#reviewer-select');
                select.empty().append('<option value="">Select a reviewer...</option>');

                $.get('<?= base_url('api/reviewers') ?>/' + departmentId)
                    .done(function(data) {
                        if (data && data.length > 0) {
                            data.forEach(function(reviewer) {
                                select.append(`<option value="${reviewer.id}">${reviewer.name} - (${reviewer.username})</option>`);
                            });
                        } else {
                            select.append('<option value="">No reviewers available</option>');
                        }
                    })
                    .fail(function() {
                        select.html('<option value="">Error loading reviewers</option>');
                    });

                $('#assignReviewerModal').modal('show');
            });
        });

        $('#confirm-assign-reviewer').on('click', function() {
            const reviewerId = $('#reviewer-select').val();

            if (!reviewerId) {
                alert('Please select a reviewer');
                return;
            }

            $(this).prop('disabled', true).text('Assigning...');

            $.post('<?= base_url('documents/assign-reviewer') ?>/' + currentDocId, {
                    reviewer_id: reviewerId
                })
                .done(function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to assign reviewer');
                    }
                })
                .fail(function() {
                    alert('Error assigning reviewer');
                })
                .always(function() {
                    $('#confirm-assign-reviewer').prop('disabled', false).text('Assign Reviewer');
                    $('#assignReviewerModal').modal('hide');
                });
        });

        // Quick Review Modal
        $('.quick-review-btn').on('click', function() {
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

        $('#confirm-review').on('click', function() {
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
                .done(function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to process review');
                    }
                })
                .fail(function() {
                    alert('Error processing review');
                })
                .always(function() {
                    $('#confirm-review').prop('disabled', false).text('Confirm Review');
                    $('#quickReviewModal').modal('hide');
                });
        });

        // Quick Approve Modal
        $('.quick-approve-btn').on('click', function() {
            currentDocId = $(this).data('doc-id');
            currentAction = $(this).data('action');
            const docTitle = $(this).data('doc-title');
            const creatorId = $(this).data('creator-id');
            const reviewerId = $(this).data('reviewer-id');
            // reviewers and approvers lists encoded as JSON
            const reviewerList = $(this).attr('data-reviewers') ? JSON.parse($(this).attr('data-reviewers')) : [];
            const approverList = $(this).attr('data-approvers') ? JSON.parse($(this).attr('data-approvers')) : [];

            $('#approve-doc-title').text(docTitle);

            const actionTexts = {
                'approve': {
                    text: 'Approve',
                    class: 'bg-success'
                },
                'reject': {
                    text: 'Reject',
                    class: 'bg-danger'
                },
                'return_for_revision': {
                    text: 'Return for Revision',
                    class: 'bg-warning'
                }
            };

            const actionInfo = actionTexts[currentAction] || {
                text: currentAction,
                class: 'bg-secondary'
            };
            $('#approve-action-text').text(actionInfo.text).removeClass().addClass('badge ' + actionInfo.class);

            // Clear previous comments
            $('#approve-comments').val('');

            if (currentAction === 'return_for_revision') {
                $('#quick-target-section').show();
                $('#quick-target-user').empty();
                $('#quick-target-user').append('<option value="creator">Creator</option>');
                if (reviewerId) {
                    $('#quick-target-user').append('<option value="reviewer">Reviewer</option>');
                }
                // include other reviewers
                reviewerList.forEach(function(u) {
                    if (u.id && u.id != reviewerId) {
                        $('#quick-target-user').append('<option value="' + u.id + '">Reviewer: ' + u.username + '</option>');
                    }
                });
                // include other approvers
                approverList.forEach(function(u) {
                    if (u.id) {
                        $('#quick-target-user').append('<option value="' + u.id + '">Approver: ' + u.username + '</option>');
                    }
                });
            } else {
                $('#quick-target-section').hide();
            }

            $('#quickApproveModal').modal('show');
        });

        $('#confirm-approve').on('click', function() {
            const comments = $('#approve-comments').val();

            if ((currentAction === 'reject' || currentAction === 'return_for_revision') && !comments.trim()) {
                alert('Comments are required for rejection or return');
                return;
            }

            let postAction = currentAction;
            let targetUserId = '';
            if (currentAction === 'return_for_revision') {
                const choice = $('#quick-target-user').val();
                if (choice === 'creator') {
                    postAction = 'return_to_creator';
                    targetUserId = $(".quick-approve-btn[data-doc-id='" + currentDocId + "']").data('creator-id');
                } else if (choice === 'reviewer') {
                    postAction = 'return_to_reviewer';
                    targetUserId = $(".quick-approve-btn[data-doc-id='" + currentDocId + "']").data('reviewer-id');
                } else if (choice) {
                    // numeric id passed (other reviewer or approver)
                    postAction = 'return_to_reviewer';
                    targetUserId = choice;
                }
            }

            $(this).prop('disabled', true).text('Processing...');

            $.post('<?= base_url('documents/quick-approve') ?>/' + currentDocId, {
                    action: postAction,
                    comments: comments,
                    target_user_id: targetUserId
                })
                .done(function(response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to process approval');
                    }
                })
                .fail(function() {
                    alert('Error processing approval');
                })
                .always(function() {
                    $('#confirm-approve').prop('disabled', false).text('Confirm Action');
                    $('#quickApproveModal').modal('hide');
                });
        });

        // Resubmit Modal
        $('.resubmit-btn').on('click', function() {
            currentDocId = $(this).data('doc-id');
            const docTitle = $(this).data('doc-title');

            $('#resubmit-doc-title').text(docTitle);
            $('#resubmitModal').modal('show');
        });

        $('#confirm-resubmit').on('click', function() {
            $(this).prop('disabled', true).text('Resubmitting...');

            $.post('<?= base_url('documents/resubmit-after-revision') ?>/' + currentDocId)
                .done(function(response) {
                    if (response && response.success) {
                        alert(response.message || 'Document resubmitted successfully');
                        location.reload();
                    } else {
                        // Handle non-AJAX response (redirect)
                        location.reload();
                    }
                })
                .fail(function() {
                    alert('Error resubmitting document');
                })
                .always(function() {
                    $('#confirm-resubmit').prop('disabled', false).text('Resubmit Document');
                    $('#resubmitModal').modal('hide');
                });
        });
    </script>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
</body>

</html>