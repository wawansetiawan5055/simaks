<?php
/**
 * Security Headers Configuration
 * 
 * CRITICAL SECURITY: Protect against common web vulnerabilities
 * Include this file BEFORE any output in index.php
 */

// Prevent clickjacking attacks
header("X-Frame-Options: SAMEORIGIN");

// Prevent MIME type sniffing
header("X-Content-Type-Options: nosniff");

// Enable XSS protection (legacy browsers)
header("X-XSS-Protection: 1; mode=block");

// Control referrer information
header("Referrer-Policy: strict-origin-when-cross-origin");

// Content Security Policy (CSP)
// TEMPORARILY DISABLED - causing CDN blocking issues
// Re-enable after testing all external resources
// $csp = [
//     "default-src 'self'",
//     "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com",
//     "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
//     "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
//     "img-src 'self' data: https:",
//     "connect-src 'self'",
//     "frame-ancestors 'self'",
// ];
// header("Content-Security-Policy: " . implode("; ", $csp));

// Prevent browser caching of sensitive pages
// Only for authenticated pages
if (isset($_SESSION['user_id'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
}

// Strict Transport Security (HSTS) - Only enable if using HTTPS
// Uncomment when deployed with SSL certificate
// if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
//     header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
// }

// Remove server signature
header_remove("X-Powered-By");

// NOTE: Session cookie parameters are set in index.php BEFORE session_start()
// Cannot be changed here as session is already active
