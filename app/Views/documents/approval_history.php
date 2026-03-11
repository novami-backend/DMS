<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval History - DMS</title>
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

        .document-info {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 10px;
            padding: 5px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .timeline-compact {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .timeline-item-compact {
            display: flex;
            gap: 12px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }

        .timeline-marker-compact {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            width: 28px;
            height: 28px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }

        .timeline-content-compact {
            flex: 1;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-sm {
            padding: 0.75rem 1rem;
            font-size: 14px;
            margin-bottom: 0;
        }

        .card {
            margin-bottom: 1rem;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header h6 {
            font-size: 15px;
            margin-bottom: 0;
            font-weight: 600;
        }

        .card-body {
            font-size: 14px;
        }

        .card-body small {
            font-size: 13px;
        }

        .badge {
            font-size: 12px;
            padding: 0.35rem 0.65rem;
        }

        .btn-sm {
            font-size: 14px;
        }

        .approval-list {
            position: relative;
            padding-left: 1.5rem;
            border-left: 2px dashed #007bff;
            /* dashed vertical line */
        }

        .approval-item {
            margin-bottom: 1rem;
            position: relative;
        }

        .approval-item::before {
            content: '';
            position: absolute;
            left: -30px;
            top: 10px;
            width: 12px;
            height: 12px;
            background: #007bff;
            border-radius: 50%;
            border: 2px solid #fff;
            /* optional: white outline for clarity */
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
                        'pageTitle' => '<i class="fas fa-history me-2"></i>Approval History',
                        'pageDescription' => 'Document approval timeline and history'
                    ]) ?>

                    <div class="row">
                        <!-- Main Content -->
                        <div class="col-lg-9">
                            <!-- Document Info Card -->
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i><?= esc($document['title']) ?>
                                    </h6>
                                </div>
                                <div class="card-body py-2 px-3">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <small><strong>Type:</strong> <?= esc($document['type_name']) ?></small>
                                        </div>
                                        <div class="col-md-3">
                                            <small><strong>Department:</strong>
                                                <?= esc($document['department_name']) ?></small>
                                        </div>
                                        <div class="col-md-3">
                                            <small><strong>Created:</strong>
                                                <?= date('M j, Y', strtotime($document['created_at'])) ?></small>
                                        </div>
                                        <!-- <div class="col-md-3">
                                            <small><strong>Status:</strong> <span
                                                    class="badge badge-<?= getStatusColor($document['status']) ?>"><?= ucfirst($document['status']) ?></span></small>
                                        </div> -->
                                    </div>
                                </div>
                            </div>

                            <!-- Approval Timeline Card -->
                            <div class="card">
                                <div class="card-header py-2">
                                    <h6 class="mb-0"><i class="fas fa-timeline me-2"></i>Approval Timeline</h6>
                                </div>
                                <div class="card-body py-2 px-3">
                                    <?php if (empty($approval_history)): ?>
                                        <div class="alert alert-info alert-sm mb-0">
                                            <i class="fas fa-info-circle"></i> No approval history available.
                                        </div>
                                    <?php else: ?>
                                        <div class="approval-list">
                                            <?php foreach ($approval_history as $history): ?>
                                                <div class="approval-item">
                                                    <div class="approval-content">
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <div>
                                                                <strong
                                                                    style="font-size: 14px;"><?= getActionTitle($history['action']) ?></strong>
                                                                <br>
                                                                <small class="text-muted">By:
                                                                    <?= esc($history['performed_by_name']) ?></small>
                                                            </div>
                                                            <small class="text-muted text-nowrap ms-2">
                                                                <?= date('M j, g:i A, Y', strtotime($history['created_at'])) ?>
                                                            </small>
                                                        </div>
                                                        <?php if ($history['comments']): ?>
                                                            <small
                                                                class="text-muted d-block"><em><?= nl2br(esc($history['comments'])) ?></em></small>
                                                        <?php endif ?>
                                                    </div>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </div>

                        </div>

                        <!-- Sidebar -->
                        <div class="col-lg-3">
                            <!-- Document Summary Card -->
                            <div class="card mb-3">
                                <div class="card-header py-2">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Summary</h6>
                                </div>
                                <div class="card-body py-2 px-3">
                                    <small>
                                        <div class="mb-2"><strong>ID:</strong> <?= $document['id'] ?></div>
                                        <div class="mb-2"><strong>Created By:</strong>
                                            <?= esc($document['created_by_name']) ?></div>
                                        <div class="mb-2"><strong>Reviewer:</strong>
                                            <?= $document['reviewer_name'] ?? 'Not assigned' ?></div>
                                        <div class="mb-2"><strong>Approver:</strong>
                                            <?= $document['approver_name'] ?? 'Not assigned' ?></div>
                                        <div class="mb-2"><strong>Approval Status:</strong> <span
                                                class="badge bg-<?= getApprovalStatusColor($document['approval_status']) ?>"><?= ucfirst(str_replace('_',' ', $document['approval_status'])) ?></span>
                                        </div>
                                        <?php if ($document['effective_date']): ?>
                                            <div class="mb-2"><strong>Effective:</strong>
                                                <?= date('M j, Y', strtotime($document['effective_date'])) ?></div>
                                        <?php endif ?>
                                    </small>
                                </div>
                            </div>

                            <!-- Actions Card -->
                            <div class="card">
                                <div class="card-header py-2">
                                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Actions</h6>
                                </div>
                                <div class="card-body py-2 px-3">
                                    <div class="d-grid gap-2">
                                        <a href="<?= base_url('documents/view/' . $document['id']) ?>"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>View Document
                                        </a>
                                        <a href="<?= base_url('approval-dashboard') ?>"
                                            class="btn btn-sm btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i>Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
</body>

<?php
function getStatusColor($status)
{
    $colors = [
        'draft' => 'warning',
        'active' => 'success',
        'archived' => 'secondary'
    ];
    return $colors[$status] ?? 'secondary';
}

function getApprovalStatusColor($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'sent_for_review':
            return 'info';
        case 'sent_for_approval':
            return 'primary';
        case 'approved_by_approver':
            return 'success';
        case 'admin_approved':
            return 'success';
        case 'returned_for_revision':
            return 'secondary';
        case 'rejected':
            return 'danger';
        default:
            return 'dark';
    }
}

function getActionColor($action)
{
    $colors = [
        'submitted_for_review' => 'primary',
        'reviewed' => 'info',
        'submitted_for_approval' => 'primary',
        'approved' => 'success',
        'rejected' => 'danger',
        'returned_for_revision' => 'warning'
    ];
    return $colors[$action] ?? 'secondary';
}

function getActionIcon($action)
{
    $icons = [
        'submitted_for_review' => 'fa-paper-plane',
        'reviewed' => 'fa-search',
        'submitted_for_approval' => 'fa-upload',
        'approved' => 'fa-check',
        'rejected' => 'fa-times',
        'returned_for_revision' => 'fa-undo'
    ];
    return $icons[$action] ?? 'fa-circle';
}

function getActionTitle($action)
{
    $titles = [
        'submitted_for_review' => 'Submitted for Review',
        'reviewed' => 'Document Reviewed',
        'submitted_for_approval' => 'Submitted for Final Approval',
        'approved' => 'Document Approved',
        'rejected' => 'Document Rejected',
        'returned_for_revision' => 'Returned for Revision'
    ];
    return $titles[$action] ?? ucfirst(str_replace('_', ' ', $action));
}
?>

</html>