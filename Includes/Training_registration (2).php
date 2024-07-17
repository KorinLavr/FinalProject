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
    
    
    // Haversine formula to calculate distance between two points - for locations
    function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371; // Earth radius in kilometers
    
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
    
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
        return $earthRadius * $c;
    }
    
    // Function to find matches
    function findMatches($conn, $trainingType, $startHour, $endHour, $maxPrice, $userLat, $userLon, $clientNum)
    {
        $exact_matches = [];
        $partial_matches = [];
        $maxDistance = 25; // Maximum distance in kilometers to filter trainings
        
        $currentDate = date('Y-m-d'); // Get the current date in 'YYYY-MM-DD' format
        $oneHourLaterFromNow = date('H:i:s', strtotime('+1 hour'));  // Get the current time plus one hour
        
      
        $added_keys = [];
     
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
            $distance = calculateDistance($userLat, $userLon, $row['Latitude'], $row['Longitude']);
            if ($distance <= $maxDistance) {
                $key = $row['Type'] . '|' . $row['Train_date'] . '|' . $row['Time'] . '|' . $row['Trainer_ID'];
                    if (!in_array($key, $added_keys)) {
                        $row['Distance'] = $distance;
                        $exact_matches[] = $row;
                        $added_keys[] = $key;
                    }
            }
        }
        
        
        usort($exact_matches, function($a, $b) {
            return $a['Distance'] - $b['Distance'];
        });
        
    
        // Determine the total number of exact matches
        $number_exact_matches = count($exact_matches);
    
        if ($number_exact_matches >= 10) {
            // If there are 10 or more exact matches, display only the first 10 exact matches
            return array_slice($exact_matches, 0, 10);
        } elseif ($number_exact_matches >= 6) {
            return $exact_matches;
            // If there are between 6 and 9 exact matches, display only the  exact matches
        } else {
            // If there are less than 6 exact matches, complete with partial matches trying to reach 6
            $partial_matches = findPartialMatches($conn, $trainingType, $startHour, $endHour, $maxPrice, $userLat, $userLon, $clientNum, 6 - $number_exact_matches, $added_keys);
            $merged = array_merge($exact_matches, $partial_matches);
            if(count($merged) > 6)
                return array_slice($merged, 0, 6);
            elseif(count($merged) == 0){
             //   echo "לא נמצאו אימונים הקרובים למיקום שהזנת";
                return [];
            }
            else
                return $merged;
        }
    }
    
    // Function to find partial matches
    function findPartialMatches($conn, $trainingType, $startHour, $endHour, $maxPrice, $userLat, $userLon, $clientNum, $limit, $added_keys) {
        
        $partial_matches = [];
        $priceIncrement = 20;
        $maxPriceLimit = 200;
        $startHourLimit = '06:00';
        $endHourLimit = '23:00';
        $currentDate = date('Y-m-d');
        $oneHourLaterFromNow = date('H:i:s', strtotime('+1 hour'));
        $maxDistance = 25;
        $checkIfLimit = 0;
     
    
        // Loop until we reach the desired limit
        while (count($partial_matches) < $limit  &&  $checkIfLimit < 2 ) {
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
            $iteration_matches = [];
            while ($row = $result->fetch_assoc()) {
                $distance = calculateDistance($userLat, $userLon, $row['Latitude'], $row['Longitude']);
                if ($distance <= $maxDistance) {
                    $key = $row['Type'] . '|' . $row['Train_date'] . '|' . $row['Time'] . '|' . $row['Trainer_ID'];
                    if (!in_array($key, $added_keys)) {
                        $row['Distance'] = $distance;
                        $iteration_matches[] = $row;
                        $added_keys[] = $key;
                    }
                }
            }
            
            usort($iteration_matches, function($a, $b) {
                return $a['Distance'] - $b['Distance'];
            });
            
            $partial_matches = array_merge($partial_matches, $iteration_matches);

            
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
                $iteration_matches = [];
                while ($row = $result->fetch_assoc()) {
                    $distance = calculateDistance($userLat, $userLon, $row['Latitude'], $row['Longitude']);
                    if ($distance <= $maxDistance) {
                        $key = $row['Type'] . '|' . $row['Train_date'] . '|' . $row['Time'] . '|' . $row['Trainer_ID'];
                        if (!in_array($key, $added_keys)) {
                            $row['Distance'] = $distance;
                            $iteration_matches[] = $row;
                            $added_keys[] = $key;
                        }
                    }
                }
            }
            
                usort($iteration_matches, function($a, $b) {
                    return $a['Distance'] - $b['Distance'];
                });
            
                $partial_matches = array_merge($partial_matches, $iteration_matches);
            
            $newStartHour = date('H:i', strtotime($startHour . ' -1 hour'));
            $startHour = $newStartHour < $startHourLimit ? $startHourLimit : $newStartHour;

            $newEndHour = date('H:i', strtotime($endHour . ' +1 hour'));
            $endHour = $newEndHour > $endHourLimit ? $endHourLimit : $newEndHour;

            $maxPrice = min($maxPrice + $priceIncrement, $maxPriceLimit);
            
            if($startHour == $startHourLimit && $endHour == $endHourLimit &&  $maxPrice == $maxPriceLimit) {
                    $checkIfLimit ++;
            }
                                                        
        }
        
        return $partial_matches;
    }
    
    
    // Fetch user preferences from the database
    $query = "SELECT Training_type, Start_hour, End_hour, Max_price, Latitude, Longitude FROM Training_preferences WHERE client_num = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $clientNum);
    $stmt->execute();
    $result = $stmt->get_result();
    $preferences = $result->fetch_assoc();
    
    $trainingType = $preferences['Training_type'];
    $startHour = $preferences['Start_hour'];
    $endHour = $preferences['End_hour'];
    $maxPrice = $preferences['Max_price'];
    $userLat = $preferences['Latitude'];
    $userLon = $preferences['Longitude'];
    
    // Find matches based on user preferences
    $trainings_to_show = findMatches($conn, $trainingType, $startHour, $endHour, $maxPrice, $userLat, $userLon, $clientNum);
    

    // Place this at the beginning of your HTML output
    if (empty($trainings_to_show)) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                displayMessage('לא נמצאו אימונים הקרובים למיקום שהזנת. נסה/י לשנות את העדפות האימון.');
            });
        </script>";
    }

    include 'Training_registration.html';
?>
