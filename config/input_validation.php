<?php
/**
 * Input Validation Helpers for SIMAKS
 * 
 * CRITICAL SECURITY: Validates and sanitizes all user input
 * Prevents injection attacks, XSS, and invalid data
 */

class InputValidator {
    /**
     * Validate and sanitize integer ID
     * 
     * @param mixed $input Input value
     * @param int $min Minimum allowed value
     * @param int $max Maximum allowed value
     * @return int Validated ID
     * @throws Exception If validation fails
     */
    public static function validateId($input, $min = 1, $max = 999999) {
        $id = filter_var($input, FILTER_VALIDATE_INT);
        
        if ($id === false || $id < $min || $id > $max) {
            throw new Exception("Invalid ID parameter. Expected integer between $min and $max.");
        }
        
        return $id;
    }
    
    /**
     * Validate string input
     * 
     * @param mixed $input Input value
     * @param int $max_length Maximum string length
     * @param bool $allow_html Allow HTML tags (default: false)
     * @return string Validated and sanitized string
     * @throws Exception If validation fails
     */
    public static function validateString($input, $max_length = 255, $allow_html = false) {
        // Convert to string and trim
        $str = trim((string)$input);
        
        // Check length
        if (strlen($str) > $max_length) {
            throw new Exception("String too long. Maximum length: $max_length characters.");
        }
        
        // Sanitize HTML if not allowed
        if (!$allow_html) {
            $str = htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        } else {
            // If HTML allowed, use strip_tags with safe tags
            $safe_tags = '<p><br><strong><em><u><ul><ol><li><a><img>';
            $str = strip_tags($str, $safe_tags);
        }
        
        return $str;
    }
    
    /**
     * Validate date format (Y-m-d)
     * 
     * @param mixed $input Date string
     * @return string Validated date
     * @throws Exception If validation fails
     */
    public static function validateDate($input) {
        $date = DateTime::createFromFormat('Y-m-d', $input);
        
        if (!$date || $date->format('Y-m-d') !== $input) {
            throw new Exception("Invalid date format. Expected: YYYY-MM-DD");
        }
        
        return $input;
    }
    
    /**
     * Validate enum value (from allowed list)
     * 
     * @param mixed $input Input value
     * @param array $allowed_values List of allowed values
     * @param bool $strict Use strict comparison
     * @return mixed Validated value
     * @throws Exception If validation fails
     */
    public static function validateEnum($input, array $allowed_values, $strict = true) {
        if (!in_array($input, $allowed_values, $strict)) {
            $allowed_str = implode(', ', $allowed_values);
            throw new Exception("Invalid value. Allowed values: $allowed_str");
        }
        
        return $input;
    }
    
    /**
     * Validate email address
     * 
     * @param mixed $input Email string
     * @return string Validated email
     * @throws Exception If validation fails
     */
    public static function validateEmail($input) {
        $email = filter_var($input, FILTER_VALIDATE_EMAIL);
        
        if ($email === false) {
            throw new Exception("Invalid email address format.");
        }
        
        return $email;
    }
    
    /**
     * Validate phone number (Indonesian format)
     * 
     * @param mixed $input Phone number
     * @return string Validated phone number
     * @throws Exception If validation fails
     */
    public static function validatePhone($input) {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9+]/', '', $input);
        
        // Check length (8-15 digits)
        if (strlen($phone) < 8 || strlen($phone) > 15) {
            throw new Exception("Invalid phone number. Must be 8-15 digits.");
        }
        
        return $phone;
    }
    
    /**
     * Validate URL
     * 
     * @param mixed $input URL string
     * @return string Validated URL
     * @throws Exception If validation fails
     */
    public static function validateUrl($input) {
        $url = filter_var($input, FILTER_VALIDATE_URL);
        
        if ($url === false) {
            throw new Exception("Invalid URL format.");
        }
        
        return $url;
    }
    
    /**
     * Validate and sanitize array of IDs
     * 
     * @param array $input Array of IDs
     * @param int $min Minimum ID value
     * @param int $max Maximum ID value
     * @return array Validated array of IDs
     * @throws Exception If validation fails
     */
    public static function validateIdArray($input, $min = 1, $max = 999999) {
        if (!is_array($input)) {
            throw new Exception("Expected array of IDs.");
        }
        
        $validated = [];
        
        foreach ($input as $id) {
            $validated[] = self::validateId($id, $min, $max);
        }
        
        return $validated;
    }
    
    /**
     * Validate boolean value
     * 
     * @param mixed $input Input value
     * @return bool Validated boolean
     */
    public static function validateBoolean($input) {
        return filter_var($input, FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Validate integer with range
     * 
     * @param mixed $input Input value
     * @param int|null $min Minimum value (null = no limit)
     * @param int|null $max Maximum value (null = no limit)
     * @return int Validated integer
     * @throws Exception If validation fails
     */
    public static function validateInt($input, $min = null, $max = null) {
        $int = filter_var($input, FILTER_VALIDATE_INT);
        
        if ($int === false) {
            throw new Exception("Invalid integer value.");
        }
        
        if ($min !== null && $int < $min) {
            throw new Exception("Value too small. Minimum: $min");
        }
        
        if ($max !== null && $int > $max) {
            throw new Exception("Value too large. Maximum: $max");
        }
        
        return $int;
    }
    
    /**
     * Sanitize filename for safe storage
     * 
     * @param string $filename Original filename
     * @return string Safe filename
     */
    public static function sanitizeFilename($filename) {
        // Get file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Remove dangerous characters
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $ext = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
        
        // Limit length
        $name = substr($name, 0, 100);
        
        return $name . '.' . strtolower($ext);
    }
}
