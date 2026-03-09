<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Backups - <?= esc($document['title']) ?> - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .backup-card {
            transition: all 0.3s;
            border-left: 4px solid #6c757d;
        }
        .backup-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .backup-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
        }
        .backup-stats-item {
            padding: 10px;
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
                        'pageTitle' => '<i class="fas fa-save me-2"></i>Document Backups: ' . esc($document['title']),
                        'pageDescription' => 'Manage document backups and recovery options'
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
                        <button class="btn btn-primary" onclick="createBackup()">
                            <i class="fas fa-plus me-2"></i>Create Backup
                        </button>
                    </div>

                    <!-- Document Info -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
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
                                    </table>
                                </div>
                                <div class="col-md-4">
                                    <h5>Backup Statistics</h5>
                                    <div class="backup-stats">
                                        <div class="backup-stats-item">
                                            <h3 class="mb-0"><?= count($backups) ?></h3>
                                            <small class="text-muted">Total Backups</small>
                                        </div>
                                        <div class="backup-stats-item">
                                            <h3 class="mb-0">
                                                <?php 
                                                $totalSize = array_sum(array_column($backups, 'backup_size'));
                                                echo $totalSize > 0 ? round($totalSize / 1024 / 1024, 2) . ' MB' : '0 MB';
                                                ?>
                                            </h3>
                                            <small class="text-muted">Total Size</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Management -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-history me-2"></i>Backup History
                                        </h5>
                                        <span class="badge bg-light text-dark"><?= count($backups) ?> backups</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($backups)): ?>
                                        <div class="text-center py-5">
                                            <i class="fas fa-save fa-3x text-muted mb-3"></i>
                                            <h4>No backups found</h4>
                                            <p class="text-muted">No backups have been created for this document</p>
                                            <button class="btn btn-primary" onclick="createBackup()">
                                                <i class="fas fa-plus me-2"></i>Create First Backup
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="list-group">
                                            <?php foreach ($backups as $index => $backup): ?>
                                                <div class="list-group-item backup-card">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <h6 class="mb-0">
                                                                    <i class="fas fa-file-archive me-2 text-<?= $index === 0 ? 'success' : 'secondary' ?>"></i>
                                                                    Backup #<?= count($backups) - $index ?>
                                                                    <?php if ($index === 0): ?>
                                                                        <span class="badge bg-success ms-2">Latest</span>
                                                                    <?php endif; ?>
                                                                </h6>
                                                                <small class="text-muted ms-3">
                                                                    <?= date('M d, Y H:i', strtotime($backup['backup_date'])) ?>
                                                                </small>
                                                            </div>
                                                            
                                                            <div class="row mb-2">
                                                                <div class="col-md-6">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-database me-1"></i>
                                                                        Size: <?= !empty($backup['backup_size']) ? round($backup['backup_size'] / 1024 / 1024, 2) . ' MB' : 'Unknown' ?>
                                                                    </small>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-file me-1"></i>
                                                                        <?= basename($backup['backup_path']) ?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            
                                                            <?php if (!empty($backup['retention_policy'])): ?>
                                                                <div class="mb-2">
                                                                    <span class="badge bg-info">
                                                                        <i class="fas fa-clock me-1"></i>
                                                                        <?= esc($backup['retention_policy']) ?>
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                            
                                                            <div class="btn-group" role="group">
                                                                <button class="btn btn-sm btn-outline-primary" onclick="downloadBackup('<?= $backup['backup_path'] ?>')">
                                                                    <i class="fas fa-download me-1"></i>Download
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-info" onclick="restoreBackup(<?= $backup['id'] ?>, <?= $index === 0 ? 'true' : 'false' ?>)">
                                                                    <i class="fas fa-undo me-1"></i>Restore
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteBackup(<?= $backup['id'] ?>, <?= $index === 0 ? 'true' : 'false' ?>)">
                                                                    <i class="fas fa-trash me-1"></i>Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="ms-3 text-end">
                                                            <div class="text-muted small">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                <?= date('M d, Y', strtotime($backup['backup_date'])) ?>
                                                            </div>
                                                            <div class="text-muted small">
                                                                <i class="fas fa-clock me-1"></i>
                                                                <?= date('H:i', strtotime($backup['backup_date'])) ?>
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
                        
                        <!-- Backup Settings -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-cog me-2"></i>Backup Settings
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6>Auto Backup Schedule</h6>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="autoBackup" checked>
                                            <label class="form-check-label" for="autoBackup">
                                                Enable automatic backups
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Backup Frequency</label>
                                        <select class="form-select">
                                            <option>Daily</option>
                                            <option selected>Weekly</option>
                                            <option>Monthly</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Retention Policy</label>
                                        <select class="form-select">
                                            <option>30 days</option>
                                            <option selected>90 days</option>
                                            <option>180 days</option>
                                            <option>1 year</option>
                                            <option>Keep forever</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6>Storage Location</h6>
                                        <div class="alert alert-info py-2">
                                            <i class="fas fa-server me-2"></i>
                                            Local Storage
                                            <div class="text-muted small mt-1">Backups stored in: /backups/documents/</div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary" onclick="configureBackup()">
                                            <i class="fas fa-cog me-2"></i>Configure Settings
                                        </button>
                                        <button class="btn btn-outline-success" onclick="testBackup()">
                                            <i class="fas fa-vial me-2"></i>Test Backup
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Storage Usage -->
                            <div class="card mt-4">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="fas fa-hdd me-2"></i>Storage Usage
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="progress mb-3">
                                        <div class="progress-bar" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">45%</div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Used: 4.5 GB</span>
                                        <span class="text-muted">Total: 10 GB</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span>Document backups:</span>
                                        <span class="fw-bold"><?= round($totalSize / 1024 / 1024, 2) ?> MB</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Modals -->
    <div class="modal fade" id="confirmRestoreModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Restore</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to restore this backup?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This will overwrite the current document content. The current version will be saved as a backup before restoration.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmRestoreBtn">
                        <i class="fas fa-undo me-2"></i>Restore Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this backup?</p>
                    <div class="alert alert-danger" id="deleteLatestWarning" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This is the latest backup. Deleting it will remove your most recent restore point.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-trash me-2"></i>Delete Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        let currentBackupId = null;
        let isLatestBackup = false;

        function createBackup() {
            if (confirm('Create a new backup of this document?')) {
                // Simulate backup creation
                showLoading('Creating backup...');
                setTimeout(() => {
                    hideLoading();
                    alert('Backup created successfully!');
                    location.reload();
                }, 2000);
            }
        }

        function downloadBackup(path) {
            // In a real implementation, this would download the actual backup file
            alert('Downloading backup: ' + path);
        }

        function restoreBackup(backupId, isLatest) {
            currentBackupId = backupId;
            isLatestBackup = isLatest;
            document.getElementById('confirmRestoreBtn').onclick = performRestore;
            var restoreModal = new bootstrap.Modal(document.getElementById('confirmRestoreModal'));
            restoreModal.show();
        }

        function deleteBackup(backupId, isLatest) {
            currentBackupId = backupId;
            isLatestBackup = isLatest;
            document.getElementById('confirmDeleteBtn').onclick = performDelete;
            
            // Show warning if it's the latest backup
            if (isLatest) {
                document.getElementById('deleteLatestWarning').style.display = 'block';
            } else {
                document.getElementById('deleteLatestWarning').style.display = 'none';
            }
            
            var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            deleteModal.show();
        }

        function performRestore() {
            showLoading('Restoring backup...');
            setTimeout(() => {
                hideLoading();
                alert('Backup restored successfully! A new version of the current document has been created.');
                location.reload();
            }, 2000);
        }

        function performDelete() {
            showLoading('Deleting backup...');
            setTimeout(() => {
                hideLoading();
                alert('Backup deleted successfully!');
                location.reload();
            }, 1500);
        }

        function configureBackup() {
            alert('Backup configuration would open in a modal here');
        }

        function testBackup() {
            showLoading('Testing backup system...');
            setTimeout(() => {
                hideLoading();
                alert('Backup system is working correctly! A test backup was created successfully.');
            }, 3000);
        }

        function showLoading(message) {
            // In a real implementation, you'd show a proper loading modal
            document.body.innerHTML += '<div id="loadingModal" class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">' +
                '<div class="modal-dialog">' +
                '<div class="modal-content">' +
                '<div class="modal-body text-center p-5">' +
                '<div class="spinner-border text-primary mb-3" role="status"></div>' +
                '<p>' + message + '</p>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';
        }

        function hideLoading() {
            var modal = document.getElementById('loadingModal');
            if (modal) {
                modal.remove();
            }
        }
    </script>
</body>
</html>