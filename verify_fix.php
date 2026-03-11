<?php
// Set up paths
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('ENVIRONMENT', 'development');

require __DIR__ . '/app/Config/Paths.php';
$paths = new Config\Paths();

// Manual bootstrap
require_once $paths->systemDirectory . '/Config/DotEnv.php';
(new \CodeIgniter\Config\DotEnv($paths->appDirectory . '/../'))->load();

define('APPPATH', realpath(rtrim($paths->appDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
define('ROOTPATH', realpath(APPPATH . '../') . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', realpath(rtrim($paths->systemDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
define('WRITEPATH', realpath(rtrim($paths->writableDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);

require_once APPPATH . 'Config/Constants.php';
if (file_exists(APPPATH . 'Common.php')) {
    require_once APPPATH . 'Common.php';
}
require_once SYSTEMPATH . 'Common.php';

require_once SYSTEMPATH . 'Config/AutoloadConfig.php';
require_once APPPATH . 'Config/Autoload.php';
require_once SYSTEMPATH . 'Modules/Modules.php';
require_once APPPATH . 'Config/Modules.php';
require_once SYSTEMPATH . 'Autoloader/Autoloader.php';
require_once SYSTEMPATH . 'Config/BaseService.php';
require_once SYSTEMPATH . 'Config/Services.php';
require_once APPPATH . 'Config/Services.php';

\Config\Services::autoloader()->initialize(new \Config\Autoload(), new \Config\Modules())->register();
\Config\Services::autoloader()->loadHelpers();

// Get the model
$documentModel = new \App\Models\Document();
$db = \Config\Database::connect();

// Try to find an existing user or use ID 1
$userId = 1; 
$reviewerId = 1; 

echo "Testing Approval History Logging...\n";

// 1. Create test document
$docId = $documentModel->insert([
    'title' => 'Verification Document ' . time(),
    'content' => 'Testing history trails',
    'created_by' => $userId,
    'department_id' => 1,
    'type_id' => 1,
    'status' => 'draft',
    'approval_status' => 'pending'
], true); // returnID = true

if (!$docId) {
    echo "Failed to create document.\n";
    exit(1);
}

echo "Created Document ID: $docId\n";

// Helper to check history
function checkHistory($db, $docId, $expectedAction) {
    $row = $db->table('document_approval_history')
              ->where('document_id', $docId)
              ->where('action', $expectedAction)
              ->get()
              ->getRow();
    
    if ($row) {
        echo "[SUCCESS] Found history for action: $expectedAction (Status: {$row->new_status})\n";
    } else {
        echo "[FAILURE] Missing history for action: $expectedAction\n";
    }
}

// 2. Submit for review
$documentModel->submitForReview($docId, $reviewerId, $userId);
checkHistory($db, $docId, 'submitted_for_review');

// 3. Review
$documentModel->reviewDocument($docId, 'approve_for_final', 'Approved by reviewer', $reviewerId);
checkHistory($db, $docId, 'approve_for_final');

// 4. Admin Final Approve
$documentModel->adminApprove($docId, 'final_approve', 'Admin says OK', $userId);
checkHistory($db, $docId, 'final_approve');

// 5. Resubmit test
$documentModel->update($docId, ['approval_status' => 'returned_for_revision']);
$documentModel->resubmitDocument($docId, $userId);
checkHistory($db, $docId, 'resubmitted_after_revision');

echo "Test Complete.\n";

// Cleanup before exit
$db->table('document_approval_history')->where('document_id', $docId)->delete();
$documentModel->delete($docId, true);
echo "Cleaned up.\n";
