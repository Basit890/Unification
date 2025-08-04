<?php
class FileUpload {
    
    private $uploadDir;
    private $allowedTypes;
    private $maxSize;
    
    public function __construct($uploadDir = null, $maxSize = 5242880) {
        if ($uploadDir === null) {
            $this->uploadDir = __DIR__ . '/../uploads/';
        } else {
            $this->uploadDir = $uploadDir;
        }
        $this->maxSize = $maxSize;
        $this->ensureUploadDirectory();
    }
    
    private function ensureUploadDirectory() {
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                throw new Exception('Failed to create upload directory: ' . $this->uploadDir);
            }
        }
        
        if (!is_writable($this->uploadDir)) {
            throw new Exception('Upload directory is not writable: ' . $this->uploadDir);
        }
    }
    
    public function uploadImage($file) {
        $this->allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        return $this->uploadFile($file, 'image');
    }
    
    public function uploadDocument($file) {
        $this->allowedTypes = ['pdf', 'doc', 'docx'];
        return $this->uploadFile($file, 'document');
    }
    
    private function uploadFile($file, $type) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'error' => 'No file uploaded'];
        }
        
        if (!is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'error' => 'File upload validation failed'];
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
            ];
            $errorMessage = $errorMessages[$file['error']] ?? 'Unknown upload error';
            return ['success' => false, 'error' => 'Upload error: ' . $errorMessage];
        }
        
        if ($file['size'] > $this->maxSize) {
            return ['success' => false, 'error' => 'File too large. Maximum size: ' . ($this->maxSize / 1024 / 1024) . 'MB'];
        }
        
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $this->allowedTypes)) {
            return ['success' => false, 'error' => 'Invalid file type. Allowed: ' . implode(', ', $this->allowedTypes)];
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!isset($allowedMimes[$fileExtension]) || $allowedMimes[$fileExtension] !== $mimeType) {
            return ['success' => false, 'error' => 'Invalid MIME type: ' . $mimeType];
        }
        
        if (!is_dir($this->uploadDir)) {
            if (!mkdir($this->uploadDir, 0755, true)) {
                return ['success' => false, 'error' => 'Failed to create upload directory'];
            }
        }
        
        if (!is_writable($this->uploadDir)) {
            return ['success' => false, 'error' => 'Upload directory is not writable'];
        }
        
        $filename = uniqid() . '_' . time() . '.' . $fileExtension;
        $filepath = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath,
                'original_name' => $file['name']
            ];
        } else {
            $error = error_get_last();
            return ['success' => false, 'error' => 'Failed to move uploaded file: ' . ($error['message'] ?? 'Unknown error')];
        }
    }
    
    public function deleteFile($filepath) {
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
    
    public function getFileUrl($filepath) {
        if (empty($filepath)) {
            return null;
        }
        
        $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $filepath);
        return $relativePath ?: $filepath;
    }
} 