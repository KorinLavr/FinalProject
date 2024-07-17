<?php
session_start();

// Check if user is logged in as a trainer
if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Retrieve JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

// Validate incoming data
if (!$data || !isset($data['Type'], $data['Place'], $data['Train_date'], $data['Time'], $data['Cost'], $data['Max_training'], $data['Descript'], $data['Trainer_ID'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit();
}

$host = 'localhost';  
$db_user = 'iskorinla2_NKM';
$db_pass = 'korinla2_SADNA';
$db_name = 'iskorinla2_SADNA';

// Connect to database
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Prepare SQL statement
$sql = "UPDATE Train SET Type = ?, Place = ?, Train_date = ?, Time = ?, Cost = ?, Max_training = ?, Descript = ? WHERE Trainer_ID = ? AND Type = ? AND Train_date = ? AND Time = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(['error' => 'Prepare statement failed: ' . $conn->error]));
}

// Bind parameters to the statement
$stmt->bind_param("sssdsisssss",
    $data['Type'],
    $data['Place'],
    $data['Train_date'],
    $data['Time'],
    $data['Cost'],
    $data['Max_training'],
    $data['Descript'],
    $data['Trainer_ID'],
    $data['Type'],  // To match both conditions in WHERE clause
    $data['Train_date'],
    $data['Time']
);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
