<?php
// Detailed login test

include("connection.php");
include("models/Utils.php");

session_start();

// Clear any existing session data
$_SESSION = array();

// Test credentials
$email = "baby123@edoc.com";
$password = "123456";

echo "Testing login for: $email\n";

// Get user type
$stmt = $database->prepare("SELECT * FROM webuser WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows==1){
    echo "Found user in webuser table\n";
    $utype=$result->fetch_assoc()['usertype'];
    echo "User type: $utype\n";
    
    if ($utype=='p'){
        // Patient login
        $stmt = $database->prepare("SELECT * FROM patient WHERE pemail=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $checker = $stmt->get_result();
        
        if ($checker->num_rows==1){
            $user = $checker->fetch_assoc();
            echo "Found patient user\n";
            echo "Stored password: " . $user['ppassword'] . "\n";
            
            // Check if password is hashed
            $is_hashed = !empty($user['ppassword']) && 
                         password_get_info($user['ppassword'])['algo'] > 0;
            
            echo "Is password hashed: " . ($is_hashed ? "Yes" : "No") . "\n";
            
            if ($is_hashed) {
                $password_valid = password_verify($password, $user['ppassword']);
                echo "Password verification result (hashed): " . ($password_valid ? "Valid" : "Invalid") . "\n";
            } else {
                $password_valid = ($password === $user['ppassword']);
                echo "Password verification result (plain): " . ($password_valid ? "Valid" : "Invalid") . "\n";
            }
            
            if($password_valid) {
                echo "Setting session variables...\n";
                $_SESSION['user']=$email;
                $_SESSION['usertype']='p';
                
                echo "Session variables set:\n";
                echo "User: " . (isset($_SESSION['user']) ? $_SESSION['user'] : 'Not set') . "\n";
                echo "Usertype: " . (isset($_SESSION['usertype']) ? $_SESSION['usertype'] : 'Not set') . "\n";
                
                echo "Attempting redirect to patient/index.php...\n";
                header('Location: patient/index.php');
                exit();
            } else {
                echo "Invalid password\n";
            }
        } else {
            echo "Patient not found in patient table\n";
        }
    }
} else {
    echo "User not found in webuser table\n";
}
?>