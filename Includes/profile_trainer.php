<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['userType'] !== 'trainer') {
    header('Location: login.php');
    exit();
}

$user = $_SESSION['user'];
include 'profile_trainer.html';
?>
