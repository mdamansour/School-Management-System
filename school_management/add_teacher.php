<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Retrieve form data
$name = $_POST['name'];
$birth_date = $_POST['birth_date'];
$classroom_id = $_POST['classroom_id'];
$level_id = $_POST['level_id'];
$start_date = $_POST['start_date'];
$salary = $_POST['salary'];
$regulated = $_POST['regulated'];
$paid = $_POST['paid'];

// Insert data into the database
$query = "INSERT INTO teachers (name, birth_date, classroom_id, level_id, start_date, salary, regulated, paid) 
          VALUES ('$name', '$birth_date', $classroom_id, $level_id, '$start_date', $salary, $regulated, $paid)";

if ($conn->query($query) === TRUE) {
    header("Location: manage_teachers.php");
    exit();
} else {
    die("Error: " . $conn->error);
}
?>
