<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the teacher role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

// Fetch teacher-specific data here

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
</head>
<body>
    <h1>Teacher Dashboard</h1>
    <!-- Display teacher-specific information and links here -->
    <a href="index.php">Back to Home</a>
    <a href="logout.php">Logout</a>
</body>
</html>
