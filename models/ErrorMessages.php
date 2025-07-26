<?php
/**
 * Error messages management
 */

// Include configuration
include_once("../config.php");

class ErrorMessages {
    private static $messages = [
        '1' => 'Ushbu elektron pochta manzili uchun akkaunt allaqachon mavjud',
        '2' => 'Parol tasdiqlashda xatolik yuz berdi! Reconform Password',
        '3' => '',
        '4' => '',
        '5' => 'Parol kamida ' . MIN_PASSWORD_LENGTH . ' ta belgidan iborat bo`lishi kerak',
        '6' => 'Invalid request. Please try again.',
    ];
    
    /**
     * Get error message by code
     */
    public static function get($code) {
        return self::$messages[$code] ?? '';
    }
    
    /**
     * Check if error code exists
     */
    public static function exists($code) {
        return isset(self::$messages[$code]);
    }
    
    /**
     * Format error message for display
     */
    public static function format($code) {
        if (!self::exists($code)) {
            return '';
        }
        
        $message = self::get($code);
        if (empty($message)) {
            return '';
        }
        
        return '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">' . $message . '</label>';
    }
}