<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Submit for Review<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paper-plane"></i> Submit Document for Review
                    </h3>
                </div>
                <div class="card-body">
                    <div class="document-info mb-4">
                        <h4><?= esc($document['title']) ?></h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Type:</strong> <?= esc($document['type_name']) ?></p>
                                <p><strong>Department:</strong> <?= esc($document['department_name']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Created By:</strong> <?= esc($document['created_by_name']) ?></p>
                                <p><strong>Created Date:</strong> <?= date('M j, Y', strtotime($document['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>

                    <?= form_open('documents/process-submit-for-review/' . $document['id']) ?>
                    
                    <div class="form-group">
                        <label for="reviewer_id" class="required">Select Reviewer</label>
                        <select class="form-control" id="reviewer_id" name="reviewer_id" required>
                            <option value="">Choose a reviewer...</option>
                            <?php foreach ($reviewers as $reviewer): ?>
                                <option value="<?= $reviewer['id'] ?>">
                                    <?= esc($reviewer['username']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <?php if (empty($reviewers)): ?>
                            <small class="text-danger">No reviewers available in this department. Please contact your administrator.</small>
                        <?php else: ?>
                            <small class="text-muted">Select a qualified reviewer from your department.</small>
                        <?php endif ?>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Review Process Information</h6>
                        <ul class="mb-0">
                            <li>The selected reviewer will be notified of the assignment</li>
                            <li>The reviewer can approve for final approval, request revisions, or reject the document</li>
                            <li>You will be notified of the review decision</li>
                            <li>After review approval, the document will need final approval from an Admin</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="confirm_ready" required>
                            <label class="form-check-label" for="confirm_ready">
                                I confirm that this document is ready for review and meets quality standards
                            </label>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?= base_url('documents') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary" <?= empty($reviewers) ? 'disabled' : '' ?>>
                                    <i class="fas fa-paper-plane"></i> Submit for Review
                                </button>
                            </div>
                        </div>
                    </div>

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.required::after {
    content: " *";
    color: red;
}

.document-info {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 15px;
}
</style>
<?= $this->endSection() ?>