<?php
/**
 * Rate Limiting for Login Attempts
 * 
 * CRITICAL SECURITY: Prevents brute force attacks
 * Limits login attempts to 5 per 15 minutes per IP address
 */

class RateLimiter {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }
    
    /**
     * Create login_attempts table if not exists
     */
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT PRIMARY KEY AUTO_INCREMENT,
            ip_address VARCHAR(45) NOT NULL,
            username VARCHAR(100),
            attempt_time DATETIME NOT NULL,
            INDEX idx_ip_time (ip_address, attempt_time),
            INDEX idx_username (username),
            INDEX idx_cleanup (attempt_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            // Table might already exist, ignore
        }
    }
    
    /**
     * Check if IP address is blocked (too many attempts)
     * 
     * @param string $ip IP address to check
     * @param string|null $username Optional username
     * @return bool True if blocked, false if allowed
     */
    public function isBlocked($ip, $username = null) {
        // Clean old attempts first (older than 15 minutes)
        $this->cleanOldAttempts();
        
        // Count attempts in last 15 minutes for this IP
        $sql = "SELECT COUNT(*) FROM login_attempts 
                WHERE ip_address = ? 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ip]);
        $count = $stmt->fetchColumn();
        
        // Block after 5 failed attempts
        return $count >= 5;
    }
    
    /**
     * Get remaining attempts before block
     * 
     * @param string $ip IP address
     * @return int Remaining attempts
     */
    public function getRemainingAttempts($ip) {
        $sql = "SELECT COUNT(*) FROM login_attempts 
                WHERE ip_address = ? 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ip]);
        $count = $stmt->fetchColumn();
        
        $max_attempts = 5;
        $remaining = $max_attempts - $count;
        
        return max(0, $remaining);
    }
    
    /**
     * Get time until block expires
     * 
     * @param string $ip IP address
     * @return int|null Seconds until unblock, or null if not blocked
     */
    public function getTimeUntilUnblock($ip) {
        $sql = "SELECT MAX(attempt_time) as last_attempt 
                FROM login_attempts 
                WHERE ip_address = ? 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ip]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result || !$result['last_attempt']) {
            return null;
        }
        
        $last_attempt = strtotime($result['last_attempt']);
        $unblock_time = $last_attempt + (15 * 60); // 15 minutes
        $now = time();
        
        $seconds_remaining = $unblock_time - $now;
        
        return max(0, $seconds_remaining);
    }
    
    /**
     * Record a failed login attempt
     * 
     * @param string $ip IP address
     * @param string|null $username Username attempted
     */
    public function recordAttempt($ip, $username = null) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO login_attempts (ip_address, username, attempt_time) 
             VALUES (?, ?, NOW())"
        );
        $stmt->execute([$ip, $username]);
    }
    
    /**
     * Clear all attempts for an IP (after successful login)
     * 
     * @param string $ip IP address
     */
    public function clearAttempts($ip) {
        $stmt = $this->pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
    }
    
    /**
     * Clean attempts older than 15 minutes
     */
    private function cleanOldAttempts() {
        $this->pdo->exec(
            "DELETE FROM login_attempts 
             WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
        );
    }
    
    /**
     * Get all attempts for an IP (for logging/debugging)
     * 
     * @param string $ip IP address
     * @return array Array of attempts
     */
    public function getAttempts($ip) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM login_attempts 
             WHERE ip_address = ? 
             AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
             ORDER BY attempt_time DESC"
        );
        $stmt->execute([$ip]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
