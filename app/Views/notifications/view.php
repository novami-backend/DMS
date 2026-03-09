<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notifications - DMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <?= view('common/styles') ?>
  <style>
    .notification-list {
      position: relative;
      padding-left: 1.5rem;
      border-left: 2px dashed #007bff;
    }

    .notification-item {
      margin-bottom: 1rem;
      position: relative;
      background-color: #f8f9fa;
      padding: 10px;
      border-radius: 4px;
    }

    .notification-item::before {
      content: '';
      position: absolute;
      left: -30px;
      top: 10px;
      width: 12px;
      height: 12px;
      background: #007bff;
      border-radius: 50%;
      border: 2px solid #fff;
    }

    .notification-message {
      font-size: 14px;
      margin-bottom: 5px;
    }

    .notification-meta {
      font-size: 12px;
      color: #6c757d;
    }

    .badge-unread {
      background-color: #dc3545;
      color: #fff;
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
            'pageTitle' => '<i class="fas fa-bell me-2"></i>Notifications',
            'pageDescription' => 'Important system activities and alerts'
          ]) ?>

          <div class="row">
            <div class="col-lg-9">
              <!-- Notification Detail Card -->
              <div class="card">
                <div class="card-header py-2">
                  <h6 class="mb-0"><i class="fas fa-bell me-2"></i>Notification Detail</h6>
                </div>
                <div class="card-body py-2 px-3">
                  <?php if (empty($notification)): ?>
                    <div class="alert alert-info alert-sm mb-0">
                      <i class="fas fa-info-circle"></i> Notification not found.
                    </div>
                  <?php else: ?>
                    <div class="notification-item">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <div class="notification-message">
                            <?= esc($notification['message']) ?>
                            <?php if ($notification['status'] === 'unread'): ?>
                              <span class="badge badge-unread ms-2">Unread</span>
                            <?php endif; ?>
                          </div>
                          <!-- <div class="notification-meta">
                            <i class="fas fa-user me-1"></i>
                            Triggered by User: <?= esc($notification['name']) ?>
                          </div> -->
                        </div>
                        <small class="text-muted text-nowrap ms-2">
                          <?= date('M j, g:i A, Y', strtotime($notification['created_at'])) ?>
                        </small>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-3">
              <div class="card">
                <div class="card-header py-2">
                  <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Actions</h6>
                </div>
                <div class="card-body py-2 px-3">
                  <div class="d-grid gap-2">
                    <a href="<?= base_url('notifications') ?>" class="btn btn-sm btn-secondary mt-3">
                      <i class="fas fa-arrow-left me-1"></i>Back to Notifications
                    </a>
                  </div>
                </div>
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