<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Sharing - <?= esc($document['title']) ?> - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .share-card {
            transition: all 0.3s;
            border-left: 4px solid #28a745;
        }
        .share-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .access-badge {
            font-size: 0.8em;
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
                        'pageTitle' => '<i class="fas fa-share-alt me-2"></i>Document Sharing: ' . esc($document['title']),
                        'pageDescription' => 'Manage document sharing permissions and access control'
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
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fas fa-plus me-2"></i>Share Document
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
                                    <h5>Sharing Statistics</h5>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="text-center p-3 bg-light rounded">
                                                <h3 class="mb-0"><?= count($shares) ?></h3>
                                                <small class="text-muted">Active Shares</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Current Shares -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-users me-2"></i>Current Shares
                                </h5>
                                <span class="badge bg-light text-dark"><?= count($shares) ?> active</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($shares)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h4>No active shares</h4>
                                    <p class="text-muted">This document is not currently shared with anyone</p>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#shareModal">
                                        <i class="fas fa-plus me-2"></i>Share Document
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($shares as $share): ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card share-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="card-title mb-0">
                                                            <?php if (!empty($share['shared_with_user_name'])): ?>
                                                                <i class="fas fa-user me-1 text-primary"></i>
                                                                <?= esc($share['shared_with_user_name']) ?>
                                                            <?php elseif (!empty($share['shared_with_role_name'])): ?>
                                                                <i class="fas fa-user-tag me-1 text-info"></i>
                                                                <?= esc($share['shared_with_role_name']) ?>
                                                            <?php elseif (!empty($share['shared_with_department_name'])): ?>
                                                                <i class="fas fa-building me-1 text-warning"></i>
                                                                <?= esc($share['shared_with_department_name']) ?>
                                                            <?php endif; ?>
                                                        </h6>
                                                        <span class="badge bg-<?= $share['permission_level'] === 'full' ? 'danger' : ($share['permission_level'] === 'edit' ? 'warning' : 'info') ?> access-badge">
                                                            <?= ucfirst($share['permission_level']) ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-user me-1"></i>
                                                            Shared by: <?= esc($share['created_by_name']) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <?= date('M d, Y H:i', strtotime($share['created_at'])) ?>
                                                        </small>
                                                    </div>
                                                    
                                                    <?php if (!empty($share['expiration_date'])): ?>
                                                        <div class="mb-2">
                                                            <small class="<?= strtotime($share['expiration_date']) < time() ? 'text-danger' : 'text-warning' ?>">
                                                                <i class="fas fa-clock me-1"></i>
                                                                Expires: <?= date('M d, Y H:i', strtotime($share['expiration_date'])) ?>
                                                            </small>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="mb-2">
                                                            <small class="text-success">
                                                                <i class="fas fa-infinity me-1"></i>
                                                                No expiration
                                                            </small>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <div class="btn-group w-100" role="group">
                                                        <button class="btn btn-sm btn-outline-primary" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" title="Modify Access">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" title="Revoke Access">
                                                            <i class="fas fa-times"></i>
                                                        </button>
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

    <!-- Share Document Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="<?= base_url('documents/share/' . $document['id']) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Share Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Share With <span class="text-danger">*</span></label>
                            <ul class="nav nav-tabs" id="shareTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="user-tab" data-bs-toggle="tab" data-bs-target="#user" type="button" role="tab">
                                        <i class="fas fa-user me-1"></i>User
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="role-tab" data-bs-toggle="tab" data-bs-target="#role" type="button" role="tab">
                                        <i class="fas fa-user-tag me-1"></i>Role
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="department-tab" data-bs-toggle="tab" data-bs-target="#department" type="button" role="tab">
                                        <i class="fas fa-building me-1"></i>Department
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content mt-3" id="shareTabsContent">
                                <div class="tab-pane fade show active" id="user" role="tabpanel">
                                    <select name="share_with_user" class="form-select" data-share-target="user">
                                        <option value="">Select a user...</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?= $user['id'] ?>"><?= esc($user['username']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="tab-pane fade" id="role" role="tabpanel">
                                    <select name="share_with_role" class="form-select" data-share-target="role">
                                        <option value="">Select a role...</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= $role['id'] ?>"><?= esc($role['role_name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="tab-pane fade" id="department" role="tabpanel">
                                    <select name="share_with_department" class="form-select" data-share-target="department">
                                        <option value="">Select a department...</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['id'] ?>"><?= esc($dept['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Permission Level <span class="text-danger">*</span></label>
                                    <select name="permission_level" class="form-select" required>
                                        <option value="">Select permission level...</option>
                                        <option value="view">View Only</option>
                                        <option value="edit">View & Edit</option>
                                        <option value="full">Full Access</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Expiration Date</label>
                                    <input type="datetime-local" name="expiration_date" class="form-control">
                                    <div class="form-text">Leave empty for no expiration</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-share-alt me-2"></i>Share Document
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
    <script>
        // Form validation and sharing logic
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#shareModal form');
            const shareSelects = document.querySelectorAll('select[name^="share_with_"]');
            const permissionLevelSelect = document.querySelector('select[name="permission_level"]');
            
            // Ensure only one sharing option is selected
            shareSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value) {
                        // Clear other selects
                        shareSelects.forEach(otherSelect => {
                            if (otherSelect !== this) {
                                otherSelect.value = '';
                            }
                        });
                    }
                });
            });
            
            // Form validation on submit
            form.addEventListener('submit', function(e) {
                // Check if at least one sharing target is selected
                const hasSelectedTarget = Array.from(shareSelects).some(select => select.value !== '');
                
                if (!hasSelectedTarget) {
                    e.preventDefault();
                    alert('Please select a user, role, or department to share with.');
                    return;
                }
                
                // Check if permission level is selected
                if (!permissionLevelSelect.value) {
                    e.preventDefault();
                    alert('Please select a permission level.');
                    permissionLevelSelect.focus();
                    return;
                }
            });
        });
    </script>
</body>
</html>