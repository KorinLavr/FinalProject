<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$host = 'localhost';  
$db_user = 'iskorinla2_NKM';
$db_pass = 'korinla2_SADNA';
$db_name = 'iskorinla2_SADNA';

// Create connection
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Escape input to prevent SQL injection
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    // Check Trainer table
    $sql = "SELECT Trainer_ID AS id, F_name AS firstName, L_name AS lastName FROM Trainer WHERE Email='$email' AND Password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user'] = $row;
        $_SESSION['userType'] = 'trainer';
        header('Location: profile_trainer.php');
        exit();
    }

    // Check Trainee table
    $sql = "SELECT Client_num AS clientNum, First_name AS firstName, Last_name AS lastName FROM Trainee WHERE Email='$email' AND Password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['user'] = $row;
        $_SESSION['userType'] = 'trainee';
        header('Location: Trainee_profile.php');
        exit();
    }

    // If no match found, redirect to login with error parameter
    header('Location: login.html?error=1');
    exit();
} else {
    // If the request is not a POST request, just show the login form
    header('Location: login.html');
    exit();
}
?>
