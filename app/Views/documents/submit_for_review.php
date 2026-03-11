<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit for Review - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
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
                        'pageTitle' => '<i class="fas fa-paper-plane me-2"></i>Submit Document for Review',
                        'pageDescription' => 'Request review for your document'
                    ]) ?>

                    <div class="p-4">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white">
                                        <h3 class="card-title mb-0">
                                            <i class="fas fa-info-circle text-primary me-2"></i> Document Submission
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
                                        
                                        <div class="mb-3">
                                            <label for="reviewer_id" class="form-label required">Select Reviewer</label>
                                            <select class="form-select" id="reviewer_id" name="reviewer_id" required>
                                                <option value="">Choose a reviewer...</option>
                                                <?php foreach ($reviewers as $reviewer): ?>
                                                    <option value="<?= $reviewer['id'] ?>">
                                                        <?= esc($reviewer['username']) ?>
                                                    </option>
                                                <?php endforeach ?>
                                            </select>
                                            <?php if (empty($reviewers)): ?>
                                                <small class="text-danger d-block mt-1">No reviewers available in this department. Please contact your administrator.</small>
                                            <?php else: ?>
                                                <small class="text-muted">Select a qualified reviewer from your department.</small>
                                            <?php endif ?>
                                        </div>

                                        <div class="alert alert-info">
                                            <h6><i class="fas fa-info-circle"></i> Review Process Information</h6>
                                            <ul class="mb-0 small">
                                                <li>The selected reviewer will be notified of the assignment</li>
                                                <li>The reviewer can approve for final approval, request revisions, or reject the document</li>
                                                <li>You will be notified of the review decision</li>
                                                <li>After review approval, the document will need final approval from an Admin</li>
                                            </ul>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="confirm_ready" required>
                                                <label class="form-check-label" for="confirm_ready">
                                                    I confirm that this document is ready for review and meets quality standards
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mt-4">
                                            <a href="<?= base_url('documents') ?>" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left"></i> Cancel
                                            </a>
                                            <button type="submit" class="btn btn-primary" <?= empty($reviewers) ? 'disabled' : '' ?>>
                                                <i class="fas fa-paper-plane"></i> Submit for Review
                                            </button>
                                        </div>

                                        <?= form_close() ?>
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

    <?= view('common/scripts') ?>
</body>

</html>