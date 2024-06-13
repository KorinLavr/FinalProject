<?php

    session_start();
    
    if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainee') {
        header('Location: login.php');
        exit();
    }
    
    $user = $_SESSION['user'];
    $clientNum = $user['clientNum'];
    $training_type = $_POST['training_type'];
    $training_date = $_POST['training_date'];
    $training_time = $_POST['training_time'];
    $trainer_id = $_POST['trainer_id'];
    $credit_card_number = $_POST['credit_card_number'];
    $expiration_month = $_POST['expiration_month'];
    $expiration_year = $_POST['expiration_year'];
    $cvv = $_POST['cvv'];
    
    // Here you would typically process the payment using a payment gateway API.
    // For demonstration purposes, we will just simulate a successful payment.
    
    // Simulate payment processing delay
    sleep(2);
    
    // Assume payment is successful
    $payment_successful = true;
    
    if ($payment_successful) {
        // Insert the registration to the training into the database
        $host = 'localhost';
        $db = 'iskorinla2_SADNA';
        $user = 'iskorinla2_NKM';
        $pass = 'korinla2_SADNA';
    
        $conn = new mysqli($host, $user, $pass, $db);
    
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }
        
        $query = "SELECT * FROM Registration_for_training WHERE Client_num = ? AND Training_type = ? AND Training_date = ? AND Training_time = ? AND Trainer_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issss', $clientNum, $training_type, $training_date, $training_time, $trainer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
         if ($result->num_rows > 0) {
        // User is already registered for this training
            echo json_encode(['success' => false, 'message' => 'User already registered for this training']);
        } else {
            // Insert the registration to the training into the database
            $query = "INSERT INTO Registration_for_training (Client_num, Training_type, Training_date, Training_time, Trainer_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('issss', $clientNum, $training_type, $training_date, $training_time, $trainer_id);
            $stmt->execute();
            
             // Update the current participants count in the Train table
            $updateQuery = "UPDATE Train SET Num_curr_participants = Num_curr_participants + 1 WHERE Type = ? AND Train_date = ? AND Time = ? AND Trainer_ID = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param('ssss', $training_type, $training_date, $training_time, $trainer_id);
            $updateStmt->execute();
        
           // Send back response to the training payment page
            echo json_encode(['success' => true]);
        }
    } else {
        // Send back response to the training payment page
             echo json_encode(['success' => false]);
    }
    
?>
