<?php
// Simplified patient index.php for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
        exit();
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

// Import database
include("../connection.php");

$sqlmain= "select * from patient where pemail=?";
$stmt = $database->prepare($sqlmain);
if (!$stmt) {
    die("Prepare failed: " . $database->error);
}
$stmt->bind_param("s",$useremail);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$userrow = $stmt->get_result();
$userfetch=$userrow->fetch_assoc();

$userid= $userfetch["pid"];
$username=$userfetch["pname"];

// Get statistics
$patientrow = $database->query("select  * from  patient;");
$doctorrow = $database->query("select  * from  doctor;");
$appointmentrow = $database->query("select  * from  appointment where appodate>=CURDATE();");
$schedulerow = $database->query("select  * from  schedule where scheduledate=CURDATE();");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #f5f5f5; padding: 20px; margin-bottom: 20px; }
        .welcome { background: #e3f2fd; padding: 20px; margin-bottom: 20px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .stat-card { background: #f5f5f5; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Patient Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($username); ?> (<?php echo htmlspecialchars($useremail); ?>)</p>
        </div>
        
        <div class="welcome">
            <h2>Assalomu alaykum, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>Today is <?php echo date('Y-m-d'); ?></p>
        </div>
        
        <h2>Statistics</h2>
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $doctorrow ? $doctorrow->num_rows : 'N/A'; ?></h3>
                <p>Doctors</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $patientrow ? $patientrow->num_rows : 'N/A'; ?></h3>
                <p>Patients</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $appointmentrow ? $appointmentrow->num_rows : 'N/A'; ?></h3>
                <p>Today's Appointments</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $schedulerow ? $schedulerow->num_rows : 'N/A'; ?></h3>
                <p>Today's Schedules</p>
            </div>
        </div>
        
        <div style="margin-top: 30px;">
            <h2>Navigation</h2>
            <ul>
                <li><a href="doctors.php">All Doctors</a></li>
                <li><a href="schedule.php">Active Sessions</a></li>
                <li><a href="appointment.php">My Appointments</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>