<?php
// Patient Dashboard - Clean Version
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if user is logged in as patient
if(!isset($_SESSION["user"]) || $_SESSION["user"]=="" || $_SESSION['usertype']!='p'){
    header("location: ../login.php");
    exit();
}

$useremail = $_SESSION["user"];

// Import database
include("../connection.php");

// Get patient info
$stmt = $database->prepare("SELECT * FROM patient WHERE pemail=?");
$stmt->bind_param("s", $useremail);
$stmt->execute();
$userrow = $stmt->get_result();
$userfetch = $userrow->fetch_assoc();

$userid = $userfetch["pid"];
$username = $userfetch["pname"];

// Get statistics
$patientrow = $database->query("SELECT * FROM patient");
$doctorrow = $database->query("SELECT * FROM doctor");
$appointmentrow = $database->query("SELECT * FROM appointment WHERE appodate >= CURDATE()");
$schedulerow = $database->query("SELECT * FROM schedule WHERE scheduledate = CURDATE()");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bosh sahifa</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/mobile.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #666;
        }
        .user-details h2 {
            margin: 0 0 5px 0;
            font-size: 24px;
        }
        .user-details p {
            margin: 0;
            color: #666;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 15px;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 32px;
            color: #007bff;
        }
        .stat-card p {
            margin: 0;
            color: #666;
        }
        .welcome-section {
            background: #e3f2fd;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .welcome-section h2 {
            margin-top: 0;
        }
        .search-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .search-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .nav-menu {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
        }
        .nav-item {
            background: #007bff;
            color: white;
            padding: 12px 20px;
            border-radius: 4px;
            text-decoration: none;
            flex: 1;
            min-width: 150px;
            text-align: center;
        }
        .nav-item:hover {
            background: #0056b3;
        }
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }
            .user-info {
                flex-direction: column;
            }
            .search-form {
                flex-direction: column;
            }
            .search-input, .search-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="user-info">
                <div class="user-avatar">👤</div>
                <div class="user-details">
                    <h2><?php echo htmlspecialchars(substr($username, 0, 13)); ?></h2>
                    <p><?php echo htmlspecialchars(substr($useremail, 0, 22)); ?></p>
                </div>
            </div>
            <a href="../logout.php" class="logout-btn">Chiqish</a>
        </div>
        
        <!-- Navigation Menu -->
        <div class="nav-menu">
            <a href="index.php" class="nav-item" style="background: #28a745;">Bosh sahifa</a>
            <a href="doctors.php" class="nav-item">Barcha shifokorlar</a>
            <a href="schedule.php" class="nav-item">Faol seanslar</a>
            <a href="appointment.php" class="nav-item">Mening bronlarim</a>
            <a href="settings.php" class="nav-item">Sozlamalar</a>
        </div>
        
        <!-- Stats Section -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                <h3><?php echo $doctorrow ? $doctorrow->num_rows : '0'; ?></h3>
                <p>Barcha shifokorlar</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                <h3><?php echo $patientrow ? $patientrow->num_rows : '0'; ?></h3>
                <p>Barcha bemorlar</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-image: url('../img/icons/appointment-hover.svg');"></div>
                <h3><?php echo $appointmentrow ? $appointmentrow->num_rows : '0'; ?></h3>
                <p>Bugungi bronlar</p>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon" style="background-image: url('../img/icons/session-hover.svg');"></div>
                <h3><?php echo $schedulerow ? $schedulerow->num_rows : '0'; ?></h3>
                <p>Bugungi seanslar</p>
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2>Assalomu alaykum</h2>
            <h1><?php echo htmlspecialchars($username); ?>.</h1>
            <p>Shifokorlar haqida ma'lumot yo'qmi?  
               <a href="doctors.php"><b>"Barcha shifokorlar"</b></a> 
               yoki 
               <a href="schedule.php"><b>"Faol seanslar"</b></a> 
               bo'limiga o'ting.<br><br>
               O'tgan va kelajakdagi tayinlashlaringizni ko'rib chiqing va shifokoringizning kelish vaqti haqida ma'lumot oling.
            </p>
            <h3>Shifokor bilan bog'laning.</h3>
        </div>
        
        <!-- Search Section -->
        <div class="search-section">
            <h3>Shifokor qidirish</h3>
            <form action="schedule.php" method="post" class="search-form">
                <input type="search" name="search" class="search-input" 
                       placeholder="Shifokorni qidiring, biz esa mavjud sessiyani topamiz" 
                       list="doctors">
                
                <datalist id="doctors">
                    <?php
                    $list11 = $database->query("SELECT docname FROM doctor");
                    if ($list11) {
                        while($row00 = $list11->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row00["docname"]) . "'>";
                        }
                    }
                    ?>
                </datalist>
                
                <input type="submit" value="Qidirish" class="search-btn">
            </form>
        </div>
    </div>
</body>
</html>