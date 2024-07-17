<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $host = 'localhost';
    $db = 'iskorinla2_SADNA'; 
    $dbUser = 'iskorinla2_NKM'; 
    $dbPass = 'korinla2_SADNA'; 

    try {
        // Establish connection
        $pdo = new PDO("mysql:host=$host;dbname=$db", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare UPDATE statement
        $stmt = $pdo->prepare("UPDATE Trainer SET F_name = ?, L_name = ?, Sex = ?, Bdate = ?, Phone_num = ?, Email = ?, Exp_years = ?, Type = ?, Certification = ? WHERE Trainer_ID = ?");

        // Bind parameters
        $stmt->bindParam(1, $_POST['F_name']);
        $stmt->bindParam(2, $_POST['L_name']);
        $stmt->bindParam(3, $_POST['Sex']);
        $stmt->bindParam(4, $_POST['Bdate']);
        $stmt->bindParam(5, $_POST['Phone_num']);
        $stmt->bindParam(6, $_POST['Email']);
        $stmt->bindParam(7, $_POST['Exp_years']);
        $stmt->bindParam(8, $_POST['Type']);
        $certification = $_POST['Certification'] === '1' ? 1 : 0; // Convert checkbox value to boolean
        $stmt->bindParam(9, $certification);
        $stmt->bindParam(10, $_POST['Trainer_ID']);

        // Execute the statement
        $stmt->execute();

        // Return success message or JSON response
        echo json_encode(['success' => true]);
        exit();
    } catch(PDOException $e) {
        // Handle database connection or query errors
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        exit();
    }
}
?>
