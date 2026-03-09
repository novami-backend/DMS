<?php

/**
 * Attachment Helper Functions
 */

if (!function_exists('uploadDocumentAttachment')) {
    /**
     * Upload a file and return the path
     * 
     * @param \CodeIgniter\HTTP\Files\UploadedFile $file
     * @param string $documentId
     * @return array|false Returns array with 'path', 'name', 'size', 'type' or false on failure
     */
    function uploadDocumentAttachment($file, $documentId)
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return false;
        }

        $uploadDir = FCPATH . 'uploads/documents/attachments/' . $documentId;
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $newName = $file->getRandomName();
        
        if ($file->move($uploadDir, $newName)) {
            return [
                'path' => 'uploads/documents/attachments/' . $documentId . '/' . $newName,
                'name' => $file->getClientName(),
                'size' => $file->getSize(),
                'type' => $file->getClientMimeType() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION)
            ];
        }

        return false;
    }
}

if (!function_exists('deleteDocumentAttachmentFile')) {
    /**
     * Delete a physical attachment file
     * 
     * @param string $filePath
     * @return bool
     */
    function deleteDocumentAttachmentFile($filePath)
    {
        $fullPath = FCPATH . $filePath;
        
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}

if (!function_exists('getAttachmentExtension')) {
    /**
     * Get file extension from file path
     * 
     * @param string $filePath
     * @return string
     */
    function getAttachmentExtension($filePath)
    {
        return strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    }
}

if (!function_exists('isImageAttachment')) {
    /**
     * Check if attachment is an image
     * 
     * @param string $filePath
     * @return bool
     */
    function isImageAttachment($filePath)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = getAttachmentExtension($filePath);
        
        return in_array($extension, $imageExtensions);
    }
}

if (!function_exists('isPdfAttachment')) {
    /**
     * Check if attachment is a PDF
     * 
     * @param string $filePath
     * @return bool
     */
    function isPdfAttachment($filePath)
    {
        return getAttachmentExtension($filePath) === 'pdf';
    }
}
