<?php
/**
 * CSRF Protection for SIMAKS
 * 
 * CRITICAL SECURITY: Protects against Cross-Site Request Forgery attacks
 * 
 * Usage:
 * 1. Include this file in index.php
 * 2. Add csrf_field() in all forms
 * 3. Call csrf_verify() in all POST handlers
 */

if (!function_exists('csrf_token')) {
    /**
     * Generate or retrieve CSRF token for current session
     * 
     * @return string CSRF token
     */
    function csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            // Generate cryptographically secure random token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Generate hidden input field with CSRF token
     * 
     * @return string HTML hidden input
     */
    function csrf_field() {
        $token = csrf_token();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('csrf_verify')) {
    /**
     * Verify CSRF token from POST request
     * 
     * @return bool True if valid, false otherwise
     */
    function csrf_verify() {
        // Get token from POST
        $token = $_POST['csrf_token'] ?? '';
        
        // Get session token
        $session_token = $_SESSION['csrf_token'] ?? '';
        
        // Both must exist
        if (empty($token) || empty($session_token)) {
            return false;
        }
        
        // Use hash_equals to prevent timing attacks
        return hash_equals($session_token, $token);
    }
}

if (!function_exists('csrf_verify_or_die')) {
    /**
     * Verify CSRF token or terminate request
     * 
     * @param string $redirect_url URL to redirect on failure
     */
    function csrf_verify_or_die($redirect_url = 'index.php') {
        if (!csrf_verify()) {
            $_SESSION['pesan_error'] = "Invalid security token. Please try again.";
            redirect($redirect_url);
            exit;
        }
    }
}

if (!function_exists('csrf_regenerate')) {
    /**
     * Regenerate CSRF token (call after login/logout)
     */
    function csrf_regenerate() {
        unset($_SESSION['csrf_token']);
        csrf_token(); // Generate new one
    }
}
