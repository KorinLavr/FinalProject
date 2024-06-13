<?php
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainee') {
        header('Location: login.php');
        exit();
    }
    
    $user = $_SESSION['user'];
    $clientNum = $user['clientNum'];
    
    
    $host = 'localhost';
    $db = 'iskorinla2_SADNA';
    $dbuser = 'iskorinla2_NKM';
    $dbpass = 'korinla2_SADNA';

    $conn = new mysqli($host, $dbuser, $dbpass, $db);

    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
    
    // Function to check if training preferences are filled
    function arePreferencesFilled($conn, $clientNum) {
        $sql = "SELECT COUNT(*) as count FROM Training_preferences WHERE Client_num = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $clientNum);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] > 0;
    }
    
    $preferencesFilled = arePreferencesFilled($conn, $clientNum); // Replace with actual user ID
    $conn->close();
    
    if (isset($_GET['check_preferences'])) {
    $response = ['preferencesFilled' => $preferencesFilled];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
    }

    // Include the HTML content file
    include 'Trainee_profile.html';
?>