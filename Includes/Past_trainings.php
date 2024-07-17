<?php

    // Database connection details
    $host = 'localhost';
    $db = 'iskorinla2_SADNA';
    $dbUser = 'iskorinla2_NKM';
    $dbPass = 'korinla2_SADNA';
    
    $conn = new mysqli($host, $dbUser, $dbPass, $db);
    
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    function getPastTrainings($conn, $clientNum, $offset = 0, $limit = 5) {
        $sql = "SELECT R.*, T.Place, T.Cost, T.Descript, Tr.F_name, Tr.L_name, Tr.Email
                FROM Registration_for_training R
                LEFT JOIN Train T ON R.Trainer_id = T.Trainer_ID
                                   AND R.Training_date = T.Train_date
                                   AND R.Training_time = T.Time
                                   AND R.Training_type = T.Type
                LEFT JOIN Trainer Tr ON T.Trainer_ID = Tr.Trainer_ID
                WHERE R.Client_num = ?
                AND (R.Training_date < NOW() OR (R.Training_date = CURDATE() AND R.Training_time < CURTIME()))
                ORDER BY R.Training_date DESC, R.Training_time DESC
                LIMIT ? OFFSET ?";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $clientNum, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $trainings = [];
        while ($row = $result->fetch_assoc()) {
            $trainings[] = $row;
        }
        return $trainings;
    }
    
    // Check if this is an AJAX request to load more trainings
    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
        session_start();
        $user = $_SESSION['user'];
        $clientNum = $user['clientNum'];
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;
    
        $PastTrainings = getPastTrainings($conn, $clientNum, $offset, $limit);
        echo json_encode($PastTrainings);
        exit;
    }
    
    // Regular page request
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainee') {
        header('Location: login.php');
        exit();
    }
    
    $user = $_SESSION['user'];
    $clientNum = $user['clientNum'];
    $PastTrainings = getPastTrainings($conn, $clientNum);
    
    include 'Past_trainings.html';


?>