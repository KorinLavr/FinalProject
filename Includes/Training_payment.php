<?php

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainee') {
        header('Location: login.php');
        exit();
    }

    $user = $_SESSION['user'];
    // Retrieve the key from the URL parameter
    $key = $_GET['key'];
    
    
    // Split the key into its components
    list($trainingType, $trainDate, $trainTime, $trainerID) = explode('|', $key);
    

    $host = 'localhost';
    $db = 'iskorinla2_SADNA';
    $user = 'iskorinla2_NKM';
    $pass = 'korinla2_SADNA';

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    
    // Fetch the training details from the database using the key components
 
    $query = "SELECT * FROM Train WHERE Type = ? AND Train_date = ? AND Time = ? AND Trainer_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssss', $trainingType,  $trainDate, $trainTime, $trainerID);
    $stmt->execute();
    $result = $stmt->get_result();
    $training = $result->fetch_assoc();
    
    
    include 'Training_payment.html'; 
?>
