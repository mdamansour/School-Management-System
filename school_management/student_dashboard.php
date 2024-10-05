<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the student role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Fetch student-specific data here

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Student Dashboard</h1>
    <!-- Display student-specific information and links here -->
    <a href="index.php">Back to Home</a>
    <a href="logout.php">Logout</a>
</body>
</html>
