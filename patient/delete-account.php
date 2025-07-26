<?php

    session_start();

    //import database
    include("../models/UserSession.php");
    include("../connection.php");
    include("../models/Patient.php");
    include("../models/Utils.php");
    include("../models/Patient.php");
    
    // Check if user is logged in and is a patient
    UserSession::requireUserType('p');
    
    $useremail = UserSession::getUserEmail();
    
    // Create patient model instance
    $patientModel = new Patient($database);
    
    // Get user details
    $userfetch = $patientModel->getByEmail($useremail);
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];

    
    if($_GET){
        $id=$_GET["id"];
        $sqlmain= "select * from patient where pid=?";
        $stmt = $database->prepare($sqlmain);
        $stmt->bind_param("i",$id);
        $stmt->execute();
        $result001 = $stmt->get_result();
        $email=($result001->fetch_assoc())["pemail"];

        // Delete patient using the model
        $patientModel->delete($email);
        
        //print_r($email);
        header("location: ../logout.php");
    }


?>