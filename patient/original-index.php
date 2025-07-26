<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/mobile.css">
        
    <title>Bosh sahifa</title>
    <style>
        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table,.anime{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    <?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");

    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();

    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];


    //echo $userid;
    //echo $username;
    
    ?>
    
    <!-- Mobile menu -->
    <div class="mobile-menu">
        <button class="mobile-menu-toggle" id="menuToggle">Menyu ▼</button>
        <div class="mobile-menu-items" id="menuItems">
            <a href="index.php" class="mobile-menu-item active">Bosh sahifa</a>
            <a href="doctors.php" class="mobile-menu-item">Barcha shifokorlar</a>
            <a href="schedule.php" class="mobile-menu-item">Faol seanslar/Joy olish</a>
            <a href="appointment.php" class="mobile-menu-item">Mening bronlarim</a>
            <a href="settings.php" class="mobile-menu-item">Sozlamalar</a>
            <a href="../logout.php" class="mobile-menu-item">Chiqish</a>
        </div>
    </div>
    
    <!-- Desktop menu (hidden on mobile) -->
    <div class="desktop-menu">
        <div class="container">
            <div class="menu">
                <table class="menu-container" border="0">
                    <tr>
                        <td style="padding:10px" colspan="2">
                            <table border="0" class="profile-container">
                                <tr>
                                    <td width="30%" style="padding-left:20px" >
                                        <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                    </td>
                                    <td style="padding:0px;margin:0px;">
                                        <p class="profile-title"><?php echo substr($username,0,13)  ?>.</p>
                                        <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <a href="../logout.php" ><input type="button" value="Chiqish" class="logout-btn btn-primary-soft btn"></a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="menu-row" >
                        <td class="menu-btn menu-icon-home menu-active menu-icon-home-active" >
                            <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Bosh sahifa</p></div></a>
                        </td>
                    </tr>
                    <tr class="menu-row">
                        <td class="menu-btn menu-icon-doctor">
                            <a href="doctors.php" class="non-style-link-menu"><div><p class="menu-text">Barcha shifokorlar</p></div></a>
                        </td>
                    </tr>
                    
                    <tr class="menu-row" >
                        <td class="menu-btn menu-icon-session">
                            <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Faol seanslar/Joy olish</p></div></a>
                        </td>
                    </tr>
                    <tr class="menu-row" >
                        <td class="menu-btn menu-icon-appoinment">
                            <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Mening bronlarim</p></div></a>
                        </td>
                    </tr>
                    <tr class="menu-row" >
                        <td class="menu-btn menu-icon-settings">
                            <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Sozlamalar</p></div></a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Mobile profile section -->
        <div class="profile-mobile">
            <img src="../img/user.png" alt="Profile" class="profile-image">
            <div class="profile-info">
                <h3><?php echo substr($username,0,13)  ?></h3>
                <p><?php echo substr($useremail,0,22)  ?></p>
            </div>
            <a href="../logout.php" class="logout-button">Chiqish</a>
        </div>
        
        <!-- Date section -->
        <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
            <tr>
                <td colspan="1" class="nav-bar" >
                    <p style="font-size: 23px;padding-left:12px;font-weight: 600;margin-left:20px;">Bosh sahifa</p>
                </td>
                <td width="25%">
                </td>
                <td width="15%">
                    <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                        Bugungi sana
                    </p>
                    <p class="heading-sub12" style="padding: 0;margin: 0;">
                        <?php 
                    date_default_timezone_set('Asia/Tashkent');
    
                    $today = date('Y-m-d');
                    echo $today;


                    $patientrow = $database->query("select  * from  patient;");
                    $doctorrow = $database->query("select  * from  doctor;");
                    $appointmentrow = $database->query("select  * from  appointment where appodate>='$today';");
                    $schedulerow = $database->query("select  * from  schedule where scheduledate='$today';");


                    ?>
                    </p>
                </td>
                <td width="10%">
                    <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                </td>
            </tr>
            
            <!-- Welcome section (mobile) -->
            <tr>
                <td colspan="4">
                    <div class="welcome-mobile">
                        <h3>Assalomu alaykum</h3>
                        <h1><?php echo $username  ?>.</h1>
                        <p>Shifokorlar haqida ma'lumot yo'qmi?  
                           <a href="doctors.php" class="non-style-link"><b>"Barcha shifokorlar"</b></a> 
                           yoki 
                           <a href="schedule.php" class="non-style-link"><b>"Faol seanslar"</b></a> 
                           bo'limiga o'ting.<br><br>
                           O'tgan va kelajakdagi tayinlashlaringizni ko'rib chiqing va shifokoringizning kelish vaqti haqida ma'lumot oling.
                        </p>
                        <h3>Shifokor bilan bog'laning.</h3>
                    </div>
                </td>
            </tr>
            
            <!-- Search section (mobile) -->
            <tr>
                <td colspan="4">
                    <div class="search-mobile">
                        <form action="schedule.php" method="post">
                            <input type="search" name="search" class="input-text" placeholder="Shifokorni qidiring, biz esa mavjud sessiyani topamiz" list="doctors">
                            
                            <?php
                                echo '<datalist id="doctors">';
                                $list11 = $database->query("select  docname,docemail from  doctor;");

                                for ($y=0;$y<$list11->num_rows;$y++){
                                    $row00=$list11->fetch_assoc();
                                    $d=$row00["docname"];
                                    
                                    echo "<option value='$d'><br/>";
                                    
                                };

                            echo ' </datalist>';
                            ?>
                            
                            <input type="Submit" value="Qidirish" class="login-btn btn-primary btn">
                        </form>
                    </div>
                </td>
            </tr>
            
            <!-- Status cards (mobile) -->
            <tr>
                <td colspan="4">
                    <div class="status-grid">
                        <div class="status-card">
                            <div class="status-card-icon" style="background-image: url('../img/icons/doctors-hover.svg');"></div>
                            <div class="status-card-content">
                                <h3><?php echo $doctorrow->num_rows ?></h3>
                                <p>Barcha shifokorlar</p>
                            </div>
                        </div>
                        
                        <div class="status-card">
                            <div class="status-card-icon" style="background-image: url('../img/icons/patients-hover.svg');"></div>
                            <div class="status-card-content">
                                <h3><?php echo $patientrow->num_rows ?></h3>
                                <p>Barcha bemorlar</p>
                            </div>
                        </div>
                        
                        <div class="status-card">
                            <div class="status-card-icon" style="background-image: url('../img/icons/appointment-hover.svg');"></div>
                            <div class="status-card-content">
                                <h3><?php echo $appointmentrow->num_rows ?></h3>
                                <p>Bugungi bronlar</p>
                            </div>
                        </div>
                        
                        <div class="status-card">
                            <div class="status-card-icon" style="background-image: url('../img/icons/session-hover.svg');"></div>
                            <div class="status-card-content">
                                <h3><?php echo $schedulerow->num_rows ?></h3>
                                <p>Bugungi seanslar</p>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <!-- Mobile menu script -->
    <script>
        document.getElementById('menuToggle').addEventListener('click', function() {
            const menuItems = document.getElementById('menuItems');
            menuItems.classList.toggle('active');
            
            // Change arrow direction
            const toggleBtn = this;
            if (menuItems.classList.contains('active')) {
                toggleBtn.textContent = 'Menyu ▲';
            } else {
                toggleBtn.textContent = 'Menyu ▼';
            }
        });
    </script>
</body>
</html>