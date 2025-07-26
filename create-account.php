<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hisob yaratish - eDoc</title>
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
            // Sanitize and validate input
        $fname = trim($_SESSION['personal']['fname']);
        $lname = trim($_SESSION['personal']['lname']);
        $name = $fname." ".$lname;
        $address = trim($_SESSION['personal']['address']);
        $nic = trim($_SESSION['personal']['nic']);
        $dob = $_SESSION['personal']['dob'];
        $email = Utils::sanitizeEmail($_POST['newemail']);
        $tele = Utils::sanitizePhone($_POST['tele']);
        $newpassword = $_POST['newpassword'];
        $cpassword = $_POST['cpassword'];
        
        // Validate email
        if (!Utils::validateEmail($email)) {
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid email format.</label>';
        }
        // Validate password match
        elseif ($newpassword !== $cpassword) {
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>';
        }
        // Validate password strength
        elseif (strlen($newpassword) < MIN_PASSWORD_LENGTH) {
            $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters long.</label>';
        } else {
            // Check if user already exists in webuser table
            $stmt = $database->prepare("SELECT * FROM webuser WHERE email=?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Also check if user already exists in patient table
            $stmt2 = $database->prepare("SELECT * FROM patient WHERE pemail=?");
            $stmt2->bind_param("s", $email);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            
            if($result->num_rows>=1 || $result2->num_rows>=1){
                $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>';
            }else{
                //TODO
                $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
                $stmt = $database->prepare("INSERT INTO patient(pemail,pname,ppassword, paddress, pnic,pdob,ptel) VALUES(?,?,?,?,?,?,?)");
                $stmt->bind_param("sssssss", $email, $name, $hashedPassword, $address, $nic, $dob, $tele);
                $stmt->execute();
                
                $stmt = $database->prepare("INSERT INTO webuser VALUES(?,?)");
                $userType = 'p';
                $stmt->bind_param("ss", $email, $userType);
                $stmt->execute();

                //print_r("insert into patient values($pid,'$email','$fname','$lname','$newpassword','$address','$nic','$dob','$tele');");
                $_SESSION["user"]=$email;
                $_SESSION["usertype"]="p";
                $_SESSION["username"]=$fname;

                header('Location: patient/index.php');
                $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>';
            }
        }
        
    }
}else{
    //header('location: signup.php');
    $error='<label for="promter" class="form-label"></label>';
}

?>

    <div class="form-container w-full max-w-2xl bg-white rounded-xl shadow-md overflow-hidden p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Keling, boshlaymiz!</h1>
            <p class="text-gray-600 mt-2">Muammo yo'q, endi foydalanuvchi hisobini yarating</p>
        </div>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label for="newemail" class="block text-gray-700 font-medium mb-2">Elektron pochta:</label>
                <input 
                    type="email" 
                    name="newemail" 
                    id="newemail"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Pochta manzil" 
                    required
                >
            </div>
            
            <div>
                <label for="tele" class="block text-gray-700 font-medium mb-2">Telefon:</label>
                <input 
                    type="tel" 
                    name="tele" 
                    id="tele"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Misol: 0712345678"
                    pattern="[0]{1}[0-9]{9}"
                >
            </div>
            
            <div>
                <label for="newpassword" class="block text-gray-700 font-medium mb-2">Yangi parol yarating:</label>
                <input 
                    type="password" 
                    name="newpassword" 
                    id="newpassword"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Yangi parol" 
                    required
                >
            </div>
            
            <div>
                <label for="cpassword" class="block text-gray-700 font-medium mb-2">Parolni tasdiqlang:</label>
                <input 
                    type="password" 
                    name="cpassword" 
                    id="cpassword"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Parolni tasdiqlang" 
                    required
                >
            </div>
            
            <div>
                <?php echo $error ?>
            </div>
            
            <div class="flex justify-between pt-4">
                <input type="reset" value="Tiklash" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-300 cursor-pointer">
                
                <div class="flex space-x-4">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="submit" value="Ro'yxatdan o'tish" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300 cursor-pointer">
                </div>
            </div>
        </form>
        
        <div class="text-center text-gray-600 mt-8 pt-6 border-t border-gray-200">
            Allaqachon hisobingiz bormi? 
            <a href="login.php" class="text-blue-600 hover:text-blue-800 font-medium">Kirish</a>
        </div>
    </div>
</body>
</html>