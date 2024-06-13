<?php

    header('Content-Type: application/json; charset=UTF-8');
    
    
    $host = 'localhost';  
    $user = 'iskorinla2_NKM';
    $pass = 'korinla2_SADNA';
    $db = 'iskorinla2_SADNA';
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
        exit();
    }
    
    $userType = $_POST['userType'];
    
    if ($userType === 'trainer') {
        // Insert into Trainer table
        $trainerID = $_POST['trainer-ID'];
        $firstName = $_POST['first-name'];
        $lastName = $_POST['last-name'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $experience = $_POST['experience'];
        $trainingTypes = $_POST['training-types'];
        $certification = isset($_POST['certification']) && $_POST['certification'] === 'yes' ? 1 : 0;
        $password = $_POST['password'];
    
        $sql = "INSERT INTO Trainer(Trainer_ID, F_name, L_name, Sex, Bdate, Phone_num, Email, Exp_years, Type, Certification, Password) 
                VALUES ('$trainerID','$firstName', '$lastName', '$gender', '$dob', '$phone', '$email', '$experience', '$trainingTypes', '$certification', '$password')";
    
        if ($conn->query($sql) === FALSE) {
            echo json_encode(['error' => 'הרישום נכשל. המשתמש עם ת"ז זהה קיים במערכת' . $conn->error]);
            exit();
        }
        echo json_encode(['message' => 'המאמן נוצר בהצלחה!']);
    
        
    } elseif ($userType === 'trainee') {
      
        $firstName = $_POST['first-name'];
        $lastName = $_POST['last-name'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        $sql = "INSERT INTO Trainee(First_name, Last_name, Gender, Birth_date, Phone_num, Email, Password) 
                VALUES ('$firstName', '$lastName', '$gender', '$dob', '$phone', '$email', '$password')";
                
        if ($conn->query($sql) === FALSE) {
            echo json_encode(['error' => 'הרישום נכשל' . $conn->error]);
            exit();
        }
        echo json_encode(['message' => 'המתאמן נוצר בהצלחה!']);
    
        
    } 
    
    $conn->close();
?>
