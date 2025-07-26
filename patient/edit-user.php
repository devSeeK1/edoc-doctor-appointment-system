
    <?php
    
    
    session_start();
    
    //import database
    include("../models/UserSession.php");
    include("../connection.php");
    include("../csrf.php");
    include("../models/Patient.php");
    include("../models/ErrorMessages.php");
    include("../models/Utils.php");
    
    // Check if user is logged in and is a patient
    UserSession::requireUserType('p');

    if($_POST){
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
            header("location: settings.php?action=edit&error=6&id=" . (isset($_POST['id00']) ? $_POST['id00'] : ''));
            exit();
        }
        
        // Sanitize and validate input
        $stmt = $database->prepare("SELECT * FROM webuser");
        $stmt->execute();
        $result = $stmt->get_result();
        $name = trim($_POST['name']);
        $nic = trim($_POST['nic']);
        $oldemail = Utils::sanitizeEmail($_POST["oldemail"]);
        $address = trim($_POST['address']);
        $email = Utils::sanitizeEmail($_POST['email']);
        $tele = Utils::sanitizePhone($_POST['Tele']);
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $id = (int)$_POST['id00'];
        
        // Validate email
        if (!Utils::validateEmail($email)) {
            $error='1'; // Invalid email
        }
        // Validate password match
        elseif ($password !== $cpassword) {
            $error='2'; // Password mismatch
        }
        // Validate password strength
        elseif (strlen($password) < MIN_PASSWORD_LENGTH) {
            $error='5'; // Password too short
        } else {
            $error='3';

            $sqlmain= "select patient.pid from patient inner join webuser on patient.pemail=webuser.email where webuser.email=?;";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("s",$email);
            $stmt->execute();
            $result = $stmt->get_result();
            //$resultqq= $database->query("select * from doctor where docid='$id';");
            if($result->num_rows==1){
                $id2=$result->fetch_assoc()["pid"];
            }else{
                $id2=$id;
            }
            

            if($id2!=$id){
                $error='1';
                //$resultqq1= $database->query("select * from doctor where docemail='$email';");
                //$did= $resultqq1->fetch_assoc()["docid"];
                //if($resultqq1->num_rows==1){
                    
            }else{
                // Update patient using the model
                $patientModel->update($id2, $email, $name, $password, $nic, $tele, $address);
                
                $sql1="update webuser set email=? where email=? ;";
                $stmt = $database->prepare($sql1);
                $stmt->bind_param("ss", $email, $oldemail);
                $stmt->execute();
                
                $error= '4';
                
            }
            
        }
        
    }else{
        //header('location: signup.php');
        $error='3';
    }
    

    header("location: settings.php?action=edit&error=".$error."&id=".$id);
    ?>