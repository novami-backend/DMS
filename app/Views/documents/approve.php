<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final Approval - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .required::after {
            content: " *";
            color: red;
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

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }

        .timeline-title {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .timeline-text {
            margin: 0;
            font-size: 13px;
        }

        .document-content {
            margin-top: 20px;
        }

        .document-info {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 15px;
        }

        .approval-checklist {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .approval-checklist .form-check {
            margin-bottom: 10px;
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
            <div class="col-lg-10 col-md-9 p-0">
                <div class="main-content">
                    <!-- Header -->
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-stamp me-2"></i>Final Document Approval',
                        'pageDescription' => 'Review and finalize document approval'
                    ]) ?>

                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h3 class="card-title mb-0">
                                            <i class="fas fa-file-alt text-primary me-2"></i> Document Information
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="document-info mb-4">
                                            <h4><?= esc($document['title']) ?></h4>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Type:</strong> <?= esc($document['type_name']) ?></p>
                                                    <p><strong>Department:</strong> <?= esc($document['department_name']) ?></p>
                                                    <p><strong>Created By:</strong> <?= esc($document['created_by_name']) ?></p>
                                                    <p><strong>Reviewer:</strong> <?= esc($document['reviewer_name'] ?? 'Not assigned') ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Created Date:</strong> <?= date('M j, Y', strtotime($document['created_at'])) ?></p>
                                                    <p><strong>Reviewed Date:</strong> <?= $document['reviewed_at'] ? date('M j, Y', strtotime($document['reviewed_at'])) : 'Not reviewed' ?></p>
                                                    <p><strong>Status:</strong> 
                                                        <span class="badge bg-warning"><?= ucfirst($document['status']) ?></span>
                                                    </p>
                                                    <p><strong>Approval Status:</strong> 
                                                        <span class="badge bg-primary"><?= ucfirst(str_replace('_', ' ', $document['approval_status'])) ?></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($document['reviewer_comments']): ?>
                                        <div class="reviewer-comments mb-4">
                                            <h5><i class="fas fa-comment"></i> Reviewer Comments</h5>
                                            <div class="alert alert-info">
                                                <?= nl2br(esc($document['reviewer_comments'])) ?>
                                            </div>
                                        </div>
                                        <?php endif ?>

                                        <div class="document-content">
                                            <h5>Document Content</h5>
                                            <div class="border p-3 bg-light rounded">
                                                <?= nl2br(esc($document['content'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Final Approval Form -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-gavel text-success me-2"></i> Final Approval Decision
                                        </h5>
                                    </div>
                                    <?= form_open('documents/process-approval/' . $document['id']) ?>
                                    <div class="card-body">
                                        <div class="alert alert-warning small">
                                            <h6><i class="fas fa-exclamation-triangle"></i> Final Approval Authority</h6>
                                            <p class="mb-0">As an Admin, you have the final approval authority. Your decision will activate or reject this document.</p>
                                        </div>

                                        <div class="mb-3">
                                            <label for="action" class="form-label required">Decision Action</label>
                                            <select class="form-select" id="action" name="action" required>
                                                <option value="">Select Action</option>
                                                <?php if (session()->get('role_name') === 'admin' || session()->get('role_name') === 'superadmin'): ?>
                                                    <option value="final_approve">Final Approve & Activate</option>
                                                    <option value="return_to_approver">Return to Approver</option>
                                                <?php else: ?>
                                                    <option value="approve">Approve & Move to Final</option>
                                                <?php endif; ?>
                                                <option value="return_to_reviewer">Return to Reviewer</option>
                                                <option value="return_to_creator">Return to Creator (Revision)</option>
                                                <option value="reject">Reject Document</option>
                                            </select>
                                        </div>

                                        <div id="target-selection" style="display: none;" class="mb-3">
                                            <label for="target_user_id" id="target-label" class="form-label required">Select Target User</label>
                                            <select class="form-select" id="target_user_id" name="target_user_id">
                                                <option value="">Select User</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="comments" class="form-label">Comments / Reason</label>
                                            <textarea class="form-control" id="comments" name="comments" rows="4" 
                                                      placeholder="Provide necessary feedback and instructions..."></textarea>
                                            <small class="text-muted">Your comments will be part of the permanent approval record.</small>
                                        </div>

                                        <div class="approval-checklist border p-3 bg-light rounded mb-3" id="checklist-section" style="display: none;">
                                            <h6><i class="fas fa-clipboard-list"></i> Approval Checklist</h6>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="check1">
                                                <label class="form-check-label" for="check1">
                                                    Document has been properly reviewed
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="check2">
                                                <label class="form-check-label" for="check2">
                                                    Content meets quality standards
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="check3">
                                                <label class="form-check-label" for="check3">
                                                    Document complies with requirements
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white d-grid gap-2">
                                        <button type="submit" class="btn btn-success" id="approveBtn" disabled>
                                            <i class="fas fa-check-circle"></i> Submit Final Decision
                                        </button>
                                        <a href="<?= base_url('approval-dashboard') ?>" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                                        </a>
                                    </div>
                                    <?= form_close() ?>
                                </div>

                                <!-- Approval History -->
                                <?php if (!empty($approval_history)): ?>
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-history text-info me-2"></i> Approval History
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline">
                                            <?php foreach ($approval_history as $history): ?>
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-<?= getActionColor($history['action']) ?>"></div>
                                                <div class="timeline-content">
                                                    <h6 class="timeline-title"><?= ucfirst(str_replace('_', ' ', $history['action'])) ?></h6>
                                                    <p class="timeline-text small">
                                                        <strong><?= esc($history['performed_by_name']) ?></strong><br>
                                                        <?= esc($history['comments']) ?><br>
                                                        <small class="text-muted"><?= date('M j, Y g:i A', strtotime($history['created_at'])) ?></small>
                                                    </p>
                                                </div>
                                            </div>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endif ?>

                                <!-- Digital Signature Info -->
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-white">
                                        <h5 class="card-title mb-0">
                                            <i class="fas fa-signature text-secondary me-2"></i> Digital Signature
                                        </h5>
                                    </div>
                                    <div class="card-body small">
                                        <p class="mb-1"><strong>Approver:</strong> <?= esc($username) ?></p>
                                        <p class="mb-1"><strong>Role:</strong> <?= esc($role_name) ?></p>
                                        <p class="mb-1"><strong>Timestamp:</strong> <?= date('M j, Y g:i A') ?></p>
                                        <p class="mb-1"><strong>IP Address:</strong> <?= $this->request->getIPAddress() ?></p>
                                        <small class="text-muted">This information will be recorded with your approval decision.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?= view('common/footer') ?>
            </div>
        </div>
    </div>

    <?php
    function getActionColor($action) {
        $colors = [
            'submitted_for_review' => 'primary',
            'reviewed' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'return_for_revision' => 'warning'
        ];
        return $colors[$action] ?? 'secondary';
    }
    ?>

    <?= view('common/scripts') ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const reviewers = <?= json_encode($reviewers ?? []) ?>;
            const approvers = <?= json_encode($approvers ?? []) ?>;
            const currentReviewerId = <?= json_encode($document['reviewer_id'] ?? null) ?>;
            const currentApproverId = <?= json_encode($document['approver_id'] ?? null) ?>;

            function checkApprovalRequirements() {
                const action = $('#action').val();
                const isApproval = (action === 'approve' || action === 'final_approve');
                
                let allChecked = true;
                if (isApproval) {
                    allChecked = $('.approval-checklist input[type="checkbox"]').length === 
                                  $('.approval-checklist input[type="checkbox"]:checked').length;
                }

                const actionSelected = action !== '';
                let targetSelected = true;
                if ($('#target-selection').is(':visible')) {
                    targetSelected = $('#target_user_id').val() !== '';
                }
                
                $('#approveBtn').prop('disabled', !(allChecked && actionSelected && targetSelected));
            }

            $('.approval-checklist input[type="checkbox"], #action, #target_user_id').change(checkApprovalRequirements);

            // Handle action change
            $('#action').change(function() {
                const action = $(this).val();
                const commentsField = $('#comments');
                const targetSelection = $('#target-selection');
                const targetDropdown = $('#target_user_id');
                const targetLabel = $('#target-label');
                const checklistSection = $('#checklist-section');
                
                // Reset target dropdown
                targetDropdown.empty().append('<option value="">Select User</option>');
                targetSelection.hide();
                checklistSection.hide();
                
                if (action === 'approve' || action === 'final_approve') {
                    commentsField.attr('placeholder', 'Add any approval comments (optional)...');
                    commentsField.prop('required', false);
                    checklistSection.show();
                } else if (action === 'reject') {
                    commentsField.attr('placeholder', 'Explain why this document is being rejected (required)...');
                    commentsField.prop('required', true);
                } else if (action === 'return_to_reviewer') {
                    commentsField.attr('placeholder', 'Specify what the reviewer needs to re-check (required)...');
                    commentsField.prop('required', true);
                    targetLabel.text('Select Reviewer');
                    reviewers.forEach(user => {
                        const selected = (user.id == currentReviewerId) ? 'selected' : '';
                        targetDropdown.append(`<option value="${user.id}" ${selected}>${user.username}</option>`);
                    });
                    targetSelection.show();
                } else if (action === 'return_to_approver') {
                    commentsField.attr('placeholder', 'Specify why it is being returned to the approver (required)...');
                    commentsField.prop('required', true);
                    targetLabel.text('Select Approver');
                    approvers.forEach(user => {
                        const selected = (user.id == currentApproverId) ? 'selected' : '';
                        targetDropdown.append(`<option value="${user.id}" ${selected}>${user.username}</option>`);
                    });
                    targetSelection.show();
                } else if (action === 'return_to_creator') {
                    commentsField.attr('placeholder', 'Specify what needs to be revised by the creator (required)...');
                    commentsField.prop('required', true);
                }
                
                checkApprovalRequirements();
            });

            // Form validation
            $('form').on('submit', function(e) {
                const action = $('#action').val();
                const comments = $('#comments').val().trim();
                
                if (action !== 'approve' && action !== 'final_approve' && comments === '') {
                    e.preventDefault();
                    alert('Comments/Reason are required for this action.');
                    $('#comments').focus();
                    return false;
                }
                
                let confirmMsg = 'Are you sure you want to proceed?';
                if (action === 'approve' || action === 'final_approve') {
                    confirmMsg = 'Confirm approval? This will move the document forward.';
                } else if (action === 'reject') {
                    confirmMsg = 'Confirm rejection? This action is final.';
                } else if (action.startsWith('return_')) {
                    confirmMsg = 'Confirm return? The document will be sent back for changes.';
                }
                
                return confirm(confirmMsg);
            });
        });
    </script>
</body>

</html>