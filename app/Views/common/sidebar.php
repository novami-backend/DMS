<!-- Sidebar Component -->
<div class="sidebar">
    <div class="text-center mb-4">
        <i class="fas fa-file-alt fa-2x mb-2"></i>
        <h5>MedZus DMS</h5>
    </div>

    <ul class="nav flex-column">
        <?php if (userHasPermission('dashboard_access')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('notification_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'notifications' ? 'active' : '' ?>" href="<?= base_url('notifications') ?>">
                    <i class="fas fa-bell"></i> Notification
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('permission_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'permissions' ? 'active' : '' ?>" href="<?= base_url('permissions') ?>">
                    <i class="fas fa-key"></i> Permissions
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('role_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'roles' ? 'active' : '' ?>" href="<?= base_url('roles') ?>">
                    <i class="fas fa-user-tag"></i> Roles
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('department_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'departments' ? 'active' : '' ?>" href="<?= base_url('departments') ?>">
                    <i class="fas fa-building"></i> Departments
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('user_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'users' ? 'active' : '' ?>" href="<?= base_url('users') ?>">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('document_type_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'document-types' ? 'active' : '' ?>" href="<?= base_url('document-types') ?>">
                    <i class="fas fa-file-contract"></i> Document Types
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('document_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'templates' ? 'active' : '' ?>" href="<?= base_url('templates') ?>">
                    <i class="fas fa-file-code"></i> Templates
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('document_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'documents' ? 'active' : '' ?>" href="<?= base_url('documents') ?>">
                    <i class="fas fa-file-alt"></i> Documents
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('document_approve')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'approval-dashboard' ? 'active' : '' ?>" href="<?= base_url('approval-dashboard') ?>">
                    <i class="fas fa-file-alt"></i> Documents Approval
                </a>
            </li>
        <?php endif; ?>

        <?php if (userHasPermission('activity_log_read')): ?>
            <li class="nav-item">
                <a class="nav-link <?= service('uri')->getSegment(1) === 'activity-logs' ? 'active' : '' ?>" href="<?= base_url('activity-logs') ?>">
                    <i class="fas fa-history"></i> Activity Logs
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item mt-4">
            <a class="nav-link" href="<?= base_url('/logout') ?>">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>