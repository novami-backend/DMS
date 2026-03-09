<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= $page_title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-search"></i> <?= $page_title ?>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info"><?= count($documents) ?> Documents</span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($documents)): ?>
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>No Documents Assigned for Review</h5>
                            <p>You currently have no documents assigned for review. Check back later or contact your administrator.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Document</th>
                                        <th>Type</th>
                                        <th>Department</th>
                                        <th>Created By</th>
                                        <th>Submitted Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documents as $doc): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($doc['title']) ?></strong>
                                                <br><small class="text-muted">ID: <?= $doc['id'] ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary"><?= esc($doc['type_name']) ?></span>
                                            </td>
                                            <td><?= esc($doc['department_name']) ?></td>
                                            <td><?= esc($doc['created_by_name']) ?></td>
                                            <td>
                                                <?php if ($doc['submitted_for_review_at']): ?>
                                                    <?= date('M j, Y', strtotime($doc['submitted_for_review_at'])) ?>
                                                    <br><small class="text-muted"><?= date('g:i A', strtotime($doc['submitted_for_review_at'])) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">Not submitted</span>
                                                <?php endif ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'sent_for_review' => 'info',
                                                    'reviewed' => 'primary',
                                                    'approved' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                                $statusColor = $statusColors[$doc['approval_status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $statusColor ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $doc['approval_status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('documents/view/' . $doc['id']) ?>" 
                                                       class="btn btn-sm btn-info" title="View Document">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($doc['approval_status'] === 'sent_for_review'): ?>
                                                        <a href="<?= base_url('documents/review/' . $doc['id']) ?>" 
                                                           class="btn btn-sm btn-primary" title="Review Document">
                                                            <i class="fas fa-search"></i> Review
                                                        </a>
                                                    <?php endif ?>
                                                    <a href="<?= base_url('documents/approval-history/' . $doc['id']) ?>" 
                                                       class="btn btn-sm btn-secondary" title="View History">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('.table').DataTable({
        "pageLength": 25,
        "order": [[4, "desc"]], // Sort by submitted date
        "columnDefs": [
            { "orderable": false, "targets": -1 } // Disable sorting on Actions column
        ]
    });
});
</script>
<?= $this->endSection() ?>