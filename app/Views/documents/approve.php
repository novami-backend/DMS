<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Final Approval<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-stamp"></i> Final Document Approval
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">Reviewed - Awaiting Final Approval</span>
                    </div>
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
                                    <span class="badge badge-warning"><?= ucfirst($document['status']) ?></span>
                                </p>
                                <p><strong>Approval Status:</strong> 
                                    <span class="badge badge-primary"><?= ucfirst(str_replace('_', ' ', $document['approval_status'])) ?></span>
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
                        <div class="border p-3 bg-light">
                            <?= nl2br(esc($document['content'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Final Approval Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-gavel"></i> Final Approval Decision
                    </h5>
                </div>
                <?= form_open('documents/process-approval/' . $document['id']) ?>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Final Approval Authority</h6>
                        <p class="mb-0">As an Admin, you have the final approval authority. Your decision will activate or reject this document.</p>
                    </div>

                    <div class="form-group">
                        <label for="action" class="required">Final Decision</label>
                        <select class="form-control" id="action" name="action" required>
                            <option value="">Select Decision</option>
                            <option value="approve">Approve & Activate Document</option>
                            <option value="reject">Reject Document</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="comments">Approval Comments</label>
                        <textarea class="form-control" id="comments" name="comments" rows="4" 
                                  placeholder="Add any final comments (optional for approval, required for rejection)..."></textarea>
                        <small class="text-muted">Your comments will be part of the permanent approval record.</small>
                    </div>

                    <div class="approval-checklist">
                        <h6><i class="fas fa-clipboard-list"></i> Final Approval Checklist</h6>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check1" required>
                            <label class="form-check-label" for="check1">
                                Document has been properly reviewed
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check2" required>
                            <label class="form-check-label" for="check2">
                                Content meets quality standards
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check3" required>
                            <label class="form-check-label" for="check3">
                                Document complies with ISO 17025 requirements
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="check4" required>
                            <label class="form-check-label" for="check4">
                                I authorize this document for use
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block" id="approveBtn" disabled>
                        <i class="fas fa-check-circle"></i> Submit Final Decision
                    </button>
                    <a href="<?= base_url('approval-dashboard') ?>" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                <?= form_close() ?>
            </div>

            <!-- Approval History -->
            <?php if (!empty($approval_history)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-history"></i> Approval History
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($approval_history as $history): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-<?= getActionColor($history['action']) ?>"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title"><?= ucfirst(str_replace('_', ' ', $history['action'])) ?></h6>
                                <p class="timeline-text">
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
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-signature"></i> Digital Signature
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Approver:</strong> <?= esc($username) ?></p>
                    <p><strong>Role:</strong> <?= esc($role_name) ?></p>
                    <p><strong>Timestamp:</strong> <?= date('M j, Y g:i A') ?></p>
                    <p><strong>IP Address:</strong> <?= $this->request->getIPAddress() ?></p>
                    <small class="text-muted">This information will be recorded with your approval decision.</small>
                </div>
            </div>
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
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Enable/disable approve button based on checklist
    function checkApprovalRequirements() {
        const allChecked = $('.approval-checklist input[type="checkbox"]').length === 
                          $('.approval-checklist input[type="checkbox"]:checked').length;
        const actionSelected = $('#action').val() !== '';
        
        $('#approveBtn').prop('disabled', !(allChecked && actionSelected));
    }

    $('.approval-checklist input[type="checkbox"], #action').change(checkApprovalRequirements);

    // Handle action change
    $('#action').change(function() {
        const action = $(this).val();
        const commentsField = $('#comments');
        
        if (action === 'approve') {
            commentsField.attr('placeholder', 'Add any final approval comments (optional)...');
            commentsField.prop('required', false);
        } else if (action === 'reject') {
            commentsField.attr('placeholder', 'Explain why this document is being rejected (required)...');
            commentsField.prop('required', true);
        }
        
        checkApprovalRequirements();
    });

    // Form validation
    $('form').on('submit', function(e) {
        const action = $('#action').val();
        const comments = $('#comments').val().trim();
        
        if (action === 'reject' && comments === '') {
            e.preventDefault();
            alert('Comments are required when rejecting a document.');
            $('#comments').focus();
            return false;
        }
        
        if (action === 'approve') {
            return confirm('Are you sure you want to approve this document? This will activate it for use.');
        } else if (action === 'reject') {
            return confirm('Are you sure you want to reject this document? This action cannot be easily undone.');
        }
    });
});
</script>
<?= $this->endSection() ?>