<?php
session_start(); 


if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$host = 'localhost';  
$db_user = 'iskorinla2_NKM';
$db_pass = 'korinla2_SADNA';
$db_name = 'iskorinla2_SADNA';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'];
$time = $data['time'];
$train_date = $data['train_date'];

$sql = "DELETE FROM Train WHERE Type = ? AND Time = ? AND Train_date = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare statement failed: ' . $conn->error]);
    exit();
}
$stmt->bind_param("sss", $type, $time, $train_date); 
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to delete training']);
}

$stmt->close();
$conn->close();
?>
