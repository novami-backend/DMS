<?php

if (! function_exists('getActionColor')) {
    function getActionColor(string $action): string
    {
        $colors = [
            'submitted_for_review' => 'primary',
            'reviewed'             => 'info',
            'approved'             => 'success',
            'rejected'             => 'danger',
            'return_for_revision'  => 'warning',
        ];
        return $colors[$action] ?? 'secondary';
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-2 col-md-3 p-0">
            <?= view('common/sidebar') ?>
        </div>

        <!-- Main Content -->
        <div class="p-3">
            <div class="main-content">
                <!-- Header -->
                <?= view('common/header', [
                    'pageTitle' => '<i class="fas fa-file-alt me-2"></i>Review Document',
                    'pageDescription' => 'Review Documents'
                ]) ?>

                <div class="row">
                    <!-- Left Side: Document Info + Content -->
                    <div class="col-lg-8">
                        <div class="document-info mb-4 border-bottom pb-3">
                            <h4><?= esc($document['title']) ?></h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Type:</strong> <?= esc($document['type_name']) ?></p>
                                    <p><strong>Department:</strong> <?= esc($document['department_name']) ?></p>
                                    <p><strong>Created By:</strong> <?= esc($document['created_by_name']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Created Date:</strong> <?= date('M j, Y', strtotime($document['created_at'])) ?></p>
                                    <p><strong>Status:</strong>
                                        <span class="badge bg-warning"><?= ucfirst($document['status']) ?></span>
                                    </p>
                                    <p><strong>Approval Status:</strong>
                                        <span class="badge bg-info"><?= ucfirst(str_replace('_', ' ', $document['approval_status'])) ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="document-content mb-4">
                            <h5>Document Content</h5>
                            <div class="border p-3 bg-light">
                            <?= $document['content'] ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Review Panel + Approval History -->
                    <div class="col-lg-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fas fa-clipboard-check"></i> Review Actions</h5>
                            </div>
                            <?= form_open('documents/process-review/' . $document['id']) ?>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="action" class="form-label required">Review Decision</label>
                                    <select class="form-select" id="action" name="action" required>
                                        <option value="">Select Action</option>
                                        <option value="approve_for_final">Approve & Forward to Approver</option>
                                        <option value="return_to_creator">Return for Revision (to Creator)</option>
                                        <option value="reject">Reject Document</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="comments" class="form-label required">Comments</label>
                                    <textarea class="form-control" id="comments" name="comments" rows="5"
                                        placeholder="Provide detailed comments..." required></textarea>
                                    <small class="text-muted">Your comments will be visible to the document creator and approvers.</small>
                                </div>
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Review Guidelines</h6>
                                    <ul class="mb-0">
                                        <li>Check document accuracy and completeness</li>
                                        <li>Verify compliance with standards</li>
                                        <li>Ensure proper formatting and clarity</li>
                                        <li>Validate technical content</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-footer d-grid gap-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-check"></i> Submit Review
                                </button>
                                <a href="<?= base_url('documents/my-reviews') ?>" class="btn btn-secondary w-100">
                                    <i class="fas fa-arrow-left"></i> Back to My Reviews
                                </a>
                            </div>
                            <?= form_close() ?>
                        </div>

                        <?php if (!empty($approval_history)): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="fas fa-history"></i> Approval History</h5>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('common/footer') ?>
<?= view('common/scripts') ?>
<script>
    $(document).ready(function() {
        $('#action').change(function() {
            const action = $(this).val();
            const commentsField = $('#comments');
            if (action === 'approve_for_final') {
                commentsField.attr('placeholder', 'Provide any final comments or recommendations...');
            } else if (action === 'return_to_creator' || action === 'return_for_revision') {
                commentsField.attr('placeholder', 'Specify what needs to be revised...');
            } else if (action === 'reject') {
                commentsField.attr('placeholder', 'Explain why this document is being rejected...');
            }
        });
    });
</script>
</body>

</html>