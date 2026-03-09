# Simple Document Approval System Implementation

## Overview
This implementation provides a streamlined document approval process that integrates seamlessly with your existing role-based access control system. The system supports a clear approval workflow without the complexity of the DocumentWorkflow table, making it easier to manage and understand.

## Approval Workflow

### 1. Document Creation (Draft Status)
- **Who**: Super Admin, Admin, Lab Manager
- **Status**: `draft` with `approval_status: pending`
- Documents start in draft mode and can be edited by creators

### 2. Submit for Review
- **Who**: Document creator, Lab Manager, Admin, Super Admin
- **Action**: Assign a reviewer from the same department
- **Status Change**: `approval_status: pending` → `sent_for_review`
- **Database Fields Updated**:
  - `reviewer_id`
  - `submitted_for_review_at`
  - `approval_status = 'sent_for_review'`

### 3. Document Review
- **Who**: Assigned Reviewer, Lab Manager, Admin, Super Admin
- **Actions Available**:
  - **Approve for Final**: Move to final approval stage
  - **Return for Revision**: Send back to creator for changes
  - **Reject**: Reject the document with reason
- **Status Changes**:
  - Approve → `approval_status: reviewed`
  - Return → `approval_status: pending`
  - Reject → `approval_status: rejected`

### 4. Final Approval (Admin Authority)
- **Who**: Admin, Super Admin (Final approval authority)
- **Actions Available**:
  - **Approve**: Activate document (`status: active`, `approval_status: approved`)
  - **Reject**: Reject with reason (`approval_status: rejected`)
- **Digital Signature**: Timestamped approval with user details

### 5. Document Lifecycle Management
- **Lock Obsolete Documents**: Admin can archive approved documents
- **Audit Trail**: Complete approval history tracking

## Database Schema

### Enhanced Documents Table
```sql
-- New approval fields added to existing documents table
approval_status ENUM('pending', 'sent_for_review', 'reviewed', 'approved', 'rejected') DEFAULT 'pending'
reviewer_id INT(11) UNSIGNED DEFAULT NULL
reviewer_comments TEXT DEFAULT NULL
reviewed_at DATETIME DEFAULT NULL
approver_id INT(11) UNSIGNED DEFAULT NULL
approver_comments TEXT DEFAULT NULL
approved_at DATETIME DEFAULT NULL
rejection_reason TEXT DEFAULT NULL
rejected_at DATETIME DEFAULT NULL
submitted_for_review_at DATETIME DEFAULT NULL
submitted_for_approval_at DATETIME DEFAULT NULL
```

### Approval History Table
```sql
CREATE TABLE document_approval_history (
  id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  document_id INT(11) UNSIGNED NOT NULL,
  action ENUM('submitted_for_review', 'reviewed', 'submitted_for_approval', 'approved', 'rejected', 'returned_for_revision'),
  performed_by INT(11) UNSIGNED NOT NULL,
  comments TEXT DEFAULT NULL,
  previous_status VARCHAR(50) DEFAULT NULL,
  new_status VARCHAR(50) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## Role-Based Permissions

### Super Admin
- **Full Access**: All approval functions
- **Can**: Create, review, approve, reject, lock documents
- **Special**: System-wide document management

### Admin (Quality Head)
- **Final Approval Authority**: Can approve any document
- **Can**: Create, manage, assign reviewers, final approval, lock obsolete documents
- **Dashboard**: Monitor all approval activities

### Lab Manager
- **Technical Control**: Review and approve within authority
- **Can**: Create, review documents, approve if authorized
- **Focus**: Technical and operational document control

### Reviewer
- **Review Authority**: Review assigned documents only
- **Can**: Review, comment, recommend approval/rejection
- **Limitation**: No final approval rights

### Approver
- **Signatory Authority**: Final electronic approval
- **Can**: Approve documents assigned to them
- **Responsibility**: Digital sign-off with timestamp

### Staff Level 1 & 2
- **Read-Only**: View approved documents only
- **Cannot**: Participate in approval process
- **Access**: Department-specific approved documents

### Auditor
- **Audit Access**: View all documents and approval history
- **Can**: View approval trails, export reports
- **Cannot**: Modify documents or approval status

## Key Features

### 1. Simple Approval Flow
- **Linear Process**: Draft → Review → Final Approval → Active
- **Clear Status**: Easy to understand approval states
- **Role Integration**: Works with existing 8-role system

### 2. Admin Dashboard
- **Centralized Monitoring**: View all approval activities
- **Status Tabs**: Organized by approval status
- **Quick Actions**: Direct access to approval functions
- **Statistics**: Visual approval metrics

### 3. Digital Signatures
- **Timestamped Approvals**: Automatic timestamp recording
- **User Attribution**: Clear record of who approved what
- **IP Tracking**: Security audit trail
- **Permanent Record**: Immutable approval history

### 4. Audit Trail
- **Complete History**: Every action recorded
- **User Attribution**: Who did what when
- **Comments Preserved**: All review and approval comments
- **Status Changes**: Track all status transitions

### 5. Department-Based Assignment
- **Reviewer Selection**: Choose from department reviewers
- **Approver Assignment**: Department-specific approvers
- **Access Control**: Department-based permissions

## User Interface Components

### 1. Approval Dashboard (`/documents/approval-dashboard`)
- **Statistics Cards**: Visual metrics for each status
- **Tabbed Interface**: Organized by approval status
- **Action Buttons**: Quick access to approval functions
- **Search & Filter**: Find documents quickly

### 2. Review Interface (`/documents/review/{id}`)
- **Document Display**: Full document content view
- **Review Form**: Action selection with comments
- **Guidelines**: Built-in review checklist
- **History Timeline**: Previous approval actions

### 3. Final Approval Interface (`/documents/approve/{id}`)
- **Approval Checklist**: Required validation steps
- **Digital Signature**: User and timestamp info
- **Decision Form**: Approve or reject with comments
- **Security Confirmation**: Double-confirmation for approval

### 4. My Reviews/Approvals (`/documents/my-reviews`, `/documents/my-approvals`)
- **Personal Dashboard**: Documents assigned to user
- **Status Indicators**: Clear visual status
- **Quick Actions**: Direct access to review/approve
- **Sorting & Filtering**: Organize by priority

## Implementation Benefits

### 1. Simplicity
- **No Complex Workflow Engine**: Direct status-based approach
- **Easy to Understand**: Clear linear approval process
- **Minimal Database Changes**: Uses existing documents table

### 2. Role Integration
- **Seamless Integration**: Works with existing role system
- **Permission-Based**: Automatic access control
- **Department Aware**: Respects organizational structure

### 3. Compliance Ready
- **ISO 17025 Support**: Meets document control requirements
- **Audit Trail**: Complete approval history
- **Digital Signatures**: Timestamped approvals
- **Access Control**: Role-based permissions

### 4. User Experience
- **Intuitive Interface**: Easy to use approval screens
- **Clear Status**: Visual approval indicators
- **Quick Actions**: Streamlined approval process
- **Mobile Friendly**: Responsive design

## Usage Examples

### Creating and Submitting a Document
```php
// 1. Create document (Lab Manager)
$documentId = $documentModel->insert([
    'title' => 'HPLC Operating Procedure',
    'content' => 'Detailed procedure...',
    'type_id' => 1,
    'department_id' => 2,
    'status' => 'draft',
    'approval_status' => 'pending',
    'created_by' => $userId
]);

