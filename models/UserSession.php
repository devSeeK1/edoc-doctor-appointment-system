<?php
/**
 * User session management
 */

class UserSession {
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION["user"]) && $_SESSION["user"] != "" && $_SESSION['usertype'] != '';
    }
    
    /**
     * Get current user email
     */
    public static function getUserEmail() {
        return $_SESSION["user"] ?? null;
    }
    
    /**
     * Get current user type
     */
    public static function getUserType() {
        return $_SESSION["usertype"] ?? null;
    }
    
    /**
     * Redirect user based on their type
     */
    public static function redirectByUserType() {
        if (!self::isLoggedIn()) {
            header("location: ../login.php");
            exit();
        }
        
        $userType = self::getUserType();
        switch ($userType) {
            case 'p':
                header("location: patient/index.php");
                break;
            case 'a':
                header("location: admin/index.php");
                break;
            case 'd':
                header("location: doctor/index.php");
                break;
            default:
                header("location: login.php");
        }
        exit();
    }
    
    /**
     * Require specific user type
     */
    public static function requireUserType($requiredType) {
        if (!self::isLoggedIn() || self::getUserType() != $requiredType) {
            header("location: ../login.php");
            exit();
        }
    }
}