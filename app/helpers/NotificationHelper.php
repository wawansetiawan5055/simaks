<?php
/**
 * Notification Helper Class
 * Provides consistent notification methods for controllers
 * 
 * Usage:
 *   NotificationHelper::success('Data berhasil disimpan');
 *   NotificationHelper::error('Terjadi kesalahan');
 *   NotificationHelper::warning('Perhatian!');
 *   NotificationHelper::info('Informasi penting');
 * 
 * @package SIMAKS
 * @subpackage Helpers
 */

class NotificationHelper {
    
    /**
     * Show success notification
     * @param string $message The success message to display
     */
    public static function success($message) {
        $_SESSION['pesan_sukses'] = $message;
    }
    
    /**
     * Show error notification
     * @param string $message The error message to display
     */
    public static function error($message) {
        $_SESSION['pesan_error'] = $message;
    }
    
    /**
     * Show warning notification
     * @param string $message The warning message to display
     */
    public static function warning($message) {
        $_SESSION['pesan_warning'] = $message;
    }
    
    /**
     * Show info notification
     * @param string $message The info message to display
     */
    public static function info($message) {
        $_SESSION['pesan_info'] = $message;
    }
    
    /**
     * Clear all notification messages from session
     */
    public static function clear() {
        unset($_SESSION['pesan_sukses']);
        unset($_SESSION['pesan_error']);
        unset($_SESSION['pesan_warning']);
        unset($_SESSION['pesan_info']);
    }
    
    /**
     * Check if there are any notification messages
     * @return bool True if there are messages, false otherwise
     */
    public static function hasMessages() {
        return isset($_SESSION['pesan_sukses']) 
            || isset($_SESSION['pesan_error'])
            || isset($_SESSION['pesan_warning'])
            || isset($_SESSION['pesan_info']);
    }
    
    /**
     * Get all notification messages and clear them from session
     * @return array Associative array with message types as keys
     */
    public static function getAndClear() {
        $messages = [
            'success' => isset($_SESSION['pesan_sukses']) ? $_SESSION['pesan_sukses'] : null,
            'error' => isset($_SESSION['pesan_error']) ? $_SESSION['pesan_error'] : null,
            'warning' => isset($_SESSION['pesan_warning']) ? $_SESSION['pesan_warning'] : null,
            'info' => isset($_SESSION['pesan_info']) ? $_SESSION['pesan_info'] : null,
        ];
        
        self::clear();
        
        return $messages;
    }
}
