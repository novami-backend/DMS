<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Upload extends BaseController
{
    public function image()
    {
        $file = $this->request->getFile('upload'); // CKEditor sends field as "upload"

        if (!$file->isValid()) {
            return $this->response->setJSON([
                'error' => ['message' => $file->getErrorString()]
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Move file to writable/uploads
        $newName = $file->getRandomName();
        $file->move(FCPATH . 'uploads', $newName);

        // Return JSON with file URL
        return $this->response->setJSON([
            'url' => base_url('uploads/' . $newName)
        ]);
    }
}
