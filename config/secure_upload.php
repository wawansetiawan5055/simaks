<?php
/**
 * Secure File Upload Helper
 * 
 * CRITICAL SECURITY: Validates file uploads to prevent malicious file execution
 * 
 * Usage:
 * try {
 *     $filename = SecureFileUpload::upload($_FILES['photo'], '../public/uploads/guru', 'image');
 *     // Save $filename to database
 * } catch (Exception $e) {
 *     // Handle error
 * }
 */

class SecureFileUpload {
    // Allowed extensions (whitelist)
    private static $allowed_images = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private static $allowed_docs = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    
    // Max file sizes (bytes)
    const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB
    const MAX_DOC_SIZE = 10 * 1024 * 1024;   // 10MB
    
    /**
     * Upload file with comprehensive security validation
     * 
     * @param array $file $_FILES['field_name']
     * @param string $upload_dir Target directory (without trailing slash)
     * @param string $type 'image' or 'document'
     * @return string Uploaded filename (hashed)
     * @throws Exception On validation failure
     */
    public static function upload($file, $upload_dir, $type = 'image') {
        // 1. Check for upload errors
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new Exception("Invalid file upload parameters.");
        }
        
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception("File too large.");
            case UPLOAD_ERR_NO_FILE:
                throw new Exception("No file uploaded.");
            default:
                throw new Exception("Upload error occurred.");
        }
        
        // 2. Validate file size
        $max_size = ($type === 'image') ? self::MAX_IMAGE_SIZE : self::MAX_DOC_SIZE;
        if ($file['size'] > $max_size) {
            $max_mb = $max_size / 1024 / 1024;
            throw new Exception("File too large. Maximum: {$max_mb}MB");
        }
        
        // 3. Get and validate file extension
        $original_name = basename($file['name']);
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        
        $allowed = ($type === 'image') ? self::$allowed_images : self::$allowed_docs;
        if (!in_array($ext, $allowed)) {
            throw new Exception("Invalid file type. Allowed: " . implode(', ', $allowed));
        }
        
        // 4. Validate MIME type (prevent extension spoofing)
        if ($type === 'image') {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            $allowed_mimes = [
                'image/jpeg',
                'image/pjpeg', // Progressive JPEG
                'image/png',
                'image/gif',
                'image/webp'
            ];
            
            if (!in_array($mime, $allowed_mimes)) {
                throw new Exception("Invalid image file. MIME type mismatch.");
            }
            
            // Additional check: verify image can be loaded
            $img_check = @getimagesize($file['tmp_name']);
            if ($img_check === false) {
                throw new Exception("Invalid or corrupted image file.");
            }
        }
        
        // 5. Generate secure random filename
        $new_filename = bin2hex(random_bytes(16)) . '_' . time() . '.' . $ext;
        $target_path = $upload_dir . '/' . $new_filename;
        
        // 6. Ensure upload directory exists
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                throw new Exception("Failed to create upload directory.");
            }
        }
        
        // 7. Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $target_path)) {
            throw new Exception("Failed to save uploaded file.");
        }
        
        // 8. Set proper file permissions
        chmod($target_path, 0644);
        
        return $new_filename;
    }
    
    /**
     * Delete uploaded file securely
     * 
     * @param string $filepath Full path to file
     * @return bool Success status
     */
    public static function delete($filepath) {
        if (file_exists($filepath) && is_file($filepath)) {
            return @unlink($filepath);
        }
        return false;
    }
    
    /**
     * Get human-readable file size
     * 
     * @param int $bytes File size in bytes
     * @return string Formatted size
     */
    public static function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
