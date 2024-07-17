
<?php

session_start();
if (!isset($_SESSION['user'])) {
    // Handle unauthorized access
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Database connection details
$host = 'localhost';
$user = 'iskorinla2_NKM';
$pass = 'korinla2_SADNA';
$db = 'iskorinla2_SADNA';

// Assuming you want to fetch user details based on userType and userID sent via GET parameters
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Validate and sanitize input parameters
    $userType = isset($_GET['userType']) ? $_GET['userType'] : null;
    $userID = isset($_GET['userID']) ? $_GET['userID'] : null;

    // Basic input validation
    if (empty($userType) || empty($userID)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Missing userType or userID']);
        exit();
    }

    try {
        // Connect to database
        $conn = new PDO("mysql:host=$host;dbname=$db", $dbUser, $dbPass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL statement
        $stmt = $conn->prepare("SELECT * FROM your_table WHERE userType = :userType AND userID = :userID");
        $stmt->bindParam(':userType', $userType);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        // Fetch user data
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userData) {
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'User not found']);
            exit();
        }

        // Return user data as JSON response
        echo json_encode(['data' => $userData]);

    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed']);
}
?>
