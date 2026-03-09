<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Document - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        body {
            font-size: 5px;
        }

        .document-content {
            /* background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin: 1rem 0; */
            min-height: 200px;
        }

        /* CKEditor content styles */
        .document-content table {
            border-collapse: collapse;
            width: 100%;
            margin: 0.5em 0;
        }

        .document-content table td,
        .document-content table th {
            border: 1px solid #bfbfbf;
            /* padding: 8px; */
            min-width: 2em;
        }

        .document-content table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .document-content img {
            max-width: 100%;
            height: auto;
        }

        .document-content figure {
            margin: 0.5em 0;
        }

        .document-content figure.image {
            display: table;
            clear: both;
            text-align: center;
            margin: 1em auto;
        }

        .document-content figure.image img {
            display: block;
            margin: 0 auto;
        }

        .document-content figure.image.image-style-side {
            float: right;
            margin-left: 1.5em;
            max-width: 50%;
        }

        .document-content figure.image.image_resized {
            max-width: 100%;
            display: block;
            box-sizing: border-box;
        }

        .document-content figure.image.image_resized img {
            width: 100%;
        }

        .document-content .image-style-align-left {
            float: left;
            margin-right: 1.5em;
        }

        .document-content .image-style-align-center {
            margin-left: auto;
            margin-right: auto;
        }

        .document-content .image-style-align-right {
            float: right;
            margin-left: 1.5em;
        }

        .document-content blockquote {
            border-left: 5px solid #ccc;
            font-style: italic;
            margin-left: 0;
            margin-right: 0;
            overflow: hidden;
            padding-left: 1.5em;
            padding-right: 1.5em;
        }

        /* Text alignment */
        .document-content .text-align-left {
            text-align: left !important;
        }

        .document-content .text-align-center {
            text-align: center !important;
        }

        .document-content .text-align-right {
            text-align: right !important;
        }

        .document-content .text-align-justify {
            text-align: justify !important;
        }

        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
        }

        .approval-status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .approval-status-sent_for_review {
            background-color: #17a2b8;
            color: #fff;
        }

        .approval-status-reviewed {
            background-color: #007bff;
            color: #fff;
        }

        .approval-status-approved {
            background-color: #28a745;
            color: #fff;
        }

        .approval-status-rejected {
            background-color: #dc3545;
            color: #fff;
        }

        .document-meta {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
        }

        .action-buttons {
            position: sticky;
            top: 20px;
            background: white;
            padding: 1rem;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }

        /* Approval Card Styles */
        .approval-card {
            border: 1px solid #e0e0e0;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
            transition: box-shadow 0.3s ease;
        }

        .approval-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        .approval-card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            border-bottom: 2px solid #0056b3;
        }

        .approval-card-body {
            padding: 1rem;
        }

        /* .approval-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f0f0f0;
        } */

        .approval-item:last-child {
            border-bottom: none;
        }

        .approval-item.mt-3 {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f0f0f0;
        }

        /* .approval-label {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
            min-width: 100px;
        } */

        /* .approval-value {
            color: #555;
            font-size: 0.9rem;
            text-align: right;
            flex: 1;
            margin-left: 1rem;
        } */

        .approval-comments {
            background: #f8f9fa;
            border-left: 3px solid #007bff;
            padding: 0.75rem;
            border-radius: 0.25rem;
            font-style: italic;
            color: #555;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Card Variants */
        .review-card .approval-card-header {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            border-bottom-color: #138496;
        }

        .review-card .approval-comments {
            border-left-color: #17a2b8;
        }

        .approval-card-main .approval-card-header {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            border-bottom-color: #1e7e34;
        }

        .approval-card-main .approval-comments {
            border-left-color: #28a745;
        }

        .rejection-card .approval-card-header {
            background: linear-gradient(135deg, #dc3545 0%, #bd2130 100%);
            border-bottom-color: #bd2130;
        }

        .rejection-card .approval-comments,
        .rejection-reason {
            border-left-color: #dc3545;
            background: #fff5f5;
        }

        .rejection-reason {
            border-left: 3px solid #dc3545;
            padding: 0.75rem;
            border-radius: 0.25rem;
            font-style: italic;
            color: #555;
            font-size: 0.9rem;
            line-height: 1.5;
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
                        'pageTitle' => '<i class="fas fa-file-alt me-2"></i>View Document',
                        'pageDescription' => 'Document Details and Content'
                    ]) ?>

                    <div class="row">
                        <!-- Document Content -->
                        <div class="col-lg-9">
                            <!-- Document Header Table (for print) -->
                            <table id="ssp-header-table" class="document-header-table" style="display: none;">
                                <thead>
                                    <tr>
                                        <td style="width: 20%;">
                                            <strong>Document ID:</strong><br>
                                            <?= esc($document['id']) ?>
                                        </td>
                                        <td style="width: 40%;">
                                            <strong><?= esc($document['title']) ?></strong><br>
                                            <small><?= esc($document['type_name']) ?></small>
                                        </td>
                                        <td style="width: 20%;">
                                            <strong>Department:</strong><br>
                                            <?= esc($document['department_name']) ?>
                                        </td>
                                        <td style="width: 20%;">
                                            <strong>Page:</strong><br>
                                            <span class="page-number"></span> of <span class="total-pages"></span>
                                        </td>
                                    </tr>
                                </thead>
                            </table>

                            <!-- Document Content -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-file-text me-2"></i>Document Content</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($fields) && !empty($formData)): ?>
                                        <!-- Display Form Data -->
                                        <?php foreach ($fields as $section => $sectionFields): ?>
                                            <h6 class="text-primary border-bottom pb-2 mb-3"><?= esc($section) ?></h6>
                                            <?php foreach ($sectionFields as $field): ?>
                                                <div class="mb-3">
                                                    <strong><?= esc($field['field_label']) ?>:</strong>
                                                    <div class="document-content mt-1">
                                                        <?php
                                                        $fieldName = $field['field_name'];
                                                        $value = $formData[$fieldName] ?? '';

                                                        if ($field['field_type'] === 'table' && is_array($value)) {
                                                            // Render table data
                                                            $columns = json_decode($field['options'], true);
                                                            echo '<table class="table table-sm table-bordered">';
                                                            echo '<thead><tr>';
                                                            foreach ($columns as $col) {
                                                                echo '<th>' . esc($col['label']) . '</th>';
                                                            }
                                                            echo '</tr></thead><tbody>';
                                                            foreach ($value as $row) {
                                                                echo '<tr>';
                                                                foreach ($columns as $col) {
                                                                    echo '<td>' . esc($row[$col['name']] ?? '') . '</td>';
                                                                }
                                                                echo '</tr>';
                                                            }
                                                            echo '</tbody></table>';
                                                        } elseif ($field['field_type'] === 'checkbox') {
                                                            echo $value ? '<i class="fas fa-check-square text-success"></i> Yes' : '<i class="far fa-square"></i> No';
                                                        } else {
                                                            echo nl2br(esc($value ?: 'N/A'));
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <!-- Display Regular Content -->
                                        <div class="document-content">
                                            <?php if (!empty($document['content'])): ?>
                                                <?= $document['content']; ?>
                                            <?php else: ?>
                                                <p class="text-muted"><em>No content available for this document.</em></p>
                                            <?php endif ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Attachments Section -->
                            <?php
                            // Get attachments for this document
                            $attachmentModel = new \App\Models\DocumentAttachment();
                            $attachments = $attachmentModel->getDocumentAttachmentsWithUploaders($document['id']);
                            ?>
                            <?php if (!empty($attachments)): ?>
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachments (<?= count($attachments) ?>)</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group">
                                            <?php foreach ($attachments as $attachment): ?>
                                                <div class="list-group-item">
                                                    <?php 
                                                    $ext = strtolower(pathinfo($attachment['file_path'], PATHINFO_EXTENSION));
                                                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                        <div class="mb-3 text-center bg-light p-2 border rounded">
                                                            <a href="<?= base_url($attachment['file_path']) ?>" target="_blank">
                                                                <img src="<?= base_url($attachment['file_path']) ?>" alt="<?= esc($attachment['file_name']) ?>" class="img-fluid rounded shadow-sm" style="max-height: 400px; object-fit: contain;">
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                                        <div class="flex-grow-1">
                                                            <div class="mb-2">
                                                                <a href="<?= base_url($attachment['file_path']) ?>" target="_blank" class="text-decoration-none">
                                                                    <i class="<?= \App\Models\DocumentAttachment::getFileIcon($attachment['file_type']) ?> me-2"></i>
                                                                    <strong><?= esc($attachment['file_name']) ?></strong>
                                                                </a>
                                                            </div>
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-upload me-1"></i>
                                                                Uploaded by: <strong><?= esc($attachment['name'] ?? $attachment['username']) ?></strong>
                                                                | Size: <strong><?= \App\Models\DocumentAttachment::formatFileSize($attachment['file_size']) ?></strong>
                                                                | Date: <strong><?= date('M d, Y H:i', strtotime($attachment['created_at'])) ?></strong>
                                                            </small>
                                                        </div>
                                                        <div>
                                                            <?php 
                                                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                                <!-- <button type="button" class="btn btn-sm btn-info text-white ms-1" data-bs-toggle="modal" data-bs-target="#imageModal<?= $attachment['id'] ?>">
                                                                    <i class="fas fa-eye me-1"></i>Preview
                                                                </button> -->
                                                            <?php elseif ($ext === 'pdf'): ?>
                                                                <button type="button" class="btn btn-sm btn-info text-white ms-1" data-bs-toggle="modal" data-bs-target="#pdfModal<?= $attachment['id'] ?>">
                                                                    <i class="fas fa-eye me-1"></i>Preview
                                                                </button>
                                                            <?php endif; ?>
                                                            <?php if ($role_name === 'admin' || $role_name === 'superadmin' || $document['created_by'] == session()->get('user_id') || $attachment['uploaded_by'] == session()->get('user_id')): ?>
                                                                <button type="button" class="btn btn-sm btn-danger ms-1" onclick="deleteAttachment(<?= $attachment['id'] ?>)">
                                                                    <i class="fas fa-trash me-1"></i>Delete
                                                                </button>
                                                            <?php endif; ?>
                                                            <a href="<?= base_url($attachment['file_path']) ?>" class="btn btn-sm btn-primary" download>
                                                                <i class="fas fa-download me-1"></i>Download
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Action Sidebar -->
                        <div class="col-lg-3">
                            <div class="action-buttons">
                                <h5 class="mb-3"><i class="fas fa-cogs me-2"></i>Actions</h5>

                                <!-- Navigation Actions -->
                                <div class="d-grid gap-2 mb-3">
                                    <a href="<?= base_url('documents') ?>" class="btn btn-secondary btn-sm">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Documents
                                    </a>
                                    <a href="<?= base_url('approval-dashboard') ?>" class="btn btn-info btn-sm">
                                        <i class="fas fa-tachometer-alt me-2"></i>Approval Dashboard
                                    </a>
                                </div>

                                <!-- Export Actions -->
                                <h6 class="mb-2"><i class="fas fa-file-export me-2"></i>Preview & Export</h6>
                                <div class="d-grid gap-2 mb-3">
                                    <a href="<?= base_url('documents/export-pdf/' . $document['id']) ?>"
                                        class="btn btn-danger btn-sm" target="_blank">
                                        <i class="fas fa-file-pdf me-2"></i>Export as PDF
                                    </a>
                                    <!-- <button onclick="printDocument()" class="btn btn-primary btn-sm">
                                        <i class="fas fa-print me-2"></i>Print Document
                                    </button> -->
                                </div>

                                <!-- Document Actions -->
                                <?php if ($role_name === 'admin' || $role_name === 'superadmin' || $document['created_by'] == session()->get('user_id')): ?>
                                    <div class="d-grid gap-2 mb-3">
                                        <?php if ($document['approval_status'] !== 'approved'): ?>
                                            <a href="<?= base_url('documents/edit/' . $document['id']) ?>"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-2"></i>Edit Document
                                            </a>
                                        <?php endif ?>
                                    </div>
                                <?php endif ?>

                                <?php if ($document['approval_status'] === 'sent_for_review' && ($role_name === 'reviewer' || $role_name === 'lab_manager' || $role_name === 'superadmin')): ?>
                                    <div class="d-grid gap-2 mb-3">
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#reviewModal">
                                            <i class="fas fa-clipboard-check me-2"></i>Review Document
                                        </button>
                                    </div>
                                <?php endif ?>

                                <?php if ($document['approval_status'] === 'sent_for_approval' && ($role_name === 'approver')): ?>
                                    <div class="d-grid gap-2 mb-3">
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#approvalModal">
                                            <i class="fas fa-check-circle me-2"></i>Approval
                                        </button>
                                    </div>
                                <?php endif ?>

                                <?php if ($document['approval_status'] === 'approved_by_approver' && ($role_name === 'admin' || $role_name === 'superadmin')): ?>
                                    <div class="d-grid gap-2 mb-3">
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#adminApprovalModal">
                                            <i class="fas fa-check-circle me-2"></i>Final Approval
                                        </button>
                                    </div>
                                <?php endif ?>

                                <!-- History and Audit -->
                                <!-- <div class="d-grid gap-2 mb-3">
                                    <a href="<?= base_url('documents/approval-history/' . $document['id']) ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-history me-2"></i>Approval History
                                                </a>
                                            </div> -->

                                <!-- Admin Actions -->
                                <?php if ($role_name === 'admin' || $role_name === 'superadmin'): ?>
                                    <hr>
                                    <h6 class="mb-2">Admin Actions</h6>
                                    <div class="d-grid gap-2">
                                        <?php if ($document['approval_status'] === 'approved' && $document['status'] === 'active'): ?>
                                            <button class="btn btn-warning btn-sm"
                                                onclick="confirmLock(<?= $document['id'] ?>)">
                                                <i class="fas fa-lock me-2"></i>Lock as Obsolete
                                            </button>
                                        <?php endif ?>

                                        <?php if ($document['status'] === 'draft'): ?>
                                            <button class="btn btn-danger btn-sm"
                                                onclick="confirmDelete(<?= $document['id'] ?>)">
                                                <i class="fas fa-trash me-2"></i>Delete Document
                                            </button>
                                        <?php endif ?>
                                    </div>
                                <?php endif ?>


                                <!-- Approval Information -->
                                <?php if ($document['approval_status'] !== 'pending'): ?>
                                    <div class="card mt-4">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Approval Information
                                            </h6>
                                        </div>
                                        <div class="card-body" style="font-size: small;">
                                            <!-- Review Information -->
                                            <?php if ($document['reviewer_id']): ?>
                                                <div class="mb-4 pb-4 border-bottom">
                                                    <h6 class="text-info mb-3"><i class="fas fa-search me-2"></i>Review
                                                        Information</h6>
                                                    <div class="approval-item">
                                                        <span class="approval-label">Reviewer:</span>
                                                        <span
                                                            class="approval-value"><?= esc($document['reviewer_name'] ?? 'Not assigned') ?></span>
                                                    </div>
                                                    <?php if ($document['submitted_for_review_at']): ?>
                                                        <div class="approval-item">
                                                            <span class="approval-label">Submitted:</span>
                                                            <span
                                                                class="approval-value"><?= date('M j, Y g:i A', strtotime($document['submitted_for_review_at'])) ?></span>
                                                        </div>
                                                    <?php endif ?>
                                                    <?php if ($document['reviewed_at']): ?>
                                                        <div class="approval-item">
                                                            <span class="approval-label">Reviewed:</span>
                                                            <span
                                                                class="approval-value"><?= date('M j, Y g:i A', strtotime($document['reviewed_at'])) ?></span>
                                                        </div>
                                                    <?php endif ?>
                                                    <?php if ($document['reviewer_comments']): ?>
                                                        <div class="approval-item mt-3">
                                                            <span class="approval-label d-block mb-2">Comments:</span>
                                                            <div class="approval-comments">
                                                                <?= nl2br(esc($document['reviewer_comments'])) ?>
                                                            </div>
                                                        </div>
                                                    <?php endif ?>
                                                </div>
                                            <?php endif ?>

                                            <!-- Approval Information -->
                                            <?php if ($document['approver_id']): ?>
                                                <div class="mb-4 pb-4 border-bottom">
                                                    <h6 class="text-success mb-3"><i
                                                            class="fas fa-check-circle me-2"></i>Approval Information</h6>
                                                    <div class="approval-item">
                                                        <span class="approval-label">Approver:</span>
                                                        <span
                                                            class="approval-value"><?= esc($document['approver_name'] ?? 'Not assigned') ?></span>
                                                    </div>
                                                    <?php if ($document['approved_at']): ?>
                                                        <div class="approval-item">
                                                            <span class="approval-label">Approved:</span>
                                                            <span
                                                                class="approval-value"><?= date('M j, Y g:i A', strtotime($document['approved_at'])) ?></span>
                                                        </div>
                                                    <?php endif ?>
                                                    <?php if ($document['approver_comments']): ?>
                                                        <div class="approval-item mt-3">
                                                            <span class="approval-label d-block mb-2">Comments:</span>
                                                            <div class="approval-comments">
                                                                <?= nl2br(esc($document['approver_comments'])) ?>
                                                            </div>
                                                        </div>
                                                    <?php endif ?>
                                                </div>
                                            <?php endif ?>

                                            <!-- Rejection Information -->
                                            <?php if ($document['approval_status'] === 'rejected'): ?>
                                                <div class="mb-0">
                                                    <h6 class="text-danger mb-3"><i
                                                            class="fas fa-times-circle me-2"></i>Rejection Information</h6>
                                                    <?php if ($document['rejected_at']): ?>
                                                        <div class="approval-item">
                                                            <span class="approval-label">Rejected:</span>
                                                            <span
                                                                class="approval-value"><?= date('M j, Y g:i A', strtotime($document['rejected_at'])) ?></span>
                                                        </div>
                                                    <?php endif ?>
                                                    <?php if ($document['rejection_reason']): ?>
                                                        <div class="approval-item mt-3">
                                                            <span class="approval-label d-block mb-2">Reason:</span>
                                                            <div class="approval-comments rejection-reason">
                                                                <?= nl2br(esc($document['rejection_reason'])) ?>
                                                            </div>
                                                        </div>
                                                    <?php endif ?>
                                                </div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>

                        <!-- Review Modal -->
                        <div class="modal fade" id="reviewModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Review Document</h5>
                                        <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Document: <strong><?= esc($document['title']) ?></strong></p>
                                        <div class="mb-3">
                                            <label class="form-label">Select Action:</label>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-success"
                                                    onclick="setReviewAction('approve_for_final')">
                                                    <i class="fas fa-check me-2"></i>Approve for Final
                                                </button>
                                                <button type="button" class="btn btn-warning"
                                                    onclick="setReviewAction('return_for_revision')">
                                                    <i class="fas fa-undo me-2"></i>Return for Revision
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                    onclick="setReviewAction('reject')">
                                                    <i class="fas fa-times me-2"></i>Reject Document
                                                </button>
                                            </div>
                                        </div>
                                        <div id="review-action-section" style="display: none;">
                                            <hr>
                                            <p>Action: <span id="review-action-text"
                                                    class="badge bg-info"></span></p>
                                            <div class="mb-3">
                                                <label for="review-comments" class="form-label">Comments <span
                                                        class="text-muted">(Optional)</span></label>
                                                <textarea class="form-control" id="review-comments" rows="4"
                                                    placeholder="Enter your comments..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="confirm-review-btn"
                                            onclick="submitReview()" style="display: none;">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Modal -->
                        <div class="modal fade" id="approvalModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Approval</h5>
                                        <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Document: <strong><?= esc($document['title']) ?></strong></p>
                                        <div class="mb-3">
                                            <label class="form-label">Select Action:</label>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-success"
                                                    onclick="setApprovalAction('approve')">
                                                    <i class="fas fa-check-circle me-2"></i>Approve
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                    onclick="setApprovalAction('reject')">
                                                    <i class="fas fa-times-circle me-2"></i>Reject
                                                </button>
                                            </div>
                                        </div>
                                        <div id="approval-action-section" style="display: none;">
                                            <hr>
                                            <p>Action: <span id="approval-action-text"
                                                    class="badge bg-info"></span></p>
                                            <div class="mb-3">
                                                <label for="approval-comments" class="form-label">Comments <span
                                                        class="text-muted">(Optional for approval, Required for
                                                        rejection)</span></label>
                                                <textarea class="form-control" id="approval-comments" rows="4"
                                                    placeholder="Enter your comments..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="confirm-approval-btn"
                                            onclick="submitApproval()" style="display: none;">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Approval Modal -->
                        <div class="modal fade" id="adminApprovalModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Final Approval</h5>
                                        <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Document: <strong><?= esc($document['title']) ?></strong></p>
                                        <div class="mb-3">
                                            <label class="form-label">Select Action:</label>
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-success"
                                                    onclick="setAdminApprovalAction('approve')">
                                                    <i class="fas fa-check-circle me-2"></i>Approve
                                                </button>
                                                <button type="button" class="btn btn-danger"
                                                    onclick="setAdminApprovalAction('reject')">
                                                    <i class="fas fa-times-circle me-2"></i>Reject
                                                </button>
                                            </div>
                                        </div>
                                        <div id="admin-approval-action-section" style="display: none;">
                                            <hr>
                                            <p>Action: <span id="admin-approval-action-text"
                                                    class="badge bg-info"></span></p>
                                            <div class="mb-3">
                                                <label for="admin-approval-comments" class="form-label">Comments
                                                    <span class="text-muted">(Optional for approval, Required
                                                        for rejection)</span></label>
                                                <textarea class="form-control" id="admin-approval-comments"
                                                    rows="4" placeholder="Enter your comments..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary"
                                            id="confirm-admin-approval-btn" onclick="submitAdminApproval()"
                                            style="display: none;">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            let currentReviewAction = null;
                            let currentApprovalAction = null;
                            const documentId = <?= $document['id'] ?>;

                            function setReviewAction(action) {
                                currentReviewAction = action;
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
                                const actionInfo = actionTexts[action];
                                document.getElementById('review-action-text').textContent = actionInfo.text;
                                document.getElementById('review-action-text').className = 'badge ' + actionInfo.class;
                                document.getElementById('review-comments').value = '';
                                document.getElementById('review-action-section').style.display = 'block';
                                document.getElementById('confirm-review-btn').style.display = 'block';
                            }

                            function setApprovalAction(action) {
                                currentApprovalAction = action;
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
                                const actionInfo = actionTexts[action];
                                document.getElementById('approval-action-text').textContent = actionInfo.text;
                                document.getElementById('approval-action-text').className = 'badge ' + actionInfo.class;
                                document.getElementById('approval-comments').value = '';
                                document.getElementById('approval-action-section').style.display = 'block';
                                document.getElementById('confirm-approval-btn').style.display = 'block';
                            }

                            let currentAdminApprovalAction = null;

                            function setAdminApprovalAction(action) {
                                currentAdminApprovalAction = action;
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
                                const actionInfo = actionTexts[action];
                                document.getElementById('admin-approval-action-text').textContent = actionInfo.text;
                                document.getElementById('admin-approval-action-text').className = 'badge ' + actionInfo.class;
                                document.getElementById('admin-approval-comments').value = '';
                                document.getElementById('admin-approval-action-section').style.display = 'block';
                                document.getElementById('confirm-admin-approval-btn').style.display = 'block';
                            }

                            function submitAdminApproval() {
                                const comments = document.getElementById('admin-approval-comments').value;

                                if (currentAdminApprovalAction === 'reject' && !comments.trim()) {
                                    alert('Comments are required for rejection');
                                    return;
                                }

                                // Disable button to prevent double submission
                                const btn = document.getElementById('confirm-admin-approval-btn');
                                btn.disabled = true;
                                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                                // Use AJAX to submit
                                fetch('<?= base_url('documents/quick-approve') ?>/' + documentId, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        body: new URLSearchParams({
                                            'action': currentAdminApprovalAction,
                                            'comments': comments
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert(data.message);
                                            location.reload();
                                        } else {
                                            alert('Error: ' + data.message);
                                            btn.disabled = false;
                                            btn.innerHTML = 'Confirm';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('An error occurred while processing your request');
                                        btn.disabled = false;
                                        btn.innerHTML = 'Confirm';
                                    });
                            }

                            function submitReview() {
                                const comments = document.getElementById('review-comments').value;

                                if (currentReviewAction === 'reject' && !comments.trim()) {
                                    alert('Comments are required for rejection');
                                    return;
                                }

                                // Disable button to prevent double submission
                                const btn = document.getElementById('confirm-review-btn');
                                btn.disabled = true;
                                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                                // Use AJAX to submit
                                fetch('<?= base_url('documents/quick-review') ?>/' + documentId, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        body: new URLSearchParams({
                                            'action': currentReviewAction,
                                            'comments': comments
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert(data.message);
                                            location.reload();
                                        } else {
                                            alert('Error: ' + data.message);
                                            btn.disabled = false;
                                            btn.innerHTML = 'Confirm';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('An error occurred while processing your request');
                                        btn.disabled = false;
                                        btn.innerHTML = 'Confirm';
                                    });
                            }

                            function submitApproval() {
                                const comments = document.getElementById('approval-comments').value;

                                if (currentApprovalAction === 'reject' && !comments.trim()) {
                                    alert('Comments are required for rejection');
                                    return;
                                }

                                // Disable button to prevent double submission
                                const btn = document.getElementById('confirm-approval-btn');
                                btn.disabled = true;
                                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                                // Use AJAX to submit
                                fetch('<?= base_url('documents/quick-approve') ?>/' + documentId, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        body: new URLSearchParams({
                                            'action': currentApprovalAction,
                                            'comments': comments
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert(data.message);
                                            location.reload();
                                        } else {
                                            alert('Error: ' + data.message);
                                            btn.disabled = false;
                                            btn.innerHTML = 'Confirm';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('An error occurred while processing your request');
                                        btn.disabled = false;
                                        btn.innerHTML = 'Confirm';
                                    });
                            }

                            function confirmLock(documentId) {
                                if (confirm('Are you sure you want to lock this document as obsolete? This action cannot be undone.')) {
                                    // Create a form and submit it
                                    const form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = '<?= base_url('documents/lock') ?>/' + documentId;

                                    // A    dd CSRF token if available
                                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                                    if (csrfToken) {
                                        const csrfInput = document.createElement('input');
                                        csrfInput.type = 'hidden';
                                        csrfInput.name = 'csrf_token';
                                        csrfInput.value = csrfToken.getAttribute('content');
                                        form.appendChild(csrfInput);
                                    }

                                    document.body.appendChild(form);
                                    form.submit();
                                }
                            }


                            function confirmDelete(documentId) {
                                if (confirm('Are you sure you want to delete this document? This action cannot be undone.')) {
                                    window.location.href = '<?= base_url('documents/delete') ?>/' + documentId;
                                }
                            }

                            function deleteAttachment(attachmentId) {
                                if (confirm('Are you sure you want to delete this attachment? This action cannot be undone.')) {
                                    // Make fetch request
                                    fetch('<?= base_url('attachments/delete') ?>/' + attachmentId, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                alert('Attachment deleted successfully');
                                                location.reload(); // Reload to update UI
                                            } else {
                                                alert('Error deleting attachment: ' + (data.message || 'Unknown error'));
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            alert('An error occurred while deleting the attachment');
                                        });
                                }
                            }
                        </script>

                        <!-- PDF Generation Libraries -->
                        <script
                            src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
                        <scr ipt src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js">
                            </script>

                            <script>
                                // P      rint document function
                                function printDocument() {
                                    // Add print-mode class to body to trigger print styles
                                    document.body.classList.add('print-mode');

                                    window.print();

                                    // Remove print-mode class after print dialog closes
                                    setTimeout(() => {
                                        document.body.classList.remove('print-mode');
                                    }, 1000);
                                }

                                // Generate PDF function
                                async function generatePDF() {
                                    const {
                                        jsPDF
                                    } = window.jspdf;

                                    // Show loading indicator
                                    const btn = event.target.closest('button');
                                    const originalHTML = btn.innerHTML;
                                    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating PDF...';
                                    btn.disabled = true;

                                    try {
                                        // Get the document content area
                                        const contentArea = document.querySelector('.col-lg-9');

                                        // Hide action buttons temporarily
                                        const actionButtons = document.querySelector('.action-buttons');
                                        if (actionButtons) actionButtons.style.display = 'none';

                                        // C       reate canvas from HTML
                                        const canvas = await html2canvas(contentArea, {
                                            scale: 2,
                                            useCORS: true,
                                            logging: false,
                                            backgroundColor: '#ffffff'
                                        });

                                        // Restore action buttons
                                        if (actionButtons) actionButtons.style.display = 'block';

                                        // Calculate PDF dimensions
                                        const imgWidth = 210; // A4 width in mm
                                        const pageHeight = 297; // A4 height in mm
                                        const imgHeight = (canvas.height * imgWidth) / canvas.width;
                                        let heightLeft = imgHeight;

                                        const pdf = new jsPDF('p', 'mm', 'a4');
                                        let position = 0;

                                        // Add image to PDF
                                        const imgData = canvas.toDataURL('image/png');
                                        pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                                        heightLeft -= pageHeight;

                                        // Add new pages if content is longer than one page
                                        while (heightLeft >= 0) {
                                            position = heightLeft - imgHeight;
                                            pdf.addPage();
                                            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                                            heightLeft -= pageHeight;
                                        }

                                        // Save PDF
                                        const filename = '<?= preg_replace('/[^a-zA-Z0-9_-]/', '_', $document['title']) ?>.pdf';
                                        pdf.save(filename);

                                    } catch (error) {
                                        console.error('Error generating PDF:', error);
                                        alert('Error generating PDF. Please try again.');
                                    } finally {
                                        // Restore button
                                        btn.innerHTML = originalHTML;
                                        btn.disabled = false;
                                    }
                                }
                            </script>

                            <style>
                                /* Print styles - show only document content */
                                @media print {

                                    /* Hide everything by default */
                                    body * {
                                        visibility: hidden;
                                    }

                                    /* Show the document content area and approval block */
                                    .col-lg-9,
                                    .col-lg-9 * {
                                        visibility: visible;
                                    }

                                    /* Position content area at top of page */
                                    .col-lg-9 {
                                        position: absolute;
                                        left: 0;
                                        top: 5;
                                        width: 100%;
                                        background: white !important;
                                        padding: 0;
                                        margin: 0;
                                    }

                                    /* Hide card headers and borders */
                                    .card {
                                        border: none !important;
                                        box-shadow: none !important;
                                        padding: 0 !important;
                                        margin: 0 !important;
                                    }

                                    .card-header {
                                        display: none !important;
                                    }

                                    .card-body {
                                        padding: 0 !important;
                                        margin: 0 !important;
                                    }

                                    /* Document header table - show on first page only in flow, then repeat as running header */
                                    .document-header-table {
                                        border-collapse: collapse !important;
                                        width: 100%;
                                        margin-bottom: 20px;
                                        page-break-after: avoid;
                                    }

                                    .document-header-table td {
                                        border: 1px solid #000 !important;
                                        padding: 8px;
                                        text-align: center;
                                    }

                                    /* Create a running header using thead */
                                    .document-header-table thead {
                                        display: table-header-group;
                                    }

                                    /* Remove padding and borders from document content */
                                    .document-content {
                                        background: transparent !important;
                                        border: none !important;
                                        padding: 0 !important;
                                        margin: 0 !important;
                                        page-break-inside: avoid;
                                    }

                                    .document-content table {
                                        page-break-inside: auto;
                                    }

                                    .document-content tr {
                                        page-break-inside: avoid;
                                        page-break-after: auto;
                                    }

                                    /* Force page breaks for content sections */
                                    @page {
                                        margin-top: 0;
                                        margin-bottom: 20mm;
                                    }
                                }

                                /* Alternative: Using print-mode class for more control */
                                body.print-mode .col-lg-2,
                                body.print-mode .action-buttons,
                                body.print-mode .card.docheader,
                                body.print-mode .card-header,
                                body.print-mode .main-content>div:not(.row),
                                body .print-mode header,
                                body.print-mode footer,
                                body.print-mode nav {
                                    display: none !important;
                                }
                            </style>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteAttachment(attachmentId) {
            if (confirm('Are you sure you want to delete this attachment?')) {
                fetch('<?= base_url('attachments/delete') ?>/' + attachmentId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting attachment');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the attachment');
                });
            }
        }
    </script>
    
    <!-- Attachment Modals -->
    <?php if (!empty($attachments)): ?>
        <?php foreach ($attachments as $attachment): ?>
            <?php 
            $ext = strtolower(pathinfo($attachment['file_path'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): 
            ?>
            <!-- Image Modal -->
            <div class="modal fade" id="imageModal<?= $attachment['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= esc($attachment['file_name']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body text-center p-0">
                            <img src="<?= base_url($attachment['file_path']) ?>" class="img-fluid" alt="<?= esc($attachment['file_name']) ?>">
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif ($ext === 'pdf'): ?>
            <!-- PDF Modal -->
            <div class="modal fade" id="pdfModal<?= $attachment['id'] ?>" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
                    <div class="modal-content h-100">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= esc($attachment['file_name']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0 h-100">
                            <object data="<?= base_url($attachment['file_path']) ?>" type="application/pdf" width="100%" height="100%" style="min-height: 75vh;">
                                <iframe src="<?= base_url($attachment['file_path']) ?>" width="100%" height="100%" style="border: none; min-height: 75vh;">
                                    <p>Your browser does not support PDFs. 
                                        <a href="<?= base_url($attachment['file_path']) ?>" target="_blank">Download the PDF</a>.
                                    </p>
                                </iframe>
                            </object>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>