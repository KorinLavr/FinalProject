<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
   // ob_start();
    session_start();
    // Check if user is logged in as trainee
    if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainee') {
        header('Location: login.php');
        exit();
    }
    
    $user = $_SESSION['user'];
    $clientNum = $user['clientNum'];

    // Include database connection code
    // Set header for JSON response
  //  header('Content-Type: application/json; charset=UTF-8');
    
    // Database connection 
    $host = 'localhost';
    $db = 'iskorinla2_SADNA';
    $user = 'iskorinla2_NKM';
    $pass = 'korinla2_SADNA';
 //   $charset = 'utf8mb4';
    
    // Create a new connection
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Check if the connection was successful
    if ($conn->connect_error) {
         die('Connection failed: ' . $conn->connect_error);
    }
    
    // Update trainee details if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];

        $update_sql = "UPDATE Trainee SET First_name = ?, Last_name = ?, Gender = ?, Birth_date = ?, Phone_num = ?, Email = ? WHERE Client_num = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssss", $firstName, $lastName, $gender, $dob, $phone, $email, $clientNum);

        if ($update_stmt->execute()) {
            $message = "פרטים עודכנו בהצלחה.";
        } else {
             $message = "שגיאה בעדכון הפרטים: " . $conn->error;
        }

        $update_stmt->close();
        
        // Redirect back to HTML with success message
        header("Location: Personal_details_trainee.html?message=" . urlencode($message));
        exit();
    }

    // Fetch trainee details based on trainee number
  //  $trainee_num = $_GET['trainee_num'];
    $sql = "SELECT First_name, Last_name, Phone_num, Email, Birth_date, Gender FROM Trainee WHERE Client_num = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $clientNum);
    $stmt->execute();
    $result = $stmt->get_result();
    $trainee = $result->fetch_assoc();
   
    $stmt->close();
    $conn->close();
    // Include the HTML content for the form
//    ob_end_flush();

    include 'Personal_details_trainee.html';
    
    // Redirect to the HTML file
  //  header('Location: Personal_details_trainee.html');
  //  exit();
?>

