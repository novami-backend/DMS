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
      padding-left: 0.5rem;
    }
    .notification-item {
      margin-bottom: 1rem;
      position: relative;
      background-color: #fff;
      padding: 15px;
      border-radius: 8px;
      border-left: 5px solid #dee2e6;
      transition: all 0.2s ease;
    }
    .notification-item:hover {
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transform: translateY(-2px);
    }
    .notification-item.unread {
      background-color: #f0f7ff;
    }
    /* Priority Styling */
    .priority-low { border-left-color: #6c757d; }
    .priority-medium { border-left-color: #0dcaf0; }
    .priority-high { border-left-color: #ffc107; }
    .priority-urgent { border-left-color: #dc3545; }
    
    .priority-badge {
      font-size: 10px;
      text-transform: uppercase;
      padding: 2px 6px;
      border-radius: 10px;
    }
    .priority-urgent .priority-badge { background-color: #dc3545; color: white; }
    
    .notification-message {
      font-size: 15px;
      color: #333;
      margin-bottom: 8px;
    }
    .notification-meta {
      font-size: 12px;
      color: #6c757d;
      display: flex;
      gap: 15px;
      align-items: center;
    }
    .category-tag {
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 600;
        color: #495057;
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
          'pageDescription' => 'Manage your alerts and recurring reminders'
        ]) ?>

        <div class="row px-4">
          <div class="col-lg-9">
            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body py-3">
                    <form action="<?= base_url('notifications') ?>" method="get" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="unread" <?= $filters['status'] == 'unread' ? 'selected' : '' ?>>Unread Only</option>
                                <option value="read" <?= $filters['status'] == 'read' ? 'selected' : '' ?>>Read</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Priority</label>
                            <select name="priority" class="form-select form-select-sm">
                                <option value="">All Priorities</option>
                                <option value="low" <?= $filters['priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                                <option value="medium" <?= $filters['priority'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="high" <?= $filters['priority'] == 'high' ? 'selected' : '' ?>>High</option>
                                <option value="urgent" <?= $filters['priority'] == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-filter me-1"></i>Apply Filters
                            </button>
                            <a href="<?= base_url('notifications') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notifications Card -->
            <div class="card shadow-sm">
              <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-list me-2"></i>Inbox
                </h6>
                <?php if (!empty($notifications)): ?>
                    <a href="<?= base_url('notifications/markAllRead') ?>" class="btn btn-sm btn-link text-decoration-none">
                        <i class="fas fa-check-double me-1"></i>Mark all as read
                    </a>
                <?php endif; ?>
              </div>
              <div class="card-body bg-light bg-opacity-10">
                <?php if (empty($notifications)): ?>
                  <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5>All caught up!</h5>
                    <p class="text-muted">No notifications matching your filters.</p>
                  </div>
                <?php else: ?>
                  <div class="notification-list">
                    <?php foreach ($notifications as $note): ?>
                      <div class="notification-item priority-<?= esc($note['priority']) ?> <?= $note['status'] === 'unread' ? 'unread shadow-sm' : '' ?>">
                        <div class="d-flex justify-content-between">
                          <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-1">
                                <span class="category-tag small me-2"><?= esc($note['category']) ?></span>
                                <?php if ($note['frequency'] !== 'once'): ?>
                                    <span class="badge bg-soft-info text-info border border-info small px-2 py-1" style="font-size: 10px;">
                                        <i class="fas fa-redo fa-spin-hover me-1"></i><?= ucfirst($note['frequency']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="notification-message">
                              <?php if ($note['status'] === 'unread'): ?>
                                <i class="fas fa-circle text-primary me-2" style="font-size: 8px;"></i>
                              <?php endif; ?>
                              <?= esc($note['message']) ?>
                            </div>
                            <div class="notification-meta">
                              <span><i class="far fa-calendar-alt me-1"></i><?= date('M j, Y', strtotime($note['created_at'])) ?></span>
                              <span><i class="far fa-clock me-1"></i><?= date('g:i A', strtotime($note['created_at'])) ?></span>
                              <span><i class="far fa-user me-1"></i>By <?= esc($note['creator_name']) ?></span>
                            </div>
                          </div>
                          <div class="text-end">
                            <div class="mb-2">
                                <span class="priority-badge"><?= esc($note['priority']) ?></span>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <?php if (!empty($note['link_url'])): ?>
                                    <a href="<?= base_url($note['link_url']) ?>" class="btn btn-outline-primary shadow-none">View Details</a>
                                <?php endif; ?>
                                <a href="<?= base_url('notifications/view/' . $note['id']) ?>" class="btn btn-outline-secondary shadow-none">
                                    <i class="fas fa-eye"></i>
                                </a>
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

          <!-- Sidebar -->
          <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
              <div class="card-body">
                <div class="d-grid gap-2">
                  <a href="<?= base_url('notifications/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i>Create Alert
                  </a>
                  <hr>
                  <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-1"></i>Dashboard
                  </a>
                </div>
              </div>
            </div>
            
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <h6><i class="fas fa-lightbulb me-2"></i>Quick Tip</h6>
                    <p class="small mb-0 opacity-75">Use "Recurring" notifications for regular reminders like document audits or monthly reviews.</p>
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
