<?php

namespace App\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\UserActivityLog;
use App\Models\DocumentAttachment;
use App\Models\NotificationModel;

class Documents extends BaseController
{
    protected $documentModel;
    protected $documentTypeModel;

    protected $notificationModel;
    protected $departmentModel;
    protected $logModel;
    protected $attachmentModel;
    protected $db;
    protected $preparedByDebug = [];

    public function __construct()
    {
        $this->documentModel = new Document();
        $this->documentTypeModel = new DocumentType();
        $this->departmentModel = new Department();
        $this->logModel = new UserActivityLog();
        $this->attachmentModel = new DocumentAttachment();
        $this->notificationModel = new NotificationModel();
        $this->db = \Config\Database::connect();
        helper(['permission', 'form', 'attachment']);
    }

    // Helper method to get user role name
    private function getUserRole()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return null;
        }

        $result = $this->db->table('users')
            ->select('roles.role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $userId)
            ->get()
            ->getRow();

        return $result ? $result->role_name : null;
    }

    // Role checking helper methods
    private function isSuperAdmin($userId = null)
    {
        $userId = $userId ?? session()->get('user_id');
        $role = $this->getUserRoleForUser($userId);
        return $role === 'superadmin';
    }

    private function isAdmin($userId = null)
    {
        $userId = $userId ?? session()->get('user_id');
        $role = $this->getUserRoleForUser($userId);
        return in_array($role, ['superadmin', 'admin', 'dept_admin']);
    }

    private function isLabManager($userId = null)
    {
        $userId = $userId ?? session()->get('user_id');
        $role = $this->getUserRoleForUser($userId);
        return $role === 'lab_manager';
    }

    private function isReviewer($userId = null)
    {
        $userId = $userId ?? session()->get('user_id');
        $role = $this->getUserRoleForUser($userId);
        return $role === 'reviewer';
    }

    private function isApprover($userId = null)
    {
        $userId = $userId ?? session()->get('user_id');
        $role = $this->getUserRoleForUser($userId);
        return $role === 'approver';
    }

    private function isAuditor($userId = null)
    {
        $userId = $userId ?? session()->get('user_id');
        $role = $this->getUserRoleForUser($userId);
        return $role === 'auditor';
    }

    private function getUserRoleForUser($userId)
    {
        if (!$userId) {
            return null;
        }

        $result = $this->db->table('users')
            ->select('roles.role_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $userId)
            ->get()
            ->getRow();

        return $result ? $result->role_name : null;
    }

    public function index()
    {
        $filters = [
            'q' => $this->request->getGet('q'),
            'type_id' => $this->request->getGet('type_id'),
            'department_id' => $this->request->getGet('department_id'),
            'status' => $this->request->getGet('review_status') // align with form
        ];

        $userId = session()->get('user_id');
        $roleId = $this->getUserRoleId($userId);
        $userDepartmentId = $this->getUserDepartmentId($userId);

        $builder = $this->db->table('documents d')
            ->select('d.*, dt.name as type_name, d.document_number, dept.name as department_name, u.name as created_by_name')
            ->join('document_types dt', 'dt.id = d.type_id')
            ->join('departments dept', 'dept.id = d.department_id')
            ->join('users u', 'u.id = d.created_by');

        // Apply search filters
        if (!empty($filters['q'])) {
            $builder->groupStart()
                ->like('d.title', $filters['q'])
                ->orLike('d.content', $filters['q'])
                ->groupEnd();
        }

        if (!empty($filters['type_id'])) {
            $builder->where('d.type_id', $filters['type_id']);
        }

        if (!empty($filters['department_id'])) {
            $builder->where('d.department_id', $filters['department_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('d.status', $filters['status']);
        }

        // Apply role-based filtering
        if (!$this->isSuperAdmin($userId)) {
            if ($roleId == 1 || $roleId == 2) { // Admin role
                // Admin can see all documents
            } else {
                // Other roles can only see documents from their department
                $builder->where('d.department_id', $userDepartmentId);
            }
        }

        $documents = $builder->orderBy('d.created_at', 'DESC')->get()->getResultArray();

        // Group documents hierarchically: Type -> Department -> Documents
        $groupedDocuments = [];
        foreach ($documents as $doc) {
            $typeId = $doc['type_id'];
            $deptId = $doc['department_id'];

            if (!isset($groupedDocuments[$typeId])) {
                $groupedDocuments[$typeId] = [
                    'name' => $doc['type_name'],
                    'departments' => []
                ];
            }

            if (!isset($groupedDocuments[$typeId]['departments'][$deptId])) {
                $groupedDocuments[$typeId]['departments'][$deptId] = [
                    'name' => $doc['department_name'],
                    'documents' => []
                ];
            }

            $groupedDocuments[$typeId]['departments'][$deptId]['documents'][] = $doc;
        }

        $data = [
            'groupedDocuments' => $groupedDocuments,
            'totalDocuments' => count($documents),
            'documentTypes' => $this->documentTypeModel->findAll(),
            'departments' => $this->departmentModel->findAll(),
            'filters' => $filters,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole(),
            'can_create_documents' => userHasPermission('document_create'),
            'can_edit_documents' => userHasPermission('document_update'),
            'can_delete_documents' => userHasPermission('document_delete')
        ];

        $notifications = $this->notificationModel->getUnread(session()->get('user_id'));
        $data['notifications'] = $notifications;

        $this->logModel->logActivity($userId, 'Viewed documents list');
        return view('documents/index', $data);
    }

    public function view($id)
    {
        $document = $this->documentModel->getDocumentById($id);

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        // Check access permissions
        $userId = session()->get('user_id');
        // if (!$this->canAccessDocument($document, $userId)) {
        //     return redirect()->to('/documents')->with('error', 'You do not have permission to view this document');
        // }

        // Fix image paths in content - convert relative paths to absolute URLs
        if (!empty($document['content'])) {
            $document['content'] = $this->fixImagePaths($document['content']);
        }

        // Get the original creator's information for "Prepared By"
        $userModel = new \App\Models\User();
        $creator = $userModel->find($document['created_by']);

        // Replace "Prepared By" information in document content
        if ($creator && !empty($document['content'])) {
            $document['content'] = $this->replacePreparedByInfo($document['content'], $creator);
        }

        // Replace "Checked By" and "Approved By" information
        if (!empty($document['content'])) {
            $document['content'] = $this->replaceApprovalInfo($document['content'], $document);
        }

        // Get template and form data if exists
        $templateModel = new \App\Models\DocumentTemplate();
        $fieldModel = new \App\Models\TemplateField();

        $template = $templateModel->getTemplateByTypeId($document['type_id']);
        $fields = $template ? $fieldModel->getFieldsBySection($template['id']) : [];
        $formData = isset($document['form_data']) && $document['form_data'] ? json_decode($document['form_data'], true) : [];

        $reviewers = $this->documentModel->getReviewersByDepartment($document['department_id']);
        $approvers = $this->documentModel->getApproversByDepartment($document['department_id']);

        $data = [
            'document' => $document,
            'template' => $template,
            'fields' => $fields,
            'formData' => $formData,
            'reviewers' => $reviewers,
            'approvers' => $approvers,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole()
        ];

        // print_r($document);die;

        $this->logModel->logActivity($userId, 'Viewed document', 'Document ID: ' . $id);
        return view('documents/view', $data);
    }

    public function createold()
    {
        if (!userHasPermission('document_create')) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to create documents');
        }

        $documentTypes = $this->documentTypeModel->findAll();
        $departments = $this->departmentModel->findAll();

        $data = [
            'documentTypes' => $documentTypes,
            'departments' => $departments,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole()
        ];
        return view('documents/create', $data);
    }

    public function create()
    {
        if (!userHasPermission('document_create')) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to create documents');
        }

        $documentTypes = $this->documentTypeModel->findAll();

        $roleId = session()->get('role_id');
        $departmentId = session()->get('department_id');

        // Filter departments based on role
        if (in_array($roleId, [1, 2])) {
            // Superadmin or Admin → all departments
            $departments = $this->departmentModel->findAll();
        } else {
            // Other roles → only their own department
            $departments = $this->departmentModel->where('id', $departmentId)->findAll();
        }

        $data = [
            'documentTypes' => $documentTypes,
            'departments'   => $departments,
            'username'      => session()->get('username'),
            'role_name'     => $this->getUserRole()
        ];

        // log access to the creation form
        $this->logModel->logActivity(session()->get('user_id'), 'Opened document creation form');

        return view('documents/create', $data);
    }

    public function getTemplateByType($typeId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $templateModel = new \App\Models\DocumentTemplate();

        $template = $templateModel->getTemplateByTypeId($typeId);
        // print_r($template);die;
        if (!$template) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No template found for this document type'
            ]);
        }

        // Generate next document number
        $nextDocNumber = $this->generateNextDocumentNumber($typeId);

        // Get current user information for "Prepared By"
        $userId = session()->get('user_id');
        $userModel = new \App\Models\User();
        $currentUser = $userModel->find($userId);
        // print_r($currentUser); die;
        // Replace document number placeholder in template
        $layoutTemplate = $template['layout_template'] ?? '';
        $originalTemplate = $layoutTemplate;
        $layoutTemplate = $this->replaceDocumentNumber($layoutTemplate, $nextDocNumber);

        // Replace "Prepared By" information in approval table
        if ($currentUser) {
            $layoutTemplate = $this->replacePreparedByInfo($layoutTemplate, $currentUser);
        }

        return $this->response->setJSON([
            'success' => true,
            'template' => $template,
            'layout_template' => $layoutTemplate,
            'document_number' => $nextDocNumber,
            '$currentUser' => $currentUser,
            'debug' => [
                'generated_number' => $nextDocNumber,
                'replacement_made' => $originalTemplate !== $layoutTemplate,
                'has_doc_id' => strpos($layoutTemplate, 'id="doc-number"') !== false || strpos($layoutTemplate, "id='doc-number'") !== false,
                'prepared_by_info' => $this->preparedByDebug
            ]
        ]);
    }

    private function generateNextDocumentNumber($typeId)
    {
        // Get document type details
        $documentType = $this->db->table('document_types')->where('id', $typeId)->get()->getRowArray();

        if (!$documentType) {
            return 'DOC/001/001';
        }

        // Try to extract main series from existing document number in template
        $template = $this->db->table('document_templates')->where('document_type_id', $typeId)->get()->getRowArray();
        $mainSeries = null;

        if ($template && !empty($template['layout_template'])) {
            // Try multiple patterns to find document number in template
            // Pattern matches: SSP/MR/001 or SSP/MR/001/001 (we want the main series part)
            $patterns = [
                '/(?:Doc\.\s*No\.?|Document\s*No\.?)[^A-Z]*([A-Z]+(?:\/[A-Z]+)*\/\d+)/i',
                '/<span[^>]*id=["\']doc-number["\'][^>]*>([A-Z]+(?:\/[A-Z]+)*\/\d+)(?:\/\d+)?<\/span>/i',
                '/([A-Z]+(?:\/[A-Z]+)*\/\d+)(?:\/\d+)?/'
            ];

            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $template['layout_template'], $matches)) {
                    $mainSeries = $matches[1];
                    break;
                }
            }
        }

        // If no main series found in template, use a default
        if (empty($mainSeries)) {
            $mainSeries = 'DOC/001';
        }

        // Get the last document number for this type with the same main series
        $lastDoc = $this->db->table('documents')
            ->where('type_id', $typeId)
            ->where('document_number IS NOT NULL')
            ->where('document_number !=', '')
            ->like('document_number', $mainSeries . '/', 'after') // Match documents starting with main series
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        // Determine next number
        if ($lastDoc && !empty($lastDoc['document_number'])) {
            // Extract the last number from document number (e.g., "002" from "SSP/MR/001/002")
            preg_match('/\/(\d+)$/', $lastDoc['document_number'], $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Format with leading zeros (3 digits)
        $formattedNumber = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return $mainSeries . '/' . $formattedNumber;
    }

    private function replaceDocumentNumber($content, $newDocNumber)
    {
        // Method 1: Replace by ID (most reliable if template has id="doc-number")
        $content = preg_replace(
            '/(<span[^>]*id=["\']doc-number["\'][^>]*>)[^<]+(<\/span>)/i',
            '${1}' . $newDocNumber . '${2}',
            $content
        );

        // Method 2: Replace document number in table cell structure
        // Matches: <td>Doc. No.</td><td><span>SSP/MR/001/001</span></td>
        $content = preg_replace_callback(
            '/(<td[^>]*>.*?Doc\.\s*No\.?.*?<\/td>\s*<td[^>]*>(?:<span[^>]*>)?)([A-Z\/]+\/\d+(?:\/\d+)?)(?:<\/span>)?(<\/td>)/i',
            function ($matches) use ($newDocNumber) {
                return $matches[1] . $newDocNumber . $matches[3];
            },
            $content
        );

        return $content;
    }

    /**
     * Replace "Prepared By" information in approval table
     * Returns array with content and debug info
     */
    private function replacePreparedByInfo($content, $user)
    {
        $debug = [];
        $debug['user_id'] = $user['id'] ?? 'unknown';
        $debug['started'] = true;

        // Check if approval table exists
        if (!preg_match('/[a-z0-9]+-approval-table/i', $content)) {
            $debug['approval_table_found'] = false;
            $this->preparedByDebug = $debug;
            return $content;
        }

        $debug['approval_table_found'] = true;

        // Get user's full name or username
        $userName = esc($user['name'] ?? $user['username'] ?? '');
        $sign = esc($user['sign'] ?? $user['sign'] ?? '');

        $debug['user_name'] = $userName;
        $debug['sign_present'] = !empty($sign);
        $debug['sign_value'] = $sign;

        // Get user's designation from users table
        $userRole = esc($user['designation'] ?? '');
        $debug['user_designation'] = $userRole;

        // Extract font styling from the table to apply to dynamic content
        $fontStyle = '';
        if (preg_match('/<span[^>]*style=["\']([^"\']*font[^"\']*)["\'][^>]*>/i', $content, $styleMatches)) {
            $fontStyle = ' style="' . $styleMatches[1] . '"';
            $debug['font_style_detected'] = $styleMatches[1];
        } elseif (preg_match('/<span[^>]*style=["\']([^"\']*)["\'][^>]*>/i', $content, $styleMatches)) {
            // Get any existing span style from the table
            $fontStyle = ' style="' . $styleMatches[1] . '"';
            $debug['generic_style_detected'] = $styleMatches[1];
        } else {
            $debug['style_detected'] = false;
        }

        // Replace Name row - check if span with ID already exists, otherwise create it
        $nameReplaced = false;

        // First try to update existing span with ID
        if (preg_match('/<span[^>]*id=["\']prepared-by-name["\'][^>]*>.*?<\/span>/is', $content)) {
            $debug['name_span_exists'] = true;
            $content = preg_replace(
                '/(<span[^>]*id=["\']prepared-by-name["\'][^>]*>).*?(<\/span>)/is',
                '$1' . $userName . '$2',
                $content,
                1
            );
            $nameReplaced = true;
        } else {
            // Create new span if it doesn't exist
            $content = preg_replace_callback(
                '/(<table[^>]*id=["\']/[a-z0-9]+-approval-table/i["\'][^>]*>.*?<tr[^>]*>.*?<td[^>]*>.*?Name:.*?<\/td>\s*<td[^>]*>)(&nbsp;|\s*|<span[^>]*>.*?<\/span>)(<\/td>)/is',
                function ($matches) use ($userName, $fontStyle, &$nameReplaced) {
                    $nameReplaced = true;
                    // Check if there's existing styling in the original content
                    $existingStyle = '';
                    if (preg_match('/<span[^>]*style=["\']([^"\']*)["\'][^>]*>/i', $matches[2], $styleMatch)) {
                        $existingStyle = ' style="' . $styleMatch[1] . '"';
                    } else {
                        $existingStyle = $fontStyle;
                    }

                    return $matches[1] . '<span id="prepared-by-name"' . $existingStyle . '>' . $userName . '</span>' . $matches[3];
                },
                $content,
                1
            );
        }

        if ($nameReplaced) {
            $debug['name_replaced'] = true;
        } else {
            $debug['name_replaced'] = false;
        }

        // Replace Designation row - check if span with ID already exists, otherwise create it
        $designationReplaced = false;

        // First try to update existing span with ID
        if (preg_match('/<span[^>]*id=["\']prepared-by-designation["\'][^>]*>.*?<\/span>/is', $content)) {
            $debug['designation_span_exists'] = true;
            $content = preg_replace(
                '/(<span[^>]*id=["\']prepared-by-designation["\'][^>]*>).*?(<\/span>)/is',
                '$1' . $userRole . '$2',
                $content,
                1
            );
            $designationReplaced = true;
        } else {
            // Create new span if it doesn't exist
            $content = preg_replace_callback(
                '/(<table[^>]*id=["\']/[a-z0-9]+-approval-table/i["\'][^>]*>.*?<tr[^>]*>.*?<td[^>]*>.*?Designation:.*?<\/td>\s*<td[^>]*>)(&nbsp;|\s*|<span[^>]*>.*?<\/span>)(<\/td>)/is',
                function ($matches) use ($userRole, $fontStyle, &$designationReplaced) {
                    $designationReplaced = true;
                    // Check if there's existing styling in the original content
                    $existingStyle = '';
                    if (preg_match('/<span[^>]*style=["\']([^"\']*)["\'][^>]*>/i', $matches[2], $styleMatch)) {
                        $existingStyle = ' style="' . $styleMatch[1] . '"';
                    } else {
                        $existingStyle = $fontStyle;
                    }

                    return $matches[1] . '<span id="prepared-by-designation"' . $existingStyle . '>' . $userRole . '</span>' . $matches[3];
                },
                $content,
                1
            );
        }

        if ($designationReplaced) {
            $debug['designation_replaced'] = true;
        } else {
            $debug['designation_replaced'] = false;
        }

        // Replace sign row - check if span with ID already exists, otherwise create it
        $signReplaced = false;

        // First try to update existing span with ID
        if (preg_match('/<span[^>]*id=["\']prepared-by-sign["\'][^>]*>.*?<\/span>/is', $content)) {
            $debug['sign_span_exists'] = true;
            $content = preg_replace(
                '/(<span[^>]*id=["\']prepared-by-sign["\'][^>]*>).*?(<\/span>)/is',
                '$1' . $sign . '$2',
                $content,
                1
            );
            $signReplaced = true;
        } else {
            // Create new span if it doesn't exist
            $content = preg_replace_callback(
                '/(<table[^>]*id=["\']/[a-z0-9]+-approval-table/i["\'][^>]*>.*?<tr[^>]*>.*?<td[^>]*>.*?Signature:.*?<\/td>\s*<td[^>]*>)(&nbsp;|\s*|<span[^>]*>.*?<\/span>)(<\/td>)/is',
                function ($matches) use ($sign, $fontStyle, &$signReplaced) {
                    $signReplaced = true;
                    // Check if there's existing styling in the original content
                    $existingStyle = '';
                    if (preg_match('/<span[^>]*style=["\']([^"\']*)["\'][^>]*>/i', $matches[2], $styleMatch)) {
                        $existingStyle = ' style="' . $styleMatch[1] . '"';
                    } else {
                        $existingStyle = $fontStyle;
                    }

                    return $matches[1] . '<span id="prepared-by-sign"' . $existingStyle . '>' . $sign . '</span>' . $matches[3];
                },
                $content,
                1
            );
        }

        if ($signReplaced) {
            $debug['signature_replaced'] = true;
        } else {
            $debug['signature_replaced'] = false;
        }

        $debug['completed'] = true;

        // Store debug info in class property
        $this->preparedByDebug = $debug;

        return $content;
    }

    /**
     * Replace "Checked By" (Reviewer) and "Approved By" (Approver) information in approval table
     */
    private function replaceApprovalInfo($content, $document)
    {
        log_message('info', 'replaceApprovalInfo: Starting for document ID: ' . ($document['id'] ?? 'unknown'));

        // Check if approval table exists
        if (!preg_match('/[a-z0-9]+-approval-table/i', $content)) {
            log_message('info', 'replaceApprovalInfo: No approval table found in content');
            return $content;
        }

        log_message('info', 'replaceApprovalInfo: Approval table found');
        log_message('info', 'replaceApprovalInfo: Reviewer ID: ' . ($document['reviewer_id'] ?? 'null') . ', Approver ID: ' . ($document['approver_id'] ?? 'null'));

        $userModel = new \App\Models\User();
        $db = \Config\Database::connect();

        // Extract font styling from the table
        $fontStyle = '';
        if (preg_match('/<span[^>]*style=["\']([^"\']*font[^"\']*)["\'][^>]*>/i', $content, $styleMatches)) {
            $fontStyle = ' style="' . $styleMatches[1] . '"';
        } elseif (preg_match('/<span[^>]*style=["\']([^"\']*)["\'][^>]*>/i', $content, $styleMatches)) {
            $fontStyle = ' style="' . $styleMatches[1] . '"';
        }

        // Handle "Reviewed By" (Reviewer) information - uses reviewed-by-* IDs
        if (!empty($document['reviewer_id'])) {
            log_message('info', 'replaceApprovalInfo: Processing reviewer ID: ' . $document['reviewer_id']);
            $reviewer = $userModel->find($document['reviewer_id']);

            if ($reviewer) {
                $reviewerName = esc($reviewer['name'] ?? $reviewer['username'] ?? '');
                $reviewerSign = esc($reviewer['sign'] ?? '');

                log_message('info', 'replaceApprovalInfo: Reviewer found - Name: ' . $reviewerName . ', Sign: ' . ($reviewerSign ? 'present' : 'empty'));

                // Get reviewer's designation from users table
                $reviewerRole = esc($reviewer['designation'] ?? '');
                log_message('info', 'replaceApprovalInfo: Reviewer designation: ' . $reviewerRole);

                // Replace Name in Reviewed By column
                if (preg_match('/<span[^>]*id=["\']reviewed-by-name["\'][^>]*>.*?<\/span>/is', $content)) {
                    log_message('info', 'replaceApprovalInfo: Found existing reviewed-by-name span, updating content');
                    $content = preg_replace(
                        '/(<span[^>]*id=["\']reviewed-by-name["\'][^>]*>).*?(<\/span>)/is',
                        '$1' . $reviewerName . '$2',
                        $content,
                        1
                    );
                    log_message('info', 'replaceApprovalInfo: Successfully replaced reviewer name');
                }

                // Replace Designation in Reviewed By column
                if (preg_match('/<span[^>]*id=["\']reviewed-by-designation["\'][^>]*>.*?<\/span>/is', $content)) {
                    log_message('info', 'replaceApprovalInfo: Found existing reviewed-by-designation span, updating content');
                    $content = preg_replace(
                        '/(<span[^>]*id=["\']reviewed-by-designation["\'][^>]*>).*?(<\/span>)/is',
                        '$1' . $reviewerRole . '$2',
                        $content,
                        1
                    );
                    log_message('info', 'replaceApprovalInfo: Successfully replaced reviewer designation');
                }

                // Replace Signature in Reviewed By column
                if (preg_match('/<span[^>]*id=["\']reviewed-by-sign["\'][^>]*>.*?<\/span>/is', $content)) {
                    log_message('info', 'replaceApprovalInfo: Found existing reviewed-by-sign span, updating content');
                    $content = preg_replace(
                        '/(<span[^>]*id=["\']reviewed-by-sign["\'][^>]*>).*?(<\/span>)/is',
                        '$1' . $reviewerSign . '$2',
                        $content,
                        1
                    );
                    log_message('info', 'replaceApprovalInfo: Successfully replaced reviewer signature');
                }
            } else {
                log_message('warning', 'replaceApprovalInfo: Reviewer not found for ID: ' . $document['reviewer_id']);
            }
        } else {
            log_message('info', 'replaceApprovalInfo: No reviewer_id set, skipping Reviewed By column');
        }

        // Handle "Approved By" (Approver) information - uses approved-by-* IDs
        if (!empty($document['approver_id'])) {
            log_message('info', 'replaceApprovalInfo: Processing approver ID: ' . $document['approver_id']);
            $approver = $userModel->find($document['approver_id']);

            if ($approver) {
                $approverName = esc($approver['name'] ?? $approver['username'] ?? '');
                $approverSign = esc($approver['sign'] ?? '');

                log_message('info', 'replaceApprovalInfo: Approver found - Name: ' . $approverName . ', Sign: ' . ($approverSign ? 'present' : 'empty'));

                // Get approver's designation from users table
                $approverRole = esc($approver['designation'] ?? '');
                log_message('info', 'replaceApprovalInfo: Approver designation: ' . $approverRole);

                // Replace Name in Approved By column
                if (preg_match('/<span[^>]*id=["\']approved-by-name["\'][^>]*>.*?<\/span>/is', $content)) {
                    log_message('info', 'replaceApprovalInfo: Found existing approved-by-name span, updating content');
                    $content = preg_replace(
                        '/(<span[^>]*id=["\']approved-by-name["\'][^>]*>).*?(<\/span>)/is',
                        '$1' . $approverName . '$2',
                        $content,
                        1
                    );
                    log_message('info', 'replaceApprovalInfo: Successfully replaced approver name');
                }

                // Replace Designation in Approved By column
                if (preg_match('/<span[^>]*id=["\']approved-by-designation["\'][^>]*>.*?<\/span>/is', $content)) {
                    log_message('info', 'replaceApprovalInfo: Found existing approved-by-designation span, updating content');
                    $content = preg_replace(
                        '/(<span[^>]*id=["\']approved-by-designation["\'][^>]*>).*?(<\/span>)/is',
                        '$1' . $approverRole . '$2',
                        $content,
                        1
                    );
                    log_message('info', 'replaceApprovalInfo: Successfully replaced approver designation');
                }

                // Replace Signature in Approved By column
                if (preg_match('/<span[^>]*id=["\']approved-by-sign["\'][^>]*>.*?<\/span>/is', $content)) {
                    log_message('info', 'replaceApprovalInfo: Found existing approved-by-sign span, updating content');
                    $content = preg_replace(
                        '/(<span[^>]*id=["\']approved-by-sign["\'][^>]*>).*?(<\/span>)/is',
                        '$1' . $approverSign . '$2',
                        $content,
                        1
                    );
                    log_message('info', 'replaceApprovalInfo: Successfully replaced approver signature');
                }
            } else {
                log_message('warning', 'replaceApprovalInfo: Approver not found for ID: ' . $document['approver_id']);
            }
        } else {
            log_message('info', 'replaceApprovalInfo: No approver_id set, skipping Approved By column');
        }

        log_message('info', 'replaceApprovalInfo: Completed processing for document ID: ' . ($document['id'] ?? 'unknown'));
        return $content;
    }

    public function store()
    {
        if (!userHasPermission('document_create')) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to create documents');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|min_length[3]',
            'type_id' => 'required|is_not_unique[document_types.id]',
            'department_id' => 'required|is_not_unique[departments.id]',
            'content' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Get form data if exists
        $formData = $this->request->getPost('form_data');
        $formDataJson = $formData ? json_encode($formData) : null;

        // Extract document number from content
        $content = $this->request->getPost('content');
        $documentNumber = $this->extractDocumentNumber($content);

        // If document number not found in content, generate it
        if (empty($documentNumber)) {
            $typeId = $this->request->getPost('type_id');
            $documentNumber = $this->generateNextDocumentNumber($typeId);
        }

        $documentId = $this->documentModel->insert([
            'title' => $this->request->getPost('title'),
            'document_number' => $documentNumber,
            'content' => $content,
            'type_id' => $this->request->getPost('type_id'),
            'department_id' => $this->request->getPost('department_id'),
            'status' => $this->request->getPost('status') ?? 'draft',
            'approval_status' => 'pending',
            'effective_date' => $this->request->getPost('effective_date') !== '' ? $this->request->getPost('effective_date') : null,
            'review_date' => $this->request->getPost('review_date') !== '' ? $this->request->getPost('review_date') : null,
            'form_data' => $formDataJson,
            'created_by' => session()->get('user_id')
        ]);

        if ($documentId) {
            // Handle document metadata if provided
            $metadata = $this->processMetadataFromPost();
            if (!empty($metadata)) {
                $this->documentModel->setDocumentMetadata($documentId, $metadata);
            }

            // Handle attachment uploads
            $files = $this->request->getFileMultiple('attachments');
            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file && $file->isValid() && !$file->hasMoved()) {
                        $uploadResult = uploadDocumentAttachment($file, $documentId);
                        if ($uploadResult) {
                            $this->attachmentModel->addAttachment(
                                $documentId,
                                $uploadResult['name'],
                                $uploadResult['path'],
                                $uploadResult['size'],
                                $uploadResult['type'],
                                session()->get('user_id')
                            );
                        }
                    }
                }
            }

            $this->logModel->logActivity(session()->get('user_id'), 'Created document', 'Created document: ' . $this->request->getPost('title') . ' (' . $documentNumber . ')');
            return redirect()->to('/documents')->with('success', 'Document created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create document');
    }

    private function extractDocumentNumber($content)
    {
        // Extract document number from content using various patterns
        // Updated to support format: SSP/MR/001/001
        $patterns = [
            '/Doc\.\s*No\.?\s*([A-Z\/]+\/\d+\/\d+)/i',
            '/Document\s*No\.?\s*([A-Z\/]+\/\d+\/\d+)/i',
            '/Doc\s*No:?\s*([A-Z\/]+\/\d+\/\d+)/i',
            '/Document\s*Number:?\s*([A-Z\/]+\/\d+\/\d+)/i',
            // Fallback to old format if new format not found
            '/Doc\.\s*No\.?\s*([A-Z\/]+\/\d+)/i',
            '/Document\s*No\.?\s*([A-Z\/]+\/\d+)/i',
            '/Doc\s*No:?\s*([A-Z\/]+\/\d+)/i',
            '/Document\s*Number:?\s*([A-Z\/]+\/\d+)/i'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Fix image paths in content - convert relative paths to absolute URLs
     */
    private function fixImagePaths($content)
    {
        // Pattern to match img tags with src attributes
        $content = preg_replace_callback(
            '/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i',
            function ($matches) {
                $beforeSrc = $matches[1];
                $src = $matches[2];
                $afterSrc = $matches[3];

                // Check if it's already an absolute URL (starts with http:// or https://)
                if (preg_match('/^https?:\/\//i', $src)) {
                    return $matches[0]; // Already absolute, return as is
                }

                // Check if it's a relative path to uploads folder
                if (preg_match('/^\.\.\/+uploads\/documents\/images\/(.+)$/i', $src, $pathMatches)) {
                    // Convert to absolute URL
                    $filename = $pathMatches[1];
                    $absoluteUrl = base_url('uploads/documents/images/' . $filename);
                    return '<img' . $beforeSrc . 'src="' . $absoluteUrl . '"' . $afterSrc . '>';
                }

                // Check if it's already a proper relative path (starts with uploads/)
                if (preg_match('/^uploads\/documents\/images\/(.+)$/i', $src)) {
                    // Convert to absolute URL
                    $absoluteUrl = base_url($src);
                    return '<img' . $beforeSrc . 'src="' . $absoluteUrl . '"' . $afterSrc . '>';
                }

                // Return original if no match
                return $matches[0];
            },
            $content
        );

        return $content;
    }

    public function preview($id = null)
    {
        // If ID is provided, load existing document
        if ($id) {
            $document = $this->documentModel->find($id);
            if (!$document) {
                return redirect()->to('/documents')->with('error', 'Document not found');
            }

            $userId = session()->get('user_id');
            if (!$this->canAccessDocument($document, $userId)) {
                return redirect()->to('/documents')->with('error', 'Access denied');
            }

            $formData = $document['form_data'] ? json_decode($document['form_data'], true) : [];

            // Fix image paths in content
            if (!empty($document['content'])) {
                $document['content'] = $this->fixImagePaths($document['content']);
            }

            // Get the original creator's information for "Prepared By"
            $userModel = new \App\Models\User();
            $creator = $userModel->find($document['created_by']);

            // Replace "Prepared By" information in document content
            if ($creator && !empty($document['content'])) {
                $document['content'] = $this->replacePreparedByInfo($document['content'], $creator);
            }

            // Replace "Checked By" and "Approved By" information
            if (!empty($document['content'])) {
                $document['content'] = $this->replaceApprovalInfo($document['content'], $document);
            }
        } else {
            // Preview from form submission
            $formData = $this->request->getPost('form_data') ?? [];
            $document = [
                'title' => $this->request->getPost('title'),
                'type_id' => $this->request->getPost('type_id'),
                'department_id' => $this->request->getPost('department_id'),
                'status' => $this->request->getPost('status') ?? 'draft',
                'effective_date' => $this->request->getPost('effective_date'),
                'review_date' => $this->request->getPost('review_date'),
                'content' => $this->request->getPost('content'),
                'form_data' => json_encode($formData)
            ];

            // Fix image paths in content
            if (!empty($document['content'])) {
                $document['content'] = $this->fixImagePaths($document['content']);
            }

            // Get current user's information for "Prepared By" (new document preview)
            $userId = session()->get('user_id');
            $userModel = new \App\Models\User();
            $currentUser = $userModel->find($userId);

            // Replace "Prepared By" information in document content
            if ($currentUser && !empty($document['content'])) {
                $document['content'] = $this->replacePreparedByInfo($document['content'], $currentUser);
            }
        }

        // Get template and fields
        $templateModel = new \App\Models\DocumentTemplate();
        $fieldModel = new \App\Models\TemplateField();

        $template = $templateModel->getTemplateByTypeId($document['type_id']);
        $fields = $template ? $fieldModel->getFieldsBySection($template['id']) : [];

        // Get document type and department
        $documentType = $this->documentTypeModel->find($document['type_id']);
        $department = $this->departmentModel->find($document['department_id']);

        $data = [
            'document' => $document,
            'formData' => $formData,
            'template' => $template,
            'fields' => $fields,
            'documentType' => $documentType,
            'department' => $department,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole()
        ];

        // record preview activity (new vs existing document)
        $this->logModel->logActivity(session()->get('user_id'), 'Previewed document', 'Document ID: ' . ($id ?? 'new'));

        return view('documents/preview', $data);
    }

    public function edit($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        $userId = session()->get('user_id');
        if (!$this->canEditDocument($document, $userId)) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to edit this document');
        }

        // Get the original creator's information for "Prepared By"
        $userModel = new \App\Models\User();
        $creator = $userModel->find($document['created_by']);

        // Replace "Prepared By" information in document content
        if ($creator && !empty($document['content'])) {
            $document['content'] = $this->replacePreparedByInfo($document['content'], $creator);
        }

        // Replace "Checked By" and "Approved By" information
        if (!empty($document['content'])) {
            $document['content'] = $this->replaceApprovalInfo($document['content'], $document);
        }

        // Fix image paths to use absolute URLs
        if (!empty($document['content'])) {
            $document['content'] = $this->fixImagePaths($document['content']);
        }

        $documentTypes = $this->documentTypeModel->findAll();
        $departments = $this->departmentModel->findAll();

        $data = [
            'document' => $document,
            'documentTypes' => $documentTypes,
            'departments' => $departments,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole()
        ];

        // log entry to edit page
        $this->logModel->logActivity($userId, 'Opened document edit form', 'Document ID: ' . $id);

        return view('documents/edit', $data);
    }

    public function update($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        $userId = session()->get('user_id');
        if (!$this->canEditDocument($document, $userId)) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to edit this document');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'title' => 'required|min_length[3]',
            'type_id' => 'required|is_not_unique[document_types.id]',
            'department_id' => 'required|is_not_unique[departments.id]',
            'content' => 'permit_empty'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $updateData = [
            'title' => $this->request->getPost('title'),
            'content' => $this->request->getPost('content'),
            'type_id' => $this->request->getPost('type_id'),
            'department_id' => $this->request->getPost('department_id'),
            'effective_date' => $this->request->getPost('effective_date') !== '' ? $this->request->getPost('effective_date') : null,
            'review_date' => $this->request->getPost('review_date') !== '' ? $this->request->getPost('review_date') : null
        ];

        // Only allow status change if user has permission
        if (userHasPermission('document_approve')) {
            $updateData['status'] = $this->request->getPost('status');
        }

        $this->documentModel->update($id, $updateData);

        // Handle attachment uploads
        $files = $this->request->getFileMultiple('attachments');
        if (!empty($files)) {
            foreach ($files as $file) {
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $uploadResult = uploadDocumentAttachment($file, $id);
                    if ($uploadResult) {
                        $this->attachmentModel->addAttachment(
                            $id,
                            $uploadResult['name'],
                            $uploadResult['path'],
                            $uploadResult['size'],
                            $uploadResult['type'],
                            session()->get('user_id')
                        );
                    }
                }
            }
        }

        // Handle attachment deletion
        $deleteAttachmentIds = $this->request->getPost('delete_attachments');
        if (!empty($deleteAttachmentIds)) {
            $deleteIds = is_array($deleteAttachmentIds) ? $deleteAttachmentIds : [$deleteAttachmentIds];
            foreach ($deleteIds as $attachmentId) {
                $attachment = $this->attachmentModel->find($attachmentId);
                if ($attachment && $attachment['document_id'] == $id) {
                    deleteDocumentAttachmentFile($attachment['file_path']);
                    $this->attachmentModel->deleteAttachment($attachmentId);
                }
            }
        }

        $this->logModel->logActivity($userId, 'Updated document', 'Updated document: ' . $this->request->getPost('title'));
        return redirect()->to('/documents')->with('success', 'Document updated successfully');
    }

    public function delete($id)
    {
        $document = $this->documentModel->find($id);

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        $userId = session()->get('user_id');
        if (!$this->canDeleteDocument($document, $userId)) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to delete this document');
        }

        $this->documentModel->delete($id);
        $this->logModel->logActivity($userId, 'Deleted document', 'Deleted document: ' . $document['title']);

        return redirect()->to('/documents')->with('success', 'Document deleted successfully');
    }

    // Document Approval System Methods

    public function submitForReview($id)
    {
        // Only document creators, Lab Managers, and Admins can submit for review
        if (!$this->isSuperAdmin() && !$this->isAdmin() && !$this->isLabManager()) {
            $document = $this->documentModel->find($id);
            if (!$document || $document['created_by'] != session()->get('user_id')) {
                return redirect()->to('/documents')->with('error', 'You do not have permission to submit this document for review.');
            }
        }

        $document = $this->documentModel->getDocumentById($id);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        if ($document['approval_status'] !== 'pending') {
            return redirect()->to('/documents')->with('error', 'Document is not in pending status');
        }

        // Get available reviewers from the same department
        $reviewers = $this->documentModel->getReviewersByDepartment($document['department_id']);

        $data = [
            'document' => $document,
            'reviewers' => $reviewers,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole()
        ];

        // log that user is preparing to submit for review
        $this->logModel->logActivity(session()->get('user_id'), 'Opened submit for review form', 'Document ID: ' . $id);

        return view('documents/submit_for_review', $data);
    }

    public function processSubmitForReview($id)
    {
        $reviewerId = $this->request->getPost('reviewer_id');

        if (empty($reviewerId)) {
            return redirect()->back()->with('error', 'Please select a reviewer');
        }

        $result = $this->documentModel->submitForReview($id, $reviewerId, session()->get('user_id'));

        if ($result) {
            $this->logModel->logActivity(session()->get('user_id'), 'Submitted document for review', 'Document ID: ' . $id);
            return redirect()->to('/documents')->with('success', 'Document submitted for review successfully');
        }

        return redirect()->back()->with('error', 'Failed to submit document for review');
    }

    public function reviewDocument($id)
    {
        // Only assigned reviewers, Lab Managers, and Admins can review
        $document = $this->documentModel->getDocumentById($id);

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        $userId = session()->get('user_id');

        if (!$this->isSuperAdmin() && !$this->isAdmin() && !$this->isLabManager()) {
            if (!$this->isReviewer() || $document['reviewer_id'] != $userId) {
                return redirect()->to('/documents')->with('error', 'You are not assigned to review this document');
            }
        }

        if ($document['approval_status'] !== 'sent_for_review') {
            return redirect()->to('/documents')->with('error', 'Document is not under review');
        }

        $approvalHistory = $this->documentModel->getApprovalHistory($id);

        $data = [
            'document' => $document,
            'approval_history' => $approvalHistory,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole()
        ];

        // record that reviewer opened the review page
        $this->logModel->logActivity($userId, 'Opened review page', 'Document ID: ' . $id);

        return view('documents/review', $data);
    }

    public function processReview($id)
    {
        $action = $this->request->getPost('action');
        $comments = $this->request->getPost('comments');

        if (empty($action) || empty($comments)) {
            return redirect()->back()->with('error', 'Please provide action and comments');
        }

        $allowedActions = ['approve_for_final', 'reject', 'return_to_creator', 'return_for_revision'];
        if (!in_array($action, $allowedActions)) {
            return redirect()->back()->with('error', 'Invalid action');
        }

        $result = $this->documentModel->reviewDocument($id, $action, $comments, session()->get('user_id'));

        if ($result) {
            $actionText = [
                'approve_for_final' => 'approved',
                'reject' => 'rejected',
                'return_to_creator' => 'returned to creator',
                'return_for_revision' => 'returned for revision'
            ];

            $this->logModel->logActivity(session()->get('user_id'), 'Reviewed document', 'Document ID: ' . $id . ' - ' . $actionText[$action]);
            return redirect()->to('/approval-dashboard')->with('success', 'Document ' . ($actionText[$action] ?? 'processed') . ' successfully');
        }

        return redirect()->back()->with('error', 'Failed to process review');
    }

    public function approveDocument($id)
    {
        // Only assigned approvers, Admins, and superadmins can approve
        $document = $this->documentModel->getDocumentById($id);

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        $userId = session()->get('user_id');

        if (!$this->isSuperAdmin() && !$this->isAdmin() && !$this->isLabManager()) {
            if (!$this->isApprover()) {
                return redirect()->to('/documents')->with('error', 'Only approvers can access approval');
            }
            if (!empty($document['approver_id']) && $document['approver_id'] != $userId) {
                return redirect()->to('/documents')->with('error', 'You are not assigned to approve this document');
            }
        }

        // Admins have final approval authority and can approve directly
        if ($this->isAdmin() || $this->isSuperAdmin()) {
            $approvalHistory = $this->documentModel->getApprovalHistory($id);
            $reviewers = $this->documentModel->getReviewersByDepartment($document['department_id']);
            $approvers = $this->documentModel->getApproversByDepartment($document['department_id']);

            $data = [
                'document' => $document,
                'approval_history' => $approvalHistory,
                'reviewers' => $reviewers,
                'approvers' => $approvers,
                'username' => session()->get('username'),
                'role_name' => $this->getUserRole()
            ];

            // log that approver/admin opened approval form
            $this->logModel->logActivity($userId, 'Opened approval page', 'Document ID: ' . $id);

            return view('documents/approve', $data);
        }

        return redirect()->to('/documents')->with('error', 'Document approval not available');
    }

    public function processApproval($id)
    {
        $action = $this->request->getPost('action');
        $comments = $this->request->getPost('comments');
        $targetUserId = $this->request->getPost('target_user_id');

        if (empty($action)) {
            return redirect()->back()->with('error', 'Please select an action');
        }

        $userId = session()->get('user_id');
        $isAdmin = $this->isAdmin() || $this->isSuperAdmin();

        if ($isAdmin) {
            // Admin final approval logic
            $result = $this->documentModel->adminApprove($id, $action, $comments, $userId, $targetUserId);
        } else {
            // Approver intermediate approval logic
            $result = $this->documentModel->approveDocument($id, $action, $comments, $userId, $targetUserId);
        }

        if ($result) {
            $this->logModel->logActivity($userId, 'Processed approval/return', 'Document ID: ' . $id . ' Action: ' . $action);
            return redirect()->to('/approval-dashboard')->with('success', 'Action processed successfully');
        }

        return redirect()->back()->with('error', 'Failed to process approval action');
    }

    public function myReviews()
    {
        // Show documents assigned to current user for review
        if (!$this->isReviewer() && !$this->isLabManager() && !$this->isAdmin() && !$this->isSuperAdmin()) {
            return redirect()->to('/documents')->with('error', 'You do not have review permissions');
        }

        $userId = session()->get('user_id');
        $documents = $this->documentModel->getDocumentsForReview($userId);

        $data = [
            'documents' => $documents,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole(),
            'page_title' => 'My Reviews'
        ];

        $this->logModel->logActivity($userId, 'Accessed my reviews list');

        return view('documents/my_reviews', $data);
    }

    public function myApprovals()
    {
        // Show documents assigned to current user for approval
        if (!$this->isApprover() && !$this->isAdmin() && !$this->isSuperAdmin()) {
            return redirect()->to('/documents')->with('error', 'You do not have approval permissions');
        }

        $userId = session()->get('user_id');
        $documents = $this->documentModel->getDocumentsForApproval($userId);

        $data = [
            'documents' => $documents,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole(),
            'page_title' => 'My Approvals'
        ];

        $this->logModel->logActivity($userId, 'Accessed my approvals list');

        return view('documents/my_reviews', $data); // Reuse the same view
    }

    public function approvalDashboard()
    {
        $roleId       = session()->get('role_id');
        $departmentId = session()->get('department_id');

        // Base query: get documents by status
        // Apply department filter for non-admin/superadmin
        $pendingDocuments = $this->documentModel->getDocumentsByApprovalStatus('pending', $roleId, $departmentId);
        $sentForReviewDocuments = $this->documentModel->getDocumentsByApprovalStatus('sent_for_review', $roleId, $departmentId);
        $sentForApprovalDocuments = $this->documentModel->getDocumentsByApprovalStatus('sent_for_approval', $roleId, $departmentId);
        $approvedByApproverDocuments = $this->documentModel->getDocumentsByApprovalStatus('approved_by_approver', $roleId, $departmentId);
        $adminApprovedDocuments = $this->documentModel->getDocumentsByApprovalStatus('admin_approved', $roleId, $departmentId);
        $returnedForRevisionDocuments = $this->documentModel->getDocumentsByApprovalStatus('returned_for_revision', $roleId, $departmentId);
        $rejectedDocuments = $this->documentModel->getDocumentsByApprovalStatus('rejected', $roleId, $departmentId);

        // prepare lists of reviewers/approvers per document
        $reviewerLists = [];
        $approverLists = [];
        foreach (array_merge($sentForApprovalDocuments, $returnedForRevisionDocuments) as $doc) {
            $deptId = $doc['department_id'] ?? null;
            if ($deptId) {
                if (!isset($reviewerLists[$doc['id']])) {
                    $reviewerLists[$doc['id']] = $this->documentModel->getReviewersByDepartment($deptId);
                }
                if (!isset($approverLists[$doc['id']])) {
                    $approverLists[$doc['id']] = $this->documentModel->getApproversByDepartment($deptId);
                }
            }
        }

        $data = [
            'pending_documents' => $pendingDocuments,
            'sent_for_review_documents' => $sentForReviewDocuments,
            'sent_for_approval_documents' => $sentForApprovalDocuments,
            'approved_by_approver_documents' => $approvedByApproverDocuments,
            'admin_approved_documents' => $adminApprovedDocuments,
            'returned_for_revision_documents' => $returnedForRevisionDocuments,
            'rejected_documents' => $rejectedDocuments,
            'reviewer_lists' => $reviewerLists,
            'approver_lists' => $approverLists,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole(),
            'page_title' => 'Approval Dashboard',
            'notifications' => $this->notificationModel->getUnread(session()->get('user_id'))
        ];
        // track dashboard access
        $this->logModel->logActivity(session()->get('user_id'), 'Accessed approval dashboard');

        return view('documents/approval_dashboard', $data);
    }

    public function approvalHistory($id)
    {
        $document = $this->documentModel->getDocumentById($id);

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        // Check if user can view this document
        if (!$this->isSuperAdmin() && !$this->isAdmin() && !$this->isAuditor()) {
            $userId = session()->get('user_id');
            $userDepartmentId = $this->getUserDepartmentId($userId);

            if ($document['department_id'] != $userDepartmentId) {
                return redirect()->to('/documents')->with('error', 'You do not have permission to view this document');
            }
        }

        $approvalHistory = $this->documentModel->getApprovalHistory($id);

        $data = [
            'document' => $document,
            'approval_history' => $approvalHistory,
            'username' => session()->get('username'),
            'role_name' => $this->getUserRole()
        ];

        $this->logModel->logActivity(session()->get('user_id'), 'Viewed approval history', 'Document ID: ' . $id);

        return view('documents/approval_history', $data);
    }

    public function lockDocument($id)
    {
        // Only Admins can lock obsolete documents
        if (!$this->isAdmin() && !$this->isSuperAdmin()) {
            return redirect()->to('/documents')->with('error', 'You do not have permission to lock documents');
        }

        $document = $this->documentModel->find($id);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        $result = $this->documentModel->update($id, [
            'status' => 'archived',
            'approval_status' => 'approved' // Keep approval status but archive the document
        ]);

        if ($result) {
            $this->logModel->logActivity(session()->get('user_id'), 'Locked obsolete document', 'Document ID: ' . $id);
            return redirect()->to('/documents')->with('success', 'Document locked successfully');
        }

        return redirect()->back()->with('error', 'Failed to lock document');
    }

    // Quick Action Methods for Approval Dashboard
    public function quickReview($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/approval-dashboard');
        }

        $document = $this->documentModel->find($id);
        if (!$document) {
            return $this->response->setJSON(['success' => false, 'message' => 'Document not found']);
        }

        // Check if document is in the correct status for review
        if ($document['approval_status'] !== 'sent_for_review') {
            return $this->response->setJSON(['success' => false, 'message' => 'Document is not under review']);
        }

        $userId = session()->get('user_id');

        // STRICT PERMISSION CHECK: Only the assigned reviewer can review
        // Admins and superadmins cannot review on behalf of reviewers
        if (!$this->isReviewer($userId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only assigned reviewers can review documents. You do not have reviewer role.']);
        }

        // Check if this user is the assigned reviewer for this document
        if ($document['reviewer_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'You are not the assigned reviewer for this document. Only the assigned reviewer can perform this action.']);
        }

        $action = $this->request->getPost('action');
        $comments = $this->request->getPost('comments') ?? '';

        if (!in_array($action, ['approve_for_final', 'reject', 'return_for_revision'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid action']);
        }

        if ($action === 'reject' && empty($comments)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Comments are required for rejection']);
        }

        $result = $this->documentModel->reviewDocument($id, $action, $comments, $userId);

        if ($result) {
            $actionText = [
                'approve_for_final' => 'approved',
                'reject' => 'rejected',
                'return_for_revision' => 'returned for revision'
            ];

            $this->logModel->logActivity($userId, 'Reviewed document', 'Document ID: ' . $id . ' - ' . $actionText[$action]);
            return $this->response->setJSON(['success' => true, 'message' => 'Document ' . $actionText[$action] . ' successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to process review']);
    }

    public function quickApprove($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/approval-dashboard');
        }

        $document = $this->documentModel->find($id);
        if (!$document) {
            return $this->response->setJSON(['success' => false, 'message' => 'Document not found']);
        }

        $userId = session()->get('user_id');
        $action = $this->request->getPost('action');
        $comments = $this->request->getPost('comments') ?? '';

        if (!in_array($action, ['approve', 'reject', 'return_to_creator', 'return_to_reviewer'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid action']);
        }

        if ($action === 'reject' && empty($comments)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Comments are required for rejection']);
        }

        // Handle Approver approval (sent_for_approval → approved_by_approver) or returns
        if ($document['approval_status'] === 'sent_for_approval') {
            // Only assigned approver (or lab_manager/superadmin) may act.  If no approver assigned, any approver may.
            if (!$this->isSuperAdmin($userId) && !$this->isAdmin($userId) && !$this->isLabManager($userId)) {
                if (!$this->isApprover($userId)) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Only approvers can approve documents']);
                }
                if (!empty($document['approver_id']) && $document['approver_id'] != $userId) {
                    return $this->response->setJSON(['success' => false, 'message' => 'You are not assigned to approve this document']);
                }
            }

            if ($action === 'approve') {
                $result = $this->documentModel->approveDocument($id, 'approve', $comments, $userId);
                $message = 'Document approved successfully';
                $logMessage = 'Approved document';
            } elseif ($action === 'reject') {
                $result = $this->documentModel->rejectDocument($id, $comments, $userId);
                $message = 'Document rejected successfully';
                $logMessage = 'Rejected document';
            } elseif (in_array($action, ['return_to_creator', 'return_to_reviewer'])) {
                $result = $this->documentModel->approveDocument($id, $action, $comments, $userId, $this->request->getPost('target_user_id'));
                $message = 'Document returned for revision';
                $logMessage = 'Returned document';
            }
        }
        // Handle Admin final approval (approved_by_approver → admin_approved)
        elseif ($document['approval_status'] === 'approved_by_approver') {
            // Check if user is admin or superadmin
            if (!$this->isAdmin($userId) && !$this->isSuperAdmin($userId)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Only admins can give final approval. You do not have admin role.']);
            }

            if ($action === 'approve') {
                // Use adminApprove to ensure status transition is logged
                $result = $this->documentModel->adminApprove($id, 'final_approve', $comments, $userId);
                $message = 'Document approved successfully';
                $logMessage = 'Admin approved document';
            } else {
                $result = $this->documentModel->rejectDocument($id, $comments, $userId);
                $message = 'Document rejected successfully';
                $logMessage = 'Admin rejected document';
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Document is not in a valid status for approval']);
        }

        if ($result) {
            $this->logModel->logActivity($userId, $logMessage, 'Document ID: ' . $id);
            return $this->response->setJSON(['success' => true, 'message' => $message]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to process approval']);
    }

    public function assignReviewer($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/approval-dashboard');
        }

        $document = $this->documentModel->find($id);
        if (!$document) {
            return $this->response->setJSON(['success' => false, 'message' => 'Document not found']);
        }

        // Only admins and superadmins can assign reviewers
        if (!$this->isSuperAdmin() && !$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'You are not authorized to assign reviewers']);
        }

        $reviewerId = $this->request->getPost('reviewer_id');

        if (empty($reviewerId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please select a reviewer']);
        }

        $result = $this->documentModel->submitForReview($id, $reviewerId, session()->get('user_id'));

        if ($result) {
            $this->logModel->logActivity(session()->get('user_id'), 'Assigned reviewer to document', 'Document ID: ' . $id);
            return $this->response->setJSON(['success' => true, 'message' => 'Reviewer assigned and document submitted for review']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to assign reviewer']);
    }

    // API endpoint to get reviewers by department
    public function getReviewersByDepartment($departmentId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        // Only admins and superadmins can access this
        if (!$this->isSuperAdmin() && !$this->isAdmin()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $reviewers = $this->documentModel->getReviewersByDepartment($departmentId);

        return $this->response->setJSON([
            'success' => true,
            'reviewers' => $reviewers
        ]);
    }

    // Method to resubmit a document after revision
    public function resubmitAfterRevision($id)
    {
        $document = $this->documentModel->find($id);
        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        // Check if document is in revision status
        if ($document['approval_status'] !== 'returned_for_revision') {
            return redirect()->to('/documents')->with('error', 'Document is not in revision status');
        }

        $userId = session()->get('user_id');

        // Check permissions - only document creator, admins, or superadmins can resubmit
        if (!$this->isSuperAdmin() && !$this->isAdmin() && $document['created_by'] != $userId) {
            return redirect()->to('/documents')->with('error', 'You are not authorized to resubmit this document');
        }

        // Reset the document to pending status for resubmission using model method
        $result = $this->documentModel->resubmitDocument($id, $userId);

        if ($result) {
            $this->logModel->logActivity($userId, 'Resubmitted document after revision', 'Document ID: ' . $id);

            return redirect()->to('/approval-dashboard')->with('success', 'Document resubmitted successfully and is now pending review');
        }

        return redirect()->back()->with('error', 'Failed to resubmit document');
    }

    // Helper methods
    private function processMetadataFromPost()
    {
        $metadata = [];
        // Process any metadata from the form
        // This is a placeholder for metadata processing
        return $metadata;
    }

    private function canAccessDocument($document, $userId)
    {
        // superadmin can access all documents
        if ($this->isSuperAdmin($userId)) {
            return true;
        }

        // Check if user is in the same department
        $userDepartmentId = $this->getUserDepartmentId($userId);
        return $document['department_id'] == $userDepartmentId;
    }

    private function canEditDocument($document, $userId)
    {
        // superadmin can edit all documents
        if ($this->isSuperAdmin($userId)) {
            return true;
        }

        // Document creator can edit their own documents if not approved
        if ($document['created_by'] == $userId && $document['approval_status'] !== 'approved') {
            return true;
        }

        // Admin can edit documents in their scope
        if ($this->isAdmin($userId)) {
            return true;
        }

        return false;
    }

    private function canDeleteDocument($document, $userId)
    {
        // superadmin can delete all documents
        if ($this->isSuperAdmin($userId)) {
            return true;
        }

        // Document creator can delete their own draft documents
        if ($document['created_by'] == $userId && $document['status'] == 'draft') {
            return true;
        }

        return false;
    }

    private function getUserRoleId($userId)
    {
        $result = $this->db->table('users')
            ->select('role_id')
            ->where('id', $userId)
            ->get()
            ->getRow();

        return $result ? $result->role_id : null;
    }

    private function getUserDepartmentId($userId)
    {
        $result = $this->db->table('users')
            ->select('department_id')
            ->where('id', $userId)
            ->get()
            ->getRow();

        return $result ? $result->department_id : null;
    }

    public function uploadImage()
    {
        // Auth filter already handles authentication, no need for manual check

        $file = $this->request->getFile('upload');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'error' => 'No file uploaded or invalid file'
            ])->setStatusCode(400);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON([
                'error' => 'Invalid file type. Only images are allowed.'
            ])->setStatusCode(400);
        }

        // Validate file size (max 5MB)
        if ($file->getSize() > 5242880) {
            return $this->response->setJSON([
                'error' => 'File size exceeds 5MB limit'
            ])->setStatusCode(400);
        }

        try {
            // Create uploads directory in public folder if it doesn't exist
            $uploadPath = FCPATH . 'uploads/documents/images/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $newName = $file->getRandomName();

            // Move file
            if ($file->move($uploadPath, $newName)) {
                // Return URL for TinyMCE (relative to public folder)
                $url = base_url('uploads/documents/images/' . $newName);

                return $this->response->setJSON([
                    'location' => $url
                ]);
            } else {
                return $this->response->setJSON([
                    'error' => 'Failed to upload file'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => 'Upload error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function exportPdf($id)
    {
        $document = $this->db->table('documents')->where('id', $id)->get()->getRowArray();
        if (!$document)
            return redirect()->to('/documents')->with('error', 'Document not found');

        // Get the original creator's information for "Prepared By"
        $userModel = new \App\Models\User();
        $creator = $userModel->find($document['created_by']);

        // Replace "Prepared By" information in document content
        if ($creator && !empty($document['content'])) {
            $document['content'] = $this->replacePreparedByInfo($document['content'], $creator);
        }

        // Replace "Checked By" and "Approved By" information
        if (!empty($document['content'])) {
            $document['content'] = $this->replaceApprovalInfo($document['content'], $document);
        }

        if (ob_get_length())
            ob_end_clean();

        // Route to template-specific formatting function
        $templateId = $document['type_id'] ?? null;
        // echo $templateId;die;
        if ($templateId == 1) {
            // echo '*';die;
            return $this->exportPdfTemplate1($document);
        } elseif ($templateId == 2) {
            // echo '**2';die;
            return $this->exportPdfTemplate2($document);
        } elseif ($templateId == 3) {
            return $this->exportPdfTemplate3($document);
        } elseif ($templateId == 4) {
            return $this->exportPdfTemplate4($document);
        } elseif ($templateId == 5) {
            return $this->exportPdfTemplate5($document);
        } else {
            // Default formatting for unknown templates
            echo '****Default';
            // die;
            return $this->exportPdfDefault($document);
        }
    }

    /**
     * Common PDF setup - shared initialization for all templates
     */
    private function setupPdfCommon(&$pdf, $fontName)
    {
        // Configuration
        $pdf->SetCreator('DMS');
        $pdf->SetMargins(15, 35, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);
        $pdf->setCellHeightRatio(1.0);
        $pdf->setCellPaddings(0, 0, 0, 0);
        $pdf->setCellMargins(0, 0, 0, 0);
        $pdf->SetFont($fontName, '', 11);
    }

    /**
     * Initialize Cambria font and return font name
     */
    private function initializeCambriaFont()
    {
        $cambriaTTFPath = FCPATH . 'assets/fonts/cambria.ttf';
        $cambriaBTTFPath = FCPATH . 'assets/fonts/cambriab.ttf';
        $cambriaITTFPath = FCPATH . 'assets/fonts/cambriai.ttf';
        $cambriaZTTFPath = FCPATH . 'assets/fonts/cambriaz.ttf';
        $tcpdfFontsDir = FCPATH . '../vendor/tecnickcom/tcpdf/fonts/';

        $fontName = 'cambria';

        if (file_exists($tcpdfFontsDir . 'cambria.php')) {
            $fontName = 'cambria';
            error_log('Using existing Cambria Regular font definition');

            if (!file_exists($tcpdfFontsDir . 'cambriai.php') && file_exists($cambriaITTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont($cambriaITTFPath, 'TrueTypeUnicode', '', 32, $tcpdfFontsDir);
                    error_log('Cambria Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria italic font error: ' . $e->getMessage());
                }
            }

            if (!file_exists($tcpdfFontsDir . 'cambriab.php') && file_exists($cambriaBTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont($cambriaBTTFPath, 'TrueTypeUnicode', '', 32, $tcpdfFontsDir);
                    error_log('Cambria Bold font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold font error: ' . $e->getMessage());
                }
            }

            if (!file_exists($tcpdfFontsDir . 'cambriaz.php') && file_exists($cambriaZTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont($cambriaZTTFPath, 'TrueTypeUnicode', '', 32, $tcpdfFontsDir);
                    error_log('Cambria Bold Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold italic font error: ' . $e->getMessage());
                }
            }
        } elseif (file_exists($cambriaTTFPath)) {
            try {
                $addedFontName = \TCPDF_FONTS::addTTFfont($cambriaTTFPath, 'TrueTypeUnicode', '', 32, $tcpdfFontsDir);
                if ($addedFontName && $addedFontName !== false) {
                    $fontName = $addedFontName;
                    error_log('Cambria Regular font successfully added as: ' . $fontName);
                } else {
                    error_log('Failed to add Cambria Regular font - TCPDF returned false');
                }
            } catch (\Throwable $e) {
                error_log('Cambria Regular font error: ' . $e->getMessage());
            }
        }

        return $fontName;
    }

    /**
     * Common body content processing - shared formatting for all templates
     */
    private function processBodyContent($bodyContent)
    {
        // Remove ALL header tables from the body content
        $bodyContent = preg_replace('/<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>/si', '', $bodyContent);

        // Remove empty divs with only non-breaking spaces
        $bodyContent = preg_replace('/<div[^>]*style="[^"]*margin-left:\s*\d+px[^"]*"[^>]*>\s*&nbsp;\s*<\/div>/i', '', $bodyContent);
        $bodyContent = preg_replace('/<div[^>]*>\s*&nbsp;\s*<\/div>/i', '', $bodyContent);

        // Format numbered points into two-column layout
        $bodyContent = preg_replace_callback('/<p([^>]*)>\s*(\d+\.\d+(?:\.\d+)?)\s+([^<]+)<\/p>/i', function ($matches) {
            $pAttrs = $matches[1];
            $number = $matches[2];
            $text = $matches[3];

            return '<table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0px; padding: 0px;">
                <tr style="page-break-inside: avoid;">
                    <td style="width: 50px; padding: 0px 10px 0px 0px; text-align: right; vertical-align: top;">' . $number . '</td>
                    <td style="padding: 0px 0px 0px 10px; text-align: left; vertical-align: top;">' . $text . '</td>
                </tr>
            </table>';
        }, $bodyContent);

        // Replace <strong><em> with Cambria Bold Italic
        $bodyContent = preg_replace(
            '/<strong>\s*<em>(.*?)<\/em>\s*<\/strong>/i',
            '<span style="font-family: cambriaz;">$1</span>',
            $bodyContent
        );

        // Convert margin-left to non-breaking spaces for indentation
        $bodyContent = preg_replace_callback('/<(span|div)([^>]*?)style="([^"]*?)margin-left:\s*(\d+)px([^"]*?)"([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $beforeStyle = $matches[2];
            $styleStart = $matches[3];
            $marginPx = $matches[4];
            $styleEnd = $matches[5];
            $afterStyle = $matches[6];

            $numSpaces = max(1, intval($marginPx / 4));
            $spaces = str_repeat('&nbsp;', $numSpaces);
            $newStyle = preg_replace('/margin-left:\s*\d+px;?\s*/i', '', $styleStart . $styleEnd);

            return '<' . $tag . $beforeStyle . 'style="' . $newStyle . '"' . $afterStyle . '>' . $spaces;
        }, $bodyContent);

        // Add nobr="true" to all tables
        $bodyContent = preg_replace_callback('/<table([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];
            if (strpos($attributes, 'nobr=') === false) {
                $attributes .= ' nobr="true"';
            }
            if (strpos($attributes, 'cellpadding=') === false) {
                $attributes .= ' cellpadding="4"';
            }
            // Add border-collapse style to ensure borders render properly
            if (strpos($attributes, 'style=') === false) {
                $attributes .= ' style="border-collapse: collapse; border: 1px solid #000;"';
            } else {
                $attributes = preg_replace('/style="([^"]*)"/', 'style="$1 border-collapse: collapse; border: 1px solid #000;"', $attributes);
            }
            return '<table' . $attributes . '>';
        }, $bodyContent);

        // Add CSS style to table rows
        $bodyContent = preg_replace('/<tr([^>]*)>/i', '<tr$1 style="page-break-inside: avoid;">', $bodyContent);

        // Add padding to table cells while preserving width
        $bodyContent = preg_replace_callback('/<(td|th)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            $width = '';
            if (preg_match('/width\s*=\s*["\']?([^"\'>\s]+)["\']?/i', $attributes, $widthMatch)) {
                $width = $widthMatch[1];
                $attributes = preg_replace('/\s*width\s*=\s*["\']?[^"\'>\s]+["\']?/i', '', $attributes);
            }

            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                $newStyle = $existingStyle . '; padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                $newStyle = 'padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . $attributes . ' style="' . $newStyle . '">';
            }
        }, $bodyContent);

        // Reduce paragraph spacing
        $bodyContent = preg_replace_callback('/<p([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                $newStyle = $existingStyle . '; margin: 0px; padding: 0px; line-height: 1;';
                return '<p' . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                return '<p' . $attributes . ' style="margin: 0px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent);

        // Add spacing for list items (li tags) - more space between bullet points
        $bodyContent = preg_replace_callback('/<li([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                $newStyle = $existingStyle . '; margin-bottom: 8px; line-height: 1.2;';
                return '<li' . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                return '<li' . $attributes . ' style="margin-bottom: 8px; line-height: 1.2;">';
            }
        }, $bodyContent);

        // Reduce spacing for headings and divs
        $bodyContent = preg_replace_callback('/<(h[1-6]|div)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                $newStyle = $existingStyle . '; margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;';
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                return '<' . $tag . $attributes . ' style="margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent);

        // Wrap ALL images in center div tags
        $bodyContent = preg_replace_callback('/<img([^>]*?)>/i', function ($matches) {
            $imgTag = $matches[0];
            return '<div align="center">' . $imgTag . '</div>';
        }, $bodyContent);

        // Convert image URLs to absolute paths
        $bodyContent = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
            $fullTag = $matches[0];
            $beforeSrc = $matches[1];
            $src = $matches[2];
            $afterSrc = $matches[3];

            if (strpos($src, 'uploads/documents/images/') !== false) {
                preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                if (isset($fileMatches[1])) {
                    $filename = $fileMatches[1];
                    $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                    if (file_exists($absolutePath)) {
                        return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . '>';
                    }
                }
            }

            return $fullTag;
        }, $bodyContent);

        return $bodyContent;
    }

    /**
     * Template 1: Standard System Procedure (SSP) - Repeats header table on each page
     */
    private function exportPdfTemplate1($document)
    {
        // Custom TCPDF class for Template 1 - repeats header on each page
        $pdf = new class($document) extends \TCPDF {
            private $doc;
            private $headerHeight = 0;
            private $pageCount = 0;

            public function __construct($doc)
            {
                parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $this->doc = $doc;
            }

            public function AddPage($orientation = '', $format = '', $keepmargins = false, $blanks = -1)
            {
                // Call parent AddPage
                parent::AddPage($orientation, $format, $keepmargins, $blanks);

                // After first page, change top margin to accommodate header
                $this->pageCount++;
                if ($this->pageCount > 1) {
                    $this->SetTopMargin(40);
                    $this->setHeaderMargin(5);
                } else {
                    // First page: increase header margin to prevent overlap
                    $this->setHeaderMargin(5);
                }
            }

            public function Header()
            {
                // Store current Y position to calculate header height
                $startY = $this->GetY();

                // 1. Get the header HTML from document content
                $documentContent = $this->doc['content'];

                // 2. EXTRACT HEADER TABLE: Support multiple header table types (ssp-header-table, sop-header-table, etc.)
                // Match any table with id ending in "-header-table"
                if (preg_match('/(<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>)/si', $documentContent, $matches)) {
                    $headerTable = $matches[1];
                    $tableId = $matches[2]; // Store the table ID for reference
                } else {
                    return; // No header table found, skip header
                }

                // 3. Convert image URLs to absolute file paths for TCPDF
                $headerTable = $this->convertImageUrlsToPath($headerTable);

                // 4. Convert pt units to px for better TCPDF compatibility
                $headerTable = preg_replace('/(\d+(?:\.\d+)?)pt/', '${1}px', $headerTable);

                // 5. Add border="0" attribute to table if not present to disable TCPDF defaults
                if (strpos($headerTable, 'border=') === false) {
                    $headerTable = str_replace('<table', '<table border="0"', $headerTable);
                }

                // 6. Dynamic Replacements
                // Replace page numbers - use TCPDF aliases which will be replaced after all pages are generated
                // For Cambria font compatibility, we'll use helvetica for page numbers
                $headerTable = preg_replace_callback('/(<span[^>]*style="[^"]*font-family:\s*cambria[^"]*"[^>]*>)\d+\s+of\s+\d+(<\/span>)/i', function ($matches) {
                    // Replace cambria with helvetica for page numbers to avoid encoding issues
                    $openTag = str_replace('font-family: cambria', 'font-family: helvetica', $matches[1]);
                    // Use TCPDF's alias system for dynamic page numbers
                    return $openTag . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages() . $matches[2];
                }, $headerTable);

                // Replace document number if it exists in span with id="doc-number"
                if (!empty($this->doc['document_number'])) {
                    $headerTable = preg_replace(
                        '/(<span[^>]*id=["\']doc-number["\'][^>]*>)[^<]*(<\/span>)/i',
                        '$1' . $this->doc['document_number'] . '$2',
                        $headerTable
                    );
                }

                // 7. Set position and print ONLY the table
                // Position header at top of page with small margin
                $this->SetY(5);
                $this->writeHTML($headerTable, true, false, true, false, '');

                // Calculate header height for proper spacing
                $this->headerHeight = $this->GetY() - $startY + 5;
            }

            private function convertImageUrlsToPath($html)
            {
                // Convert image URLs to absolute file paths and handle alignment
                $html = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
                    $fullTag = $matches[0];
                    $beforeSrc = $matches[1];
                    $src = $matches[2];
                    $afterSrc = $matches[3];

                    $allAttributes = $beforeSrc . $afterSrc;

                    // Check if it's a relative URL from our uploads folder
                    if (strpos($src, 'uploads/documents/images/') !== false) {
                        // Extract just the filename
                        preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                        if (isset($fileMatches[1])) {
                            $filename = $fileMatches[1];
                            $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                            // Check if file exists
                            if (file_exists($absolutePath)) {
                                // Extract alignment from style attribute
                                $align = '';
                                if (preg_match('/style=["\']([^"\']*)["\']/', $allAttributes, $styleMatch)) {
                                    $style = $styleMatch[1];

                                    // Check for margin-left: auto; margin-right: auto (center)
                                    if (strpos($style, 'margin-left: auto') !== false && strpos($style, 'margin-right: auto') !== false) {
                                        $align = ' align="center"';
                                    }
                                    // Check for display: block with margin auto
                                    elseif (strpos($style, 'display: block') !== false && strpos($style, 'margin') !== false) {
                                        if (strpos($style, 'margin-left: auto') !== false || strpos($style, 'margin-right: auto') !== false) {
                                            $align = ' align="center"';
                                        }
                                    }
                                    // Check for float
                                    elseif (preg_match('/float:\s*(left|right)/', $style, $floatMatch)) {
                                        $align = ' align="' . $floatMatch[1] . '"';
                                    }
                                }

                                // Check for existing align attribute
                                if (empty($align) && preg_match('/align=["\']([^"\']+)["\']/', $allAttributes, $alignMatch)) {
                                    $align = ' align="' . $alignMatch[1] . '"';
                                }

                                return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . $align . '>';
                            }
                        }
                    }

                    return $fullTag;
                }, $html);

                return $html;
            }
        };

        // Configuration
        $pdf->SetCreator('DMS');
        // Set initial top margin for first page to accommodate header
        $pdf->SetMargins(15, 40, 15);
        $pdf->SetAutoPageBreak(TRUE, 15); // Reduce bottom margin
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);

        // Increase cell height ratio for better spacing and to help prevent row splitting
        $pdf->setCellHeightRatio(1.0);

        // Reduce spacing between paragraphs
        $pdf->setCellPaddings(0, 0, 0, 0); // Remove cell padding
        $pdf->setCellMargins(0, 0, 0, 0); // Remove cell margins

        // Add custom Cambria font from assets
        $cambriaTTFPath = FCPATH . 'assets/fonts/cambria.ttf';
        $cambriaBTTFPath = FCPATH . 'assets/fonts/cambriab.ttf';
        $cambriaITTFPath = FCPATH . 'assets/fonts/cambriai.ttf';
        $cambriaZTTFPath = FCPATH . 'assets/fonts/cambriaz.ttf';
        $tcpdfFontsDir = FCPATH . '../vendor/tecnickcom/tcpdf/fonts/';

        $fontName = 'cambria'; // Default fallback

        // Check if we already have a generated Cambria Regular font
        if (file_exists($tcpdfFontsDir . 'cambria.php')) {
            $fontName = 'cambria';
            error_log('Using existing Cambria Regular font definition');

            // Ensure italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriai.php') && file_exists($cambriaITTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaITTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria italic font error: ' . $e->getMessage());
                }
            }

            // Ensure bold variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriab.php') && file_exists($cambriaBTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaBTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold font error: ' . $e->getMessage());
                }
            }

            // Ensure bold italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriaz.php') && file_exists($cambriaZTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaZTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold italic font error: ' . $e->getMessage());
                }
            }
        } elseif (file_exists($cambriaTTFPath)) {
            try {
                // Add Cambria Regular if not already present
                $addedFontName = \TCPDF_FONTS::addTTFfont(
                    $cambriaTTFPath,
                    'TrueTypeUnicode',
                    '',
                    32,
                    $tcpdfFontsDir
                );

                if ($addedFontName && $addedFontName !== false) {
                    $fontName = $addedFontName;
                    error_log('Cambria Regular font successfully added as: ' . $fontName);
                } else {
                    error_log('Failed to add Cambria Regular font - TCPDF returned false');
                }
            } catch (\Throwable $e) {
                error_log('Cambria Regular font error: ' . $e->getMessage());
            }
        }

        // Set font BEFORE adding page
        $pdf->SetFont($fontName, '', 11);

        $pdf->AddPage();

        // Clean body: Remove ALL header tables from the body content so they don't double-print
        // This removes any table with id ending in "-header-table" (ssp-header-table, sop-header-table, etc.)
        $bodyContent = $document['content'];
        $bodyContent = preg_replace('/<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>/si', '', $bodyContent);

        // Remove the document title (first h1 or h2 tag at the beginning)
        // This prevents the title from appearing above the header table
        $bodyContent = preg_replace('/<h[1-2][^>]*>.*?<\/h[1-2]>/si', '', $bodyContent, 1);

        // Remove empty divs with only non-breaking spaces (these create unwanted gaps)
        $bodyContent = preg_replace('/<div[^>]*style="[^"]*margin-left:\s*\d+px[^"]*"[^>]*>\s*&nbsp;\s*<\/div>/i', '', $bodyContent);
        $bodyContent = preg_replace('/<div[^>]*>\s*&nbsp;\s*<\/div>/i', '', $bodyContent);

        // Format numbered points (e.g., "6.1 Text here") into two-column layout
        // This creates a left column for the number and right column for the text
        $bodyContent = preg_replace_callback('/<p([^>]*)>\s*(\d+\.\d+(?:\.\d+)?)\s+([^<]+)<\/p>/i', function ($matches) {
            $pAttrs = $matches[1];
            $number = $matches[2];
            $text = $matches[3];

            // Create a table-like structure using divs for proper alignment
            // Left column: fixed width for number, right column: flexible for text
            return '<table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0px; padding: 0px;">
                <tr style="page-break-inside: avoid;">
                    <td style="width: 50px; padding: 0px 10px 0px 0px; text-align: right; vertical-align: top;">' . $number . '</td>
                    <td style="padding: 0px 0px 0px 10px; text-align: left; vertical-align: top;">' . $text . '</td>
                </tr>
            </table>';
        }, $bodyContent);

        // Replace font references with the actual font name returned by TCPDF FIRST
        // This ensures the font is properly set before we process other styles
        if ($fontName !== 'cambria') {
            // Cambria font was successfully added, replace all font references with it
            $bodyContent = preg_replace('/font-family:\s*["\']?cambria["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?calibri["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?arial["\']?/i', 'font-family: ' . $fontName, $bodyContent);
        } else {
            // Keep cambria in CSS so PDF viewers will request it
            // Don't replace - let the PDF viewer handle font substitution
        }

        // Replace <strong><em> with Cambria Bold Italic
        $bodyContent = preg_replace(
            '/<strong>\s*<em>(.*?)<\/em>\s*<\/strong>/i',
            '<span style="font-family: cambriaz;">$1</span>',
            $bodyContent
        );

        // Convert margin-left to non-breaking spaces for indentation in TCPDF
        // TCPDF doesn't render margin/padding well on inline elements, so we use &nbsp; for visual indentation
        // Handle both span and div tags with margin-left
        $bodyContent = preg_replace_callback('/<(span|div)([^>]*?)style="([^"]*?)margin-left:\s*(\d+)px([^"]*?)"([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $beforeStyle = $matches[2];
            $styleStart = $matches[3];
            $marginPx = $matches[4];
            $styleEnd = $matches[5];
            $afterStyle = $matches[6];

            // Calculate number of non-breaking spaces (roughly 1 space = 4px)
            $numSpaces = max(1, intval($marginPx / 4));
            $spaces = str_repeat('&nbsp;', $numSpaces);

            // Remove margin-left from style
            $newStyle = preg_replace('/margin-left:\s*\d+px;?\s*/i', '', $styleStart . $styleEnd);

            return '<' . $tag . $beforeStyle . 'style="' . $newStyle . '"' . $afterStyle . '>' . $spaces;
        }, $bodyContent);

        // Add nobr="true" to all tables to prevent row splitting across pages
        // Also add cellpadding for better readability
        $bodyContent = preg_replace_callback('/<table([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Add nobr="true" if not present
            if (strpos($attributes, 'nobr=') === false) {
                $attributes .= ' nobr="true"';
            }

            // Add cellpadding if not present
            if (strpos($attributes, 'cellpadding=') === false) {
                $attributes .= ' cellpadding="4"';
            }

            return '<table' . $attributes . '>';
        }, $bodyContent);

        // Add CSS style to table rows to keep them together
        $bodyContent = preg_replace('/<tr([^>]*)>/i', '<tr$1 style="page-break-inside: avoid;">', $bodyContent);

        // Add padding to table cells (td and th) for better readability while preserving width
        $bodyContent = preg_replace_callback('/<(td|th)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Extract existing width if present
            $width = '';
            if (preg_match('/width\s*=\s*["\']?([^"\'>\s]+)["\']?/i', $attributes, $widthMatch)) {
                $width = $widthMatch[1];
                // Remove width from attributes so we can add it to style
                $attributes = preg_replace('/\s*width\s*=\s*["\']?[^"\'>\s]+["\']?/i', '', $attributes);
            }

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add padding and width to existing style
                $newStyle = $existingStyle . '; padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute with padding and width
                $newStyle = 'padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . $attributes . ' style="' . $newStyle . '">';
            }
        }, $bodyContent);

        // Reduce paragraph spacing by adding margin styles and line-height
        $bodyContent = preg_replace_callback('/<p([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - increased line-height to 1.8 for better readability
                $newStyle = $existingStyle . '; margin: 0px; padding: 0px; line-height: 1;';
                return '<p' . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<p' . $attributes . ' style="margin: 0px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent); // Reduce spacing for headings (h1-h6) and divs - add negative margin-bottom to reduce gap before next section
        $bodyContent = preg_replace_callback('/<(h[1-6]|div)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - negative margin-bottom to reduce gap
                $newStyle = $existingStyle . '; margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;';
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<' . $tag . $attributes . ' style="margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent);

        // STEP 1: Wrap ALL images in center div tags for consistent PDF rendering
        $bodyContent = preg_replace_callback('/<img([^>]*?)>/i', function ($matches) {
            $imgTag = $matches[0];
            return '<div align="center">' . $imgTag . '</div>';
        }, $bodyContent);

        // STEP 2: Convert image URLs to absolute paths
        $bodyContent = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
            $fullTag = $matches[0];
            $beforeSrc = $matches[1];
            $src = $matches[2];
            $afterSrc = $matches[3];

            // Check if it's a relative URL from our uploads folder
            if (strpos($src, 'uploads/documents/images/') !== false) {
                // Extract just the filename
                preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                if (isset($fileMatches[1])) {
                    $filename = $fileMatches[1];
                    $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                    // Check if file exists
                    if (file_exists($absolutePath)) {
                        return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . '>';
                    }
                }
            }

            return $fullTag;
        }, $bodyContent);

        $pdf->writeHTML($bodyContent, true, false, true, false, '');

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document['title']) . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    private function exportPdfTemplate2($document)
    {
        // Custom TCPDF class for Template 1 - repeats header on each page
        $pdf = new class($document) extends \TCPDF {
            private $doc;
            private $headerHeight = 0;
            private $pageCount = 0;

            public function __construct($doc)
            {
                parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $this->doc = $doc;
            }

            public function AddPage($orientation = '', $format = '', $keepmargins = false, $blanks = -1)
            {
                // Call parent AddPage
                parent::AddPage($orientation, $format, $keepmargins, $blanks);

                // After first page, change top margin to accommodate header
                $this->pageCount++;
                if ($this->pageCount > 1) {
                    $this->SetTopMargin(50);
                    $this->setHeaderMargin(5);
                } else {
                    // First page: increase header margin to prevent overlap
                    $this->setHeaderMargin(5);
                }
            }

            public function Header()
            {
                // Store current Y position to calculate header height
                $startY = $this->GetY();

                // 1. Get the header HTML from document content
                $documentContent = $this->doc['content'];

                // 2. EXTRACT HEADER TABLE: Support multiple header table types (ssp-header-table, sop-header-table, etc.)
                // Match any table with id ending in "-header-table"
                if (preg_match('/(<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>)/si', $documentContent, $matches)) {
                    $headerTable = $matches[1];
                    $tableId = $matches[2]; // Store the table ID for reference
                } else {
                    return; // No header table found, skip header
                }

                // 3. Convert image URLs to absolute file paths for TCPDF
                $headerTable = $this->convertImageUrlsToPath($headerTable);

                // 4. Convert pt units to px for better TCPDF compatibility
                $headerTable = preg_replace('/(\d+(?:\.\d+)?)pt/', '${1}px', $headerTable);

                // 5. Add border="0" attribute to table if not present to disable TCPDF defaults
                if (strpos($headerTable, 'border=') === false) {
                    $headerTable = str_replace('<table', '<table border="0"', $headerTable);
                }

                // 6. Dynamic Replacements
                // Replace page numbers - use TCPDF aliases which will be replaced after all pages are generated
                // For Cambria font compatibility, we'll use helvetica for page numbers
                $headerTable = preg_replace_callback('/(<span[^>]*style="[^"]*font-family:\s*cambria[^"]*"[^>]*>)\d+\s+of\s+\d+(<\/span>)/i', function ($matches) {
                    // Replace cambria with helvetica for page numbers to avoid encoding issues
                    $openTag = str_replace('font-family: cambria', 'font-family: helvetica', $matches[1]);
                    // Use TCPDF's alias system for dynamic page numbers
                    return $openTag . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages() . $matches[2];
                }, $headerTable);

                // Replace document number if it exists in span with id="doc-number"
                if (!empty($this->doc['document_number'])) {
                    $headerTable = preg_replace(
                        '/(<span[^>]*id=["\']doc-number["\'][^>]*>)[^<]*(<\/span>)/i',
                        '$1' . $this->doc['document_number'] . '$2',
                        $headerTable
                    );
                }

                // 7. Set position and print ONLY the table
                // Position header at top of page with small margin
                $this->SetY(5);
                $this->writeHTML($headerTable, true, false, true, false, '');

                // Calculate header height for proper spacing
                $this->headerHeight = $this->GetY() - $startY + 5;
            }

            private function convertImageUrlsToPath($html)
            {
                // Convert image URLs to absolute file paths and handle alignment
                $html = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
                    $fullTag = $matches[0];
                    $beforeSrc = $matches[1];
                    $src = $matches[2];
                    $afterSrc = $matches[3];

                    $allAttributes = $beforeSrc . $afterSrc;

                    // Check if it's a relative URL from our uploads folder
                    if (strpos($src, 'uploads/documents/images/') !== false) {
                        // Extract just the filename
                        preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                        if (isset($fileMatches[1])) {
                            $filename = $fileMatches[1];
                            $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                            // Check if file exists
                            if (file_exists($absolutePath)) {
                                // Extract alignment from style attribute
                                $align = '';
                                if (preg_match('/style=["\']([^"\']*)["\']/', $allAttributes, $styleMatch)) {
                                    $style = $styleMatch[1];

                                    // Check for margin-left: auto; margin-right: auto (center)
                                    if (strpos($style, 'margin-left: auto') !== false && strpos($style, 'margin-right: auto') !== false) {
                                        $align = ' align="center"';
                                    }
                                    // Check for display: block with margin auto
                                    elseif (strpos($style, 'display: block') !== false && strpos($style, 'margin') !== false) {
                                        if (strpos($style, 'margin-left: auto') !== false || strpos($style, 'margin-right: auto') !== false) {
                                            $align = ' align="center"';
                                        }
                                    }
                                    // Check for float
                                    elseif (preg_match('/float:\s*(left|right)/', $style, $floatMatch)) {
                                        $align = ' align="' . $floatMatch[1] . '"';
                                    }
                                }

                                // Check for existing align attribute
                                if (empty($align) && preg_match('/align=["\']([^"\']+)["\']/', $allAttributes, $alignMatch)) {
                                    $align = ' align="' . $alignMatch[1] . '"';
                                }

                                return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . $align . '>';
                            }
                        }
                    }

                    return $fullTag;
                }, $html);

                return $html;
            }
        };

        // Configuration
        $pdf->SetCreator('DMS');
        // Set initial top margin for first page to accommodate header
        $pdf->SetMargins(15, 50, 15);
        $pdf->SetAutoPageBreak(TRUE, 15); // Reduce bottom margin
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);

        // Increase cell height ratio for better spacing and to help prevent row splitting
        $pdf->setCellHeightRatio(1.0);

        // Reduce spacing between paragraphs
        $pdf->setCellPaddings(0, 0, 0, 0); // Remove cell padding
        $pdf->setCellMargins(0, 0, 0, 0); // Remove cell margins

        // Add custom Cambria font from assets
        $cambriaTTFPath = FCPATH . 'assets/fonts/cambria.ttf';
        $cambriaBTTFPath = FCPATH . 'assets/fonts/cambriab.ttf';
        $cambriaITTFPath = FCPATH . 'assets/fonts/cambriai.ttf';
        $cambriaZTTFPath = FCPATH . 'assets/fonts/cambriaz.ttf';
        $tcpdfFontsDir = FCPATH . '../vendor/tecnickcom/tcpdf/fonts/';

        $fontName = 'cambria'; // Default fallback

        // Check if we already have a generated Cambria Regular font
        if (file_exists($tcpdfFontsDir . 'cambria.php')) {
            $fontName = 'cambria';
            error_log('Using existing Cambria Regular font definition');

            // Ensure italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriai.php') && file_exists($cambriaITTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaITTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria italic font error: ' . $e->getMessage());
                }
            }

            // Ensure bold variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriab.php') && file_exists($cambriaBTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaBTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold font error: ' . $e->getMessage());
                }
            }

            // Ensure bold italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriaz.php') && file_exists($cambriaZTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaZTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold italic font error: ' . $e->getMessage());
                }
            }
        } elseif (file_exists($cambriaTTFPath)) {
            try {
                // Add Cambria Regular if not already present
                $addedFontName = \TCPDF_FONTS::addTTFfont(
                    $cambriaTTFPath,
                    'TrueTypeUnicode',
                    '',
                    32,
                    $tcpdfFontsDir
                );

                if ($addedFontName && $addedFontName !== false) {
                    $fontName = $addedFontName;
                    error_log('Cambria Regular font successfully added as: ' . $fontName);
                } else {
                    error_log('Failed to add Cambria Regular font - TCPDF returned false');
                }
            } catch (\Throwable $e) {
                error_log('Cambria Regular font error: ' . $e->getMessage());
            }
        }

        // Set font BEFORE adding page
        $pdf->SetFont($fontName, '', 11);

        $pdf->AddPage();

        // Clean body: Remove ALL header tables from the body content so they don't double-print
        // This removes any table with id ending in "-header-table" (ssp-header-table, sop-header-table, etc.)
        $bodyContent = $document['content'];
        $bodyContent = preg_replace('/<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>/si', '', $bodyContent);

        // Remove the document title (first h1 or h2 tag at the beginning)
        // This prevents the title from appearing above the header table
        $bodyContent = preg_replace('/<h[1-2][^>]*>.*?<\/h[1-2]>/si', '', $bodyContent, 1);

        // Remove empty divs with only non-breaking spaces (these create unwanted gaps)
        $bodyContent = preg_replace('/<div[^>]*style="[^"]*margin-left:\s*\d+px[^"]*"[^>]*>\s*&nbsp;\s*<\/div>/i', '', $bodyContent);
        $bodyContent = preg_replace('/<div[^>]*>\s*&nbsp;\s*<\/div>/i', '', $bodyContent);

        // Format numbered points (e.g., "6.1 Text here") into two-column layout
        // This creates a left column for the number and right column for the text
        $bodyContent = preg_replace_callback('/<p([^>]*)>\s*(\d+\.\d+(?:\.\d+)?)\s+([^<]+)<\/p>/i', function ($matches) {
            $pAttrs = $matches[1];
            $number = $matches[2];
            $text = $matches[3];

            // Create a table-like structure using divs for proper alignment
            // Left column: fixed width for number, right column: flexible for text
            return '<table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0px; padding: 0px;">
                <tr style="page-break-inside: avoid;">
                    <td style="width: 50px; padding: 0px 10px 0px 0px; text-align: right; vertical-align: top;">' . $number . '</td>
                    <td style="padding: 0px 0px 0px 10px; text-align: left; vertical-align: top;">' . $text . '</td>
                </tr>
            </table>';
        }, $bodyContent);

        // Replace font references with the actual font name returned by TCPDF FIRST
        // This ensures the font is properly set before we process other styles
        if ($fontName !== 'cambria') {
            // Cambria font was successfully added, replace all font references with it
            $bodyContent = preg_replace('/font-family:\s*["\']?cambria["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?calibri["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?arial["\']?/i', 'font-family: ' . $fontName, $bodyContent);
        } else {
            // Keep cambria in CSS so PDF viewers will request it
            // Don't replace - let the PDF viewer handle font substitution
        }

        // Replace <strong><em> with Cambria Bold Italic
        $bodyContent = preg_replace(
            '/<strong>\s*<em>(.*?)<\/em>\s*<\/strong>/i',
            '<span style="font-family: cambriaz;">$1</span>',
            $bodyContent
        );

        // Convert margin-left to non-breaking spaces for indentation in TCPDF
        // TCPDF doesn't render margin/padding well on inline elements, so we use &nbsp; for visual indentation
        // Handle both span and div tags with margin-left
        $bodyContent = preg_replace_callback('/<(span|div)([^>]*?)style="([^"]*?)margin-left:\s*(\d+)px([^"]*?)"([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $beforeStyle = $matches[2];
            $styleStart = $matches[3];
            $marginPx = $matches[4];
            $styleEnd = $matches[5];
            $afterStyle = $matches[6];

            // Calculate number of non-breaking spaces (roughly 1 space = 4px)
            $numSpaces = max(1, intval($marginPx / 4));
            $spaces = str_repeat('&nbsp;', $numSpaces);

            // Remove margin-left from style
            $newStyle = preg_replace('/margin-left:\s*\d+px;?\s*/i', '', $styleStart . $styleEnd);

            return '<' . $tag . $beforeStyle . 'style="' . $newStyle . '"' . $afterStyle . '>' . $spaces;
        }, $bodyContent);

        // Add nobr="true" to all tables to prevent row splitting across pages
        // Also add cellpadding for better readability
        $bodyContent = preg_replace_callback('/<table([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Add nobr="true" if not present
            if (strpos($attributes, 'nobr=') === false) {
                $attributes .= ' nobr="true"';
            }

            // Add cellpadding if not present
            if (strpos($attributes, 'cellpadding=') === false) {
                $attributes .= ' cellpadding="4"';
            }

            return '<table' . $attributes . '>';
        }, $bodyContent);

        // Add CSS style to table rows to keep them together
        $bodyContent = preg_replace('/<tr([^>]*)>/i', '<tr$1 style="page-break-inside: avoid;">', $bodyContent);

        // Add padding to table cells (td and th) for better readability while preserving width
        $bodyContent = preg_replace_callback('/<(td|th)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Extract existing width if present
            $width = '';
            if (preg_match('/width\s*=\s*["\']?([^"\'>\s]+)["\']?/i', $attributes, $widthMatch)) {
                $width = $widthMatch[1];
                // Remove width from attributes so we can add it to style
                $attributes = preg_replace('/\s*width\s*=\s*["\']?[^"\'>\s]+["\']?/i', '', $attributes);
            }

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add padding and width to existing style
                $newStyle = $existingStyle . '; padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute with padding and width
                $newStyle = 'padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . $attributes . ' style="' . $newStyle . '">';
            }
        }, $bodyContent);

        // Reduce paragraph spacing by adding margin styles and line-height
        $bodyContent = preg_replace_callback('/<p([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - increased line-height to 1.8 for better readability
                $newStyle = $existingStyle . '; margin: 0px; padding: 0px; line-height: 1;';
                return '<p' . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<p' . $attributes . ' style="margin: 0px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent); // Reduce spacing for headings (h1-h6) and divs - add negative margin-bottom to reduce gap before next section
        $bodyContent = preg_replace_callback('/<(h[1-6]|div)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - negative margin-bottom to reduce gap
                $newStyle = $existingStyle . '; margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;';
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<' . $tag . $attributes . ' style="margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent);

        // STEP 1: Wrap ALL images in center div tags for consistent PDF rendering
        $bodyContent = preg_replace_callback('/<img([^>]*?)>/i', function ($matches) {
            $imgTag = $matches[0];
            return '<div align="center">' . $imgTag . '</div>';
        }, $bodyContent);

        // STEP 2: Convert image URLs to absolute paths
        $bodyContent = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
            $fullTag = $matches[0];
            $beforeSrc = $matches[1];
            $src = $matches[2];
            $afterSrc = $matches[3];

            // Check if it's a relative URL from our uploads folder
            if (strpos($src, 'uploads/documents/images/') !== false) {
                // Extract just the filename
                preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                if (isset($fileMatches[1])) {
                    $filename = $fileMatches[1];
                    $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                    // Check if file exists
                    if (file_exists($absolutePath)) {
                        return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . '>';
                    }
                }
            }

            return $fullTag;
        }, $bodyContent);

        $pdf->writeHTML($bodyContent, true, false, true, false, '');

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document['title']) . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    private function exportPdfTemplate3($document)
    {
        // Custom TCPDF class for Template 1 - repeats header on each page
        $pdf = new class($document) extends \TCPDF {
            private $doc;
            private $headerHeight = 0;
            private $pageCount = 0;

            public function __construct($doc)
            {
                parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $this->doc = $doc;
            }

            public function AddPage($orientation = '', $format = '', $keepmargins = false, $blanks = -1)
            {
                // Call parent AddPage
                parent::AddPage($orientation, $format, $keepmargins, $blanks);

                // After first page, change top margin to accommodate header
                $this->pageCount++;
                if ($this->pageCount > 1) {
                    $this->SetTopMargin(40);
                    $this->setHeaderMargin(5);
                } else {
                    // First page: increase header margin to prevent overlap
                    $this->setHeaderMargin(5);
                }
            }

            public function Header()
            {
                // Store current Y position to calculate header height
                $startY = $this->GetY();

                // 1. Get the header HTML from document content
                $documentContent = $this->doc['content'];

                // 2. EXTRACT HEADER TABLE: Support multiple header table types (ssp-header-table, sop-header-table, etc.)
                // Match any table with id ending in "-header-table"
                if (preg_match('/(<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>)/si', $documentContent, $matches)) {
                    $headerTable = $matches[1];
                    $tableId = $matches[2]; // Store the table ID for reference
                } else {
                    return; // No header table found, skip header
                }

                // 3. Convert image URLs to absolute file paths for TCPDF
                $headerTable = $this->convertImageUrlsToPath($headerTable);

                // 4. Convert pt units to px for better TCPDF compatibility
                $headerTable = preg_replace('/(\d+(?:\.\d+)?)pt/', '${1}px', $headerTable);

                // 5. Add border="0" attribute to table if not present to disable TCPDF defaults
                if (strpos($headerTable, 'border=') === false) {
                    $headerTable = str_replace('<table', '<table border="0"', $headerTable);
                }

                // 6. Dynamic Replacements
                // Replace page numbers - use TCPDF aliases which will be replaced after all pages are generated
                // For Cambria font compatibility, we'll use helvetica for page numbers
                $headerTable = preg_replace_callback('/(<span[^>]*style="[^"]*font-family:\s*cambria[^"]*"[^>]*>)\d+\s+of\s+\d+(<\/span>)/i', function ($matches) {
                    // Replace cambria with helvetica for page numbers to avoid encoding issues
                    $openTag = str_replace('font-family: cambria', 'font-family: helvetica', $matches[1]);
                    // Use TCPDF's alias system for dynamic page numbers
                    return $openTag . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages() . $matches[2];
                }, $headerTable);

                // Replace document number if it exists in span with id="doc-number"
                if (!empty($this->doc['document_number'])) {
                    $headerTable = preg_replace(
                        '/(<span[^>]*id=["\']doc-number["\'][^>]*>)[^<]*(<\/span>)/i',
                        '$1' . $this->doc['document_number'] . '$2',
                        $headerTable
                    );
                }

                // 7. Set position and print ONLY the table
                // Position header at top of page with small margin
                $this->SetY(5);
                $this->writeHTML($headerTable, true, false, true, false, '');

                // Calculate header height for proper spacing
                $this->headerHeight = $this->GetY() - $startY + 5;
            }

            private function convertImageUrlsToPath($html)
            {
                // Convert image URLs to absolute file paths and handle alignment
                $html = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
                    $fullTag = $matches[0];
                    $beforeSrc = $matches[1];
                    $src = $matches[2];
                    $afterSrc = $matches[3];

                    $allAttributes = $beforeSrc . $afterSrc;

                    // Check if it's a relative URL from our uploads folder
                    if (strpos($src, 'uploads/documents/images/') !== false) {
                        // Extract just the filename
                        preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                        if (isset($fileMatches[1])) {
                            $filename = $fileMatches[1];
                            $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                            // Check if file exists
                            if (file_exists($absolutePath)) {
                                // Extract alignment from style attribute
                                $align = '';
                                if (preg_match('/style=["\']([^"\']*)["\']/', $allAttributes, $styleMatch)) {
                                    $style = $styleMatch[1];

                                    // Check for margin-left: auto; margin-right: auto (center)
                                    if (strpos($style, 'margin-left: auto') !== false && strpos($style, 'margin-right: auto') !== false) {
                                        $align = ' align="center"';
                                    }
                                    // Check for display: block with margin auto
                                    elseif (strpos($style, 'display: block') !== false && strpos($style, 'margin') !== false) {
                                        if (strpos($style, 'margin-left: auto') !== false || strpos($style, 'margin-right: auto') !== false) {
                                            $align = ' align="center"';
                                        }
                                    }
                                    // Check for float
                                    elseif (preg_match('/float:\s*(left|right)/', $style, $floatMatch)) {
                                        $align = ' align="' . $floatMatch[1] . '"';
                                    }
                                }

                                // Check for existing align attribute
                                if (empty($align) && preg_match('/align=["\']([^"\']+)["\']/', $allAttributes, $alignMatch)) {
                                    $align = ' align="' . $alignMatch[1] . '"';
                                }

                                return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . $align . '>';
                            }
                        }
                    }

                    return $fullTag;
                }, $html);

                return $html;
            }
        };

        // Configuration
        $pdf->SetCreator('DMS');
        // Set initial top margin for first page to accommodate header
        $pdf->SetMargins(15, 50, 15);
        $pdf->SetAutoPageBreak(TRUE, 15); // Reduce bottom margin
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);

        // Increase cell height ratio for better spacing and to help prevent row splitting
        $pdf->setCellHeightRatio(1.0);

        // Reduce spacing between paragraphs
        $pdf->setCellPaddings(0, 0, 0, 0); // Remove cell padding
        $pdf->setCellMargins(0, 0, 0, 0); // Remove cell margins

        // Add custom Cambria font from assets
        $cambriaTTFPath = FCPATH . 'assets/fonts/cambria.ttf';
        $cambriaBTTFPath = FCPATH . 'assets/fonts/cambriab.ttf';
        $cambriaITTFPath = FCPATH . 'assets/fonts/cambriai.ttf';
        $cambriaZTTFPath = FCPATH . 'assets/fonts/cambriaz.ttf';
        $tcpdfFontsDir = FCPATH . '../vendor/tecnickcom/tcpdf/fonts/';

        $fontName = 'cambria'; // Default fallback

        // Check if we already have a generated Cambria Regular font
        if (file_exists($tcpdfFontsDir . 'cambria.php')) {
            $fontName = 'cambria';
            error_log('Using existing Cambria Regular font definition');

            // Ensure italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriai.php') && file_exists($cambriaITTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaITTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria italic font error: ' . $e->getMessage());
                }
            }

            // Ensure bold variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriab.php') && file_exists($cambriaBTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaBTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold font error: ' . $e->getMessage());
                }
            }

            // Ensure bold italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriaz.php') && file_exists($cambriaZTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaZTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold italic font error: ' . $e->getMessage());
                }
            }
        } elseif (file_exists($cambriaTTFPath)) {
            try {
                // Add Cambria Regular if not already present
                $addedFontName = \TCPDF_FONTS::addTTFfont(
                    $cambriaTTFPath,
                    'TrueTypeUnicode',
                    '',
                    32,
                    $tcpdfFontsDir
                );

                if ($addedFontName && $addedFontName !== false) {
                    $fontName = $addedFontName;
                    error_log('Cambria Regular font successfully added as: ' . $fontName);
                } else {
                    error_log('Failed to add Cambria Regular font - TCPDF returned false');
                }
            } catch (\Throwable $e) {
                error_log('Cambria Regular font error: ' . $e->getMessage());
            }
        }

        // Set font BEFORE adding page
        $pdf->SetFont($fontName, '', 11);

        $pdf->AddPage();

        // Clean body: Remove ALL header tables from the body content so they don't double-print
        // This removes any table with id ending in "-header-table" (ssp-header-table, sop-header-table, etc.)
        $bodyContent = $document['content'];
        $bodyContent = preg_replace('/<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>/si', '', $bodyContent);

        // Remove the document title (first h1 or h2 tag at the beginning)
        // This prevents the title from appearing above the header table
        $bodyContent = preg_replace('/<h[1-2][^>]*>.*?<\/h[1-2]>/si', '', $bodyContent, 1);

        // Remove empty divs with only non-breaking spaces (these create unwanted gaps)
        $bodyContent = preg_replace('/<div[^>]*style="[^"]*margin-left:\s*\d+px[^"]*"[^>]*>\s*&nbsp;\s*<\/div>/i', '', $bodyContent);
        $bodyContent = preg_replace('/<div[^>]*>\s*&nbsp;\s*<\/div>/i', '', $bodyContent);

        // Format numbered points (e.g., "6.1 Text here") into two-column layout
        // This creates a left column for the number and right column for the text
        $bodyContent = preg_replace_callback('/<p([^>]*)>\s*(\d+\.\d+(?:\.\d+)?)\s+([^<]+)<\/p>/i', function ($matches) {
            $pAttrs = $matches[1];
            $number = $matches[2];
            $text = $matches[3];

            // Create a table-like structure using divs for proper alignment
            // Left column: fixed width for number, right column: flexible for text
            return '<table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0px; padding: 0px;">
                <tr style="page-break-inside: avoid;">
                    <td style="width: 50px; padding: 0px 10px 0px 0px; text-align: right; vertical-align: top;">' . $number . '</td>
                    <td style="padding: 0px 0px 0px 10px; text-align: left; vertical-align: top;">' . $text . '</td>
                </tr>
            </table>';
        }, $bodyContent);

        // Replace font references with the actual font name returned by TCPDF FIRST
        // This ensures the font is properly set before we process other styles
        if ($fontName !== 'cambria') {
            // Cambria font was successfully added, replace all font references with it
            $bodyContent = preg_replace('/font-family:\s*["\']?cambria["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?calibri["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?arial["\']?/i', 'font-family: ' . $fontName, $bodyContent);
        } else {
            // Keep cambria in CSS so PDF viewers will request it
            // Don't replace - let the PDF viewer handle font substitution
        }

        // Replace <strong><em> with Cambria Bold Italic
        $bodyContent = preg_replace(
            '/<strong>\s*<em>(.*?)<\/em>\s*<\/strong>/i',
            '<span style="font-family: cambriaz;">$1</span>',
            $bodyContent
        );

        // Convert margin-left to non-breaking spaces for indentation in TCPDF
        // TCPDF doesn't render margin/padding well on inline elements, so we use &nbsp; for visual indentation
        // Handle both span and div tags with margin-left
        $bodyContent = preg_replace_callback('/<(span|div)([^>]*?)style="([^"]*?)margin-left:\s*(\d+)px([^"]*?)"([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $beforeStyle = $matches[2];
            $styleStart = $matches[3];
            $marginPx = $matches[4];
            $styleEnd = $matches[5];
            $afterStyle = $matches[6];

            // Calculate number of non-breaking spaces (roughly 1 space = 4px)
            $numSpaces = max(1, intval($marginPx / 4));
            $spaces = str_repeat('&nbsp;', $numSpaces);

            // Remove margin-left from style
            $newStyle = preg_replace('/margin-left:\s*\d+px;?\s*/i', '', $styleStart . $styleEnd);

            return '<' . $tag . $beforeStyle . 'style="' . $newStyle . '"' . $afterStyle . '>' . $spaces;
        }, $bodyContent);

        // Add nobr="true" to all tables to prevent row splitting across pages
        // Also add cellpadding for better readability
        $bodyContent = preg_replace_callback('/<table([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Add nobr="true" if not present
            if (strpos($attributes, 'nobr=') === false) {
                $attributes .= ' nobr="true"';
            }

            // Add cellpadding if not present
            if (strpos($attributes, 'cellpadding=') === false) {
                $attributes .= ' cellpadding="4"';
            }

            return '<table' . $attributes . '>';
        }, $bodyContent);

        // Add CSS style to table rows to keep them together
        $bodyContent = preg_replace('/<tr([^>]*)>/i', '<tr$1 style="page-break-inside: avoid;">', $bodyContent);

        // Add padding to table cells (td and th) for better readability while preserving width
        $bodyContent = preg_replace_callback('/<(td|th)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Extract existing width if present
            $width = '';
            if (preg_match('/width\s*=\s*["\']?([^"\'>\s]+)["\']?/i', $attributes, $widthMatch)) {
                $width = $widthMatch[1];
                // Remove width from attributes so we can add it to style
                $attributes = preg_replace('/\s*width\s*=\s*["\']?[^"\'>\s]+["\']?/i', '', $attributes);
            }

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add padding and width to existing style
                $newStyle = $existingStyle . '; padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute with padding and width
                $newStyle = 'padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . $attributes . ' style="' . $newStyle . '">';
            }
        }, $bodyContent);

        // Reduce paragraph spacing by adding margin styles and line-height
        $bodyContent = preg_replace_callback('/<p([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - increased line-height to 1.8 for better readability
                $newStyle = $existingStyle . '; margin: 0px; padding: 0px; line-height: 1;';
                return '<p' . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<p' . $attributes . ' style="margin: 0px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent); // Reduce spacing for headings (h1-h6) and divs - add negative margin-bottom to reduce gap before next section
        $bodyContent = preg_replace_callback('/<(h[1-6]|div)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - negative margin-bottom to reduce gap
                $newStyle = $existingStyle . '; margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;';
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<' . $tag . $attributes . ' style="margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent);

        // STEP 1: Wrap ALL images in center div tags for consistent PDF rendering
        $bodyContent = preg_replace_callback('/<img([^>]*?)>/i', function ($matches) {
            $imgTag = $matches[0];
            return '<div align="center">' . $imgTag . '</div>';
        }, $bodyContent);

        // STEP 2: Convert image URLs to absolute paths
        $bodyContent = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
            $fullTag = $matches[0];
            $beforeSrc = $matches[1];
            $src = $matches[2];
            $afterSrc = $matches[3];

            // Check if it's a relative URL from our uploads folder
            if (strpos($src, 'uploads/documents/images/') !== false) {
                // Extract just the filename
                preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                if (isset($fileMatches[1])) {
                    $filename = $fileMatches[1];
                    $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                    // Check if file exists
                    if (file_exists($absolutePath)) {
                        return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . '>';
                    }
                }
            }

            return $fullTag;
        }, $bodyContent);

        $pdf->writeHTML($bodyContent, true, false, true, false, '');

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document['title']) . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    private function exportPdfTemplate4($document)
    {
        // Custom TCPDF class for Template 1 - repeats header on each page
        $pdf = new class($document) extends \TCPDF {
            private $doc;
            private $headerHeight = 0;
            private $pageCount = 0;

            public function __construct($doc)
            {
                parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $this->doc = $doc;
            }

            public function AddPage($orientation = '', $format = '', $keepmargins = false, $blanks = -1)
            {
                // Call parent AddPage
                parent::AddPage($orientation, $format, $keepmargins, $blanks);

                // After first page, change top margin to accommodate header
                $this->pageCount++;
                if ($this->pageCount > 1) {
                    $this->SetTopMargin(10);
                    $this->setHeaderMargin(5);
                } else {
                    // First page: increase header margin to prevent overlap
                    $this->setHeaderMargin(1);
                }
            }

            public function Header()
            {
                // Store current Y position to calculate header height
                $startY = $this->GetY();

                // 1. Get the header HTML from document content
                $documentContent = $this->doc['content'];

                // 2. EXTRACT HEADER TABLE: Support multiple header table types (ssp-header-table, sop-header-table, etc.)
                // Match any table with id ending in "-header-table"
                if (preg_match('/(<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>)/si', $documentContent, $matches)) {
                    $headerTable = $matches[1];
                    $tableId = $matches[2]; // Store the table ID for reference
                } else {
                    return; // No header table found, skip header
                }

                // 3. Convert image URLs to absolute file paths for TCPDF
                $headerTable = $this->convertImageUrlsToPath($headerTable);

                // 4. Convert pt units to px for better TCPDF compatibility
                $headerTable = preg_replace('/(\d+(?:\.\d+)?)pt/', '${1}px', $headerTable);

                // 5. Add border="0" attribute to table if not present to disable TCPDF defaults
                if (strpos($headerTable, 'border=') === false) {
                    $headerTable = str_replace('<table', '<table border="0"', $headerTable);
                }

                // 6. Dynamic Replacements
                // Replace page numbers - use TCPDF aliases which will be replaced after all pages are generated
                // For Cambria font compatibility, we'll use helvetica for page numbers
                $headerTable = preg_replace_callback('/(<span[^>]*style="[^"]*font-family:\s*cambria[^"]*"[^>]*>)\d+\s+of\s+\d+(<\/span>)/i', function ($matches) {
                    // Replace cambria with helvetica for page numbers to avoid encoding issues
                    $openTag = str_replace('font-family: cambria', 'font-family: helvetica', $matches[1]);
                    // Use TCPDF's alias system for dynamic page numbers
                    return $openTag . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages() . $matches[2];
                }, $headerTable);

                // Replace document number if it exists in span with id="doc-number"
                if (!empty($this->doc['document_number'])) {
                    $headerTable = preg_replace(
                        '/(<span[^>]*id=["\']doc-number["\'][^>]*>)[^<]*(<\/span>)/i',
                        '$1' . $this->doc['document_number'] . '$2',
                        $headerTable
                    );
                }

                // 7. Set position and print ONLY the table
                // Position header at top of page with small margin
                $this->SetY(5);
                $this->writeHTML($headerTable, true, false, true, false, '');

                // Calculate header height for proper spacing
                $this->headerHeight = $this->GetY() - $startY + 5;
            }

            private function convertImageUrlsToPath($html)
            {
                // Convert image URLs to absolute file paths and handle alignment
                $html = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
                    $fullTag = $matches[0];
                    $beforeSrc = $matches[1];
                    $src = $matches[2];
                    $afterSrc = $matches[3];

                    $allAttributes = $beforeSrc . $afterSrc;

                    // Check if it's a relative URL from our uploads folder
                    if (strpos($src, 'uploads/documents/images/') !== false) {
                        // Extract just the filename
                        preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                        if (isset($fileMatches[1])) {
                            $filename = $fileMatches[1];
                            $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                            // Check if file exists
                            if (file_exists($absolutePath)) {
                                // Extract alignment from style attribute
                                $align = '';
                                if (preg_match('/style=["\']([^"\']*)["\']/', $allAttributes, $styleMatch)) {
                                    $style = $styleMatch[1];

                                    // Check for margin-left: auto; margin-right: auto (center)
                                    if (strpos($style, 'margin-left: auto') !== false && strpos($style, 'margin-right: auto') !== false) {
                                        $align = ' align="center"';
                                    }
                                    // Check for display: block with margin auto
                                    elseif (strpos($style, 'display: block') !== false && strpos($style, 'margin') !== false) {
                                        if (strpos($style, 'margin-left: auto') !== false || strpos($style, 'margin-right: auto') !== false) {
                                            $align = ' align="center"';
                                        }
                                    }
                                    // Check for float
                                    elseif (preg_match('/float:\s*(left|right)/', $style, $floatMatch)) {
                                        $align = ' align="' . $floatMatch[1] . '"';
                                    }
                                }

                                // Check for existing align attribute
                                if (empty($align) && preg_match('/align=["\']([^"\']+)["\']/', $allAttributes, $alignMatch)) {
                                    $align = ' align="' . $alignMatch[1] . '"';
                                }

                                return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . $align . '>';
                            }
                        }
                    }

                    return $fullTag;
                }, $html);

                return $html;
            }
        };

        // Configuration
        $pdf->SetCreator('DMS');
        // Set initial top margin for first page to accommodate header
        $pdf->SetMargins(15, 10, 15);
        $pdf->SetAutoPageBreak(TRUE, 15); // Reduce bottom margin
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);

        // Increase cell height ratio for better spacing and to help prevent row splitting
        $pdf->setCellHeightRatio(1.0);

        // Reduce spacing between paragraphs
        $pdf->setCellPaddings(0, 0, 0, 0); // Remove cell padding
        $pdf->setCellMargins(0, 0, 0, 0); // Remove cell margins

        // Add custom Cambria font from assets
        $cambriaTTFPath = FCPATH . 'assets/fonts/cambria.ttf';
        $cambriaBTTFPath = FCPATH . 'assets/fonts/cambriab.ttf';
        $cambriaITTFPath = FCPATH . 'assets/fonts/cambriai.ttf';
        $cambriaZTTFPath = FCPATH . 'assets/fonts/cambriaz.ttf';
        $tcpdfFontsDir = FCPATH . '../vendor/tecnickcom/tcpdf/fonts/';

        $fontName = 'cambria'; // Default fallback

        // Check if we already have a generated Cambria Regular font
        if (file_exists($tcpdfFontsDir . 'cambria.php')) {
            $fontName = 'cambria';
            error_log('Using existing Cambria Regular font definition');

            // Ensure italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriai.php') && file_exists($cambriaITTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaITTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria italic font error: ' . $e->getMessage());
                }
            }

            // Ensure bold variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriab.php') && file_exists($cambriaBTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaBTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold font error: ' . $e->getMessage());
                }
            }

            // Ensure bold italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriaz.php') && file_exists($cambriaZTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaZTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold italic font error: ' . $e->getMessage());
                }
            }
        } elseif (file_exists($cambriaTTFPath)) {
            try {
                // Add Cambria Regular if not already present
                $addedFontName = \TCPDF_FONTS::addTTFfont(
                    $cambriaTTFPath,
                    'TrueTypeUnicode',
                    '',
                    32,
                    $tcpdfFontsDir
                );

                if ($addedFontName && $addedFontName !== false) {
                    $fontName = $addedFontName;
                    error_log('Cambria Regular font successfully added as: ' . $fontName);
                } else {
                    error_log('Failed to add Cambria Regular font - TCPDF returned false');
                }
            } catch (\Throwable $e) {
                error_log('Cambria Regular font error: ' . $e->getMessage());
            }
        }

        // Set font BEFORE adding page
        $pdf->SetFont($fontName, '', 10);

        $pdf->AddPage();

        // Clean body: Remove ALL header tables from the body content so they don't double-print
        // This removes any table with id ending in "-header-table" (ssp-header-table, sop-header-table, etc.)
        $bodyContent = $document['content'];
        $bodyContent = preg_replace_callback('/<(td|th)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Extract existing style
            $style = '';
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $style = $styleMatch[1];
                // Append padding/border but keep existing width
                if (strpos($style, 'padding') === false) {
                    $style .= '; padding: 2px 3px;';
                }
                if (strpos($style, 'border') === false) {
                    $style .= '; border: 1px solid #000;';
                }
                $attributes = str_replace($styleMatch[0], 'style="' . $style . '"', $attributes);
            } else {
                // No style attribute → add new one
                $attributes .= ' style="padding: 2px 3px; border: 1px solid #000;"';
            }

            return '<' . $tag . $attributes . '>';
        }, $bodyContent);


        // Format numbered points (e.g., "6.1 Text here") into two-column layout
        // This creates a left column for the number and right column for the text
        $bodyContent = preg_replace_callback('/<p([^>]*)>\s*(\d+\.\d+(?:\.\d+)?)\s+([^<]+)<\/p>/i', function ($matches) {
            $pAttrs = $matches[1];
            $number = $matches[2];
            $text = $matches[3];

            // Create a table-like structure using divs for proper alignment
            // Left column: fixed width for number, right column: flexible for text
            return '<table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0px; padding: 0px;">
                <tr style="page-break-inside: avoid;">
                    <td style="width: 50px; padding: 0px 10px 0px 0px; text-align: right; vertical-align: top;">' . $number . '</td>
                    <td style="padding: 0px 0px 0px 10px; text-align: left; vertical-align: top;">' . $text . '</td>
                </tr>
            </table>';
        }, $bodyContent);

        // Replace font references with the actual font name returned by TCPDF FIRST
        // This ensures the font is properly set before we process other styles
        if ($fontName !== 'cambria') {
            // Cambria font was successfully added, replace all font references with it
            $bodyContent = preg_replace('/font-family:\s*["\']?cambria["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?calibri["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?arial["\']?/i', 'font-family: ' . $fontName, $bodyContent);
        } else {
            // Keep cambria in CSS so PDF viewers will request it
            // Don't replace - let the PDF viewer handle font substitution
        }

        // Replace <strong><em> with Cambria Bold Italic
        $bodyContent = preg_replace(
            '/<strong>\s*<em>(.*?)<\/em>\s*<\/strong>/i',
            '<span style="font-family: cambriaz;">$1</span>',
            $bodyContent
        );

        // Convert margin-left to non-breaking spaces for indentation in TCPDF
        // TCPDF doesn't render margin/padding well on inline elements, so we use &nbsp; for visual indentation
        // Handle both span and div tags with margin-left
        $bodyContent = preg_replace_callback('/<(span|div)([^>]*?)style="([^"]*?)margin-left:\s*(\d+)px([^"]*?)"([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $beforeStyle = $matches[2];
            $styleStart = $matches[3];
            $marginPx = $matches[4];
            $styleEnd = $matches[5];
            $afterStyle = $matches[6];

            // Calculate number of non-breaking spaces (roughly 1 space = 4px)
            $numSpaces = max(1, intval($marginPx / 4));
            $spaces = str_repeat('&nbsp;', $numSpaces);

            // Remove margin-left from style
            $newStyle = preg_replace('/margin-left:\s*\d+px;?\s*/i', '', $styleStart . $styleEnd);

            return '<' . $tag . $beforeStyle . 'style="' . $newStyle . '"' . $afterStyle . '>' . $spaces;
        }, $bodyContent);

        // Add nobr="true" to all tables to prevent row splitting across pages
        // Also add cellpadding for better readability
        $bodyContent = preg_replace_callback('/<table([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Add nobr="true" if not present
            if (strpos($attributes, 'nobr=') === false) {
                $attributes .= ' nobr="true"';
            }

            // Add cellpadding if not present
            if (strpos($attributes, 'cellpadding=') === false) {
                $attributes .= ' cellpadding="4"';
            }

            return '<table' . $attributes . '>';
        }, $bodyContent);

        // Add CSS style to table rows to keep them together
        $bodyContent = preg_replace('/<tr([^>]*)>/i', '<tr$1 style="page-break-inside: avoid;">', $bodyContent);

        // Add padding to table cells (td and th) for better readability while preserving width
        $bodyContent = preg_replace_callback('/<(td|th)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Extract existing width if present
            $width = '';
            if (preg_match('/width\s*=\s*["\']?([^"\'>\s]+)["\']?/i', $attributes, $widthMatch)) {
                $width = $widthMatch[1];
                // Remove width from attributes so we can add it to style
                $attributes = preg_replace('/\s*width\s*=\s*["\']?[^"\'>\s]+["\']?/i', '', $attributes);
            }

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add padding and width to existing style
                $newStyle = $existingStyle . '; padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute with padding and width
                $newStyle = 'padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . $attributes . ' style="' . $newStyle . '">';
            }
        }, $bodyContent);

        // Reduce paragraph spacing by adding margin styles and line-height
        $bodyContent = preg_replace_callback('/<p([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - increased line-height to 1.8 for better readability
                $newStyle = $existingStyle . '; margin: 0px; padding: 0px; line-height: 1;';
                return '<p' . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<p' . $attributes . ' style="margin: 0px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent); // Reduce spacing for headings (h1-h6) and divs - add negative margin-bottom to reduce gap before next section
        $bodyContent = preg_replace_callback('/<(h[1-6]|div)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - negative margin-bottom to reduce gap
                $newStyle = $existingStyle . '; margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;';
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<' . $tag . $attributes . ' style="margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent);

        // STEP 1: Wrap ALL images in center div tags for consistent PDF rendering
        $bodyContent = preg_replace_callback('/<img([^>]*?)>/i', function ($matches) {
            $imgTag = $matches[0];
            return '<div align="center">' . $imgTag . '</div>';
        }, $bodyContent);

        // STEP 2: Convert image URLs to absolute paths
        $bodyContent = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
            $fullTag = $matches[0];
            $beforeSrc = $matches[1];
            $src = $matches[2];
            $afterSrc = $matches[3];

            // Check if it's a relative URL from our uploads folder
            if (strpos($src, 'uploads/documents/images/') !== false) {
                // Extract just the filename
                preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                if (isset($fileMatches[1])) {
                    $filename = $fileMatches[1];
                    $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                    // Check if file exists
                    if (file_exists($absolutePath)) {
                        return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . '>';
                    }
                }
            }

            return $fullTag;
        }, $bodyContent);

        $pdf->writeHTML($bodyContent, true, false, true, false, '');

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document['title']) . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    private function exportPdfTemplate5($document)
    {
        // Custom TCPDF class for Template 1 - repeats header on each page
        $pdf = new class($document) extends \TCPDF {
            private $doc;
            private $headerHeight = 0;
            private $pageCount = 0;

            public function __construct($doc)
            {
                parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
                $this->doc = $doc;
            }

            public function AddPage($orientation = '', $format = '', $keepmargins = false, $blanks = -1)
            {
                // Call parent AddPage
                parent::AddPage($orientation, $format, $keepmargins, $blanks);

                // After first page, change top margin to accommodate header
                $this->pageCount++;
                if ($this->pageCount > 1) {
                    $this->SetTopMargin(10);
                    $this->setHeaderMargin(5);
                } else {
                    // First page: increase header margin to prevent overlap
                    $this->setHeaderMargin(1);
                }
            }

            public function Header()
            {
                // Store current Y position to calculate header height
                $startY = $this->GetY();

                // 1. Get the header HTML from document content
                $documentContent = $this->doc['content'];

                // 2. EXTRACT HEADER TABLE: Support multiple header table types (ssp-header-table, sop-header-table, etc.)
                // Match any table with id ending in "-header-table"
                if (preg_match('/(<table[^>]*id=["\']([a-z0-9]+-header-table)["\'][^>]*>.*?<\/table>)/si', $documentContent, $matches)) {
                    $headerTable = $matches[1];
                    $tableId = $matches[2]; // Store the table ID for reference
                } else {
                    return; // No header table found, skip header
                }

                // 3. Convert image URLs to absolute file paths for TCPDF
                $headerTable = $this->convertImageUrlsToPath($headerTable);

                // 4. Convert pt units to px for better TCPDF compatibility
                $headerTable = preg_replace('/(\d+(?:\.\d+)?)pt/', '${1}px', $headerTable);

                // 5. Add border="0" attribute to table if not present to disable TCPDF defaults
                if (strpos($headerTable, 'border=') === false) {
                    $headerTable = str_replace('<table', '<table border="0"', $headerTable);
                }

                // 6. Dynamic Replacements
                // Replace page numbers - use TCPDF aliases which will be replaced after all pages are generated
                // For Cambria font compatibility, we'll use helvetica for page numbers
                $headerTable = preg_replace_callback('/(<span[^>]*style="[^"]*font-family:\s*cambria[^"]*"[^>]*>)\d+\s+of\s+\d+(<\/span>)/i', function ($matches) {
                    // Replace cambria with helvetica for page numbers to avoid encoding issues
                    $openTag = str_replace('font-family: cambria', 'font-family: helvetica', $matches[1]);
                    // Use TCPDF's alias system for dynamic page numbers
                    return $openTag . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages() . $matches[2];
                }, $headerTable);

                // Replace document number if it exists in span with id="doc-number"
                if (!empty($this->doc['document_number'])) {
                    $headerTable = preg_replace(
                        '/(<span[^>]*id=["\']doc-number["\'][^>]*>)[^<]*(<\/span>)/i',
                        '$1' . $this->doc['document_number'] . '$2',
                        $headerTable
                    );
                }

                // 7. Set position and print ONLY the table
                // Position header at top of page with small margin
                $this->SetY(5);
                $this->writeHTML($headerTable, true, false, true, false, '');

                // Calculate header height for proper spacing
                $this->headerHeight = $this->GetY() - $startY + 5;
            }

            private function convertImageUrlsToPath($html)
            {
                // Convert image URLs to absolute file paths and handle alignment
                $html = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
                    $fullTag = $matches[0];
                    $beforeSrc = $matches[1];
                    $src = $matches[2];
                    $afterSrc = $matches[3];

                    $allAttributes = $beforeSrc . $afterSrc;

                    // Check if it's a relative URL from our uploads folder
                    if (strpos($src, 'uploads/documents/images/') !== false) {
                        // Extract just the filename
                        preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                        if (isset($fileMatches[1])) {
                            $filename = $fileMatches[1];
                            $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                            // Check if file exists
                            if (file_exists($absolutePath)) {
                                // Extract alignment from style attribute
                                $align = '';
                                if (preg_match('/style=["\']([^"\']*)["\']/', $allAttributes, $styleMatch)) {
                                    $style = $styleMatch[1];

                                    // Check for margin-left: auto; margin-right: auto (center)
                                    if (strpos($style, 'margin-left: auto') !== false && strpos($style, 'margin-right: auto') !== false) {
                                        $align = ' align="center"';
                                    }
                                    // Check for display: block with margin auto
                                    elseif (strpos($style, 'display: block') !== false && strpos($style, 'margin') !== false) {
                                        if (strpos($style, 'margin-left: auto') !== false || strpos($style, 'margin-right: auto') !== false) {
                                            $align = ' align="center"';
                                        }
                                    }
                                    // Check for float
                                    elseif (preg_match('/float:\s*(left|right)/', $style, $floatMatch)) {
                                        $align = ' align="' . $floatMatch[1] . '"';
                                    }
                                }

                                // Check for existing align attribute
                                if (empty($align) && preg_match('/align=["\']([^"\']+)["\']/', $allAttributes, $alignMatch)) {
                                    $align = ' align="' . $alignMatch[1] . '"';
                                }

                                return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . $align . '>';
                            }
                        }
                    }

                    return $fullTag;
                }, $html);

                return $html;
            }
        };

        // Configuration
        $pdf->SetCreator('DMS');
        // Set initial top margin for first page to accommodate header
        $pdf->SetMargins(15, 10, 15);
        $pdf->SetAutoPageBreak(TRUE, 15); // Reduce bottom margin
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);

        // Increase cell height ratio for better spacing and to help prevent row splitting
        $pdf->setCellHeightRatio(1.0);

        // Reduce spacing between paragraphs
        $pdf->setCellPaddings(0, 0, 0, 0); // Remove cell padding
        $pdf->setCellMargins(0, 0, 0, 0); // Remove cell margins

        // Add custom Cambria font from assets
        $cambriaTTFPath = FCPATH . 'assets/fonts/cambria.ttf';
        $cambriaBTTFPath = FCPATH . 'assets/fonts/cambriab.ttf';
        $cambriaITTFPath = FCPATH . 'assets/fonts/cambriai.ttf';
        $cambriaZTTFPath = FCPATH . 'assets/fonts/cambriaz.ttf';
        $tcpdfFontsDir = FCPATH . '../vendor/tecnickcom/tcpdf/fonts/';

        $fontName = 'cambria'; // Default fallback

        // Check if we already have a generated Cambria Regular font
        if (file_exists($tcpdfFontsDir . 'cambria.php')) {
            $fontName = 'cambria';
            error_log('Using existing Cambria Regular font definition');

            // Ensure italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriai.php') && file_exists($cambriaITTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaITTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria italic font error: ' . $e->getMessage());
                }
            }

            // Ensure bold variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriab.php') && file_exists($cambriaBTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaBTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold font error: ' . $e->getMessage());
                }
            }

            // Ensure bold italic variant is available
            if (!file_exists($tcpdfFontsDir . 'cambriaz.php') && file_exists($cambriaZTTFPath)) {
                try {
                    \TCPDF_FONTS::addTTFfont(
                        $cambriaZTTFPath,
                        'TrueTypeUnicode',
                        '',
                        32,
                        $tcpdfFontsDir
                    );
                    error_log('Cambria Bold Italic font successfully added');
                } catch (\Throwable $e) {
                    error_log('Cambria bold italic font error: ' . $e->getMessage());
                }
            }
        } elseif (file_exists($cambriaTTFPath)) {
            try {
                // Add Cambria Regular if not already present
                $addedFontName = \TCPDF_FONTS::addTTFfont(
                    $cambriaTTFPath,
                    'TrueTypeUnicode',
                    '',
                    32,
                    $tcpdfFontsDir
                );

                if ($addedFontName && $addedFontName !== false) {
                    $fontName = $addedFontName;
                    error_log('Cambria Regular font successfully added as: ' . $fontName);
                } else {
                    error_log('Failed to add Cambria Regular font - TCPDF returned false');
                }
            } catch (\Throwable $e) {
                error_log('Cambria Regular font error: ' . $e->getMessage());
            }
        }

        // Set font BEFORE adding page
        $pdf->SetFont($fontName, '', 10);

        $pdf->AddPage();

        // Clean body: Remove ALL header tables from the body content so they don't double-print
        // This removes any table with id ending in "-header-table" (ssp-header-table, sop-header-table, etc.)
        $bodyContent = $document['content'];
        $bodyContent = preg_replace_callback('/<(td|th)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Extract existing style
            $style = '';
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $style = $styleMatch[1];
                // Append padding/border but keep existing width
                if (strpos($style, 'padding') === false) {
                    $style .= '; padding: 2px 3px;';
                }
                if (strpos($style, 'border') === false) {
                    $style .= '; border: 1px solid #000;';
                }
                $attributes = str_replace($styleMatch[0], 'style="' . $style . '"', $attributes);
            } else {
                // No style attribute → add new one
                $attributes .= ' style="padding: 2px 3px; border: 1px solid #000;"';
            }

            return '<' . $tag . $attributes . '>';
        }, $bodyContent);


        // Format numbered points (e.g., "6.1 Text here") into two-column layout
        // This creates a left column for the number and right column for the text
        $bodyContent = preg_replace_callback('/<p([^>]*)>\s*(\d+\.\d+(?:\.\d+)?)\s+([^<]+)<\/p>/i', function ($matches) {
            $pAttrs = $matches[1];
            $number = $matches[2];
            $text = $matches[3];

            // Create a table-like structure using divs for proper alignment
            // Left column: fixed width for number, right column: flexible for text
            return '<table cellpadding="0" cellspacing="0" style="width: 100%; margin: 0px; padding: 0px;">
                <tr style="page-break-inside: avoid;">
                    <td style="width: 50px; padding: 0px 10px 0px 0px; text-align: right; vertical-align: top;">' . $number . '</td>
                    <td style="padding: 0px 0px 0px 10px; text-align: left; vertical-align: top;">' . $text . '</td>
                </tr>
            </table>';
        }, $bodyContent);

        // Replace font references with the actual font name returned by TCPDF FIRST
        // This ensures the font is properly set before we process other styles
        if ($fontName !== 'cambria') {
            // Cambria font was successfully added, replace all font references with it
            $bodyContent = preg_replace('/font-family:\s*["\']?cambria["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?calibri["\']?/i', 'font-family: ' . $fontName, $bodyContent);
            $bodyContent = preg_replace('/font-family:\s*["\']?arial["\']?/i', 'font-family: ' . $fontName, $bodyContent);
        } else {
            // Keep cambria in CSS so PDF viewers will request it
            // Don't replace - let the PDF viewer handle font substitution
        }

        // Replace <strong><em> with Cambria Bold Italic
        $bodyContent = preg_replace(
            '/<strong>\s*<em>(.*?)<\/em>\s*<\/strong>/i',
            '<span style="font-family: cambriaz;">$1</span>',
            $bodyContent
        );

        // Convert margin-left to non-breaking spaces for indentation in TCPDF
        // TCPDF doesn't render margin/padding well on inline elements, so we use &nbsp; for visual indentation
        // Handle both span and div tags with margin-left
        $bodyContent = preg_replace_callback('/<(span|div)([^>]*?)style="([^"]*?)margin-left:\s*(\d+)px([^"]*?)"([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $beforeStyle = $matches[2];
            $styleStart = $matches[3];
            $marginPx = $matches[4];
            $styleEnd = $matches[5];
            $afterStyle = $matches[6];

            // Calculate number of non-breaking spaces (roughly 1 space = 4px)
            $numSpaces = max(1, intval($marginPx / 4));
            $spaces = str_repeat('&nbsp;', $numSpaces);

            // Remove margin-left from style
            $newStyle = preg_replace('/margin-left:\s*\d+px;?\s*/i', '', $styleStart . $styleEnd);

            return '<' . $tag . $beforeStyle . 'style="' . $newStyle . '"' . $afterStyle . '>' . $spaces;
        }, $bodyContent);

        // Add nobr="true" to all tables to prevent row splitting across pages
        // Also add cellpadding for better readability
        $bodyContent = preg_replace_callback('/<table([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Add nobr="true" if not present
            if (strpos($attributes, 'nobr=') === false) {
                $attributes .= ' nobr="true"';
            }

            // Add cellpadding if not present
            if (strpos($attributes, 'cellpadding=') === false) {
                $attributes .= ' cellpadding="4"';
            }

            return '<table' . $attributes . '>';
        }, $bodyContent);

        // Add CSS style to table rows to keep them together
        $bodyContent = preg_replace('/<tr([^>]*)>/i', '<tr$1 style="page-break-inside: avoid;">', $bodyContent);

        // Add padding to table cells (td and th) for better readability while preserving width
        $bodyContent = preg_replace_callback('/<(td|th)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Extract existing width if present
            $width = '';
            if (preg_match('/width\s*=\s*["\']?([^"\'>\s]+)["\']?/i', $attributes, $widthMatch)) {
                $width = $widthMatch[1];
                // Remove width from attributes so we can add it to style
                $attributes = preg_replace('/\s*width\s*=\s*["\']?[^"\'>\s]+["\']?/i', '', $attributes);
            }

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add padding and width to existing style
                $newStyle = $existingStyle . '; padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute with padding and width
                $newStyle = 'padding: 2px 3px; border: 1px solid #000;';
                if ($width) {
                    $newStyle .= ' width: ' . $width . ';';
                }
                return '<' . $tag . $attributes . ' style="' . $newStyle . '">';
            }
        }, $bodyContent);

        // Reduce paragraph spacing by adding margin styles and line-height
        $bodyContent = preg_replace_callback('/<p([^>]*)>/i', function ($matches) {
            $attributes = $matches[1];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - increased line-height to 1.8 for better readability
                $newStyle = $existingStyle . '; margin: 0px; padding: 0px; line-height: 1;';
                return '<p' . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<p' . $attributes . ' style="margin: 0px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent); // Reduce spacing for headings (h1-h6) and divs - add negative margin-bottom to reduce gap before next section
        $bodyContent = preg_replace_callback('/<(h[1-6]|div)([^>]*)>/i', function ($matches) {
            $tag = $matches[1];
            $attributes = $matches[2];

            // Check if style attribute already exists
            if (preg_match('/style=["\']([^"\']*)["\']/', $attributes, $styleMatch)) {
                $existingStyle = $styleMatch[1];
                // Add margin and line-height to existing style - negative margin-bottom to reduce gap
                $newStyle = $existingStyle . '; margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;';
                return '<' . $tag . str_replace($styleMatch[0], 'style="' . $newStyle . '"', $attributes) . '>';
            } else {
                // Add new style attribute
                return '<' . $tag . $attributes . ' style="margin: 0px; margin-bottom: -3px; padding: 0px; line-height: 1;">';
            }
        }, $bodyContent);

        // STEP 1: Wrap ALL images in center div tags for consistent PDF rendering
        $bodyContent = preg_replace_callback('/<img([^>]*?)>/i', function ($matches) {
            $imgTag = $matches[0];
            return '<div align="center">' . $imgTag . '</div>';
        }, $bodyContent);

        // STEP 2: Convert image URLs to absolute paths
        $bodyContent = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
            $fullTag = $matches[0];
            $beforeSrc = $matches[1];
            $src = $matches[2];
            $afterSrc = $matches[3];

            // Check if it's a relative URL from our uploads folder
            if (strpos($src, 'uploads/documents/images/') !== false) {
                // Extract just the filename
                preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                if (isset($fileMatches[1])) {
                    $filename = $fileMatches[1];
                    $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;

                    // Check if file exists
                    if (file_exists($absolutePath)) {
                        return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . '>';
                    }
                }
            }

            return $fullTag;
        }, $bodyContent);

        $pdf->writeHTML($bodyContent, true, false, true, false, '');

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document['title']) . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    private function exportPdfDefault($document)
    {
        // Default PDF export for templates without specific implementations
        $pdf = new \TCPDF();

        $pdf->SetCreator('DMS');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set font
        $pdf->SetFont('helvetica', '', 11);
        $pdf->AddPage();

        // Add title
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(0, 10, $document['title'], 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Ln(5);

        // Add document content
        if (!empty($document['content'])) {
            $bodyContent = $document['content'];

            // Convert image URLs to absolute paths
            $bodyContent = preg_replace_callback('/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*?)>/i', function ($matches) {
                $fullTag = $matches[0];
                $beforeSrc = $matches[1];
                $src = $matches[2];
                $afterSrc = $matches[3];

                if (strpos($src, 'uploads/documents/images/') !== false) {
                    preg_match('/uploads\/documents\/images\/(.+)$/', $src, $fileMatches);
                    if (isset($fileMatches[1])) {
                        $filename = $fileMatches[1];
                        $absolutePath = FCPATH . 'uploads/documents/images/' . $filename;
                        if (file_exists($absolutePath)) {
                            return '<img' . $beforeSrc . 'src="' . $absolutePath . '"' . $afterSrc . '>';
                        }
                    }
                }
                return $fullTag;
            }, $bodyContent);

            $pdf->writeHTML($bodyContent, true, false, true, false, '');
        }

        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document['title']) . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }

    public function exportDoc($id)
    {
        // Redirect to preview page
        return redirect()->to('/documents/preview-doc/' . $id);
    }

    public function previewDoc($id)
    {
        $document = $this->db->table('documents')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        // Check access
        if (!$this->canAccessDocument($document, session()->get('user_id'))) {
            return redirect()->to('/documents')->with('error', 'Access denied');
        }

        // Fix image paths in content
        if (!empty($document['content'])) {
            $document['content'] = $this->fixImagePaths($document['content']);
        }

        // Get the original creator's information for "Prepared By"
        $userModel = new \App\Models\User();
        $creator = $userModel->find($document['created_by']);

        // Replace "Prepared By" information in document content
        if ($creator && !empty($document['content'])) {
            $document['content'] = $this->replacePreparedByInfo($document['content'], $creator);
        }

        // Replace "Checked By" and "Approved By" information
        if (!empty($document['content'])) {
            $document['content'] = $this->replaceApprovalInfo($document['content'], $document);
        }

        // Get document type and department
        $documentType = $this->db->table('document_types')->where('id', $document['type_id'])->get()->getRowArray();
        $department = $this->db->table('departments')->where('id', $document['department_id'])->get()->getRowArray();

        $data = [
            'document' => $document,
            'documentType' => $documentType,
            'department' => $department
        ];

        return view('documents/preview_doc', $data);
    }

    public function downloadDoc($id)
    {
        $document = $this->db->table('documents')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$document) {
            return redirect()->to('/documents')->with('error', 'Document not found');
        }

        // Check access
        if (!$this->canAccessDocument($document, session()->get('user_id'))) {
            return redirect()->to('/documents')->with('error', 'Access denied');
        }

        // Get document type and department
        $documentType = $this->db->table('document_types')->where('id', $document['type_id'])->get()->getRowArray();
        $department = $this->db->table('departments')->where('id', $document['department_id'])->get()->getRowArray();

        // Create PHPWord instance
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Add document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator('Document Management System');
        $properties->setTitle($document['title']);
        $properties->setSubject('Document Export');

        // Add a section
        $section = $phpWord->addSection([
            'marginLeft' => 1000,
            'marginRight' => 1000,
            'marginTop' => 1000,
            'marginBottom' => 1000,
        ]);

        // Title
        $section->addText(
            $document['title'],
            ['bold' => true, 'size' => 18, 'color' => '2c3e50'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER, 'spaceAfter' => 200]
        );

        $section->addLine(['weight' => 1, 'width' => 450, 'height' => 0, 'color' => '3498db']);
        $section->addTextBreak(1);

        // Metadata table
        $tableStyle = ['borderSize' => 6, 'borderColor' => 'cccccc', 'cellMargin' => 80];
        $phpWord->addTableStyle('MetadataTable', $tableStyle);
        $table = $section->addTable('MetadataTable');

        $table->addRow();
        $table->addCell(3000)->addText('Document Type:', ['bold' => true]);
        $table->addCell(6000)->addText($documentType['name'] ?? 'N/A');

        $table->addRow();
        $table->addCell(3000)->addText('Department:', ['bold' => true]);
        $table->addCell(6000)->addText($department['name'] ?? 'N/A');

        $table->addRow();
        $table->addCell(3000)->addText('Status:', ['bold' => true]);
        $table->addCell(6000)->addText(ucfirst($document['status']));

        if (isset($document['version'])) {
            $table->addRow();
            $table->addCell(3000)->addText('Version:', ['bold' => true]);
            $table->addCell(6000)->addText($document['version']);
        }

        $table->addRow();
        $table->addCell(3000)->addText('Created:', ['bold' => true]);
        $table->addCell(6000)->addText(date('F d, Y', strtotime($document['created_at'])));

        if ($document['effective_date']) {
            $table->addRow();
            $table->addCell(3000)->addText('Effective Date:', ['bold' => true]);
            $table->addCell(6000)->addText(date('F d, Y', strtotime($document['effective_date'])));
        }

        $section->addTextBreak(2);
        $section->addLine(['weight' => 1, 'width' => 450, 'height' => 0]);
        $section->addTextBreak(1);

        // Add content - convert HTML to Word format
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, $document['content'], false, false);

        // Save file
        $version = isset($document['version']) ? '_v' . $document['version'] : '';
        $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $document['title']) . $version . '.docx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('php://output');
        exit;
    }

    // public function uploadImage()
    // {
    //     $file = $this->request->getFile('upload');
    //     if (!$file || !$file->isValid()) {
    //         return $this->response->setJSON(['error' => $file ? $file->getErrorString() : 'No file uploaded'])->setStatusCode(500);
    //     }
    //     $validTypes = ['jpg', 'jpeg', 'png', 'gif'];
    //     if (!in_array($file->getExtension(), $validTypes)) {
    //         return $this->response->setJSON(['error' => 'Invalid file type']);
    //     }
    //     $newName = $file->getRandomName();
    //     $file->move(FCPATH . 'uploads', $newName);
    //     return $this->response->setJSON(['location' => base_url('uploads/' . $newName)]);
    // }
}
