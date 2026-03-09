# Document Management System - Complete Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Core Functionalities](#core-functionalities)
3. [Document Approval Process Flow](#document-approval-process-flow)
4. [User Roles & Permissions](#user-roles--permissions)
5. [Database Architecture](#database-architecture)
6. [Technical Implementation](#technical-implementation)

---

## System Overview

This is a comprehensive **Document Management System (DMS)** built with CodeIgniter 4 that provides document lifecycle management with a robust approval workflow system. The system implements role-based access control (RBAC) and tracks all user activities.

### Key Features
- Document creation, editing, and management
- Multi-stage approval workflow (Review → Approval → Publish)
- Version control and document history
- Document sharing with granular permissions
- Advanced search with full-text indexing
- Automated backup system
- Activity logging and audit trails
- Department-based document organization
- Document type categorization

---

## Core Functionalities

### 1. Authentication & Authorization System

**Components:**
- `Auth Controller` - Handles login/logout
- `AuthFilter` - Protects routes requiring authentication
- `PermissionFilter` - Enforces permission-based access
- `permission_helper.php` - Helper functions for permission checks

**Features:**
- Secure password hashing (bcrypt)
- Session-based authentication
- Role-based access control (RBAC)
- Permission-level granular control
- Activity logging for all login attempts

**Key Functions:**
```php
userHasPermission($permission)  // Check if user has specific permission
requirePermission($permission)   // Enforce permission or redirect
```

### 2. User Management

**Controller:** `Users.php`

**Features:**
- Create, read, update, delete users
- Assign roles to users
- Assign departments to users
- Activate/deactivate user accounts
- View user activity logs

**User Fields:**
- Username (unique)
- Email (unique)
- Password (hashed)
- Status (active/inactive/suspended)
- Department assignment
- Role assignment (via user_roles table)

### 3. Role & Permission Management

**Controllers:** `Roles.php`, `Permissions.php`

**Role System:**
- Predefined roles: Super Admin, Admin, Lab Manager, Reviewer, Approver, Auditor
- Custom role creation
- Multiple permissions per role
- Role-permission mapping

**Permission Categories:**
- User management (create, read, update, delete)
- Role management
- Document management (create, read, update, delete, approve)
- Department management
- Document type management
- Activity log viewing

### 4. Department Management

**Controller:** `Departments.php`

**Features:**
- Create/edit/delete departments
- View document count per department
- Department-based document filtering
- Department-based user assignment

**Use Cases:**
- Organize documents by business units
- Restrict document access by department
- Assign reviewers from same department

### 5. Document Type Management

**Controller:** `DocumentTypes.php`

**Features:**
- Create/edit/delete document types
- View document count per type
- Type-based document categorization

**Examples:**
- Policy Documents
- Standard Operating Procedures (SOPs)
- Work Instructions
- Forms
- Reports

### 6. Document Management

**Controller:** `Documents.php`
**Model:** `Document.php`

**Core Document Operations:**

#### Create Document
- Title, content, type, department
- Status: draft/active/archived
- Approval status: pending (default)
- Effective date and review date
- Metadata support

#### Edit Document
- Only creators can edit draft documents
- Admins can edit any document
- Approved documents cannot be edited (must create new version)

#### Delete Document
- Only creators can delete draft documents
- Super admins can delete any document
- Cascade deletion of related records

#### View Document
- Department-based access control
- Display document details
- Show approval history
- View version history

### 7. Document Version Control

**Model:** `DocumentVersion.php`

**Features:**
- Automatic version creation on updates
- Version numbering (1.0, 1.1, 1.2, etc.)
- Change description tracking
- Version comparison
- Restore previous versions
- Version history view

**Version Fields:**
- Version number
- Title snapshot
- Content snapshot
- File path (if uploaded)
- File hash (for integrity)
- Changes description
- Created by user
- Timestamp

### 8. Document Sharing

**Model:** `DocumentShare.php`

**Sharing Options:**
- Share with specific user
- Share with role
- Share with department
- Permission levels: view, edit, full
- Expiration date support

**Features:**
- Track who shared the document
- View all shares for a document
- Check user access permissions
- Automatic expiration handling

### 9. Document Search & Indexing

**Controller:** `DocumentSearch.php`
**Model:** `DocumentSearchIndex.php`

**Search Capabilities:**
- Full-text search on title and content
- Filter by document type
- Filter by department
- Filter by status
- Filter by creator
- Tag-based search
- Keyword search

**Indexing:**
- Automatic indexing on document creation
- Manual re-indexing support
- Search term extraction
- Content indexing
- Tag and keyword indexing

### 10. Document Backup System

**Model:** `DocumentBackup.php`

**Features:**
- Automated backup creation
- Backup path storage
- Backup size tracking
- Retention policy support
- Expired backup cleanup
- Restore from backup

### 11. Document Workflow System

**Model:** `DocumentWorkflow.php`

**Workflow Types:**
- Review workflow
- Approval workflow
- Publish workflow

**Workflow Statuses:**
- Pending
- In Progress
- Completed
- Rejected

**Features:**
- Assign workflow to users
- Set due dates
- Track workflow progress
- Add comments
- Completion timestamps

### 12. Activity Logging

**Controller:** `ActivityLogs.php`
**Model:** `UserActivityLog.php`

**Logged Activities:**
- User login/logout
- Document creation/editing/deletion
- Approval actions
- Role/permission changes
- User management actions
- Department/type management
- Search activities

**Log Fields:**
- User ID
- Action description
- Timestamp
- IP address
- Additional details

---

## Document Approval Process Flow

### Overview
The system implements a **multi-stage approval workflow** with the following stages:

1. **Pending** → Document created, awaiting submission
2. **Under Review** → Submitted to reviewer
3. **Reviewed** → Reviewer approved, awaiting final approval
4. **Approved** → Admin/Approver approved, document active
5. **Rejected** → Rejected at any stage
6. **Returned for Revision** → Sent back to creator for changes

### Detailed Approval Flow

#### Stage 1: Document Creation
```
Creator creates document
↓
Status: draft
Approval Status: pending
↓
Document saved in system
```

**Who can create:**
- Users with `document_create` permission
- Typically: Lab Managers, Admins, Super Admins

**Actions:**
- Fill in title, content, type, department
- Set effective date and review date
- Save as draft

#### Stage 2: Submit for Review
```
Creator/Lab Manager submits document
↓
Select Reviewer from same department
↓
Approval Status: sent_for_review
Reviewer assigned
↓
Notification to Reviewer (if implemented)
```

**Who can submit:**
- Document creator
- Lab Managers
- Admins
- Super Admins

**Process:**
1. Navigate to document
2. Click "Submit for Review"
3. Select reviewer from dropdown (filtered by department)
4. Submit

**Database Changes:**
```php
approval_status = 'sent_for_review'
reviewer_id = selected_reviewer_id
submitted_for_review_at = current_timestamp
```

**Approval History Log:**
```php
action = 'submitted_for_review'
performed_by = current_user_id
comments = 'Document submitted for review'
```

#### Stage 3: Review Process
```
Reviewer receives document
↓
Reviewer examines content
↓
Reviewer makes decision:
  - Approve for Final Approval
  - Reject
  - Return for Revision
```

**Who can review:**
- Assigned reviewer
- Lab Managers (can review any document in their department)
- Admins
- Super Admins

**Review Actions:**

##### A. Approve for Final Approval
```php
approval_status = 'reviewed'
reviewer_comments = reviewer's comments
reviewed_at = current_timestamp
```
Document moves to final approval stage.

##### B. Reject
```php
approval_status = 'rejected'
rejection_reason = reviewer's reason
rejected_at = current_timestamp
```
Document workflow ends. Creator must create new document or resubmit.

##### C. Return for Revision
```php
approval_status = 'returned_for_revision'
revision_comments = reviewer's feedback
returned_for_revision_at = current_timestamp
revision_count = revision_count + 1
```
Document sent back to creator for modifications.

**Review Form Fields:**
- Action dropdown (approve/reject/return)
- Comments textarea (required)
- Submit button

#### Stage 4: Final Approval
```
Admin/Approver receives reviewed document
↓
Admin examines document and review comments
↓
Admin makes decision:
  - Approve (Final)
  - Reject
```

**Who can approve:**
- Admins
- Super Admins
- Designated Approvers (if assigned)

**Approval Actions:**

##### A. Final Approval
```php
approval_status = 'approved'
status = 'active'  // Document becomes active
approver_comments = approver's comments
approved_at = current_timestamp
```
Document is now published and active in the system.

##### B. Final Rejection
```php
approval_status = 'rejected'
rejection_reason = approver's reason
rejected_at = current_timestamp
```
Document workflow ends.

#### Stage 5: Revision Handling
```
Creator receives returned document
↓
Creator makes required changes
↓
Creator resubmits document
↓
approval_status = 'pending'
reviewer_id = null
↓
Process starts again from Stage 2
```

**Resubmission Process:**
1. Creator edits document content
2. Clicks "Resubmit After Revision"
3. Document status resets to pending
4. Previous reviewer assignment cleared
5. Must go through review process again

### Approval Dashboard

**Route:** `/approval-dashboard`

**Purpose:** Central hub for monitoring all documents in approval workflow

**Sections:**
1. **Pending Documents** - Awaiting submission for review
2. **Sent for Review** - Currently with reviewers
3. **Sent for Approval** - Awaiting final approval
4. **Approved by Approver** - Approved documents
5. **Admin Approved** - Final approved documents
6. **Returned for Revision** - Documents needing changes
7. **Rejected Documents** - Rejected documents

**Quick Actions:**
- Quick Review (AJAX)
- Quick Approve (AJAX)
- Assign Reviewer (AJAX)
- Resubmit After Revision

### My Reviews Page

**Route:** `/documents/my-reviews`

**Purpose:** Shows documents assigned to current user for review

**Who can access:**
- Reviewers (see their assigned documents)
- Lab Managers (see all department documents)
- Admins (see all documents)

**Features:**
- List of documents pending review
- Quick access to review form
- Filter by status
- Sort by submission date

### My Approvals Page

**Route:** `/documents/my-approvals`

**Purpose:** Shows documents awaiting final approval

**Who can access:**
- Approvers
- Admins
- Super Admins

**Features:**
- List of reviewed documents
- Quick access to approval form
- View review comments
- Approve or reject

### Approval History

**Route:** `/documents/approval-history/:id`

**Purpose:** Complete audit trail of document approval process

**Information Displayed:**
- All actions taken on document
- Who performed each action
- When action was performed
- Comments/reasons for each action
- Status changes
- Timestamps

**History Actions Tracked:**
- submitted_for_review
- reviewed (approve_for_final)
- rejected
- return_for_revision
- resubmitted_after_revision
- approved (final)

### Role-Based Approval Permissions

#### Super Admin
- Can perform all actions
- Can approve documents directly without review
- Can override any workflow stage
- Can lock obsolete documents

#### Admin
- Can perform all actions except system configuration
- Can approve documents directly
- Can assign reviewers
- Can view all documents

#### Lab Manager
- Can create documents
- Can submit documents for review
- Can review documents in their department
- Can view department documents

#### Reviewer
- Can review assigned documents
- Can approve for final approval
- Can reject documents
- Can return documents for revision
- Cannot perform final approval

#### Approver
- Can perform final approval
- Can reject documents
- Cannot review documents (separate role)

#### Auditor
- Read-only access
- Can view all documents
- Can view approval history
- Cannot make changes

### Document Status vs Approval Status

**Document Status:**
- `draft` - Document being created/edited
- `active` - Published and in use
- `archived` - Obsolete/locked

**Approval Status:**
- `pending` - Created, not submitted
- `sent_for_review` - With reviewer
- `reviewed` - Reviewer approved
- `approved` - Final approval granted
- `rejected` - Rejected at any stage
- `returned_for_revision` - Sent back for changes

### Workflow State Machine

```
[pending] 
    ↓ (submit_for_review)
[sent_for_review]
    ↓ (approve_for_final)
[reviewed]
    ↓ (final_approve)
[approved] ← END STATE

Alternative paths:
[sent_for_review] → (reject) → [rejected] ← END STATE
[reviewed] → (reject) → [rejected] ← END STATE
[sent_for_review] → (return_for_revision) → [returned_for_revision]
[returned_for_revision] → (resubmit) → [pending] → cycle repeats
```

---

## User Roles & Permissions

### Role Hierarchy

1. **Super Admin** (ID: 1)
   - Full system access
   - Cannot be deleted
   - Can manage all users, roles, permissions
   - Can approve documents without review
   - Can lock obsolete documents

2. **Admin** (ID: 2)
   - Administrative access
   - Can manage users, roles, documents
   - Can perform final approvals
   - Can assign reviewers
   - Can view all activity logs

3. **Lab Manager**
   - Department-level management
   - Can create and submit documents
   - Can review documents in department
   - Can manage department users

4. **Reviewer**
   - Can review assigned documents
   - Can approve for final approval
   - Can reject or return for revision
   - Department-scoped access

5. **Approver**
   - Can perform final approvals
   - Can reject documents
   - Department or system-wide scope

6. **Auditor**
   - Read-only access
   - Can view all documents
   - Can view approval history
   - Cannot make changes

### Permission Keys

**User Management:**
- `user_create`
- `user_read`
- `user_update`
- `user_delete`

**Role Management:**
- `role_create`
- `role_update`
- `role_delete`

**Document Management:**
- `document_create`
- `document_read`
- `document_edit`
- `document_delete`
- `document_approve`
- `document_review`

**Department Management:**
- `department_create`
- `department_update`
- `department_delete`

**Document Type Management:**
- `document_type_create`
- `document_type_update`
- `document_type_delete`

**Activity Logs:**
- `view_activity_logs`

---

## Database Architecture

### Core Tables

#### 1. users
```sql
- id (PK)
- username (unique)
- password_hash
- email (unique)
- status (active/inactive/suspended)
- department_id (FK)
- created_at
- updated_at
```

#### 2. roles
```sql
- id (PK)
- role_name (unique)
- description
- created_at
- updated_at
```

#### 3. permissions
```sql
- id (PK)
- permission_key (unique)
- description
- created_at
- updated_at
```

#### 4. user_roles (junction table)
```sql
- user_id (FK)
- role_id (FK)
- PRIMARY KEY (user_id, role_id)
```

#### 5. role_permissions (junction table)
```sql
- role_id (FK)
- permission_id (FK)
- PRIMARY KEY (role_id, permission_id)
```

#### 6. departments
```sql
- id (PK)
- name
- description
- created_at
- updated_at
```

#### 7. document_types
```sql
- id (PK)
- name
- description
- created_at
- updated_at
```

#### 8. documents
```sql
- id (PK)
- title
- content (LONGTEXT)
- type_id (FK)
- department_id (FK)
- status (draft/active/archived)
- approval_status (pending/sent_for_review/reviewed/approved/rejected/returned_for_revision)
- reviewer_id (FK to users)
- reviewer_comments
- reviewed_at
- approver_id (FK to users)
- approver_comments
- approved_at
- rejection_reason
- rejected_at
- returned_for_revision_at
- revision_comments
- revision_count
- submitted_for_review_at
- submitted_for_approval_at
- effective_date
- review_date
- created_by (FK to users)
- created_at
- updated_at
```

#### 9. document_versions
```sql
- id (PK)
- document_id (FK)
- version_number
- title
- content
- file_path
- file_hash
- changes_description
- created_by (FK to users)
- created_at
- updated_at
```

#### 10. document_shares
```sql
- id (PK)
- document_id (FK)
- shared_with_user_id (FK, nullable)
- shared_with_role_id (FK, nullable)
- shared_with_department_id (FK, nullable)
- permission_level (view/edit/full)
- expiration_date
- created_by (FK to users)
- created_at
- updated_at
```

#### 11. document_search_index
```sql
- id (PK)
- document_id (FK)
- search_terms (FULLTEXT)
- indexed_content (FULLTEXT)
- tags
- keywords
- indexed_at
- created_at
- updated_at
```

#### 12. document_workflows
```sql
- id (PK)
- document_id (FK)
- workflow_type (review/approval/publish)
- current_status (pending/in_progress/completed/rejected)
- assigned_to (FK to users)
- due_date
- comments
- created_at
- updated_at
- completed_at
```

#### 13. document_backups
```sql
- id (PK)
- document_id (FK)
- backup_path
- backup_size
- backup_date
- retention_policy
```

#### 14. document_metadata
```sql
- id (PK)
- document_id (FK)
- meta_key
- meta_value
```

#### 15. document_approval_history
```sql
- id (PK)
- document_id (FK)
- action
- performed_by (FK to users)
- comments
- previous_status
- new_status
- created_at
```

#### 16. user_activity_logs
```sql
- id (PK)
- user_id (FK)
- action
- timestamp
- ip_address
- details
```

### Database Relationships

```
users (1) ←→ (M) user_roles (M) ←→ (1) roles
roles (1) ←→ (M) role_permissions (M) ←→ (1) permissions
users (1) ←→ (M) documents (created_by)
users (1) ←→ (M) documents (reviewer_id)
users (1) ←→ (M) documents (approver_id)
departments (1) ←→ (M) documents
departments (1) ←→ (M) users
document_types (1) ←→ (M) documents
documents (1) ←→ (M) document_versions
documents (1) ←→ (M) document_shares
documents (1) ←→ (M) document_workflows
documents (1) ←→ (M) document_backups
documents (1) ←→ (1) document_search_index
documents (1) ←→ (M) document_approval_history
users (1) ←→ (M) user_activity_logs
```

---

## Technical Implementation

### Framework & Architecture
- **Framework:** CodeIgniter 4
- **Architecture:** MVC (Model-View-Controller)
- **Database:** MySQL
- **Authentication:** Session-based
- **Password Hashing:** bcrypt (PASSWORD_DEFAULT)

### Key Design Patterns

#### 1. Repository Pattern
Models act as repositories for database operations:
```php
$this->documentModel->getDocumentById($id);
$this->documentModel->submitForReview($id, $reviewerId, $userId);
```

#### 2. Helper Functions
Permission checking abstracted into helpers:
```php
userHasPermission('document_create');
requirePermission('document_approve', '/documents');
```

#### 3. Filters (Middleware)
- `AuthFilter` - Authentication check
- `PermissionFilter` - Permission enforcement

#### 4. Activity Logging
Centralized logging through UserActivityLog model:
```php
$this->logModel->logActivity($userId, 'action', 'details');
```

### Security Features

1. **Password Security**
   - Bcrypt hashing
   - Automatic hashing on insert/update

2. **SQL Injection Prevention**
   - Query Builder usage
   - Prepared statements
   - Parameter binding

3. **CSRF Protection**
   - Built-in CodeIgniter CSRF tokens
   - Form validation

4. **XSS Prevention**
   - Input escaping
   - Output encoding

5. **Access Control**
   - Role-based permissions
   - Department-based restrictions
   - Document ownership checks

6. **Audit Trail**
   - All actions logged
   - IP address tracking
   - Timestamp recording

### API Endpoints

#### AJAX Endpoints
```php
POST /documents/quick-review/:id
POST /documents/quick-approve/:id
POST /documents/assign-reviewer/:id
POST /documents/resubmit-after-revision/:id
GET  /api/reviewers/:departmentId
```

#### Search API
```php
GET /api/search?q=term&type_id=1&department_id=2
POST /api/search/index/:id
DELETE /api/search/index/:id
```

### File Structure
```
app/
├── Controllers/
│   ├── Auth.php
│   ├── Users.php
│   ├── Roles.php
│   ├── Permissions.php
│   ├── Documents.php
│   ├── Departments.php
│   ├── DocumentTypes.php
│   ├── DocumentSearch.php
│   └── ActivityLogs.php
├── Models/
│   ├── User.php
│   ├── Role.php
│   ├── Permission.php
│   ├── Document.php
│   ├── DocumentVersion.php
│   ├── DocumentShare.php
│   ├── DocumentWorkflow.php
│   ├── DocumentBackup.php
│   ├── DocumentSearchIndex.php
│   ├── Department.php
│   ├── DocumentType.php
│   └── UserActivityLog.php
├── Filters/
│   ├── AuthFilter.php
│   └── PermissionFilter.php
├── Helpers/
│   └── permission_helper.php
├── Views/
│   ├── auth/
│   ├── dashboard/
│   ├── users/
│   ├── roles/
│   ├── permissions/
│   ├── documents/
│   ├── departments/
│   ├── document_types/
│   └── activity_logs/
└── Database/
    ├── Migrations/
    └── Seeds/
```

### Key Methods in Documents Controller

**Document CRUD:**
- `index()` - List documents with filters
- `view($id)` - View single document
- `create()` - Show create form
- `store()` - Save new document
- `edit($id)` - Show edit form
- `update($id)` - Update document
- `delete($id)` - Delete document

**Approval Workflow:**
- `submitForReview($id)` - Show reviewer selection
- `processSubmitForReview($id)` - Submit to reviewer
- `reviewDocument($id)` - Show review form
- `processReview($id)` - Process review decision
- `approveDocument($id)` - Show approval form
- `processApproval($id)` - Process final approval
- `resubmitAfterRevision($id)` - Resubmit after changes

**Dashboard & Lists:**
- `approvalDashboard()` - Central approval monitoring
- `myReviews()` - Documents for current user to review
- `myApprovals()` - Documents for current user to approve
- `approvalHistory($id)` - View approval audit trail

**Quick Actions (AJAX):**
- `quickReview($id)` - Fast review action
- `quickApprove($id)` - Fast approval action
- `assignReviewer($id)` - Assign reviewer from dashboard

**Utility Methods:**
- `lockDocument($id)` - Archive obsolete document
- `getReviewersByDepartment($departmentId)` - Get available reviewers

### Key Methods in Document Model

**Approval Methods:**
- `submitForReview($documentId, $reviewerId, $userId)`
- `reviewDocument($documentId, $action, $comments, $userId)`
- `approveDocument($documentId, $comments, $userId)`
- `rejectDocument($documentId, $reason, $userId)`
- `logApprovalAction($documentId, $action, $userId, $comments)`
- `getApprovalHistory($documentId)`

**Query Methods:**
- `getDocumentById($id)`
- `getDocumentsByStatus($status)`
- `getDocumentsByApprovalStatus($status)`
- `getDocumentsForReview($userId)`
- `getDocumentsForApproval($userId)`
- `getReviewersByDepartment($departmentId)`
- `getApproversByDepartment($departmentId)`

**Version Control:**
- `createVersion($documentId, $changesDescription, $userId)`
- `getDocumentHistory($documentId)`
- `restoreVersion($documentId, $versionId)`

**Sharing:**
- `shareDocument($documentId, $shareData)`
- `getDocumentShares($documentId)`

**Search:**
- `searchDocuments($searchTerm, $filters)`

**Workflow:**
- `createWorkflow($documentId, $workflowData)`
- `getDocumentWorkflows($documentId)`
- `updateWorkflowStatus($workflowId, $status, $comments)`

**Backup:**
- `createBackup($documentId, $backupPath, $backupSize)`
- `getDocumentBackups($documentId)`

---

## Summary

This Document Management System provides a complete solution for managing documents through their entire lifecycle with a robust approval workflow. The system ensures:

1. **Security** - Role-based access control with granular permissions
2. **Traceability** - Complete audit trail of all actions
3. **Compliance** - Multi-stage approval process with review and approval
4. **Collaboration** - Document sharing and workflow assignment
5. **Version Control** - Track all changes with version history
6. **Search** - Full-text search with advanced filtering
7. **Backup** - Automated backup and retention
8. **Flexibility** - Configurable roles, permissions, and workflows

The approval process ensures that documents go through proper review and approval stages before becoming active, with the ability to reject or request revisions at any stage. All actions are logged for audit purposes, and the system supports department-based organization and role-based access control.
