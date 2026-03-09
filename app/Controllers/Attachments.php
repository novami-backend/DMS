<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DocumentAttachment;
use CodeIgniter\HTTP\ResponseInterface;

class Attachments extends BaseController
{
    public function index()
    {
        //
    }

    public function delete($id)
    {
        $attachmentModel = new DocumentAttachment();
        $attachment = $attachmentModel->find($id);

        if (!$attachment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Attachment not found']);
        }

        // Delete the physical file
        if (file_exists(FCPATH . $attachment['file_path'])) {
            unlink(FCPATH . $attachment['file_path']);
        }

        // Delete from DB
        $attachmentModel->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Attachment deleted successfully']);
    }
}
