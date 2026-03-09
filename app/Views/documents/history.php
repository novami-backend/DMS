<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document History - <?= esc($document['title']) ?> - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .version-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s;
        }
        .version-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .current-version {
            border-left-color: #28a745;
            background-color: rgba(40, 167, 69, 0.05);
        }
        .version-content {
            max-height: 200px;
            overflow-y: auto;
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
                        'pageTitle' => '<i class="fas fa-history me-2"></i>Document History: ' . esc($document['title']),
                        'pageDescription' => 'View and manage document versions'
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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVersionModal">
                            <i class="fas fa-plus me-2"></i>Create New Version
                        </button>
                    </div>

                    <!-- Document Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
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
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><span class="badge status-<?= $document['status'] ?> status-badge"><?= ucfirst($document['status']) ?></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Version Statistics</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h3 class="mb-0"><?= count($versions) ?></h3>
                                                <small class="text-muted">Total Versions</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h3 class="mb-0">1.<?= count($versions) ?></h3>
                                                <small class="text-muted">Current Version</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Version History -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-code-branch me-2"></i>Version History
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($versions)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-code-branch fa-3x text-muted mb-3"></i>
                                    <h4>No versions found</h4>
                                    <p class="text-muted">This document has not been versioned yet</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVersionModal">
                                        <i class="fas fa-plus me-2"></i>Create First Version
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($versions as $index => $version): ?>
                                        <div class="col-12 mb-3">
                                            <div class="card version-card <?= $index === 0 ? 'current-version' : '' ?>">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <h5 class="card-title mb-0">
                                                                    Version <?= esc($version['version_number']) ?>
                                                                    <?php if ($index === 0): ?>
                                                                        <span class="badge bg-success ms-2">Current</span>
                                                                    <?php endif; ?>
                                                                </h5>
                                                                <small class="text-muted ms-3">
                                                                    by <?= esc($version['created_by_name']) ?> on 
                                                                    <?= date('M d, Y H:i', strtotime($version['created_at'])) ?>
                                                                </small>
                                                            </div>
                                                            
                                                            <?php if (!empty($version['changes_description'])): ?>
                                                                <div class="alert alert-info mb-3 py-2">
                                                                    <i class="fas fa-info-circle me-2"></i>
                                                                    <?= esc($version['changes_description']) ?>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <div class="version-content bg-light p-3 rounded mb-3">
                                                                <h6>Content Preview:</h6>
                                                                <p class="mb-0">
                                                                    <?= substr(strip_tags($version['content']), 0, 300) ?>...
                                                                </p>
                                                            </div>
                                                            
                                                            <div class="btn-group" role="group">
                                                                <?php if ($index > 0): // Not the current version ?>
                                                                    <button class="btn btn-outline-success" 
                                                                            onclick="confirmRestore(<?= $version['id'] ?>, '<?= esc($version['version_number']) ?>')">
                                                                        <i class="fas fa-undo me-1"></i>Restore
                                                                    </button>
                                                                <?php endif; ?>
                                                                <button class="btn btn-outline-primary" 
                                                                        onclick="viewVersionDetails(<?= $version['id'] ?>)">
                                                                    <i class="fas fa-eye me-1"></i>View Details
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="ms-3 text-end">
                                                            <div class="text-muted small">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                <?= date('M d, Y', strtotime($version['created_at'])) ?>
                                                            </div>
                                                            <div class="text-muted small">
                                                                <i class="fas fa-clock me-1"></i>
                                                                <?= date('H:i', strtotime($version['created_at'])) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Version Modal -->
    <div class="modal fade" id="createVersionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?= base_url('documents/create-version/' . $document['id']) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Version</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Changes Description <span class="text-danger">*</span></label>
                            <textarea name="changes_description" class="form-control" rows="3" 
                                      placeholder="Describe the changes made in this version..." required></textarea>
                            <div class="form-text">Briefly explain what changes were made in this version</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Version
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Restore Confirmation Modal -->
    <div class="modal fade" id="restoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Restore</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to restore version <strong id="restoreVersionNumber"></strong>?</p>
                    <p class="text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This will create a new version of the current document with the content from the selected version.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmRestoreBtn" class="btn btn-success">
                        <i class="fas fa-undo me-2"></i>Restore Version
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        function confirmRestore(versionId, versionNumber) {
            document.getElementById('restoreVersionNumber').textContent = versionNumber;
            document.getElementById('confirmRestoreBtn').href = '<?= base_url('documents/restore-version/' . $document['id']) ?>/' + versionId;
            var restoreModal = new bootstrap.Modal(document.getElementById('restoreModal'));
            restoreModal.show();
        }

        function viewVersionDetails(versionId) {
            // In a real implementation, this would show a modal with detailed version information
            alert('Version details functionality would be implemented here');
        }
    </script>
</body>
</html>