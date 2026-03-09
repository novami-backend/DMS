<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'DMS - Document Management System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script> -->
    <?= view('common/styles') ?>

    <?php if (isset($additionalStyles)): ?>
        <?= $additionalStyles ?>
    <?php endif; ?>
    <!-- Header Component -->
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1"><?= $pageTitle ?? 'Dashboard' ?></h2>
                <p class="text-muted mb-0"><?= $pageDescription ?? 'Welcome back!' ?></p>
            </div>

            <div class="d-flex align-items-center">
                <!-- Notification Bell -->
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <?php if (!empty($notifications) && count($notifications) > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= count($notifications) ?>
                            </span>
                        <?php endif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                        <li class="dropdown-header">Notifications</li>
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $note): ?>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" href="<?= base_url('notifications/view/' . $note['id']) ?>">
                                        <span><?= esc($note['message']) ?></span>&nbsp;
                                        <small class="text-muted">
                                            <?= date('d M h:i A', strtotime($note['created_at'])) ?>
                                        </small>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><span class="dropdown-item text-muted">No new notifications</span></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Role badge -->
                <span class="badge bg-primary me-3">Role: <?= esc(session()->get('role_name')) ?></span>

                <!-- User dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?= esc(session()->get('username')) ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('profile/edit') ?>"><i class="fas fa-user-cog me-2"></i>Edit Profile</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</head>