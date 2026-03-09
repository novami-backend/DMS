<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Notification - DMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <?= view('common/styles') ?>
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
          'pageTitle' => '<i class="fas fa-plus-circle me-2"></i>Create Notification',
          'pageDescription' => 'Send alerts or set recurring reminders for users'
        ]) ?>

        <div class="row px-4 justify-content-center">
          <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-edit me-2"></i>Notification Details</h6>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('notifications/store') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold d-block">Target Audience</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="target_type" id="target_all" value="all" autocomplete="off" onchange="toggleTargetFields()" <?= old('target_type') == 'all' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary" for="target_all"><i class="fas fa-users me-1"></i>All Users</label>

                                    <input type="radio" class="btn-check" name="target_type" id="target_dept" value="department" autocomplete="off" onchange="toggleTargetFields()" <?= old('target_type') == 'department' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary" for="target_dept"><i class="fas fa-building me-1"></i>Department</label>

                                    <input type="radio" class="btn-check" name="target_type" id="target_individual" value="individual" autocomplete="off" onchange="toggleTargetFields()" <?= (old('target_type') == 'individual' || !old('target_type')) ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-primary" for="target_individual"><i class="fas fa-user me-1"></i>Specific Users</label>
                                </div>
                            </div>

                            <div class="col-md-12 target-field" id="dept_select_group" style="display: <?= old('target_type') == 'department' ? 'block' : 'none' ?>;">
                                <label for="department_id" class="form-label fw-bold">Select Department</label>
                                <select name="department_id" id="department_id" class="form-select">
                                    <option value="">Choose Department...</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?= $dept['id'] ?>" <?= old('department_id') == $dept['id'] ? 'selected' : '' ?>>
                                            <?= esc($dept['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text text-info"><i class="fas fa-info-circle me-1"></i>Notification will be sent to all active users in this department.</div>
                            </div>

                            <div class="col-md-12 target-field" id="user_select_group" style="display: <?= (old('target_type') == 'individual' || !old('target_type')) ? 'block' : 'none' ?>;">
                                <label for="recipient_ids" class="form-label fw-bold">Select User(s)</label>
                                <select name="recipient_ids[]" id="recipient_ids" class="form-select" multiple size="5">
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= (is_array(old('recipient_ids')) && in_array($user['id'], old('recipient_ids'))) ? 'selected' : '' ?>>
                                            <?= esc($user['username']) ?> (<?= esc($user['email']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Hold Ctrl (Cmd on Mac) to select multiple users.</div>
                            </div>

                            <div class="col-md-12">
                                <label for="message" class="form-label fw-bold">Message</label>
                                <textarea name="message" id="message" class="form-control" rows="3" placeholder="Enter notification message..." required><?= old('message') ?></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-bold">Priority</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="low" <?= old('priority') == 'low' ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= old('priority', 'medium') == 'medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= old('priority') == 'high' ? 'selected' : '' ?>>High</option>
                                    <option value="urgent" <?= old('priority') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="frequency" class="form-label fw-bold">Frequency</label>
                                <select name="frequency" id="frequency" class="form-select" required onchange="toggleInterval()">
                                    <option value="once" <?= old('frequency') == 'once' ? 'selected' : '' ?>>Once Only</option>
                                    <option value="daily" <?= old('frequency') == 'daily' ? 'selected' : '' ?>>Daily</option>
                                    <option value="weekly" <?= old('frequency') == 'weekly' ? 'selected' : '' ?>>Weekly</option>
                                    <option value="monthly" <?= old('frequency') == 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                    <option value="yearly" <?= old('frequency') == 'yearly' ? 'selected' : '' ?>>Yearly</option>
                                    <option value="custom" <?= old('frequency') == 'custom' ? 'selected' : '' ?>>Custom Interval</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="interval_group" style="display: <?= old('frequency') == 'custom' ? 'block' : 'none' ?>;">
                                <label for="interval_minutes" class="form-label fw-bold">Interval (Minutes)</label>
                                <input type="number" name="interval_minutes" id="interval_minutes" class="form-control" placeholder="e.g. 60" value="<?= old('interval_minutes') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="category" class="form-label fw-bold">Category</label>
                                <input type="text" name="category" id="category" class="form-control" placeholder="e.g. System, Audit, Reminder" value="<?= old('category') ?>">
                            </div>

                            <div class="col-md-6">
                                <label for="link_url" class="form-label fw-bold">Action Link (Optional)</label>
                                <input type="text" name="link_url" id="link_url" class="form-control" placeholder="e.g. documents/view/1" value="<?= old('link_url') ?>">
                                <div class="form-text">Relative path inside the application.</div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                            <a href="<?= base_url('notifications') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Send Notification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 alert alert-info shadow-sm">
                <h6><i class="fas fa-info-circle me-2"></i>About Recurring Notifications</h6>
                <p class="small mb-0">Bulk notifications (All Users / Departments) will be created as individual records for each user. Recurring settings will apply to each record independently.</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
<?= view('common/footer') ?>
<?= view('common/scripts') ?>
<script>
function toggleTargetFields() {
    const targetType = document.querySelector('input[name="target_type"]:checked').value;
    document.getElementById('dept_select_group').style.display = (targetType === 'department') ? 'block' : 'none';
    document.getElementById('user_select_group').style.display = (targetType === 'individual') ? 'block' : 'none';
}

function toggleInterval() {
    const freq = document.getElementById('frequency').value;
    const intervalGroup = document.getElementById('interval_group');
    intervalGroup.style.display = (freq === 'custom') ? 'block' : 'none';
}
</script>
</body>
</html>
