<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .log-login { background-color: #d1ecf1; }
        .log-create { background-color: #d4edda; }
        .log-update { background-color: #fff3cd; }
        .log-delete { background-color: #f8d7da; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="p-0">
                <?= view('common/sidebar') ?>
            </div>

            <!-- Main Content -->
            <div class="p-0">
                <div class="main-content">
                    <!-- Header -->
                    <?= view('common/header', [
                        'pageTitle' => '<i class="fas fa-history me-2"></i>Activity Logs',
                        'pageDescription' => 'System activity and user actions'
                    ]) ?>

                    <!-- Logs Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>User</th>
                                            <th>Action</th>
                                            <th>Details</th>
                                            <th>IP Address</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($logs as $log): ?>
                                        <tr class="log-<?= strtolower(explode(' ', $log['action'])[0]) ?>">
                                            <td><?= date('M j, Y H:i:s', strtotime($log['timestamp'])) ?></td>
                                            <td><?= esc($log['username']) ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= esc($log['action']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($log['details']): ?>
                                                    <small class="text-muted"><?= esc($log['details']) ?></small>
                                                <?php else: ?>
                                                    <span class="text-muted">No details</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($log['ip_address']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
</body>
</html>