<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$host = 'localhost';  
$user = 'iskorinla2_NKM';
$pass = 'korinla2_SADNA';
$db = 'iskorinla2_SADNA';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$trainerID = $_POST['trainer-ID'];
$trainingType = $_POST['training-type'];
$location = $_POST['location'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$trainingDate = $_POST['training-date'];
$trainingTime = $_POST['training-time'];
$trainingPrice = $_POST['training-price'];
$trainingMax = $_POST['training-max'];
$trainingDescription = $_POST['training-Description'];

$sql = "INSERT INTO Train (Trainer_ID, Type, Place, Latitude, Longitude, Train_date, Time, Cost, Max_training, Descript) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssis", $trainerID, $trainingType, $location, $latitude, $longitude,  $trainingDate, $trainingTime, $trainingPrice, $trainingMax, $trainingDescription);

$response = [];
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = $stmt->error;
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>
