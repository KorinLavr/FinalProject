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
        die(json_encode(array('success' => false, 'message' => 'המשתמש לא מזוהה. עליך להתחבר למערכת על מנת להגדיר העדפות אימון.')));
    }
    
    $client_num = $_SESSION['user']['clientNum'];
    $training_type = $_POST['training_type'] ?? null;
    $start_hour = $_POST['start_hour'] ?? null;
    $end_hour = $_POST['end_hour'] ?? null;
    $max_price = $_POST['max_price'] ?? null;
    $location_training = $_POST['location_training'] ?? null;
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    
    if ($training_type && $start_hour && $end_hour && $max_price && $location_training) {
        // Check for existing preferences
        $sql = "SELECT * FROM Training_preferences WHERE Client_num = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $client_num);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // Update existing preferences
            $sql = "UPDATE Training_preferences SET Training_type = ?, Start_hour = ?, End_hour = ?, Max_price = ?, Training_location = ?, Latitude = ?, Longitude = ? WHERE Client_num = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssi", $training_type, $start_hour, $end_hour, $max_price, $location_training, $latitude, $longitude, $client_num);
            if ($stmt->execute()) {
                $response = array('success' => true);
            } else {
                $response = array('success' => false, 'message' => $conn->error);
            }
        } else {
            // Insert new preferences
            $sql = "INSERT INTO Training_preferences (Client_num, Training_type, Start_hour, End_hour, Max_price, Training_location, Latitude, Longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssss", $client_num, $training_type, $start_hour, $end_hour, $max_price, $location_training, $latitude, $longitude);
            if ($stmt->execute()) {
                $response = array('success' => true);
            } else {
                $response = array('success' => false, 'message' => $conn->error);
            }
        }
    
        // Close statement
        $stmt->close();
    } else {
        // Fetch existing preferences
        $sql = "SELECT * FROM Training_preferences WHERE Client_num = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $client_num);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $preferences = $result->fetch_assoc();
            $response = array('success' => true, 'data' => $preferences);
        } else {
            $response = array('success' => false, 'message' => 'No preferences found');
        }
    
        // Close statement
        $stmt->close();
    }
    
    // Return JSON response
    echo json_encode($response);
    
    // Close connection
    $conn->close();

?>
