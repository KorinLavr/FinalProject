<?php

    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainee') {
        header('Location: login.php');
        exit();
    }
    
    
    $user = $_SESSION['user'];
    $clientNum = $user['clientNum'];
    
    // Database connection details
    $host = 'localhost';
    $db = 'iskorinla2_SADNA';
    $dbUser = 'iskorinla2_NKM';
    $dbPass = 'korinla2_SADNA';
    
    $conn = new mysqli($host, $dbUser, $dbPass, $db);
    
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
  
    
    $sql = "SELECT R.*, T.Place, T.Cost, T.Descript, Tr.F_name, Tr.L_name, Tr.Email
        FROM Registration_for_training R
        LEFT JOIN Train T ON R.Trainer_id = T.Trainer_ID
                           AND R.Training_date = T.Train_date
                           AND R.Training_time = T.Time
                           AND R.Training_type = T.Type
        LEFT JOIN Trainer Tr ON T.Trainer_ID = Tr.Trainer_ID
        WHERE R.Client_num = ?
        AND (R.Training_date > NOW() OR (R.Training_date = CURDATE() AND R.Training_time > CURTIME()))
        ORDER BY R.Training_date, R.Training_time";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clientNum);
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        die('Execute failed: ' . $stmt->error);
    }
    $futureTrainings = $result->fetch_all(MYSQLI_ASSOC);
    
    $conn->close();
    
    
    include 'Future_trainings.html';
?>
