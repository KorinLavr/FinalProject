<?php
session_start(); // Ensure the session is started

// Check if user is logged in and is a trainer
if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['user']['id']; // Accessing the correct key for Trainer_ID

$host = 'localhost';  
$db_user = 'iskorinla2_NKM';
$db_pass = 'korinla2_SADNA';
$db_name = 'iskorinla2_SADNA';

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

$sql = "SELECT * FROM Train WHERE Trainer_ID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['error' => 'Prepare statement failed: ' . $conn->error]));
}
$stmt->bind_param("i", $userID); // "i" for integer
$stmt->execute();
$result = $stmt->get_result();
$trainings = [];

while ($row = $result->fetch_assoc()) {
    $trainings[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($trainings);
?>
