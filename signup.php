<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ro'yxatdan o'tish - eDoc</title>
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

// Include CSRF protection and utilities
include("csrf.php");
include("models/Utils.php");

if($_POST){
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error='<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Invalid request. Please try again.</label>';
    } else {
        
        $_SESSION["personal"]=array(
            'fname'=>trim($_POST['fname']),
            'lname'=>trim($_POST['lname']),
            'address'=>trim($_POST['address']),
            'nic'=>trim($_POST['nic']),
            'dob'=>$_POST['dob']
        );

        print_r($_SESSION["personal"]);
        header("location: create-account.php");
    }
}

?>

    <div class="form-container w-full max-w-2xl bg-white rounded-xl shadow-md overflow-hidden p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Keling, boshlaymiz!</h1>
            <p class="text-gray-600 mt-2">Davom etish uchun shaxsiy ma'lumotlaringizni kiriting</p>
        </div>

        <form action="" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="fname" class="block text-gray-700 font-medium mb-2">Ism:</label>
                    <input 
                        type="text" 
                        name="fname" 
                        id="fname"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Ismingiz" 
                        required
                    >
                </div>
                
                <div>
                    <label for="lname" class="block text-gray-700 font-medium mb-2">Familiya:</label>
                    <input 
                        type="text" 
                        name="lname" 
                        id="lname"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Familiyangiz" 
                        required
                    >
                </div>
            </div>
            
            <div>
                <label for="address" class="block text-gray-700 font-medium mb-2">Manzil:</label>
                <input 
                    type="text" 
                    name="address" 
                    id="address"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Manzilingiz" 
                    required
                >
            </div>
            
            <div>
                <label for="nic" class="block text-gray-700 font-medium mb-2">ShIR:</label>
                <input 
                    type="text" 
                    name="nic" 
                    id="nic"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="ShIR raqamingiz" 
                    required
                >
            </div>
            
            <div>
                <label for="dob" class="block text-gray-700 font-medium mb-2">Tug'ilgan sana:</label>
                <input 
                    type="date" 
                    name="dob" 
                    id="dob"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    required
                >
            </div>
            
            <div class="flex justify-between pt-4">
                <input type="reset" value="Qayta tiklash" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-6 rounded-lg transition duration-300 cursor-pointer">
                
                <div class="flex space-x-4">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="submit" value="Keyingi" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300 cursor-pointer">
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