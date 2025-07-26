<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - eDoc</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .form-container {
            animation: fadeInUp 0.5s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <?php

    //learn from w3schools.com
    //Unset all the server side variables

    session_start();

    $_SESSION["user"]="";
    $_SESSION["usertype"]="";
    
    // Import configuration
    include("config.php");
    
    // Set the new timezone
    date_default_timezone_set(TIMEZONE);
    $date = date('Y-m-d');

    $_SESSION["date"]=$date;
    
    //import database
    include("connection.php");
    include("csrf.php");
    include("models/Utils.php");

    if($_POST){
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid request. Please try again.</label>';
        } else {

            $email = Utils::sanitizeEmail($_POST['useremail']);
            $password = $_POST['userpassword'];
            
            $error='<label for="promter" class="form-label"></label>';

            // Validate email
            if (!Utils::validateEmail($email)) {
                $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid email format.</label>';
            } else {
                $stmt = $database->prepare("SELECT * FROM webuser WHERE email=?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows==1){
                    $utype=$result->fetch_assoc()['usertype'];
                    if ($utype=='p'){
                        //TODO
                        $stmt = $database->prepare("SELECT * FROM patient WHERE pemail=?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $checker = $stmt->get_result();
                if ($checker->num_rows>=1){
                            $user = $checker->fetch_assoc();
                    // Check if password is hashed or plain text
                    $is_hashed = !empty($user['ppassword']) && 
                                 password_get_info($user['ppassword'])['algo'] > 0;
                    
                    if ($is_hashed) {
                        // Password is hashed
                        $password_valid = password_verify($password, $user['ppassword']);
                    } else {
                        // Password is plain text
                        $password_valid = ($password === $user['ppassword']);
                    }
                    
                    error_log("LOGIN DEBUG: Patient login attempt - Email: $email, Hashed: " . ($is_hashed ? "Yes" : "No") . ", Valid: " . ($password_valid ? "Yes" : "No"));
                    
                    if($password_valid) {
                        //   Patient dashbord
                        $_SESSION['user']=$email;
                        $_SESSION['usertype']='p';
                        $_SESSION['username']=$user['pname'];
                        
                        error_log("LOGIN DEBUG: Patient login successful - Session ID: " . session_id() . ", User: " . $_SESSION['user'] . ", Usertype: " . $_SESSION['usertype']);
                        
                        header('location: patient/index.php');
                        exit(); // Add exit to ensure redirect happens
                    } else {
                        $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Noto`g`ri hisob ma`lumotlari: noto`g`ri elektron pochta yoki parol</label>';
                    }
                        }else{
                            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Noto`g`ri hisob ma`lumotlari: noto`g`ri elektron pochta yoki parol</label>';
                        }

                    }elseif($utype=='a'){
                        //TODO
                        $stmt = $database->prepare("SELECT * FROM admin WHERE aemail=?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $checker = $stmt->get_result();
                        if ($checker->num_rows>=1){
                            $user = $checker->fetch_assoc();
                            // Check if password is hashed or plain text
                            $is_hashed = !empty($user['apassword']) && 
                                         password_get_info($user['apassword'])['algo'] > 0;
                            
                            if ($is_hashed) {
                                // Password is hashed
                                $password_valid = password_verify($password, $user['apassword']);
                            } else {
                                // Password is plain text
                                $password_valid = ($password === $user['apassword']);
                            }
                            
                            // Debug output
                            error_log("Admin login attempt - Email: $email, Hashed: " . ($is_hashed ? "Yes" : "No") . ", Valid: " . ($password_valid ? "Yes" : "No"));
                            
                            if($password_valid) {
                                //   Admin dashbord
                                $_SESSION['user']=$email;
                                $_SESSION['usertype']='a';
                                
                                error_log("LOGIN DEBUG: Admin login successful - Session ID: " . session_id() . ", User: " . $_SESSION['user'] . ", Usertype: " . $_SESSION['usertype']);
                                
                                header('location: admin/index.php');
                                exit(); // Add exit to ensure redirect happens
                            } else {
                                $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Noto`g`ri hisob ma`lumotlari: noto`g`ri elektron pochta yoki parol</label>';
                            }
                        }else{
                            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Noto`g`ri hisob ma`lumotlari: noto`g`ri elektron pochta yoki parol</label>';
                        }


                    }elseif($utype=='d'){
                        //TODO
                        $stmt = $database->prepare("SELECT * FROM doctor WHERE docemail=?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $checker = $stmt->get_result();
                        if ($checker->num_rows>=1){
                            $user = $checker->fetch_assoc();
                            // Check if password is hashed or plain text
                            $is_hashed = !empty($user['docpassword']) && 
                                         password_get_info($user['docpassword'])['algo'] > 0;
                            
                            if ($is_hashed) {
                                // Password is hashed
                                $password_valid = password_verify($password, $user['docpassword']);
                            } else {
                                // Password is plain text
                                $password_valid = ($password === $user['docpassword']);
                            }
                            
                            // Debug output
                            error_log("Doctor login attempt - Email: $email, Hashed: " . ($is_hashed ? "Yes" : "No") . ", Valid: " . ($password_valid ? "Yes" : "No"));
                            
                            if($password_valid) {
                                //   Doctor dashbord
                                $_SESSION['user']=$email;
                                $_SESSION['usertype']='d';
                                
                                error_log("LOGIN DEBUG: Doctor login successful - Session ID: " . session_id() . ", User: " . $_SESSION['user'] . ", Usertype: " . $_SESSION['usertype']);
                                
                                header('location: doctor/index.php');
                                exit(); // Add exit to ensure redirect happens
                            } else {
                                $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Noto`g`ri hisob ma`lumotlari: noto`g`ri elektron pochta yoki parol</label>';
                            }
                        }else{
                            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Noto`g`ri hisob ma`lumotlari: noto`g`ri elektron pochta yoki parol</label>';
                        }

                    }
                    
                }else{
                    $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Biz ushbu elektron pochta uchun hech qanday hisob topa olmadik.</label>';
                }
            }
        }
    }else{
        $error='<label for="promter" class="form-label">&nbsp;</label>';
    }

    ?>

    <div class="form-container w-full max-w-md bg-white rounded-xl shadow-md overflow-hidden p-8 space-y-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800">Xush kelibsiz!</h1>
            <p class="text-gray-600 mt-2">Davom etish uchun ma'lumotlaringiz bilan kiring.</p>
        </div>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="useremail" class="block text-gray-700 font-medium mb-2">Elektron pochta:</label>
                <input 
                    type="email" 
                    name="useremail" 
                    id="useremail"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Pochta manzil" 
                    required
                >
            </div>
            
            <div>
                <label for="userpassword" class="block text-gray-700 font-medium mb-2">Parol:</label>
                <input 
                    type="password" 
                    name="userpassword" 
                    id="userpassword"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Parol" 
                    required
                >
            </div>
            
            <div>
                <?php echo $error ?>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300"
            >
                Kirish
            </button>
        </form>
        
        <div class="text-center text-gray-600">
            Hisobingiz yo'qmi? 
            <a href="signup.php" class="text-blue-600 hover:text-blue-800 font-medium">Ro'yxatdan o'tish</a>
        </div>
    </div>
</body>
</html>