// 2. Submit for review
$documentModel->submitForReview($documentId, $reviewerId, $userId);

// 3. Review document (Reviewer)
$documentModel->reviewDocument($documentId, 'approve_for_final', 'Document looks good', $reviewerId);

// 4. Final approval (Admin)
$documentModel->approveDocument($documentId, 'Approved for use', $adminId);
```

### Checking User Permissions
```php
// Check if user can review
if (isReviewer() || isLabManager() || isAdmin()) {
    // Show review interface
}

// Check if user can give final approval
if (isAdmin() || isSuperAdmin()) {
    // Show final approval interface
}

// Check if user can view approval dashboard
if (isAdmin() || isSuperAdmin()) {
    // Show admin dashboard
}
```

## Security Features

### 1. Access Control
- **Role-Based**: Only authorized users can perform actions
- **Department Restrictions**: Users limited to their departments
- **Document-Level**: Individual document permissions

### 2. Audit Trail
- **Complete Logging**: Every action recorded
- **Immutable History**: Cannot be modified after creation
- **User Attribution**: Clear responsibility chain

### 3. Digital Signatures
- **Timestamp Verification**: Exact approval time
- **User Verification**: Confirmed user identity
- **IP Tracking**: Security audit capability

## Installation Steps

### 1. Database Setup
```sql
-- Run the simple approval system script
SOURCE simple_approval_system.sql;
```

### 2. Update Routes
```php
// Add approval routes to Routes.php
$routes->get('documents/approval-dashboard', 'Documents::approvalDashboard');
$routes->get('documents/my-reviews', 'Documents::myReviews');
$routes->get('documents/my-approvals', 'Documents::myApprovals');
$routes->get('documents/submit-for-review/(:num)', 'Documents::submitForReview/$1');
$routes->post('documents/process-submit-for-review/(:num)', 'Documents::processSubmitForReview/$1');
$routes->get('documents/review/(:num)', 'Documents::reviewDocument/$1');
$routes->post('documents/process-review/(:num)', 'Documents::processReview/$1');
$routes->get('documents/approve/(:num)', 'Documents::approveDocument/$1');
$routes->post('documents/process-approval/(:num)', 'Documents::processApproval/$1');
$routes->get('documents/approval-history/(:num)', 'Documents::approvalHistory/$1');
$routes->post('documents/lock/(:num)', 'Documents::lockDocument/$1');
```

### 3. Navigation Updates
Add approval links to your navigation based on user roles:
```php
<?php if (isAdmin() || isSuperAdmin()): ?>
    <a href="<?= base_url('documents/approval-dashboard') ?>">Approval Dashboard</a>
<?php endif ?>

<?php if (isReviewer() || isLabManager()): ?>
    <a href="<?= base_url('documents/my-reviews') ?>">My Reviews</a>
<?php endif ?>

<?php if (isApprover() || isAdmin()): ?>
    <a href="<?= base_url('documents/my-approvals') ?>">My Approvals</a>
<?php endif ?>
```

This simple approval system provides all the functionality you need for document approval while maintaining simplicity and integration with your existing role-based access control system.