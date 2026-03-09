<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= view('common/styles') ?>
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .stat-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .stat-card-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
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
                        'pageTitle' => 'Dashboard',
                        'pageDescription' => 'Welcome back, ' . esc($username) . '!'
                    ]) ?>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-2x mb-3"></i>
                                    <h3><?= esc($userCount) ?></h3>
                                    <p class="mb-0">Total Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card-4">
                                <div class="card-body text-center">
                                    <i class="fas fa-user fa-2x mb-3"></i>
                                    <h3><?= esc($activeUserCount) ?></h3>
                                    <p class="mb-0">Active Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card-2">
                                <div class="card-body text-center">
                                    <i class="fas fa-user-times fa-2x mb-3"></i>
                                    <h3><?= esc($inactiveUserCount) ?></h3>
                                    <p class="mb-0">Inactive Users</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4">
                            <div class="card stat-card-3">
                                <div class="card-body text-center">
                                    <i class="fas fa-file-alt fa-2x mb-3"></i>
                                    <h3><?= esc($documentCount) ?></h3>
                                    <p class="mb-0">Documents</p>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-md-3 mb-4">
                            <div class="card stat-card-4">
                                <div class="card-body text-center">
                                    <i class="fas fa-tasks fa-2x mb-3"></i>
                                    <h3>24</h3>
                                    <p class="mb-0">Pending Tasks</p>
                                </div>
                            </div>
                        </div> -->
                    </div>

                    <!-- Recent Activity -->
                    <!-- <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar me-2"></i>Recent Activity</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success rounded-circle p-2 me-3">
                                            <i class="fas fa-check text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">System Backup Completed</h6>
                                            <small class="text-muted">2 hours ago</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary rounded-circle p-2 me-3">
                                            <i class="fas fa-user-plus text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">New User Registered</h6>
                                            <small class="text-muted">5 hours ago</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-warning rounded-circle p-2 me-3">
                                            <i class="fas fa-exclamation text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Document Expiring Soon</h6>
                                            <small class="text-muted">1 day ago</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle p-2 me-3">
                                            <i class="fas fa-download text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Report Generated</h6>
                                            <small class="text-muted">2 days ago</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-bell me-2"></i>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="/users" class="btn btn-outline-primary">
                                            <i class="fas fa-user-plus me-2"></i>Manage Users
                                        </a>
                                        <a href="/roles" class="btn btn-outline-secondary">
                                            <i class="fas fa-user-tag me-2"></i>Manage Roles
                                        </a>
                                        <a href="/activity-logs" class="btn btn-outline-info">
                                            <i class="fas fa-history me-2"></i>View Logs
                                        </a>
                                        <a href="#" class="btn btn-outline-success">
                                            <i class="fas fa-file-export me-2"></i>Generate Report
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

    <?= view('common/footer') ?>
    <?= view('common/scripts') ?>
</body>
</html>