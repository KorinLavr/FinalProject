<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainer') {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
$host = 'localhost';
$user_db = 'iskorinla2_NKM';
$pass = 'korinla2_SADNA';
$db = 'iskorinla2_SADNA';

$conn = new mysqli($host, $user_db, $pass, $db);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$trainerID = $user['id'];

$sql = "SELECT * FROM Trainer WHERE Trainer_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $trainerID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $trainer = $result->fetch_assoc();
} else {
    echo "No trainer found";
    exit();
}

$stmt->close();
$conn->close();

include 'personal_details.html';
?>
