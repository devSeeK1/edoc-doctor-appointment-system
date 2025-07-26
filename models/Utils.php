<?php
/**
 * Utility functions
 */

class Utils {
    /**
     * Sanitize and validate email
     */
    public static function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Sanitize phone number (remove non-digits)
     */
    public static function sanitizePhone($phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }
    
    /**
     * Escape HTML special characters
     */
    public static function escapeHtml($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}