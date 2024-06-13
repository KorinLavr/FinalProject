<?php
        
    ini_set('max_execution_time', 20); // Set to 60 seconds or higher as needed
    
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    session_start();
    
    if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainee') {
        header('Location: login.php');
        exit();
    }
    
    $user = $_SESSION['user'];
    $clientNum = $user['clientNum'];
    
     // Set the default timezone to Israel
        date_default_timezone_set('Asia/Jerusalem');
    
    // Database connection
    $host = 'localhost';
    $db = 'iskorinla2_SADNA';
    $user = 'iskorinla2_NKM';
    $pass = 'korinla2_SADNA';
  //  $charset = 'utf8mb4';
    
    // Create a new connection
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Check if the connection was successful
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    
//    $conn = openConnection();
    
    
    // Function to find matches
    function findMatches($conn, $trainingType, $startHour, $endHour, $maxPrice, $clientNum)
    {
        $exact_matches = [];
        $partial_matches = [];
        
        $currentDate = date('Y-m-d'); // Get the current date in 'YYYY-MM-DD' format
        $oneHourLaterFromNow = date('H:i:s', strtotime('+1 hour'));  // Get the current time plus one hour
        
      //  $priceIncrement = 20;
      //  $maxPriceLimit = 200;
     //   $startHourLimit = '06:00';
     //   $endHourLimit = '23:00';
        $added_keys = [];
     //   echo "Current Date: $currentDate";
     //   echo "Hour: $oneHourLaterFromNow";
        // Initial query to find exact matches
        $query = "
                SELECT t.*, tr.F_name, tr.L_name, tr.Exp_years, tr.Type AS Type_trainer 
                FROM Train t 
                JOIN Trainer tr ON t.Trainer_ID = tr.Trainer_ID
                WHERE t.Type = ? AND (t.Time BETWEEN ? AND ?) AND t.Cost <= ? 
                AND (t.Train_date > ? OR (t.Train_date = ? AND t.Time >= ?))
                AND t.Num_curr_participants < t.Max_training
                AND NOT EXISTS (
                    SELECT 1 
                    FROM Registration_for_training rt 
                    WHERE rt.Client_num = ? 
                    AND rt.Training_type = t.Type 
                    AND rt.Training_date  = t.Train_date 
                    AND rt.Training_time = t.Time 
                    AND rt.Trainer_id = t.Trainer_ID
                )
                ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('sssssssi', $trainingType, $startHour, $endHour, $maxPrice, $currentDate, $currentDate, $oneHourLaterFromNow, $clientNum);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $key = $row['Type'] . '|' . $row['Train_date'] . '|' . $row['Time'] . '|' . $row['Trainer_ID'];
                if (!in_array($key, $added_keys)) {
                    $exact_matches[] = $row;
                    $added_keys[] = $key;
                }
        }
        
        
    //    echo "Exact Matches:\n";
     //   print_r($exact_matches);
        // print_r($added_keys);


        // Determine the total number of exact matches
        $number_exact_matches = count($exact_matches);
    
        if ($number_exact_matches >= 10) {
            // If there are 10 or more exact matches, display only the first 10 exact matches
            return array_slice($exact_matches, 0, 10);
        } elseif ($number_exact_matches >= 7) {
            // If there are between 7 and 9 exact matches, complete with partial matches to reach 10
            $remaining_matches = 10 - $number_exact_matches;
            $partial_matches = findPartialMatches($conn, $trainingType, $startHour, $endHour, $maxPrice, $clientNum, $remaining_matches, $added_keys );
            $merged = array_merge($exact_matches, $partial_matches);
            return array_slice($merged, 0, 10);
        } elseif ($number_exact_matches == 6) {
            // If there are exactly 6 exact matches, display only the 6 exact matches
            return $exact_matches;
        } else {
            // If there are less than 6 exact matches, complete with partial matches to reach 6
            $partial_matches = findPartialMatches($conn, $trainingType, $startHour, $endHour, $maxPrice, $clientNum, 6 - $number_exact_matches, $added_keys);
            $merged = array_merge($exact_matches, $partial_matches);
            return array_slice($merged, 0, 6);
        }
    }
    
    // Function to find partial matches
    function findPartialMatches($conn, $trainingType, $startHour, $endHour, $maxPrice, $clientNum, $limit, $added_keys) {
        
        $partial_matches = [];
        $priceIncrement = 20;
        $maxPriceLimit = 200;
        $startHourLimit = '06:00';
        $endHourLimit = '23:00';
        $currentDate = date('Y-m-d');
        $oneHourLaterFromNow = date('H:i:s', strtotime('+1 hour'));
       // print_r($added_keys);
    
        // Loop until we reach the desired limit
        while (count($partial_matches) < $limit) {
            // Query to find partial matches in exact training type
            $query = "
                    SELECT t.*, tr.F_name, tr.L_name, tr.Exp_years, tr.Type AS Type_trainer  
                    FROM Train t 
                    JOIN Trainer tr ON t.Trainer_ID = tr.Trainer_ID
                    WHERE t.Type = ? AND (t.Time BETWEEN ? AND ?) AND t.Cost <= ? 
                    AND (t.Train_date > ? OR (t.Train_date = ? AND t.Time >= ?))
                    AND t.Num_curr_participants < t.Max_training
                    AND NOT EXISTS (
                        SELECT 1 
                        FROM Registration_for_training rt 
                        WHERE rt.Client_num = ? 
                        AND rt.Training_type = t.Type 
                        AND rt.Training_date  = t.Train_date 
                        AND rt.Training_time = t.Time 
                        AND rt.Trainer_id = t.Trainer_ID
                    )
                    ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sssssssi', $trainingType, $startHour, $endHour, $maxPrice, $currentDate, $currentDate, $oneHourLaterFromNow, $clientNum);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $key = $row['Type'] . '|' . $row['Train_date'] . '|' . $row['Time'] . '|' . $row['Trainer_ID'];
                if (!in_array($key, $added_keys)) {
                    $partial_matches[] = $row;
                    $added_keys[] = $key;
                }
             
            }
            // print_r($added_keys);
            
         //   echo "partial Matches:\n";
          //  print_r($partial_matches);
            
            if(count($partial_matches) < $limit){
                // Query to find partial matches without exact training type
                $query = "SELECT t.*, tr.F_name, tr.L_name, tr.Exp_years, tr.Type AS Type_trainer  
                        FROM Train t 
                        JOIN Trainer tr ON t.Trainer_ID = tr.Trainer_ID
                        WHERE (t.Time BETWEEN ? AND ?) AND t.Cost <= ? 
                        AND (t.Train_date > ? OR (t.Train_date = ? AND t.Time >= ?))
                        AND t.Num_curr_participants < t.Max_training
                        AND NOT EXISTS (
                            SELECT 1 
                            FROM Registration_for_training rt 
                            WHERE rt.Client_num = ? 
                            AND rt.Training_type = t.Type 
                            AND rt.Training_date  = t.Train_date 
                            AND rt.Training_time = t.Time 
                            AND rt.Trainer_id = t.Trainer_ID
                        )
                        ";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ssssssi', $startHour, $endHour, $maxPrice, $currentDate, $currentDate, $oneHourLaterFromNow, $clientNum);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $key = $row['Type'] . '|' . $row['Train_date'] . '|' . $row['Time'] . '|' . $row['Trainer_ID'];
                    if (!in_array($key, $added_keys)) {
                        $partial_matches[] = $row;
                        $added_keys[] = $key;
                    }
                }
            }
            
        //    echo "partial Matches:\n";
         //   print_r($partial_matches);
            // Update conditions for next iteration, but ensure they don't exceed limits
            $newStartHour = date('H:i', strtotime($startHour . ' -1 hour'));
            $startHour = $newStartHour < $startHourLimit ? $startHourLimit : $newStartHour;

            $newEndHour = date('H:i', strtotime($endHour . ' +1 hour'));
            $endHour = $newEndHour > $endHourLimit ? $endHourLimit : $newEndHour;

            $maxPrice = min($maxPrice + $priceIncrement, $maxPriceLimit);
        }
    
        return $partial_matches;
    }
    
    
    // Fetch user preferences from the database
    $query = "SELECT Training_type, Start_hour, End_hour, Max_price FROM Training_preferences WHERE client_num = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $clientNum);
    $stmt->execute();
    $result = $stmt->get_result();
    $preferences = $result->fetch_assoc();
    
    $trainingType = $preferences['Training_type'];
    $startHour = $preferences['Start_hour'];
    $endHour = $preferences['End_hour'];
    $maxPrice = $preferences['Max_price'];
    
    // Find matches based on user preferences
    $trainings_to_show = findMatches($conn, $trainingType, $startHour, $endHour, $maxPrice,  $clientNum);
    
    
    // Display the trainings based on the number of exact matches
 //   $trainings_to_display = [];
    
  //  if (count($trainings) >= 10) {
        // If there are 10 or more exact matches, display only the first 10 exact matches
    //    $trainings_to_display = $trainings;
  //  } elseif (count($trainings) >= 7) {
        // If there are between 7 and 9 exact matches, complete with partial matches to reach 10
     //   $trainings_to_display = array_slice($trainings, 0, 10);
 //   } else {
        // If there are less than 6 exact matches or equal, complete with partial matches to reach 6
      //  $trainings_to_display = array_slice($trainings, 0, 6);
  //  }
    
    include 'Training_registration.html';
?>
