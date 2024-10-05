<?php
session_start();
include 'db.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $birth_date = $_POST['birth_date'];
    $classroom_id = $_POST['classroom_id'];
    $level_id = $_POST['level_id'];
    $start_date = $_POST['start_date'];

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("INSERT INTO students (name, birth_date, classroom_id, level_id, start_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $birth_date, $classroom_id, $level_id, $start_date);

    if ($stmt->execute()) {
        header("Location: manage_students.php");
    } else {
        die("Error: " . $stmt->error);
    }
}
?>
