<?php

    session_start();

    // Set header for JSON response
    header('Content-Type: application/json; charset=UTF-8');
    
    // Database connection (update with your actual database credentials)
    $host = 'localhost';
    $db = 'iskorinla2_SADNA';
    $user = 'iskorinla2_NKM';
    $pass = 'korinla2_SADNA';
    $charset = 'utf8mb4';
    
    // Create a new connection
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Check if the connection was successful
    if ($conn->connect_error) {
        die(json_encode(array('success' => false, 'message' => 'Connection failed: ' . $conn->connect_error)));
    }
    
    // Ensure the user is logged in
    if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainee') {
        die(json_encode(array('success' => false, 'message' => 'המשתמש לא מזוהה')));
    }
    
    $client_num = $_SESSION['user']['clientNum'];
    $training_type = $_POST['training_type'] ?? null;
    $start_hour = $_POST['start_hour'] ?? null;
    $end_hour = $_POST['end_hour'] ?? null;
    $max_price = $_POST['max_price'] ?? null;
  //  $edit = isset($_POST['edit']) && $_POST['edit'] === 'true';

    if ($training_type && $start_hour && $end_hour && $max_price) {
        // Check for existing preferences
        $sql = "SELECT * FROM Training_preferences WHERE Client_num = '$client_num'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Update existing preferences
            $sql = "UPDATE Training_preferences SET Training_type = '$training_type', Start_hour = '$start_hour', End_hour = '$end_hour', Max_price = '$max_price' WHERE Client_num = '$client_num'";
            if ($conn->query($sql) === TRUE) {
                $response = array('success' => true);
            } else {
                $response = array('success' => false, 'message' => $conn->error);
            }
        } else {
            // Insert new preferences
            $sql = "INSERT INTO Training_preferences (Client_num, Training_type, Start_hour, End_hour, Max_price) 
                    VALUES ('$client_num', '$training_type', '$start_hour', '$end_hour', '$max_price')";
            if ($conn->query($sql) === TRUE) {
                $response = array('success' => true);
            } else {
                $response = array('success' => false, 'message' => $conn->error);
            }
        }
    } else {
        // Check for existing preferences
        $sql = "SELECT * FROM Training_preferences WHERE Client_num = '$client_num'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $preferences = $result->fetch_assoc();
            $response = array('success' => true, 'data' => $preferences);
        } else {
            $response = array('success' => false, 'message' => 'No preferences found');
        }
    }
    
    // Return JSON response
    echo json_encode($response);
?>